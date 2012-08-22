<?php
/**
* @version 2.0.0
* @package RSTickets! Pro 2.0.0
* @copyright (C) 2010 www.rsjoomla.com
* @license GPL, http://www.gnu.org/licenses/gpl-2.0.html
*/

defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.view');
jimport('joomla.html.pane');

class RSTicketsProViewAllusers extends JView
{
	function display($tpl = null)
	{
		$mainframe =& JFactory::getApplication();
		
		$this->assignRef('sortColumn', JRequest::getVar('filter_order','name'));
		$this->assignRef('sortOrder', JRequest::getVar('filter_order_Dir','ASC'));
		
		$this->assignRef('users', $this->get('users'));
		$this->assignRef('pagination', $this->get('pagination'));
		
		$filter_word = JRequest::getCmd('search', '');
		$this->assignRef('filter_word', $filter_word);
		
		parent::display($tpl);
	}
}