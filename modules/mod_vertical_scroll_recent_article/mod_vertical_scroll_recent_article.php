<?php
/**
 * Vertical scroll recent article
 *
 * @package Vertical scroll recent article
 * @subpackage Vertical scroll recent article
 * @version   2.0 July 15, 2011
 * @author    Gopi http://www.gopiplus.com
 * @copyright Copyright (C) 2010 - 2011 www.gopiplus.com, LLC
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 *
 */

// no direct access
defined('_JEXEC') or die;

// Include the syndicate functions only once
require_once(dirname(__FILE__).DS.'helper.php');

@$args['vspost_height'] = $params->get('vspost_height');
@$args['vspost_display'] = $params->get('vspost_display');
@$args['vspost_category_id'] = $params->get('vspost_category_id');
@$args['vspost_no_of_items'] = $params->get('vspost_no_of_items');
@$args['vspost_no_of_chars'] = $params->get('vspost_no_of_chars');
@$args['vspost_order_field'] = $params->get('vspost_order_field');
@$args['vspost_order_by'] = $params->get('vspost_order_by');
@$args['vspost_use_cache'] = $params->get('vspost_use_cache');
@$args['moduleclass_sfx'] = $params->get('moduleclass_sfx');

$cache = & JFactory::getCache();

if (@$args['vspost_use_cache']) 
{
  $items = $cache->call(array('modVerticalScrollRecentArticleHelper','getArticleList'),$args);
}
else
{
  $items = modVerticalScrollRecentArticleHelper::getArticleList($args);
}

modVerticalScrollRecentArticleHelper::loadScripts($params);

// include the template for display
require(JModuleHelper::getLayoutPath('mod_vertical_scroll_recent_article'));

?>