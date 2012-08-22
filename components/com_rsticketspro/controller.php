<?php
/**
* @version 2.0.0
* @package RSTickets! Pro 2.0.0
* @copyright (C) 2010 www.rsjoomla.com
* @license GPL, http://www.gnu.org/licenses/gpl-2.0.html
*/

defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.controller');

class RSTicketsProController extends JController
{
	var $_db;
	
	function __construct()
	{
		parent::__construct();
		
		$this->registerTask('applykbtemplate', 'savekbtemplate');
		$this->registerTask('applykbconvert', 'savekbconvert');
		
		$lang 		=& JFactory::getLanguage();
		$mainframe  =& JFactory::getApplication();
		$document   =& JFactory::getDocument();
		
		$lang->load('com_rsticketspro', JPATH_SITE, 'en-GB', true);
		$lang->load('com_rsticketspro', JPATH_SITE, $lang->getDefault(), true);
		$lang->load('com_rsticketspro', JPATH_SITE, null, true);
		if ($mainframe->isAdmin())
		{
			$lang->load('com_rsticketspro', JPATH_ADMINISTRATOR, 'en-GB', true);
			$lang->load('com_rsticketspro', JPATH_ADMINISTRATOR, $lang->getDefault(), true);
			$lang->load('com_rsticketspro', JPATH_ADMINISTRATOR, null, true);
		}
		
		if ($mainframe->isAdmin() && RSTicketsProHelper::isJ16())
			JHTML::_('behavior.framework');
		
		// Add the css stylesheets
		$document->addStyleSheet(JURI::root(true).'/components/com_rsticketspro/assets/css/rsticketspro.css');
		if (RSTicketsProHelper::isJ16())
			$document->addStyleSheet(JURI::root(true).'/components/com_rsticketspro/assets/css/rsticketspro16.css');
		$document->addStyleSheet(JURI::root(true).'/components/com_rsticketspro/assets/css/rsticketspro-print.css', 'text/css', 'print');
		
		if ($mainframe->isSite())
			$document->addStyleSheet(JURI::root(true).'/components/com_rsticketspro/assets/css/newstyle.css');
			
		if ($mainframe->isAdmin())
		{
			$document->addStyleSheet(JURI::root(true).'/administrator/components/com_rsticketspro/assets/css/rsticketspro.css');
			if (RSTicketsProHelper::isJ16())
				$document->addStyleSheet(JURI::root(true).'/administrator/components/com_rsticketspro/assets/css/rsticketspro16.css');
		}
		// Add the js
		$document->addScript(JURI::root(true).'/components/com_rsticketspro/assets/js/rsticketspro.js');
		
		// Set the database object
		$this->_db =& JFactory::getDBO();
		
		RSTicketsProHelper::readConfig(true);
		
		if (!RSTicketsProHelper::getConfig('css_inherit'))
			$document->addStyleSheet(JURI::root(true).'/components/com_rsticketspro/assets/css/designs/'.RSTicketsProHelper::getConfig('css_design'));
	}
	
	function fixAdminMenus()
	{
		$mainframe =& JFactory::getApplication();
		if ($mainframe->isSite()) return;
		
		$db =& JFactory::getDBO();
		
		$db->setQuery("SELECT id FROM #__menu WHERE `id`='1'");
		if (!$db->loadResult())
		{
			$db->setQuery("INSERT IGNORE INTO `#__menu` (`id`, `menutype`, `title`, `alias`, `note`, `path`, `link`, `type`, `published`, `parent_id`, `level`, `component_id`, `ordering`, `checked_out`, `checked_out_time`, `browserNav`, `access`, `img`, `template_style_id`, `params`, `lft`, `rgt`, `home`, `language`, `client_id`) VALUES (1, '', 'Menu_Item_Root', 'root', '', '', '', '', 1, 0, 0, 0, 0, 0, '0000-00-00 00:00:00', 0, 0, '', 0, '', 0, 93, 0, '*', 0)");
			$db->query();
		}
		
		$db->setQuery("SELECT extension_id FROM #__extensions WHERE `element`='com_rsticketspro' AND `type`='component'");
		$db->setQuery("UPDATE #__menu SET component_id='".$db->loadResult()."' WHERE id > 1 AND component_id=0 AND `type`='component' AND `link` LIKE 'index.php?option=com_rsticketspro%'");
		$db->query();
		
		$mainframe->redirect('index.php?option=com_rsticketspro', JText::_('RST_FIX_ADMIN_MENUS_ATTEMPTED'));
	}
	
