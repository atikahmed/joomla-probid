<?php
/**
* @version 1.0.0
* @package RSMembership! 1.0.0
* @copyright (C) 2009-2010 www.rsjoomla.com
* @license GPL, http://www.gnu.org/licenses/gpl-2.0.html
*/

defined('_JEXEC') or die('Restricted access');

jimport( 'joomla.application.component.view');

class RSMembershipViewUser extends JView
{
	function display($tpl = null)
	{
		$mainframe =& JFactory::getApplication();
		
		// get parameters
		$params = clone($mainframe->getParams('com_rsmembership'));
		$this->assignRef('params', $params);
		
		$this->assignRef('config', $this->get('config'));
		
		$this->assignRef('fields_validation', RSMembershipHelper::getFieldsValidation());
		$this->assignRef('fields', RSMembershipHelper::getFields());
		
		if (RSMembershipHelper::isJ16())
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