<?php
/**
 * getmodtab Module Entry Point
 * 
 * @package    
 * @subpackage 
 * @link 
 * @license        
 * 
 */
 
// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );
 
// Include the syndicate functions only once
require_once( dirname(__FILE__).DS.'helper.php' );

JHtml::_('behavior.framework', true);
$uniqid				= $module->id;


$document = JFactory::getDocument();
$document->addStylesheet(JURI::base(true) . '/modules/mod_getmodtab/assets/css/layout.css.php?id=' .$uniqid);//Load css
$document->addScript(JURI::base(true) . '/modules/mod_getmodtab/assets/js/hide.js');//Load javascript

if(strcmp($params->get('activator'), 'click') == 0)
	$activator = 'onclick';
else
	$activator = 'onmouseover';

$list = modgetmodtabHelper::getTabs($params);
 
require( JModuleHelper::getLayoutPath( 'mod_getmodtab' ) );
?>
