<?php
/**
* @version 2.0.0
* @package RSTickets! Pro 2.0.0
* @copyright (C) 2010 www.rsjoomla.com
* @license GPL, http://www.gnu.org/licenses/gpl-2.0.html
*/

defined('_JEXEC') or die('Restricted access');

jimport( 'joomla.application.component.view');

class RSTicketsProViewDashboard extends JView
{
	function display($tpl = null)
	{
		$mainframe =& JFactory::getApplication();
		
		$this->assignRef('user', JFactory::getUser());
		
		$login_option = RSTicketsProHelper::isJ16() ? 'com_users' : 'com_user';
		$Itemid 	  = JRequest::getInt('Itemid');
		$return		  = base64_encode(RSTicketsProHelper::route('index.php?option=com_rsticketspro&view=dashboard'.($Itemid ? '&Itemid='.$Itemid : ''), false));
		$login_link   = JRoute::_('index.php?option='.$login_option.'&view=login'.($Itemid ? '&Itemid='.$Itemid : '').'&return='.$return);
		$this->assignRef('login_link', $login_link);
		
		$this->assignRef('categories', $this->get('categories'));
		$this->assignRef('tickets',  $this->get('tickets'));
		
		$params = $mainframe->getParams('com_rsticketspro');
		$this->assignRef('params', $params);
		
		JHTML::_('behavior.mootools');
		
		$doc =& JFactory::getDocument();
		if (RSTicketsProHelper::isJ16() || JPluginHelper::isEnabled('system', 'mtupgrade'))
			$doc->addScript(JURI::root(true).'/components/com_rsticketspro/assets/js/more.js');
		
		if (RSTicketsProHelper::isJ16())
		{
			// Description
			if ($params->get('menu-meta_description'))
				$this->document->setDescription($params->get('menu-meta_description'));
			// Keywords
			if ($params->get('menu-meta_keywords'))
				$this->document->setMetadata('keywords', $params->get('menu-meta_keywords'));
			// Robots
			if ($params->get('robots'))
				$this->document->setMetadata('robots', $params->get('robots'));
		}
		
		parent::display($tpl);
	}
	
	function trim($string,$max=255,$more='...')
	{
		return RSTicketsProHelper::shorten($string, $max, $more);
	}
}