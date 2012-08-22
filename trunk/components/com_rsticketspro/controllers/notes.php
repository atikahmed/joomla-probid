<?php
/**
* @version 2.0.0
* @package RSTickets! Pro 2.0.0
* @copyright (C) 2010 www.rsjoomla.com
* @license GPL, http://www.gnu.org/licenses/gpl-2.0.html
*/

defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.controller');

class RSTicketsProControllerNotes extends RSTicketsProController
{
	function __construct()
	{
		parent::__construct();
	}
	
	function delete()
	{
		$model = $this->getModel('notes');
		$ticket_id = $model->remove();
		
		$this->setRedirect(RSTicketsProHelper::route('index.php?option=com_rsticketspro&view=notes&ticket_id='.$ticket_id.'&tmpl=component', false), JText::_('RST_DELETE_TICKET_NOTE_OK'));
	}
	
	function save()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit('Invalid Token');
		
		$model = $this->getModel('notes');
		$model->save();
		
		$ticket_id = JRequest::getInt('ticket_id');
			
		$this->setRedirect(RSTicketsProHelper::route('index.php?option=com_rsticketspro&view=notes&ticket_id='.$ticket_id.'&tmpl=component', false), JText::_('RST_ADD_TICKET_NOTE_OK'));
	}
	
	function update()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit('Invalid Token');
		
		$model = $this->getModel('notes');
		$model->update();
		
		$ticket_id = JRequest::getInt('ticket_id');
			
		$this->setRedirect(RSTicketsProHelper::route('index.php?option=com_rsticketspro&view=notes&ticket_id='.$ticket_id.'&tmpl=component', false), JText::_('RST_UPDATE_TICKET_NOTE_OK'));
	}
	
	function edit()
	{
		JRequest::setVar('view', 'notes');
		JRequest::setVar('layout', 'edit');
		
		parent::display();
	}
}
?>