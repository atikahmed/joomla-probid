<?php
/**
 * @package		AJAX Scroller
 * @copyright	Copyright (C) 2011 Sakic.Net. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */
 
 // no direct access
defined('_JEXEC') or die;

class modAjaxScrollerCommonHelper
{
	static function get_scroller_item($title, $link, $text, $date, $key, $index, $params, $moduleid) {
	
		$items_count = intval( $params->get( 'items_count', 1 ) );
		$display_title = intval( $params->get( 'title', 0 ) );
		$link_title = intval( $params->get( 'link_title', 1 ) );
		$display_date = intval( $params->get( 'date', 0 ) );
		$target_blank = intval( $params->get( 'target_blank', 0 ) );
		$display_text = intval( $params->get( 'text', 1 ) );
		$read_more = intval( $params->get( 'read_more', 0 ) );
		$display_page = intval( $params->get( 'page', 0 ) );
		$display_image = intval( $params->get( 'display_image', 0 ) );
	
		$content = '';
		if ($index==0) {
			$content .= '<div id="m'.$moduleid.$key.'">';
		}
		if ($items_count>1) {
			if ($index % 2) {
				$content .= '<div class="even">';
			} else {
				$content .= '<div class="odd">';
			}
		}
		if ($display_title && !empty($title)) {
			if ($link_title && !empty($link)) {
				$content .= "<a href=\"".$link."\" title=\"".$title."\" class=\"title\"";
				if ($target_blank) {
					$content .= ' target="_blank"';
				}
				$content .= ">".$title."</a>\n";
			} else {
				$content .= "<span class=\"title\">$title</span>\n";
			}
		}
		if ($display_date && !empty($date) && !empty($text)) {
			$content .= "<span class=\"date\">".$date."</span>\n";
		}
		if ($display_text && !empty($text)) {
			$content .= $text;
			if ($read_more && !empty($link)) {
				if (!defined('_READ_MORE')) {
					if (class_exists('JText')) {
						define('_READ_MORE', JText::_('Read more...'));
					} else {
						define('_READ_MORE', 'Read more...');
					}
				}
				$content .= "<p class=\"link\"><a href=\"".$link."\" title=\"".$title."\"";
				if ($target_blank) {
					$content .= ' target="_blank"';
				}
				$content .= ">"._READ_MORE."</a></p>\n";
			}
		} else if ($display_image && preg_match('/<img (.*?)>/si', $text, $matches)) {
			$content .= $matches[0];
		}
		if ($items_count>1) {
			$content .= '</div>';
		}
		if ($index>=$items_count-1) {
			// page number
			if ($display_page && $index>=$items_count-1) {
				$page = ceil(($key+1)/$items_count);
				$content .= '<div class="page">'.$page.'</div>';
			}
			$content .= '</div>';
		}
		return $content;
	}
	
	static function ajax_scroller_fix_text($text, $params) {

		$char_limit = intval( $params->get( 'char_limit', 0 ) );
		$strip_tags = intval( $params->get( 'strip_tags', 0 ) );
		$display_image = intval( $params->get( 'display_image', 0 ) );
		$image_max_width = intval( $params->get( 'image_max_width', 40 ) );
		
		if ($strip_tags) {
			if ($display_image) {
				$text = strip_tags($text,'<img><image>');
			} else {
				$text = strip_tags($text);
			}
		} else if ($display_image && $char_limit!=0) {
			$text = strip_tags($text,'<a><b><strong><img><image><span><br><br /><small>');
		} else if ($char_limit!=0) {
			// allow for some tags
			$text = strip_tags($text,'<a><b><strong><span><br><br /><small>');
		}

		if ($char_limit && strlen(strip_tags($text))>$char_limit) {
			$text = self::ajax_scroller_truncate($text, $char_limit, '&#133;', false, true);
		}
		
		return $text;
		
	}
	
