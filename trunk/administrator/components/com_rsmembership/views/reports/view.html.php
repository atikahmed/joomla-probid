<?php
/**
* @version 1.0.0
* @package RSMembership! 1.0.0
* @copyright (C) 2009-2010 www.rsjoomla.com
* @license GPL, http://www.gnu.org/licenses/gpl-2.0.html
*/
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.view');
jimport('joomla.html.pane');

class RSMembershipViewReports extends JView
{
	function display($tpl = null)
	{
		$mainframe =& JFactory::getApplication();
		
		JToolBarHelper::title('RSMembership!','rsmembership');
		
		JSubMenuHelper::addEntry(JText::_('RSM_TRANSACTIONS'), 'index.php?option=com_rsmembership&view=transactions');
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
		JSubMenuHelper::addEntry(JText::_('RSM_REPORTS'), 'index.php?option=com_rsmembership&view=reports', true);
		JSubMenuHelper::addEntry(JText::_('RSM_CONFIGURATION'), 'index.php?option=com_rsmembership&view=configuration');
		JSubMenuHelper::addEntry(JText::_('RSM_UPDATES'), 'index.php?option=com_rsmembership&view=updates');
		
		$params = array();
		$params['allowAllClose'] = true;
		$pane =& JPane::getInstance('sliders', $params);
		$this->assignRef('pane', $pane);
		
		$date = RSMembershipHelper::getCurrentDate();
		if (RSMembershipHelper::isJ16())
		{
			$date =& JFactory::getDate();
			$date = $date->toUnix();
		}
		
		$this->assignRef('from_calendar', JHTML::_('calendar', '', 'from_date', 'rsm_from_calendar'));
		$this->assignRef('to_calendar', JHTML::_('calendar', date('Y-m-d', $date), 'to_date', 'rsm_to_calendar'));
		$this->assign('user_id', $this->get('userId'));
		$this->assign('report', $this->get('report'));
		$this->assign('count_memberships', $this->get('countMemberships'));



		$color_pickers = (!empty($this->report) && $this->report == 2 ? '4' : $this->count_memberships);
		$this->assign('color_pickers', $color_pickers);		

		$lists['transaction_types'] = $this->get('transactiontypes');
		$lists['memberships'] = $this->get('memberships');
		$lists['memberships_transactions'] = $this->get('membershipstransactions');

		
		$units = array();
		$units[] = JHTML::_('select.option', 'day', JText::_('RSM_DAY'));
		$units[] = JHTML::_('select.option', 'month', JText::_('RSM_MONTH'));
		$units[] = JHTML::_('select.option', 'quarter', JText::_('RSM_QUARTER'));
		$units[] = JHTML::_('select.option', 'year', JText::_('RSM_YEAR'));
		$lists['unit'] = JHTML::_('select.genericlist', $units, 'unit');

		$reports = array();
		$reports[] = JHTML::_('select.option', 'report_1', JText::_('RSM_REPORT_1'));
		$reports[] = JHTML::_('select.option', 'report_2', JText::_('RSM_REPORT_2'));

		$lists['report'] = JHTML::_('select.genericlist', $reports, 'report', 'onchange="rsm_check_report(this.value);"');
		
		$gateways =  $this->assign('gateways', $this->get('gateways'));
		
		$viewin = array();
		$viewin[] = JHTML::_('select.option', 60, JText::_('RSM_MINUTES'));
		$viewin[] = JHTML::_('select.option', 3600, JText::_('RSM_HOURS'));
		$viewin[] = JHTML::_('select.option', 86400, JText::_('RSM_DAYS'));
		$lists['viewin'] = JHTML::_('select.genericlist', $viewin, 'viewin', 'style="display: none;"');

		$this->assignRef('lists', $lists);
		$this->assign('customer', $this->get('customer'));
		
		if ($this->get('ie'))
			JError::raiseWarning(500, JText::_('RSM_IE_WARNING'));

		parent::display($tpl);
	}
}