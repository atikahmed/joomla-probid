<?php
/**
* @version 2.0.0
* @package RSTickets! Pro 2.0.0
* @copyright (C) 2010 www.rsjoomla.com
* @license GPL, http://www.gnu.org/licenses/gpl-2.0.html
*/

defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.model');

class RSTicketsProModelSubmit extends JModel
{
	var $_data = null;
	var $_db;
	
	var $recaptcha_error = null;
	
	function __construct()
	{
		parent::__construct();
		
		$mainframe =& JFactory::getApplication();
		
		$this->_db = JFactory::getDBO();
		
		$user = JFactory::getUser();
		if ($mainframe->isSite() && !RSTicketsProHelper::getConfig('rsticketspro_add_tickets') && $user->get('guest'))
		{
			$Itemid = JRequest::getInt('Itemid');
			$Itemid = $Itemid ? '&Itemid='.$Itemid : '';
			JError::raiseWarning(500, JText::_('RST_CANNOT_SUBMIT_TICKET'));
			$user_option = RSTicketsProHelper::isJ16() ? 'com_users' : 'com_user';
			$link = base64_encode(RSTicketsProHelper::route('index.php?option=com_rsticketspro&view=submit'.$Itemid, false));
			$mainframe->redirect(RSTicketsProHelper::route('index.php?option='.$user_option.'&view=login&return='.$link, false));
		}
		
		if (RSTicketsProHelper::isStaff())
		{
			$permissions = RSTicketsProHelper::getCurrentPermissions();
			if (!$permissions->add_ticket && !$permissions->add_ticket_customers && !$permissions->add_ticket_staff)
			{
				JError::raiseWarning(500, JText::_('RST_STAFF_CANNOT_SUBMIT_TICKET'));
				$mainframe->redirect(RSTicketsProHelper::route('index.php?option=com_rsticketspro&view=rsticketspro', false));
			}
		}
		
		$this->_setData();
		$this->_processData();
	}
	
	function _setData()
	{
		$mainframe =& JFactory::getApplication();
		
		if ($mainframe->isSite())
			$params = $mainframe->getParams('com_rsticketspro');
		else
			$params = new JObject();
		
		$post = JRequest::get('post');
		
		if (RSTicketsProHelper::getConfig('allow_rich_editor'))
			$post['message'] = JRequest::getVar('message', '', 'post', 'none', JREQUEST_ALLOWHTML);
		else
			$post['message'] = JRequest::getVar('message', '', 'post', 'none', JREQUEST_ALLOWRAW);
			
		$post['department_id'] = JRequest::getInt('department_id', $params->get('department_id', 0));
		if (!empty($post['department_id']) && empty($post['priority_id']))
		{
			$this->_db->setQuery("SELECT priority_id FROM #__rsticketspro_departments WHERE id='".(int) $post['department_id']."'");
			$post['priority_id'] = $this->_db->loadResult();
		}
		
		$this->_data = $post;
	}
	
	function getData()
	{
		return $this->_data;
	}
	
