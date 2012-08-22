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

class RSMembershipViewUsers extends JView
{
	function display($tpl = null)
	{		
		if ($tpl == 'memberships')
		{
			parent::display($tpl);
			return true;
		}
		
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
		JSubMenuHelper::addEntry(JText::_('RSM_USERS'), 'index.php?option=com_rsmembership&view=users', true);
		JSubMenuHelper::addEntry(JText::_('RSM_FIELDS'), 'index.php?option=com_rsmembership&view=fields');
		JSubMenuHelper::addEntry(JText::_('RSM_REPORTS'), 'index.php?option=com_rsmembership&view=reports');
		JSubMenuHelper::addEntry(JText::_('RSM_CONFIGURATION'), 'index.php?option=com_rsmembership&view=configuration');
		JSubMenuHelper::addEntry(JText::_('RSM_UPDATES'), 'index.php?option=com_rsmembership&view=updates');
		
		$task = JRequest::getVar('task','');
		
		$this->assign('temp', $this->get('TempId'));
		
		if ($task == 'edit')
		{
			JToolBarHelper::title('RSMembership! <small>['.JText::_('RSM_EDIT_MEMBERSHIP_USER').']</small>','rsmembership');
			
			if (!$this->temp)
			{
				JToolBarHelper::apply();
				JToolBarHelper::save();
			}
			JToolBarHelper::cancel();
			
			$params = array();
			$params['startOffset'] = JRequest::getInt('tabposition', 0);
			$tabs =& JPane::getInstance('Tabs',$params,true);
			$this->assignRef('tabs', $tabs);
			
			$row = $this->get('user');
			$this->assignRef('row', $row);
			
			$show_edit 		= $this->temp ? false : true;
			$user_id   		= $this->temp ? 0 : $row->user_id;
			$show_required 	= false;
			$transaction_id = $this->temp ? $this->temp : 0;
			
			$this->assignRef('fields', RSMembershipHelper::getFields($show_edit, $user_id, $show_required, $transaction_id));

			$this->assignRef('transactions', $this->get('transactions'));
			
			$this->assignRef('cache', $this->get('cache'));
		}
		elseif ($task == 'editmembership')
		{
			$row = $this->get('membership');
			
			$all_memberships = $this->get('memberships');
			$memberships = array();
			$categories = array();
			foreach ($all_memberships as $membership)
				$categories[$membership->category_id][] = $membership;
				
			foreach ($categories as $category_id => $cat_memberships)
			{
				$optgroup = new stdClass();
				$optgroup->value = '<OPTGROUP>';
				$optgroup->text = isset($cat_memberships[0]) ? $cat_memberships[0]->category_name : '';
				$memberships[] = $optgroup;
				
				foreach ($cat_memberships as $membership)
					$memberships[] = JHTML::_('select.option', $membership->id, $membership->name);
				
				$optgroup = new stdClass();
				$optgroup->value = '</OPTGROUP>';
				$optgroup->text = '';
				$memberships[] = $optgroup;
			}
			
			unset($all_memberships);
			unset($categories);
			
			$date = JFactory::getDate();
			$this->assign('now', $date->toUnix());
			$this->assignRef('periods', $this->get('periods'));
			
			$lists['membership'] = JHTML::_('select.genericlist', $memberships, 'membership_id', 'onchange="rsmembership_change_membership(1)"', 'value', 'text', $row->membership_id);
			
			$all_extras = $this->get('extras');
			$all_extravalues = $this->get('extravalues');
			$extras = array();
			foreach ($all_extras as $extra_id => $extra_text)
			{
				$extras[] = JHTML::_('select.optgroup', $extra_text);
				if (isset($all_extravalues[$extra_id]))
					foreach ($all_extravalues[$extra_id] as $extra_value_id => $extra_value_text)
						$extras[] = JHTML::_('select.option', $extra_value_id, $extra_value_text);
						
				$close = new stdClass();
				$close->value = '</OPTGROUP>';
				$close->text = '';
				$extras[] = $close;
			}
			$this->assign('hasExtras', count($all_extras) > 0);
			
			$lists['extras'] = JHTML::_('select.genericlist', $extras, 'extras[]', 'size="5" multiple="multiple"'.($row->noextra ? ' disabled="disabled"' : ''), 'value', 'text', $row->extras, 'extras');
			
			$statuses = RSMembershipHelper::getStatuses();
			$lists['status'] = JHTML::_('select.genericlist', $statuses, 'status', '', 'value', 'text', $row->status, 'rsm_status');
			$lists['published'] = JHTML::_('select.booleanlist','published','class="inputbox"',$row->published);
			
			$this->assignRef('row', $row);
			$this->assignRef('lists', $lists);
			$editor =& JFactory::getEditor();
			$this->assignRef('editor', $editor);
			$this->assign('function', 'addmemberships');
		}
		else
		{
			JToolBarHelper::editListX('edit');
			
			$this->assignRef('sortColumn', JRequest::getVar('filter_order','u.name'));
			$this->assignRef('sortOrder', JRequest::getVar('filter_order_Dir','ASC'));
			
			$this->assignRef('users', $this->get('users'));
			$this->assignRef('pagination', $this->get('pagination'));
			
			$filter_word = JRequest::getString('search', '');
			$this->assignRef('filter_word', $filter_word);
			
			$all_memberships = $this->get('memberships');
			$memberships = array();
			foreach ($all_memberships as $membership)
				$memberships[] = JHTML::_('select.option', $membership->id, $membership->name);
			
			$filter_membership = JRequest::getVar('membership', array(0), 'post', 'array');
			JArrayHelper::toInteger($filter_membership, array(0));
			$lists['memberships'] = JHTML::_('select.genericlist', $memberships, 'membership[]', 'multiple="multiple" size="5"', 'value', 'text', $filter_membership, 'membership');
			
			$statuses = RSMembershipHelper::getStatuses();
			$filter_status = JRequest::getVar('status', array(), 'post', 'array');
			JArrayHelper::toInteger($filter_status, array());
			$lists['status'] = JHTML::_('select.genericlist', $statuses, 'status[]', 'multiple="multiple" size="5"', 'value', 'text', $filter_status, 'rsm_status');
			
			$this->assignRef('lists', $lists);
		}
		
		$this->assign('currency', RSMembershipHelper::getConfig('currency'));
		
		parent::display($tpl);
	}
}