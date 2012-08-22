<?php
/**
* @version 1.0.0
* @package RSMembership! 1.0.0
* @copyright (C) 2009-2010 www.rsjoomla.com
* @license GPL, http://www.gnu.org/licenses/gpl-2.0.html
*/

defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.model');

class RSMembershipModelShare extends JModel
{
	var $_data = null;
	var $_total = 0;
	var $_query = '';
	var $_pagination = null;
	var $_db = null;
	
	function __construct()
	{
		parent::__construct();
		$this->_db = JFactory::getDBO();
		$this->_query = $this->_buildQuery();
		
		$mainframe =& JFactory::getApplication();
		$option    = 'com_rsmembership';
		
		// Get pagination request variables
		$limit = $mainframe->getUserStateFromRequest($option.'.share.limit', 'limit', $mainframe->getCfg('list_limit'), 'int');
		$limitstart = $mainframe->getUserStateFromRequest($option.'.share.limitstart', 'limitstart', 0, 'int');

		// In case limit has been changed, adjust it
		$limitstart = ($limit != 0 ? (floor($limitstart / $limit) * $limit) : 0);

		$this->setState($option.'.share.limit', $limit);
		$this->setState($option.'.share.limitstart', $limitstart);
	}
	
	function _buildQuery()
	{
		$layout = JRequest::getVar('layout');
		$query = '';
		
		switch ($layout)
		{			
			case 'article':
				if (RSMembershipHelper::isJ16())
				{
					$query = "SELECT a.*, c.title AS categorytitle FROM #__content a LEFT JOIN #__categories c ON (c.id = a.catid) WHERE 1";
					$filter_word = JRequest::getVar('search', '');
					if (!empty($filter_word))
						$query .= " AND a.`title` LIKE '%".$this->_db->getEscaped($filter_word)."%'";
			
					$sortColumn = JRequest::getVar('filter_order', 'ordering');
					$sortColumn = $this->_db->getEscaped($sortColumn);
			
					$sortOrder = JRequest::getVar('filter_order_Dir', 'ASC');
					$sortOrder = $this->_db->getEscaped($sortOrder);
			
					$query .= " ORDER BY `".$sortColumn."` ".$sortOrder;
				}
				else
				{
					$query = "SELECT a.*, c.title AS categorytitle, s.title AS sectiontitle FROM #__content a LEFT JOIN #__categories c ON (c.id = a.catid) LEFT JOIN #__sections s ON (s.id = a.sectionid) WHERE 1";
					$filter_word = JRequest::getVar('search', '');
					if (!empty($filter_word))
						$query .= " AND a.`title` LIKE '%".$this->_db->getEscaped($filter_word)."%'";
			
					$sortColumn = JRequest::getVar('filter_order', 'ordering');
					$sortColumn = $this->_db->getEscaped($sortColumn);
			
					$sortOrder = JRequest::getVar('filter_order_Dir', 'ASC');
					$sortOrder = $this->_db->getEscaped($sortOrder);
			
					$query .= " ORDER BY `".$sortColumn."` ".$sortOrder;
				}
			break;
			
			case 'section':
				$query = "SELECT * FROM #__sections WHERE 1";
				$filter_word = JRequest::getVar('search', '');
				if (!empty($filter_word))
					$query .= " AND `title` LIKE '%".$this->_db->getEscaped($filter_word)."%'";
		
				$sortColumn = JRequest::getVar('filter_order', 'ordering');
				$sortColumn = $this->_db->getEscaped($sortColumn);
		
				$sortOrder = JRequest::getVar('filter_order_Dir', 'ASC');
				$sortOrder = $this->_db->getEscaped($sortOrder);
		
				$query .= " ORDER BY `".$sortColumn."` ".$sortOrder;
			break;
			
			case 'category':
				if (RSMembershipHelper::isJ16())
				{
					$query = "SELECT * FROM #__categories WHERE `extension`='com_content' ";
					$filter_word = JRequest::getVar('search', '');
					if (!empty($filter_word))
						$query .= " AND `title` LIKE '%".$this->_db->getEscaped($filter_word)."%'";
			
					$sortColumn = JRequest::getVar('filter_order', 'title');
					$sortColumn = $this->_db->getEscaped($sortColumn);
			
					$sortOrder = JRequest::getVar('filter_order_Dir', 'ASC');
					$sortOrder = $this->_db->getEscaped($sortOrder);
			
					$query .= " ORDER BY `".$sortColumn."` ".$sortOrder;
				}
				else
				{
					$query = "SELECT * FROM #__categories WHERE 1";
					$filter_word = JRequest::getVar('search', '');
					if (!empty($filter_word))
						$query .= " AND `title` LIKE '%".$this->_db->getEscaped($filter_word)."%'";
			
					$sortColumn = JRequest::getVar('filter_order', 'ordering');
					$sortColumn = $this->_db->getEscaped($sortColumn);
			
					$sortOrder = JRequest::getVar('filter_order_Dir', 'ASC');
					$sortOrder = $this->_db->getEscaped($sortOrder);
			
					$query .= " ORDER BY `".$sortColumn."` ".$sortOrder;
				}
			break;
			
			case 'module':
				$query = "SELECT * FROM #__modules m WHERE 1";
				$filter_word = JRequest::getVar('search', '');
				if (!empty($filter_word))
					$query .= " AND `title` LIKE '%".$this->_db->getEscaped($filter_word)."%' OR `module` LIKE '%".$this->_db->getEscaped($filter_word)."%'";
		
				$sortColumn = JRequest::getVar('filter_order', 'client_id, position, ordering');
				$sortColumn = $this->_db->getEscaped($sortColumn);
		
				$sortOrder = JRequest::getVar('filter_order_Dir', 'ASC');
				$sortOrder = $this->_db->getEscaped($sortOrder);
		
				$query .= " ORDER BY ".$sortColumn." ".$sortOrder;
			break;
			
			case 'menu':
				if (RSMembershipHelper::isJ16())
				{
					$query = "SELECT id, title AS name, menutype, published FROM #__menu m WHERE published != '-2' AND client_id=0 AND `parent_id` > 0";
					$filter_word = JRequest::getVar('search', '');
					if (!empty($filter_word))
						$query .= " AND `title` LIKE '%".$this->_db->getEscaped($filter_word)."%'";
					
					$sortColumn = JRequest::getVar('filter_order', 'menutype, ordering');
					$sortColumn = $this->_db->getEscaped($sortColumn);
			
					$sortOrder = JRequest::getVar('filter_order_Dir', 'ASC');
					$sortOrder = $this->_db->getEscaped($sortOrder);
			
					$query .= " ORDER BY ".$sortColumn." ".$sortOrder;
				}
				else
				{
					$query = "SELECT * FROM #__menu m WHERE published != '-2'";
					$filter_word = JRequest::getVar('search', '');
					if (!empty($filter_word))
						$query .= " AND `name` LIKE '%".$this->_db->getEscaped($filter_word)."%'";
			
					$sortColumn = JRequest::getVar('filter_order', 'menutype, ordering');
					$sortColumn = $this->_db->getEscaped($sortColumn);
			
					$sortOrder = JRequest::getVar('filter_order_Dir', 'ASC');
					$sortOrder = $this->_db->getEscaped($sortOrder);
			
					$query .= " ORDER BY ".$sortColumn." ".$sortOrder;
				}
			break;
		}
		
		return $query;
	}
	
