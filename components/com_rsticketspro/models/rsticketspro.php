<?php
/**
* @version 2.0.0
* @package RSTickets! Pro 2.0.0
* @copyright (C) 2010 www.rsjoomla.com
* @license GPL, http://www.gnu.org/licenses/gpl-2.0.html
*/

defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.model');

class RSTicketsProModelRSTicketsPro extends JModel
{
	var $_data = null;
	var $_total = 0;
	var $_query = '';
	var $_pagination = null;
	var $_db = null;
	
	var $params = null;
	
	var $_permissions = array();
	
	function __construct()
	{
		parent::__construct();
		
		$mainframe =& JFactory::getApplication();
		$option    = 'com_rsticketspro';
		
		$user = JFactory::getUser();
		if ($user->get('guest'))
		{
			$link = JRequest::getURI();
			$link = base64_encode($link);
			$user_option = RSTicketsProHelper::isJ16() ? 'com_users' : 'com_user';
			$mainframe->redirect(RSTicketsProHelper::route('index.php?option='.$user_option.'&view=login&return='.$link, false));
		}
		
		$this->_db = JFactory::getDBO();
		
		// Get pagination request variables
		$limit		= JRequest::getVar('limit', $mainframe->getCfg('list_limit'), '', 'int');
		$limitstart	= JRequest::getVar('limitstart', 0, '', 'int');

		// In case limit has been changed, adjust it
		$limitstart = ($limit != 0 ? (floor($limitstart / $limit) * $limit) : 0);

		$this->setState($option.'.tickets.limit', $limit);
		$this->setState($option.'.tickets.limitstart', $limitstart);
		
		// hack for backend management
		if ($mainframe->isAdmin())
		{
			$data  = "orderby=date\n";
			$data .= "direction=desc\n";
			
			jimport('joomla.html.parameter');
			
			$this->params = new JParameter($data);
		}
		else
			$this->params = $mainframe->getParams('com_rsticketspro');
		
		switch ($this->params->get('orderby'))
		{
			default: $sortColumn = 'date'; $sortOrder = 'DESC'; break;
			
			case 'last_reply': $sortColumn = 'last_reply'; $sortOrder = 'DESC'; break;
			case 'subject': $sortColumn = 'subject'; $sortOrder = 'ASC'; break;
			case 'status': $sortColumn = 'status'; $sortOrder = 'DESC'; break;
			case 'priority': $sortColumn = 'priority'; $sortOrder = 'DESC'; break;
			case 'replies': $sortColumn = 'replies'; $sortOrder = 'DESC'; break;
		}
		
		if ($this->params->get('direction'))
			$sortOrder = $this->params->get('direction');
		
		$filter_order = $mainframe->getUserStateFromRequest($option.'.tickets.filter_order', 'filter_order', $sortColumn);
		$filter_order_Dir = $mainframe->getUserStateFromRequest($option.'.tickets.filter_order_Dir', 'filter_order_Dir', $sortOrder);
		
		$this->setState($option.'.tickets.filter_order', $filter_order);
		$this->setState($option.'.tickets.filter_order_Dir', $filter_order_Dir);
		
		$this->_query = $this->_buildQuery();
	}
	
	function getPermissions()
	{
		$mainframe =& JFactory::getApplication();
		if ($mainframe->isAdmin() && empty($this->_permissions))
		{
			JError::raiseWarning(500, JText::_('RST_PERMISSIONS_ERROR'));
			$mainframe->redirect(RSTicketsProHelper::route('index.php?option=com_rsticketspro', false));
			return;
		}
		
		return @$this->_permissions;
	}
	
