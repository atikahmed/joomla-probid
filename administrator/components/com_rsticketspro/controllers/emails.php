<?php
/**
* @version 2.0.0
* @package RSTickets! Pro 2.0.0
* @copyright (C) 2010 www.rsjoomla.com
* @license GPL, http://www.gnu.org/licenses/gpl-2.0.html
*/

defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.controller');

class RSTicketsProControllerEmails extends RSTicketsProController
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
		JRequest::setVar('view', 'emails');
		JRequest::setVar('layout', 'edit');
		parent::display();
	}
	
	/**
	 * Logic to save
	 */
	function save()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit('Invalid Token');

		// Get the model
		$model = $this->getModel('emails');
		
		// Save
		$result = $model->save();
		$language = $model->getLanguage();
		$type = $model->getType();
		
		$task = JRequest::getCmd('task');
		switch($task)
		{
			case 'apply':
				$link = 'index.php?option=com_rsticketspro&controller=emails&task=edit&type='.$type.'&language='.$language;
				if ($result)
					$this->setRedirect($link, JText::_('RST_EMAILS_SAVED_OK'));
				else
					$this->setRedirect($link, JText::_('RST_EMAILS_SAVED_ERROR'));
			break;
		
			case 'save':
				$link = 'index.php?option=com_rsticketspro&view=emails&language='.$language;
				if ($result)
					$this->setRedirect($link, JText::_('RST_EMAILS_SAVED_OK'));
				else
					$this->setRedirect($link, JText::_('RST_EMAILS_SAVED_ERROR'));
			break;
		}
	}
}
?>