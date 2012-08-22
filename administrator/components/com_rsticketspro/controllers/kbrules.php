<?php
/**
* @version 2.0.0
* @package RSTickets! Pro 2.0.0
* @copyright (C) 2010 www.rsjoomla.com
* @license GPL, http://www.gnu.org/licenses/gpl-2.0.html
*/

defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.controller');

class RSTicketsProControllerKBRules extends RSTicketsProController
{
	function __construct()
	{
		parent::__construct();
		$this->registerTask('apply', 'save');
		$this->registerTask('publish', 'changestatus');
		$this->registerTask('unpublish', 'changestatus');
		
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_rsticketspro'.DS.'helpers'.DS.'json.php');
	}
	
	/**
	 * Display "New" / "Edit"
	 */
	function edit()
	{
		JRequest::setVar('view', 'kbrules');
		JRequest::setVar('layout', 'edit');
		parent::display();
	}
	
	/**
	 * Logic to move
	 */
	function move() 
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit('Invalid Token');
		
		// Get the table instance
		$row =& JTable::getInstance('RSTicketsPro_KB_Rules','Table');
		
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
		$this->setRedirect('index.php?option=com_rsticketspro&view=kbrules');
	}
	
	/**
	 * Logic to publish/unpublish
	 */
	function changestatus()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit('Invalid Token');
		
		// Get the model
		$model = $this->getModel('kbrules');
		
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
				$msg = JText::sprintf('RST_KB_RULES_PUBLISHED', $total);
			else
				$msg = JText::sprintf('RST_KB_RULES_UNPUBLISHED', $total);
			
			// Clean the cache, if any
			$cache =& JFactory::getCache('com_rsticketspro');
			$cache->clean();
		}
		
		// Redirect
		$this->setRedirect('index.php?option=com_rsticketspro&view=kbrules', $msg);
	}
	
	/**
	 * Logic to remove
	 */
	function remove()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit('Invalid Token');

		// Get the model
		$model = $this->getModel('kbrules');
		
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
			$msg = JText::sprintf('RST_KB_RULES_DELETED', $total);
			
			// Clean the cache, if any
			$cache =& JFactory::getCache('com_rsticketspro');
			$cache->clean();
		}
		
		// Redirect
		$this->setRedirect('index.php?option=com_rsticketspro&view=kbrules', $msg);
	}
	
	/**
	 * Logic to save
	 */
	function save()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit('Invalid Token');

		// Get the model
		$model = $this->getModel('kbrules');
		
		// Save
		$result = $model->save();
		$cid = $model->getId();
		
		$task = JRequest::getCmd('task');
		switch($task)
		{
			case 'apply':
				$link = 'index.php?option=com_rsticketspro&controller=kbrules&task=edit&cid='.$cid;
				if ($result)
					$this->setRedirect($link, JText::_('RST_KB_RULE_SAVED_OK'));
				else
					$this->setRedirect($link, JText::_('RST_KB_RULE_SAVED_ERROR'));
			break;
		
			case 'save':
				$link = 'index.php?option=com_rsticketspro&view=kbrules';
				if ($result)
					$this->setRedirect($link, JText::_('RST_KB_RULE_SAVED_OK'));
				else
					$this->setRedirect($link, JText::_('RST_KB_RULE_SAVED_ERROR'));
			break;
		}
	}
	
	function showDepartments()
	{
		header('Content-Type: text/javascript; charset=utf-8');
		
		$model = $this->getModel('kbrules');
		$departments = $model->getDepartments();
		
		echo RSTicketsProJSON::encode($departments);
		die();
	}
	
	function showPriorities()
	{
		header('Content-Type: text/javascript; charset=utf-8');
		
		$model = $this->getModel('kbrules');
		$priorities = $model->getPriorities();
		
		echo RSTicketsProJSON::encode($priorities);
		die();
	}
	
	function showStatuses()
	{
		header('Content-Type: text/javascript; charset=utf-8');
		
		$model = $this->getModel('kbrules');
		$statuses = $model->getStatuses();
		
		echo RSTicketsProJSON::encode($statuses);
		die();
	}
	
	function showCustomFields()
	{
		header('Content-Type: text/javascript; charset=utf-8');
		
		$model = $this->getModel('kbrules');
		$departments = $model->getDepartments();
		$custom_fields = $model->getCustomFields();
		
		echo RSTicketsProJSON::encode($departments);
		echo "\n";
		echo RSTicketsProJSON::encode($custom_fields);
		
		die();
	}
	
	function showCustomFieldValues()
	{
		header('Content-Type: text/javascript; charset=utf-8');
		
		$model = $this->getModel('kbrules');
		
		$values = $model->getCustomFieldValues();
		echo RSTicketsProJSON::encode($values);
		
		die();
	}
}
?>