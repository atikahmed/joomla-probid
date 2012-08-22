<?php
/**
* @version 2.0.0
* @package RSTickets! Pro 2.0.0
* @copyright (C) 2010 www.rsjoomla.com
* @license GPL, http://www.gnu.org/licenses/gpl-2.0.html
*/

defined('_JEXEC') or die('Restricted access');

jimport( 'joomla.application.component.view');

class RSTicketsProViewTicket extends JView
{
	function display($tpl = null)
	{
		$mainframe =& JFactory::getApplication();
		
		if ($mainframe->isSite())
		{
			$params = $mainframe->getParams('com_rsticketspro');
			$this->assignRef('params', $params);
		}
		
		$row = $this->get('ticket');
		$this->assignRef('row', $row);
		
		$this->assign('date_format', RSTicketsProHelper::getConfig('date_format'));
		$this->assign('show_ticket_info', RSTicketsProHelper::getConfig('show_ticket_info'));
		$this->assign('show_ticket_voting', RSTicketsProHelper::getConfig('show_ticket_voting'));
		$this->assign('what', RSTicketsProHelper::getConfig('show_user_info'));
		$this->assign('avatar', RSTicketsProHelper::getConfig('avatars'));
		$this->assign('show_email_link', RSTicketsProHelper::getConfig('show_email_link'));
		$this->assign('show_signature', RSTicketsProHelper::getConfig('show_signature'));
		$this->assign('show_kb_search', RSTicketsProHelper::getConfig('show_kb_search'));
		$this->assign('show_time_spent', RSTicketsProHelper::getConfig('enable_time_spent'));
		$this->assign('time_spent_unit', JText::_('RST_TIME_UNIT_'.strtoupper(RSTicketsProHelper::getConfig('time_spent_unit'))));
		
		$this->assign('permissions', $this->get('permissions'));
		$is_staff = RSTicketsProHelper::isStaff();
		$this->assign('is_staff', $is_staff);
		$this->assign('can_upload', $this->get('canupload'));
		$this->assign('can_update', $this->get('canupdate'));
		$this->assign('can_update_custom_fields', $this->get('canupdatecustomfields'));
		$this->assign('data', $this->get('data'));
		
		$this->assign('use_editor', RSTicketsProHelper::getConfig('allow_rich_editor'));
		$this->assignRef('editor', JFactory::getEditor());
		
		$editor_javascript = "document.getElementById('message').innerHTML = content.replace(/<(.*?)>/g, '');";
		if ($this->use_editor)
		{
			// fix for JCE
			if ($this->editor->get('_name') == 'jce')
				$editor_javascript = str_replace("'content'", 'content', $this->editor->setContent('message', 'content'));
			// fix for JoomlaCK
			elseif ($this->editor->get('_name') == 'jckeditor')
				$editor_javascript = str_replace(array('(!oEditor) ', "'content'"), array('(!oEditor) ? ', 'content'), $this->editor->setContent('message', 'content'))."\n";
			else
				$editor_javascript = $this->editor->setContent('message', 'content');
		}
		else
			$editor_javascript = str_replace('innerHTML', 'value', $editor_javascript);
			
		$this->assign('editor_javascript', $editor_javascript);
		
		$this->assign('show_footer', $this->get('showfooter'));
		$this->assign('footer', $this->get('footer'));
		
		$this->assign('do_print', JRequest::getInt('print', 0));
		
		$this->assignRef('department', $this->get('department'));
		
		if ($is_staff)
		{
			$status = RSTicketsProHelper::getStatuses();
			$lists['status'] = JHTML::_('select.genericlist', $status, 'status_id', '', 'value', 'text', $row->status_id);

			$priority = RSTicketsProHelper::getPriorities();
			$lists['priority'] = JHTML::_('select.genericlist', $priority, 'priority_id', '', 'value', 'text', $row->priority_id);

			$department = RSTicketsProHelper::getDepartments();
			$lists['department'] = JHTML::_('select.genericlist', $department, 'department_id', '', 'value', 'text', $row->department_id);

			$staff = RSTicketsProHelper::getStaff();
			$unassigned[] = JHTML::_('select.option', 0, JText::_('RST_UNASSIGNED'));
			$staff = array_merge($unassigned, $staff);

			$lists['staff'] = JHTML::_('select.genericlist', $staff, 'staff_id', '', 'value', 'text', $row->staff_id);

			$this->assignRef('lists', $lists);

			$this->assign('history_tickets', $this->get('HistoryTickets'));

		}
		
		$ticket_view = RSTicketsProHelper::getConfig('ticket_view');
		$this->assign('ticket_view', $ticket_view);
		if ($ticket_view == 'tabbed')
		{
			jimport('joomla.html.pane');
			$tabparams = array();
			$tabparams['startOffset'] = JRequest::getInt('tabposition', 0);
			$tabs =& JPane::getInstance('Tabs', $tabparams, true);
			$this->assignRef('tabs', $tabs);
		}
		
		$model = $this->getModel();
		$model->addViewingHistory();
		
		parent::display();
	}
}