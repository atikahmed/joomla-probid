<?php
/**
* @version 2.0.0
* @package RSTickets! Pro 2.0.0
* @copyright (C) 2010 www.rsjoomla.com
* @license GPL, http://www.gnu.org/licenses/gpl-2.0.html
*/

defined('_JEXEC') or die('Restricted access');

jimport( 'joomla.application.component.view');

class RSTicketsProViewTicketMessage extends JView
{
	function display($tpl = null)
	{
		$mainframe =& JFactory::getApplication();
		
		$this->assignRef('row', $this->get('row'));
		
		$this->assign('use_editor', RSTicketsProHelper::getConfig('allow_rich_editor'));
		$this->assignRef('editor', JFactory::getEditor());
		
		parent::display();
	}
}