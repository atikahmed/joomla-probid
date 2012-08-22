<?php
/**
* @version 2.0.0
* @package RSTickets! Pro 2.0.0
* @copyright (C) 2010 www.rsjoomla.com
* @license GPL, http://www.gnu.org/licenses/gpl-2.0.html
*/

defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.model');

class RSTicketsProModelSearches extends JModel
{
	var $_row;
	
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
			JError::raiseWarning(500, JText::_('RST_CUSTOMER_CANNOT_VIEW_SEARCHES'));
			$mainframe->redirect(RSTicketsProHelper::route('index.php?option=com_rsticketspro&view=rsticketspro', false));
		}
		
		$task = JRequest::getVar('task');
		if ($task == 'edit' || $task == 'save' || $task == 'search')
			$this->_getSearch();
		
		$filter_order = $mainframe->getUserStateFromRequest($option.'.searches.filter_order', 'filter_order', 'ordering');
		$filter_order_Dir = $mainframe->getUserStateFromRequest($option.'.searches.filter_order_Dir', 'filter_order_Dir', 'ASC');
		
		$this->setState($option.'.searches.filter_order', $filter_order);
		$this->setState($option.'.searches.filter_order_Dir', $filter_order_Dir);
		
		$this->_query = $this->_buildQuery();
	}
	
	function _buildQuery()
	{
		$mainframe =& JFactory::getApplication();

		$user = JFactory::getUser();
		
		$query = "SELECT * FROM #__rsticketspro_searches WHERE user_id='".$user->get('id')."' ";
		
		$sortColumn = $this->_db->getEscaped($this->getSortColumn());
		$sortOrder = $this->_db->getEscaped($this->getSortOrder());
		
		$query .= " ORDER BY `".$sortColumn."` ".$sortOrder;
		
		return $query;
	}
	
	function _getSearch()
	{		
		$this->_row =& JTable::getInstance('RSTicketsPro_Searches','Table');
		
		$cid = JRequest::getInt('cid', 0);
		if ($cid)
		{
			$this->_row->load($cid);
		
			$user = JFactory::getUser();
			if ($user->get('id') != $this->_row->user_id)
			{
				$mainframe =& JFactory::getApplication();
				JError::raiseWarning(500, JText::_('RST_STAFF_CANNOT_VIEW_SEARCHES'));
				$mainframe->redirect(RSTicketsProHelper::route('index.php?option=com_rsticketspro&view=searches', false));
			}
		}
	}
	
	function getSearch()
	{
		return $this->_row;
	}
	
	function getSearches()
	{
		$option = 'com_rsticketspro';
		
		if (empty($this->_data))
			$this->_data = $this->_getList($this->_query, $this->getState($option.'.searches.limitstart'), $this->getState($option.'.searches.limit'));
		
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
			$this->_pagination = new JPagination($this->getTotal(), $this->getState($option.'.searches.limitstart'), $this->getState($option.'.searches.limit'));
		}
		
		return $this->_pagination;
	}
	
	function getSortColumn()
	{
		$option = 'com_rsticketspro';
		return $this->getState($option.'.searches.filter_order', 'ordering');
	}
	
	function getSortOrder()
	{
		$option = 'com_rsticketspro';
		return $this->getState($option.'.searches.filter_order_Dir', 'ASC');
	}
	
	function save()
	{
		$row =& JTable::getInstance('RSTicketsPro_Searches','Table');
		$post = JRequest::get('post');
		$post['params'] = '';
		
		$mainframe =& JFactory::getApplication();
		$option    = 'com_rsticketspro';
		
		$search = array();
		$search['filter_word'] = $mainframe->getUserStateFromRequest($option.'.ticketsfilter.filter_word', 'filter_word', '', 'word');
		$search['customer'] = $mainframe->getUserStateFromRequest($option.'.ticketsfilter.customer', 'customer', '', 'word');
		$search['staff'] = $mainframe->getUserStateFromRequest($option.'.ticketsfilter.staff', 'staff', '', 'word');
		$search['department_id'] = $mainframe->getUserStateFromRequest($option.'.ticketsfilter.department_id', 'department_id', array(0), 'array');
		$search['priority_id'] = $mainframe->getUserStateFromRequest($option.'.ticketsfilter.priority_id', 'priority_id', array(0), 'array');
		$search['status_id'] = $mainframe->getUserStateFromRequest($option.'.ticketsfilter.status_id', 'status_id', array(0), 'array');
		$search['flagged'] = $mainframe->getUserStateFromRequest($option.'.ticketsfilter.flagged', 'flagged', 0, 'int');
		
		$post['params'] = base64_encode(serialize($search));
		$user = JFactory::getUser();
		$post['user_id'] = $user->get('id');
		
		if (!$row->bind($post))
			return JError::raiseWarning(500, $row->getError());
		
		if (empty($row->id))
			$row->ordering = $row->getNextOrder("`user_id`='".(int) $user->get('id')."'");
		
		if (!empty($row->id) && empty($post['update_search']))
			unset($row->params);
		
		if ($row->store())
		{
			if ($row->default)
			{
				$user = JFactory::getUser();
				$this->_db->setQuery("UPDATE #__rsticketspro_searches SET default=0 WHERE user_id='".$user->get('id')."' AND id != '".$row->id."'");
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
	
	function remove()
	{
		$mainframe =& JFactory::getApplication();
		
		$cid = JRequest::getInt('cid');
		
		$this->_db->setQuery("DELETE FROM #__rsticketspro_searches WHERE id='".$cid."' LIMIT 1");
		$this->_db->query();
	}
}
?>