	function _processData()
	{
		// don't process anything if the form hasn't been submitted
		if (empty($this->_data['task']) || $this->_data['task'] != 'submit') return;
		
		$mainframe =& JFactory::getApplication();
		
		// get the customer (ticket submitter) information
		$user = JFactory::getUser();
		if (($mainframe->isSite() && $user->get('guest')) || ($mainframe->isAdmin() && $this->_data['submit_type'] == 1))
		{
			jimport('joomla.mail.helper');
			
			if (empty($this->_data['email']) || !JMailHelper::isEmailAddress($this->_data['email']))
			{
				JError::raiseNotice(500, JText::_('RST_TICKET_EMAIL_ERROR'));
				return;
			}
			
			$this->_db->setQuery("SELECT id FROM #__users WHERE email LIKE '".$this->_db->getEscaped($this->_data['email'])."'");
			$user_id = $this->_db->loadResult();
			
			if ($user_id && RSTicketsProHelper::isStaff($user_id))
			{
				JError::raiseNotice(500, JText::sprintf('RST_TICKET_EMAIL_STAFF_ERROR', $this->_data['email']));
				return;
			}
			
			$this->_data['customer_id'] = 0;
			
			if (empty($this->_data['name']))
			{
				JError::raiseNotice(500, JText::_('RST_TICKET_NAME_ERROR'));
				return;
			}
		}
		else
		{
			$this->_data['email'] = $user->get('email');
			$this->_data['name'] = $user->get('name');
			$this->_data['customer_id'] = $user->get('id');
			
			if (RSTicketsProHelper::isStaff())
			{
				$permissions = RSTicketsProHelper::getCurrentPermissions();
				if (!$permissions->add_ticket && !$permissions->add_ticket_customers && !$permissions->add_ticket_staff)
				{
					JError::raiseWarning(500, JText::_('RST_STAFF_CANNOT_SUBMIT_TICKET'));
					$mainframe->redirect(RSTicketsProHelper::route('index.php?option=com_rsticketspro&view=rsticketspro', false));
				}
				elseif ($permissions->add_ticket_customers || $permissions->add_ticket_staff)
				{
					$this->_data['email'] = '';
					$this->_data['name'] = '';
					$this->_data['customer_id'] = 0;
					
					$customer_id = JRequest::getInt('customer_id', 0, 'post');
					if (($mainframe->isSite() && !$customer_id) || ($mainframe->isAdmin() && $this->_data['submit_type'] == 2 && !$customer_id))
					{
						JError::raiseNotice(500, JText::_('RST_TICKET_CUSTOMER_ERROR'));
						return;
					}
					
					$customer = JFactory::getUser($customer_id);
					$this->_data['email'] = $customer->get('email');
					$this->_data['name'] = $customer->get('name');
					$this->_data['customer_id'] = $customer->get('id');
				}
			}
		}
		
		// must select a department
		if (empty($this->_data['department_id']))
		{
			JError::raiseNotice(500, JText::_('RST_TICKET_DEPARTMENT_ERROR'));
			return;
		}
		
		// get all custom fields
		$custom_fields = $this->_getList("SELECT * FROM #__rsticketspro_custom_fields WHERE department_id='".(int) $this->_data['department_id']."' AND published=1 ORDER BY ordering");
		// get the submitted custom fields
		$sent_custom_fields = JRequest::getVar('rst_custom_fields', array(), 'post');
		$sent_custom_fields = @$sent_custom_fields['department_'.$this->_data['department_id']];
		
		// add the custom fields to an array so that we can send them as a parameter later on
		$correct_custom_fields = array();
		foreach ($custom_fields as $field)
		{
			if ($field->type == 'freetext') continue;
			if ($field->required)
			{
				$validation_message = JText::_($field->validation);
				if (empty($validation_message))
					$validation_message = JText::sprintf('RST_VALIDATION_DEFAULT_ERROR', JText::_($field->label));
				
				if (empty($sent_custom_fields[$field->name]))
				{
					JError::raiseNotice(500, $validation_message);
					return false;
				}
				elseif (is_array($sent_custom_fields[$field->name]) && empty($sent_custom_fields[$field->name][0]))
				{
					JError::raiseNotice(500, $validation_message);
					return false;
				}
			}
			
			if (!empty($sent_custom_fields[$field->name]))
				$correct_custom_fields[$field->id] = $sent_custom_fields[$field->name];
		}
		
		// must write a subject
		if (empty($this->_data['subject']))
		{
			JError::raiseNotice(500, JText::_('RST_TICKET_SUBJECT_ERROR'));
			return;
		}
		
		// must write a message
		if (empty($this->_data['message']))
		{
			JError::raiseNotice(500, JText::_('RST_TICKET_MESSAGE_ERROR'));
			return;
		}
		
		// must select a priority
		if (empty($this->_data['priority_id']))
		{
			JError::raiseNotice(500, JText::_('RST_TICKET_PRIORITY_ERROR'));
			return;
		}
		
		if ($mainframe->isSite())
		{
			$captcha_enabled = RSTicketsProHelper::getConfig('captcha_enabled');
			$use_captcha = $this->getUseCaptcha();
			if ($use_captcha && $captcha_enabled)
			{
				if ($captcha_enabled == 1)
				{
					$captcha_image = new JSecurImage();
					$valid = $captcha_image->check($this->_data['captcha']);
					if (!$valid)
					{
						JError::raiseNotice(500, JText::_('RST_TICKET_CAPTCHA_ERROR'));
						return;
					}
				}
				elseif ($captcha_enabled == 2)
				{
					$privatekey = RSTicketsProHelper::getConfig('recaptcha_private_key');
					
					$response = JReCAPTCHA::checkAnswer($privatekey, @$_SERVER['REMOTE_ADDR'], @$this->_data['recaptcha_challenge_field'], @$this->_data['recaptcha_response_field']);
					if ($response === false || !$response->is_valid)
					{
						$this->recaptcha_error = @$response->error;
						JError::raiseNotice(500, JText::_('RST_TICKET_CAPTCHA_ERROR'));
						return;
					}
				}
			}
		}
		
		$this->_data['agent'] = @$_SERVER['HTTP_USER_AGENT'];
		$this->_data['referer'] = @$_SERVER['HTTP_REFERER'];
		$this->_data['ip'] = @$_SERVER['REMOTE_ADDR'];
		
		$correct_files = array();
		if ($this->getCanUpload())
		{
			$department =& JTable::getInstance('RSTicketsPro_Departments','Table');
			$department->load($this->_data['department_id']);
			$upload_extensions = str_replace("\r\n", "\n", $department->upload_extensions);
			$upload_extensions = explode("\n", $upload_extensions);
		
			$files = JRequest::get('files');
			$files = @$files['rst_files'];
			
			if (is_array($files))
				foreach ($files['tmp_name'] as $i => $file_tmp)
				{
					if ($files['error'][$i] == 4) continue;
					
					switch ($files['error'][$i])
					{
						default: $msg = 'RST_TICKET_UPLOAD_ERROR'; break;
						case 1: $msg = 'RST_TICKET_UPLOAD_ERROR_INI_SIZE'; break;
						case 2: $msg = 'RST_TICKET_UPLOAD_ERROR_FORM_SIZE'; break;
						case 3: $msg = 'RST_TICKET_UPLOAD_ERROR_PARTIAL'; break;
						case 6: $msg = 'RST_TICKET_UPLOAD_ERROR_NO_TMP_DIR'; break;
						case 7: $msg = 'RST_TICKET_UPLOAD_ERROR_CANT_WRITE'; break;
						case 8: $msg = 'RST_TICKET_UPLOAD_ERROR_PHP_EXTENSION'; break;
					}
					
					$file_name = $files['name'][$i];
					if ($files['error'][$i])
					{
						JError::raiseWarning(500, JText::sprintf($msg, $file_name));
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
		
		RSTicketsProHelper::addTicket($this->_data, $correct_custom_fields, $correct_files);

		$redirect = RSTicketsProHelper::getConfig('submit_redirect');
		 if ($redirect && $mainframe->isSite())
			$mainframe->redirect($redirect);
		else
			$mainframe->redirect(RSTicketsProHelper::route('index.php?option=com_rsticketspro&view=submit', false), JText::_('RST_TICKET_SUBMIT_OK'));
	}
	
	function getCustomFields()
	{
		$return = array();
		
		$post = JRequest::getVar('rst_custom_fields', array(), 'post');
		if (!empty($this->_data['department_id']))
			$post = @$post['department_'.$this->_data['department_id']];
			
		$fields = $this->_getList("SELECT * FROM #__rsticketspro_custom_fields WHERE published='1' ORDER BY department_id, ordering");
		foreach ($fields as $field)
			$return[$field->department_id][] = RSTicketsProHelper::showCustomField($field, (isset($this->_data['department_id']) && $this->_data['department_id'] == $field->department_id ? $post : array()), true, $field->department_id);
		
		return $return;
	}
	
	function getDepartments()
	{
		$departments = $this->_getList("SELECT id, priority_id, upload, upload_extensions, upload_files, predefined_subjects FROM #__rsticketspro_departments WHERE published='1' ORDER BY ordering");
		foreach ($departments as $i => $department)
		{
			$upload_extensions = $department->upload_extensions;
			$upload_extensions = str_replace("\r\n", "\n", $upload_extensions);
			$upload_extensions = str_replace("\n", ", ", $upload_extensions);
			if (trim($upload_extensions) == '')
				$upload_extensions = '*';
			$departments[$i]->upload_extensions = $upload_extensions;
		}
		
		return $departments;
	}
	
	function getCanUpload()
	{
		if (empty($this->_data['department_id'])) return true;
		
		$this->_db->setQuery("SELECT upload FROM #__rsticketspro_departments WHERE id='".(int) $this->_data['department_id']."'");
		$upload = $this->_db->loadResult();
		
		$user = JFactory::getUser();
			
		if ($upload == 0)
			return false;
		elseif ($upload == 1)
			return true;
		elseif ($upload == 2 && $user->get('guest'))
			return false;
		
		return true;
	}
	
	function getUseCaptcha()
	{
		$captcha_enabled = RSTicketsProHelper::getConfig('captcha_enabled');
		if (!$captcha_enabled) return false;
		
		$captcha_enabled_for = RSTicketsProHelper::getConfig('captcha_enabled_for');
		$captcha_enabled_for = explode(',', $captcha_enabled_for);
		$user = JFactory::getUser();
		
		$enabled_for_unregistered = $captcha_enabled_for[0];
		$enabled_for_customers = $captcha_enabled_for[1];
		$enabled_for_staff = $captcha_enabled_for[2];
		
		$is_logged = !$user->get('guest');
		$is_staff = RSTicketsProHelper::isStaff();
		
		if (!$is_logged && $enabled_for_unregistered)
			return true;
		
		if ($is_logged && !$is_staff && $enabled_for_customers)
			return true;
		
		if ($is_logged && $is_staff && $enabled_for_staff)
			return true;
		
		return false;
	}
	
	function getUseBuiltin()
	{
		$captcha_enabled = RSTicketsProHelper::getConfig('captcha_enabled');
		return $captcha_enabled == 1;
	}
	
	function getUseRecaptcha()
	{
		$captcha_enabled = RSTicketsProHelper::getConfig('captcha_enabled');
		return $captcha_enabled == 2;
	}
	
	function getRecaptchaError()
	{
		return $this->recaptcha_error;
	}
}
?>