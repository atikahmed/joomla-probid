<?php
/**
* @version 1.0.0
* @package RSMembership! 1.0.0
* @copyright (C) 2009-2010 www.rsjoomla.com
* @license GPL, http://www.gnu.org/licenses/gpl-2.0.html
*/

defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.model');

class RSMembershipModelCoupons extends JModel
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
		$option    = 'com_rsmembership';
		
		// Get pagination request variables
		$limit = $mainframe->getUserStateFromRequest($option.'.coupons.limit', 'limit', $mainframe->getCfg('list_limit'), 'int');
		$limitstart = $mainframe->getUserStateFromRequest($option.'.coupons.limitstart', 'limitstart', 0, 'int');

		// In case limit has been changed, adjust it
		$limitstart = ($limit != 0 ? (floor($limitstart / $limit) * $limit) : 0);

		$this->setState($option.'.coupons.limit', $limit);
		$this->setState($option.'.coupons.limitstart', $limitstart);
	}
	
	function _buildQuery()
	{
		$mainframe =& JFactory::getApplication();

		$query = "SELECT * FROM #__rsmembership_coupons WHERE 1";

		$filter_word = JRequest::getString('search', '');
		if (!empty($filter_word))
			$query .= " AND `name` LIKE '%".$filter_word."%'";
		
		$filter_state = $mainframe->getUserStateFromRequest('rsmembership_filter_state', 'filter_state');
		if ($filter_state != '')
			$query .= " AND `published`='".($filter_state == 'U' ? '0' : '1')."'";
		
		$sortColumn = JRequest::getVar('filter_order', 'date_added');
		$sortColumn = $this->_db->getEscaped($sortColumn);
		
		$sortOrder = JRequest::getVar('filter_order_Dir', 'DESC');
		$sortOrder = $this->_db->getEscaped($sortOrder);
		
		$query .= " ORDER BY ".$sortColumn." ".$sortOrder;
		
		return $query;
	}
	
	function getCoupons()
	{
		$option = 'com_rsmembership';
		
		if (empty($this->_data))
			$this->_data = $this->_getList($this->_query, $this->getState($option.'.coupons.limitstart'), $this->getState($option.'.coupons.limit'));
		
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
			$this->_pagination = new JPagination($this->getTotal(), $this->getState($option.'.coupons.limitstart'), $this->getState($option.'.coupons.limit'));
		}
		
		return $this->_pagination;
	}
	
	function getMemberships()
	{
		return $this->_getList('SELECT id, name FROM #__rsmembership_memberships ORDER BY `ordering` ASC');
	}
	
	function getCoupon()
	{
		$cid = JRequest::getVar('cid', 0);
		if (is_array($cid))
			$cid = $cid[0];
		$cid = (int) $cid;
		
		$row =& JTable::getInstance('RSMembership_Coupons','Table');
		$row->load($cid);
		
		$row->items = array();
		if ($cid)
		{
			$this->_db->setQuery("SELECT membership_id FROM #__rsmembership_coupon_items WHERE `coupon_id`='".$cid."'");
			$row->items = $this->_db->loadResultArray();
		}
		
		return $row;
	}
	
	function publish($cid=array(), $publish=1)
	{
		if (!is_array($cid) || count($cid) > 0)
		{
			$publish = (int) $publish;
			$cids = implode(',', $cid);

			$query = "UPDATE #__rsmembership_coupons SET `published`='".$publish."' WHERE `id` IN (".$cids.")"	;
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

		$query = "DELETE FROM #__rsmembership_coupons WHERE `id` IN (".$cids.")";
		$this->_db->setQuery($query);
		$this->_db->query();
		
		return true;
	}
	
	function save()
	{
		$row =& JTable::getInstance('RSMembership_Coupons','Table');
		$post = JRequest::get('post');
		
		// These elements are not filtered for HTML code
		$post['description'] = JRequest::getVar('description', '', 'post', 'none', JREQUEST_ALLOWRAW);
		
		if (!$row->bind($post))
			return JError::raiseWarning(500, $row->getError());
		
		if ($post['date_start'])
		{
			$date = JFactory::getDate($post['date_start']);
			$row->date_start = $date->toUnix();
		}
		if ($post['date_end'])
		{
			$date = JFactory::getDate($post['date_end']);
			$row->date_end = $date->toUnix();
		}
		
		unset($row->date_added);
		if (!$row->id)
		{
			$date = JFactory::getDate();
			$row->date_added = $date->toUnix();
		}
		
		if ($row->store())
		{
			$this->_db->setQuery("DELETE FROM #__rsmembership_coupon_items WHERE `coupon_id`='".$row->id."'");
			$this->_db->query();
			if (isset($post['items']) && is_array($post['items']))
				foreach ($post['items'] as $membership_id)
				{
					$this->_db->setQuery("INSERT INTO #__rsmembership_coupon_items SET `coupon_id`='".$row->id."', `membership_id`='".(int) $membership_id."'");
					$this->_db->query();
				}
			
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