	function _buildQuery()
	{
		$mainframe =& JFactory::getApplication();
		$option    = 'com_rsticketspro';
		
		$this->_db->setQuery("SELECT VERSION()");
		$mysql_version = $this->_db->loadResult();
		
		$what = RSTicketsProHelper::getConfig('show_user_info');
		
		$cusername = $what == 'username' ? 'c.username AS customer' : 'c.username';
		$cname = $what == 'name' ? 'c.name AS customer' : 'c.name';
		$cemail = $what == 'email' ? 'c.email AS customer' : 'c.email';
		
		$susername = $what == 'username' ? 's.username AS staff' : 's.username';
		$sname = $what == 'name' ? 's.name AS staff' : 's.name';
		$semail = $what == 'email' ? 's.email AS staff' : 's.email';
		
		$query = "SELECT t.*, $cusername, $cname, $cemail, $susername, $sname, $semail, st.name AS status, pr.name AS priority FROM #__rsticketspro_tickets t LEFT JOIN #__users c ON (t.customer_id = c.id) LEFT JOIN #__users s ON (t.staff_id = s.id) LEFT JOIN #__rsticketspro_statuses st ON (st.id = t.status_id) LEFT JOIN #__rsticketspro_priorities pr ON (pr.id = t.priority_id) WHERE 1";

		$user = JFactory::getUser();
		
		// staff member ?
		$this->is_staff = RSTicketsProHelper::isStaff();
		if ($this->is_staff)
		{
			$departments = RSTicketsProHelper::getCurrentDepartments();
			
			// do we have a filter set ?
			$show_filter = $this->params->get('show_filter');
			if ($show_filter)
				switch ($show_filter)
				{
					case 'show_assigned':
						$query .= " AND staff_id = '".(int) $user->get('id')."'";
					break;
					case 'show_submitted':
						$query .= " AND customer_id = '".(int) $user->get('id')."'";
					break;
					case 'show_both':
						$query .= " AND (staff_id = '".(int) $user->get('id')."' OR customer_id = '".(int) $user->get('id')."')";
					break;
					
					case 'show_unassigned':
						$query .= " AND staff_id = 0";
					break;
				}
			
			// detect current permissions
			$this->_permissions = RSTicketsProHelper::getCurrentPermissions();
			// can see unassigned tickets ?
			if (!$this->_permissions->see_unassigned_tickets)
				$query .= " AND staff_id > 0";
			// can see other (assigned) tickets ?
			if (!$this->_permissions->see_other_tickets)
				$query .= " AND staff_id IN (0,".(int) $user->get('id').")";
			
			$flagged = $mainframe->getUserStateFromRequest($option.'.ticketsfilter.flagged', 'flagged', 0, 'int');
			if ($flagged)
				$query .= " AND flagged='1'";
		}
		// customer ?
		else
			$query .= " AND customer_id = '".(int) $user->get('id')."'";
		
		$priority_id = $mainframe->getUserStateFromRequest($option.'.ticketsfilter.priority_id', 'priority_id', array(0), 'array');
		JArrayHelper::toInteger($priority_id, array(0));
		
		if ($this->params->get('default_priority') && $priority_id[0] == 0)
		{
			$default_priority = $this->params->get('default_priority');
			if (is_array($default_priority))
				$default_priority = implode(',', $default_priority);
			
			$query .= " AND priority_id IN (".$default_priority.")";
		}
		
		$status_id = $mainframe->getUserStateFromRequest($option.'.ticketsfilter.status_id', 'status_id', array(0), 'array');
		JArrayHelper::toInteger($status_id, array(0));
		
		if ($this->params->get('default_status') && $status_id[0] == 0)
		{
			$default_status = $this->params->get('default_status');
			if (is_array($default_status))
				$default_status = implode(',', $default_status);
			
			$query .= " AND status_id IN (".$default_status.")";
		}
		
		// are we searching ?
		//$task = $mainframe->getUserStateFromRequest($option.'.ticketsfilter.rsticketspro_search', 'task', '', 'int');
		$task = JRequest::getCmd('task');
		if ($task == 'search')
		{
			$session = JFactory::getSession();
			$session->set($option.'.ticketsfilter.rsticketspro_search', 1);
		}
		
		$filter_word = $mainframe->getUserStateFromRequest($option.'.ticketsfilter.filter_word', 'filter_word', '');
		if ($filter_word)
		{
			$this->setState($option.'.ticketsfilter.filter_word', $filter_word);
			
			$filter_word = str_replace('%', '\%', $filter_word);
			$filter_word = str_replace(' ', '%',  $filter_word);
			$filter_word = $this->_db->getEscaped($filter_word);
			
			if (version_compare($mysql_version, '4.1', '<'))
			{
				$this->_db->setQuery("SELECT ticket_id FROM #__rsticketspro_ticket_messages WHERE message LIKE '%".$filter_word."%'");
				$ticket_ids = $this->_db->loadResultArray();
				if (empty($ticket_ids))
					$ticket_ids = array(0);
					
				$query .= " AND (code LIKE '%".$filter_word."%' OR subject LIKE '%".$filter_word."%' OR t.id IN (".implode(',', $ticket_ids)."))";
			}
			else
				$query .= " AND (code LIKE '%".$filter_word."%' OR subject LIKE '%".$filter_word."%' OR t.id IN (SELECT ticket_id FROM #__rsticketspro_ticket_messages WHERE message LIKE '%".$filter_word."%'))";
		}
		
		$customer = $mainframe->getUserStateFromRequest($option.'.ticketsfilter.customer', 'customer', '', 'string');
		if ($customer && is_string($customer))
		{
			$this->setState($option.'.ticketsfilter.customer', $customer);
			
			$customer = str_replace('%', '\%', $customer);
			$customer = str_replace(' ', '%', $customer);
			$customer = $this->_db->getEscaped($customer);
			$query .= " AND (c.username LIKE '%".$customer."%' OR c.name LIKE '%".$customer."%' OR c.email LIKE '%".$customer."%')";
		}
			
		$staff = $mainframe->getUserStateFromRequest($option.'.ticketsfilter.staff', 'staff', '');
		if (($staff || $staff === '0') && !is_object($staff) && !is_array($staff))
		{
			$this->setState($option.'.ticketsfilter.staff', $staff);
			
			$staff = str_replace('%', '\%', $staff);
			$staff = str_replace(' ', '%', $staff);
			$staff = $this->_db->getEscaped($staff);
			
			if ($staff === '0')
				$query .= " AND staff_id = 0";
			else
				$query .= " AND (s.username LIKE '%".$staff."%' OR s.name LIKE '%".$staff."%' OR s.email LIKE '%".$staff."%')";
		}
		
		$department_id = $mainframe->getUserStateFromRequest($option.'.ticketsfilter.department_id', 'department_id', array(0), 'array');
		JArrayHelper::toInteger($department_id, array(0));
		if (@$department_id[0] != 0)
			$query .= " AND department_id IN (".implode(',', $department_id).")";
			
		if ($this->is_staff && !empty($departments))
		{
			if ($show_filter != 'show_assigned' && $show_filter != 'show_unassigned')
				$query .= " AND (department_id IN (".implode(',', $departments).") OR customer_id='".$user->get('id')."')";
			else
				$query .= " AND department_id IN (".implode(',', $departments).")";
		}
			
		$this->setState($option.'.ticketsfilter.department_id', $department_id);
		
		if ($priority_id)
		{
			if ($priority_id[0] != 0)
				$query .= " AND priority_id IN (".implode(',', $priority_id).")";
			
			$this->setState($option.'.ticketsfilter.priority_id', $priority_id);
		}
		
		if ($status_id)
		{
			if ($status_id[0] != 0)
				$query .= " AND status_id IN (".implode(',', $status_id).")";
			
			$this->setState($option.'.ticketsfilter.status_id', $status_id);
		}
		// end search check
		
		$sortColumn = $this->_db->getEscaped($this->getSortColumn());
		$sortOrder = $this->_db->getEscaped($this->getSortOrder());
		
		$query .= " ORDER BY ".$sortColumn." ".$sortOrder;
		
		return $query;
	}
	
