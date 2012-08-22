<?php
/**
* @version 2.0.0
* @package RSTickets! Pro 2.0.0
* @copyright (C) 2010 www.rsjoomla.com
* @license GPL, http://www.gnu.org/licenses/gpl-2.0.html
*/

defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.model');

class RSTicketsProModelTicket extends JModel
{
	var $_data = null;
	var $_total = 0;
	var $_query = '';
	var $_pagination = null;
	var $_db = null;
	
	var $_permissions = null;
	var $_ticket;
	var $is_staff;
	
	var $updates;
	
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
		
		$this->_getIsStaff();
		if ($this->is_staff)
		{
			$this->_getPermissions();
			$departments = RSTicketsProHelper::getCurrentDepartments();
		}
		
		$this->_getTicket();
		
		if (!$this->is_staff && $this->_ticket->customer_id != $user->get('id'))
		{
			JError::raiseWarning(500, JText::_('RST_CUSTOMER_CANNOT_VIEW_TICKET'));
			$mainframe->redirect(RSTicketsProHelper::route('index.php?option=com_rsticketspro&view=rsticketspro', false));
		}
		
		if ($this->is_staff)
		{
			// staff - check if belongs to department only if he is not the customer
			if ($this->_ticket->customer_id != $user->get('id') && !in_array($this->_ticket->department_id, $departments))
			{
				JError::raiseWarning(500, JText::_('RST_STAFF_CANNOT_VIEW_TICKET'));
				$mainframe->redirect(RSTicketsProHelper::route('index.php?option=com_rsticketspro&view=rsticketspro', false));
			}
			
			if (RSTicketsProHelper::getConfig('staff_force_departments') && !in_array($this->_ticket->department_id, $departments))
			{
				JError::raiseWarning(500, JText::_('RST_STAFF_CANNOT_VIEW_TICKET'));
				$mainframe->redirect(RSTicketsProHelper::route('index.php?option=com_rsticketspro&view=rsticketspro', false));
			}
		
			if (!$this->_permissions->see_unassigned_tickets && $this->_ticket->staff_id == 0)
			{
				JError::raiseWarning(500, JText::_('RST_STAFF_CANNOT_VIEW_TICKET'));
				$mainframe->redirect(RSTicketsProHelper::route('index.php?option=com_rsticketspro&view=rsticketspro', false));
			}
			if (!$this->_permissions->see_other_tickets && $this->_ticket->staff_id > 0 && $this->_ticket->staff_id != $user->get('id'))
			{
				JError::raiseWarning(500, JText::_('RST_STAFF_CANNOT_VIEW_TICKET'));
				$mainframe->redirect(RSTicketsProHelper::route('index.php?option=com_rsticketspro&view=rsticketspro', false));
			}
		}
		
		if ($mainframe->isSite())
		{
			$pathway =& $mainframe->getPathway();
			$pathway->addItem('['.$this->_ticket->code.'] '.$this->_ticket->subject, '');
		}
		
		$document =& JFactory::getDocument();
		$document->setTitle('['.$this->_ticket->code.'] '.$this->_ticket->subject, '');
		
