<?php
/**
* @version 1.0.0
* @package RSMembership! 1.0.0
* @copyright (C) 2009-2010 www.rsjoomla.com
* @license GPL, http://www.gnu.org/licenses/gpl-2.0.html
*/

defined('_JEXEC') or die('Restricted access');

jimport( 'joomla.application.component.view');

class RSMembershipViewMembership extends JView
{
	function display($tpl = null)
	{
		$mainframe =& JFactory::getApplication();
		
		$params = clone($mainframe->getParams('com_rsmembership'));
		$this->assignRef('params', $params);
		
		$membership = $this->get('membership');
		$this->assignRef('membership', $membership);
		
		if (empty($membership->id) || !$membership->published)
		{
			JError::raiseWarning(500, JText::_('RSM_MEMBERSHIP_NOT_EXIST'));
			$mainframe->redirect(JRoute::_('index.php?option=com_rsmembership', false));
		}
		
		$category = $this->get('category');
		
		$pathway =& $mainframe->getPathway();
		if ($category)
		{
			$catid = $category->id.':'.JFilterOutput::stringURLSafe($category->name);
			$pathway->addItem($category->name, JRoute::_('index.php?option=com_rsmembership&view=rsmembership&catid='.$catid));
		}
		$pathway->addItem($membership->name, '');
		
		$document =& JFactory::getDocument();
		if (!$params->get('page_title'))
			$document->setTitle($membership->name);
		else
			$document->setTitle($params->get('page_title').' - '.$membership->name);
		
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