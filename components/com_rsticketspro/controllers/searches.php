<?php
/**
* @version 2.0.0
* @package RSTickets! Pro 2.0.0
* @copyright (C) 2010 www.rsjoomla.com
* @license GPL, http://www.gnu.org/licenses/gpl-2.0.html
*/

defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.controller');

class RSTicketsProControllerSearches extends RSTicketsProController
{
	function __construct()
	{
		parent::__construct();
		$this->registerTask('orderup', 'move');
		$this->registerTask('orderdown', 'move');
		$this->registerTask('apply', 'save');
	}
	
	/**
	 * Display "New" / "Edit"
	 */
	function edit()
	{
		JRequest::setVar('view', 'searches');
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
		$row =& JTable::getInstance('RSTicketsPro_Searches','Table');
		
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
		$this->setRedirect(RSTicketsProHelper::route('index.php?option=com_rsticketspro&view=searches', false), JText::_('RST_SEARCHES_ORDERED'));
	}
	
	/**
	 * Logic to move
	 */
	function move() 
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit('Invalid Token');
		
		// Get the table instance
		$row =& JTable::getInstance('RSTicketsPro_Searches','Table');
		
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
		$this->setRedirect(RSTicketsProHelper::route('index.php?option=com_rsticketspro&view=searches', false));
	}
	
	/**
	 * Logic to remove
	 */
	function delete()
	{
		$mainframe =& JFactory::getApplication();
		
		$model = $this->getModel('searches');
		
		$cid = JRequest::getInt('cid');
		$model->remove($cid);
			
		$msg = JText::_('RST_SEARCH_DELETED');
			
		// Clean the cache, if any
		$cache =& JFactory::getCache('com_rsticketspro');
		$cache->clean();
		
		// Redirect
		$this->setRedirect(RSTicketsProHelper::route('index.php?option=com_rsticketspro&view=searches', false), $msg);
	}
	
	/**
	 * Logic to save
	 */
	function save()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit('Invalid Token');

		// Get the model
		$model = $this->getModel('searches');
		
		// Save
		$result = $model->save();
		
		$link = RSTicketsProHelper::route('index.php?option=com_rsticketspro&view=searches', false);
		if ($result)
			$this->setRedirect($link, JText::_('RST_SEARCH_SAVED_OK'));
		else
			$this->setRedirect($link, JText::_('RST_SEARCH_SAVED_ERROR'));
	}
	
	function search()
	{
		$mainframe =& JFactory::getApplication();
		
		// Get the model
		$model = $this->getModel('searches');
		
		$search = $model->getSearch();
		
		$params = unserialize(base64_decode($search->params));
		
		foreach ($params as $param => $value)
			JRequest::setVar($param, $value);
		
		if ($mainframe->isAdmin())
			JRequest::setVar('view', 'tickets');
		else
			JRequest::setVar('view', 'rsticketspro');
		JRequest::setVar('layout', 'default');
		
		$mainframe =& JFactory::getApplication();
		$option    = 'com_rsticketspro';
		$mainframe->setUserState($option.'.ticketsfilter.predefined_search', $search->id);
		
		parent::display();
	}
}
?>