<?php
/**
 * @package		AJAX Scroller
 * @copyright	Copyright (C) 2011 Sakic.Net. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */
 
 // no direct access
defined('_JEXEC') or die;

require_once dirname(__FILE__).'/common.php';

class modAjaxScrollerRssHelper
{
	static function get($moduleid, $params, $start=0) {

		$items_count = intval( $params->get( 'items_count', 1 ) );
		$twitter_timeline = $params->get( 'twitter_timeline', 'user' );
		$date_format = $params->get( 'date_format', 'Y-m-d' );
		
		$current_id = JRequest::getInt( 'c', $start );
		$direction = JRequest::getString( 'd' );
		$display_image = intval( $params->get( 'display_image', 0 ) );
		$image_max_width = intval( $params->get( 'image_max_width', 40 ) );

		$rows = self::fetchRSS($params);
		
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
	
	static function fetchRSS($params, $force=0) {
	
		$type = $params->get( 'type', '' );
		$rssurl = $params->get( 'rss_url', '' );
		$twitter_timeline = $params->get( 'twitter_timeline', 'user' );
		$twitter_keyword = $params->get( 'twitter_keyword', '' );
		if ($type=='twitter' && $twitter_timeline=='search') {
			$rssurl = 'http://search.twitter.com/search.atom?q='.urlencode($twitter_keyword);
		}
		$items_limit = intval( $params->get( 'items_limit', 10 ) );
		$rss_image_only = intval( $params->get( 'rss_image_only', 0 ) );
		$doCache = intval( $params->get( 'scr_cache', 1 ) );
		$CacheTime = intval( $params->get( 'cache_time', 3600 ) );
		$feed_desc = 1;
		$item_desc = 1;
		$cacheDir = JPATH_BASE.DS.'cache'.DS;
		
		$feed_array = array();
		$feed_array[0] = '';

		jimport ('simplepie.simplepie');
		$simplepie = new SimplePie();
		// check if cache directory is writeable
		if ( !is_writable( $cacheDir ) || !$doCache ) {
			$simplepie->enable_cache(false);
		} else {
			// delete the cache file if fetch forced
			$cache_file = $cacheDir . md5($rssurl) . '.spc';
			if ($force && file_exists($cache_file)) {
				// delete the cache, force the new fetch
				@unlink($cache_file);
			}
			$simplepie->set_cache_name_function('md5');
			$simplepie->set_cache_location($cacheDir);
			$simplepie->set_cache_duration($CacheTime);
		}
		$simplepie->set_feed_url($rssurl);
		$simplepie->init();
		$simplepie->handle_content_type();
		
		if ($simplepie->data) {
			$rssDoc = $simplepie;
		} else {
			return 'Error: Feed not retrieved';
		}

		//$feed_array[0]['feed_title'] = @$rssDoc->get_title();
		//$feed_array[0]['feed_link'] = @$rssDoc->get_link();
		//$feed_array[0]['feed_desc'] = @$rssDoc->get_description();

		// items
		$feed_items = @$rssDoc->get_items();
		if (empty($feed_items)) {
			return 'Error: No items fetched';
		}
		if ($rss_image_only) {
			// filter only feeds that contain image
			$temp_array = array();
			foreach ($feed_items as $item) {
				$description = @$item->get_description();
				if ($description && stristr($description,'img src')) {
					$temp_array[] = $item;
				}
			}
			$feed_items = $temp_array;
		}
		if ($items_limit > 0) {
			$feed_items = array_slice($feed_items, 0, $items_limit);
		}
		
		foreach ( $feed_items as $j=>$item ) {
			if ( !is_null( @$item->get_link() ) ) {
				$feed_array[0][$j]['item_href'] = $item->get_link();
			}
			if ( !is_null( @$item->get_title() ) ) {
				$feed_array[0][$j]['item_title'] = @$item->get_title();
			}
			if ( !is_null( @$item->get_date() ) ) {
				$feed_array[0][$j]['item_date'] = @$item->get_date();
			}
			if ( @$item->get_description() ) {
				$feed_array[0][$j]['item_desc'] = @$item->get_description();
				$feed_array[0][$j]['item_desc'] = str_replace('&apos;', "'", $feed_array[0][$j]['item_desc']);
			}
			
			// Twitter search			
			if ( preg_match('/^tag:search.twitter.com/', @$item->get_id()) ) {
				$user_profile_image_url = @$item->get_link(0, 'image');
				
				$user_screen_name = '';
				if ( @$item->get_author() ) {
					$author = @$item->get_author();
					if (isset($author->name)) {
						$author = $author->name;
						if (!empty($author) && stristr($author, ' ')) {
							$author = explode(' ', $author);
							$author = $author[0];
							$user_screen_name = $author;
						}
					}
				}
				
				// Replace title
				$feed_array[0][$j]['item_title'] = $user_screen_name;

				// Replace text
				$text = $feed_array[0][$j]['item_desc'];
				$created_at = $feed_array[0][$j]['item_date'];
				$source = @$item->get_item_tags('http://api.twitter.com/', 'source');
				$source = @$item->sanitize($source[0]['data'], SIMPLEPIE_CONSTRUCT_HTML);
				$feed_array[0][$j]['item_desc'] = modAjaxScrollerCommonHelper::ajax_scroller_format_twitter($text, $params, $user_profile_image_url, $user_screen_name, $created_at, $source);
			}
		}

		//echo '<pre>'; print_r($feed_array[0]); die;
		
		return $feed_array[0];
	}
}
