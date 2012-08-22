<?php
/**
* @version 2.0.0
* @package RSTickets! Pro 2.0.0
* @copyright (C) 2010 www.rsjoomla.com
* @license GPL, http://www.gnu.org/licenses/gpl-2.0.html
*/

defined('_JEXEC') or die('Restricted access');

jimport( 'joomla.application.component.view');

class RSTicketsProViewNotes extends JView
{
	function display($tpl = null)
	{
		$mainframe =& JFactory::getApplication();
		
		$this->assign('date_format', RSTicketsProHelper::getConfig('date_format'));
		$this->assign('permissions', $this->get('permissions'));
		$this->assign('ticket_id', JRequest::getInt('ticket_id'));
		
		$task = JRequest::getCmd('task');
		if ($task == 'edit')
		{
			$this->assignRef('row', $this->get('note'));
		}
		else
		{
			$this->assign('avatar', RSTicketsProHelper::getConfig('avatars'));
			$this->assignRef('notes', $this->get('notes'));
			$this->assignRef('pagination', $this->get('pagination'));
			$this->assignRef('limitstart', JRequest::getInt('limitstart', 0));
		}
		
		parent::display();
	}
}