	static function ajax_scroller_format_twitter($str, $params, $user_profile_image_url, $user_screen_name, $created_at, $source, $in_reply_to_user_id='', $in_reply_to_screen_name='', $in_reply_to_status_id='') {
		
		$target_blank = intval( $params->get( 'target_blank', 0 ) );
		$display_screen_name = intval( $params->get( 'display_screen_name', 1 ) );
		
		if ($target_blank) {
			$new_window = ' target="_blank"';
		} else {
			$new_window = '';
		}
		
		$text = '<img src="'.$user_profile_image_url.'" width="48" height="48" hspace="2" align="left" alt="'.$user_screen_name.'" />';
		if ($display_screen_name) {
			$text .= '<a href="http://twitter.com/'.$user_screen_name.'"'.$new_window.' class="user_screen_name">'.$user_screen_name.'</a>: ';
		}
		$text .= self::ajax_scroller_replace_links($str, $params);
		
		if (!empty($in_reply_to_user_id)) {
			$text = str_replace('@'.$in_reply_to_screen_name, '@<a href="http://twitter.com/'.$in_reply_to_screen_name.'"'.$new_window.'>'.$in_reply_to_screen_name.'</a>', $text);
		}
		
		// date
		$now = date(DATE_RFC822);
		$time_diff = self::ajax_scroller_get_time_difference($created_at, $now);
		$time_str = '';
		if ($time_diff['days']>0) {
			$days = $time_diff['days'];
			if ($time_diff['hours']>=24) {
				$days++;
			}
			if ($days==1) {
				$time_str = $days.' day ago';
			} else {
				$time_str = $days.' days ago';
			}
		} else if ($time_diff['hours']>0) {
			$hours = $time_diff['hours'];
			if ($time_diff['minutes']>=30) {
				$hours++;
			}
			if ($hours==1) {
				$time_str = $hours.' hour ago';
			} else {
				$time_str = $hours.' hours ago';
			}
		} else if ($time_diff['minutes']>0) {
			$minutes = $time_diff['minutes'];
			if ($time_diff['minutes']>=30) {
				$minutes++;
			}
			if ($minutes==1) {
				$time_str = $minutes.' minute ago';
			} else {
				$time_str = $minutes.' minutes ago';
			}
		} else if ($time_diff['seconds']>0) {
			$seconds = $time_diff['seconds'];
			$time_str = $seconds.' seconds ago';
		}
		$text .= ' <small>';
		$text .= $time_str;
		
		// from
		$text .= ' from '.$source;
		
		// in response
		if (!empty($in_reply_to_status_id)) {
			$text .= ' <a href="http://twitter.com/'.$in_reply_to_screen_name.'/status/'.$in_reply_to_status_id.'"'.$new_window.'>in reply to '.$in_reply_to_screen_name.'</a>';
		}
		
		$text .= '</small>';

		return $text;
	}
	
	private static function ajax_scroller_replace_links($text, $params) {
		$target_blank = intval( $params->get( 'target_blank', 0 ) );
		if ($target_blank) {
			$new_window = ' target="_blank"';
		} else {
			$new_window = '';
		}
		// doesn't work correctly if links already present
		if (!stristr($text, '<a ')) {
			$text = preg_replace("/([ \t]|^)www\./i", "\\1http://www.", $text);
			$text = preg_replace("/(http:\/\/[^ )\r\n!]+)/i", "<a href=\"\\1\"$new_window>\\1</a>", $text);
			$text = preg_replace("/(https:\/\/[^ )\r\n!]+)/i", "<a href=\"\\1\"$new_window>\\1</a>", $text);
			$text = preg_replace("/([-a-z0-9_]+(\.[_a-z0-9-]+)*@([a-z0-9-]+(\.[a-z0-9-]+)+))/i", "<a href=\"mailto:\\1\">\\1</a>", $text);
		}
		return $text;
	}
	
	private static function ajax_scroller_get_time_difference( $start, $end ) {
		$uts['start']      =    strtotime( $start );
		$uts['end']        =    strtotime( $end );
		if( $uts['start']!==-1 && $uts['end']!==-1 )
		{
			if( $uts['end'] >= $uts['start'] )
			{
				$diff    =    $uts['end'] - $uts['start'];
				if( $days=intval((floor($diff/86400))) )
					$diff = $diff % 86400;
				if( $hours=intval((floor($diff/3600))) )
					$diff = $diff % 3600;
				if( $minutes=intval((floor($diff/60))) )
					$diff = $diff % 60;
				$diff    =    intval( $diff );            
				return( array('days'=>$days, 'hours'=>$hours, 'minutes'=>$minutes, 'seconds'=>$diff) );
			}
			else
			{
				trigger_error( "Ending date/time is earlier than the start date/time", E_USER_WARNING );
			}
		}
		else
		{
			trigger_error( "Invalid date/time data detected", E_USER_WARNING );
		}
		return( false );
	}
	
