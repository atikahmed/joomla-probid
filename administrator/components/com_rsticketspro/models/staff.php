<?php
/**
* @version 2.0.0
* @package RSTickets! Pro 2.0.0
* @copyright (C) 2010 www.rsjoomla.com
* @license GPL, http://www.gnu.org/licenses/gpl-2.0.html
*/

defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.model');

class RSTicketsProModelStaff extends JModel
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
		$limit = $mainframe->getUserStateFromRequest($option.'.staff.limit', 'limit', $mainframe->getCfg('list_limit'), 'int');
		$limitstart = $mainframe->getUserStateFromRequest($option.'.staff.limitstart', 'limitstart', 0, 'int');

		// In case limit has been changed, adjust it
		$limitstart = ($limit != 0 ? (floor($limitstart / $limit) * $limit) : 0);

		$this->setState($option.'.staff.limit', $limit);
		$this->setState($option.'.staff.limitstart', $limitstart);
	}
	
	function _buildQuery()
	{
		$mainframe =& JFactory::getApplication();

		$query = "SELECT s.*, g.name, u.username, u.name as fullname, u.email, p.name AS priority_name FROM #__rsticketspro_staff s LEFT JOIN #__rsticketspro_groups g ON (g.id = s.group_id) LEFT JOIN #__users u ON (u.id = s.user_id) LEFT JOIN #__rsticketspro_priorities p ON (s.priority_id=p.id) WHERE 1";

		$filter_word = JRequest::getCmd('search', '');
		if (!empty($filter_word))
			$query .= " AND u.username LIKE '%".$filter_word."%'";
		
		$sortColumn = JRequest::getVar('filter_order', 'g.name');
		$sortColumn = $this->_db->getEscaped($sortColumn);
		
		$sortOrder = JRequest::getVar('filter_order_Dir', 'ASC');
		$sortOrder = $this->_db->getEscaped($sortOrder);
		
		$query .= " ORDER BY ".$sortColumn." ".$sortOrder;
		
		return $query;
	}
	
	function getStaff()
	{
		$option = 'com_rsticketspro';
		
		if (empty($this->_data))
			$this->_data = $this->_getList($this->_query, $this->getState($option.'.staff.limitstart'), $this->getState($option.'.staff.limit'));
		
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
			$this->_pagination = new JPagination($this->getTotal(), $this->getState($option.'.staff.limitstart'), $this->getState($option.'.staff.limit'));
		}
		
		return $this->_pagination;
	}
	
	function getStaffMember()
	{
		$cid = JRequest::getInt('cid', 0);
		
		$row =& JTable::getInstance('RSTicketsPro_Staff','Table');
		$row->load($cid);
		
		$row->username = JText::_('RST_NO_USER_SELECTED');
		$row->departments = array();
		if (!empty($row->user_id))
		{
			$user = JFactory::getUser($row->user_id);
			$row->username = $user->get('username');
			
			$this->_db->setQuery("SELECT `department_id` FROM #__rsticketspro_staff_to_department WHERE `user_id`='".$row->user_id."'");
			$row->departments = $this->_db->loadResultArray();
		}
		
		return $row;
	}
	
	function remove($cids)
	{
		$cids = implode(',', $cids);

		$this->_db->setQuery("SELECT user_id FROM #__rsticketspro_staff WHERE `id` IN (".$cids.")");
		$user_ids = $this->_db->loadResultArray();
		
		$this->_db->setQuery("DELETE FROM #__rsticketspro_staff WHERE `id` IN (".$cids.")");
		$this->_db->query();
		
		if ($user_ids)
		{
			$user_ids = implode(',', $user_ids);
			$this->_db->setQuery("DELETE FROM #__rsticketspro_staff_to_department WHERE `user_id` IN (".$user_ids.")");
			$this->_db->query();
		
			$this->_db->setQuery("UPDATE #__rsticketspro_tickets SET staff_id=0 WHERE staff_id IN (".$user_ids.")");
			$this->_db->query();
		}
		
		return true;
	}
	
	function save()
	{
		$row =& JTable::getInstance('RSTicketsPro_Staff','Table');
		$post = JRequest::get('post');
		
		$post['signature'] = JRequest::getVar('signature', '', 'post', 'none', JREQUEST_ALLOWHTML);
		
		if (!$row->bind($post))
			return JError::raiseWarning(500, $row->getError());
		
		if ($row->store())
		{
			$this->_db->setQuery("DELETE FROM #__rsticketspro_staff_to_department WHERE `user_id`='".$row->user_id."'");
			$this->_db->query();
			
			foreach ($post['department_id'] as $department_id)
			{
				$department_id = (int) $department_id;
				if (empty($department_id)) continue;
				
				$this->_db->setQuery("INSERT INTO #__rsticketspro_staff_to_department SET `department_id`='".$department_id."', `user_id`='".$row->user_id."'");
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
		
		if (class_exists('plgUserRSTicketsPro'))
			plgUserRSTicketsPro::onLoginUser($user=array(), $options=array());
	}
	
	function getId()
	{
		return $this->_id;
	}
	
	function getGroups()
	{
		return $this->_getList("SELECT * FROM #__rsticketspro_groups ORDER BY `name` ASC");
	}
	
	function getDepartments()
	{
		return $this->_getList("SELECT * FROM #__rsticketspro_departments ORDER BY `ordering` ASC");
	}
	
	function getPriorities()
	{
		$priorities = $this->_getList("SELECT * FROM #__rsticketspro_priorities ORDER BY `ordering` ASC");
		$tmp = new stdClass();
		$tmp->id = 0;
		$tmp->name = JText::_('RST_ALL_PRIORITIES');
		array_unshift($priorities, $tmp);
		
		return $priorities;
	}
}
?>