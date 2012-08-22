<?php
/**
* @version 2.0.0
* @package RSTickets! Pro 2.0.0
* @copyright (C) 2010 www.rsjoomla.com
* @license GPL, http://www.gnu.org/licenses/gpl-2.0.html
*/

defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.controller');

class RSTicketsProControllerCustomFields extends RSTicketsProController
{
	function __construct()
	{
		parent::__construct();
		$this->registerTask('orderup', 'move');
		$this->registerTask('orderdown', 'move');
		$this->registerTask('apply', 'save');
		$this->registerTask('publish', 'changestatus');
		$this->registerTask('unpublish', 'changestatus');
	}
	
	/**
	 * Display "New" / "Edit"
	 */
	function edit()
	{
		JRequest::setVar('view', 'customfields');
		JRequest::setVar('layout', 'edit');
		parent::display();
	}
	
	/**
	 * Save the ordering
	 */
	function saveOrder()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit('Invalid Token');
		
		// Get the table instance
		$row =& JTable::getInstance('RSTicketsPro_Custom_Fields','Table');
		
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
		$department_id = JRequest::getInt('department_id');
		$this->setRedirect('index.php?option=com_rsticketspro&view=customfields&department_id='.$department_id, JText::_('RST_CUSTOM_FIELDS_ORDERED'));
	}
	
	/**
	 * Logic to move
	 */
	function move() 
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit('Invalid Token');
		
		// Get the table instance
		$row =& JTable::getInstance('RSTicketsPro_Custom_Fields','Table');
		
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
		$department_id = JRequest::getInt('department_id');
		$this->setRedirect('index.php?option=com_rsticketspro&view=customfields&department_id='.$department_id);
	}
	
	/**
	 * Logic to publish/unpublish
	 */
	function changestatus()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit('Invalid Token');
		
		// Get the model
		$model = $this->getModel('customfields');
		
		// Get the selected items
		$cid = JRequest::getVar('cid', array(0), 'post', 'array');

		// Get the task
		$task = JRequest::getCmd('task');
		
		// Force array elements to be integers
		JArrayHelper::toInteger($cid, array(0));
		
		$msg = '';
		
		// No items are selected
		if (!is_array($cid) || count($cid) < 1)
			JError::raiseWarning(500, JText::_('SELECT ITEM PUBLISH'));
		// Try to publish the item
		else
		{
			$value = $task == 'publish' ? 1 : 0;
			if (!$model->publish($cid, $value))
				JError::raiseError(500, $model->getError());

			$total = count($cid);
			if ($value)
				$msg = JText::sprintf('RST_CUSTOM_FIELDS_PUBLISHED', $total);
			else
				$msg = JText::sprintf('RST_CUSTOM_FIELDS_UNPUBLISHED', $total);
			
			// Clean the cache, if any
			$cache =& JFactory::getCache('com_rsticketspro');
			$cache->clean();
		}
		
		// Redirect
		$department_id = JRequest::getInt('department_id');
		$this->setRedirect('index.php?option=com_rsticketspro&view=customfields&department_id='.$department_id, $msg);
	}
	
	/**
	 * Logic to remove
	 */
	function remove()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit('Invalid Token');

		// Get the model
		$model = $this->getModel('customfields');
		
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
			$msg = JText::sprintf('RST_CUSTOM_FIELDS_DELETED', $total);
			
			// Clean the cache, if any
			$cache =& JFactory::getCache('com_rsticketspro');
			$cache->clean();
		}
		
		// Redirect
		$department_id = JRequest::getInt('department_id');
		$this->setRedirect('index.php?option=com_rsticketspro&view=customfields&department_id='.$department_id, $msg);
	}
	
	/**
	 * Logic to save
	 */
	function save()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit('Invalid Token');

		// Get the model
		$model = $this->getModel('customfields');
		
		// Save
		$result = $model->save();
		$cid = $model->getId();
		
		$department_id = JRequest::getInt('department_id');
		
		$task = JRequest::getCmd('task');
		switch($task)
		{
			case 'apply':
				$link = 'index.php?option=com_rsticketspro&controller=customfields&task=edit&cid='.$cid;
				if ($result)
					$this->setRedirect($link, JText::_('RST_CUSTOM_FIELD_SAVED_OK'));
				else
					$this->setRedirect($link, JText::_('RST_CUSTOM_FIELD_SAVED_ERROR'));
			break;
		
			case 'save':
				$link = 'index.php?option=com_rsticketspro&view=customfields&department_id='.$department_id;
				if ($result)
					$this->setRedirect($link, JText::_('RST_CUSTOM_FIELD_SAVED_OK'));
				else
					$this->setRedirect($link, JText::_('RST_CUSTOM_FIELD_SAVED_ERROR'));
			break;
		}
	}
}
?>