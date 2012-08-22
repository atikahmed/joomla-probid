<?php
/**
* @version 1.0.0
* @package RSMembership! 1.0.0
* @copyright (C) 2009-2010 www.rsjoomla.com
* @license GPL, http://www.gnu.org/licenses/gpl-2.0.html
*/

defined('_JEXEC') or die('Restricted access');

JHTML::_('behavior.tooltip');
jimport('joomla.form.formfield');

class JFormFieldMembershipCategory extends JFormField
{
	/**
	* Element name
	*
	* @access       protected
	* @var          string
	*/
	protected $_name = 'Membership Category';

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
		
		$db = JFactory::getDBO();
		
		$options = array();
		$options[0] = new stdClass();
		$options[0]->id = 0;
		$options[0]->name = JText::_('RSM_NO_CATEGORY');
		$db->setQuery("SELECT * FROM #__rsmembership_categories ORDER BY ordering");
		$options = array_merge($options, $db->loadObjectList());
		
		if ($value == '')
			$value = $options;

		// Render the HTML SELECT list.
		return JHTML::_('select.genericlist', $options, $ctrl, $attribs, 'id', 'name', $value, $ctrl);
	}
}
?>