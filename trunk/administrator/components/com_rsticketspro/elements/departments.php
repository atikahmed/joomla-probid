<?php
/**
* @version 2.0.0
* @package RSTickets! Pro 2.0.0
* @copyright (C) 2010 www.rsjoomla.com
* @license GPL, http://www.gnu.org/licenses/gpl-2.0.html
*/

defined('_JEXEC') or die('Restricted access');
JHTML::_('behavior.tooltip');

require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_rsticketspro'.DS.'helpers'.DS.'rsticketspro.php');

class JElementDepartments extends JElement
{
	/**
	* Element name
	*
	* @access       protected
	* @var          string
	*/
	var    $_name = 'Departments';

	function fetchElement($name, $value, &$node, $control_name)
	{
		// Base name of the HTML control.
		$ctrl  = $control_name .'['. $name .']';

		// Construct an array of the HTML OPTION statements.
		$options = array ();

		// Construct the various argument calls that are supported.
		$attribs       = ' ';
		if ($v = $node->attributes( 'class' )) {
				$attribs       .= 'class="'.$v.'"';
		} else {
				$attribs       .= 'class="inputbox"';
		}
		if ($m = $node->attributes( 'multiple' ))
		{
				$attribs       .= '';
				$ctrl          .= '';
		}
		
		$novalue = new stdClass();
		$novalue->value='0';
		$novalue->text=JText::_('RST_DEPARTMENTS_NO_VALUE');
		$novalue->disabled='';
		
		$options = RSTicketsProHelper::getDepartments();
		array_unshift($options,$novalue);

		
		// Render the HTML SELECT list.
		return JHTML::_('select.genericlist', $options, $ctrl, $attribs, 'value', 'text', $value, $control_name.$name );
	}
}
?>