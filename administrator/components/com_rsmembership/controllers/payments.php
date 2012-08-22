<?php
/**
* @version 1.0.0
* @package RSMembership! 1.0.0
* @copyright (C) 2009-2010 www.rsjoomla.com
* @license GPL, http://www.gnu.org/licenses/gpl-2.0.html
*/

defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.controller');

class RSMembershipControllerPayments extends RSMembershipController
{
	function __construct()
	{
		parent::__construct();
		
		// Payments Tasks
		$this->registerTask('apply', 'save');
		$this->registerTask('orderup', 'move');
		$this->registerTask('orderdown', 'move');
		
		JTable::addIncludePath(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_rsmembership'.DS.'tables');
	}
	
	function cancel()
	{
		$this->setRedirect('index.php?option=com_rsmembership&view=payments');
	}
	
	/**
	 * Payments Tasks
	 */
	// Payments - Edit
	function edit()
	{
		JRequest::setVar('view', 'payments');
		JRequest::setVar('layout', 'edit');
		
		parent::display();
	}
	
	// Payments - Remove
	function remove()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit('Invalid Token');

		// Get the model
		$model = $this->getModel('payments');
		
		// Get the selected items
		$cid = JRequest::getVar('cid', array(0), 'post', 'array');

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
			$msg = JText::sprintf('RSM_PAYMENT_DELETED', $total);
			
			// Clean the cache, if any
			$cache =& JFactory::getCache('com_rsmembership');
			$cache->clean();
		}
		
		// Redirect
		$this->setRedirect('index.php?option=com_rsmembership&view=payments', $msg);
	}

	// Payments - Save
	function save()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit('Invalid Token');

		// Get the model
		$model = $this->getModel('payments');

		// Save
		$result = $model->save();
		$cid = JRequest::getInt('cid');

		$task = JRequest::getCmd('task');
		switch($task)
		{
			case 'apply':
				$link = 'index.php?option=com_rsmembership&controller=payments&task=edit&cid='.$cid;
				if ($result)
					$this->setRedirect($link, JText::_('RSM_PAYMENT_SAVED_OK'));
				else
					$this->setRedirect($link, JText::_('RSM_PAYMENT_SAVED_ERROR'));
			break;

			case 'save':
				$link = 'index.php?option=com_rsmembership&view=payments';
				if ($result)
					$this->setRedirect($link, JText::_('RSM_PAYMENT_SAVED_OK'));
				else
					$this->setRedirect($link, JText::_('RSM_PAYMENT_SAVED_ERROR'));
			break;
		}
	}
	
	// Save Ordering
	function saveOrder()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit('Invalid Token');
		
		// Get the table instance
		$row =& JTable::getInstance('RSMembership_Payments','Table');
		
		// Get the selected items
		$cid = JRequest::getVar('cid', array(0), 'post', 'array');
		
		// Get the ordering
		$order = JRequest::getVar('order', array (0), 'post', 'array');
		
		// Force array elements to be integers
		JArrayHelper::toInteger($cid, array(0));
		JArrayHelper::toInteger($order, array(0));
		
		// Load each element of the array
		for ($i=0;$i<count($cid);$i++)
		{
			// Load the item
			$row->load($cid[$i]);
			
			// Set the new ordering only if different
			if ($row->ordering != $order[$i])
			{	
				$row->ordering = $order[$i];
				if (!$row->store()) 
				{
					$this->setError($this->_db->getErrorMsg());
					return false;
				}
			}
		}
		// Redirect
		$this->setRedirect('index.php?option=com_rsmembership&view=payments', JText::_('RSM_PAYMENTS_ORDERED'));
	}
	
	// Move Up/Down
	function move() 
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit('Invalid Token');
		
		// Get the table instance
		$row =& JTable::getInstance('RSMembership_Payments','Table');
		
		// Get the selected items
		$cid = JRequest::getVar('cid', array(0), 'post', 'array');
		
		// Get the task
		$task = JRequest::getCmd('task');
		
		// Force array elements to be integers
		JArrayHelper::toInteger($cid, array(0));
		
		// Set the direction to move
		$direction = $task == 'orderup' ? -1 : 1;
		
		// Can move only one element
		if (is_array($cid))	$cid = $cid[0];
		
		// Load row
		if (!$row->load($cid)) 
		{
			$this->setError($this->_db->getErrorMsg());
			return false;
		}
		
		// Move
		$row->move($direction);
	
		// Redirect
		$this->setRedirect('index.php?option=com_rsmembership&view=payments');
	}
}
?>