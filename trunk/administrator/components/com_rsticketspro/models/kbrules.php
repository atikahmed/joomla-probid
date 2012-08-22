<?php
/**
* @version 2.0.0
* @package RSTickets! Pro 2.0.0
* @copyright (C) 2010 www.rsjoomla.com
* @license GPL, http://www.gnu.org/licenses/gpl-2.0.html
*/

defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.model');

class RSTicketsProModelKBRules extends JModel
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
		$limit = $mainframe->getUserStateFromRequest($option.'.kbrules.limit', 'limit', $mainframe->getCfg('list_limit'), 'int');
		$limitstart = $mainframe->getUserStateFromRequest($option.'.kbrules.limitstart', 'limitstart', 0, 'int');

		// In case limit has been changed, adjust it
		$limitstart = ($limit != 0 ? (floor($limitstart / $limit) * $limit) : 0);

		$this->setState($option.'.kbrules.limit', $limit);
		$this->setState($option.'.kbrules.limitstart', $limitstart);
	}
	
	function _buildQuery()
	{
		$mainframe =& JFactory::getApplication();

		$query = "SELECT c.name AS category, r.* FROM #__rsticketspro_kb_rules r LEFT JOIN #__rsticketspro_kb_categories c ON (r.category_id=c.id) WHERE 1";

		$filter_word = JRequest::getVar('search', '');
		if (!empty($filter_word))
			$query .= " AND r.`name` LIKE '%".$this->_db->getEscaped($filter_word)."%'";
		
		$filter_state = $mainframe->getUserStateFromRequest('rsticketspro.rules.filter_state', 'filter_state');
		if ($filter_state != '')
			$query .= " AND r.`published`='".($filter_state == 'U' ? '0' : '1')."'";
		
		$sortColumn = JRequest::getVar('filter_order', 'category, r.name');
		$sortColumn = $this->_db->getEscaped($sortColumn);
		
		$sortOrder = JRequest::getVar('filter_order_Dir', 'ASC');
		$sortOrder = $this->_db->getEscaped($sortOrder);
		
		$query .= " ORDER BY ".$sortColumn." ".$sortOrder;
		
		return $query;
	}
	
	function getKBRules()
	{
		$option = 'com_rsticketspro';
		
		if (empty($this->_data))
			$this->_data = $this->_getList($this->_query, $this->getState($option.'.kbrules.limitstart'), $this->getState($option.'.kbrules.limit'));
		
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
			$this->_pagination = new JPagination($this->getTotal(), $this->getState($option.'.kbrules.limitstart'), $this->getState($option.'.kbrules.limit'));
		}
		
		return $this->_pagination;
	}
	
	function getKBRule()
	{
		$cid = JRequest::getInt('cid', 0);
		
		$row =& JTable::getInstance('RSTicketsPro_KB_Rules','Table');
		$row->load($cid);
		
		$row->conditions = unserialize($row->conditions);
		
		return $row;
	}
	
	function publish($cid=array(), $publish=1)
	{
		if (!is_array($cid) || count($cid) > 0)
		{
			$publish = (int) $publish;
			$cids = implode(',', $cid);

			$query = "UPDATE #__rsticketspro_kb_rules SET `published`='".$publish."' WHERE `id` IN (".$cids.")";
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

		$query = "DELETE FROM #__rsticketspro_kb_rules WHERE `id` IN (".$cids.")";
		$this->_db->setQuery($query);
		$this->_db->query();
		
		return true;
	}
	
	function save()
	{
		$row =& JTable::getInstance('RSTicketsPro_KB_Rules','Table');
		$post = JRequest::get('post');
		
		$j = 0;
		foreach ($post['select_type'] as $i => $type)
		{
			$condition = new stdClass();
			$condition->type = $type;
			$condition->condition = $post['select_condition'][$i];
			if ($type == 'custom_field')
			{
				$condition->custom_field = $post['select_custom_field_value'][$j];
				$j++;
			}
			$condition->value = $post['select_value'][$i];
			$condition->connector = $post['select_connector'][$i];
			
			$post['conditions'][] = $condition;
		}
		$post['conditions'] = serialize($post['conditions']);
		
		if (!$row->bind($post))
			return JError::raiseWarning(500, $row->getError());
		
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
	
	function getDepartments()
	{
		$results = $this->_getList("SELECT id, name FROM #__rsticketspro_departments ORDER BY `ordering`");
		foreach ($results as $i => $result)
			$results[$i]->name = JText::_($result->name);
		
		return $results;
	}
	
	function getPriorities()
	{
		$results = $this->_getList("SELECT id, name FROM #__rsticketspro_priorities ORDER BY `ordering`");
		foreach ($results as $i => $result)
			$results[$i]->name = JText::_($result->name);
		
		return $results;
	}
	
	function getStatuses()
	{
		$results = $this->_getList("SELECT id, name FROM #__rsticketspro_statuses ORDER BY `ordering`");
		foreach ($results as $i => $result)
			$results[$i]->name = JText::_($result->name);
		
		return $results;
	}
	
	function getCustomFields()
	{
		$results = $this->_getList("SELECT `id`, `department_id`, `name`, `type`, `values` FROM #__rsticketspro_custom_fields ORDER BY `department_id`, `ordering`");
		return $results;
	}
	
	function getCustomFieldValues()
	{
		$return = array();
		$cfid = JRequest::getInt('cfid');
		
		$this->_db->setQuery("SELECT `values` FROM #__rsticketspro_custom_fields WHERE id='".$cfid."'");
		if ($values = $this->_db->loadResult())
		{
			$values = str_replace("\r\n", "\n", $values);
			$values = explode("\n", $values);
			foreach ($values as $value)
			{
				$tmp = new stdClass();
				$tmp->id = $tmp->name = $value;
				
				$return[] = $tmp;
			}
		}
		
		return $return;
	}
}
?>