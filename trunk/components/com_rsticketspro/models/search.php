<?php
/**
* @version 2.0.0
* @package RSTickets! Pro 2.0.0
* @copyright (C) 2010 www.rsjoomla.com
* @license GPL, http://www.gnu.org/licenses/gpl-2.0.html
*/

defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.model');

class RSTicketsProModelSearch extends JModel
{
	var $params = null;
	
	function __construct()
	{
		parent::__construct();
		
		$mainframe =& JFactory::getApplication();
		$option    = 'com_rsticketspro';
		
		if ($mainframe->isAdmin())
		{
			$data  = "staff_itemid=\n";
			$data .= "customer_itemid=\n";
			
			jimport('joomla.html.parameter');
			
			$this->params = new JParameter($data);
		}
		else
			$this->params = $mainframe->getParams('com_rsticketspro');
		
		$user = JFactory::getUser();
		if ($user->get('guest'))
		{
			$link = JRequest::getURI();
			$link = base64_encode($link);
			$user_option = RSTicketsProHelper::isJ16() ? 'com_users' : 'com_user';
			$mainframe->redirect(RSTicketsProHelper::route('index.php?option='.$user_option.'&view=login&return='.$link, false));
		}
	}
	
	function getItemId()
	{
		if (RSTicketsProHelper::isStaff() && $this->params->get('staff_itemid'))
			return '&Itemid='.(int) $this->params->get('staff_itemid');
		
		if (!RSTicketsProHelper::isStaff() && $this->params->get('customer_itemid'))
			return '&Itemid='.(int) $this->params->get('customer_itemid');
		
		return '';
	}
}
?>