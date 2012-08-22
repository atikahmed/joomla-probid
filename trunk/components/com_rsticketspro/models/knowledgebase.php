<?php
/**
* @version 2.0.0
* @package RSTickets! Pro 2.0.0
* @copyright (C) 2010 www.rsjoomla.com
* @license GPL, http://www.gnu.org/licenses/gpl-2.0.html
*/

defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.model');

class RSTicketsProModelKnowledgebase extends JModel
{
	var $_data = null;
	var $_total = 0;
	var $_pagination = null;
	var $_db = null;
	
	var $is_staff;
	
	function __construct()
	{
		parent::__construct();
		$this->_db = JFactory::getDBO();
		
		$mainframe =& JFactory::getApplication();
		$option    = 'com_rsticketspro';
		
		$this->params   = $mainframe->getParams('com_rsticketspro');
		$this->is_staff = RSTicketsProHelper::isStaff();
		
		// Get pagination request variables
		//$limit		= JRequest::getVar('limit', $mainframe->getCfg('list_limit'), '', 'int');
		$limit		= $mainframe->getUserStateFromRequest($option.'.categories.limit', 'limit', $mainframe->getCfg('list_limit'));
		$limitstart	= JRequest::getVar('limitstart', 0, '', 'int');

		// In case limit has been changed, adjust it
		$limitstart = ($limit != 0 ? (floor($limitstart / $limit) * $limit) : 0);

		$this->setState($option.'.categories.limit', $limit);
		$this->setState($option.'.categories.limitstart', $limitstart);
		
		$this->category_id = JRequest::getInt('cid', 0);
		
		$pathway =& $mainframe->getPathway();
		$path = $this->getPath();
		foreach ($path as $item)
			$pathway->addItem($item->name, $item->link);
	}
	
	function getCategories()
	{
		$mainframe =& JFactory::getApplication();
		$option    = 'com_rsticketspro';
		
		$category_id = $this->category_id;

		$query = "SELECT * FROM #__rsticketspro_kb_categories WHERE `published`='1' AND `parent_id`='".$category_id."'";	
		if (!$this->is_staff)
			$query .= " AND private='0'";
		$query .= " ORDER BY `ordering` ASC";
		
		return $this->_getList($query);
	}
	
	function getPath()
	{
		$return = array();
		
		$parent_id = $this->category_id;
		$row =& JTable::getInstance('RSTicketsPro_KB_Categories','Table');
		
		while ($parent_id > 0)
		{
			$row->load($parent_id);
			$parent_id = $row->parent_id;
			
			$obj = new stdClass();
			$obj->name = $row->name;
			$obj->link = RSTicketsProHelper::route('index.php?option=com_rsticketspro&view=knowledgebase&cid='.$row->id.':'.JFilterOutput::stringURLSafe($row->name));
			
			$return[] = $obj;
		}
		
		krsort($return);
		
		return $return;
	}
	
	function getCategory()
	{
		$category_id = $this->category_id;
		
		$row =& JTable::getInstance('RSTicketsPro_KB_Categories','Table');
		$row->load($category_id);
		
		$cat =& JTable::getInstance('RSTicketsPro_KB_Categories','Table');
		if ($row->parent_id)
		{
			$parent_id = $row->parent_id;
			$cat->load($parent_id);
			while ($parent_id > 0)
			{
				$parent_id = $cat->parent_id;
				$cat->load($parent_id);
				
				if ($cat->private)
					$row->private = 1;
				if (!$cat->published)
					$row->published = 0;
			}
		}
		
		if ((!$this->is_staff && $row->private) || !$row->published)
		{
			$mainframe =& JFactory::getApplication();
			JError::raiseWarning(500, JText::_('RST_CANNOT_VIEW_CATEGORY'));
			$mainframe->redirect('index.php?option=com_rsticketspro&view=knowledgebase');
		}
		
		$document =& JFactory::getDocument();
		if (!empty($row->meta_description))
			$document->setMetaData('description', $row->meta_description);
		if (!empty($row->meta_keywords))
			$document->setMetaData('keywords', $row->meta_keywords);
		
		return $row;
	}
	
