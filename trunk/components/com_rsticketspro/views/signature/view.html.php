<?php
/**
* @version 2.0.0
* @package RSTickets! Pro 2.0.0
* @copyright (C) 2010 www.rsjoomla.com
* @license GPL, http://www.gnu.org/licenses/gpl-2.0.html
*/

defined('_JEXEC') or die('Restricted access');

jimport( 'joomla.application.component.view');

class RSTicketsProViewSignature extends JView
{
	function display($tpl = null)
	{
		$mainframe =& JFactory::getApplication();
		
		if ($mainframe->isSite())
		{
			$params = $mainframe->getParams('com_rsticketspro');
			$this->assignRef('params', $params);
		}
		
		$this->assign('signature', RSTicketsProHelper::getSignature());
		
		$this->assignRef('editor', JFactory::getEditor());
		
		$this->assign('show_footer', RSTicketsProHelper::getConfig('rsticketspro_link'));
		$this->assign('footer', RSTicketsProHelper::getFooter());
		
		if (RSTicketsProHelper::isJ16() && $mainframe->isSite())
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
		
		parent::display();
	}
}