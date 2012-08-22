<?php
/**
* @version 1.0.0
* @package RSMembership! 1.0.0
* @copyright (C) 2009-2010 www.rsjoomla.com
* @license GPL, http://www.gnu.org/licenses/gpl-2.0.html
*/

defined('_JEXEC') or die('Restricted access');

jimport( 'joomla.application.component.view');

class RSMembershipViewRSMembership extends JView
{
	function display($tpl = null)
	{
		$mainframe =& JFactory::getApplication();
		
		$params = clone($mainframe->getParams('com_rsmembership'));
		$this->assignRef('params', $params);
		$this->assignRef('columns_no', $this->params->get('columns_no', 1));
		$this->assignRef('show_buttons', $this->params->get('show_buttons', 2));
		$this->assignRef('items', $this->get('memberships'));
		$this->assignRef('pagination', $this->get('pagination'));
		$this->assignRef('total', $this->get('total'));
		
		$this->assignRef('sortColumn', $this->get('sortColumn'));
		$this->assignRef('sortOrder', $this->get('sortOrder'));
		$this->assignRef('limitstart', JRequest::getInt('limitstart', 0));
		
		$Itemid = JRequest::getInt('Itemid',0);
		if ($Itemid > 0)
			$this->assign('Itemid', '&Itemid='.$Itemid);
		else
			$this->assign('Itemid', '');
		
		$this->assign('currency', RSMembershipHelper::getConfig('currency'));
		
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