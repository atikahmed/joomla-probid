<?php
/**
* @version 2.0.0
* @package RSTickets! Pro 2.0.0
* @copyright (C) 2010 www.rsjoomla.com
* @license GPL, http://www.gnu.org/licenses/gpl-2.0.html
*/

defined('_JEXEC') or die('Restricted access');

jimport( 'joomla.application.component.view');

class RSTicketsProViewKnowledgebase extends JView
{
	var $hot_hits = 0;
	
	function display($tpl = null)
	{
		$mainframe =& JFactory::getApplication();
		
		$params = $mainframe->getParams('com_rsticketspro');
		$this->assignRef('params', $params);
		
		$layout = $this->getLayout();
		
		if ($layout == 'results')
		{
			$this->assignRef('items', $this->get('results'));
			$this->assignRef('pagination', $this->get('resultspagination'));
			$this->assignRef('word', $this->get('resultsword'));
		}
		else
		{
			$this->assignRef('categories', $this->get('categories'));
			
			$this->assignRef('items', $this->get('content'));
			$this->assignRef('pagination', $this->get('contentpagination'));
			
			$this->assignRef('sortColumn', $this->get('sortcolumn'));
			$this->assignRef('sortOrder', $this->get('sortorder'));
			
			$filter_word = $this->get('filterword');
			$this->assignRef('filter_word', $filter_word);
			
			$this->assign('category', $this->get('category'));
			$this->assign('cid', JRequest::getInt('cid'));
		}
		
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
	
	function isHot($hits)
	{
		if (empty($this->hot_hits))
			$this->hot_hits = RSTicketsProHelper::getConfig('kb_hot_hits');
		
		return $hits >= $this->hot_hits;
	}
}