<?php
/**
* @version 2.0.0
* @package RSTickets! Pro 2.0.0
* @copyright (C) 2010 www.rsjoomla.com
* @license GPL, http://www.gnu.org/licenses/gpl-2.0.html
*/

defined('_JEXEC') or die('Restricted access');

jimport( 'joomla.application.component.view');

class RSTicketsProViewRSTicketsPro extends JView
{
	function display($tpl = null)
	{
		$mainframe =& JFactory::getApplication();
		
		if ($mainframe->isSite())
		{
			$params = $mainframe->getParams('com_rsticketspro');
			$this->assignRef('params', $params);
		}
		
		$this->assign('date_format', RSTicketsProHelper::getConfig('date_format'));
		$this->assign('permissions', $this->get('permissions'));
		$this->assign('is_staff', RSTicketsProHelper::isStaff());
		
		$this->assignRef('tickets', $this->get('tickets'));
		$this->assignRef('pagination', $this->get('pagination'));
		
		$staff = RSTicketsProHelper::getStaff();
		$unassigned = array();
		$unassigned[] = JHTML::_('select.option', -1, JText::_('RST_UNCHANGED'));
		$unassigned[] = JHTML::_('select.option', 0, JText::_('RST_UNASSIGNED'));
		$staff = array_merge($unassigned, $staff);
		
		$lists['staff'] = JHTML::_('select.genericlist', $staff, 'bulk_staff_id', '', 'value', 'text');
		
		$priority = RSTicketsProHelper::getPriorities();
		$unchanged = array();
		$unchanged[] = JHTML::_('select.option', 0, JText::_('RST_UNCHANGED'));
		$priority = array_merge($unchanged, $priority);
		
		$lists['priority'] = JHTML::_('select.genericlist', $priority, 'bulk_priority_id', '', 'value', 'text');
		
		$status = RSTicketsProHelper::getStatuses();
		$unchanged = array();
		$unchanged[] = JHTML::_('select.option', 0, JText::_('RST_UNCHANGED'));
		$status = array_merge($unchanged, $status);
		
		$lists['status'] = JHTML::_('select.genericlist', $status, 'bulk_status_id', '', 'value', 'text');
		
		$delete = array();
		$delete[] = JHTML::_('select.option', 0, JText::_('RST_UNCHANGED'));
		$delete[] = JHTML::_('select.option', 1, JText::_('RST_DELETE_SELECTED'));
		
		$lists['delete'] = JHTML::_('select.genericlist', $delete, 'bulk_delete', 'onchange="rst_disable_bulk(this.value);" onclick="rst_disable_bulk(this.value);"', 'value', 'text', '');
		
		$notify = array();
		$notify[] = JHTML::_('select.option', 0, JText::_('RST_UNCHANGED'));
		$notify[] = JHTML::_('select.option', 1, JText::_('RST_NOTIFY_SELECTED'));
		
		$lists['notify'] = JHTML::_('select.genericlist', $notify, 'bulk_notify', '', 'value', 'text', '');
		
		$this->assignRef('lists', $lists);
		
		$this->assignRef('sortColumn', $this->get('sortColumn'));
		$this->assignRef('sortOrder', $this->get('sortOrder'));
		$this->assignRef('limitstart', JRequest::getInt('limitstart', 0));
		
		$this->assign('is_searching', $this->get('searching'));
		$searches = $this->get('searches');
		$this->assign('searches', $searches);
		$this->assign('has_searches', !empty($searches));
		$this->assign('predefined_search', $this->get('predefinedsearch'));
		$this->assign('show_footer', RSTicketsProHelper::getConfig('rsticketspro_link'));
		$this->assign('footer', RSTicketsProHelper::getFooter());
		
		$this->assignRef('priorityColors', $this->get('prioritycolors'));
		$this->assign('colorWholeTicket', RSTicketsProHelper::getConfig('color_whole_ticket'));
		
		if (RSTicketsProHelper::isJ16() && $mainframe->isSite())
		{
			// Description
			if ($params->get('menu-meta_description'))
				$this->document->setDescription($params->get('menu-meta_description'));
			// Keywords
			if ($params->get('menu-meta_keywords'))
				$this->document->setMetadata('keywords', $params->get('menu-meta_keywords'));
			// Robots
			if ($params->get('robots'))
				$this->document->setMetadata('robots', $params->get('robots'));
		}
		
		parent::display();
	}
}