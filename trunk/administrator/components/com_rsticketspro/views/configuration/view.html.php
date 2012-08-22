<?php
/**
* @version 2.0.0
* @package RSTickets! Pro 2.0.0
* @copyright (C) 2010 www.rsjoomla.com
* @license GPL, http://www.gnu.org/licenses/gpl-2.0.html
*/

defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.view');
jimport('joomla.html.pane');

class RSTicketsProViewConfiguration extends JView
{
	function display($tpl = null)
	{
		$mainframe =& JFactory::getApplication();
		
		JToolBarHelper::title('RSTickets! Pro','rsticketspro');
		
		JSubMenuHelper::addEntry(JText::_('RST_MANAGE_TICKETS'), 'index.php?option=com_rsticketspro&view=tickets');
		JSubMenuHelper::addEntry(JText::_('RST_DEPARTMENTS'), 'index.php?option=com_rsticketspro&view=departments');
		JSubMenuHelper::addEntry(JText::_('RST_GROUPS'), 'index.php?option=com_rsticketspro&view=groups');
		JSubMenuHelper::addEntry(JText::_('RST_STAFF_MEMBERS'), 'index.php?option=com_rsticketspro&view=staff');
		JSubMenuHelper::addEntry(JText::_('RST_PRIORITIES'), 'index.php?option=com_rsticketspro&view=priorities');
		JSubMenuHelper::addEntry(JText::_('RST_STATUSES'), 'index.php?option=com_rsticketspro&view=statuses');
		JSubMenuHelper::addEntry(JText::_('RST_KNOWLEDGEBASE'), 'index.php?option=com_rsticketspro&view=knowledgebase');
		JSubMenuHelper::addEntry(JText::_('RST_EMAIL_MESSAGES'), 'index.php?option=com_rsticketspro&view=emails');
		JSubMenuHelper::addEntry(JText::_('RST_CONFIGURATION'), 'index.php?option=com_rsticketspro&view=configuration', true);
		$mainframe->triggerEvent('onAfterTicketsMenu');
		JSubMenuHelper::addEntry(JText::_('RST_UPDATES'), 'index.php?option=com_rsticketspro&view=updates');
		
		JToolBarHelper::apply();
		JToolBarHelper::save();
		JToolBarHelper::cancel();
			
		$config = RSTicketsProHelper::getConfig();		
		$this->assignRef('config', $config);
		
		// General
		$lists['allow_rich_editor'] = JHTML::_('select.booleanlist','allow_rich_editor','class="inputbox"',$config->allow_rich_editor);
		$lists['show_kb_search'] = JHTML::_('select.booleanlist','show_kb_search','class="inputbox"',$config->show_kb_search);
		$lists['show_signature'] = JHTML::_('select.booleanlist','show_signature','class="inputbox"',$config->show_signature);
		$lists['rsticketspro_link'] = JHTML::_('select.booleanlist','rsticketspro_link','class="inputbox"',$config->rsticketspro_link);
		$lists['calculate_itemids'] = JHTML::_('select.booleanlist','calculate_itemids','class="inputbox"',$config->calculate_itemids);
		//$lists['css_inherit'] = JHTML::_('select.booleanlist','css_inherit','class="inputbox" onclick="rst_enable_designs(this.value)"',$config->css_inherit);
		
		$designs = $this->get('designs');
		//$lists['css_design'] = JHTML::_('select.genericlist', $designs, 'css_design', 'class="inputbox"'.($config->css_inherit ? ' disabled="disabled"' : ''), 'value', 'text', $config->css_design);
		$lists['staff_force_departments'] = JHTML::_('select.booleanlist','staff_force_departments','class="inputbox"',$config->staff_force_departments);
		
		// Tickets
		$rsticketspro_add_tickets = array(
			JHTML::_('select.option', '1', JText::_('RST_EVERYONE')),
			JHTML::_('select.option', '0', JText::_('RST_REGISTERED'))
		);
		$lists['rsticketspro_add_tickets'] = JHTML::_('select.genericlist', $rsticketspro_add_tickets, 'rsticketspro_add_tickets', '', 'value', 'text', $config->rsticketspro_add_tickets);
		$lists['show_email_link'] = JHTML::_('select.booleanlist','show_email_link','class="inputbox"',$config->show_email_link);
		$lists['show_ticket_info'] = JHTML::_('select.booleanlist','show_ticket_info','class="inputbox"',$config->show_ticket_info);
		$lists['show_ticket_voting'] = JHTML::_('select.booleanlist','show_ticket_voting','class="inputbox"',$config->show_ticket_voting);
		$lists['allow_ticket_closing'] = JHTML::_('select.booleanlist','allow_ticket_closing','class="inputbox"',$config->allow_ticket_closing);
		$lists['allow_ticket_reopening'] = JHTML::_('select.booleanlist','allow_ticket_reopening','class="inputbox"',$config->allow_ticket_reopening);
		$ticket_view = array(
			JHTML::_('select.option', 'plain', JText::_('RST_TICKET_VIEW_PLAIN')),
			JHTML::_('select.option', 'tabbed', JText::_('RST_TICKET_VIEW_TABBED'))
		);
		$lists['ticket_view'] = JHTML::_('select.genericlist', $ticket_view, 'ticket_view', 'class="inputbox"', 'value', 'text', $config->ticket_view);
		$ticket_viewing_history = array(
			JHTML::_('select.option', 0, JText::_('RST_TICKET_VIEWING_HISTORY_DISABLE')),
			JHTML::_('select.option', 1, JText::_('RST_TICKET_VIEWING_HISTORY_STAFF')),
			JHTML::_('select.option', 2, JText::_('RST_TICKET_VIEWING_HISTORY_CUSTOMER'))
		);
		$lists['ticket_viewing_history'] = JHTML::_('select.genericlist', $ticket_viewing_history, 'ticket_viewing_history', 'class="inputbox"', 'value', 'text', $config->ticket_viewing_history);
		$user_info = array(
			JHTML::_('select.option', 'name', JText::_('RST_NAME')),
			JHTML::_('select.option', 'username', JText::_('RST_USERNAME')),
			JHTML::_('select.option', 'email', JText::_('RST_EMAIL'))
		);
		$lists['show_user_info'] = JHTML::_('select.genericlist', $user_info, 'show_user_info', 'class="inputbox"', 'value', 'text', $config->show_user_info);
		// Messages direction
		$items = array(
			JHTML::_('select.option', 'ASC', JText::_('RST_MESSAGES_ASC')),
			JHTML::_('select.option', 'DESC', JText::_('RST_MESSAGES_DESC'))
		);
		$lists['messages_direction'] = JHTML::_('select.genericlist', $items, 'messages_direction', 'class="inputbox"', 'value', 'text', $config->messages_direction);
		$lists['color_whole_ticket'] = JHTML::_('select.booleanlist', 'color_whole_ticket', 'class="inputbox"', $config->color_whole_ticket);
		
		// Avatars
		$avatars = $this->get('avatarsavailable');
		$avatars_array = array(JHTML::_('select.option', '', JText::_('RST_NO_AVATARS_COMPONENT'),'value', 'text'));
		foreach ($avatars as $component => $enabled)
			$avatars_array[] = JHTML::_('select.option', $component, JText::_('RST_'.strtoupper($component)),'value', 'text', $enabled ? false : true);
		$lists['avatars'] = JHTML::_('select.genericlist', $avatars_array, 'avatars', '', 'value', 'text', $config->avatars);
		
		// CAPTCHA
		$captcha = array(
				JHTML::_('select.option', 0, JText::_('No')),
				JHTML::_('select.option', 1, JText::_('RST_USE_BUILTIN_CAPTCHA')),
				JHTML::_('select.option', 2, JText::_('RST_USE_RECAPTCHA'))
			);
		$lists['captcha_enabled'] = JHTML::_('select.genericlist', $captcha, 'captcha_enabled', 'class="inputbox" onchange="rst_captcha_enable(this.value);" onclick="rst_captcha_enable(this.value);"', 'value', 'text', $config->captcha_enabled);
		
		$lists['captcha_enabled_for'] = '';
		$captcha_enabled_for = explode(',', $config->captcha_enabled_for);		
		$lists['captcha_enabled_for'] .= '<input type="checkbox" '.($captcha_enabled_for[0] ? 'checked="checked"' : '').' '.($config->captcha_enabled ? '' : 'disabled="disabled"').' name="captcha_enabled_for_unregistered" value="1" id="captcha_enabled_for0" /> <label for="captcha_enabled_for0">'.JText::_('RST_CAPTCHA_UNREGISTERED').'</label>';
		$lists['captcha_enabled_for'] .= '<input type="checkbox" '.($captcha_enabled_for[1] ? 'checked="checked"' : '').' '.($config->captcha_enabled ? '' : 'disabled="disabled"').' name="captcha_enabled_for_customers" value="1" id="captcha_enabled_for1" /> <label for="captcha_enabled_for1">'.JText::_('RST_CAPTCHA_CUSTOMERS').'</label>';
		$lists['captcha_enabled_for'] .= '<input type="checkbox" '.($captcha_enabled_for[2] ? 'checked="checked"' : '').' '.($config->captcha_enabled ? '' : 'disabled="disabled"').' name="captcha_enabled_for_staff" value="1" id="captcha_enabled_for2" /> <label for="captcha_enabled_for2">'.JText::_('RST_CAPTCHA_STAFF').'</label>';
		
		$lists['captcha_lines'] = JHTML::_('select.booleanlist','captcha_lines','class="inputbox"'.($config->captcha_enabled != 1 ? ' disabled="disabled"' : ''),$config->captcha_lines);
		$lists['captcha_case_sensitive'] = JHTML::_('select.booleanlist','captcha_case_sensitive','class="inputbox"'.($config->captcha_enabled != 1 ? ' disabled="disabled"' : ''),$config->captcha_case_sensitive);
		
		$themes = array(
			JHTML::_('select.option', 'red', JText::_('RST_RECAPTCHA_THEME_RED')),
			JHTML::_('select.option', 'white', JText::_('RST_RECAPTCHA_THEME_WHITE')),
			JHTML::_('select.option', 'blackglass', JText::_('RST_RECAPTCHA_THEME_BLACKGLASS')),
			JHTML::_('select.option', 'clean', JText::_('RST_RECAPTCHA_THEME_CLEAN'))
		);
		$lists['recaptcha_theme'] = JHTML::_('select.genericlist', $themes, 'recaptcha_theme', 'class="inputbox"'.($config->captcha_enabled != 2 ? ' disabled="disabled"' : ''), 'value', 'text', $config->recaptcha_theme);
		
		// Email
		$lists['email_use_global'] = JHTML::_('select.booleanlist','email_use_global','class="inputbox" onclick="rst_email_enable(this.value)"',$config->email_use_global);
		
		// Autoclose
		$lists['autoclose_enabled'] = JHTML::_('select.booleanlist','autoclose_enabled','class="inputbox" onclick="rst_autoclose_enable(this.value);"',$config->autoclose_enabled);
		
		// Comments
		$lists['kb_comments'] = JHTML::_('select.genericlist', $this->get('commentoptions'), 'kb_comments', 'class="inputbox"', 'value', 'text', $config->kb_comments);
		
		// Predefined Subjects
		$lists['allow_predefined_subjects'] = JHTML::_('select.booleanlist','allow_predefined_subjects','class="inputbox"',$config->allow_predefined_subjects);
		
		// Time Spent
		$lists['enable_time_spent'] = JHTML::_('select.booleanlist','enable_time_spent','class="inputbox" onclick="rst_time_spent_enable(this.value);"',$config->enable_time_spent);
		$units = array(
			JHTML::_('select.option', 'm', JText::_('RST_TIME_UNIT_MINUTES')),
			JHTML::_('select.option', 'h', JText::_('RST_TIME_UNIT_HOURS')),
			JHTML::_('select.option', 'd', JText::_('RST_TIME_UNIT_DAYS'))
		);
		$lists['time_spent_unit'] = JHTML::_('select.genericlist', $units, 'time_spent_unit', 'class="inputbox"'.(!$config->enable_time_spent ? ' disabled="disabled"' : ''), 'value', 'text', $config->time_spent_unit);
		
		$params = array();
		$params['startOffset'] = JRequest::getInt('tabposition', 0);
		$tabs =& JPane::getInstance('Tabs', $params, true);
		$this->assignRef('tabs', $tabs);
		
		$this->assignRef('editor', JFactory::getEditor());
		
		$this->assignRef('lists', $lists);
		
		parent::display($tpl);
	}
}