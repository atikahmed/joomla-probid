
<?php

	defined('_JEXEC') or die('Restricted access');
	require_once (dirname(__FILE__).DS.'helper.php');
	
	JHtml::_('behavior.framework', true);
	$uniqid	= $module->id;
	
	$slideshow = ModPtslideshowHelper::getSlideshow($params->get('category_'));
	$catSlideshow = ModPtslideshowHelper::getCategory($params->get('category_'));

	$moduleclass_sfx = htmlspecialchars($params->get('moduleclass_sfx'));
	
	$doc =& JFactory::getDocument();
	
	if($params->get('load_jquery') == 1)
	{
		$doc->addScript(JURI::base()."modules/mod_ptslideshow/js/jquery-1.7.js"); // load js
	}

	$width = $params->get('width');
	$height = $params->get('height');
	$button_width = $params->get('button_width');
	$button_height = $params->get('button_height');
	$button_margin = $params->get('button_margin');
	$auto_start = $params->get('auto_start');
	$delay = $params->get('delay');
	$transition = $params->get('transition');
	$transition_speed = $params->get('transition_speed');
	$auto_center = $params->get('auto_center');
	$cpanel_position = $params->get('cpanel_position');
	$cpanel_align = $params->get('cpanel_align');
	$timer_align = $params->get('timer_align');
	$display_thumbs = $params->get('display_thumbs');
	$display_dbuttons = $params->get('display_dbuttons');
	$display_playbutton = $params->get('display_playbutton');
	$display_numbers = $params->get('display_numbers');
	$display_timer = $params->get('display_timer');
	$mouseover_pause = $params->get('mouseover_pause');
	$cpanel_mouseover = $params->get('cpanel_mouseover');
	$text_mouseover = $params->get('text_mouseover');
	$text_effect = $params->get('text_effect');
	$text_sync = $params->get('text_sync');
	$tooltip_type = $params->get('tooltip_type');
	$shuffle = $params->get('shuffle');
	$block_size = $params->get('block_size');
	$vert_size = $params->get('vert_size');
	$horz_size = $params->get('horz_size');
	$block_delay = $params->get('block_delay');
	$vstripe_delay = $params->get('vstripe_delay');
	$hstripe_delay = $params->get('hstripe_delay');
	
	$load_rotator_css = $params->get('load_rotator_css');
	if($load_rotator_css == 1)
	{
		$doc->addStyleSheet(JURI::base()."modules/mod_ptslideshow/css/wt-rotator.css"); // load css
	}
	
	$load_rotator = $params->get('load_rotator');
	if($load_rotator == 1)
	{
		//$doc->addScript(JURI::base()."modules/mod_ptslideshow/js/jquery.wt-rotator.min.js"); // load js
		$doc->addScript(JURI::base()."modules/mod_ptslideshow/js/jquery.wt-rotator.js"); // load js
	}
			
	require(JModuleHelper::getLayoutPath('mod_ptslideshow'));
?>