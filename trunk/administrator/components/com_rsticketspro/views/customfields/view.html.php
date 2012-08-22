<?php
/**
* @version 2.0.0
* @package RSTickets! Pro 2.0.0
* @copyright (C) 2010 www.rsjoomla.com
* @license GPL, http://www.gnu.org/licenses/gpl-2.0.html
*/

defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.view');

class RSTicketsProViewCustomFields extends JView
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
			JToolBarHelper::title('RSTickets! Pro <small>['.JText::_('RST_EDIT_PRIORITY').']</small>','rsticketspro');
			
			JToolBarHelper::apply();
			JToolBarHelper::save();
			JToolBarHelper::cancel();
			
			$row = $this->get('customfield');
			$this->assignRef('row', $row);
			
			$type = array(
				JHTML::_('select.option', 'freetext', JText::_('RST_FREETEXT')),
				JHTML::_('select.option', 'textbox', JText::_('RST_TEXTBOX')),
				JHTML::_('select.option', 'textarea', JText::_('RST_TEXTAREA')),
				JHTML::_('select.option', 'select', JText::_('RST_SELECT')),
				JHTML::_('select.option', 'multipleselect', JText::_('RST_MULTIPLESELECT')),
				JHTML::_('select.option', 'checkbox', JText::_('RST_CHECKBOX')),
				JHTML::_('select.option', 'radio', JText::_('RST_RADIO')),
				JHTML::_('select.option', 'calendar', JText::_('RST_CALENDAR')),
				JHTML::_('select.option', 'calendartime', JText::_('RST_CALENDARTIME'))
			);
			$lists['type'] = JHTML::_('select.genericlist', $type, 'type', '', 'value', 'text', $row->type);
			
			$lists['required'] = JHTML::_('select.booleanlist', 'required', '', $row->required);
			
			$lists['published'] = JHTML::_('select.booleanlist','published','class="inputbox"',$row->published);
			$this->assignRef('lists', $lists);
		}
		else
		{
			JToolBarHelper::addNewX('edit');
			JToolBarHelper::editListX('edit');
			JToolBarHelper::spacer();
			
			JToolBarHelper::publishList();
			JToolBarHelper::unpublishList();
			JToolBarHelper::spacer();
			
			JToolBarHelper::deleteList('RST_CONFIRM_DELETE');
			JToolBarHelper::divider();
			
			JToolBarHelper::back('Back', "index.php?option=com_rsticketspro&view=departments");
			
			$filter_state = $mainframe->getUserStateFromRequest('rsticketspro.filter_state', 'filter_state');
			$mainframe->setUserState('rsticketspro.filter_state', $filter_state);
			$lists['state']	= JHTML::_('grid.state', $filter_state);
			$this->assignRef('lists', $lists);
			
			$this->assignRef('sortColumn', JRequest::getVar('filter_order','ordering'));
			$this->assignRef('sortOrder', JRequest::getVar('filter_order_Dir','ASC'));
			
			$this->assignRef('customfields', $this->get('customfields'));
			$this->assignRef('pagination', $this->get('pagination'));
			
			$filter_word = JRequest::getCmd('search', '');
			$this->assignRef('filter_word', $filter_word);
		}
		$this->assignRef('department_id', $this->get('departmentid'));
		
		parent::display($tpl);
	}
}