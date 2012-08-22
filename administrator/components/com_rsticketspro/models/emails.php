<?php
/**
* @version 2.0.0
* @package RSTickets! Pro 2.0.0
* @copyright (C) 2010 www.rsjoomla.com
* @license GPL, http://www.gnu.org/licenses/gpl-2.0.html
*/

defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.model');

class RSTicketsProModelEmails extends JModel
{
	var $_data = null;
	var $_query = '';
	var $_db = null;
	var $_id = 0;
	
	var $_language = '';
	
	function __construct()
	{
		parent::__construct();
		
		$this->_language = JRequest::getVar('language', 'en-GB');
		
		$this->_db = JFactory::getDBO();
		$this->_query = $this->_buildQuery();
	}
	
	function save()
	{
		$post = JRequest::get('post');
		
		// These elements are not filtered for HTML code
		$message = JRequest::getVar('message', '', 'post', 'none', JREQUEST_ALLOWRAW);
		$subject = !empty($post['subject']) ? $this->_db->getEscaped($post['subject']) : '';
		
		$language = $this->_db->getEscaped($this->getLanguage());
		$type = $this->_db->getEscaped($this->getType());
		
		$message = $this->_db->getEscaped($message);
		
		$this->_db->setQuery("SELECT `type` FROM #__rsticketspro_emails WHERE `lang`='".$language."' AND `type`='".$type."' LIMIT 1");
		if ($this->_db->loadResult())
			$this->_db->setQuery("UPDATE #__rsticketspro_emails SET `subject`='".$subject."', `message`='".$message."' WHERE `lang`='".$language."' AND `type`='".$type."' LIMIT 1");
		else
			$this->_db->setQuery("INSERT INTO #__rsticketspro_emails SET `subject`='".$subject."', `message`='".$message."', `lang`='".$language."', `type`='".$type."'");
		
		if ($this->_db->query())
			return true;
		else
		{
			JError::raiseWarning(500, $this->_db->getErrorMsg());
			return false;
		}
	}
	
	function _buildQuery()
	{
		$query = "SELECT * FROM #__rsticketspro_emails WHERE `lang`='".$this->_db->getEscaped($this->getLanguage())."' ORDER BY `type` ASC";
		return $query;
	}
	
	function getEmails()
	{
		$option = 'com_rsticketspro';
		
		if (empty($this->_data))
			$this->_data = $this->_getList($this->_query);
		
		$types = array('add_ticket_customer', 'add_ticket_notify', 'add_ticket_reply_customer', 'add_ticket_reply_staff', 'add_ticket_staff', 'notification_email', 'reject_email', 'new_user_email', 'notification_max_replies_nr', 'notification_replies_with_no_response_nr', 'notification_not_allowed_keywords');
		foreach ($types as $type)
		{
			$found = false;
			foreach ($this->_data as $row)
				if ($row->type == $type)
				{
					$found = true;
					break;
				}
			if (!$found)
			{
				$new_row = new stdClass();
				$new_row->lang = $this->getLanguage();
				$new_row->type = $type;
				$new_row->subject = '';
				$new_row->message = '';
				
				$this->_data[] = $new_row;
			}
		}
		return $this->_data;
	}
	
	function getEmail()
	{
		$language = $this->_db->getEscaped($this->getLanguage());
		$type = $this->_db->getEscaped($this->getType());
		
		$this->_db->setQuery("SELECT * FROM #__rsticketspro_emails WHERE `lang`='".$language."' AND `type`='".$type."' LIMIT 1");
		$return = $this->_db->loadObject();
		
		if (!$return)
		{
			$return = new stdClass();
			$return->lang = $this->getLanguage();
			$return->type = $type;
			$return->subject = '';
			$return->message = '';
		}
		
		return $return;
	}
	
	function getLanguages()
	{
		$lang = JFactory::getLanguage();
		return $lang->getKnownLanguages();
	}
	
	function getLanguage()
	{
		return $this->_language;
	}
	
	function getType()
	{
		return JRequest::getVar('type', '');
	}
	
	function getId()
	{
		return $this->_id;
	}
}
?>