	function getShareType()
	{
		return JRequest::getVar('share_type');
	}
	
	function getHeaders()
	{
		$headers = array();
		
		$instances = RSMembership::getSharedContentPlugins();
		foreach ($instances as $instance)
			if (method_exists($instance, 'getHeaders'))
				$instance->getHeaders($this->getShareType(), $headers);
		
		return $headers;
	}
	
	function getData()
	{
		$option = 'com_rsmembership';
		
		if (empty($this->_data))
		{
			if ($this->_isPlugin())
			{
				$instances = RSMembership::getSharedContentPlugins();
				foreach ($instances as $instance)
					if (method_exists($instance, 'getData'))
						$instance->getData($this->getShareType(), $this->_data, $this->getState($option.'.share.limitstart'), $this->getState($option.'.share.limit'));
			}
			else
				$this->_data = $this->_getList($this->_query, $this->getState($option.'.share.limitstart'), $this->getState($option.'.share.limit'));
		}
		
		return $this->_data;
	}
	
	function getTotal()
	{
		if (empty($this->_total))
		{
			if ($this->_isPlugin())
			{
				$instances = RSMembership::getSharedContentPlugins();
				foreach ($instances as $instance)
					if (method_exists($instance, 'getTotal'))
						$instance->getTotal($this->getShareType(), $this->_total);
			}
			else
				$this->_total = $this->_getListCount($this->_query); 
		}
		
		return $this->_total;
	}
	
