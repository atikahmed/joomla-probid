<?php
/**
* @version 2.0.0
* @package RSTickets! Pro 2.0.0
* @copyright (C) 2010 www.rsjoomla.com
* @license GPL, http://www.gnu.org/licenses/gpl-2.0.html
*/

defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.model');

class RSTicketsProModelKBContent extends JModel
{
	var $_data = null;
	var $_total = 0;
	var $_query = '';
	var $_pagination = null;
	var $_db = null;
	
	var $_id = 0;
	
	function __construct()
	{
		parent::__construct();
		$this->_db = JFactory::getDBO();
		$this->_query = $this->_buildQuery();
		
		$mainframe =& JFactory::getApplication();
		$option    = 'com_rsticketspro';
		
		// Get pagination request variables
		$limit = $mainframe->getUserStateFromRequest($option.'.kbcontent.limit', 'limit', $mainframe->getCfg('list_limit'), 'int');
		$limitstart = $mainframe->getUserStateFromRequest($option.'.kbcontent.limitstart', 'limitstart', 0, 'int');

		// In case limit has been changed, adjust it
		$limitstart = ($limit != 0 ? (floor($limitstart / $limit) * $limit) : 0);

		$this->setState($option.'.kbcontent.limit', $limit);
		$this->setState($option.'.kbcontent.limitstart', $limitstart);
	}
	
	function _buildQuery()
	{
		$mainframe =& JFactory::getApplication();

		$query = "SELECT c.*, cat.name AS category FROM #__rsticketspro_kb_content c LEFT JOIN #__rsticketspro_kb_categories cat ON (c.category_id=cat.id) WHERE 1";

		$filter_word = JRequest::getCmd('search', '');
		if (!empty($filter_word))
			$query .= " AND c.`name` LIKE '%".$filter_word."%'";
		
		$filter_state = $mainframe->getUserStateFromRequest('rsticketspro.filter_state', 'filter_state');
		if ($filter_state != '')
			$query .= " AND c.`published`='".($filter_state == 'U' ? '0' : '1')."'";
		
		$category_state = $mainframe->getUserStateFromRequest('rsticketspro.category_state', 'category_state', -1, 'int');
		if ($category_state != '-1')
			$query .= " AND `category_id`='".(int) $category_state."'";
		
		$sortColumn = JRequest::getVar('filter_order', 'ordering');
		$sortColumn = $this->_db->getEscaped($sortColumn);
		
		$sortOrder = JRequest::getVar('filter_order_Dir', 'ASC');
		$sortOrder = $this->_db->getEscaped($sortOrder);
		
		$query .= " ORDER BY `".$sortColumn."` ".$sortOrder;
		
		return $query;
	}
	
	function getKBArticles()
	{
		$option = 'com_rsticketspro';
		
		if (empty($this->_data))
			$this->_data = $this->_getList($this->_query, $this->getState($option.'.kbcontent.limitstart'), $this->getState($option.'.kbcontent.limit'));
		
		return $this->_data;
	}
	
	function getTotal()
	{
		if (empty($this->_total))
			$this->_total = $this->_getListCount($this->_query); 
		
		return $this->_total;
	}
	
	function getPagination()
	{
		if (empty($this->_pagination))
		{
			$option = 'com_rsticketspro';
			jimport('joomla.html.pagination');
			$this->_pagination = new JPagination($this->getTotal(), $this->getState($option.'.kbcontent.limitstart'), $this->getState($option.'.kbcontent.limit'));
		}
		
		return $this->_pagination;
	}
	
	function getKBArticle()
	{
		$cid = JRequest::getInt('cid', 0);
		
		$row =& JTable::getInstance('RSTicketsPro_KB_Content','Table');
		$row->load($cid);
		
		if ($row->from_ticket_id)
		{
			$this->_db->setQuery("SELECT id, subject, code FROM #__rsticketspro_tickets WHERE id='".(int) $row->from_ticket_id."'");
			$row->ticket = $this->_db->loadObject();
		}
		
		return $row;
	}
	
	function publish($cid=array(), $publish=1)
	{
		if (!is_array($cid) || count($cid) > 0)
		{
			$publish = (int) $publish;
			$cids = implode(',', $cid);

			$query = "UPDATE #__rsticketspro_kb_content SET `published`='".$publish."' WHERE `id` IN (".$cids.")";
			$this->_db->setQuery($query);
			if (!$this->_db->query())
			{
				$this->setError($this->_db->getErrorMsg());
				return false;
			}
		}
		return $cid;
	}
	
	function remove($cids)
	{
		$cids = implode(',', $cids);

		$query = "DELETE FROM #__rsticketspro_kb_content WHERE `id` IN (".$cids.")";
		$this->_db->setQuery($query);
		$this->_db->query();
		
		return true;
	}
	
	function save()
	{
		$date =& JFactory::getDate();
		$row  =& JTable::getInstance('RSTicketsPro_KB_Content','Table');
		$post = JRequest::get('post');
		
		$post['text'] = JRequest::getVar('text', '', 'post', 'none', JREQUEST_ALLOWRAW);
		
		if (!$row->bind($post))
			return JError::raiseWarning(500, $row->getError());
		
		unset($row->hits);
		unset($row->created);
		
		if (empty($row->id))
		{
			$row->ordering = $row->getNextOrder("`category_id`='".$row->category_id."'");
			$row->created = $date->toUnix();
		}
		$row->modified = $date->toUnix();
		
		if ($row->store())
		{
			$this->_id = $row->id;
			return true;
		}
		else
		{
			JError::raiseWarning(500, $row->getError());
			return false;
		}
	}
	
	function getId()
	{
		return $this->_id;
	}
}
?>