		$this->_setData();
		$this->_processData();
	}
	
	function _setData()
	{
		$post = JRequest::get('post');
		
		if (RSTicketsProHelper::getConfig('allow_rich_editor'))
			$post['message'] = JRequest::getVar('message', '', 'post', 'none', JREQUEST_ALLOWHTML);
		else
			$post['message'] = JRequest::getVar('message', '', 'post', 'none', JREQUEST_ALLOWRAW);
			
		$this->_data = $post;
	}
	
	function _processData()
	{
		// don't process anything if the form hasn't been submitted
		if (empty($this->_data['task'])) return;
		
		if ($this->_data['task'] == 'submitreply')
			return $this->_submitReply();
		elseif ($this->_data['task'] == 'saveticketinfo')
			return $this->_saveTicketInfo();
		elseif ($this->_data['task'] == 'savecustomfields')
			return $this->_saveCustomFields();
		elseif ($this->_data['task'] == 'savetickettime')
			return $this->_saveTicketTime();
	}
	
	function _submitReply()
	{
		// can add replies
		if ($this->is_staff && !$this->_permissions->answer_ticket)
		{
			JError::raiseNotice(500, JText::_('RST_CANNOT_UPDATE_TICKET'));
			return;
		}
		
		// must not be closed
		if ($this->_ticket->status_id == 2)
		{
			JError::raiseWarning(500, JText::_('RST_TICKET_REPLIES_CLOSED_ERROR'));
			return;
		}
		
		// must write a message
		if (empty($this->_data['message']))
		{
			JError::raiseNotice(500, JText::_('RST_TICKET_MESSAGE_ERROR'));
			return;
		}
		
		$correct_files = array();
		if ($this->getCanUpload())
		{
			$department =& JTable::getInstance('RSTicketsPro_Departments','Table');
			$department->load($this->_ticket->department_id);
			$upload_extensions = str_replace("\r\n", "\n", $department->upload_extensions);
			$upload_extensions = explode("\n", $upload_extensions);
		
			$files = JRequest::get('files');
			$files = @$files['rst_files'];
			
			if (is_array($files))
				foreach ($files['tmp_name'] as $i => $file_tmp)
				{
					if ($files['error'][$i] == 4) continue;
					
					$file_name = $files['name'][$i];
					if ($files['error'][$i])
					{
						JError::raiseWarning(500, JText::sprintf('RST_TICKET_UPLOAD_ERROR', $file_name));
						return;
					}
					if (!RSTicketsProHelper::isAllowedExtension(RSTicketsProHelper::getExtension($file_name), $upload_extensions))
					{
						$upload_extensions = implode(', ', $upload_extensions);
						JError::raiseNotice(500, JText::sprintf('RST_TICKET_UPLOAD_EXTENSION_ERROR', $file_name, $upload_extensions));
						return;
					}
					if ($department->upload_size > 0 && $files['size'][$i] > $department->upload_size*1048576)
					{
						JError::raiseWarning(500, JText::sprintf('RST_TICKET_UPLOAD_SIZE_ERROR', $file_name, $department->upload_size));
						return;
					}
					
					$correct_files[] = array('src' => 'upload', 'tmp_name' => $file_tmp, 'name' => $file_name);
				}
		}
		
		// add the ticket id
		$this->_data['ticket_id'] = $this->_ticket->id;
		// add the current user
		$user = JFactory::getUser();
		$this->_data['user_id'] = $user->get('id');
		
		RSTicketsProHelper::addTicketReply($this->_data, $correct_files);
		
		$mainframe =& JFactory::getApplication();
		$mainframe->redirect(RSTicketsProHelper::route('index.php?option=com_rsticketspro&view=ticket&cid='.$this->_ticket->id.':'.JFilterOutput::stringURLSafe($this->_ticket->subject), false), JText::_('RST_TICKET_SUBMIT_REPLY_OK'));
	}
	
	function _saveBulkInfo()
	{
		if (!$this->is_staff)
			return;
		
		$this->_saveStaff();
		$this->_savePriority();
		$this->_saveStatus();
		
		$this->_runUpdate();
	}
	
	function _saveTicketInfo()
	{
		if (!$this->is_staff)
			return;
		
		$this->updates = array();
		
		$this->_saveSubject();
		$this->_saveDepartment();
		$this->_savePriority();
		$this->_saveStaff();
		$this->_saveCustomer();
		$this->_saveStatus();
		
		$this->_runUpdate();
		
		$mainframe =& JFactory::getApplication();
		$mainframe->redirect(RSTicketsProHelper::route('index.php?option=com_rsticketspro&view=ticket&cid='.$this->_ticket->id.':'.JFilterOutput::stringURLSafe($this->_ticket->subject), false), JText::_('RST_TICKET_UPDATED_OK'));
	}
	
	function _saveTicketTime()
	{
		if (!$this->is_staff || !RSTicketsProHelper::getConfig('enable_time_spent'))
			return;
		
		$this->_db->setQuery("UPDATE #__rsticketspro_tickets SET `time_spent`='".$this->_db->getEscaped($this->_data['time_spent'])."' WHERE id='".$this->_ticket->id."' LIMIT 1");
		$this->_db->query();
		
		$mainframe =& JFactory::getApplication();
		$mainframe->redirect(RSTicketsProHelper::route('index.php?option=com_rsticketspro&view=ticket&cid='.$this->_ticket->id.':'.JFilterOutput::stringURLSafe($this->_ticket->subject), false), JText::_('RST_TIME_SPENT_UPDATED_OK'));
	}
	
	function _saveSubject()
	{
		$subject = $this->_db->getEscaped($this->_data['ticket_subject']);
		if ($this->_permissions->update_ticket)
			$this->updates[] = "`subject`='".$subject."'";
	}
	
	function _saveDepartment()
	{
		$department_id = (int) $this->_data['department_id'];
		
		if (!$this->_permissions->move_ticket || $department_id == $this->_ticket->department_id)
			return;
		
		$this->updates[] = "`department_id`='".$department_id."'";
		
		$department =& JTable::getInstance('RSTicketsPro_Departments','Table');
		$department->load($department_id);
		// random
		if ($department->get('generation_rule') == 1)
		{
			// add the department prefix
			$code = $department->get('prefix') . '-' . strtoupper(RSTicketsProHelper::generateNumber(10));
			$this->_db->setQuery("SELECT id FROM #__rsticketspro_tickets WHERE code='".$code."'");
			while ($this->_db->loadResult())
			{
				// add the department prefix
				$code = $department->get('prefix') . '-' . strtoupper(RSTicketsProHelper::generateNumber(10));
				$this->_db->setQuery("SELECT id FROM #__rsticketspro_tickets WHERE code='".$code."'");
			}
		}
		// sequential
		else
		{
			$code = $department->get('next_number');
			$code = str_pad($code, 10, 0, STR_PAD_LEFT);
			// add the department prefix
			$code = $department->get('prefix') . '-' . $code;
			
			$this->_db->setQuery("UPDATE #__rsticketspro_departments SET `next_number` = `next_number` + 1 WHERE id='".$department_id."' LIMIT 1");
			$this->_db->query();
		}
		
		$this->updates[] = "`code`='".$code."'";
		
		$this->_db->setQuery("SELECT v.custom_field_id, v.value, cf.type, cf.name FROM #__rsticketspro_custom_fields_values v LEFT JOIN #__rsticketspro_custom_fields cf ON (cf.id = v.custom_field_id) WHERE v.ticket_id='".(int) $this->_ticket->id."' AND cf.published='1'");
		$current_fields = $this->_db->loadObjectList();
		
		foreach ($current_fields as $field)
		{
			// check if there's a field that matches
			$this->_db->setQuery("SELECT id FROM #__rsticketspro_custom_fields WHERE department_id='".$department_id."' AND name LIKE '".$this->_db->getEscaped($field->name)."'");
			$found = $this->_db->loadObject();
			if ($found)
			{
				$this->_db->setQuery("SELECT id FROM #__rsticketspro_custom_fields_values WHERE custom_field_id='".$found->id."' AND ticket_id='".$this->_ticket->id."' LIMIT 1");
				$duplicate = $this->_db->loadResult();
				if ($duplicate) continue;
				
				$new_field =& JTable::getInstance('RSTicketsPro_Custom_Fields_Values','Table');
				$new_field->custom_field_id = $found->id;
				$new_field->ticket_id = $this->_ticket->id;
				$new_field->value = $field->value;
				
				$this->_db->setQuery("SELECT id FROM #__rsticketspro_custom_field_values WHERE custom_field_id='".$found->id."' AND ticket_id='".$this->_ticket->id."'");
				if (!$this->_db->loadResult())
					$new_field->store();
			}
		}
	}
	
	function _savePriority()
	{
		$priority_id = (int) $this->_data['priority_id'];
		if ($this->_permissions->update_ticket && $priority_id)
			$this->updates[] = "`priority_id`='".$priority_id."'";
	}
	
	function _saveStaff()
	{
		$staff_id = (int) $this->_data['staff_id'];
		if ($staff_id == -1)
			$is_ok_staff = false;
		else
			$is_ok_staff = $staff_id ? RSTicketsProHelper::isStaff($staff_id) : true;
		if ($this->_permissions->assign_tickets && $is_ok_staff)
		{
			$this->updates[] = "`staff_id`='".$staff_id."'";
			
			// send email to the staff member that gets assigned this ticket
			if ($staff_id > 0 && $this->_ticket->staff_id != $staff_id)
			{
				$department =& JTable::getInstance('RSTicketsPro_Departments','Table');
				$department->load($this->_ticket->department_id);
				if ($department->notify_assign)
				{
					// get email sending settings
					$from = RSTicketsProHelper::getConfig('email_address');
					$fromname = RSTicketsProHelper::getConfig('email_address_fullname');
					// are we using global ?
					if (RSTicketsProHelper::getConfig('email_use_global'))
					{
						$config = new JConfig();
						$from = $config->mailfrom;
						$fromname = $config->fromname;
					}
					if (!$department->get('email_use_global'))
					{
						$from = $department->email_address;
						$fromname = $department->email_address_fullname;
					}
					
					$email = RSTicketsProHelper::getEmail('add_ticket_staff');
					$customer = JFactory::getUser($this->_ticket->customer_id);
					$staff = JFactory::getUser($staff_id);
					
					$messages_direction = RSTicketsProHelper::getConfig('messages_direction');
					$message = $messages_direction == 'ASC' ? reset(@$this->_ticket->messages) : end(@$this->_ticket->messages);
					$message = $message->message;
					$code = $this->_ticket->code;
					
					$custom_fields_email = '';
					$this->_db->setQuery("SELECT v.value, cf.label, cf.type FROM #__rsticketspro_custom_fields_values v LEFT JOIN #__rsticketspro_custom_fields cf ON (cf.id = v.custom_field_id) WHERE v.ticket_id='".$this->_ticket->id."'");
					$custom_fields = $this->_db->loadObjectList();
					foreach ($custom_fields as $custom_field)
					{
						if (in_array($custom_field->type, array('select', 'multipleselect', 'checkbox')))
							$custom_field->value = str_replace("\n", ', ', $custom_field->value);
						
						$custom_fields_email .= '<p>'.JText::_($custom_field->label).': '.$custom_field->value.'</p>';
					}
					
					$replace = array('{live_site}', '{ticket}', '{customer_name}', '{customer_username}', '{customer_email}', '{staff_name}', '{staff_username}', '{staff_email}', '{code}', '{subject}', '{message}', '{custom_fields}');
					$with = array(JURI::root(), RSTicketsProHelper::route(JURI::root().'index.php?option=com_rsticketspro&view=ticket&cid='.$this->_ticket->id.':'.JFilterOutput::stringURLSafe($this->_ticket->subject)), $customer->get('name'), $customer->get('username'), $customer->get('email'), $staff->get('name'), $staff->get('username'), $staff->get('email'), $code, $this->_ticket->subject, $message, $custom_fields_email);
					
					$email_subject = '['.$code.'] '.$this->_ticket->subject;
					$email_message = str_replace($replace, $with, $email->message);
					
					JUtility::sendMail($from, $fromname, $staff->get('email'), $email_subject, $email_message, 1);
				}
			}
		}
	}
	
	function _saveCustomer()
	{
		// must check if we can assign this customer
		$customer_id = (int) $this->_data['customer_id'];
		if ($this->_permissions->add_ticket_customers || $this->_permissions->add_ticket_staff)
			$this->updates[] = "`customer_id`='".$customer_id."'";
	}
	
	function _saveStatus()
	{
		$status_id = (int) $this->_data['status_id'];
		if ($this->_permissions->change_ticket_status && $status_id)
			$this->updates[] = "`status_id`='".$status_id."'";
	}
	
	function _saveCustomFields()
	{
		$mainframe =& JFactory::getApplication();
		
		$url = RSTicketsProHelper::route('index.php?option=com_rsticketspro&view=ticket&cid='.$this->_ticket->id.':'.JFilterOutput::stringURLSafe($this->_ticket->subject), false);
		
		if (!$this->_permissions->update_ticket_custom_fields)
		{
			JError::raiseWarning(500, JText::_('RST_CANNOT_UPDATE_TICKET'));
			$mainframe->redirect($url);
		}
		
		$custom_fields = $this->_data['rst_custom_fields'];
		foreach ($custom_fields as $field_name => $value)
		{
			if (is_array($value))
				$value = implode("\n", $value);
				
			$this->_db->setQuery("SELECT id FROM #__rsticketspro_custom_fields WHERE name='".$this->_db->getEscaped($field_name)."' AND department_id='".$this->_ticket->department_id."' AND published='1' LIMIT 1");
			$field_id = $this->_db->loadResult();
			if (empty($field_id))
				continue;
			
			$this->_db->setQuery("SELECT id FROM #__rsticketspro_custom_fields_values WHERE custom_field_id='".(int) $field_id."' AND ticket_id='".$this->_ticket->id."' LIMIT 1");			
			if (!$this->_db->loadResult())
			{
				$new_field =& JTable::getInstance('RSTicketsPro_Custom_Fields_Values','Table');
				$new_field->custom_field_id = $field_id;
				$new_field->ticket_id = $this->_ticket->id;
				$new_field->value = $value;
				
				$new_field->store();
			}
			else
			{
				$this->_db->setQuery("UPDATE #__rsticketspro_custom_fields_values SET value='".$this->_db->getEscaped($value)."' WHERE custom_field_id='".(int) $field_id."' AND ticket_id='".$this->_ticket->id."' LIMIT 1");
				$this->_db->query();
			}
		}
		
		$mainframe->redirect($url, JText::_('RST_TICKET_UPDATED_OK'));
	}
	
	function _deleteTicket()
	{
		if (!$this->_permissions->delete_ticket) return;
		
		// delete the actual ticket
		$this->_db->setQuery("DELETE FROM #__rsticketspro_tickets WHERE id='".(int) $this->_ticket->id."' LIMIT 1");
		$this->_db->query();
		
		// custom fields
		$this->_db->setQuery("DELETE FROM #__rsticketspro_custom_fields_values WHERE ticket_id='".(int) $this->_ticket->id."'");
		$this->_db->query();
		
		// messages
		$this->_db->setQuery("DELETE FROM #__rsticketspro_ticket_messages WHERE ticket_id='".(int) $this->_ticket->id."'");
		$this->_db->query();
		
		// notes
		$this->_db->setQuery("DELETE FROM #__rsticketspro_ticket_notes WHERE ticket_id='".(int) $this->_ticket->id."'");
		$this->_db->query();
		
		// history
		$this->_db->setQuery("DELETE FROM #__rsticketspro_ticket_history WHERE ticket_id='".(int) $this->_ticket->id."'");
		$this->_db->query();
		
		// files
		$files = $this->_getList("SELECT id, ticket_message_id FROM #__rsticketspro_ticket_files WHERE ticket_id='".(int) $this->_ticket->id."'");
		jimport('joomla.filesystem.file');
		foreach ($files as $file)
		{
			$hash = md5($file->id.' '.$file->ticket_message_id);
			JFile::delete(RST_UPLOAD_FOLDER.DS.$hash);
		}
	}
	
	function _notifyTicket()
	{
		if (!$this->is_staff)
			return;
		
		if (!RSTicketsProHelper::getConfig('autoclose_enabled'))
			return;
		
		if ($this->_ticket->last_reply_customer)
			return;
		
		if ($this->_ticket->autoclose_sent)
			return;
		
		$date = JFactory::getDate();
		$date = $date->toUnix();
		$date = RSTicketsProHelper::getCurrentDate($date);
		
		$this->_db->setQuery("UPDATE #__rsticketspro_tickets SET autoclose_sent='".$date."' WHERE id='".$this->_ticket->id."' LIMIT 1");
		$this->_db->query();
		
		$interval = RSTicketsProHelper::getConfig('autoclose_email_interval') * 86400;
		if ($interval < 86400)
			$interval = 86400;
		
		$last_reply_interval = RSTicketsProHelper::getCurrentDate($this->_ticket->last_reply) + $interval;
		
		if ($last_reply_interval > $date)
			return;
		
		$overdue = floor(($date - $last_reply_interval) / 86400);
		$closed = RSTicketsProHelper::getConfig('autoclose_interval');
		
		// get email sending settings
		$from = RSTicketsProHelper::getConfig('email_address');
		$fromname = RSTicketsProHelper::getConfig('email_address_fullname');
		// are we using global ?
		if (RSTicketsProHelper::getConfig('email_use_global'))
		{
			$config = new JConfig();
			$from = $config->mailfrom;
			$fromname = $config->fromname;
		}
		$department =& JTable::getInstance('RSTicketsPro_Departments','Table');
		$department->load($this->_ticket->department_id);
		if (!$department->get('email_use_global'))
		{
			$from = $department->email_address;
			$fromname = $department->email_address_fullname;
		}
		
		$email = RSTicketsProHelper::getEmail('notification_email');
		$customer = JFactory::getUser($this->_ticket->customer_id);
		$staff = JFactory::getUser($this->_ticket->staff_id);
		$code = $this->_ticket->code;
		
		$replace = array('{live_site}', '{ticket}', '{customer_name}', '{customer_username}', '{customer_email}', '{staff_name}', '{staff_username}', '{staff_email}', '{code}', '{subject}', '{inactive_interval}', '{close_interval}');
		$with = array(JURI::root(), RSTicketsProHelper::route(JURI::root().'index.php?option=com_rsticketspro&view=ticket&cid='.$this->_ticket->id.':'.JFilterOutput::stringURLSafe($this->_ticket->subject)), $customer->get('name'), $customer->get('username'), $customer->get('email'), $staff->get('name'), $staff->get('username'), $staff->get('email'), $code, $this->_ticket->subject, $overdue, $closed);
		
		$email_subject = str_replace($replace, $with, $email->subject);
		$email_message = str_replace($replace, $with, $email->message);
		
		JUtility::sendMail($from, $fromname, $customer->get('email'), $email_subject, $email_message, 1);
	}
	
	function _runUpdate()
	{
		if (!empty($this->updates))
		{
			$this->updates = implode(', ', $this->updates);
			$this->_db->setQuery("UPDATE #__rsticketspro_tickets SET {$this->updates} WHERE id='".$this->_ticket->id."' LIMIT 1");
			$this->_db->query();
		}
	}
	
	function getData()
	{
		return $this->_data;
	}
	
	function _getTicket()
	{
		$cid = JRequest::getInt('cid', 0);
		
		$row =& JTable::getInstance('RSTicketsPro_Tickets','Table');
		$row->load($cid);
		
		$this->_db->setQuery("SELECT name FROM #__rsticketspro_departments WHERE id='".(int) $row->department_id."'");
		$row->department = $this->_db->loadResult();
		
		$this->_db->setQuery("SELECT name FROM #__rsticketspro_statuses WHERE id='".(int) $row->status_id."'");
		$row->status = JText::_($this->_db->loadResult());
		
		$this->_db->setQuery("SELECT name FROM #__rsticketspro_priorities WHERE id='".(int) $row->priority_id."'");
		$row->priority = JText::_($this->_db->loadResult());
		
		$row->staff = JFactory::getUser($row->staff_id);
		$row->customer = JFactory::getUser($row->customer_id);
		
		$what = RSTicketsProHelper::getConfig('show_user_info');
		$what = $this->_db->getEscaped($what);
		$what_sql = "u.".$what." AS user, u.email AS email";
			
		$messages_direction = RSTicketsProHelper::getConfig('messages_direction');
		$messages_direction = $messages_direction == 'ASC' ? 'ASC' : 'DESC';
			
		$row->messages = $this->_getList("SELECT m.*, $what_sql FROM #__rsticketspro_ticket_messages m LEFT JOIN #__users u ON (m.user_id=u.id) WHERE `ticket_id`='".(int) $row->id."' ORDER BY `date` ".$messages_direction);
		
		$row->files = array();
		$files = $this->_getList("SELECT * FROM #__rsticketspro_ticket_files WHERE ticket_id='".$cid."'");
		foreach ($files as $file)
			$row->files[$file->ticket_message_id][] = $file;
		
		$row->custom_field_values = array();
		$row->custom_fields = array();
		
		$this->_db->setQuery("SELECT v.custom_field_id, v.value, cf.type, cf.name FROM #__rsticketspro_custom_fields_values v LEFT JOIN #__rsticketspro_custom_fields cf ON (cf.id = v.custom_field_id) WHERE v.ticket_id='".$row->id."'");
		$custom_field_values = $this->_db->loadObjectList();
		foreach ($custom_field_values as $custom_field_value)
		{
			if (in_array($custom_field_value->type, array('select', 'multipleselect', 'checkbox')))
				$custom_field_value->value = explode("\n", $custom_field_value->value);
				
			$row->custom_field_values[$custom_field_value->name] = $custom_field_value->value;
		}
		
		$do_print = JRequest::getInt('print', 0);
		$editable = false;
		if ($this->is_staff && $this->_permissions->update_ticket_custom_fields && !$do_print)
			$editable = true;
		
		if (JRequest::getVar('task') == 'kbconvert')
			$editable = false;
		
		$this->_db->setQuery("SELECT * FROM #__rsticketspro_custom_fields WHERE `published`='1' AND department_id='".(int) $row->department_id."'  ORDER BY `ordering`");
		$custom_fields = $this->_db->loadObjectList();
		foreach ($custom_fields as $field)
			$row->custom_fields[] = RSTicketsProHelper::showCustomField($field, $row->custom_field_values, $editable);
		
		$this->_db->setQuery("SELECT COUNT(id) FROM #__rsticketspro_ticket_notes WHERE ticket_id='".$row->id."'");
		$row->notes = $this->_db->loadResult();
		
		$this->_ticket = $row;
		unset($row);
	}
	
	function _getPermissions()
	{
		$this->_permissions = RSTicketsProHelper::getCurrentPermissions();
	}
	
	function getPermissions()
	{
		return $this->_permissions;
	}

	function _getIsStaff()
	{
		$this->is_staff = RSTicketsProHelper::isStaff();
	}

	function getTicket()
	{
		return $this->_ticket;
	}

	function getHistoryTickets()
	{
		$customer_id = $this->_ticket->customer_id;
		$ticket_id 	 = $this->_ticket->id;
		
		return $this->_getList("SELECT t.id, t.subject, t.replies, t.code, t.date, s.name AS status_name FROM #__rsticketspro_tickets t LEFT JOIN #__rsticketspro_statuses s ON (t.status_id=s.id) WHERE t.`id` != '".$ticket_id."' AND t.`customer_id`='".$customer_id."' ORDER BY `date` DESC");
	}

	function getCanUpload()
	{		
		$this->_db->setQuery("SELECT upload FROM #__rsticketspro_departments WHERE id='".(int) $this->_ticket->department_id."'");
		$upload = $this->_db->loadResult();
			
		if ($upload == 0)
			return false;
		elseif ($upload == 1 || $upload == 2)
			return true;
		
		return true;
	}

	function setFlag($flagged)
	{
		$this->_db->setQuery("UPDATE #__rsticketspro_tickets SET flagged='".(int) $flagged."' WHERE id='".$this->_ticket->id."' LIMIT 1");
		$this->_db->query();
	}

	function setFeedback($feedback)
	{
		if ($feedback > 5)
			$feedback = 5;
		if ($feedback < 1)
			$feedback = 1;
			
		$this->_db->setQuery("UPDATE #__rsticketspro_tickets SET feedback='".(int) $feedback."' WHERE id='".$this->_ticket->id."' AND feedback='0' LIMIT 1");		
		$this->_db->query();
	}

	function getCanUpdate()
	{
		if ($this->is_staff)
			return ($this->_permissions->update_ticket || $this->_permissions->move_ticket || $this->_permissions->change_ticket_status || $this->_permissions->update_ticket || $this->_permissions->assign_tickets || $this->permissions->add_ticket_customers || $this->permissions->add_ticket_staff);
		
		return false;
	}

	function getCanUpdateCustomFields()
	{
		if ($this->is_staff)
			return $this->_permissions->update_ticket_custom_fields;
		else
			return false;
	}

	function closeTicket()
	{
		$this->_db->setQuery("UPDATE #__rsticketspro_tickets SET status_id='2' WHERE id='".$this->_ticket->id."' LIMIT 1");
		$this->_db->query();
	}

	function reopenTicket()
	{
		$this->_db->setQuery("UPDATE #__rsticketspro_tickets SET status_id='1' WHERE id='".$this->_ticket->id."' LIMIT 1");
		$this->_db->query();
	}

	function addViewingHistory()
	{
		$row =& JTable::getInstance('RSTicketsPro_Ticket_History','Table');
		
		// ticket id
		$row->ticket_id = $this->_ticket->id;
		
		// current user
		$user = JFactory::getUser();
		$row->user_id = $user->get('id');
		
		// ip
		$row->ip = @$_SERVER['REMOTE_ADDR'];
		
		// date
		$date = JFactory::getDate();
		$date = $date->toUnix();
		$row->date = $date;
		
		// store
		$row->store();
	}
	
	function getDepartment()
	{
		$department =& JTable::getInstance('RSTicketsPro_Departments','Table');
		$department->load($this->_ticket->department_id);
		
		return $department;
	}
}
?>