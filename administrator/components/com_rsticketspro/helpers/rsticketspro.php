<?php
/**
* @version 2.0.0
* @package RSTickets! Pro 2.0.0
* @copyright (C) 2010 www.rsjoomla.com
* @license GPL, http://www.gnu.org/licenses/gpl-2.0.html
*/

defined('_JEXEC') or die('Restricted access');

define('_RSTICKETSPRO_VERSION', '8');
define('_RSTICKETSPRO_VERSION_LONG', '2.0.0');
define('_RSTICKETSPRO_KEY', '8TIK5J3PRO');
define('_RSTICKETSPRO_PRODUCT', 'RSTickets! Pro');
define('_RSTICKETSPRO_COPYRIGHT', '&copy; 2010-2011 RSJoomla!');
define('_RSTICKETSPRO_LICENSE', 'GPL Commercial License');
define('_RSTICKETSPRO_AUTHOR', '<a href="http://www.rsjoomla.com" target="_blank">www.rsjoomla.com</a>');

if (!defined('RST_UPLOAD_FOLDER'))
	define('RST_UPLOAD_FOLDER', JPATH_SITE.DS.'components'.DS.'com_rsticketspro'.DS.'assets'.DS.'files');

JTable::addIncludePath(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_rsticketspro'.DS.'tables');

class RSTicketsProHelper
{
	function readConfig($force=false)
	{
		static $rsticketspro_config;
		
		if (!is_object($rsticketspro_config) || $force)
		{
			$rsticketspro_config = new stdClass();
			
			$db =& JFactory::getDBO();
			$db->setQuery("SELECT * FROM `#__rsticketspro_configuration`");
			$config = $db->loadObjectList();
			if (!empty($config))
				foreach ($config as $config_item)
					$rsticketspro_config->{$config_item->name} = $config_item->value;
		}
		
		return $rsticketspro_config;
	}
	
	function getConfig($name = null)
	{
		$config = RSTicketsProHelper::readConfig();
		if ($name != null)
		{
			if (isset($config->$name))
				return $config->$name;
			else
				return false;
		}
		else
			return $config;
	}
	
	function genKeyCode()
	{
		$code = RSTicketsProHelper::getConfig('global_register_code');
		if ($code === false)
			$code = '';
		return md5($code._RSTICKETSPRO_KEY);
	}
	
	function isJ16()
	{
		jimport('joomla.version');
		$version = new JVersion();
		return $version->isCompatible('1.6.0');
	}
	
	function isJ25()
	{
		jimport('joomla.version');
		$version = new JVersion();
		return $version->isCompatible('2.5.0');
	}
	
	function getAdminGroups()
	{
		$db =& JFactory::getDBO();
		
		// J! 1.6 only
		if (RSTicketsProHelper::isJ16())
		{			
			$db->setQuery("SELECT id FROM #__usergroups");
			$groups = $db->loadResultArray();
			
			$admin_groups = array();
			foreach ($groups as $group_id)
			{
				if (JAccess::checkGroup($group_id, 'core.login.admin'))
					$admin_groups[] = $group_id;
				elseif (JAccess::checkGroup($group_id, 'core.admin'))
					$admin_groups[] = $group_id;
			}
			
			$admin_groups = array_unique($admin_groups);
			
			return $admin_groups;
		}
	}
	
	function getAdminUsers()
	{
		$db =& JFactory::getDBO();
		
		// J! 1.5
		if (!RSTicketsProHelper::isJ16())
		{
			$db->setQuery("SELECT * FROM #__users WHERE gid > 22 ORDER BY username ASC");
			return $db->loadObjectList();
		}
		// J! 1.6 ACL
		else
		{
			$admin_groups = RSTicketsProHelper::getAdminGroups();
			
			$db->setQuery("SELECT u.* FROM #__user_usergroup_map m RIGHT JOIN #__users u ON (u.id=m.user_id) WHERE m.group_id IN (".implode(',', $admin_groups).") ORDER BY u.username ASC");
			return $db->loadObjectList();
		}
	}
	
	function mailRoute($url, $xhtml=true, $Itemid=0)
	{
		$url .= $Itemid ? '&Itemid='.$Itemid : '';
		
		$mainframe =& JFactory::getApplication();
		if (RSTicketsProHelper::isJ16()) // 1.6, 1.7
			$path = JURI::root(false).$url;
		else // 1.5
			$path = $mainframe->isAdmin() ? JURI::root().JRoute::_($url, $xhtml) : rtrim(JURI::root(false, ''), '/').JRoute::_($url, $xhtml);
		
		return $path;
	}
	
	function route($url, $xhtml=true, $Itemid='')
	{		
		if (!$Itemid && RSTicketsProHelper::getConfig('calculate_itemids'))
			$Itemid = RSTicketsProHelper::_findRoute($url);
		
		if (strpos($url, 'Itemid=') === false)
		{
			if (!$Itemid)
			{
				$Itemid = JRequest::getInt('Itemid');
				if ($Itemid)
					$Itemid = 'Itemid='.$Itemid;
			}
			elseif ($Itemid)
				$Itemid = 'Itemid='.(int) $Itemid;
			
			if ($Itemid)
				$url .= (strpos($url, '?') === false) ? '?'.$Itemid : '&'.$Itemid;
		}
		
		return JRoute::_($url, $xhtml);
	}
	
	function _findRoute($url)
	{
		$app	   = JFactory::getApplication();
		
		if ($app->isAdmin())
			return '';
		
		static $cache;
		
		if (!is_array($cache))
			$cache = array();
		
		$user   = JFactory::getUser();
		$access = RSTicketsProHelper::isJ16() ? 0 : $user->get('aid');
		
		$hash = md5($url);
		if (isset($cache[$access][$hash]))
			return $cache[$access][$hash];
		
		$query = array();
		$url   = str_replace('index.php?', '', $url);
		$parts = explode('&', $url);
		foreach ($parts as $part)
		{
			$part = explode('=', $part, 2);
			$query[$part[0]] = @$part[1];
		}
		
		if (!isset($query['option']))
			return '';
		
		if (isset($query['view']) && $query['view'] == 'ticket')
		{
			$query = array();
			$query['option'] = 'com_rsticketspro';
			$query['view'] = 'rsticketspro';
		}
		
		if (JRequest::getVar('option') == 'com_rsticketspro')
		{
			$count = 0;
			foreach ($query as $var => $value)
			{
				if (JRequest::getVar($var) && JRequest::getVar($var) == $value)
					$count++;
			}
			if ($count == count($query) && JRequest::getInt('Itemid'))
				return JRequest::getInt('Itemid');
		}
		
		$menus	   = $app->getMenu('site');
		$component = JComponentHelper::getComponent($query['option']);
		if (RSTicketsProHelper::isJ16())
			$items = $menus->getItems('component_id', $component->id);
		else
			$items = $menus->getItems('componentid', $component->id);
		
		if ($items)
		foreach ($items as $item)
		{
			if (!isset($item->query))
				continue;
			
			$count = 0;
			foreach ($item->query as $var => $value)
			{
				if (isset($query[$var]) && $value == $query[$var])
					$count++;
			}
			
			if ($count == count($query))
			{
				if (!isset($item->access) || RSTicketsProHelper::isJ16())
					$item->access = 0;
				$cache[$item->access][$hash] = $item->id;
			}
		}
		
		if (isset($cache[$access][$hash]))
			return $cache[$access][$hash];
		
		if (!RSTicketsProHelper::isJ16())
		{
			if ($access > 0)
			{
				$tmp_access = $access;
				while ($tmp_access > 0)
				{
					$tmp_access--;
					if (isset($cache[$tmp_access][$hash]))
						return $cache[$tmp_access][$hash];
				}
			}
			$tmp_access = 0;
			while ($tmp_access <= 2)
			{
				if (isset($cache[$tmp_access][$hash]))
					return $cache[$tmp_access][$hash];
				$tmp_access++;
			}
		}
		
		return '';
	}
	
	function convertTicket($ticket, $params)
	{
		$kb_template_body = RSTicketsProHelper::getConfig('kb_template_body');
		$kb_template_ticket_body = RSTicketsProHelper::getConfig('kb_template_ticket_body');
		$use_editor = RSTicketsProHelper::getConfig('allow_rich_editor');
		$date_format = RSTicketsProHelper::getConfig('date_format');
		$show_email_link = RSTicketsProHelper::getConfig('show_email_link');
		
		// Parse template body
		$replace = array('{ticket_subject}', '{ticket_department}', '{ticket_date}');
		$with = array($ticket->subject, $ticket->department, date($date_format, RSTicketsProHelper::getCurrentDate($ticket->date)));
		$text = str_replace($replace, $with, $kb_template_body);
		
		// Parse ticket message template
		$messages = array();
		foreach ($ticket->messages as $message)
		{
			if (!$use_editor)
				$message->message = nl2br($message->message);
			if ($show_email_link)
				$message->user = '<a href="mailto:'.$message->email.'">'.$message->user.'</a>';
			
			$replace = array('{message_user}', '{message_date}', '{message_text}');
			$with = array($message->user, date($date_format, RSTicketsProHelper::getCurrentDate($message->date)), $message->message);
			$messages[] = str_replace($replace, $with, $kb_template_ticket_body);
		}
		
		$text = str_replace('{ticket_messages}', implode("\n", $messages), $text);
		
		$row =& JTable::getInstance('RSTicketsPro_KB_Content','Table');
		$row->name = $params['name'];
		$row->text = $text;
		$row->category_id = (int) $params['category_id'];
		$row->published = $params['publish_article'];
		$row->private = $params['private'];
		$row->from_ticket_id = (int) $ticket->id;
		$row->ordering = $row->getNextOrder("`category_id`='".$row->category_id."'");
		$date =& JFactory::getDate();
		$row->created = $date->toUnix();
		
		return $row->store();
	}
	
	function getReplyAbove()
	{
		$use_editor = RSTicketsProHelper::getConfig('allow_rich_editor');
		if ($use_editor)
			return '<p>----------'.RSTicketsProHelper::getConfig('reply_above').'----------</p>';
		else
			return '----------'.RSTicketsProHelper::getConfig('reply_above').'----------';
	}
	
	function getPriorities($show_please_select=false)
	{
		$db = JFactory::getDBO();
		$db->setQuery("SELECT * FROM #__rsticketspro_priorities WHERE `published`='1' ORDER BY `ordering` ASC");
		$results = $db->loadObjectList();
		
		$return = array();
		if ($show_please_select)
			$return[] = JHTML::_('select.option', '', JText::_('RST_PLEASE_SELECT_PRIORITY'));
		
		foreach ($results as $result)
			$return[] = JHTML::_('select.option', $result->id, JText::_($result->name));
			
		return $return;
	}
	
	function getSubjects($department_id, $show_please_select=false)
	{
		$department =& JTable::getInstance('RSTicketsPro_Departments','Table');
		$department->load($department_id);
		
		$values = str_replace(array("\r\n", "\r"), "\n", $department->get('predefined_subjects'));
		$values = explode("\n", $values);
		
		$return = array();
		if ($show_please_select)
			$return[] = JHTML::_('select.option', '', JText::_('RST_PLEASE_SELECT_SUBJECT'));
		
		foreach ($values as $value)
			if (!empty($value))
				$return[] = JHTML::_('select.option', $value, JText::_($value));
		
		return $return;
	}
	
	function parseSubjects($subjects)
	{
		$values = str_replace(array("\r\n", "\r"), "\n", $subjects);
		$values = explode("\n", $values);
		
		$return = array();
		$return[] = "'':'".JText::_('RST_PLEASE_SELECT_SUBJECT', true)."'";
		
		foreach ($values as $value)
			if (!empty($value))
				$return[] = "'".addslashes($value)."':'".addslashes(JText::_($value))."'";
		
		return $return;
	}
	
	function getStatuses()
	{
		$db = JFactory::getDBO();
		$db->setQuery("SELECT * FROM #__rsticketspro_statuses WHERE `published`='1' ORDER BY `ordering` ASC");
		$results = $db->loadObjectList();
		
		$return = array();
		
		foreach ($results as $result)
			$return[] = JHTML::_('select.option', $result->id, JText::_($result->name));
			
		return $return;
	}
	
	function getDepartments($show_please_select=false)
	{
		$db = JFactory::getDBO();
		$db->setQuery("SELECT * FROM #__rsticketspro_departments WHERE `published`='1' ORDER BY `ordering` ASC");
		$results = $db->loadObjectList();
		
		$return = array();
		if ($show_please_select)
			$return[] = JHTML::_('select.option', '', JText::_('RST_PLEASE_SELECT_DEPARTMENT'));
		
		$force_departments = RSTicketsProHelper::getConfig('staff_force_departments');
		$is_staff = RSTicketsProHelper::isStaff();
		$departments = RSTicketsProHelper::getCurrentDepartments();
		
		foreach ($results as $result)
		{
			if ($is_staff && $force_departments && !in_array($result->id, $departments))
				continue;
				
			$return[] = JHTML::_('select.option', $result->id, JText::_($result->name));
		}
			
		return $return;
	}
	
	function getStaff($show_please_select=false)
	{
		$db = JFactory::getDBO();
		$what = RSTicketsProHelper::getConfig('show_user_info');
		
		$return = array();
		if ($show_please_select)
			$return[] = JHTML::_('select.option', '', JText::_('RST_PLEASE_SELECT_STAFF'));
		
		$db->setQuery("SELECT * FROM #__rsticketspro_departments WHERE published=1 ORDER BY ordering ASC");
		$departments = $db->loadObjectList();
		foreach ($departments as $department)
		{
			$optgroup = new stdClass();
			$optgroup->value = '<OPTGROUP>';
			$optgroup->text = JText::_($department->name);
			$return[] = $optgroup;
			
			$db->setQuery("SELECT user_id FROM #__rsticketspro_staff_to_department WHERE department_id='".$department->id."'");
			$users = $db->loadResultArray();
			foreach ($users as $user_id)
			{
				$user = JFactory::getUser($user_id);
				$return[] = JHTML::_('select.option', $user->get('id'), $user->get($what));
			}
			
			$optgroup = new stdClass();
			$optgroup->value = '</OPTGROUP>';
			$optgroup->text = '';
			$return[] = $optgroup;
		}
		
		return $return;
	}
	
	function getAvatar($user_id)
	{
		static $avatar_cache = array();
		if (isset($avatar_cache[$user_id]))
			return $avatar_cache[$user_id];
		
		$avatars = RSTicketsProHelper::getConfig('avatars');
		
		$html = '';
		switch ($avatars)
		{
			default:
			$icon = RSTicketsProHelper::isStaff($user_id) ? 'staff' : 'user';
			$html = '<img src="'.JURI::root(true).'/components/com_rsticketspro/assets/images/'.$icon.'-icon.png" alt="" />';
			break;
			
			// Gravatar
			case 'gravatar':
			$user = JFactory::getUser($user_id);
			$email = md5(strtolower(trim($user->get('email'))));
			
			$html .= '<img src="http://www.gravatar.com/avatar/'.$email.'?d='.urlencode(JURI::root().'components/com_rsticketspro/assets/images/user.png').'" alt="Gravatar" class="rst_gravatar rst_avatar" />';
			
			break;
			
			// Community Builder
			case 'comprofiler':
			$db = JFactory::getDBO();
			$db->setQuery("SELECT avatar FROM #__comprofiler WHERE user_id='".(int) $user_id."'");
			$avatar = $db->loadResult();
			if (!$avatar)
				$html .= '<img src="'.JURI::root().'components/com_comprofiler/plugin/templates/default/images/avatar/tnnophoto_n.png" alt="Community Builder Avatar" class="rst_comprofiler rst_avatar" />';
			else
				$html .= '<img src="'.JURI::root().'images/comprofiler/'.$avatar.'" alt="Community Builder Avatar" class="rst_comprofiler rst_avatar" />';
			break;
			
			// JomSocial
			case 'community':
			$db = JFactory::getDBO();
			$db->setQuery("SELECT thumb FROM #__community_users WHERE userid='".(int) $user_id."'");
			$avatar = $db->loadResult();
			
			if (!$avatar)
				$avatar = 'components/com_community/assets/default_thumb.jpg';
				
			$html .= '<img src="'.JURI::root().$avatar.'" alt="JomSocial Avatar" class="rst_community rst_avatar" />';
			break;
			
			// Kunena & Fireboard
			case 'kunena':
			case 'fireboard':
			$db = JFactory::getDBO();
			$db->setQuery("SELECT avatar FROM #__fb_users WHERE userid='".(int) $user_id."'");
			$avatar = $db->loadResult();
			
			if (!$avatar)
				$avatar = 's_nophoto.jpg';
			
			$html .= '<img src="'.JURI::root().'images/fbfiles/avatars/'.$avatar.'" alt="Kunena Avatar" class="rst_kunena rst_avatar" />';
			break;
		}
		
		$avatar_cache[$user_id] = $html;
		
		return $html;
	}
	
	function showCustomField($field, $selected=array(), $editable=true, $department_id=0)
	{
		if (empty($field) || empty($field->type)) return false;
		
		$return = array();
		$return[0] = JText::_($field->label);
		if ($field->required)
			$return[0] .= ' <span class="rst_required">(*)</span>';
		$return[1] = '';
		$return[2] = $field->description;
		
		switch ($field->type)
		{
			// freetext, textbox, textarea, select, multipleselect, checkbox, radio, calendar, calendartime
			case 'freetext':
				$field->values = RSTicketsProHelper::isCode($field->values);
				$return[1] = $field->values;
			break;
			
			case 'textbox':
				if (isset($selected[$field->name]))
					$field->values = $selected[$field->name];
				else
					$field->values = RSTicketsProHelper::isCode($field->values);
				
				$name = 'rst_custom_fields['.$field->name.']';
				if (!empty($department_id))
					$name = 'rst_custom_fields[department_'.$department_id.']['.$field->name.']';
				
				$return[1] = '<input type="text" class="rst_textbox" name="'.$name.'" value="'.RSTicketsProHelper::htmlEscape($field->values).'" '.$field->additional.' />';
				
				if (!$editable)
					$return[1] = RSTicketsProHelper::htmlEscape($field->values);
			break;
			
			case 'textarea':
				if (isset($selected[$field->name]))
					$field->values = $selected[$field->name];
				else
					$field->values = RSTicketsProHelper::isCode($field->values);
				
				$name = 'rst_custom_fields['.$field->name.']';
				if (!empty($department_id))
					$name = 'rst_custom_fields[department_'.$department_id.']['.$field->name.']';
				
				$return[1] = '<textarea class="rst_textarea" name="'.$name.'" '.$field->additional.'>'.RSTicketsProHelper::htmlEscape($field->values).'</textarea>';
				
				if (!$editable)
					$return[1] = nl2br(RSTicketsProHelper::htmlEscape($field->values));
			break;
			
			case 'select':
			case 'multipleselect':
				$field->values = RSTicketsProHelper::isCode($field->values);
				$field->values = str_replace("\r\n", "\n", $field->values);
				$field->values = explode("\n", $field->values);
				
				$multiple = $field->type == 'multipleselect' ? 'multiple="multiple"' : '';
				
				$name = 'rst_custom_fields['.$field->name.'][]';
				if (!empty($department_id))
					$name = 'rst_custom_fields[department_'.$department_id.']['.$field->name.'][]';
				
				$return[1] = '<select '.$multiple.' class="rst_select" name="'.$name.'" '.$field->additional.'>';
					foreach ($field->values as $value)
					{
						$tmp = explode('|', $value, 2);
						if (count($tmp) == 2)
						{
							$value = $tmp[0];
							$text  = $tmp[1];
						}
						else
						{
							$value = $tmp[0];
							$text  = $tmp[0];
						}
						
						$found_checked = false;
						if (preg_match('/\[c\]/',$value) || preg_match('/\[c\]/',$text))
						{
							$value = str_replace('[c]', '', $value);
							$text  = str_replace('[c]', '', $text);
							$found_checked = true;
						}
						
						$checked = '';
						if (isset($selected[$field->name]) && in_array($value, $selected[$field->name]))
							$checked = 'selected="selected"';
						elseif (!isset($selected[$field->name]) && $found_checked)
							$checked = 'selected="selected"';
						
						$return[1] .= '<option '.$checked.' value="'.RSTicketsProHelper::htmlEscape($value).'">'.RSTicketsProHelper::htmlEscape($value).'</option>';
					}
				$return[1] .= '</select>';
				
				if (!$editable)
				{
					$return[1] = '';
					if (isset($selected[$field->name]))
					{
						if (is_array($selected[$field->name]))
							$return[1] = nl2br(RSTicketsProHelper::htmlEscape(implode("\n", $selected[$field->name])));
						else
							$return[1] = RSTicketsProHelper::htmlEscape($selected[$field->name]);
					}
				}
			break;
			
			case 'checkbox':
				$field->values = RSTicketsProHelper::isCode($field->values);
				$field->values = str_replace("\r\n", "\n", $field->values);
				$field->values = explode("\n", $field->values);
				
				foreach ($field->values as $i => $value)
				{
					$tmp = explode('|', $value, 2);
					if (count($tmp) == 2)
					{
						$value = $tmp[0];
						$text  = $tmp[1];
					}
					else
					{
						$value = $tmp[0];
						$text  = $tmp[0];
					}
						
					$found_checked = false;
					if (preg_match('/\[c\]/',$value) || preg_match('/\[c\]/',$text))
					{
						$value = str_replace('[c]', '', $value);
						$text  = str_replace('[c]', '', $text);
						$found_checked = true;
					}
					
					$checked = '';
					if (isset($selected[$field->name]) && in_array($value, $selected[$field->name]))
						$checked = 'checked="checked"';
					elseif (!isset($selected[$field->name]) && $found_checked)
						$checked = 'selected="selected"';
					
					$name = 'rst_custom_fields['.$field->name.'][]';
					if (!empty($department_id))
						$name = 'rst_custom_fields[department_'.$department_id.']['.$field->name.'][]';
					
					$return[1] .= '<input '.$checked.' type="checkbox" name="'.$name.'" value="'.RSTicketsProHelper::htmlEscape($value).'" id="rst_custom_field_'.$field->id.'_'.$i.'" '.$field->additional.' /> <label for="rst_custom_field_'.$field->id.'_'.$i.'">'.$text.'</label>';
				}
				
				if (!$editable)
				{
					$return[1] = '';
					if (isset($selected[$field->name]))
					{
						if (is_array($selected[$field->name]))
							$return[1] = nl2br(RSTicketsProHelper::htmlEscape(implode("\n", $selected[$field->name])));
						else
							$return[1] = RSTicketsProHelper::htmlEscape($selected[$field->name]);
					}
				}
			break;
			
			case 'radio':
				$field->values = RSTicketsProHelper::isCode($field->values);
				$field->values = str_replace("\r\n", "\n", $field->values);
				$field->values = explode("\n", $field->values);
				
				foreach ($field->values as $i => $value)
				{
					$tmp = explode('|', $value, 2);
					if (count($tmp) == 2)
					{
						$value = $tmp[0];
						$text  = $tmp[1];
					}
					else
					{
						$value = $tmp[0];
						$text  = $tmp[0];
					}
					
					$found_checked = false;
					if (preg_match('/\[c\]/',$value) || preg_match('/\[c\]/',$text))
					{
						$value = str_replace('[c]', '', $value);
						$text  = str_replace('[c]', '', $text);
						$found_checked = true;
					}
					
					$checked = '';
					if (isset($selected[$field->name]) && $selected[$field->name] == $value)
						$checked = 'checked="checked"';
					elseif (!isset($selected[$field->name]) && $found_checked)
						$checked = 'checked="checked"';
					
					$name = 'rst_custom_fields['.$field->name.']';
					if (!empty($department_id))
						$name = 'rst_custom_fields[department_'.$department_id.']['.$field->name.']';
					
					$return[1] .= '<input '.$checked.' type="radio" name="'.$name.'" value="'.RSTicketsProHelper::htmlEscape($value).'" id="rst_custom_field_'.$field->id.'_'.$i.'" '.$field->additional.' /> <label for="rst_custom_field_'.$field->id.'_'.$i.'">'.$text.'</label>';
				}
				
				if (!$editable)
					$return[1] = RSTicketsProHelper::htmlEscape(@$selected[$field->name]);
			break;
			
			case 'calendar':
			case 'calendartime':
				if (isset($selected[$field->name]))
					$field->values = $selected[$field->name];
				else
					$field->values = RSTicketsProHelper::isCode($field->values);
					
				$name = 'rst_custom_fields['.$field->name.']';
				if (!empty($department_id))
					$name = 'rst_custom_fields[department_'.$department_id.']['.$field->name.']';
				
				$format = $field->type == 'calendartime' ? RSTicketsProHelper::getConfig('date_format') : RSTicketsProHelper::getConfig('date_format_notime');
				$format = RSTicketsProHelper::getCalendarFormat($format);
				$show_time = $field->type == 'calendartime';
				
				if (!$editable)
					$return[1] = RSTicketsProHelper::htmlEscape($field->values);
				else
					$return[1] = JHTML::_('rsticketsprocalendar.calendar', $show_time, $field->values, $name, 'rst_custom_field_'.$field->id, $format, $field->additional);
			break;
		}
		
		return $return;
	}
	
	function isCode($value)
	{
		if (preg_match('/\/\/<code>/',$value))
			return eval($value);
		else
			return $value;
	}
	
	/**
	 * Returns the internal table object
	 * @string name               The name of the <select>
	 * @int    selected           The id of the selected category
	 * @int    show_only_public   0 - show all categories, 1 - show only the ones who aren't private
	 * @mixed  additional_options Array of JHTML options to add instead of the default first ones
	 */
	function getKBCategoriesTree($name, $selected=0, $show_only_public=0, $additional_options='', $show_all_categories=1)
	{
		$mainframe =& JFactory::getApplication();
		
		$db = JFactory::getDBO();
		$db->setQuery("SELECT * FROM #__rsticketspro_kb_categories WHERE 1 ".(!$mainframe->isAdmin() ? " AND published='1' " : "")." ".($show_only_public ? " AND `private`='0'" : "")." ORDER BY parent_id, ordering ASC");
		$items = $db->loadObjectList();
		
		// establish the hierarchy of the menu
		$children = array();

		if ($items)
			foreach ($items as $item)
			{
				$parent	= $item->parent_id;
				$item->parent = $parent;
				$item->title = '';
				$list 	= @$children[$parent] ? $children[$parent] : array();
				array_push($list, $item);
				$children[$parent] = $list;
			}

		// second pass - get an indent list of the items
		$list = JHTML::_('menu.treerecurse', 0, '', array(), $children, 9999, 0, 0 );

		// assemble menu items to the array
		$items = array();
		if (!empty($additional_options))
		{
			if (is_array($additional_options))
				foreach ($additional_options as $additional_option)
					$items[] = $additional_option;
			else
				$items[] = $additional_options;
		}
		else
		{
			if ($show_all_categories)
				$items[] = JHTML::_('select.option', -1, JText::_('RST_KB_ALL_CATEGORIES'));
			$items[] = JHTML::_('select.option', 0, JText::_('RST_KB_NO_CATEGORY'));
		}

		foreach ($list as $item)
		{
			if (RSTicketsProHelper::isJ16())
			{
				$item->treename = str_replace('&#160;&#160;', '--', $item->treename);
				$items[] = JHTML::_('select.option',  $item->id, $item->treename.$item->name);
			}
			else
				$items[] = JHTML::_('select.option',  $item->id, '&nbsp;&nbsp;&nbsp;'. $item->treename);
		}

		return JHTML::_('select.genericlist', $items, $name, 'class="inputbox"', 'value', 'text', $selected);
	}
	
	function isStaff($user_id=null)
	{
		if (!$user_id)
		{
			$user = JFactory::getUser();
			if ($user->get('guest'))
				return false;
			
			$session = JFactory::getSession();
			return $session->get('rsticketspro.is_staff', false);
		}
		
		$db = JFactory::getDBO();
		$db->setQuery("SELECT id FROM #__rsticketspro_staff WHERE user_id='".(int) $user_id."'");
		if ($db->loadResult())
			return true;
		
		if (RSTicketsProHelper::isAdmin($user_id))
			return true;
		
		return false;
	}
	
	function getCurrentPermissions()
	{
		$user = JFactory::getUser();
		if ($user->get('guest'))
			return array();
		
		$session = JFactory::getSession();
		return $session->get('rsticketspro.permissions', array());
	}
	
	function getCurrentDepartments()
	{
		$user = JFactory::getUser();
		if ($user->get('guest'))
			return array();
		
		$session = JFactory::getSession();
		return $session->get('rsticketspro.departments', array());
	}
	
	function getPermissions($user_id)
	{
		$return = array();
		
		$user = JFactory::getUser($user_id);
		
		$db = JFactory::getDBO();
		$db->setQuery("SELECT group_id FROM #__rsticketspro_staff WHERE user_id='".(int) $user->get('id')."'");
		$group_id = $db->loadResult();
		
		if ($group_id)
		{
			$db->setQuery("SELECT * FROM #__rsticketspro_groups WHERE id='".(int) $group_id."'");
			$return = $db->loadObject();
		}
		elseif (RSTicketsProHelper::isAdmin($user_id))
		{
			$return = JTable::getInstance('RSTicketsPro_Groups','Table');
		}
		
		return $return;
	}
	
	function getSignature($user_id=null)
	{
		if ($user_id)
			$user = JFactory::getUser($user_id);
		else
			$user = JFactory::getUser();
			
		$db = JFactory::getDBO();
		$db->setQuery("SELECT signature FROM #__rsticketspro_staff WHERE user_id='".(int) $user->get('id')."' LIMIT 1");
		
		return $db->loadResult();
	}
	
	function isAdmin($user_id=null)
	{
		if ($user_id)
			$user = JFactory::getUser($user_id);
		else
			$user = JFactory::getUser();
		
		if (RSTicketsProHelper::isJ16())
		{
			$admin_groups = RSTicketsProHelper::getAdminGroups();
			$user_groups = $user->getAuthorisedGroups();
			foreach ($user_groups as $user_group_id)
				if (in_array($user_group_id, $admin_groups))
					return true;
		}
		else
		{
			if ($user->get('gid') == 23 || $user->get('gid') == 25 || $user->get('gid') == 24)
				return true;
		}
		
		return false;
	}
	
	function addTicket($params, $custom_fields=array(), $files=array())
	{
		$db = JFactory::getDBO();
		
		$department_id = (int) $params['department_id'];
		if (!$department_id)
			return false;
		
		// check user
		$logged = 1;
		$customer_id = (int) @$params['customer_id'];
		// create a new user if no user specified
		if (!$customer_id)
		{
			$logged = 0;
			
			$data = array();
			$data['name'] = @$params['name'];
			$customer_id = RSTicketsProHelper::createUser(@$params['email'], $data);
		}
		
		// get the department
		$department =& JTable::getInstance('RSTicketsPro_Departments','Table');
		$department->load($department_id);
		
		// priority
		$priority_id = (int) @$params['priority_id'];
		if (!$priority_id)
			$department->get('priority_id');
		
		// unassigned
		$staff_id = 0;
		// auto-assign to staff member with the least assigned tickets
		if ($department->get('assignment_type') == 1)
		{
			// select staff members that belong to this department
			$db->setQuery("SELECT user_id FROM #__rsticketspro_staff_to_department WHERE department_id='".$department->get('id')."'");
			$staff_ids = $db->loadResultArray();
			
			if (!empty($staff_ids))
			{
				$staff_ids = implode(',', $staff_ids);
				
				// select groups that can answer tickets
				$db->setQuery("SELECT id FROM #__rsticketspro_groups WHERE answer_ticket='1'");
				$group_ids = $db->loadResultArray();
				if (!empty($group_ids))
				{
					$group_ids = implode(',', $group_ids);
					
					$db->setQuery("SELECT user_id FROM #__rsticketspro_staff WHERE group_id IN (".$group_ids.") AND user_id IN (".$staff_ids.") AND priority_id IN (0".($priority_id ? ",".$priority_id : "").")");
					$staff_ids = $db->loadResultArray();
					
					if (!empty($staff_ids))
					{						
						$db->setQuery("SELECT staff_id, COUNT(id) AS tickets FROM #__rsticketspro_tickets WHERE status_id != 2 AND staff_id IN (".implode(',', $staff_ids).") GROUP BY staff_id ORDER BY tickets ASC");
						$results = $db->loadObjectList('staff_id');
						
						// must make sure we cover all staff members, even those who don't have tickets yet
						foreach ($staff_ids as $staff)
						{
							if (!isset($results[$staff]))
							{
								// found a staff member who has 0 tickets - assign
								$staff_id = $staff;
								break;
							}
						}
						
						// no staff member assigned so far - must grab from query the first result
						if (empty($staff_id))
						{
							if ($tmp = reset($results))
								$staff_id = $tmp->staff_id;
						}
						
						// get a random staff id from all the members
						if (empty($staff_id))
						{
							$staff_ids = explode(',', $staff_ids);
							$staff_id = $staff_ids[mt_rand(0, count($staff_ids) - 1)];
						}
					}
				}
			}
			
			if (empty($staff_id))
				$staff_id = 0;
		}
		
		// random
		if ($department->get('generation_rule') == 1)
		{
			// add the department prefix
			$code = $department->get('prefix') . '-' . strtoupper(RSTicketsProHelper::generateNumber(10));
			$db->setQuery("SELECT id FROM #__rsticketspro_tickets WHERE code='".$code."'");
			while ($db->loadResult())
			{
				// add the department prefix
				$code = $department->get('prefix') . '-' . strtoupper(RSTicketsProHelper::generateNumber(10));
				$db->setQuery("SELECT id FROM #__rsticketspro_tickets WHERE code='".$code."'");
			}
		}
		// sequential
		else
		{
			$code = $department->get('next_number');
			$code = str_pad($code, 10, 0, STR_PAD_LEFT);
			// add the department prefix
			$code = $department->get('prefix') . '-' . $code;
			
			$db->setQuery("UPDATE #__rsticketspro_departments SET `next_number` = `next_number` + 1 WHERE id='".$department_id."' LIMIT 1");
			$db->query();
		}
		
		// subject
		$subject = $params['subject'];

		// message
		$message = $params['message'];
		
		// date and time
		if (!empty($params['date']))
			$date = JFactory::getDate($params['date']);
		else
			$date = JFactory::getDate();
		$date = $date->toUnix();
		
		// save the ticket details in the database
		$ticket =& JTable::getInstance('RSTicketsPro_Tickets','Table');
		$ticket->department_id = $department_id;
		$ticket->staff_id = $staff_id;
		$ticket->customer_id = $customer_id;
		$ticket->code = $code;
		$ticket->subject = $subject;
		$ticket->status_id = 1;
		$ticket->priority_id = $priority_id;
		$ticket->date = $date;
		$ticket->last_reply = $date;
		$ticket->replies = 0;
		$ticket->autoclose_sent = 0;
		$ticket->agent = $params['agent'];
		$ticket->referer = $params['referer'];
		$ticket->ip = $params['ip'];
		$ticket->logged = $logged;
		$ticket->feedback = 0;
		$ticket->store();
		
		$ticket_id = $ticket->id;
		
		$message_params = array(
			'ticket_id' => $ticket_id,
			'user_id' => $customer_id,
			'date' => $date,
			'message' => $params['message'],
			'dont_send_emails' => 1,
			'return_array' => 1
		);
		
		$custom_fields_email = '';
		// save the custom fields in the database
		foreach ($custom_fields as $field_id => $value)
		{
			$new_field =& JTable::getInstance('RSTicketsPro_Custom_Fields_Values','Table');
			$new_field->custom_field_id = $field_id;
			$new_field->ticket_id = $ticket_id;
			$new_field->value = is_array($value) ? implode("\n", $value) : $value;
			
			$new_field->store();
			
			$custom_field =& JTable::getInstance('RSTicketsPro_Custom_Fields','Table');
			$custom_field->load($field_id);
			
			$custom_fields_email .= '<p>'.JText::_($custom_field->label).': '.(is_array($value) ? implode(', ', $value) : $new_field->value).'</p>';
		}
		
		list($ticket_message_id, $attachments) = RSTicketsProHelper::addTicketReply($message_params, $files);
		
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
		
		// send email to the customer with a copy of his own ticket
		if ($department->customer_send_copy_email)
		{
			$email = RSTicketsProHelper::getEmail('add_ticket_customer');
			$customer = JFactory::getUser($customer_id);
			
			$replace = array('{live_site}', '{ticket}', '{customer_name}', '{customer_username}', '{customer_email}', '{code}', '{subject}', '{message}', '{custom_fields}', '{department_id}', '{department_name}');
			$with = array(JURI::root(), RSTicketsProHelper::mailRoute('index.php?option=com_rsticketspro&view=ticket&cid='.$ticket_id.':'.JFilterOutput::stringURLSafe($ticket->subject), true, RSTicketsProHelper::getConfig('customer_itemid')), $customer->get('name'), $customer->get('username'), $customer->get('email'), $ticket->code, $ticket->subject, $message, $custom_fields_email, $department->get('id'), JText::_($department->get('name')));
			
			$email_subject = '['.$ticket->code.'] '.$ticket->subject;
			$email_message = str_replace($replace, $with, $email->message);
			$email_message = RSTicketsProHelper::getReplyAbove().$email_message;
			
			RSTicketsProHelper::sendMail($from, $fromname, $customer->get('email'), $email_subject, $email_message, 1, $department->customer_attach_email ? $attachments : null, $department->get('cc'), $department->get('bcc'));
		}
		
		// send email to the staff member that gets assigned this ticket
		if ($department->staff_send_email && $staff_id)
		{
			$email = RSTicketsProHelper::getEmail('add_ticket_staff');
			$customer = JFactory::getUser($customer_id);
			$staff = JFactory::getUser($staff_id);
			
			$replace = array('{live_site}', '{ticket}', '{customer_name}', '{customer_username}', '{customer_email}', '{staff_name}', '{staff_username}', '{staff_email}', '{code}', '{subject}', '{message}', '{custom_fields}', '{department_id}', '{department_name}');
			$with = array(JURI::root(), RSTicketsProHelper::mailRoute('index.php?option=com_rsticketspro&view=ticket&cid='.$ticket_id.':'.JFilterOutput::stringURLSafe($ticket->subject), true, RSTicketsProHelper::getConfig('staff_itemid')), $customer->get('name'), $customer->get('username'), $customer->get('email'), $staff->get('name'), $staff->get('username'), $staff->get('email'), $ticket->code, $ticket->subject, $message, $custom_fields_email, $department->get('id'), JText::_($department->get('name')));
			
			$email_subject = '['.$ticket->code.'] '.$ticket->subject;
			$email_message = str_replace($replace, $with, $email->message);
			$email_message = RSTicketsProHelper::getReplyAbove().$email_message;
			
			RSTicketsProHelper::sendMail($from, $fromname, $staff->get('email'), $email_subject, $email_message, 1, $department->staff_attach_email ? $attachments : null, $department->get('cc'), $department->get('bcc'));
		}
		
		// notify the email addresses configured in the department
		if ($department->notify_new_tickets_to)
		{
			$email = RSTicketsProHelper::getEmail('add_ticket_notify');
			$notify_new_tickets_to = $department->notify_new_tickets_to;
			$notify_new_tickets_to = str_replace("\r\n", "\n", $notify_new_tickets_to);
			$notify_new_tickets_to = explode("\n", $notify_new_tickets_to);
			
			$customer = JFactory::getUser($customer_id);
			$staff = JFactory::getUser($staff_id);
			
			$replace = array('{live_site}', '{ticket}', '{customer_name}', '{customer_username}', '{customer_email}', '{staff_name}', '{staff_username}', '{staff_email}', '{code}', '{subject}', '{message}', '{custom_fields}', '{department_id}', '{department_name}');
			$with = array(JURI::root(), RSTicketsProHelper::mailRoute('index.php?option=com_rsticketspro&view=ticket&cid='.$ticket_id.':'.JFilterOutput::stringURLSafe($ticket->subject), true, RSTicketsProHelper::getConfig('staff_itemid')), $customer->get('name'), $customer->get('username'), $customer->get('email'), $staff->get('name'), $staff->get('username'), $staff->get('email'), $ticket->code, $ticket->subject, $message, $custom_fields_email, $department->get('id'), JText::_($department->get('name')));
			
			$email_subject = '['.$ticket->code.'] '.$ticket->subject;
			$email_message = str_replace($replace, $with, $email->message);
			
			foreach ($notify_new_tickets_to as $notify_email)
			{
				$notify_email = trim($notify_email);
				RSTicketsProHelper::sendMail($from, $fromname, $notify_email, $email_subject, $email_message, 1, $department->staff_attach_email ? $attachments : null, $department->get('cc'), $department->get('bcc'));
			}
		}
		
		return $ticket_id;
	}
	
	function addAttachments($ticket_id, $ticket_message_id, $department, $files)
	{
		$attachments = array();
		foreach ($files as $file)
		{
			if ($department->get('upload_files') > 0 && count($attachments) >= $department->get('upload_files'))
				break;
				
			if ($file['src'] == 'upload')
			{
				$new_file =& JTable::getInstance('RSTicketsPro_Ticket_Files','Table');
				$new_file->ticket_id = $ticket_id;
				$new_file->ticket_message_id = $ticket_message_id;
				$new_file->filename = $file['name'];
				
				$new_file->store();
				$hash = md5($new_file->id.' '.$ticket_message_id);
				
				JFile::upload($file['tmp_name'], RST_UPLOAD_FOLDER.DS.$hash);
			}
			elseif ($file['src'] == 'cron')
			{
				$new_file =& JTable::getInstance('RSTicketsPro_Ticket_Files','Table');
				$new_file->ticket_id = $ticket_id;
				$new_file->ticket_message_id = $ticket_message_id;
				$new_file->filename = $file['filename'];
				
				$new_file->store();
				$hash = md5($new_file->id.' '.$ticket_message_id);
				
				JFile::write(RST_UPLOAD_FOLDER.DS.$hash, $file['contents']);
			}
			
			// store attachment
			$attachment = array();
			$attachment['path'] = RST_UPLOAD_FOLDER.DS.$hash;
			$attachment['filename'] = $new_file->filename;
			
			$attachments[] = $attachment;
		}
		
		return $attachments;
	}
	
	function addTicketReply($params, $files=array())
	{
		$db = JFactory::getDBO();
		
		// get the ticket
		$ticket_id = (int) $params['ticket_id'];
		$ticket =& JTable::getInstance('RSTicketsPro_Tickets','Table');
		$ticket->load($params['ticket_id']);

		// get the department
		$department_id = $ticket->department_id;
		$department =& JTable::getInstance('RSTicketsPro_Departments','Table');
		$department->load($department_id);
		
		// date and time
		if (!empty($params['date']))
			$date = JFactory::getDate($params['date']);
		else
			$date = JFactory::getDate();
			
		$date = $date->toUnix();
		
		$user_id = (int) $params['user_id'];
		$message = $params['message'];
		
		if (!RSTicketsProHelper::getConfig('allow_rich_editor'))
			$message = RSTicketsProHelper::htmlEscape($message);
		
		// append the signature ?
		if ($ticket->customer_id != $user_id)
		{
			$is_staff = RSTicketsProHelper::isStaff($user_id);
			if ($is_staff)
			{
				$signature = RSTicketsProHelper::getSignature();
				if (!empty($params['use_signature']) && $signature)
					$message .= '<div class="rst_signature">'.$signature.'</div>';
			}
		}
		else
			$is_staff = false;
			
		// save the ticket message
		$ticket_message =& JTable::getInstance('RSTicketsPro_Ticket_Messages','Table');
		$ticket_message->ticket_id = $ticket_id;
		$ticket_message->user_id = $user_id;
		$ticket_message->message = $message;
		$ticket_message->date = $date;
		$ticket_message->store();
		
		if (!RSTicketsProHelper::getConfig('allow_rich_editor'))
			$message = nl2br($message);
		
		$ticket_message_id = $ticket_message->id;
		
		jimport('joomla.filesystem.file');
		// save the files in the uploads folder & database
		$attachments = RSTicketsProHelper::addAttachments($ticket_id, $ticket_message_id, $department, $files);
		if (count($attachments))
		{
			$db->setQuery("UPDATE #__rsticketspro_tickets SET has_files='1' WHERE id='".$ticket_id."'");
			$db->query();
		}
		
		// if a customer replied, we don't need to autoclose anymore
		if (!$is_staff || $ticket->customer_id == $user_id)
		{
			$db->setQuery("UPDATE #__rsticketspro_tickets SET autoclose_sent='0' WHERE id='".$ticket_id."' LIMIT 1");
			$db->query();
		}
		
		// assign the ticket if the department's assignment type is static and the ticket isn't already assigned
		if ($is_staff && $department->assignment_type == 0 && $ticket->staff_id == 0)
		{
			$ticket->staff_id = $user_id;
			$db->setQuery("UPDATE #__rsticketspro_tickets SET staff_id='".$user_id."' WHERE id='".$ticket_id."' LIMIT 1");
			$db->query();
		}
		
		// update the status if not closed
		if ($ticket->status_id != 2)
		{
			if (!$is_staff || $ticket->customer_id == $user_id)
				$ticket->status_id = 1; // set to open if a customer replied
			elseif ($is_staff)
				$ticket->status_id = 3; // set to on-hold if a staff member replied
				
			
			$db->setQuery("UPDATE #__rsticketspro_tickets SET status_id='".$ticket->status_id."' WHERE id='".$ticket_id."' LIMIT 1");
			$db->query();
		}
		
		$last_reply_customer = $is_staff ? 0 : 1;
		$db->setQuery("UPDATE #__rsticketspro_tickets SET last_reply='".$date."', replies = replies + 1, last_reply_customer='".$last_reply_customer."' WHERE id='".$ticket_id."' LIMIT 1");
		$db->query();
		
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
		
		// send email to the staff member with the customer's reply
		if (empty($params['dont_send_emails']) && !$is_staff && $department->staff_send_email && $ticket->staff_id)
		{
			$email = RSTicketsProHelper::getEmail('add_ticket_reply_staff');
			$customer = JFactory::getUser($user_id);
			$staff = JFactory::getUser($ticket->staff_id);
			
			$replace = array('{live_site}', '{ticket}', '{customer_name}', '{customer_username}', '{customer_email}', '{staff_name}', '{staff_username}', '{staff_email}', '{code}', '{subject}', '{message}', '{department_id}', '{department_name}');
			$with = array(JURI::root(), RSTicketsProHelper::mailRoute('index.php?option=com_rsticketspro&view=ticket&cid='.$ticket_id.':'.JFilterOutput::stringURLSafe($ticket->subject), true, RSTicketsProHelper::getConfig('staff_itemid')), $customer->get('name'), $customer->get('username'), $customer->get('email'), $staff->get('name'), $staff->get('username'), $staff->get('email'), $ticket->code, $ticket->subject, $message, $department->get('id'), JText::_($department->get('name')));
			
			$email_subject = '['.$ticket->code.'] '.$ticket->subject;
			$email_message = str_replace($replace, $with, $email->message);
			$email_message = RSTicketsProHelper::getReplyAbove().$email_message;
			
			RSTicketsProHelper::sendMail($from, $fromname, $staff->get('email'), $email_subject, $email_message, 1, $department->staff_attach_email ? $attachments : null, $department->get('cc'), $department->get('bcc'));
		}
		
		// send email to the customer with the staff member's reply
		if (empty($params['dont_send_emails']) && $is_staff && $department->customer_send_email)
		{
			$email = RSTicketsProHelper::getEmail('add_ticket_reply_customer');
			$customer = JFactory::getUser($ticket->customer_id);
			$staff = JFactory::getUser($user_id);
			
			$replace = array('{live_site}', '{ticket}', '{customer_name}', '{customer_username}', '{customer_email}', '{staff_name}', '{staff_username}', '{staff_email}', '{code}', '{subject}', '{message}', '{department_id}', '{department_name}');
			$with = array(JURI::root(), RSTicketsProHelper::mailRoute('index.php?option=com_rsticketspro&view=ticket&cid='.$ticket_id.':'.JFilterOutput::stringURLSafe($ticket->subject), true, RSTicketsProHelper::getConfig('customer_itemid')), $customer->get('name'), $customer->get('username'), $customer->get('email'), $staff->get('name'), $staff->get('username'), $staff->get('email'), $ticket->code, $ticket->subject, $message, $department->get('id'), JText::_($department->get('name')));
			
			$email_subject = '['.$ticket->code.'] '.$ticket->subject;
			$email_message = str_replace($replace, $with, $email->message);
			$email_message = RSTicketsProHelper::getReplyAbove().$email_message;
			
			RSTicketsProHelper::sendMail($from, $fromname, $customer->get('email'), $email_subject, $email_message, 1, $department->customer_attach_email ? $attachments : null, $department->get('cc'), $department->get('bcc'));
		}
		
		if (!$is_staff)
		{
			//check if notification email address is not empty
			$to = RSTicketsProHelper::getConfig('notice_email_address');
			$to = explode(',', $to);
			$staff = JFactory::getUser($ticket->staff_id);
			if ($staff->get('email')) $to[] = $staff->get('email');
			
			//check if number of max replies is reached
			if (!empty($to))
			{
				$max_replies_nr  = (int) RSTicketsProHelper::getConfig('notice_max_replies_nr');
				$current_replies = RSTicketsProHelper::getConsecutiveReplies($ticket->id);
				if ($max_replies_nr > 0 && $current_replies == $max_replies_nr && !$ticket->staff_id)
				{
					$email 	  = RSTicketsProHelper::getEmail('notification_max_replies_nr');
					$customer = JFactory::getUser($user_id);
					
					$replace = array('{live_site}', '{ticket}', '{customer_name}', '{customer_username}', '{customer_email}', '{code}', '{subject}', '{message}', '{replies}', '{department_id}', '{department_name}');
					$with = array(JURI::root(), RSTicketsProHelper::mailRoute('index.php?option=com_rsticketspro&view=ticket&cid='.$ticket_id.':'.JFilterOutput::stringURLSafe($ticket->subject), true, RSTicketsProHelper::getConfig('staff_itemid')), $customer->get('name'), $customer->get('username'), $customer->get('email'), $ticket->code, $ticket->subject, $message, $current_replies, $department->get('id'), JText::_($department->get('name')));

					$email_subject = str_replace($replace, $with, $email->subject);
					$email_message = str_replace($replace, $with, $email->message);
					$email_message = RSTicketsProHelper::getReplyAbove().$email_message;

					RSTicketsProHelper::sendMail($from, $fromname, $to, $email_subject, $email_message, 1, null, $department->get('cc'), $department->get('bcc'));
				}

				//check if number of max replies with no staff response is reached
				$max_replies_nr = (int) RSTicketsProHelper::getConfig('notice_replies_with_no_response_nr');
				if ($max_replies_nr > 0 && $current_replies == $max_replies_nr && $ticket->staff_id)
				{
					$email 	  = RSTicketsProHelper::getEmail('notification_replies_with_no_response_nr');
					$customer = JFactory::getUser($user_id);
					$staff 	  = JFactory::getUser($ticket->staff_id);
					
					$replace = array('{live_site}', '{ticket}', '{customer_name}', '{customer_username}', '{customer_email}', '{staff_name}', '{staff_username}', '{staff_email}', '{code}', '{subject}', '{message}', '{replies}', '{department_id}', '{department_name}');
					$with = array(JURI::root(), RSTicketsProHelper::mailRoute('index.php?option=com_rsticketspro&view=ticket&cid='.$ticket_id.':'.JFilterOutput::stringURLSafe($ticket->subject), true, RSTicketsProHelper::getConfig('staff_itemid')), $customer->get('name'), $customer->get('username'), $customer->get('email'), $staff->get('name'), $staff->get('username'), $staff->get('email'), $ticket->code, $ticket->subject, $message, $current_replies, $department->get('id'), JText::_($department->get('name')));
					
					$email_subject = str_replace($replace, $with, $email->subject);
					$email_message = str_replace($replace, $with, $email->message);
					$email_message = RSTicketsProHelper::getReplyAbove().$email_message;

					RSTicketsProHelper::sendMail($from, $fromname, $to, $email_subject, $email_message, 1, null, $department->get('cc'), $department->get('bcc'));
				}
				
				//check if it has restricted words
				$keywords = explode(',', RSTicketsProHelper::getConfig('notice_not_allowed_keywords'));
				if (!empty($keywords))				
					foreach($keywords as $word)
					{
						$word = trim($word);
						if (empty($word)) continue;
					
						$pattern = '/\b(\w*'.preg_quote($word).'\w*)\b/';
						if (preg_match($pattern, $message))
						{
							$email 	  = RSTicketsProHelper::getEmail('notification_not_allowed_keywords');
							$customer = JFactory::getUser($user_id);

							$replace = array('{live_site}', '{ticket}', '{customer_name}', '{customer_username}', '{customer_email}', '{staff_name}', '{staff_username}', '{staff_email}', '{code}', '{subject}', '{message}', '{department_id}', '{department_name}');
							$with = array(JURI::root(), RSTicketsProHelper::mailRoute('index.php?option=com_rsticketspro&view=ticket&cid='.$ticket_id.':'.JFilterOutput::stringURLSafe($ticket->subject), true, RSTicketsProHelper::getConfig('staff_itemid')), $customer->get('name'), $customer->get('username'), $customer->get('email'), $staff->get('name'), $staff->get('username'), $staff->get('email'), $ticket->code, $ticket->subject, $message, $department->get('id'), JText::_($department->get('name')));

							$email_subject = str_replace($replace, $with, $email->subject);
							$email_message = str_replace($replace, $with, $email->message);
							$email_message = RSTicketsProHelper::getReplyAbove().$email_message;

							RSTicketsProHelper::sendMail($from, $fromname, $to, $email_subject, $email_message, 1, null, $department->get('cc'), $department->get('bcc'));
							break;
						}
					}
			}
		}
		
		if (!empty($params['return_array']))
			return array($ticket_message_id, $attachments);
		
		return $ticket_message_id;
	}
	
	function getConsecutiveReplies($ticket_id)
	{
		$ticket_id = (int) $ticket_id;

		$db = JFactory::getDBO();
		$db->setQuery("SELECT `user_id` FROM #__rsticketspro_ticket_messages WHERE `ticket_id` = '".$ticket_id."' ORDER BY `date` DESC");
		$users = $db->loadResultArray();

		$replies = 0;
		foreach ($users as $user_id)
		{
			$is_staff = RSTicketsProHelper::isStaff($user_id);
			if ($is_staff)
				break;
			
			$replies++;
		}
		
		return $replies;
	}

	function createUser($email, $data)
	{
		if (empty($email) || empty($data)) return false;
		
		$db = JFactory::getDBO();
		$db->setQuery("SELECT `id` FROM #__users WHERE `email` LIKE '".$db->getEscaped(strtolower($email))."' LIMIT 1");
		if ($user_id = $db->loadResult())
			return $user_id;
		
		$lang =& JFactory::getLanguage();
		$lang->load('com_user', JPATH_SITE, null, true);
		$lang->load('com_user', JPATH_ADMINISTRATOR, null, true);
		$lang->load('com_users', JPATH_ADMINISTRATOR, null, true);
		
		$db =& JFactory::getDBO();
			
		jimport('joomla.user.helper');
		
		// Get required system objects
		$user = JFactory::getUser(0);
		
		@list($username, $domain) = explode('@', $email);
		$db->setQuery("SELECT `id` FROM #__users WHERE `username` LIKE '".$db->getEscaped($username)."' LIMIT 1");
		
		if (preg_match( "#[<>\"'%;()&]#i", $username) || strlen(utf8_decode($username )) < 2)
		{
			$username = JFilterOutput::stringURLSafe($data['name']);
			if (strlen($username) < 2)
				$username = str_pad($username, 2, mt_rand(0,9));
		}
		
		while ($db->loadResult())
		{
			$username .= mt_rand(0,9);
			$db->setQuery("SELECT `id` FROM #__users WHERE `username` LIKE '".$db->getEscaped($username)."' LIMIT 1");
		}
		
		// Bind the post array to the user object
		$post = array();
		$post['name'] = $data['name'];
		if (trim($post['name']) == '')
			$post['name'] = $email;
		$post['email'] = $email;
		$post['username'] = $username;
		$post['password']  = JUserHelper::genRandomPassword(8);
		$original = $post['password'];
		$post['password2'] = $post['password'];
		if (!$user->bind($post, 'usertype'))
			JError::raiseError(500, $user->getError());

		// Set some initial user values
		$user->set('id', 0);
		
		if (RSTicketsProHelper::isJ16())
		{
			$params	= JComponentHelper::getParams('com_users');
			$user->set('groups', array($params->get('new_usertype', 2)));
		}
		else
		{
			$authorize =& JFactory::getACL();
			// Initialize new usertype setting
			$usersConfig =& JComponentHelper::getParams('com_users');
			$newUsertype = $usersConfig->get('new_usertype');
			if (!$newUsertype)
				$newUsertype = 'Registered';
			
			$user->set('usertype', '');
			$user->set('gid', $authorize->get_group_id('', $newUsertype, 'ARO'));
		}

		$date =& JFactory::getDate();
		$user->set('registerDate', $date->toMySQL());

		$user->set('block', 0);
		$user->set('lastvisitDate', '0000-00-00 00:00:00');

		// If there was an error with registration, set the message
		if (!$user->save())
		{
			return false;
			JError::raiseWarning('', JText::_($user->getError()));
		}
		
		// Hack for community builder - approve the user so that he can login
		if (file_exists(JPATH_SITE.DS.'components'.DS.'com_comprofiler'.DS.'comprofiler.php'))
		{
		   $db->setQuery("INSERT INTO #__comprofiler SET approved = 1 , user_id = ".$user->get('id')." , id = ".$user->get('id')." , confirmed = 1");
		   $db->query();
		}
		
		// Send registration confirmation mail
		$password = $original;
		// Disallow control chars in the email
		$password = preg_replace('/[\x00-\x1F\x7F]/', '', $password);
		
		RSTicketsProHelper::sendUserEmail($user, $password);
		return $user->get('id');
	}
	
	function sendUserEmail(&$user, $password)
	{
		$mainframe =& JFactory::getApplication();
		
		$lang = JFactory::getLanguage();
		$lang->load('com_rsticketspro', JPATH_SITE);

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
		
		$email = RSTicketsProHelper::getEmail('new_user_email');
		
		$replace = array('{live_site}', '{username}', '{password}', '{email}');
		$with = array(JURI::root(), $user->get('username'), $password, $user->get('email'));
		
		$email_subject = $email->subject;
		$email_message = str_replace($replace, $with, $email->message);
		
		JUtility::sendMail($from, $fromname, $user->get('email'), $email_subject, $email_message, 1);
	}
	
	function generateNumber($max=10)
	{
		$key = '';
		for($i=0;$i<$max;$i++)
		{
			$w1 = rand(0,1);
			$w2 = 1-$w1;
			$key .= chr($w1*rand(65,90)+$w2*rand(48,57));
		}
		
		return $key;
	}
	
	function getCurrentDate($date=null)
	{
		$config = new JConfig();
		
		if (RSTicketsProHelper::isJ16())
		{			
			$config = JFactory::getConfig();
			date_default_timezone_set($config->get('offset'));
			
			$unix = $date;
		}
		else
		{
			$date = JFactory::getDate($date, -$config->offset);
			$unix = $date->toUnix();
		}
			
		return $unix;
	}
	
	function getExtension($filename)
	{
		jimport('joomla.filesystem.file');
		return JFile::getExt($filename);
	}
	
	function isAllowedExtension($ext, $ext_array)
	{
		if (!is_array($ext_array)) return true;
		if (count($ext_array) == 0) return true;
		if (count($ext_array) == 1 && trim($ext_array[0]) == '') return true;
		if (in_array('*',$ext_array)) return true;
		
		// convert everything to lowercase
		$ext = strtolower($ext);
		array_walk($ext_array, array('RSTicketsProHelper', 'arraytolower'));
		
		return in_array($ext,$ext_array);
	}
	
	function arraytolower(&$value, $key)
	{
		$value = strtolower($value);
	}
	
	function getEmail($type)
	{
		$db = JFactory::getDBO();
		
		$lang = JFactory::getLanguage();
		$language = $lang->get('tag');
		
		$db->setQuery("SELECT * FROM #__rsticketspro_emails WHERE `lang`='".$language."' AND `type`='".$db->getEscaped($type)."' LIMIT 1");
		$email = $db->loadObject();
		if (empty($email))
		{
			$db->setQuery("SELECT * FROM #__rsticketspro_emails WHERE `lang`='en-GB' AND `type`='".$db->getEscaped($type)."' LIMIT 1");
			$email = $db->loadObject();
		}
		
		return $email;
	}
	
	function getFooter()
	{
		$footer = '<p align="center"><a href="http://www.rsjoomla.com/joomla-components/joomla-help-desk.html" title="Joomla! Help Desk Ticketing System" target="_blank">Joomla! Help Desk Ticketing System</a> by <a href="http://www.rsjoomla.com" target="_blank" title="Joomla! Extensions">RSJoomla!</a></p>';
			
		return $footer;
	}
	
	function shorten($string,$max=255,$more='...')
	{
		$string_tmp = '';
		$exp = explode(' ',$string);
		for ($i=0; $i<count($exp); $i++)
		{
			if (strlen($string_tmp) + strlen($exp[$i]) < $max)
				$string_tmp .= $exp[$i].' ';
			else
				break;
		}
		$string = substr($string_tmp,0,-1).(strlen($string) > strlen($string_tmp) ? $more : '');
		return RSTicketsProHelper::closeTags($string);
	}
	
	function closeTags($html)
	{
		preg_match_all('#<([a-z]+)(?: .*)?(?<![/|/ ])>#iU', $html, $result);
		$openedtags = $result[1]; 
		preg_match_all('#</([a-z]+)>#iU', $html, $result);
		$closedtags = $result[1];
		$len_opened = count($openedtags);
		if (count($closedtags) == $len_opened)
			return $html;
			
		$openedtags = array_reverse($openedtags);
		for ($i=0; $i < $len_opened; $i++) {
			if (!in_array($openedtags[$i], $closedtags))
				$html .= '</'.$openedtags[$i].'>';
			else
				unset($closedtags[array_search($openedtags[$i], $closedtags)]);
		}
		return $html;
	}
	
	function getCalendarFormat($format)
	{		
		/*
		%a 	abbreviated weekday name D
		%A 	full weekday name l
		%b 	abbreviated month name M
		%B 	full month name F
		%C 	century number 
		%d 	the day of the month ( 00 .. 31 ) d
		%e 	the day of the month ( 0 .. 31 ) j
		%H 	hour ( 00 .. 23 ) H
		%I 	hour ( 01 .. 12 ) h
		%j 	day of the year ( 000 .. 366 ) z
		%k 	hour ( 0 .. 23 ) G
		%l 	hour ( 1 .. 12 ) g
		%m 	month ( 01 .. 12 ) m
		%M 	minute ( 00 .. 59 ) i
		%n 	a newline character \n
		%p 	``PM'' or ``AM'' A
		%P 	``pm'' or ``am'' a
		%S 	second ( 00 .. 59 ) s
		%s 	number of seconds since Epoch (since Jan 01 1970 00:00:00 UTC) U
		%t 	a tab character \t
		%U, %W, %V 	the week number W
		%u 	the day of the week ( 1 .. 7, 1 = MON ) N
		%w 	the day of the week ( 0 .. 6, 0 = SUN ) w
		%y 	year without the century ( 00 .. 99 ) y
		%Y 	year including the century ( ex. 1979 ) Y
		%% 	a literal % character %		
		*/
		
		$php = array( '%',  'D',  'l',  'M',  'F',  'd',  'j',  'H',  'h',  'z',  'G',  'g',  'm',  'i', "\n",  'A',  'a',  's',  'U', "\t",  'W',  'N',  'w',  'y',  'Y');
		$js  = array('%%', '%a', '%A', '%b', '%B', '%d', '%e', '%H', '%I', '%j', '%k', '%l', '%m', '%M', '%n', '%p', '%P', '%S', '%s', '%t', '%U', '%u', '%w', '%y', '%Y');
		
		return str_replace($php, $js, $format);
	}
	
	function sendMail($from, $fromname, $recipient, $subject, $body, $mode=0, $attachments=null, $cc=null, $bcc=null)
	{
		if (!is_array($recipient))
			$recipient = array($recipient);
		
		jimport('joomla.mail.helper');
		foreach ($recipient as $i => $r)
		{
			$r = trim($r);
			if (!JMailHelper::isEmailAddress($r))
				unset($recipient[$i]);
		}
		
		if (empty($recipient) || !count($recipient))
			return false;
		
	 	// Get a JMail instance
		$mail =& JFactory::getMailer();
		
		$mail->ClearReplyTos();
		$mail->setSender(array($from, $fromname));
		$mail->setSubject($subject);
		$mail->setBody($body);

		// Are we sending the email as HTML?
		if ($mode)
			$mail->IsHTML(true);

		$mail->addRecipient($recipient);
		
		$mail->ClearReplyTos();
		$mail->addReplyTo(array($from, $fromname));
		
		if (!empty($cc))
		{
			$cc = str_replace(array("\r\n", "\r"), "\n", $cc);
			$cc = explode("\n", $cc);
			foreach ($cc as $i => $r)
			{
				$r = trim($r);
				if (!JMailHelper::isEmailAddress($r))
					continue;
				
				$mail->addCC($r);
			}
		}
		
		if (!empty($bcc))
		{
			$bcc = str_replace(array("\r\n", "\r"), "\n", $bcc);
			$bcc = explode("\n", $bcc);
			foreach ($bcc as $i => $r)
			{
				$r = trim($r);
				if (!JMailHelper::isEmailAddress($r))
					continue;
				
				$mail->addBCC($r);
			}
		}
		
		if (is_array($attachments) && count($attachments))
		{
			jimport('joomla.filesystem.file');
			foreach ($attachments as $attachment)
			{
				$mail->AddStringAttachment(JFile::read($attachment['path']), $attachment['filename']);
			}
		}
				
		return $mail->Send();
	}
	
	function htmlEscape($val)
	{
		return htmlentities($val, ENT_COMPAT, 'UTF-8');
	}
}