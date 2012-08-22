<?php
/**
* @version 2.0.0
* @package RSTickets! Pro 2.0.0
* @copyright (C) 2010 www.rsjoomla.com
* @license GPL, http://www.gnu.org/licenses/gpl-2.0.html
*/

defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.view');

class RSTicketsProViewStaff extends JView
{
	function display($tpl = null)
	{
		$mainframe =& JFactory::getApplication();
		
		JToolBarHelper::title('RSTickets! Pro','rsticketspro');
		
		JSubMenuHelper::addEntry(JText::_('RST_MANAGE_TICKETS'), 'index.php?option=com_rsticketspro&view=tickets');
		JSubMenuHelper::addEntry(JText::_('RST_DEPARTMENTS'), 'index.php?option=com_rsticketspro&view=departments');
		JSubMenuHelper::addEntry(JText::_('RST_GROUPS'), 'index.php?option=com_rsticketspro&view=groups');
		JSubMenuHelper::addEntry(JText::_('RST_STAFF_MEMBERS'), 'index.php?option=com_rsticketspro&view=staff', true);
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
			JToolBarHelper::title('RSTickets! Pro <small>['.JText::_('RST_EDIT_STAFF').']</small>','rsticketspro');
			
			JToolBarHelper::apply();
			JToolBarHelper::save();
			JToolBarHelper::cancel();
			
			$this->assignRef('editor', JFactory::getEditor());
			
			$row = $this->get('staffmember');
			$this->assignRef('row', $row);
			
			$groups = $this->get('groups');
			$lists['group_id'] = JHTML::_('select.genericlist', $groups, 'group_id', 'class="inputbox"', 'id', 'name', $row->group_id);
			
			$departments = $this->get('departments');
			$lists['department_id'] = JHTML::_('select.genericlist', $departments, 'department_id[]', 'class="inputbox" multiple="multiple" size="5"', 'id', 'name', $row->departments);
			
			$priorities = $this->get('priorities');
			$lists['priority_id'] = JHTML::_('select.genericlist', $priorities, 'priority_id', 'class="inputbox"', 'id', 'name', $row->priority_id);
			
			$this->assignRef('lists', $lists);
		}
		else
		{
			JToolBarHelper::addNewX('edit', JText::_('RST_NEW_STAFF_MEMBER'));
			JToolBarHelper::editListX('edit');
			JToolBarHelper::spacer();
			
			JToolBarHelper::deleteList('RST_CONFIRM_DELETE_STAFF');
			
			$this->assignRef('sortColumn', JRequest::getVar('filter_order','g.name'));
			$this->assignRef('sortOrder', JRequest::getVar('filter_order_Dir','ASC'));
			
			$this->assignRef('staff', $this->get('staff'));
			$this->assignRef('pagination', $this->get('pagination'));
			
			$filter_word = JRequest::getCmd('search', '');
			$this->assignRef('filter_word', $filter_word);
		}
		
		parent::display($tpl);
	}
}