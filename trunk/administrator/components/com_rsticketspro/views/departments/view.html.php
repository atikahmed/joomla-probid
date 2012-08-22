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

class RSTicketsProViewDepartments extends JView
{
	function display($tpl = null)
	{
		$mainframe =& JFactory::getApplication();
		
		JToolBarHelper::title('RSTickets! Pro','rsticketspro');
		
		JSubMenuHelper::addEntry(JText::_('RST_MANAGE_TICKETS'), 'index.php?option=com_rsticketspro&view=tickets');
		JSubMenuHelper::addEntry(JText::_('RST_DEPARTMENTS'), 'index.php?option=com_rsticketspro&view=departments', true);
		JSubMenuHelper::addEntry(JText::_('RST_GROUPS'), 'index.php?option=com_rsticketspro&view=groups');
		JSubMenuHelper::addEntry(JText::_('RST_STAFF_MEMBERS'), 'index.php?option=com_rsticketspro&view=staff');
		JSubMenuHelper::addEntry(JText::_('RST_PRIORITIES'), 'index.php?option=com_rsticketspro&view=priorities');
		JSubMenuHelper::addEntry(JText::_('RST_STATUSES'), 'index.php?option=com_rsticketspro&view=statuses');
		JSubMenuHelper::addEntry(JText::_('RST_KNOWLEDGEBASE'), 'index.php?option=com_rsticketspro&view=knowledgebase');
		JSubMenuHelper::addEntry(JText::_('RST_EMAIL_MESSAGES'), 'index.php?option=com_rsticketspro&view=emails');
		JSubMenuHelper::addEntry(JText::_('RST_CONFIGURATION'), 'index.php?option=com_rsticketspro&view=configuration');
		$mainframe->triggerEvent('onAfterTicketsMenu');
		JSubMenuHelper::addEntry(JText::_('RST_UPDATES'), 'index.php?option=com_rsticketspro&view=updates');
		
		$task = JRequest::getVar('task','');
		
		if ($task == 'edit')
		{
			JToolBarHelper::title('RSTickets! Pro <small>['.JText::_('RST_EDIT_DEPARTMENT').']</small>','rsticketspro');
			
			JToolBarHelper::apply();
			JToolBarHelper::save();
			JToolBarHelper::cancel();
			
			$row = $this->get('department');
			$this->assignRef('row', $row);
			
			$assignment_type = array(
				JHTML::_('select.option', '0', JText::_('RST_STATIC')),
				JHTML::_('select.option', '1', JText::_('RST_AUTOMATIC'))
			);
			$lists['assignment_type'] = JHTML::_('select.genericlist', $assignment_type, 'assignment_type', '', 'value', 'text', $row->assignment_type);
			
			$generation_rule = array(
				JHTML::_('select.option', '0', JText::_('RST_SEQUENTIAL')),
				JHTML::_('select.option', '1', JText::_('RST_RANDOM'))
			);
			$lists['generation_rule'] = JHTML::_('select.genericlist', $generation_rule, 'generation_rule', '', 'value', 'text', $row->generation_rule);
			
			$priority = RSTicketsProHelper::getPriorities();
			$lists['priority'] = JHTML::_('select.genericlist', $priority, 'priority_id', '', 'value', 'text', $row->priority_id);
			
			$lists['email_use_global'] = JHTML::_('select.booleanlist', 'email_use_global', 'class="inputbox" onclick="rst_email_enable(this.value)"', $row->email_use_global);
			$lists['customer_send_email'] = JHTML::_('select.booleanlist', 'customer_send_email', '', $row->customer_send_email);
			$lists['customer_send_copy_email'] = JHTML::_('select.booleanlist', 'customer_send_copy_email', '', $row->customer_send_copy_email);
			$lists['customer_attach_email'] = JHTML::_('select.booleanlist', 'customer_attach_email', '', $row->customer_attach_email);
			$lists['staff_send_email'] = JHTML::_('select.booleanlist', 'staff_send_email', '', $row->staff_send_email);
			$lists['staff_attach_email'] = JHTML::_('select.booleanlist', 'staff_attach_email', '', $row->staff_attach_email);
			
			$disable_uploads 	 = false;
			$upload_max_filesize = false;
			$max_file_uploads	 = false;
			if (function_exists('ini_get') && is_callable('ini_get'))
			{
				if (!ini_get('file_uploads'))
					$disable_uploads = true;
				$upload_max_filesize = ini_get('upload_max_filesize');
				$max_file_uploads 	 = ini_get('max_file_uploads');
			}
			
			$upload = array(
				JHTML::_('select.option', '1', JText::_('RST_EVERYONE'), 'value', 'text', $disable_uploads),
				JHTML::_('select.option', '2', JText::_('RST_REGISTERED'), 'value', 'text', $disable_uploads),
				JHTML::_('select.option', '0', JText::_('RST_NOBODY'))
			);
			$lists['upload'] = JHTML::_('select.genericlist', $upload, 'upload', '', 'value', 'text', $row->upload);
			
			$lists['notify_assign'] = JHTML::_('select.booleanlist', 'notify_assign', '', $row->notify_assign);
			
			$lists['published'] = JHTML::_('select.booleanlist','published','class="inputbox"',$row->published);
			$this->assignRef('lists', $lists);
			
			$params = array();
			$params['startOffset'] = JRequest::getInt('tabposition', 0);
			$tabs =& JPane::getInstance('Tabs', $params, true);
			$this->assignRef('tabs', $tabs);
			$this->assignRef('uploads_disabled', $disable_uploads);
			$this->assignRef('upload_max_filesize', $upload_max_filesize);
			$this->assignRef('max_file_uploads', $max_file_uploads);
		}
		else
		{
			JToolBarHelper::addNewX('edit');
			JToolBarHelper::editListX('edit');
			JToolBarHelper::spacer();
			
			JToolBarHelper::publishList();
			JToolBarHelper::unpublishList();
			JToolBarHelper::spacer();
			
			JToolBarHelper::deleteList('RST_CONFIRM_DELETE_DEPARTMENT');
			
			$filter_state = $mainframe->getUserStateFromRequest('rsticketspro.filter_state', 'filter_state');
			$mainframe->setUserState('rsticketspro.filter_state', $filter_state);
			$lists['state']	= JHTML::_('grid.state', $filter_state);
			$this->assignRef('lists', $lists);
			
			$this->assignRef('sortColumn', JRequest::getVar('filter_order','ordering'));
			$this->assignRef('sortOrder', JRequest::getVar('filter_order_Dir','ASC'));
			
			$this->assignRef('departments', $this->get('departments'));
			$this->assignRef('pagination', $this->get('pagination'));
			
			$filter_word = JRequest::getCmd('search', '');
			$this->assignRef('filter_word', $filter_word);
		}
		
		parent::display($tpl);
	}
}