<?php
/**
 * @package		AJAX Scroller
 * @copyright	Copyright (C) 2011 Sakic.Net. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */
 
 // no direct access
defined('_JEXEC') or die;

require_once dirname(__FILE__).'/common.php';

class modAjaxScrollerTwitterHelper
{
	static function get($moduleid, $params, $start=0) {

		$items_count = intval( $params->get( 'items_count', 1 ) );
		$twitter_timeline = $params->get( 'twitter_timeline', 'user' );
		$date_format = $params->get( 'date_format', 'Y-m-d' );
		
		$current_id = JRequest::getInt( 'c', $start );
		$direction = JRequest::getString( 'd' );
		$display_image = intval( $params->get( 'display_image', 0 ) );
		$image_max_width = intval( $params->get( 'image_max_width', 40 ) );

		$rows = self::fetchXML($params);
		
		if (!is_array($rows)) {
			if (substr($rows, 0, 6)=='Error:') {
				return modAjaxScrollerCommonHelper::get_scroller_item('', '', $rows, '', 0, 0, $params, $moduleid);
			}
			$rows = array($rows);
		}

		//echo '<pre>'; print_r($rows); die;

		$content = '';
		for ($index=0; $index<$items_count; $index++) {
			$key = $current_id + $index;
			if ($direction=='prev') {
				$key = $key - $items_count;
				if ($key < 0) {
					$remainder = count($rows) % $items_count;
					if ($remainder==0) {
						$key = count($rows) - $items_count;
					} else {
						$key = count($rows) - $remainder;
					}
					$current_id = $key - $items_count;
					$direction = 'next';
				}
			} else if ($direction=='next') {
				$key = $key + $items_count;
				if ($key>=count($rows) && $index==0) {
					$key = 0;
					$current_id = $key - $items_count;
				}
			}
			if (empty($rows[$key])) {
				$row['item_title'] = '';
				$row['item_href'] = '';
				$row['item_date'] = '';
				$row['item_desc'] = '';
			} else {
				$row = $rows[$key];
			}
			
			$title = isset($row['item_title']) ? $row['item_title'] : '';
			$link = isset($row['item_href']) ? $row['item_href'] : '';
			$date = isset($row['item_date']) ? JHTML::_('date', $row['item_date'], $date_format) : '';
			$text = isset($row['item_desc']) ? modAjaxScrollerCommonHelper::ajax_scroller_fix_text($row['item_desc'], $params) : '';
			
			if ($display_image) {
				if ($image_max_width) {
					$text = preg_replace('/height="[0-9]+"/i','',$text);
					$text = preg_replace('/width="[0-9]+"/i','',$text);
					$text = preg_replace('/<img /i','<img width="'.$image_max_width.'" ',$text);
				}
				$text = preg_replace('/hspace="[0-9]+"/i','hspace="2"',$text);
			} else {
				$text = preg_replace('/<img (.*?)>/si','',$text);
			}

			$content .= modAjaxScrollerCommonHelper::get_scroller_item($title, $link, $text, $date, $key, $index, $params, $moduleid);

		}
		
		return $content;
	}
	
