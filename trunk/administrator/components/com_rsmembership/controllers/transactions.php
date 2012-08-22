<?php
/**
* @version 1.0.0
* @package RSMembership! 1.0.0
* @copyright (C) 2009-2010 www.rsjoomla.com
* @license GPL, http://www.gnu.org/licenses/gpl-2.0.html
*/

defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.controller');

class RSMembershipControllerTransactions extends RSMembershipController
{
	function __construct()
	{
		parent::__construct();
		$this->registerTask('apply', 'save');
	}
	
	/**
	 * Display "New" / "Edit"
	 */
	function edit()
	{
		JRequest::setVar('view', 'transactions');
		JRequest::setVar('layout', 'edit');
		parent::display();
	}
	
	/**
	 * Logic to remove
	 */
	function remove()
	{
		// Check for request forgeries
		JRequest::checkToken('request') or jexit('Invalid Token');

		// Get the model
		$model = $this->getModel('transactions');
		
		// Get the selected items
		$cid = JRequest::getVar('cid', array(0), 'request', 'array');

		// Force array elements to be integers
		JArrayHelper::toInteger($cid, array(0));
		
		$msg = '';
		
		// No items are selected
		if (!is_array($cid) || count($cid) < 1)
			JError::raiseWarning(500, JText::_('SELECT ITEM DELETE'));
		// Try to remove the item
		else
		{
			$model->remove($cid);
			
			$total = count($cid);
			$msg = JText::sprintf('RSM_TRANSACTIONS_DELETED', $total);
			
			// Clean the cache, if any
			$cache =& JFactory::getCache('com_rsmembership');
			$cache->clean();
		}
		
		// Redirect
		$tabposition = JRequest::getInt('tabposition', 0);
		$user_id = JRequest::getInt('user_id', 0);
		if ($user_id > 0)
			$this->setRedirect('index.php?option=com_rsmembership&controller=users&task=edit&cid='.$user_id.'&tabposition='.$tabposition, $msg);
		else
			$this->setRedirect('index.php?option=com_rsmembership&view=transactions', $msg);
	}
	
	function approve()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit('Invalid Token');
		
		// Get the selected items
		$cid = JRequest::getVar('cid', array(0), 'post', 'array');
		
		$total = count($cid);
		$msg = JText::sprintf('RSM_TRANSACTIONS_APPROVED', $total);
		
		// Force array elements to be integers
		JArrayHelper::toInteger($cid, array(0));
		
		$msg = '';
		
		// No items are selected
		if (!is_array($cid) || count($cid) < 1)
			JError::raiseWarning(500, JText::_('SELECT ITEM'));
		else
		{
			$user =& JFactory::getUser();
			$user_id = $user->get('username');
			foreach ($cid as $id)
			{
				RSMembership::saveTransactionLog('Manually approved by '.$user_id, $id);
				RSMembership::approve($id);
			}
			
			$total = count($cid);
			$msg = JText::sprintf('RSM_TRANSACTIONS_APPROVED', $total);
			
			// Clean the cache, if any
			$cache =& JFactory::getCache('com_rsmembership');
			$cache->clean();
		}
		
		$this->setRedirect('index.php?option=com_rsmembership&view=transactions', $msg);
	}
	
	function deny()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit('Invalid Token');
		
		// Get the selected items
		$cid = JRequest::getVar('cid', array(0), 'post', 'array');
		
		$total = count($cid);
		$msg = JText::sprintf('RSM_TRANSACTIONS_DENIED', $total);
		
		// Force array elements to be integers
		JArrayHelper::toInteger($cid, array(0));
		
		$msg = '';
		
		// No items are selected
		if (!is_array($cid) || count($cid) < 1)
			JError::raiseWarning(500, JText::_('SELECT ITEM'));
		else
		{
			$user =& JFactory::getUser();
			$user_id = $user->get('username');
			foreach ($cid as $id)
			{
				RSMembership::saveTransactionLog('Manually denied by '.$user_id, $id);
				RSMembership::deny($id);
			}
			
			$total = count($cid);
			$msg = JText::sprintf('RSM_TRANSACTIONS_DENIED', $total);
			
			// Clean the cache, if any
			$cache =& JFactory::getCache('com_rsmembership');
			$cache->clean();
		}
		
		$this->setRedirect('index.php?option=com_rsmembership&view=transactions', $msg);
	}
	
	function resetSearch()
	{
		$mainframe =& JFactory::getApplication();
		$option    = 'com_rsmembership';
		
		$mainframe->setUserState($option.'.transactions.limit', $mainframe->getCfg('list_limit'));
		$mainframe->setUserState($option.'.transactions.limitstart', 0);
		$mainframe->setUserState($option.'.transactions.filter_type', null);
		$mainframe->setUserState($option.'.transactions.filter_gateway', null);
		$mainframe->setUserState($option.'.transactions.filter_status', null);
		$mainframe->setUserState($option.'.transactions.date_from', null);
		$mainframe->setUserState($option.'.transactions.date_to', null);
		$mainframe->setUserState($option.'.transactions.search', null);
		
		$this->setRedirect('index.php?option=com_rsmembership&view=transactions');
	}
}
?>