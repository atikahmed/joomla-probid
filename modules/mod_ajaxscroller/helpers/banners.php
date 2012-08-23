<?php
/**
 * @package		AJAX Scroller
 * @copyright	Copyright (C) 2011 Sakic.Net. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */
 
 // no direct access
defined('_JEXEC') or die;

require_once dirname(__FILE__).'/common.php';

class modAjaxScrollerBannersHelper
{
	static function get($moduleid, $params, $start=0) {
		
		require_once ( JPATH_BASE .DS.'libraries'.DS.'joomla'.DS.'html'.DS.'parameter.php' );
		
		$db	=& JFactory::getDBO();

		$ordering = $params->get( 'banner_ordering', 'ordering ASC' );
		if ($ordering=='random') {
			$order = 'ordering ASC';
		} else {
			$order = $ordering;
		}
		$items_count = intval( $params->get( 'items_count', 1 ) );
		$items_limit = intval( $params->get( 'items_limit', 0 ) );
		$target_blank = $params->get( 'target_blank', 0 );
		$categories = (array) $params->get( 'banner_categories', array() );
		$clients = (array) $params->get( 'banner_clients', array() );
		
		$params->set('read_more', 0);
		$params->set('date', 0);
		
		$current_id = JRequest::getInt( 'c', $start );
		$direction = JRequest::getString( 'd' );

		$date =& JFactory::getDate();
		$now = $date->toFormat();

		$nullDate = $db->getNullDate();
		
		$wheres1 = array();
		foreach($categories as $catid) {
			if (!empty($catid)) {
				$wheres1[] = "b.catid = $catid";
			}
		}
		if (empty($wheres1)) {
			$wheres1[] = '1';
		}
		
		$wheres2 = array();
		foreach($clients as $cid) {
			if (!empty($cid)) {
				$wheres2[] = "b.cid = $cid";
			}
		}
		if (empty($wheres2)) {
			$wheres2[] = '1';
		}

		$query = "SELECT * FROM #__banners AS b"
			. "\n WHERE b.state = 1"
			. "\n AND (" . implode( ' OR ', $wheres1 ) . ")"
			. "\n AND (" . implode( ' OR ', $wheres2 ) . ")"
			. "\n AND ( b.publish_up = '$nullDate' OR b.publish_up <= '$now' )"
			. "\n AND ( b.publish_down = '$nullDate' OR b.publish_down >= '$now' )"
			. "\n ORDER BY $order LIMIT 1000"
			;

		$db->setQuery( $query );
		$rows = $db->loadObjectList();
		
		if ($items_limit) {
			$rows = array_slice($rows, 0, $items_limit);
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
			echo $i.'. '.$row->id.' '.$row->name.'<br />';
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
				$empty->id = null;
				$empty->name = '';
				$empty->imageurl = '';
				$empty->clickurl = '';
				$empty->type = 0;
				$empty->custombannercode = '';
				$row = $empty;
			} else {
				$row = $rows[$key];
				$params1 = new JParameter( $row->params );
				$row->imageurl = $params1->get( 'imageurl', '' );
			}
			
			//echo "<pre>"; print_r($row); echo "</pre>"; die;
			

			// Output
			
			$link = "index.php?option=com_banners&amp;task=click&amp;id=$row->id";
			$link = JRoute::_( $link );
			
			$image = JURI::base() . $row->imageurl;
			
			if ($row->type==1 && !empty($row->custombannercode)) {
				$text = $row->custombannercode;
				$text = str_replace('{NAME}', $row->name, $text);
				$text = str_replace('{CLICKURL}', $link, $text);
			} else {
				$text = '<a href="'.$link.'"';
				$text .= $target_blank ? ' target="_blank"' : '';
				$text .= '><img src="'.$image.'" alt="'.$row->name.'" border="0" /></a>';
			}

			$content .= modAjaxScrollerCommonHelper::get_scroller_item($row->name, $link, $text, '', $key, $index, $params, $moduleid);

		}
		
		return $content;
	}
}
