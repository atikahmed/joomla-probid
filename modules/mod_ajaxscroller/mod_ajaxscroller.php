<?php
/*
* @package		AJAX Scroller
* @copyright	Copyright (C) 2008-2011 Emir Sakic, http://www.sakic.net. All rights reserved.
* @license		GNU/GPL, see LICENSE.TXT
*
* This program is free software; you can redistribute it and/or modify it
* under the terms of the GNU General Public License as published by the
* Free Software Foundation; either version 2 of the License, or
* (at your option) any later version.
* 
* This header must not be removed. Additional contributions/changes
* may be added to this header as long as no information is deleted.
*/

if (isset($_GET['c'])) {

	define( '_JEXEC', 1 );
	
	define( 'DS', DIRECTORY_SEPARATOR );

	define('JPATH_BASE', str_replace(DS.'modules'.DS.'mod_ajaxscroller', '', dirname(__FILE__)) );
	
	$temp = explode('modules', $_SERVER['REQUEST_URI']);
	$_SERVER['REQUEST_URI'] = $_SERVER['PHP_SELF'] = $_SERVER['SCRIPT_NAME'] = $temp[0].'index.php';

	require_once ( JPATH_BASE .DS.'includes'.DS.'defines.php' );
	require_once ( JPATH_BASE .DS.'includes'.DS.'framework.php' );
	require_once ( JPATH_BASE .DS.'libraries'.DS.'joomla'.DS.'html'.DS.'parameter.php' );
	
	// Instantiate the application.
	$app = JFactory::getApplication('site');

	// Initialise the application.
	$app->initialise();
	
	$router =& $app->getRouter();

	//JPluginHelper::importPlugin('system');
	//$app->triggerEvent('onAfterInitialise');

	if (empty($db)) {
		$db = &JFactory::getDbo();
	}

	$moduleid = JRequest::getInt('m');
	
	$query = "SELECT params"
		. "\n FROM #__modules "
		. "\n WHERE id = '$moduleid'";
	$db->setQuery( $query );
	$row = $db->loadResult();
	$params = new JParameter( $row );
	
	$type = $params->get( 'type', '' );
	$twitter_timeline = $params->get( 'twitter_timeline', 'user' );
	
	if ($type=='rss' || ($type=='twitter' && $twitter_timeline=='search')) {
		require_once dirname(__FILE__).'/helpers/rss.php';
		$content = modAjaxScrollerRssHelper::get($moduleid, $params);
	} else if ($type=='twitter') {
		require_once dirname(__FILE__).'/helpers/twitter.php';
		$content = modAjaxScrollerTwitterHelper::get($moduleid, $params);
	} else if ($type=='banners') {
		require_once dirname(__FILE__).'/helpers/banners.php';
		$content = modAjaxScrollerBannersHelper::get($moduleid, $params);
	} else {
		require_once dirname(__FILE__).'/helpers/content.php';
		$content = modAjaxScrollerContentHelper::get($moduleid, $params);
	}
	
	if (!defined('_ISO')) {
		define('_ISO','charset=utf-8');
	}

	header( "Expires: Mon, 26 Jul 1997 05:00:00 GMT" );
	header( "Last-Modified: ".gmdate( "D, d M Y H:i:s" )."GMT" );
	header( "Cache-Control: no-cache, must-revalidate" );
	header( "Pragma: no-cache" );
	header( "Content-Type: text/html; " . _ISO );
	echo $content;
	exit(1);
}

// no direct access
defined('_JEXEC') or die('Restricted access');

$direction = $params->get( 'direction', 'vertical' );
$items_count = intval( $params->get( 'items_count', 1 ) );
$effect = $params->get( 'effect', 'linear' );
$speed = $params->get( 'speed', 'normal' );
$show_nav = intval($params->get( 'show_nav', 1 ));
$show_load = intval($params->get( 'show_load', 1 ));
$autoplay = intval($params->get( 'autoplay', 0 ));
$show_play = intval($params->get( 'show_play', 1 ));
$delay = intval($params->get( 'delay', 5 ));
$type = $params->get( 'type', '' );
$twitter_timeline = $params->get( 'twitter_timeline', 'user' );
$remember = intval($params->get( 'remember', 0 ));
$ajax_off = intval($params->get( 'ajax_off', 0 ));
$include_mootools = intval($params->get( 'include_mootools', 0 ));

$relpath = 'modules/mod_ajaxscroller';
$path = JURI::root(true).'/'.$relpath;

if (file_exists($relpath.'/assets/css/style'.$module->id.'.css')) {
	$css = 'style'.$module->id.'.css';
} else {
	$css = 'style.css';
}

$document =& JFactory::getDocument();
//$document->addStyleSheet($path.'/scriptload.php?file[0]='.$css.'&amp;gzip=1');
$document->addStyleSheet($path.'/assets/css/'.$css);

if (!defined('AJAXSCRL_SCRIPT')) {
	define('AJAXSCRL_SCRIPT', 1);
	if ($include_mootools) {
		$document->addScript($path.'/scriptload.php?file[0]=mootools-core.js&amp;file[1]=mootools-more.js&amp;file[2]=script.js&amp;gzip=1');
		$document->_scripts = array_merge(array_slice($document->_scripts, -1), $document->_scripts);
	} else {
		JHtml::_('behavior.framework', true);
		$document->addScript($path.'/scriptload.php?file[0]=script.js&amp;gzip=1');
	}
	$document->addScriptDeclaration("
		var mPath = '$path/mod_ajaxscroller';
	");
}

$document->addScriptDeclaration("
	window.addEvent('domready', function() {
		asCreate(Array($module->id, '$direction', '$effect', '$speed', $show_nav, $show_load, $autoplay, $show_play, $delay, $remember));
	});
");

$content = '';

if ($remember && isset($_COOKIE['ajaxscrlstart'.$module->id])) {
	$start = $_COOKIE['ajaxscrlstart'.$module->id];
} else {
	$start = 0;
}

$content .= '<div id="ajaxscrl'.$module->id.'" class="ajaxscrl">';
$content .= '<div id="mContainer'.$module->id.'" class="mContainer">';
$content .= '<div id="mScroller'.$module->id.'" class="mScroller">';

if ($type=='rss' || ($type=='twitter' && $twitter_timeline=='search')) {
	require_once dirname(__FILE__).'/helpers/rss.php';
	$class = 'modAjaxScrollerRssHelper';
} else if ($type=='twitter') {
	require_once dirname(__FILE__).'/helpers/twitter.php';
	$class = 'modAjaxScrollerTwitterHelper';
} else if ($type=='banners') {
	require_once dirname(__FILE__).'/helpers/banners.php';
	$class = 'modAjaxScrollerBannersHelper';
} else {
	require_once dirname(__FILE__).'/helpers/content.php';
	$class = 'modAjaxScrollerContentHelper';
}

if ($ajax_off) {
	$i = 0;
	$break = false;
	for ($i=0; !$break; $i++) {
		//$item = $class::get($module->id, $params, $i*$items_count); // PHP 5.3.0+
		$item = call_user_func(array($class, 'get'), $module->id, $params, $i*$items_count);
		if ( preg_match('/<div id="m[0-9]+">(<div class="odd">)?<\/div>/', $item) || $i > 100 ) {
			$break = true;
		} else {
			$content .= $item;
		}
	}
} else {
	//$content .= $class::get($module->id, $params, $start); // PHP 5.3.0+
	$content .= call_user_func(array($class, 'get'), $module->id, $params, $start);
}

$content .= '</div>';
$content .= '</div>';
$content .= '</div>';