	function getContent()
	{
		$option    		= 'com_rsticketspro';
		$category_id 	= $this->category_id;
		
		$query = "SELECT * FROM #__rsticketspro_kb_content WHERE `published`='1' AND `category_id`='".$category_id."'";
		
		if (!$this->is_staff)
			$query .= " AND private='0'";
		
		$filter_word = $this->getFilterWord();
		if (!empty($filter_word))
		{
			$filter_word = $this->_db->getEscaped($filter_word);
			$filter_word = str_replace('%', '\%', $filter_word);
			$filter_word = str_replace(' ', '%', $filter_word);
			$query .= " AND (`name` LIKE '%".$filter_word."%' OR `text` LIKE '%".$filter_word."%')";
		}
		
		$this->_total = $this->_getListCount($query);
		
		$sortColumn = $this->getSortColumn();
		$sortColumn = $this->_db->getEscaped($sortColumn);
		
		$sortOrder = $this->getSortOrder();
		$sortOrder = $this->_db->getEscaped($sortOrder);
		
		$query .= " ORDER BY `".$sortColumn."` ".$sortOrder;
		
		return $this->_getList($query, $this->getState($option.'.categories.limitstart'), $this->getState($option.'.categories.limit'));
	}
	
	function getFilterWord()
	{
		$mainframe =& JFactory::getApplication();
		$option    = 'com_rsticketspro';
		
		return $mainframe->getUserStateFromRequest($option.'.kbcontent.filter', 'search', '');
	}
	
	function getSortColumn()
	{
		$mainframe =& JFactory::getApplication();
		$option    = 'com_rsticketspro';
		
		return $mainframe->getUserStateFromRequest($option.'.kbcontent.filter_order', 'filter_order', $this->params->get('order_by', 'ordering'));
	}
	
	function getSortOrder()
	{
		$mainframe =& JFactory::getApplication();
		$option    = 'com_rsticketspro';
		
		return $mainframe->getUserStateFromRequest($option.'.kbcontent.filter_order_Dir', 'filter_order_Dir', $this->params->get('order_dir', 'ASC'));
	}
	
	function getContentTotal()
	{
		return $this->_total;
	}
	
	function getContentPagination()
	{
		if (empty($this->_pagination))
		{
			$option = 'com_rsticketspro';
			jimport('joomla.html.pagination');
			$this->_pagination = new JPagination($this->getContentTotal(), $this->getState($option.'.categories.limitstart'), $this->getState($option.'.categories.limit'));
		}
		
		return $this->_pagination;
	}
	
	function getResultsWord()
	{
		$mainframe 	=& JFactory::getApplication();
		$option 	= 'com_rsticketspro';
		return $mainframe->getUserStateFromRequest($option.'.kbresults.search', 'search', '');
	}
	
	function getResults()
	{
		$option 	= 'com_rsticketspro';
		
		$value = $this->getResultsWord();
		if (!$value)
			return array();
		
		$escvalue = $this->_db->getEscaped($value);
		$escvalue = str_replace('%','\%',$escvalue);
		$escvalue = str_replace(' ','%',$escvalue);
		
		$is_staff = RSTicketsProHelper::isStaff();
		
		if (!$is_staff)
			$this->_db->setQuery("SELECT id FROM #__rsticketspro_kb_categories c WHERE c.private='0' AND c.published='1'");
		else
			$this->_db->setQuery("SELECT id FROM #__rsticketspro_kb_categories c WHERE c.published='1'");
		$cat_ids = $this->_db->loadResultArray();
		
		$results = $this->_getList("SELECT c.*, cat.name AS category_name FROM #__rsticketspro_kb_content c LEFT JOIN #__rsticketspro_kb_categories cat ON (c.category_id=cat.id) WHERE (c.name LIKE '%".$escvalue."%' OR c.text LIKE '%".$escvalue."%') ".($is_staff ? "" : " AND c.`private`='0'")." AND c.published=1 ".($cat_ids ? " AND c.category_id IN (".implode(",", $cat_ids).")" : "")." ORDER BY cat.ordering, c.ordering", $this->getState($option.'.categories.limitstart'), $this->getState($option.'.categories.limit'));
		
		$this->_total = 0;
		if ($results)
		{
			$this->_db->setQuery("SELECT COUNT(id) FROM #__rsticketspro_kb_content c WHERE (c.name LIKE '%".$escvalue."%' OR c.text LIKE '%".$escvalue."%') ".($is_staff ? "" : " AND c.`private`='0'")." AND c.published=1 ".($cat_ids ? " AND c.category_id IN (".implode(",", $cat_ids).")" : ""));
		
			$this->_total = $this->_db->loadResult();
		}
		
		return $results;
	}
	
	function getResultsTotal()
	{
		return $this->_total;
	}
	
	function getResultsPagination()
	{
		if (empty($this->_pagination))
		{
			$option = 'com_rsticketspro';
			jimport('joomla.html.pagination');
			$this->_pagination = new JPagination($this->getResultsTotal(), $this->getState($option.'.categories.limitstart'), $this->getState($option.'.categories.limit'));
		}
		
		return $this->_pagination;
	}
}
?>