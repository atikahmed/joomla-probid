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

class JFormFieldStatus extends JFormField
{
	/**
	* Element name
	*
	* @access       protected
	* @var          string
	*/
	protected $type = 'Status';

	protected function getInput()
	{
		// Base name of the HTML control.
		$ctrl   = $this->name;
		$value  = $this->value;

		// Construct an array of the HTML OPTION statements.
		$options = array ();

		// Construct the various argument calls that are supported.
		$attribs       = ' ';
		if (isset($this->element['size'])) {
			$attribs       .= 'size="'.(int) $this->element['size'].'"';
		}
		if (isset($this->element['class'])) {
			$attribs       .= 'class="'.(string) $this->element['class'].'"';
		} else {
			$attribs       .= 'class="inputbox"';
		}
		if (isset($this->element['multiple']))
		{
			$attribs       .= ' multiple="multiple"';
		}
		
		$options = RSTicketsProHelper::getStatuses();
		
		if (empty($value))
			$value = $options;

		// Render the HTML SELECT list.
		return JHTML::_('select.genericlist', $options, $ctrl, $attribs, 'value', 'text', $value, $this->name);
	}
}
?>