	function getTickets()
	{
		$option = 'com_rsticketspro';
		
		if (empty($this->_data))
			$this->_data = $this->_getList($this->_query, $this->getState($option.'.tickets.limitstart'), $this->getState($option.'.tickets.limit'));
		
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
			$this->_pagination = new JPagination($this->getTotal(), $this->getState($option.'.tickets.limitstart'), $this->getState($option.'.tickets.limit'));
		}
		
		return $this->_pagination;
	}
	
	function getSortColumn()
	{
		$option = 'com_rsticketspro';
		return $this->getState($option.'.tickets.filter_order');
	}
	
	function getSortOrder()
	{
		$option = 'com_rsticketspro';
		return $this->getState($option.'.tickets.filter_order_Dir');
	}
	
	function getSearching()
	{
		$option = 'com_rsticketspro';
		$session = JFactory::getSession();
		return $session->get($option.'.ticketsfilter.rsticketspro_search');
	}
	
	function getSearches()
	{
		$user = JFactory::getUser();
		
		return $this->_getList("SELECT * FROM #__rsticketspro_searches WHERE user_id='".(int) $user->get('id')."' ORDER BY ordering ASC");
	}
	
	function getPredefinedSearch()
	{
		$mainframe =& JFactory::getApplication();
		$option    = 'com_rsticketspro';
		return $mainframe->getUserState($option.'.ticketsfilter.predefined_search', 0);
	}
	
	function getPriorityColors()
	{
		$this->_db->setQuery("SELECT * FROM #__rsticketspro_priorities WHERE published='1'");
		return $this->_db->loadObjectList();
	}
}
?>