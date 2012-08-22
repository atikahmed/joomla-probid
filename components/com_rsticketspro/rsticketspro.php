<?php
/**
* @version 2.0.0
* @package RSTickets! Pro 2.0.0
* @copyright (C) 2010 www.rsjoomla.com
* @license GPL, http://www.gnu.org/licenses/gpl-2.0.html
*/

defined('_JEXEC') or die('Restricted access');

// Require the base controller
require_once(JPATH_COMPONENT.DS.'controller.php');
require_once(JPATH_SITE.DS.'administrator'.DS.'components'.DS.'com_rsticketspro'.DS.'helpers'.DS.'rsticketspro.php');
require_once(JPATH_SITE.DS.'administrator'.DS.'components'.DS.'com_rsticketspro'.DS.'helpers'.DS.'html.php');
require_once(JPATH_SITE.DS.'administrator'.DS.'components'.DS.'com_rsticketspro'.DS.'helpers'.DS.'securimage'.DS.'securimage.php');
require_once(JPATH_SITE.DS.'administrator'.DS.'components'.DS.'com_rsticketspro'.DS.'helpers'.DS.'recaptcha'.DS.'recaptchalib.php');

RSTicketsProHelper::readConfig();

// See if this is a request for a specific controller
$controller = JRequest::getCmd('controller');
if (!empty($controller) && file_exists(JPATH_COMPONENT.DS.'controllers'.DS.$controller.'.php'))
{
	require_once(JPATH_COMPONENT.DS.'controllers'.DS.$controller.'.php');
	$controller = 'RSTicketsProController'.$controller;
	$RSTicketsProController = new $controller();
}
else
	$RSTicketsProController = new RSTicketsProController();
	
$RSTicketsProController->execute(JRequest::getCmd('task'));

// Redirect if set
$RSTicketsProController->redirect();
?>