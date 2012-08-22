<?php
/**
* @version 2.0.0
* @package RSTickets! Pro 2.0.0
* @copyright (C) 2010 www.rsjoomla.com
* @license GPL, http://www.gnu.org/licenses/gpl-2.0.html
*/

defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.controller');

class RSTicketsProControllerTicket extends RSTicketsProController
{
	function __construct()
	{
		parent::__construct();
	}

	function flag()
	{
		$model = $this->getModel('ticket');
		
		if (!RSTicketsProHelper::isStaff())
			die();
		
		$cid = JRequest::getInt('cid', '', 'post');
		$flagged = JRequest::getInt('flagged', '', 'post');
		
		@ob_end_clean();
		$model->setFlag($flagged);
		
		die();
	}
	
	function feedback()
	{
		$model = $this->getModel('ticket');
		
		// only customers can send feedback
		if (RSTicketsProHelper::isStaff())
			die();
		
		// the ticket must be closed
		if ($model->_ticket->status_id != 2)
			die();
		
		$cid = JRequest::getInt('cid', '', 'post');
		$feedback = JRequest::getInt('feedback', '', 'post');
		
		@ob_end_clean();
		$model->setFeedback($feedback);
		
		die();
	}
	
	function close()
	{
		$mainframe =& JFactory::getApplication();
		
		$model = $this->getModel('ticket');
		$url = RSTicketsProHelper::route('index.php?option=com_rsticketspro&view=ticket&cid='.$model->_ticket->id.':'.JFilterOutput::stringURLSafe($model->_ticket->subject), false);

		$can_close = $model->is_staff ? $model->_permissions->change_ticket_status : RSTicketsProHelper::getConfig('allow_ticket_closing');
		$user = JFactory::getUser();
		if ($model->_ticket->customer_id == $user->get('id'))
			$can_close = RSTicketsProHelper::getConfig('allow_ticket_closing');
		
		if (!$can_close)
		{
			JError::raiseWarning(500, JText::_('RST_CANNOT_CLOSE_TICKET'));
			$mainframe->redirect($url);
		}
		
		$model->closeTicket();
		
		$mainframe->redirect($url, JText::_('RST_TICKET_CLOSED_OK'));
	}
	
	function reopen()
	{
		$mainframe =& JFactory::getApplication();
		
		$model = $this->getModel('ticket');
		$url = RSTicketsProHelper::route('index.php?option=com_rsticketspro&view=ticket&cid='.$model->_ticket->id.':'.JFilterOutput::stringURLSafe($model->_ticket->subject), false);
		
		$can_open = $model->is_staff ? $model->_permissions->change_ticket_status : RSTicketsProHelper::getConfig('allow_ticket_reopening');
		$user = JFactory::getUser();
		if ($model->_ticket->customer_id == $user->get('id'))
			$can_open = RSTicketsProHelper::getConfig('allow_ticket_reopening');
		
		if (!$can_open)
		{
			JError::raiseWarning(500, JText::_('RST_CANNOT_REOPEN_TICKET'));
			$mainframe->redirect($url);
		}
		
		$model->reopenTicket();
		
		$mainframe->redirect($url, JText::_('RST_TICKET_REOPENED_OK'));
	}
	
	function delete()
	{
		$mainframe =& JFactory::getApplication();
		$server = JRequest::get('server');
		$referer = $server['HTTP_REFERER'];
		
		if (!RSTicketsProHelper::isStaff())
		{
			JError::raiseWarning(500, JText::_('RST_CANNOT_DELETE_TICKETS'));
			$mainframe->redirect($referer);
		}
		
		$model = $this->getModel('ticket');
		$model->_deleteTicket();
		
		$mainframe->redirect($referer, JText::_('RST_TICKET_DELETED_OK'));
	}
	
	function notify()
	{
		$mainframe =& JFactory::getApplication();
		$server = JRequest::get('server');
		$referer = $server['HTTP_REFERER'];
		
		if (!RSTicketsProHelper::isStaff())
		{
			JError::raiseWarning(500, JText::_('RST_CANNOT_NOTIFY_TICKETS'));
			$mainframe->redirect($referer);
		}
		
		$model = $this->getModel('ticket');
		$model->_notifyTicket();
		
		$mainframe->redirect($referer, JText::_('RST_TICKET_NOTIFIED_OK'));
	}
}
?>