	function cancelAdminSubmit()
	{
		$mainframe =& JFactory::getApplication();
		if ($mainframe->isSite()) return;
		
		$this->setRedirect('index.php?option=com_rsticketspro&view=tickets');
	}
	
	function adminSubmit()
	{
		$mainframe =& JFactory::getApplication();
		if ($mainframe->isSite()) return;
		
		JRequest::setVar('view', 'submit');
		JRequest::setVar('layout', 'default');
		
		parent::display();
	}
	
	function saveKBTemplate()
	{
		$mainframe =& JFactory::getApplication();
		if ($mainframe->isSite()) return;
		
		$kb_template_body = JRequest::getVar('kb_template_body', '', 'post', 'none', JREQUEST_ALLOWRAW);
		$kb_template_ticket_body = JRequest::getVar('kb_template_ticket_body', '', 'post', 'none', JREQUEST_ALLOWRAW);
		
		$this->_db->setQuery("UPDATE #__rsticketspro_configuration SET `value`='".$this->_db->getEscaped($kb_template_body)."' WHERE `name`='kb_template_body'");
		$this->_db->query();
		
		$this->_db->setQuery("UPDATE #__rsticketspro_configuration SET `value`='".$this->_db->getEscaped($kb_template_ticket_body)."' WHERE `name`='kb_template_ticket_body'");
		$this->_db->query();
		
		if ($this->getTask() == 'applykbtemplate')
			$this->setRedirect('index.php?option=com_rsticketspro&view=kbtemplate', JText::_('RST_KB_TEMPLATE_SAVED_OK'));
		else
			$this->setRedirect('index.php?option=com_rsticketspro&view=knowledgebase', JText::_('RST_KB_TEMPLATE_SAVED_OK'));
	}
	
	function cancelKBTemplate()
	{
		$mainframe =& JFactory::getApplication();
		if ($mainframe->isSite()) return;
		
		$this->setRedirect('index.php?option=com_rsticketspro&view=knowledgebase');
	}
	
	function KBConvert()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit('Invalid Token');
		
		$this->_db->setQuery("SELECT id FROM #__rsticketspro_kb_content WHERE from_ticket_id='".JRequest::getInt('cid')."'");
		if ($this->_db->loadResult())
			JError::raiseNotice(500, JText::_('RST_KB_ALREADY_CONVERTED'));
		
