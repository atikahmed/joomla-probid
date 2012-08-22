<?php
/**
* @version 1.0.0
* @package RSMembership! 1.0.0
* @copyright (C) 2009-2010 www.rsjoomla.com
* @license GPL, http://www.gnu.org/licenses/gpl-2.0.html
*/

defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.model');

class RSMembershipModelTransactions extends JModel
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
		$limit 		= $mainframe->getUserStateFromRequest($option.'.transactions.limit', 'limit', $mainframe->getCfg('list_limit'), 'int');
		$limitstart = $mainframe->getUserStateFromRequest($option.'.transactions.limitstart', 'limitstart', 0, 'int');

		// In case limit has been changed, adjust it
		$limitstart = ($limit != 0 ? (floor($limitstart / $limit) * $limit) : 0);

		$this->setState($option.'.transactions.limit', $limit);
		$this->setState($option.'.transactions.limitstart', $limitstart);
	}
	
	function _buildQuery()
	{
		$query = "SELECT t.*, IFNULL(u.email, t.user_email) AS email FROM #__rsmembership_transactions t LEFT JOIN #__users u ON (`t`.`user_id`=`u`.`id`) WHERE 1";
		
		$filter_word = $this->getFilterWord();
		if (strlen($filter_word))
			$query .= " AND u.email LIKE '%".$this->_db->getEscaped($filter_word)."%'";
		
		$filter_type = $this->getFilterType();
		if ($filter_type && is_array($filter_type))
			$query .= " AND t.type IN ('".implode("','", $filter_type)."')";
		
		$filter_gateway = $this->getFilterGateway();
		if ($filter_gateway && is_array($filter_gateway))
			$query .= " AND t.gateway IN ('".implode("','", $filter_gateway)."')";
		
		$from = $this->getDateFrom();
		if ($from)
		{
			$date = JFactory::getDate($from);
			$query .= " AND t.date >= '".$this->_db->getEscaped($date->toUnix())."'";
		}
			
		$to = $this->getDateTo();
		if ($to)
		{
			$date = JFactory::getDate($to);
			$query .= " AND t.date <= '".$this->_db->getEscaped($date->toUnix())."'";
		}
		
		$filter_status = $this->getFilterStatus();
		if ($filter_status && is_array($filter_status))
			$query .= " AND t.status IN ('".implode("','", $filter_status)."')";
		;
		$sortColumn = $this->getSortColumn();
		$sortOrder 	= $this->getSortOrder();
		
		$query .= " ORDER BY ".$this->_db->getEscaped($sortColumn)." ".$this->_db->getEscaped($sortOrder);
		
		return $query;
	}
	
	function getTransactions()
	{		
		if (empty($this->_data))
		{
			$option 	 = 'com_rsmembership';
			$this->_data = $this->_getList($this->_query, $this->getState($option.'.transactions.limitstart'), $this->getState($option.'.transactions.limit'));
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
			jimport('joomla.html.pagination');
			
			$option    		   = 'com_rsmembership';
			$this->_pagination = new JPagination($this->getTotal(), $this->getState($option.'.transactions.limitstart'), $this->getState($option.'.transactions.limit'));
		}
		
		return $this->_pagination;
	}
	
	function remove($cids)
	{
		$cids = implode(',', $cids);

		$query = "DELETE FROM #__rsmembership_transactions WHERE `id` IN (".$cids.")";
		$this->_db->setQuery($query);
		return $this->_db->query();
	}
	
	function getCache()
	{
		return RSMembershipHelper::getCache();
	}
	
	function getTransactionTypes()
	{
		$return = array();
		
		$types = array('new', 'renew', 'upgrade', 'addextra');	
		foreach ($types as $type)
		{
			$tmp = new stdClass();
			$tmp->key 	= $type;
			$tmp->value = JText::_('RSM_TRANSACTION_'.$type);
			
			$return[] = $tmp;
		}
		
		return $return;
	}
	
	function getFilterWord()
	{
		$mainframe =& JFactory::getApplication();
		$option    = 'com_rsmembership';
		
		return $mainframe->getUserStateFromRequest($option.'.transactions.search', 'search');
	}
	
	function getSortColumn()
	{
		$mainframe =& JFactory::getApplication();
		$option    = 'com_rsmembership';
		
		return $mainframe->getUserStateFromRequest($option.'.transactions.filter_order', 'filter_order', 'date');
	}
	
	function getSortOrder()
	{
		$mainframe =& JFactory::getApplication();
		$option    = 'com_rsmembership';
		
		return $mainframe->getUserStateFromRequest($option.'.transactions.filter_order_Dir', 'filter_order_Dir', 'DESC');
	}
	
	function getFilterType()
	{
		$mainframe =& JFactory::getApplication();
		$option    = 'com_rsmembership';
		$filter_types = $mainframe->getUserStateFromRequest($option.'.transactions.filter_type', 'filter_type');
		if (is_array($filter_types))
			foreach ($filter_types as $i => $filter_type)
				$filter_types[$i] = JFilterInput::clean($filter_type, 'word');
			
		return $filter_types;
	}
	
	function getGateways()
	{		
		$return = array();
		
		$plugins = RSMembership::getPlugins();
		array_unshift($plugins, 'No Gateway');
		foreach ($plugins as $plugin => $name)
		{
			$tmp = new stdClass();
			$tmp->key 	= $name;
			$tmp->value = $name;
			
			$return[] = $tmp;
		}
		
		return $return;
	}
	
	function getFilterGateway()
	{
		$mainframe =& JFactory::getApplication();
		$option    = 'com_rsmembership';
		$db =& JFactory::getDBO();
		
		$filter_gateways = $mainframe->getUserStateFromRequest($option.'.transactions.filter_gateway', 'filter_gateway');
		if (is_array($filter_gateways))
			foreach ($filter_gateways as $i => $filter_gateway)
				$filter_gateways[$i] = $db->getEscaped($filter_gateway);
			
		return $filter_gateways;
	}
	
	function getDateFrom()
	{
		$mainframe =& JFactory::getApplication();
		$option    = 'com_rsmembership';
		
		return $mainframe->getUserStateFromRequest($option.'.transactions.date_from', 'date_from');
	}
	
	function getDateTo()
	{
		$mainframe =& JFactory::getApplication();
		$option    = 'com_rsmembership';
		
		return $mainframe->getUserStateFromRequest($option.'.transactions.date_to', 'date_to');
	}
	
	function getStatuses()
	{
		$return = array();
		
		$statuses = array('pending', 'completed', 'denied');
		foreach ($statuses as $status)
		{
			$tmp = new stdClass();
			$tmp->key 	= $status;
			$tmp->value = strip_tags(JText::_('RSM_TRANSACTION_STATUS_'.$status));
			$return[] = $tmp;
		}
		
		return $return;
	}
	
	function getFilterStatus()
	{
		$mainframe =& JFactory::getApplication();
		$option    = 'com_rsmembership';
		$db =& JFactory::getDBO();
		
		$filter_statuses = $mainframe->getUserStateFromRequest($option.'.transactions.filter_status', 'filter_status');
		if (is_array($filter_statuses))
			foreach ($filter_statuses as $i => $filter_status)
				$filter_statuses[$i] = $db->getEscaped($filter_status);
			
		return $filter_statuses;
	}
	
	function getLog()
	{
		$cid = JRequest::getInt('cid');
		$transaction =& JTable::getInstance('RSMembership_Transactions','Table');
		$transaction->load($cid);
		return $transaction->response_log;
	}
}
?>