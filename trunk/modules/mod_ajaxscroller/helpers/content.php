<?php
/**
 * @package		AJAX Scroller
 * @copyright	Copyright (C) 2011 Sakic.Net. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */
 
 // no direct access
defined('_JEXEC') or die;

require_once dirname(__FILE__).'/common.php';

class modAjaxScrollerContentHelper
{
	static function get($moduleid, $params, $start=0)
	{
		$current_id = JRequest::getInt( 'c', $start );
		$direction = JRequest::getString( 'd' );
		
		// cache
		$doCache = intval( $params->get( 'scr_cache', 0 ) );
		
		if ($doCache) {
			$CacheTime = intval( $params->get( 'cache_time', 60 ) );
			jimport('joomla.cache.cache');
			$options = array(
				'defaultgroup' 	=> 'mod_ajaxscroller'
			);
			$cache = JCache::getInstance( 'callback', $options );
			$cache->setCaching( 1 );
			$cache->setLifeTime( $CacheTime );
			$content = $cache->call('fetch_content', $moduleid, $params, $current_id, $direction);
		} else {
			$content = self::fetch_content($moduleid, $params, $current_id, $direction);
		}

		return $content;
	}
	
	static function fetch_content($moduleid, $params, $current_id, $direction)
	{
		$ordering = $params->get( 'ordering', 'created DESC' );
		if ($ordering=='random') {
			$order = 'a.ordering ASC';
		} else if ($ordering=='frontpage') {
			$order = 'f.ordering ASC';
		} else {
			$order = 'a.'.$ordering;
		}
		list($orderby, $drctn) = explode(' ', $order);
		$items_count = intval( $params->get( 'items_count', 1 ) );
		$items_limit = intval( $params->get( 'items_limit', 0 ) );
		$categories = (array) $params->get( 'categories', array(0=>'') );
		$text_type = $params->get( 'text_type', 'both' );
		$display_image = intval( $params->get( 'display_image', 0 ) );
		$image_max_width = intval( $params->get( 'image_max_width', 40 ) );
		$date_format = $params->get( 'date_format', 'Y-m-d' );
		
		require_once(JPATH_BASE.DS.'components'.DS.'com_content'.DS.'models'.DS.'articles.php');
		
		$model = JModel::getInstance('Articles', 'ContentModel', array('ignore_request' => true));
		$model->setState('params', JFactory::getApplication()->getParams());
		$categories = (array) $params->get( 'categories', array(0=>'') );
		$model->setState('filter.category_id', $categories);
		$model->setState('list.ordering', $orderby);
		$model->setState('list.direction', $drctn);
		$model->setState('list.select', 
			'a.id, a.title, a.alias, a.title_alias, a.introtext, a.fulltext, ' .
			'a.checked_out, a.checked_out_time, ' .
			'a.catid, a.created, a.created_by, a.created_by_alias, ' .
			// use created if modified is 0
			'CASE WHEN a.modified = 0 THEN a.created ELSE a.modified END as modified, ' .
				'a.modified_by, uam.name as modified_by_name,' .
			// use created if publish_up is 0
			'CASE WHEN a.publish_up = 0 THEN a.created ELSE a.publish_up END as publish_up, ' .
				'a.publish_down, a.attribs, a.metadata, a.metakey, a.metadesc, a.access, ' .
				'a.hits, a.xreference, a.featured,'.' LENGTH(a.fulltext) AS readmore '
		);
		
		//echo '<pre>'; print_r($model); die;
		
		$rows = $model->getItems();
		
		if ($items_limit) {
			$rows = array_slice($rows, 0, $items_limit);
		}
		
		// Compute the article slugs and prepare introtext (runs content plugins).
		for ($i = 0, $n = count($rows); $i < $n; $i++) {
			$item = &$rows[$i];
			$item->slug = $item->alias ? ($item->id . ':' . $item->alias) : $item->id;
			$rows[$i] = $item;
		}
		
		if ($ordering=='random') {
			uksort ( $rows, create_function('$a,$b', '
				$rand = sha1($_SERVER["REMOTE_ADDR"].date("Ymd"));
				$value = $a + $b;
				$value = substr($value,-1);

				if (intval(hexdec($rand{$value})) % 2) {
					return -1;
				} else {
					return 1;
				}
				
			') );
			// update keys
			$i = 0;
			foreach ($rows as $value) {
				$rows[$i] = $value;
				$i++;
			}
			ksort($rows);
		}
		/*
		$i=1;
		foreach($rows as $row) {
			echo $i.'. '.$row->id.' '.$row->title.'<br />';
			$i++;
		}
		die;
		*/
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
				$empty = new stdClass;
				$empty->sectionid = null;
				$empty->id = null;
				$empty->slug = '';
				$empty->catslug = '';
				$empty->created = '';
				$empty->title = '';
				$empty->introtext = '';
				$empty->fulltext = '';
				$row = $empty;
			} else {
				$row = $rows[$key];
			}
			
			//echo "<pre>"; print_r($row); echo "</pre>"; die;
			
			//$link = "index.php?option=com_content&amp;view=article&amp;id=$row->slug&amp;catid=$row->catslug" . $Itemid;
			//$link = JRoute::_( $link );
			
			require_once(JPATH_BASE.DS.'components'.DS.'com_content'.DS.'helpers'.DS.'route.php');
			$link = ContentHelperRoute::getArticleRoute($row->slug, $row->catid);			
			$link = JRoute::_($link);
			
			$date = JHTML::_('date', $row->created, $date_format);
			
			if ($text_type=='introtext') {
				$text = $row->introtext;
			} else if ($text_type=='fulltext') {
				$text = $row->fulltext;
			} else {
				$text = $row->introtext.$row->fulltext;
			}
			
			$text = modAjaxScrollerCommonHelper::ajax_scroller_fix_text($text, $params);
			
			if ($display_image) {
				$row->text = $text;
				$params->get( 'image', 1 );
				$params->get( 'intro_only', 1 );
				/*
				JPluginHelper::importPlugin('content', null, false);			
				$dispatcher =& JDispatcher::getInstance();
				$result = $dispatcher->trigger('onPrepareContent', array($row, $params, 0));
				*/
				$text = $row->text;
				if ($image_max_width) {
					$text = preg_replace('/height="[0-9]+"/i','',$text);
					$text = preg_replace('/width="[0-9]+"/i','',$text);
					$text = preg_replace('/<img /i','<img width="'.$image_max_width.'" ',$text);
				}
				$text = preg_replace('/hspace="[0-9]+"/i','hspace="2"',$text);
				$text = preg_replace('/src="images\/stories\//i','src="'.JUri::base().'images/stories/',$text);
			} else {
				$text = preg_replace('/{.*}/','',$text);
			}

			$content .= modAjaxScrollerCommonHelper::get_scroller_item($row->title, $link, $text, $date, $key, $index, $params, $moduleid);

		}
		
		return $content;
	}
}

// cache function proxy
if (!function_exists('fetch_content')) {
	function fetch_content($moduleid, $params, $current_id, $direction) {
		return modAjaxScrollerContentHelper::fetch_content($moduleid, $params, $current_id, $direction);
	}
}
