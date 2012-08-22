<?php
/**
* @version 2.0.0
* @package RSTickets! Pro 2.0.0
* @copyright (C) 2010 www.rsjoomla.com
* @license GPL, http://www.gnu.org/licenses/gpl-2.0.html
*/

defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.model');

class RSTicketsProModelDashboard extends JModel
{
	var $_data = null;
	var $_total = 0;
	var $_pagination = null;
	var $_db = null;
	
	var $is_staff;
	
	function __construct()
	{
		parent::__construct();
		
		$mainframe 		=& JFactory::getApplication();
		
		$this->_db 		=& JFactory::getDBO();
		$this->is_staff = RSTicketsProHelper::isStaff();
		$this->params 	= $mainframe->getParams('com_rsticketspro');
	}
	
	function getCategories()
	{
		$query = "SELECT * FROM #__rsticketspro_kb_categories c WHERE c.parent_id='0' AND c.`published`='1'";
		
		if (!$this->is_staff)
			$query .= " AND c.`private`='0'";
		
		$query .= " ORDER BY c.`ordering` ASC";
		
		return $this->_getList($query);
	}
	
	function _implodeTickets($results)
	{
		$tmp = array();
		foreach ($results as $result)
		{
			if (!$this->is_staff && $result->last_reply_customer)
				continue;
			
			$tmp[] = @$result->id;
		}
		
		if (count($tmp))
			return implode(',', $tmp);
		
		return false;
	}
	
	function getTickets()
	{
		$user 	 =& JFactory::getUser();
		$user_id = (int) $user->get('id');
		
		$where = "";
		if ($this->is_staff)
			$where .= " AND t.`staff_id`='".$user_id."'";
		else
			$where .= " AND t.`customer_id`='".$user_id."'";
		
		$tickets = $this->_getList("SELECT t.id, t.subject, t.last_reply_customer, s.name AS status_name FROM #__rsticketspro_tickets t LEFT JOIN #__rsticketspro_statuses s ON (t.status_id=s.id) WHERE 1 $where ORDER BY `last_reply` DESC", 0, $this->params->get('tickets_limit', 3));
		
		if ($tickets && $ticket_ids = $this->_implodeTickets($tickets))
		{
			$messages = $this->_getList("SELECT m.ticket_id, m.message FROM #__rsticketspro_ticket_messages m WHERE m.user_id !='".$user_id."' AND m.ticket_id IN (".$ticket_ids.") ORDER BY m.date DESC");
			
			foreach ($tickets as $i => $ticket)
				foreach ($messages as $message)
				{
					if ($ticket->id == $message->ticket_id)
					{
						$tickets[$i]->message = $message->message;
						break 2;
					}
				}
		}
		
		return $tickets;
	}
	
	function getSearchResults()
	{
		if (!$value = JRequest::getVar('filter'))
			return array();
		
		$escvalue = $this->_db->getEscaped($value);
		$escvalue = str_replace('%','\%',$escvalue);
		$escvalue = str_replace(' ','%',$escvalue);
		
		$is_staff = RSTicketsProHelper::isStaff();
		
		if (!$is_staff)
			$this->_db->setQuery("SELECT id FROM #__rsticketspro_kb_categories c WHERE c.private='0' AND c.published='1'");
		else
			$this->_db->setQuery("SELECT id FROM #__rsticketspro_kb_categories c WHERE c.published='1'");
		$cat_ids = $this->_db->loadResultArray();
		
		$results = $this->_getList("SELECT c.* FROM #__rsticketspro_kb_content c LEFT JOIN #__rsticketspro_kb_categories cat ON (c.category_id=cat.id) WHERE (c.name LIKE '%".$escvalue."%' OR c.text LIKE '%".$escvalue."%') ".($is_staff ? "" : " AND c.`private`='0'")." AND c.published=1 ".($cat_ids ? " AND c.category_id IN (".implode(",", $cat_ids).")" : "")." ORDER BY cat.ordering, c.ordering LIMIT 5");
		
		if ($results)
			$this->_highlight($results, $value);
		
		return $results;
	}
	
	function _highlight(&$results, $word)
	{
		$words = explode(' ', $word);
		
		foreach ($results as $i => $result)
		{
			$result->text = strip_tags($result->text);
			$result->text = RSTicketsProHelper::shorten($result->text);
			foreach ($words as $word)
			{
				$pattern = '#'.preg_quote($word).'#i';
				$result->text = preg_replace($pattern, '<b class="rsticketspro_highlight">\0</b>', $result->text);
			}
			$results[$i] = $result;
		}
	}
}
?>