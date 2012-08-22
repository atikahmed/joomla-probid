<?php
/**
* @version 2.0.0
* @package RSTickets! Pro 2.0.0
* @copyright (C) 2010 www.rsjoomla.com
* @license GPL, http://www.gnu.org/licenses/gpl-2.0.html
*/

defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.model');

class RSTicketsProModelUsers extends JModel
{
	var $_data = null;
	var $_total = 0;
	var $_query = '';
	var $_pagination = null;
	var $_db = null;
	var $_permissions = null;
	
	var $params = null;
	
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
		
		if (!RSTicketsProHelper::isStaff())
		{
			JError::raiseWarning(500, JText::_('RST_STAFF_CANNOT_VIEW_USERS'));
			$mainframe->redirect(RSTicketsProHelper::route('index.php?option=com_rsticketspro&view=rsticketspro', false));
		}
		
		$this->_permissions = RSTicketsProHelper::getCurrentPermissions();
		if (!$this->_permissions->add_ticket_customers && !$this->_permissions->add_ticket_staff)
		{
			JError::raiseWarning(500, JText::_('RST_STAFF_CANNOT_VIEW_USERS'));
			$mainframe->redirect(RSTicketsProHelper::route('index.php?option=com_rsticketspro&view=rsticketspro', false));
		}
		
		$this->_db = JFactory::getDBO();
		
		// Get pagination request variables
		$limit		= JRequest::getVar('limit', $mainframe->getCfg('list_limit'), '', 'int');
		$limitstart	= JRequest::getVar('limitstart', 0, '', 'int');

		// In case limit has been changed, adjust it
		$limitstart = ($limit != 0 ? (floor($limitstart / $limit) * $limit) : 0);

		$this->setState($option.'.users.limit', $limit);
		$this->setState($option.'.users.limitstart', $limitstart);
		
		$this->_query = $this->_buildQuery();
	}
	
	function _buildQuery()
	{
		$mainframe =& JFactory::getApplication();

		$this->_db->setQuery("SELECT VERSION()");
		$mysql_version = $this->_db->loadResult();
		
		$user = JFactory::getUser();
		
		$query = "SELECT * FROM #__users WHERE 1";
		
		// don't show the logged in user
		if (!$this->_permissions->add_ticket)
		{
			$query .= " AND (id != '".$user->get('id')."')";
		}
		
		// don't show customers
		if (!$this->_permissions->add_ticket_customers)
		{
			// less than 4.1
			if (version_compare($mysql_version, '4.1', '<'))
			{
				$this->_db->setQuery("SELECT user_id FROM #__rsticketspro_staff");
				$user_ids = $this->_db->loadResultArray();
				if (!empty($user_ids))
					$query .= " AND id IN (".implode(',', $user_ids).")";
			}
			else
			{
				$query .= " AND id IN (SELECT user_id FROM #__rsticketspro_staff)";
			}
		}
		
		// don't show staff members
		// special condition here - if the staff can submit tickets on his own we need to exclude him from the list of staff members
		if (!$this->_permissions->add_ticket_staff)
		{
			// less than 4.1
			if (version_compare($mysql_version, '4.1', '<'))
			{
				$this->_db->setQuery("SELECT user_id FROM #__rsticketspro_staff".($this->_permissions->add_ticket ? " WHERE user_id !='".$user->get('id')."'" : ""));
				$user_ids = $this->_db->loadResultArray();
				if (!empty($user_ids))
					$query .= " AND id NOT IN (".implode(',', $user_ids).")";
			}
			else
			{
				$query .= " AND id NOT IN (SELECT user_id FROM #__rsticketspro_staff".($this->_permissions->add_ticket ? " WHERE user_id !='".$user->get('id')."'" : "").")";
			}
		}
		
		$filter_word = JRequest::getString('search', '');
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
			$this->_data = $this->_getList($this->_query, $this->getState($option.'.users.limitstart'), $this->getState($option.'.users.limit'));
		
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
			$this->_pagination = new JPagination($this->getTotal(), $this->getState($option.'.users.limitstart'), $this->getState($option.'.users.limit'));
		}
		
		return $this->_pagination;
	}
}
?>