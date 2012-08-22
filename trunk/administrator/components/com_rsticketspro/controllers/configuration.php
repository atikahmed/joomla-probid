<?php
/**
* @version 2.0.0
* @package RSTickets! Pro 2.0.0
* @copyright (C) 2010 www.rsjoomla.com
* @license GPL, http://www.gnu.org/licenses/gpl-2.0.html
*/

defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.controller');

class RSTicketsProControllerConfiguration extends RSTicketsProController
{
	function __construct()
	{
		parent::__construct();
		$this->registerTask('apply', 'save');
	}
	
	/**
	 * Logic to save configuration
	 */
	function save()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit( 'Invalid Token' );

		// Get the model
		$model = $this->getModel('configuration');
		
		// Save
		$model->save();
		
		$tabposition = JRequest::getInt('tabposition', 0);
		
		$task = JRequest::getCmd('task');
		if ($task == 'apply')
			$link = 'index.php?option=com_rsticketspro&view=configuration&tabposition='.$tabposition;
		else
			$link = 'index.php?option=com_rsticketspro';
		
		// Redirect
		$this->setRedirect($link, JText::_('RST_CONFIGURATION_OK'));
	}
	
	function cancel()
	{
		$this->setRedirect('index.php?option=com_rsticketspro');
	}
}
?>