	function getPagination()
	{
		if (empty($this->_pagination))
		{
			$option = 'com_rsmembership';
			jimport('joomla.html.pagination');
			$this->_pagination = new JPagination($this->getTotal(), $this->getState($option.'.share.limitstart'), $this->getState($option.'.share.limit'));
		}
		
		return $this->_pagination;
	}
	
	function getSortColumn()
	{
		$sortColumn = JRequest::getVar('filter_order', 'ordering');
		if ($this->_isPlugin())
		{
			$instances = RSMembership::getSharedContentPlugins();
			foreach ($instances as $instance)
				if (method_exists($instance, 'getSortColumn'))
					$instance->getSortColumn($this->getShareType(), $sortColumn);
		}
		
		return $sortColumn;
	}
	
	function getSortOrder()
	{
		return JRequest::getVar('filter_order_Dir', 'ASC');
	}
	
	function _isPlugin()
	{
		return JRequest::getVar('layout') == 'plugin';
	}
	
	function getPluginShareTypes()
	{
		$plugins = array();
		
		$instances = RSMembership::getSharedContentPlugins();
		foreach ($instances as $instance)
			if (method_exists($instance, 'getSupportedSharedTypes'))
				$plugins = array_merge($plugins, $instance->getSupportedSharedTypes());
			
		return $plugins;
	}
	
	function getURL()
	{
		$cid = JRequest::getInt('cid', 0);
		
		$membership_id = JRequest::getInt('membership_id', 0);
		$extra_value_id = JRequest::getInt('extra_value_id', 0);
		
		if (!empty($membership_id))
			$row =& JTable::getInstance('RSMembership_Membership_Shared','Table');
		else
			$row =& JTable::getInstance('RSMembership_Extra_Value_Shared','Table');
		$row->load($cid);
		
		return $row;
	}
	
	function addMembershipURL($url)
	{
		$membership_id = JRequest::getInt('membership_id', 0);
		$cid = $url;
		$post = JRequest::get('post');
		
		$row =& JTable::getInstance('RSMembership_Membership_Shared','Table');
		$row->id = $cid;
		$row->membership_id = $membership_id;
		$post['params'] = JRequest::getVar('params', '', 'post', 'none', JREQUEST_ALLOWRAW);
		$row->params = $post['params'];
		$row->type = $post['where'];
		
		if (empty($row->id))
			$row->ordering = $row->getNextOrder("`membership_id`='".$row->membership_id."'");
		
		$row->store();
		
		return true;
	}
	
	function addExtraValueURL($url)
	{
		$extra_value_id = JRequest::getInt('extra_value_id', 0);
		$cid = $url;
		$post = JRequest::get('post');
		
		$row =& JTable::getInstance('RSMembership_Extra_Value_Shared','Table');
		$row->id = $cid;
		$row->extra_value_id = $extra_value_id;
		$post['params'] = JRequest::getVar('params', '', 'post', 'none', JREQUEST_ALLOWRAW);
		$row->params = $post['params'];
		$row->type = $post['where'];
		
		if (empty($row->id))
			$row->ordering = $row->getNextOrder("`extra_value_id`='".$row->extra_value_id."'");
		
		$row->store();
		
		return true;
	}
	
	function addItems($items, $type, $shared_type)
	{
		$membership_id  = JRequest::getInt('membership_id', 0);
		$extra_value_id = JRequest::getInt('extra_value_id', 0);
		
		foreach ($items as $item)
		{
			if ($type == 'membership')
			{
				$row =& JTable::getInstance('RSMembership_Membership_Shared','Table');
				$row->membership_id = $membership_id;
			}
			else
			{
				$row =& JTable::getInstance('RSMembership_Extra_Value_Shared','Table');
				$row->extra_value_id = $extra_value_id;
			}
			$row->params = $item;
			$row->type = $shared_type;
			
			if ($type == 'membership')
				$this->_db->setQuery("SELECT * FROM #__rsmembership_membership_shared WHERE `params`='".$this->_db->getEscaped($item)."' AND `membership_id`='".$membership_id."' AND `type`='".$row->type."'");
			else
				$this->_db->setQuery("SELECT * FROM #__rsmembership_extra_value_shared WHERE `params`='".$this->_db->getEscaped($item)."' AND `extra_value_id`='".$extra_value_id."' AND `type`='".$row->type."'");
			$this->_db->query();
			if ($this->_db->getNumRows())
				continue;
			
			if ($type == 'membership')
				$row->ordering = $row->getNextOrder("`membership_id`='".$row->membership_id."'");
			else
				$row->ordering = $row->getNextOrder("`extra_value_id`='".$row->extra_value_id."'");
			$row->store();
		}
		return true;
	}
}
?>