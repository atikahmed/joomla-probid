<?php
/**
 * @version     1.0.0
 * @package     com_ptslideshow
 * @copyright   Copyright (C) 2012. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @author      Created by com_combuilder - http://www.notwebdesign.com
 */

defined('JPATH_BASE') or die;

jimport('joomla.html.html');
jimport('joomla.form.formfield');

/**
 * Supports an HTML select list of categories
 */
class JFormFieldHeader extends JFormField
{
	/**
	 * The form field type.
	 *
	 * @var		string
	 * @since	1.6
	 */
	protected $type = 'header';

	/**
	 * Method to get the field input markup.
	 *
	 * @return	string	The field input markup.
	 * @since	1.6
	 */
	protected function getInput()
	{
		return JElementHeader::fetchElement($this->name, $this->value, $this->element, $this->options['control']);
	}
	
	protected function getLabel()
	{
		return '';
	}
}


jimport('joomla.html.parameter.element');

class JElementHeader extends JElement {

	var	$_name = 'header';

	function fetchElement($name, $value, &$node, $control_name){
		$document = & JFactory::getDocument();
		$style = ".paramHeaderContainer { clear:both; font-weight:bold; font-size:12px; color:#369; margin:12px 0 4px; padding:0; background: #d5e7fa; border-bottom:2px solid #96b0cb; float:left; width:100%; }"
				. ".paramHeaderContent { padding:6px 8px; }"
				. ".pane-sliders ul.adminformlist li {	margin:0; padding:0; list-style:none; clear:both; }"
				. ".pane-sliders ul.adminformlist li label { clear:left; background:#f6f6f6; border-bottom:1px solid #e9e9e9; border-right:1px solid #e9e9e9; margin:0 4px 1px 0; padding:4px; color:#666; font-weight:bold; text-align:right; font-size:11px; width:140px; }"
				. ".pane-sliders ul.adminformlist li fieldset label { clear:none; width:auto; background:none; font-weight:normal; border:none; padding:0; text-align:left; margin:5px 0; width:auto; }";
		$document->addStyleDeclaration($style);
	}

	function fetchTooltip($label, $description, &$node, $control_name, $name){
		return NULL;
	}
}
