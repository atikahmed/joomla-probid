<?php
/**
* @version 2.0.0
* @package RSTickets! Pro 2.0.0
* @copyright (C) 2010 www.rsjoomla.com
* @license GPL, http://www.gnu.org/licenses/gpl-2.0.html
*/

defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.model');

class RSTicketsProModelTicketMessage extends JModel
{
	var $_db;
	var $_message;
	var $is_staff;
	
	function __construct()
	{
		parent::__construct();
		
		$mainframe =& JFactory::getApplication();
		
		$user = JFactory::getUser();
		if ($user->get('guest'))
		{
			$link = JRequest::getURI();
			$link = base64_encode($link);
			$user_option = RSTicketsProHelper::isJ16() ? 'com_users' : 'com_user';
			$mainframe->redirect(RSTicketsProHelper::route('index.php?option='.$user_option.'&view=login&return='.$link, false));
		}
		
		$this->is_staff = RSTicketsProHelper::isStaff();
		if (!$this->is_staff)
		{
			JError::raiseWarning(500, JText::_('RST_CANNOT_UPDATE_TICKET_MESSAGE'));
			$mainframe->redirect(RSTicketsProHelper::route('index.php?option=com_rsticketspro&view=rsticketspro', false));
		}
		
		$permissions = RSTicketsProHelper::getCurrentPermissions();
		$message = $this->getRow();
		
		// can update his own replies
		if (!$permissions->update_ticket_replies && $message->user_id == $user->get('id'))
		{
			JError::raiseWarning(500, JText::_('RST_CANNOT_UPDATE_TICKET_MESSAGE'));
			$mainframe->redirect(RSTicketsProHelper::route('index.php?option=com_rsticketspro&view=rsticketspro', false));
		}
		
		// can update customer replies
		$is_customer = !RSTicketsProHelper::isStaff($message->user_id);
		if (!$permissions->update_ticket_replies_customers && $is_customer)
		{
			JError::raiseWarning(500, JText::_('RST_CANNOT_UPDATE_TICKET_MESSAGE'));
			$mainframe->redirect(RSTicketsProHelper::route('index.php?option=com_rsticketspro&view=rsticketspro', false));
		}
	
		// can update staff replies
		$is_other_staff = !$is_customer && $message->user_id != $user->get('id');
		if (!$permissions->update_ticket_replies_staff && $is_other_staff)
		{
			JError::raiseWarning(500, JText::_('RST_CANNOT_UPDATE_TICKET_MESSAGE'));
			$mainframe->redirect(RSTicketsProHelper::route('index.php?option=com_rsticketspro&view=rsticketspro', false));
		}
		
		$this->_db = JFactory::getDBO();
		
		$document =& JFactory::getDocument();
		$document->addStyleSheet(JURI::root(true).'/templates/system/css/system.css');
		$document->addStyleSheet(JURI::root(true).'/templates/system/css/general.css');
	}
	
	function save()
	{
		$row = $this->getRow();
		
		$post = JRequest::get('post');
		$post['message'] = JRequest::getVar('message', '', 'post', 'none', JREQUEST_ALLOWHTML);
		
		if (!RSTicketsProHelper::getConfig('allow_rich_editor'))
			$post['message'] = htmlspecialchars($post['message'], ENT_COMPAT, 'utf-8');
			
		$row->message = $post['message'];
		$row->store();
	}
	
	function getRow()
	{
		$cid = JRequest::getInt('cid');
		$row =& JTable::getInstance('RSTicketsPro_Ticket_Messages','Table');
		$row->load($cid);
		
		return $row;
	}
}
?>