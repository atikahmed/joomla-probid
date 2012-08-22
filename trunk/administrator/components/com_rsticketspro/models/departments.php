<?php
/**
* @version 2.0.0
* @package RSTickets! Pro 2.0.0
* @copyright (C) 2010 www.rsjoomla.com
* @license GPL, http://www.gnu.org/licenses/gpl-2.0.html
*/

defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.model');

class RSTicketsProModelDepartments extends JModel
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
		$limit = $mainframe->getUserStateFromRequest($option.'.departments.limit', 'limit', $mainframe->getCfg('list_limit'), 'int');
		$limitstart = $mainframe->getUserStateFromRequest($option.'.departments.limitstart', 'limitstart', 0, 'int');

		// In case limit has been changed, adjust it
		$limitstart = ($limit != 0 ? (floor($limitstart / $limit) * $limit) : 0);

		$this->setState($option.'.departments.limit', $limit);
		$this->setState($option.'.departments.limitstart', $limitstart);
	}
	
	function _buildQuery()
	{
		$mainframe =& JFactory::getApplication();

		$query = "SELECT * FROM #__rsticketspro_departments WHERE 1";

		$filter_word = JRequest::getCmd('search', '');
		if (!empty($filter_word))
			$query .= " AND `name` LIKE '%".$filter_word."%'";
		
		$filter_state = $mainframe->getUserStateFromRequest('rsticketspro_filter_state', 'filter_state');
		if ($filter_state != '')
			$query .= " AND `published`='".($filter_state == 'U' ? '0' : '1')."'";
		
		$sortColumn = JRequest::getVar('filter_order', 'ordering');
		$sortColumn = $this->_db->getEscaped($sortColumn);
		
		$sortOrder = JRequest::getVar('filter_order_Dir', 'ASC');
		$sortOrder = $this->_db->getEscaped($sortOrder);
		
		$query .= " ORDER BY `".$sortColumn."` ".$sortOrder;
		
		return $query;
	}
	
	function getDepartments()
	{
		$option = 'com_rsticketspro';
		
		if (empty($this->_data))
			$this->_data = $this->_getList($this->_query, $this->getState($option.'.departments.limitstart'), $this->getState($option.'.departments.limit'));
		
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
			$this->_pagination = new JPagination($this->getTotal(), $this->getState($option.'.departments.limitstart'), $this->getState($option.'.departments.limit'));
		}
		
		return $this->_pagination;
	}
	
	function getDepartment()
	{
		$cid = JRequest::getInt('cid', 0);
		
		$row =& JTable::getInstance('RSTicketsPro_Departments','Table');
		$row->load($cid);
		
		return $row;
	}
	
	function publish($cid=array(), $publish=1)
	{
		if (!is_array($cid) || count($cid) > 0)
		{
			$publish = (int) $publish;
			$cids = implode(',', $cid);

			$query = "UPDATE #__rsticketspro_departments SET `published`='".$publish."' WHERE `id` IN (".$cids.")";
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

		$this->_db->setQuery("DELETE FROM #__rsticketspro_departments WHERE `id` IN (".$cids.")");
		$this->_db->query();
		
		// delete the custom fields and values as well
		$this->_db->setQuery("DELETE FROM #__rsticketspro_custom_fields_values WHERE custom_field_id IN (SELECT id FROM #__rsticketspro_custom_fields WHERE department_id IN (".$cids."))");
		$this->_db->query();
		$this->_db->setQuery("DELETE FROM #__rsticketspro_custom_fields WHERE `department_id` IN (".$cids.")");
		$this->_db->query();
		
		// remove the department from existing staff members
		$this->_db->setQuery("DELETE FROM #__rsticketspro_staff_to_department WHERE department_id IN (".$cids.")");
		$this->_db->query();
		
		// delete tickets, messages, notes and files
		$this->_db->setQuery("DELETE FROM #__rsticketspro_ticket_messages WHERE ticket_id IN (SELECT id FROM #__rsticketspro_tickets WHERE department_id IN (".$cids."))");
		$this->_db->query();
		$this->_db->setQuery("DELETE FROM #__rsticketspro_ticket_notes WHERE ticket_id IN (SELECT id FROM #__rsticketspro_tickets WHERE department_id IN (".$cids."))");
		$this->_db->query();
		$this->_db->setQuery("DELETE FROM #__rsticketspro_ticket_files WHERE ticket_id IN (SELECT id FROM #__rsticketspro_tickets WHERE department_id IN (".$cids."))");
		$this->_db->query();
		$this->_db->setQuery("DELETE FROM #__rsticketspro_tickets WHERE department_id IN (".$cids.")");
		$this->_db->query();
		
		return true;
	}
	
	function save()
	{
		$row =& JTable::getInstance('RSTicketsPro_Departments','Table');
		$post = JRequest::get('post');
		
		$post['prefix'] = strtoupper($post['prefix']);
		
		if (!$row->bind($post))
			return JError::raiseWarning(500, $row->getError());
		
		if (empty($row->id))
			$row->ordering = $row->getNextOrder();
		
		unset($row->next_number);
		
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