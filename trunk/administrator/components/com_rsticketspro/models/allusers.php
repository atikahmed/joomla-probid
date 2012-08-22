<?php
/**
* @version 2.0.0
* @package RSTickets! Pro 2.0.0
* @copyright (C) 2010 www.rsjoomla.com
* @license GPL, http://www.gnu.org/licenses/gpl-2.0.html
*/

defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.model');

class RSTicketsProModelAllusers extends JModel
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
		$limit = $mainframe->getUserStateFromRequest($option.'.allusers.limit', 'limit', $mainframe->getCfg('list_limit'), 'int');
		$limitstart = $mainframe->getUserStateFromRequest($option.'.allusers.limitstart', 'limitstart', 0, 'int');

		// In case limit has been changed, adjust it
		$limitstart = ($limit != 0 ? (floor($limitstart / $limit) * $limit) : 0);

		$this->setState($option.'.allusers.limit', $limit);
		$this->setState($option.'.allusers.limitstart', $limitstart);
	}
	
	function _buildQuery()
	{
		$mainframe =& JFactory::getApplication();
		
		$query = "SELECT * FROM #__users WHERE 1";
		
		$this->_db->setQuery("SELECT `user_id` FROM #__rsticketspro_staff");
		$staff = $this->_db->loadResultArray();
		if (!empty($staff))
			$query .= " AND `id` NOT IN (".implode(',', $staff).")";
		
		$filter_word = JRequest::getCmd('search', '');
		if (!empty($filter_word))
			$query .= " AND (`name` LIKE '%".$filter_word."%' OR `username` LIKE '%".$filter_word."%' OR `email` LIKE '%".$filter_word."%')";
		
		$sortColumn = JRequest::getVar('filter_order', 'name');
		$sortColumn = $this->_db->getEscaped($sortColumn);
		
		$sortOrder = JRequest::getVar('filter_order_Dir', 'ASC');
		$sortOrder = $this->_db->getEscaped($sortOrder);
		
		$query .= " ORDER BY ".$sortColumn." ".$sortOrder;
		
		return $query;
	}
	
	function getUsers()
	{
		$option = 'com_rsticketspro';
		
		if (empty($this->_data))
			$this->_data = $this->_getList($this->_query, $this->getState($option.'.allusers.limitstart'), $this->getState($option.'.allusers.limit'));
		
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
			$this->_pagination = new JPagination($this->getTotal(), $this->getState($option.'.allusers.limitstart'), $this->getState($option.'.allusers.limit'));
		}
		
		return $this->_pagination;
	}
}
?>