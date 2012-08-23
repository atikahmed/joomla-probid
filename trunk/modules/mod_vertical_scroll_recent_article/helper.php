<?php
/**
 * Vertical scroll recent article
 *
 * @package Vertical scroll recent article
 * @subpackage Vertical scroll recent article
 * @version   2.0 August, 2011
 * @author    Gopi http://www.gopiplus.com
 * @copyright Copyright (C) 2010 - 2011 www.gopiplus.com, LLC
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 *
 */

// no direct access
defined('_JEXEC') or die;

class modVerticalScrollRecentArticleHelper
{
	public function loadScripts(&$params)
	{
		$doc = &JFactory::getDocument();
		$doc->addScript(JURI::Root(true).'/modules/mod_vertical_scroll_recent_article/mod_vertical_scroll_recent_article.js');
	}
	
	public function getArticleList($args)
	{
      $db = &JFactory::getDBO();

      $nullDate = $db->getNullDate();
      $date =& JFactory::getDate();
      $now = $date->toMySQL();

	  $vspost_category_id = $args['vspost_category_id'];
	  $vspost_no_of_items = $args['vspost_no_of_items'];
	  $vspost_no_of_chars = $args['vspost_no_of_chars'];
	  $vspost_order_field = $args['vspost_order_field'];
	  $vspost_use_cache = $args['vspost_use_cache'];
	  $vspost_order_by = $args['vspost_order_by'];
      
      if ($vspost_no_of_chars == 0){
        $vspost_no_of_chars=999;
      }
      
      $query  = "select cn.id, ca.id as catid, ca.alias as catalias, cn.alias as conalias, cn.sectionid, ";
			$query .= "CASE WHEN CHAR_LENGTH(cn.alias) THEN CONCAT_WS(':', cn.id, cn.alias) ELSE cn.id END as slug, ";
			$query .= "CASE WHEN CHAR_LENGTH(ca.alias) THEN CONCAT_WS(':', ca.id, ca.alias) ELSE ca.id END as catslug, ";
      $query .= "if (length(cn.title)>".$vspost_no_of_chars.",concat(substring(cn.title,1,".$vspost_no_of_chars."),'...'),cn.title) as title, ";
      $query .= "cn.title as fulltitle ";
      $query .= "from #__content as cn , #__categories as ca ";
      $query .= "where cn.id <> '' ";
      if($vspost_category_id != ""){
        $query .= " AND cn.catid in (".$vspost_category_id.") ";
      }
      
      $query .= " and state = 1 and ca.id=cn.catid ";

      $query .= ' and ( publish_up = '.$db->Quote($nullDate).' or publish_up <= '.$db->Quote($now).' )';
      $query .= ' and ( publish_down = '.$db->Quote($nullDate).' or publish_down >= '.$db->Quote($now).' )';
      
      if ($vspost_order_field == "random"){
      	$query .= " order by RAND() ";
      }else{
        $query .= " order by ".$vspost_order_field." ".$vspost_order_by;
      }
      
      if ($vspost_no_of_items != 0) {
        $query .= " limit ".$vspost_no_of_items;
      }
      
      $db->setQuery($query);
      $items = ($items = $db->loadObjectList())?$items:array();
      return $items;
    }	
}
?>