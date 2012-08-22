<?php
/**
* @version 2.0.0
* @package RSTickets! Pro 2.0.0
* @copyright (C) 2010 www.rsjoomla.com
* @license GPL, http://www.gnu.org/licenses/gpl-2.0.html
*/

defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.model');

class RSTicketsProModelNotes extends JModel
{
	var $_data = null;
	var $_total = 0;
	var $_query = '';
	var $_pagination = null;
	var $_db = null;
	var $_permissions = null;
	
	var $params = null;
	
	function __construct()
	{
		parent::__construct();
		
		$mainframe =& JFactory::getApplication();
		$option    = 'com_rsticketspro';
		
		$user = JFactory::getUser();
		if ($user->get('guest'))
		{
			$link = JRequest::getURI();
			$link = base64_encode($link);
			$user_option = RSTicketsProHelper::isJ16() ? 'com_users' : 'com_user';
			$mainframe->redirect(RSTicketsProHelper::route('index.php?option='.$user_option.'&view=login&return='.$link, false));
		}
		
		if (!RSTicketsProHelper::isStaff())
		{
			JError::raiseWarning(500, JText::_('RST_STAFF_CANNOT_VIEW_NOTES'));
			$mainframe->redirect(RSTicketsProHelper::route('index.php?option=com_rsticketspro&view=rsticketspro', false));
		}
		
		$this->_permissions = RSTicketsProHelper::getCurrentPermissions();
		if (!$this->_permissions->view_notes)
		{
			JError::raiseWarning(500, JText::_('RST_STAFF_CANNOT_VIEW_NOTES'));
			$mainframe->redirect(RSTicketsProHelper::route('index.php?option=com_rsticketspro&view=rsticketspro', false));
		}
		
		$this->_db = JFactory::getDBO();
		
		// Get pagination request variables
		$limit		= JRequest::getVar('limit', $mainframe->getCfg('list_limit'), '', 'int');
		$limitstart	= JRequest::getVar('limitstart', 0, '', 'int');

		// In case limit has been changed, adjust it
		$limitstart = ($limit != 0 ? (floor($limitstart / $limit) * $limit) : 0);

		$this->setState($option.'.notes.limit', $limit);
		$this->setState($option.'.notes.limitstart', $limitstart);
		
		$this->_query = $this->_buildQuery();
		
		$document =& JFactory::getDocument();
		$document->addStyleSheet(JURI::root(true).'/templates/system/css/system.css');
		$document->addStyleSheet(JURI::root(true).'/templates/system/css/general.css');
	}
	
	function _buildQuery()
	{
		$mainframe =& JFactory::getApplication();
		
		$ticket_id = JRequest::getInt('ticket_id');
		$what = RSTicketsProHelper::getConfig('show_user_info');
		
		$query  = "SELECT n.*, u.".$this->_db->getEscaped($what)." AS user FROM #__rsticketspro_ticket_notes n LEFT JOIN #__users u ON (n.user_id = u.id) WHERE n.ticket_id='".$ticket_id."'";
		$query .= " ORDER BY n.`date` DESC";
		
		return $query;
	}
	
	function getNotes()
	{
		$option = 'com_rsticketspro';
		
		if (empty($this->_data))
			$this->_data = $this->_getList($this->_query, $this->getState($option.'.notes.limitstart'), $this->getState($option.'.notes.limit'));
		
		return $this->_data;
	}
	
	function getTotal()
	{
		if (empty($this->_total))
			$this->_total = $this->_getListCount($this->_query); 
		
		return $this->_total;
	}
	
	function getPagination()
	{
		if (empty($this->_pagination))
		{
			$option = 'com_rsticketspro';
			jimport('joomla.html.pagination');
			$this->_pagination = new JPagination($this->getTotal(), $this->getState($option.'.notes.limitstart'), $this->getState($option.'.notes.limit'));
		}
		
		return $this->_pagination;
	}
	
	function getPermissions()
	{
		return $this->_permissions;
	}
	
	function getNote()
	{
		$mainframe =& JFactory::getApplication();
		
		$cid = JRequest::getInt('cid');
		$note =& JTable::getInstance('RSTicketsPro_Ticket_Notes','Table');
		$note->load($cid);
		
		$user = JFactory::getUser();
		
		// can update his own notes
		if (!$this->_permissions->update_note && $note->user_id == $user->get('id'))
		{
			JError::raiseWarning(500, JText::_('RST_STAFF_CANNOT_EDIT_NOTE'));
			$mainframe->redirect(RSTicketsProHelper::route('index.php?option=com_rsticketspro&view=rsticketspro', false));
		}
		
		// can update other staff notes
		if (!$this->_permissions->update_note_staff && $note->user_id != $user->get('id'))
		{
			JError::raiseWarning(500, JText::_('RST_STAFF_CANNOT_EDIT_NOTE'));
			$mainframe->redirect(RSTicketsProHelper::route('index.php?option=com_rsticketspro&view=rsticketspro', false));
		}
		
		return $note;
	}
	
	function remove()
	{
		$mainframe =& JFactory::getApplication();
		
		$cid = JRequest::getInt('cid');
		$note =& JTable::getInstance('RSTicketsPro_Ticket_Notes','Table');
		$note->load($cid);
		
		$user = JFactory::getUser();
		
		// can delete his own notes
		if (!$this->_permissions->delete_note && $note->user_id == $user->get('id'))
		{
			JError::raiseWarning(500, JText::_('RST_STAFF_CANNOT_DELETE_NOTES'));
			$mainframe->redirect(RSTicketsProHelper::route('index.php?option=com_rsticketspro&view=rsticketspro', false));
		}
	
		// can delete other staff notes
		if (!$this->_permissions->delete_note_staff && $note->user_id != $user->get('id'))
		{
			JError::raiseWarning(500, JText::_('RST_STAFF_CANNOT_DELETE_NOTES'));
			$mainframe->redirect(RSTicketsProHelper::route('index.php?option=com_rsticketspro&view=rsticketspro', false));
		}
		
		$ticket_id = $note->ticket_id;	
		$this->_db->setQuery("DELETE FROM #__rsticketspro_ticket_notes WHERE id='".$cid."' LIMIT 1");
		$this->_db->query();
		
		return $ticket_id;
	}
	
	function save()
	{
		$mainframe =& JFactory::getApplication();
		
		// can add his own notes
		if (!$this->_permissions->add_note)
		{
			JError::raiseWarning(500, JText::_('RST_STAFF_CANNOT_ADD_NOTE'));
			$mainframe->redirect(RSTicketsProHelper::route('index.php?option=com_rsticketspro&view=rsticketspro', false));
		}
		
		$note =& JTable::getInstance('RSTicketsPro_Ticket_Notes','Table');
		$post = JRequest::get('post');
		$user = JFactory::getUser();
		$date = JFactory::getDate();
		
		$note->id = null;
		$note->ticket_id = (int) $post['ticket_id'];
		$note->user_id = $user->get('id');
		$note->text = $post['text'];
		$note->date = $date->toUnix();;
		
		if ($note->store())
			return true;
		else
		{
			JError::raiseWarning(500, $note->getError());
			return false;
		}
	}
	
	function update()
	{
		$note = $this->getNote();
		
		$post = JRequest::get('post');
		$note->text = $post['text'];
		$note->store();
	}
}
?>