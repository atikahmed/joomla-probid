<?php
/**
* @version 1.0.0
* @package RSMembership! 1.0.0
* @copyright (C) 2009-2010 www.rsjoomla.com
* @license GPL, http://www.gnu.org/licenses/gpl-2.0.html
*/

defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.view');

class RSMembershipViewTransactions extends JView
{
	function display($tpl = null)
	{
		JToolBarHelper::title('RSMembership!','rsmembership');
		
		JSubMenuHelper::addEntry(JText::_('RSM_TRANSACTIONS'), 'index.php?option=com_rsmembership&view=transactions', true);
		JSubMenuHelper::addEntry(JText::_('RSM_MEMBERSHIPS'), 'index.php?option=com_rsmembership&view=memberships');
		JSubMenuHelper::addEntry(JText::_('RSM_CATEGORIES'), 'index.php?option=com_rsmembership&view=categories');
		JSubMenuHelper::addEntry(JText::_('RSM_MEMBERSHIP_EXTRAS'), 'index.php?option=com_rsmembership&view=extras');
		JSubMenuHelper::addEntry(JText::_('RSM_MEMBERSHIP_UPGRADES'), 'index.php?option=com_rsmembership&view=upgrades');
		JSubMenuHelper::addEntry(JText::_('RSM_COUPONS'), 'index.php?option=com_rsmembership&view=coupons');
		JSubMenuHelper::addEntry(JText::_('RSM_PAYMENT_INTEGRATIONS'), 'index.php?option=com_rsmembership&view=payments');
		JSubMenuHelper::addEntry(JText::_('RSM_FILES'), 'index.php?option=com_rsmembership&view=files');
		JSubMenuHelper::addEntry(JText::_('RSM_FILE_TERMS'), 'index.php?option=com_rsmembership&view=terms');
		JSubMenuHelper::addEntry(JText::_('RSM_USERS'), 'index.php?option=com_rsmembership&view=users');
		JSubMenuHelper::addEntry(JText::_('RSM_FIELDS'), 'index.php?option=com_rsmembership&view=fields');
		JSubMenuHelper::addEntry(JText::_('RSM_REPORTS'), 'index.php?option=com_rsmembership&view=reports');
		JSubMenuHelper::addEntry(JText::_('RSM_CONFIGURATION'), 'index.php?option=com_rsmembership&view=configuration');
		JSubMenuHelper::addEntry(JText::_('RSM_UPDATES'), 'index.php?option=com_rsmembership&view=updates');
	
		JToolBarHelper::custom('approve', 'approve', '', 'RSM_APPROVE');
		JToolBarHelper::custom('deny', 'deny', '', 'RSM_DENY');
		JToolBarHelper::spacer();
		JToolBarHelper::deleteList('RSM_CONFIRM_DELETE');
		
		switch ($this->getlayout())
		{
			case 'default':
			$this->assignRef('sortColumn', $this->get('sortColumn'));
			$this->assignRef('sortOrder', $this->get('sortOrder'));
			
			$this->assignRef('transactions', $this->get('transactions'));
			
			$this->assignRef('cache', $this->get('cache'));
			
			$this->assignRef('pagination', $this->get('pagination'));
			
			$this->assignRef('filter_word', $this->get('filterWord'));
			
			$lists['types'] = JHTML::_('select.genericlist', $this->get('transactionTypes'), 'filter_type[]', 'multiple="multiple" size="5"', 'key', 'value', $this->get('filterType'));
			$lists['gateway'] = JHTML::_('select.genericlist', $this->get('gateways'), 'filter_gateway[]', 'multiple="multiple" size="5"', 'key', 'value', $this->get('filterGateway'));
			$lists['status'] = JHTML::_('select.genericlist', $this->get('statuses'), 'filter_status[]', 'multiple="multiple" size="5"', 'key', 'value', $this->get('filterStatus'));
			$this->assignRef('lists', $lists);
			
			$calendars['from'] = JHTML::calendar($this->get('dateFrom'), 'date_from', 'date_from');
			$calendars['to']   = JHTML::calendar($this->get('dateTo'), 'date_to', 'date_to');
			$this->assignRef('calendars', $calendars);
			break;
			
			case 'log':
			$this->assignRef('log', $this->get('Log'));
			break;
		}
		
		parent::display($tpl);
	}
}