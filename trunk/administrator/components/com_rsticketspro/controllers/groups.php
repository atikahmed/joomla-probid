<?php
/**
* @version 2.0.0
* @package RSTickets! Pro 2.0.0
* @copyright (C) 2010 www.rsjoomla.com
* @license GPL, http://www.gnu.org/licenses/gpl-2.0.html
*/

defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.controller');

class RSTicketsProControllerGroups extends RSTicketsProController
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
		JRequest::setVar('view', 'groups');
		JRequest::setVar('layout', 'edit');
		parent::display();
	}
	
	/**
	 * Logic to remove
	 */
	function remove()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit('Invalid Token');

		// Get the model
		$model = $this->getModel('groups');
		
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
			$msg = JText::sprintf('RST_GROUPS_DELETED', $total);
			
			// Clean the cache, if any
			$cache =& JFactory::getCache('com_rsticketspro');
			$cache->clean();
		}
		
		// Redirect
		$this->setRedirect('index.php?option=com_rsticketspro&view=groups', $msg);
	}
	
	/**
	 * Logic to save
	 */
	function save()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit('Invalid Token');

		// Get the model
		$model = $this->getModel('groups');
		
		// Save
		$result = $model->save();
		$cid = $model->getId();
		
		$tabposition = JRequest::getInt('tabposition', 0);
		
		$task = JRequest::getCmd('task');
		switch($task)
		{
			case 'apply':
				$link = 'index.php?option=com_rsticketspro&controller=groups&task=edit&cid='.$cid.'&tabposition='.$tabposition;
				if ($result)
					$this->setRedirect($link, JText::_('RST_GROUP_SAVED_OK'));
				else
					$this->setRedirect($link, JText::_('RST_GROUP_SAVED_ERROR'));
			break;
		
			case 'save':
				$link = 'index.php?option=com_rsticketspro&view=groups';
				if ($result)
					$this->setRedirect($link, JText::_('RST_GROUP_SAVED_OK'));
				else
					$this->setRedirect($link, JText::_('RST_GROUP_SAVED_ERROR'));
			break;
		}
	}
}
?>