		$view =& $this->getView('kbconvert', 'html');
		$model = $this->getModel('ticket');
		$view->setLayout('default');
		$view->setModel($model);
		$view->display();
	}
	
	function automaticKBConvert()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit('Invalid Token');
		
		$cid = JRequest::getInt('cid');
		
		$model = $this->getModel('ticket');
		$ticket = $model->getTicket();
		
		$this->_db->setQuery("SELECT * FROM #__rsticketspro_kb_rules WHERE `published`='1'");
		$rules = $this->_db->loadObjectList();
		foreach ($rules as $rule)
		{
			$rule->conditions = unserialize($rule->conditions);
			if (!empty($rule->conditions))
			{
				foreach ($rule->conditions as $condition)
				{
					switch ($condition->type)
					{
						case 'department':
							if ($condition->condition == 'eq')
								$result[] = $ticket->department_id == $condition->value;
							elseif ($condition->condition == 'neq')
								$result[] = $ticket->department_id != $condition->value;
							elseif ($condition->condition == 'like')
								$result[] = strpos($ticket->department, $condition->value) !== false;
							elseif ($condition->condition == 'notlike')
								$result[] = strpos($ticket->department, $condition->value) === false;
						break;
						
						case 'subject':
							if ($condition->condition == 'eq')
								$result[] = $ticket->subject == $condition->value;
							elseif ($condition->condition == 'neq')
								$result[] = $ticket->subject != $condition->value;
							elseif ($condition->condition == 'like')
								$result[] = strpos($ticket->subject, $condition->value) !== false;
							elseif ($condition->condition == 'notlike')
								$result[] = strpos($ticket->subject, $condition->value) === false;
						break;
						
						case 'priority':
							if ($condition->condition == 'eq')
								$result[] = $ticket->priority_id == $condition->value;
							elseif ($condition->condition == 'neq')
								$result[] = $ticket->priority_id != $condition->value;
							elseif ($condition->condition == 'like')
								$result[] = strpos($ticket->priority, $condition->value) !== false;
							elseif ($condition->condition == 'notlike')
								$result[] = strpos($ticket->priority, $condition->value) === false;
						break;
						
						case 'status':
							if ($condition->condition == 'eq')
								$result[] = $ticket->status_id == $condition->value;
							elseif ($condition->condition == 'neq')
								$result[] = $ticket->status_id != $condition->value;
							elseif ($condition->condition == 'like')
								$result[] = strpos($ticket->status, $condition->value) !== false;
							elseif ($condition->condition == 'notlike')
								$result[] = strpos($ticket->status, $condition->value) === false;
						break;
						
						case 'message':
							if ($condition->condition == 'eq')
							{
								$tmp = false;
								foreach ($ticket->messages as $message)
									if ($message->message == $condition->value)
									{
										$tmp = true;
										break;
									}
								
								$result[] = $tmp;
							}
							elseif ($condition->condition == 'neq')
							{
								$tmp = true;
								foreach ($ticket->messages as $message)
									if ($message->message == $condition->value)
									{
										$tmp = false;
										break;
									}
								
								$result[] = $tmp;
							}
							elseif ($condition->condition == 'like')
							{
								$tmp = false;
								foreach ($ticket->messages as $message)
									if (strpos($message->message, $condition->value) !== false)
									{
										$tmp = true;
										break;
									}
								
								$result[] = $tmp;
							}
							elseif ($condition->condition == 'notlike')
							{
								$tmp = true;
								foreach ($ticket->messages as $message)
									if (strpos($message->message, $condition->value) !== false)
									{
										$tmp = false;
										break;
									}
								
								$result[] = $tmp;
							}
						break;
						
						case 'custom_field':
							$this->_db->setQuery("SELECT cfv.value, cf.type FROM #__rsticketspro_custom_fields_values cfv LEFT JOIN #__rsticketspro_custom_fields cf ON (cf.id=cfv.custom_field_id) WHERE cfv.custom_field_id='".(int) $condition->custom_field."' AND cfv.ticket_id='".$ticket->id."' WHERE cf.published='1'");
							if ($field = $this->_db->loadObject())
							{
								$value = $field->value;
								$types = array('select', 'multipleselect', 'checkbox', 'radio');
								
								if ($condition->condition == 'eq')
								{
									if (in_array($field->type, $types))
										$value = explode("\n", $value);
									
									if (is_array($value))
									{
										$tmp = false;
										foreach ($value as $val)
											if ($val == $condition->value)
												$tmp = true;
										
										$result[] = $tmp;
									}
									else
										$result[] = $value == $condition->value;
								}
								elseif ($condition->condition == 'neq')
								{
									if (in_array($field->type, $types))
										$value = explode("\n", $value);
									
									if (is_array($value))
									{
										$tmp = true;
										foreach ($value as $val)
											if ($val == $condition->value)
												$tmp = false;
										
										$result[] = $tmp;
									}
									else
										$result[] = $value != $condition->value;
								}
								elseif ($condition->condition == 'like')
								{									
									$result[] = strpos($value, $condition->value) !== false;
								}
								elseif ($condition->condition == 'notlike')
								{
									$result[] = strpos($value, $condition->value) === false;
								}
							}
							else
								$result[] = false;
						break;
					}
				}
				$toEval = '$result = (';
				foreach ($rule->conditions as $i => $condition)
				{
					$value = (int) $result[$i];
					$toEval .= $value.($i < count($rule->conditions)-1 ? ' '.$condition->connector.' ' : '');
				}
				$toEval .= ');';
				eval($toEval);
				// Found rule
				if ($result)
				{
					$params = array(
						'name' => $ticket->subject,
						'category_id' => $rule->category_id,
						'publish_article' => $rule->publish_article,
						'private' => $rule->private
					);
					$success = RSTicketsProHelper::convertTicket($ticket, $params);
					if ($success)
						return $this->setRedirect('index.php?option=com_rsticketspro&view=ticket&cid='.$cid, JText::sprintf('RST_KB_ARTICLE_SAVED_OK_AUTOMATIC', $rule->name));
				}
			}
		}
		
		JError::raiseNotice(500, JText::_('RST_KB_NO_RULE'));
		$this->setRedirect('index.php?option=com_rsticketspro&view=ticket&cid='.$cid);
	}
	
	function saveKBConvert()
	{
		$mainframe =& JFactory::getApplication();
		if ($mainframe->isSite()) return;
		
		$model = $this->getModel('ticket');
		$ticket = $model->getTicket();
		$cid = JRequest::getInt('cid');
		
		$post = JRequest::get('post');
		$success = RSTicketsProHelper::convertTicket($ticket, $post);
		
		if ($success)
			$this->setRedirect('index.php?option=com_rsticketspro&view=ticket&cid='.$cid, JText::_('RST_KB_ARTICLE_SAVED_OK'));
		else
		{
			JError::raiseWarning(500, JText::_('RST_KB_ARTICLE_SAVED_ERROR'));
			$this->setRedirect('index.php?option=com_rsticketspro&view=ticket&cid='.$cid);
		}
	}
	
	function cancelKBConvert()
	{
		$mainframe =& JFactory::getApplication();
		if ($mainframe->isSite()) return;
		
		$cid = JRequest::getInt('cid');
		
		$this->setRedirect('index.php?option=com_rsticketspro&view=ticket&cid='.$cid);
	}
	
	/**
	 * Display the view
	 */
	function display()
	{
		parent::display();
	}
	
	function resetsearch()
	{
		$mainframe =& JFactory::getApplication();
		$option    = 'com_rsticketspro';
		
		$session = JFactory::getSession();
		$session->clear($option.'.ticketsfilter.rsticketspro_search');
		
		$mainframe->setUserState($option.'.ticketsfilter.rsticketspro_search', null);
		$mainframe->setUserState($option.'.ticketsfilter.filter_word', null);
		$mainframe->setUserState($option.'.ticketsfilter.customer', null);
		$mainframe->setUserState($option.'.ticketsfilter.staff', null);
		$mainframe->setUserState($option.'.ticketsfilter.department_id', null);
		$mainframe->setUserState($option.'.ticketsfilter.priority_id', null);
		$mainframe->setUserState($option.'.ticketsfilter.status_id', null);
		$mainframe->setUserState($option.'.ticketsfilter.predefined_search', null);
		$mainframe->setUserState($option.'.ticketsfilter.flagged', 0);
		
		if ($mainframe->isAdmin())
			$return = RSTicketsProHelper::route('index.php?option=com_rsticketspro&view=tickets', false);
		else
			$return = RSTicketsProHelper::route('index.php?option=com_rsticketspro&view=rsticketspro', false);
		
		$mainframe->redirect($return);
	}
	
	function captcha()
	{	
		$captcha_enabled = RSTicketsProHelper::getConfig('captcha_enabled');
		if (!$captcha_enabled)
			return false;
			
		ob_end_clean();
		
		if ($captcha_enabled == 1)
		{
			$captcha = new JSecurImage();
			
			$captcha_lines = RSTicketsProHelper::getConfig('captcha_lines');
			if ($captcha_lines)
				$captcha->num_lines = 8;
			else
				$captcha->num_lines = 0;
			
			$captcha_characters = RSTicketsProHelper::getConfig('captcha_characters');
			$captcha->code_length = $captcha_characters;
			$captcha->image_width = 30*$captcha_characters + 50;
			$captcha->show();
		}
		
		die();
	}
	
	function download()
	{
		// the ticket model already checks for the correct set of permissions
		$model = $this->getModel('ticket');
		
		$mainframe =& JFactory::getApplication();
		
		$file_id = JRequest::getInt('file_id');
		$ticket_id = JRequest::getInt('cid');
		
		$this->_db->setQuery("SELECT * FROM #__rsticketspro_ticket_files WHERE ticket_id='".$ticket_id."' AND id='".$file_id."' LIMIT 1");
		$file = $this->_db->loadObject();
		if (empty($file))
		{
			JError::raiseWarning(500, JText::_('RST_CANNOT_DOWNLOAD_FILE'));
			$mainframe->redirect(RSTicketsProHelper::route('index.php?option=com_rsticketspro&view=rsticketspro', false));
		}
		
		$hash = md5($file->id.' '.$file->ticket_message_id);
		$path = RST_UPLOAD_FOLDER.DS.$hash;
		if (!file_exists($path))
		{
			JError::raiseWarning(500, JText::_('RST_CANNOT_DOWNLOAD_FILE_NOT_EXIST'));
			$mainframe->redirect(RSTicketsProHelper::route('index.php?option=com_rsticketspro&view=ticket&cid='.$ticket_id, false));
		}
		
		$this->_db->setQuery("UPDATE #__rsticketspro_ticket_files SET downloads = downloads+1 WHERE id='".$file->id."' LIMIT 1");
		$this->_db->query();
		
		@ob_end_clean();
		$filename = $file->filename;
		header("Cache-Control: public, must-revalidate");
		header('Cache-Control: pre-check=0, post-check=0, max-age=0');
		if (strstr(@$_SERVER["HTTP_USER_AGENT"],"MSIE")==false) {
			header("Cache-Control: no-cache");
			header("Pragma: no-cache");
		}
		header("Expires: 0"); 
		header("Content-Description: File Transfer");
		header("Expires: Sat, 01 Jan 2000 01:00:00 GMT");
		header("Content-Type: application/octet-stream; charset=utf-8");
		header("Content-Length: ".(string) filesize($path));
		header('Content-Disposition: attachment; filename="'.$filename.'"');
		header("Content-Transfer-Encoding: binary\n");
		@readfile($path);
		
		die();
	}
	
	function viewinline()
	{
		// the ticket model already checks for the correct set of permissions
		$model = $this->getModel('ticket');
		
		$mainframe =& JFactory::getApplication();
		
		$filename  = JRequest::getVar('filename');
		$ticket_id = JRequest::getInt('cid');
		
		$this->_db->setQuery("SELECT * FROM #__rsticketspro_ticket_files WHERE ticket_id='".$ticket_id."' AND filename='".$this->_db->getEscaped($filename)."' LIMIT 1");
		$file = $this->_db->loadObject();
		if (empty($file))
		{
			JError::raiseWarning(500, JText::_('RST_CANNOT_DOWNLOAD_FILE'));
			$mainframe->redirect(RSTicketsProHelper::route('index.php?option=com_rsticketspro&view=rsticketspro', false));
		}
		
		$hash = md5($file->id.' '.$file->ticket_message_id);
		$path = RST_UPLOAD_FOLDER.DS.$hash;
		if (!file_exists($path))
		{
			JError::raiseWarning(500, JText::_('RST_CANNOT_DOWNLOAD_FILE_NOT_EXIST'));
			$mainframe->redirect(RSTicketsProHelper::route('index.php?option=com_rsticketspro&view=ticket&cid='.$ticket_id, false));
		}
		
		jimport('joomla.filesystem.file');
		jimport('joomla.environment.browser');
		
		$browser   = &JBrowser::getInstance();
		$extension = strtolower(JFile::getExt($file->filename));
		$images    = array('jpg', 'jpeg', 'gif', 'png');
		if (in_array($extension, $images))
		{
			if ($browser->getBrowser() == 'konqueror' && $extension == 'jpg')
				$extension = 'jpeg';
			
			if ($browser->getBrowser() != 'msie') 
				header('Content-Type: image/'.$extension);
		}
		
		@ob_end_clean();
		$filename = $file->filename;
		header("Cache-Control: public, must-revalidate");
		header('Cache-Control: pre-check=0, post-check=0, max-age=0');
		if ($browser->getBrowser() != 'msie') {
			header("Cache-Control: no-cache");
			header("Pragma: no-cache");
		}
		header("Expires: 0"); 
		header("Content-Description: File Transfer");
		header("Expires: Sat, 01 Jan 2000 01:00:00 GMT");
		header("Content-Length: ".(string) filesize($path));
		header('Content-Disposition: inline; filename="'.$filename.'"');
		header("Content-Transfer-Encoding: binary\n");
		@readfile($path);
		
		die();
	}
	
	function savesignature()
	{
		$model = $this->getModel('signature');
		$model->save();
	}
	
	function deletemessage()
	{
		$mainframe =& JFactory::getApplication();
		
		$user = JFactory::getUser();
		if ($user->get('guest'))
		{
			$link = JRequest::getURI();
			$link = base64_encode($link);
			$user_option = RSTicketsProHelper::isJ16() ? 'com_users' : 'com_user';
			$mainframe->redirect(RSTicketsProHelper::route('index.php?option='.$user_option.'&view=login&return='.$link, false));
		}
		
		$is_staff = RSTicketsProHelper::isStaff();
		if (!$is_staff)
		{
			JError::raiseWarning(500, JText::_('RST_CANNOT_DELETE_TICKET_MESSAGE'));
			$mainframe->redirect(RSTicketsProHelper::route('index.php?option=com_rsticketspro&view=rsticketspro', false));
		}
		
		$permissions = RSTicketsProHelper::getCurrentPermissions();
		$cid = JRequest::getInt('cid');
		$message =& JTable::getInstance('RSTicketsPro_Ticket_Messages','Table');
		$message->load($cid);
		
		// can update his own replies
		if (!$permissions->delete_ticket_replies && $message->user_id == $user->get('id'))
		{
			JError::raiseWarning(500, JText::_('RST_CANNOT_DELETE_TICKET_MESSAGE'));
			$mainframe->redirect(RSTicketsProHelper::route('index.php?option=com_rsticketspro&view=rsticketspro', false));
		}
		
		// can update customer replies
		$is_customer = !RSTicketsProHelper::isStaff($message->user_id);
		if (!$permissions->delete_ticket_replies_customers && $is_customer)
		{
			JError::raiseWarning(500, JText::_('RST_CANNOT_DELETE_TICKET_MESSAGE'));
			$mainframe->redirect(RSTicketsProHelper::route('index.php?option=com_rsticketspro&view=rsticketspro', false));
		}
	
		// can update staff replies
		$is_other_staff = !$is_customer && $message->user_id != $user->get('id');
		if (!$permissions->delete_ticket_replies_staff && $is_other_staff)
		{
			JError::raiseWarning(500, JText::_('RST_CANNOT_DELETE_TICKET_MESSAGE'));
			$mainframe->redirect(RSTicketsProHelper::route('index.php?option=com_rsticketspro&view=rsticketspro', false));
		}
	
		$this->_db->setQuery("DELETE FROM #__rsticketspro_ticket_messages WHERE id='".$cid."' LIMIT 1");
		$this->_db->query();
		
		$this->_db->setQuery("UPDATE #__rsticketspro_tickets SET `replies`=`replies`-1 WHERE `id`='".$message->ticket_id."'");
		$this->_db->query();
	
		$this->_db->setQuery("SELECT `replies` FROM #__rsticketspro_tickets WHERE `id`='".$message->ticket_id."'");
		if ($this->_db->loadResult() < 0)
		{
			$this->_db->setQuery("UPDATE #__rsticketspro_tickets SET `replies`=0 WHERE `id`='".$message->ticket_id."'");
			$this->_db->query();
		}
	
		$ticket_id = $message->ticket_id;
		
		$mainframe->redirect(RSTicketsProHelper::route('index.php?option=com_rsticketspro&view=ticket&cid='.$ticket_id, false), JText::_('RST_DELETE_TICKET_MESSAGE_OK'));
	}
	
	function savemessage()
	{
		$mainframe =& JFactory::getApplication();
		
		$model = $this->getModel('ticketmessage');
		$model->save();
		
		$cid = JRequest::getInt('cid');
		
		$mainframe->redirect(RSTicketsProHelper::route('index.php?option=com_rsticketspro&view=ticketmessage&cid='.$cid.'&tmpl=component', false), JText::_('RST_UPDATE_TICKET_MESSAGE_OK'));
	}
	
	function updateTickets()
	{
		$mainframe =& JFactory::getApplication();
		$server = JRequest::get('server');
		$referer = $server['HTTP_REFERER'];
		
		if (!RSTicketsProHelper::isStaff())
		{
			JError::raiseWarning(500, JText::_('RST_CANNOT_UPDATE_TICKETS'));
			$mainframe->redirect($referer);
		}
		
		$msg = JText::_('RST_TICKETS_UPDATED_OK');
		
		$staff_id = JRequest::getVar('bulk_staff_id', -1);
		JRequest::setVar('staff_id', $staff_id);
		
		$priority_id = JRequest::getInt('bulk_priority_id', 0);
		JRequest::setVar('priority_id', $priority_id);
		
		$status_id = JRequest::getInt('bulk_status_id', 0);
		JRequest::setVar('status_id', $status_id);
		
		$bulk_notify = JRequest::getInt('bulk_notify', 0);
		
		$bulk_delete = JRequest::getInt('bulk_delete', 0);
		if ($bulk_delete)
			$msg = JText::_('RST_TICKETS_DELETED_OK');
		
		$cid = JRequest::getVar('cid');
		JArrayHelper::toInteger($cid);
		foreach ($cid as $ticket_id)
		{
			JRequest::setVar('cid', $ticket_id);
			$model = $this->getModel('ticket');
			
			if ($bulk_delete)
			{
				$model->_deleteTicket();
				continue;
			}
			
			$model->_saveBulkInfo();
			$model->_notifyTicket();
		}
		
		$mainframe->redirect($referer, $msg);
	}
	
	function KBSearch()
	{
		header('Content-type: text/html; charset=utf-8');
		$value = JRequest::getVar('filter');
		if (!$value)
			die();
			
		$db = JFactory::getDBO();
		$escvalue = $db->getEscaped($value);
		$escvalue = str_replace(' ','%',$escvalue);
		
		$db->setQuery("SELECT * FROM #__rsticketspro_kb_content WHERE (name LIKE '%".$escvalue."%' OR text LIKE '%".$escvalue."%') AND published=1 ORDER BY category_id, ordering LIMIT 5");
		$results = $db->loadObjectList();
		
		$db->setQuery("SELECT COUNT(id) FROM #__rsticketspro_kb_content WHERE (name LIKE '%".$escvalue."%' OR text LIKE '%".$escvalue."%') AND published=1");
		$total = $db->loadResult();
		$showing = $total < 5 ? $total : 5;
		?>
		<div id="rst_livesearch_total"><?php echo JText::sprintf('RST_KB_RESULTS', $total, $showing, $total); ?> <a href="javascript: void(0);" onclick="return rst_close_search();">[<?php echo JText::_('RST_CLOSE'); ?>]</a></div>
		<ul id="rst_livesearch_ul">
		<?php
		foreach ($results as $i => $result)
		{
			$name = str_replace($value, '<strong>'.$value.'</strong>', $result->name);
			$text = RSTicketsProHelper::shorten(str_replace($value, '<strong>'.$value.'</strong>', strip_tags($result->text)));
			?>
			<li>
			<a href="javascript: void(0);" onclick="rst_update_editor(document.getElementById('rst_result_<?php echo $i; ?>').innerHTML); return rst_close_search();"><?php echo $name; ?></a>
			<p><?php echo $text; ?></p>
			<div id="rst_result_<?php echo $i; ?>" style="display: none;"><?php echo $result->text; ?></div>
			</li>
			<?php
		}
		echo '</ul>';
		die();
	}
	
	function dashboardSearch()
	{
		header('Content-type: text/html; charset=utf-8');
		
		JRequest::setVar('view',   'dashboard');
		JRequest::setVar('layout', 'ajax');
		JRequest::setVar('format', 'raw');
		parent::display();
	}
	
	function saveRegistration()
	{
		$mainframe =& JFactory::getApplication();
		if (!$mainframe->isAdmin())
		{
			$this->setRedirect('index.php?option=com_rsticketspro');
			return;
		}
		
		$code = JRequest::getVar('global_register_code');
		$code = $this->_db->getEscaped($code);
		if (!empty($code))
		{
			$this->_db->setQuery("UPDATE #__rsticketspro_configuration SET `value`='".$code."' WHERE `name`='global_register_code'");
			$this->_db->query();
			$this->setRedirect('index.php?option=com_rsticketspro&view=updates', JText::_('RST_LICENSE_SAVED'));
		}
		else
			$this->setRedirect('index.php?option=com_rsticketspro&view=configuration');
	}
}
?>