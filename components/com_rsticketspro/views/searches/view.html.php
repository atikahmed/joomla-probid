<?php
/**
* @version 2.0.0
* @package RSTickets! Pro 2.0.0
* @copyright (C) 2010 www.rsjoomla.com
* @license GPL, http://www.gnu.org/licenses/gpl-2.0.html
*/

defined('_JEXEC') or die('Restricted access');

jimport( 'joomla.application.component.view');

class RSTicketsProViewSearches extends JView
{
	function display($tpl = null)
	{
		$mainframe =& JFactory::getApplication();
		
		if ($mainframe->isSite())
		{
			$params = $mainframe->getParams('com_rsticketspro');
			$this->assignRef('params', $params);
		}
		
		if ($mainframe->isSite())
		{
			$pathway =& $mainframe->getPathway();
			$pathway->addItem(JText::_('RST_MANAGE_SEARCHES'), RSTicketsProHelper::route('index.php?option=com_rsticketspro&view=searches'));
		}
		
		$task = JRequest::getVar('task');
		if ($task == 'edit' || $task == 'save')
		{
			$row = $this->get('search');
			$this->assignRef('row', $row);
			
			if ($mainframe->isSite())
			{
				$pathway->addItem($row->name, '');
			}
			
			$lists['default'] = JHTML::_('select.booleanlist', 'default', '', $row->default);
			$this->assignRef('lists', $lists);
		}
		else
		{		
			$this->assignRef('searches', $this->get('searches'));
			$this->assignRef('pagination', $this->get('pagination'));
		}
		
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