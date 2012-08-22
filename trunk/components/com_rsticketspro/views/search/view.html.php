<?php
/**
* @version 2.0.0
* @package RSTickets! Pro 2.0.0
* @copyright (C) 2010 www.rsjoomla.com
* @license GPL, http://www.gnu.org/licenses/gpl-2.0.html
*/

defined('_JEXEC') or die('Restricted access');

jimport( 'joomla.application.component.view');

class RSTicketsProViewSearch extends JView
{
	function display($tpl = null)
	{
		$mainframe =& JFactory::getApplication();
		
		if ($mainframe->isSite())
		{
			$params = $mainframe->getParams('com_rsticketspro');
			$this->assignRef('params', $params);
		}
		
		$departments = RSTicketsProHelper::getDepartments();
		$lists['departments'] = JHTML::_('select.genericlist', $departments, 'department_id[]', 'size="5" multiple="multiple"', 'value', 'text', $departments);
		$lists['statuses'] =  JHTML::_('select.genericlist', RSTicketsProHelper::getStatuses(), 'status_id[]', 'size="5" multiple="multiple"', 'value', 'text', RSTicketsProHelper::getStatuses());
		$lists['priorities'] =  JHTML::_('select.genericlist', RSTicketsProHelper::getPriorities(), 'priority_id[]', 'size="5" multiple="multiple"', 'value', 'text', RSTicketsProHelper::getPriorities());
		
		$ordering = array();
		$ordering[] = JHTML::_('select.option', 'date', JText::_('Default'));
		$ordering[] = JHTML::_('select.option', 'last_reply', JText::_('RST_TICKET_LAST_REPLY'));
		$ordering[] = JHTML::_('select.option', 'subject', JText::_('RST_TICKET_SUBJECT'));
		$ordering[] = JHTML::_('select.option', 'status', JText::_('RST_TICKET_STATUS'));
		$ordering[] = JHTML::_('select.option', 'priority', JText::_('RST_TICKET_PRIORITY'));
		$ordering[] = JHTML::_('select.option', 'replies', JText::_('RST_TICKET_REPLIES'));
		$lists['ordering'] = JHTML::_('select.genericlist', $ordering, 'filter_order', '', 'value', 'text');
		
		$ordering_dir = array();
		$ordering_dir[] = JHTML::_('select.option', 'DESC', JText::_('DESC'));
		$ordering_dir[] = JHTML::_('select.option', 'ASC', JText::_('ASC'));
		$lists['ordering_dir'] = JHTML::_('select.genericlist', $ordering_dir, 'filter_order_Dir', '', 'value', 'text');
		
		$this->assign('is_staff', RSTicketsProHelper::isStaff());
		$this->assign('permissions', RSTicketsProHelper::getCurrentPermissions());
		$this->assign('is_advanced', JRequest::getVar('advanced', false));
		$this->assignRef('lists', $lists);
		
		$this->assign('itemid', $this->get('itemid'));
		
		$this->assign('show_footer', RSTicketsProHelper::getConfig('rsticketspro_link'));
		$this->assign('footer', RSTicketsProHelper::getFooter());
		
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