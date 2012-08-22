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

class RSTicketsProViewGroups extends JView
{
	function display($tpl = null)
	{
		$mainframe =& JFactory::getApplication();
		
		JToolBarHelper::title('RSTickets! Pro','rsticketspro');
		
		JSubMenuHelper::addEntry(JText::_('RST_MANAGE_TICKETS'), 'index.php?option=com_rsticketspro&view=tickets');
		JSubMenuHelper::addEntry(JText::_('RST_DEPARTMENTS'), 'index.php?option=com_rsticketspro&view=departments');
		JSubMenuHelper::addEntry(JText::_('RST_GROUPS'), 'index.php?option=com_rsticketspro&view=groups', true);
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
			JToolBarHelper::title('RSTickets! Pro <small>['.JText::_('RST_EDIT_GROUP').']</small>','rsticketspro');
			
			JToolBarHelper::apply();
			JToolBarHelper::save();
			JToolBarHelper::cancel();
			
			$row = $this->get('group');
			$this->assignRef('row', $row);
			
			$lists['add_ticket'] = JHTML::_('select.booleanlist','add_ticket','class="inputbox"',$row->add_ticket);
			$lists['add_ticket_customers'] = JHTML::_('select.booleanlist','add_ticket_customers','class="inputbox"',$row->add_ticket_customers);
			$lists['add_ticket_staff'] = JHTML::_('select.booleanlist','add_ticket_staff','class="inputbox"',$row->add_ticket_staff);
			$lists['update_ticket'] = JHTML::_('select.booleanlist','update_ticket','class="inputbox"',$row->update_ticket);
			$lists['update_ticket_custom_fields'] = JHTML::_('select.booleanlist','update_ticket_custom_fields','class="inputbox"',$row->update_ticket_custom_fields);
			$lists['delete_ticket'] = JHTML::_('select.booleanlist','delete_ticket','class="inputbox"',$row->delete_ticket);
			$lists['answer_ticket'] = JHTML::_('select.booleanlist','answer_ticket','class="inputbox"',$row->answer_ticket);
			$lists['update_ticket_replies'] = JHTML::_('select.booleanlist','update_ticket_replies','class="inputbox"',$row->update_ticket_replies);
			$lists['update_ticket_replies_customers'] = JHTML::_('select.booleanlist','update_ticket_replies_customers','class="inputbox"',$row->update_ticket_replies_customers);
			$lists['update_ticket_replies_staff'] = JHTML::_('select.booleanlist','update_ticket_replies_staff','class="inputbox"',$row->update_ticket_replies_staff);
			$lists['delete_ticket_replies'] = JHTML::_('select.booleanlist','delete_ticket_replies','class="inputbox"',$row->delete_ticket_replies);
			$lists['delete_ticket_replies_customers'] = JHTML::_('select.booleanlist','delete_ticket_replies_customers','class="inputbox"',$row->delete_ticket_replies_customers);
			$lists['delete_ticket_replies_staff'] = JHTML::_('select.booleanlist','delete_ticket_replies_staff','class="inputbox"',$row->delete_ticket_replies_staff);
			$lists['assign_tickets'] = JHTML::_('select.booleanlist','assign_tickets','class="inputbox"',$row->assign_tickets);
			$lists['change_ticket_status'] = JHTML::_('select.booleanlist','change_ticket_status','class="inputbox"',$row->change_ticket_status);
			$lists['see_unassigned_tickets'] = JHTML::_('select.booleanlist','see_unassigned_tickets','class="inputbox"',$row->see_unassigned_tickets);
			$lists['see_other_tickets'] = JHTML::_('select.booleanlist','see_other_tickets','class="inputbox"',$row->see_other_tickets);
			$lists['move_ticket'] = JHTML::_('select.booleanlist','move_ticket','class="inputbox"',$row->move_ticket);
			$lists['view_notes'] = JHTML::_('select.booleanlist','view_notes','class="inputbox"',$row->view_notes);
			$lists['add_note'] = JHTML::_('select.booleanlist','add_note','class="inputbox"',$row->add_note);
			$lists['update_note'] = JHTML::_('select.booleanlist','update_note','class="inputbox"',$row->update_note);
			$lists['update_note_staff'] = JHTML::_('select.booleanlist','update_note_staff','class="inputbox"',$row->update_note_staff);
			$lists['delete_note'] = JHTML::_('select.booleanlist','delete_note','class="inputbox"',$row->delete_note);
			$lists['delete_note_staff'] = JHTML::_('select.booleanlist','delete_note_staff','class="inputbox"',$row->delete_note_staff);
			$this->assignRef('lists', $lists);
			
			$params = array();
			$params['startOffset'] = JRequest::getInt('tabposition', 0);
			$tabs =& JPane::getInstance('Tabs', $params, true);
			$this->assignRef('tabs', $tabs);
		}
		else
		{
			JToolBarHelper::addNewX('edit');
			JToolBarHelper::editListX('edit');
			JToolBarHelper::spacer();
			
			JToolBarHelper::deleteList('RST_CONFIRM_DELETE_GROUP');
			
			$this->assignRef('sortColumn', JRequest::getVar('filter_order','name'));
			$this->assignRef('sortOrder', JRequest::getVar('filter_order_Dir','ASC'));
			
			$this->assignRef('groups', $this->get('groups'));
			$this->assignRef('pagination', $this->get('pagination'));
			
			$filter_word = JRequest::getCmd('search', '');
			$this->assignRef('filter_word', $filter_word);
		}
		
		parent::display($tpl);
	}
}