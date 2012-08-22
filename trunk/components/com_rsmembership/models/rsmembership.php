<?php
/**
* @version 1.0.0
* @package RSMembership! 1.0.0
* @copyright (C) 2009-2010 www.rsjoomla.com
* @license GPL, http://www.gnu.org/licenses/gpl-2.0.html
*/

defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.model');

class RSMembershipModelRSMembership extends JModel
{
	var $_data = null;
	var $_total = 0;
	var $_query = '';
	var $_pagination = null;
	var $_db = null;
	
	function __construct()
	{
		parent::__construct();
		
		$mainframe =& JFactory::getApplication();
		$option = 'com_rsmembership';
		
		// Get pagination request variables
		$limit		= JRequest::getVar('limit', $mainframe->getCfg('list_limit'), '', 'int');
		$limitstart	= JRequest::getVar('limitstart', 0, '', 'int');

		// In case limit has been changed, adjust it
		$limitstart = ($limit != 0 ? (floor($limitstart / $limit) * $limit) : 0);

		$this->setState($option.'.memberships.limit', $limit);
		$this->setState($option.'.memberships.limitstart', $limitstart);
		
		$params = clone($mainframe->getParams('com_rsmembership'));
		switch ($params->get('orderby'))
		{
			case 'title': $sortColumn = 'm.name'; $sortOrder = 'ASC'; break;
			case 'rtitle': $sortColumn = 'm.name'; $sortOrder = 'DESC'; break;
			case 'price': $sortColumn = 'price'; $sortOrder = 'ASC'; break;
			case 'rprice': $sortColumn = 'price'; $sortOrder = 'DESC'; break;
			default: case 'order': $sortColumn = 'ordering'; $sortOrder = 'ASC'; break;
		}
		
		$filter_order = $mainframe->getUserStateFromRequest($option.'.memberships.filter_order', 'filter_order', $sortColumn);
		$filter_order_Dir = $mainframe->getUserStateFromRequest($option.'.memberships.filter_order_Dir', 'filter_order_Dir', $sortOrder);
		
		$this->setState($option.'.memberships.filter_order', $filter_order);
		$this->setState($option.'.memberships.filter_order_Dir', $filter_order_Dir);
		
		$this->_query = $this->_buildQuery();
	}
	
	function _buildQuery()
	{
		$mainframe =& JFactory::getApplication();
		$option = 'com_rsmembership';
		
		$params = clone($mainframe->getParams('com_rsmembership'));
		
		$query = "SELECT c.name AS category_name, m.* FROM #__rsmembership_memberships m LEFT JOIN #__rsmembership_categories c ON (c.id=m.category_id) WHERE m.`published`='1' ";
		
		$category_id = JRequest::getInt('catid');
		if ($category_id)
		{
			$query .= " AND m.category_id='".$category_id."'";
		}
		else
		{
			$categories = $params->get('categories', array());
			if (!is_array($categories))
				$categories = (array) $categories;
			
			if (!empty($categories))
				$query .= " AND m.category_id IN (".implode(',', $categories).")";
		}
		
		$sortColumn = $this->_db->getEscaped($this->getSortColumn());
		$sortOrder  = $this->_db->getEscaped($this->getSortOrder());
		
		$query .= " ORDER BY ".$sortColumn." ".$sortOrder;
		return $query;
	}
	
	function getMemberships()
	{
		$option = 'com_rsmembership';
		
		if (empty($this->_data))
		{
			$pattern = '#<hr\s+id=("|\')system-readmore("|\')\s*\/*>#i';
			
			$this->_data = $this->_getList($this->_query, $this->getState($option.'.memberships.limitstart'), $this->getState($option.'.memberships.limit'));
			if (is_array($this->_data))
				foreach ($this->_data as $i => $row)
				{
					if ($row->use_trial_period)
						$this->_data[$i]->price = $row->trial_price;
					
					if (preg_match($pattern, $row->description))
						list($row->description, $fulldescription) = preg_split($pattern, $row->description, 2);
				}
		}
		
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
			$option = 'com_rsmembership';
			jimport('joomla.html.pagination');
			$this->_pagination = new JPagination($this->getTotal(), $this->getState($option.'.memberships.limitstart'), $this->getState($option.'.memberships.limit'));
		}
		
		return $this->_pagination;
	}
	
	function getSortColumn()
	{
		$option = 'com_rsmembership';
		return $this->getState($option.'.memberships.filter_order');
	}
	
	function getSortOrder()
	{
		$option = 'com_rsmembership';
		return $this->getState($option.'.memberships.filter_order_Dir');
	}
}
?>