	static function fetchXML($params, $force=0) {
		
		$rssurl = $params->get( 'rss_url', '' );
		$items_limit = intval( $params->get( 'items_limit', 10 ) );
		$doCache = intval( $params->get( 'scr_cache', 1 ) );		
		$CacheTime = intval( $params->get( 'cache_time', 3600 ) );
		$twitter_timeline = $params->get( 'twitter_timeline', 'user' );
		$username = $params->get( 'twitter_username', '' );
		$password = $params->get( 'twitter_password', '' );
		$list = $params->get( 'twitter_list', '' );
		
		if ($twitter_timeline=='friends') {
			$rssurl = 'http://api.twitter.com/1/statuses/friends_timeline.xml';
		} else if ($twitter_timeline=='mentions') {
			$rssurl = 'http://api.twitter.com/1/statuses/mentions.xml';
		} else if ($twitter_timeline=='list') {
			$rssurl = 'http://api.twitter.com/1/'.urlencode($username).'/lists/'.urlencode($list).'/statuses.xml';
		} else if ($twitter_timeline=='user_rt' && $username!='') {
			$rssurl = 'http://api.twitter.com/1/statuses/user_timeline.xml?screen_name='.urlencode($username).'&include_rts=true';
		} else {
			if ($username!='') {
				$rssurl = 'http://api.twitter.com/1/statuses/user_timeline/'.urlencode($username).'.xml';
			} else {
				$rssurl = str_replace('.rss', '.xml', $rssurl);
			}
		}
		$feed_desc = 1;
		$item_desc = 1;
		
		$feed_array = array();
		
		$xmlDoc = & JFactory::getXMLParser('Simple');
		
		if ($doCache) {
			if (!class_exists('JCache')) {
				require_once( JPATH_SITE.DS.'libraries'.DS.'joomla'.DS.'cache'.DS.'cache.php' );
			}
			$options = array(
				'defaultgroup' 	=> 'mod_ajaxscroller',
				'lifetime' => $CacheTime,
				'checkTime'	=> true,
				'caching'	=> true
			);
			$cache = new JCache( $options );
			$cache->setLifeTime($CacheTime);
			
			if ($force) {
				// delete the cache, force the new fetch
				$cache->remove(md5($rssurl), 'mod_ajaxscroller');
			}
			if ($string = $cache->get(md5($rssurl), 'mod_ajaxscroller')) {
				$xmlDoc->loadString($string);
			} else {
				$xml = simplexml_load_file($rssurl);
				$string = $xml->asXML();
				$string = str_replace('georss:','georss_',$string);	// simplexml doesn't like ':'
				$xmlDoc->loadString($string);
				$cache->store($xmlDoc->document->toString(), md5($rssurl));
			}
		} else {
			$xml = simplexml_load_file($rssurl);
			$string = $xml->asXML();
			$string = str_replace('georss:','georss_',$string);	// simplexml doesn't like ':'
			$xmlDoc->loadString($string);
		}
		
		$root =& $xmlDoc->document;
		$statuses =& $root->children();
		$length = count($statuses);
		
		$total = $items_limit && $items_limit < $length ? $items_limit : $length;
		
		if ($total==0) {
			$feed_array = $xmlDoc->loadString($string);
		}

		for ($i = 0; $i<$total; $i++) {
			$status =& $statuses[$i];
			
			$id =& $status->getElementByPath('id')->data();
			$created_at =& $status->getElementByPath('created_at')->data();				
			$text =& $status->getElementByPath('text')->data();
			$source =& $status->getElementByPath('source')->data();				
			$in_reply_to_status_id =& $status->getElementByPath('in_reply_to_status_id')->data();
			$in_reply_to_user_id =& $status->getElementByPath('in_reply_to_user_id')->data();
			$in_reply_to_screen_name =& $status->getElementByPath('in_reply_to_screen_name')->data();
			$user_id =& $status->getElementByPath('user')->getElementByPath('id')->data();
			$user_screen_name =& $status->getElementByPath('user')->getElementByPath('screen_name')->data();
			$user_profile_image_url =& $status->getElementByPath('user')->getElementByPath('profile_image_url')->data();
			
			$feed_array[$i]['item_href'] = 'http://twitter.com/'.$user_screen_name.'/statuses/'.$id;
			$feed_array[$i]['item_date'] = $created_at;
			$feed_array[$i]['item_title'] = $user_screen_name;
			//$text = htmlentities($text);
			$feed_array[$i]['item_desc'] = modAjaxScrollerCommonHelper::ajax_scroller_format_twitter($text, $params, $user_profile_image_url, $user_screen_name, $created_at, $source, $in_reply_to_user_id, $in_reply_to_screen_name, $in_reply_to_status_id);
		}
		
		return $feed_array;
	}
	
}