	/**
	 * Truncates text.
	 *
	 * Cuts a string to the length of $length and replaces the last characters
	 * with the ending if the text is longer than length.
	 *
	 * @param string  $text	String to truncate.
	 * @param integer $length Length of returned string, including ellipsis.
	 * @param string  $ending Ending to be appended to the trimmed string.
	 * @param boolean $exact If false, $text will not be cut mid-word
	 * @param boolean $considerHtml If true, HTML tags would be handled correctly
	 * @return string Trimmed string.
	 */
	private static function ajax_scroller_truncate($text, $length = 100, $ending = '...', $exact = true, $considerHtml = false) {
		if ($considerHtml) {
			// if the plain text is shorter than the maximum length, return the whole text
			if (strlen(preg_replace('/<.*?>/', '', $text)) <= $length) {
				return $text;
			}
			
			// splits all html-tags to scanable lines
			preg_match_all('/(<.+?>)?([^<>]*)/s', $text, $lines, PREG_SET_ORDER);

			$total_length = strlen($ending);
			$open_tags = array();
			$truncate = '';
			
			foreach ($lines as $line_matchings) {
				// if there is any html-tag in this line, handle it and add it (uncounted) to the output
				if (!empty($line_matchings[1])) {
					// if it's an "empty element" with or without xhtml-conform closing slash (f.e. <br/>)
					if (preg_match('/^<(\s*.+?\/\s*|\s*(img|br|input|hr|area|base|basefont|col|frame|isindex|link|meta|param)(\s.+?)?)>$/is', $line_matchings[1])) {
						// do nothing
					// if tag is a closing tag (f.e. </b>)
					} else if (preg_match('/^<\s*\/([^\s]+?)\s*>$/s', $line_matchings[1], $tag_matchings)) {
						// delete tag from $open_tags list
						$pos = array_search($tag_matchings[1], $open_tags);
						if ($pos !== false) {
							unset($open_tags[$pos]);
						}
					// if tag is an opening tag (f.e. <b>)
					} else if (preg_match('/^<\s*([^\s>!]+).*?>$/s', $line_matchings[1], $tag_matchings)) {
						// add tag to the beginning of $open_tags list
						array_unshift($open_tags, strtolower($tag_matchings[1]));
					}
					// add html-tag to $truncate'd text
					$truncate .= $line_matchings[1];
				}
				
				// calculate the length of the plain text part of the line; handle entities as one character
				$content_length = strlen(preg_replace('/&[0-9a-z]{2,8};|&#[0-9]{1,7};|&#x[0-9a-f]{1,6};/i', ' ', $line_matchings[2]));
				if ($total_length+$content_length > $length) {
					// the number of characters which are left
					$left = $length - $total_length;
					$entities_length = 0;
					// search for html entities
					if (preg_match_all('/&[0-9a-z]{2,8};|&#[0-9]{1,7};|&#x[0-9a-f]{1,6};/i', $line_matchings[2], $entities, PREG_OFFSET_CAPTURE)) {
						// calculate the real length of all entities in the legal range
						foreach ($entities[0] as $entity) {
							if ($entity[1]+1-$entities_length <= $left) {
								$left--;
								$entities_length += strlen($entity[0]);
							} else {
								// no more characters left
								break;
							}
						}
					}
					$truncate .= substr($line_matchings[2], 0, $left+$entities_length);
					// maximum lenght is reached, so get off the loop
					break;
				} else {
					$truncate .= $line_matchings[2];
					$total_length += $content_length;
				}
				
				// if the maximum length is reached, get off the loop
				if($total_length >= $length) {
					break;
				}
			}
		} else {
			if (strlen($text) <= $length) {
				return $text;
			} else {
				$truncate = substr($text, 0, $length - strlen($ending));
			}
		}
		
		// if the words shouldn't be cut in the middle...
		if (!$exact) {
			// ...search the last occurance of a space...
			$spacepos = strrpos($truncate, ' ');
			if (isset($spacepos)) {
				// ...and cut the text in this position
				$truncate = substr($truncate, 0, $spacepos);
			}
		}
		
		// add the defined ending to the text
		$truncate .= $ending;
		
		if($considerHtml) {
			// close all unclosed html-tags
			foreach ($open_tags as $tag) {
				$truncate .= '</' . $tag . '>';
			}
		}
		
		return $truncate;
	}
	
}
