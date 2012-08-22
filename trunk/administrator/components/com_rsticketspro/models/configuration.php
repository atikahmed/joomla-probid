<?php
/**
* @version 2.0.0
* @package RSTickets! Pro 2.0.0
* @copyright (C) 2010 www.rsjoomla.com
* @license GPL, http://www.gnu.org/licenses/gpl-2.0.html
*/

defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.model');

class RSTicketsProModelConfiguration extends JModel
{
	var $_db;
	
	function __construct()
	{
		parent::__construct();
		
		$this->_db = JFactory::getDBO();
	}
	
	function save()
	{
		$config = RSTicketsProHelper::getConfig();
		
		$post = JRequest::get('post');
		
		$post['global_message'] = JRequest::getVar('global_message', '', 'post', 'none', JREQUEST_ALLOWRAW);
		$post['submit_message'] = JRequest::getVar('submit_message', '', 'post', 'none', JREQUEST_ALLOWRAW);
		
		if (isset($post['global_register_code']))
		{
			$this->_db->setQuery("UPDATE #__rsticketspro_configuration SET `value`='".$this->_db->getEscaped($post['global_register_code'])."' WHERE `name`='global_register_code' LIMIT 1");
			$this->_db->query();
		}
		if (isset($post['date_format']))
		{
			$this->_db->setQuery("UPDATE #__rsticketspro_configuration SET `value`='".$this->_db->getEscaped($post['date_format'])."' WHERE `name`='date_format' LIMIT 1");
			$this->_db->query();
		}
		if (isset($post['date_format_notime']))
		{
			$this->_db->setQuery("UPDATE #__rsticketspro_configuration SET `value`='".$this->_db->getEscaped($post['date_format_notime'])."' WHERE `name`='date_format_notime' LIMIT 1");
			$this->_db->query();
		}
		if (isset($post['rsticketspro_link']))
		{
			$this->_db->setQuery("UPDATE #__rsticketspro_configuration SET `value`='".(int) $post['rsticketspro_link']."' WHERE `name`='rsticketspro_link' LIMIT 1");
			$this->_db->query();
		}
		if (isset($post['allow_rich_editor']))
		{
			$this->_db->setQuery("UPDATE #__rsticketspro_configuration SET `value`='".(int) $post['allow_rich_editor']."' WHERE `name`='allow_rich_editor' LIMIT 1");
			$this->_db->query();
		}
		if (isset($post['show_kb_search']))
		{
			$this->_db->setQuery("UPDATE #__rsticketspro_configuration SET `value`='".(int) $post['show_kb_search']."' WHERE `name`='show_kb_search' LIMIT 1");
			$this->_db->query();
		}
		if (isset($post['show_signature']))
		{
			$this->_db->setQuery("UPDATE #__rsticketspro_configuration SET `value`='".(int) $post['show_signature']."' WHERE `name`='show_signature' LIMIT 1");
			$this->_db->query();
		}
		if (isset($post['submit_redirect']))
		{
			$this->_db->setQuery("UPDATE #__rsticketspro_configuration SET `value`='".$this->_db->getEscaped($post['submit_redirect'])."' WHERE `name`='submit_redirect' LIMIT 1");
			$this->_db->query();
		}
		if (isset($post['staff_force_departments']))
		{
			$this->_db->setQuery("UPDATE #__rsticketspro_configuration SET `value`='".(int) $post['staff_force_departments']."' WHERE `name`='staff_force_departments' LIMIT 1");
			$this->_db->query();
		}
		if (isset($post['global_message']))
		{
			$this->_db->setQuery("UPDATE #__rsticketspro_configuration SET `value`='".$this->_db->getEscaped($post['global_message'])."' WHERE `name`='global_message' LIMIT 1");
			$this->_db->query();
		}
		if (isset($post['submit_message']))
		{
			$this->_db->setQuery("UPDATE #__rsticketspro_configuration SET `value`='".$this->_db->getEscaped($post['submit_message'])."' WHERE `name`='submit_message' LIMIT 1");
			$this->_db->query();
		}
		if (isset($post['rsticketspro_add_tickets']))
		{
			$this->_db->setQuery("UPDATE #__rsticketspro_configuration SET `value`='".(int) $post['rsticketspro_add_tickets']."' WHERE `name`='rsticketspro_add_tickets' LIMIT 1");
			$this->_db->query();
		}
		if (isset($post['show_ticket_info']))
		{
			$this->_db->setQuery("UPDATE #__rsticketspro_configuration SET `value`='".(int) $post['show_ticket_info']."' WHERE `name`='show_ticket_info' LIMIT 1");
			$this->_db->query();
		}
		if (isset($post['show_user_info']))
		{
			$this->_db->setQuery("UPDATE #__rsticketspro_configuration SET `value`='".$this->_db->getEscaped($post['show_user_info'])."' WHERE `name`='show_user_info' LIMIT 1");
			$this->_db->query();
		}
		if (isset($post['show_email_link']))
		{
			$this->_db->setQuery("UPDATE #__rsticketspro_configuration SET `value`='".(int) $post['show_email_link']."' WHERE `name`='show_email_link' LIMIT 1");
			$this->_db->query();
		}
		if (isset($post['show_ticket_voting']))
		{
			$this->_db->setQuery("UPDATE #__rsticketspro_configuration SET `value`='".(int) $post['show_ticket_voting']."' WHERE `name`='show_ticket_voting' LIMIT 1");
			$this->_db->query();
		}
		if (isset($post['allow_ticket_closing']))
		{
			$this->_db->setQuery("UPDATE #__rsticketspro_configuration SET `value`='".(int) $post['allow_ticket_closing']."' WHERE `name`='allow_ticket_closing' LIMIT 1");
			$this->_db->query();
		}
		if (isset($post['allow_ticket_reopening']))
		{
			$this->_db->setQuery("UPDATE #__rsticketspro_configuration SET `value`='".(int) $post['allow_ticket_reopening']."' WHERE `name`='allow_ticket_reopening' LIMIT 1");
			$this->_db->query();
		}
		if (isset($post['ticket_view']))
		{
			$this->_db->setQuery("UPDATE #__rsticketspro_configuration SET `value`='".$this->_db->getEscaped($post['ticket_view'])."' WHERE `name`='ticket_view' LIMIT 1");
			$this->_db->query();
		}
		if (isset($post['ticket_viewing_history']))
		{
			$this->_db->setQuery("UPDATE #__rsticketspro_configuration SET `value`='".(int) $post['ticket_viewing_history']."' WHERE `name`='ticket_viewing_history' LIMIT 1");
			$this->_db->query();
		}
		if (isset($post['messages_direction']))
		{
			$this->_db->setQuery("UPDATE #__rsticketspro_configuration SET `value`='".$this->_db->getEscaped($post['messages_direction'])."' WHERE `name`='messages_direction' LIMIT 1");
			$this->_db->query();
		}
		if (isset($post['color_whole_ticket']))
		{
			$this->_db->setQuery("UPDATE #__rsticketspro_configuration SET `value`='".(int) $post['color_whole_ticket']."' WHERE `name`='color_whole_ticket' LIMIT 1");
			$this->_db->query();
		}
		if (isset($post['avatars']))
		{
			$this->_db->setQuery("UPDATE #__rsticketspro_configuration SET `value`='".$this->_db->getEscaped($post['avatars'])."' WHERE `name`='avatars' LIMIT 1");
			$this->_db->query();
		}
		if (isset($post['notice_email_address']))
		{
			$this->_db->setQuery("UPDATE #__rsticketspro_configuration SET `value`='".$this->_db->getEscaped($post['notice_email_address'])."' WHERE `name`='notice_email_address' LIMIT 1");
			$this->_db->query();
		}
		if (isset($post['notice_max_replies_nr']))
		{
			$this->_db->setQuery("UPDATE #__rsticketspro_configuration SET `value`='".(int) $post['notice_max_replies_nr']."' WHERE `name`='notice_max_replies_nr' LIMIT 1");
			$this->_db->query();
		}
		if (isset($post['notice_not_allowed_keywords']))
		{
			$this->_db->setQuery("UPDATE #__rsticketspro_configuration SET `value`='".$this->_db->getEscaped($post['notice_not_allowed_keywords'])."' WHERE `name`='notice_not_allowed_keywords' LIMIT 1");
			$this->_db->query();
		}
		if (isset($post['notice_replies_with_no_response_nr']))
		{
			$this->_db->setQuery("UPDATE #__rsticketspro_configuration SET `value`='".(int) $post['notice_replies_with_no_response_nr']."' WHERE `name`='notice_replies_with_no_response_nr' LIMIT 1");
			$this->_db->query();
		}
		
		$this->_db->setQuery("UPDATE #__rsticketspro_configuration SET `value`='".(int) $post['captcha_enabled']."' WHERE `name`='captcha_enabled' LIMIT 1");
		$this->_db->query();
		if (isset($post['captcha_enabled']))
		{
			if ($post['captcha_enabled'] == 1)
			{
				$captcha_enabled_for = array();
				if (isset($post['captcha_enabled_for_unregistered']))
					$captcha_enabled_for[] = 1;
				else
					$captcha_enabled_for[] = 0;
					
				if (isset($post['captcha_enabled_for_customers']))
					$captcha_enabled_for[] = 1;
				else
					$captcha_enabled_for[] = 0;
				
				if (isset($post['captcha_enabled_for_staff']))
					$captcha_enabled_for[] = 1;
				else
					$captcha_enabled_for[] = 0;
					
				$this->_db->setQuery("UPDATE #__rsticketspro_configuration SET `value`='".implode(',',$captcha_enabled_for)."' WHERE `name`='captcha_enabled_for' LIMIT 1");
				$this->_db->query();
				
				if (isset($post['captcha_characters']))
				{
					$post['captcha_characters'] = (int) $post['captcha_characters'];
					if ($post['captcha_characters'] < 3)
					{
						$post['captcha_characters'] = 3;
						JError::raiseWarning(500, JText::_('RST_CAPTCHA_CHARACTERS_ERROR'));
					}
					
					$this->_db->setQuery("UPDATE #__rsticketspro_configuration SET `value`='".$post['captcha_characters']."' WHERE `name`='captcha_characters' LIMIT 1");
					$this->_db->query();
				}
				if (isset($post['captcha_lines']))
				{
					$this->_db->setQuery("UPDATE #__rsticketspro_configuration SET `value`='".(int) $post['captcha_lines']."' WHERE `name`='captcha_lines' LIMIT 1");
					$this->_db->query();
				}
				if (isset($post['captcha_case_sensitive']))
				{
					$this->_db->setQuery("UPDATE #__rsticketspro_configuration SET `value`='".(int) $post['captcha_case_sensitive']."' WHERE `name`='captcha_case_sensitive' LIMIT 1");
					$this->_db->query();
				}
			}
			elseif ($post['captcha_enabled'] == 2)
			{
				if (isset($post['recaptcha_public_key']))
				{
					$this->_db->setQuery("UPDATE #__rsticketspro_configuration SET `value`='".$post['recaptcha_public_key']."' WHERE `name`='recaptcha_public_key' LIMIT 1");
					$this->_db->query();
				}
				if (isset($post['recaptcha_private_key']))
				{
					$this->_db->setQuery("UPDATE #__rsticketspro_configuration SET `value`='".$post['recaptcha_private_key']."' WHERE `name`='recaptcha_private_key' LIMIT 1");
					$this->_db->query();
				}
				if (isset($post['recaptcha_theme']))
				{
					$this->_db->setQuery("UPDATE #__rsticketspro_configuration SET `value`='".$post['recaptcha_theme']."' WHERE `name`='recaptcha_theme' LIMIT 1");
					$this->_db->query();
				}
			}
		}
		
		if (isset($post['email_use_global']))
		{
			$this->_db->setQuery("UPDATE #__rsticketspro_configuration SET `value`='".(int) $post['email_use_global']."' WHERE `name`='email_use_global' LIMIT 1");
			$this->_db->query();
			
			if (isset($post['email_address']))
			{
				$this->_db->setQuery("UPDATE #__rsticketspro_configuration SET `value`='".$this->_db->getEscaped($post['email_address'])."' WHERE `name`='email_address' LIMIT 1");
				$this->_db->query();
			}
			if (isset($post['email_address_fullname']))
			{
				$this->_db->setQuery("UPDATE #__rsticketspro_configuration SET `value`='".$this->_db->getEscaped($post['email_address_fullname'])."' WHERE `name`='email_address_fullname' LIMIT 1");
				$this->_db->query();
			}
		}
		if (isset($post['reply_above']))
		{
			$this->_db->setQuery("UPDATE #__rsticketspro_configuration SET `value`='".$this->_db->getEscaped($post['reply_above'])."' WHERE `name`='reply_above' LIMIT 1");
			$this->_db->query();
		}
		if (isset($post['customer_itemid']))
		{
			$this->_db->setQuery("UPDATE #__rsticketspro_configuration SET `value`='".(int) $post['customer_itemid']."' WHERE `name`='customer_itemid' LIMIT 1");
			$this->_db->query();
		}
		if (isset($post['staff_itemid']))
		{
			$this->_db->setQuery("UPDATE #__rsticketspro_configuration SET `value`='".(int) $post['staff_itemid']."' WHERE `name`='staff_itemid' LIMIT 1");
			$this->_db->query();
		}
		
		$this->_db->setQuery("UPDATE #__rsticketspro_configuration SET `value`='".(int) $post['autoclose_enabled']."' WHERE `name`='autoclose_enabled' LIMIT 1");
		$this->_db->query();
		if (isset($post['autoclose_enabled']) && $post['autoclose_enabled'] == 1)
		{
			if (isset($post['autoclose_cron_interval']))
			{
				$post['autoclose_cron_interval'] = (int) $post['autoclose_cron_interval'];
				if ($post['autoclose_cron_interval'] < 10)
				{
					$post['autoclose_cron_interval'] = 10;
					JError::raiseWarning(500, JText::_('RST_AUTOCLOSE_CHECK_ERROR'));
				}
				$this->_db->setQuery("UPDATE #__rsticketspro_configuration SET `value`='".$post['autoclose_cron_interval']."' WHERE `name`='autoclose_cron_interval' LIMIT 1");
				$this->_db->query();
			}
			if (isset($post['autoclose_email_interval']))
			{
				$post['autoclose_email_interval'] = (int) $post['autoclose_email_interval'];
				if ($post['autoclose_email_interval'] < 1)
				{
					$post['autoclose_email_interval'] = 1;
					JError::raiseWarning(500, JText::_('RST_AUTOCLOSE_DAYS_STATUS_ERROR'));
				}
				$this->_db->setQuery("UPDATE #__rsticketspro_configuration SET `value`='".$post['autoclose_email_interval']."' WHERE `name`='autoclose_email_interval' LIMIT 1");
				$this->_db->query();
			}
			if (isset($post['autoclose_interval']))
			{
				$post['autoclose_interval'] = (int) $post['autoclose_interval'];
				if ($post['autoclose_interval'] < 1)
				{
					$post['autoclose_interval'] = 1;
					JError::raiseWarning(500, JText::_('RST_AUTOCLOSE_DAYS_CLOSED_ERROR'));
				}
				$this->_db->setQuery("UPDATE #__rsticketspro_configuration SET `value`='".$post['autoclose_interval']."' WHERE `name`='autoclose_interval' LIMIT 1");
				$this->_db->query();
			}
		}
		if (isset($post['kb_hot_hits']))
		{
			$this->_db->setQuery("UPDATE #__rsticketspro_configuration SET `value`='".(int) $post['kb_hot_hits']."' WHERE `name`='kb_hot_hits' LIMIT 1");
			$this->_db->query();
		}
		if (isset($post['kb_comments']))
		{
			$this->_db->setQuery("UPDATE #__rsticketspro_configuration SET `value`='".$this->_db->getEscaped($post['kb_comments'])."' WHERE `name`='kb_comments' LIMIT 1");
			$this->_db->query();
		}
		if (isset($post['allow_predefined_subjects']))
		{
			$this->_db->setQuery("UPDATE #__rsticketspro_configuration SET `value`='".(int) $post['allow_predefined_subjects']."' WHERE `name`='allow_predefined_subjects' LIMIT 1");
			$this->_db->query();
		}
		if (isset($post['enable_time_spent']))
		{
			$this->_db->setQuery("UPDATE #__rsticketspro_configuration SET `value`='".(int) $post['enable_time_spent']."' WHERE `name`='enable_time_spent' LIMIT 1");
			$this->_db->query();
		}
		if (isset($post['time_spent_unit']))
		{
			$this->_db->setQuery("UPDATE #__rsticketspro_configuration SET `value`='".$this->_db->getEscaped($post['time_spent_unit'])."' WHERE `name`='time_spent_unit' LIMIT 1");
			$this->_db->query();
		}
		if (isset($post['calculate_itemids']))
		{
			$this->_db->setQuery("UPDATE #__rsticketspro_configuration SET `value`='".(int) $post['calculate_itemids']."' WHERE `name`='calculate_itemids' LIMIT 1");
			$this->_db->query();
		}
		
		RSTicketsProHelper::readConfig(true);
	}
	
	function getConfig()
	{
		return RSTicketsProHelper::getConfig();
	}
	
	function getAvatarsAvailable()
	{
		$return = array(
			'gravatar' => true,
			'comprofiler' => false,
			'community' => false,
			'kunena' => false,
			'fireboard' => false
		);
		
		$db = JFactory::getDBO();
		
		foreach ($return as $component => $value)
		{
			if ($component == 'gravatar')
				continue;
			
			if (RSTicketsProHelper::isJ16())
				$db->setQuery("SELECT `extension_id` FROM #__extensions WHERE `type`='component' AND `element`='com_".$component."'");
			else
				$db->setQuery("SELECT `id` FROM #__components WHERE `option`='com_".$component."'");
				
			$return[$component] = $db->loadResult() > 0;
		}
		
		return $return;
	}
	
	function getCommentOptions()
	{
		$db =& JFactory::getDBO();

		$supported_components = array('RSComments' => 'com_rscomments', 'JComments' => 'com_jcomments', 'Jom Comment' => 'com_jomcomment');
		
		$comments   = array();
		$comments[] = JHTML::_('select.option', '0', JText::_('RST_KB_COMMENTS_DISABLED'));
		$comments[] = JHTML::_('select.option', 'facebook', JText::_('RST_FACEBOOK_COMMENTS'));
		foreach ($supported_components as $name => $item) 
		{
			$disabled = true;
			$path = JPATH_ADMINISTRATOR.DS.'components'.DS.$item;
			if (file_exists($path))
			{
				if (RSTicketsProHelper::isJ16())
					$db->setQuery("SELECT `enabled` FROM #__extensions WHERE `type` = 'component' AND `element`='".$item."' LIMIT 1");
				else
					$db->setQuery("SELECT `enabled` FROM #__components WHERE `name`='".$name."' LIMIT 1");
				
				if ($db->loadResult())
					$disabled = false;
			}
			
			$comments[] = JHTML::_('select.option', $item, $name, 'value', 'text', $disabled);
		}
		
		return $comments;
	}
	
	function getDesigns()
	{
		jimport('joomla.filesystem.folder');
		
		$files = JFolder::files(JPATH_SITE.DS.'components'.DS.'com_rsticketspro'.DS.'assets'.DS.'css'.DS.'designs', '\.css$');
		
		$return = array();
		foreach ($files as $file)
			$return[] = JHTML::_('select.option', $file, $file);
		
		return $return;
	}
}
?>