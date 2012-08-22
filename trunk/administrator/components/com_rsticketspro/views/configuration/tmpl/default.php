<?php
/**
* @version 2.0.0
* @package RSTickets! Pro 2.0.0
* @copyright (C) 2010 www.rsjoomla.com
* @license GPL, http://www.gnu.org/licenses/gpl-2.0.html
*/

defined('_JEXEC') or die('Restricted access');

JHTML::_('behavior.tooltip');
?>
<form action="<?php echo JRoute::_('index.php?option=com_rsticketspro&view=configuration'); ?>" method="post" name="adminForm" id="adminForm">
<?php
echo $this->tabs->startPane('configuration-pane');

echo $this->tabs->startPanel(JText::_('RST_GENERAL'), 'general');
?>
<div class="col100">
	<fieldset class="adminform">
		<table class="admintable">
		<tr>
			<td width="300" style="width: 300px;" align="right" class="key">
				<span class="hasTip" title="<?php echo JText::_('RST_YOUR_CODE'); ?>">
				<?php echo JText::_('RST_YOUR_CODE'); ?>
				</span>
			</td>
			<td>
				<input class="text_area" type="text" name="global_register_code" id="global_register_code" size="35" value="<?php echo $this->escape($this->config->global_register_code); ?>" />
			</td>
		</tr>
		<tr>
			<td width="300" style="width: 300px;" align="right" class="key">
				<span class="hasTip" title="<?php echo JText::_('RST_DATE_TIME_DESC'); ?>">
				<?php echo JText::_('RST_DATE_TIME'); ?>
				</span>
			</td>
			<td>
				<input class="text_area" type="text" name="date_format" id="date_format" size="35" value="<?php echo $this->escape($this->config->date_format); ?>" />
			</td>
		</tr>
		<tr>
			<td width="300" style="width: 300px;" align="right" class="key">
				<span class="hasTip" title="<?php echo JText::_('RST_DATE_TIME_NOTIME_DESC'); ?>">
				<?php echo JText::_('RST_DATE_TIME_NOTIME'); ?>
				</span>
			</td>
			<td>
				<input class="text_area" type="text" name="date_format_notime" id="date_format_notime" size="35" value="<?php echo $this->escape($this->config->date_format_notime); ?>" />
			</td>
		</tr>
		<tr>
			<td width="300" style="width: 300px;" align="right" class="key">
				<span class="hasTip" title="<?php echo JText::_('RST_RSTICKETSPRO_LINK_DESC'); ?>">
				<?php echo JText::_('RST_RSTICKETSPRO_LINK'); ?>
				</span>
			</td>
			<td>
				<?php echo $this->lists['rsticketspro_link']; ?>
			</td>
		</tr>
		<tr>
			<td width="300" style="width: 300px;" align="right" class="key">
				<span class="hasTip" title="<?php echo JText::_('RST_ALLOW_RICH_EDITOR_DESC'); ?>">
				<?php echo JText::_('RST_ALLOW_RICH_EDITOR'); ?>
				</span>
			</td>
			<td>
				<?php echo $this->lists['allow_rich_editor']; ?>
			</td>
		</tr>
		<tr>
			<td width="300" style="width: 300px;" align="right" class="key">
				<span class="hasTip" title="<?php echo JText::_('RST_SHOW_KB_SEARCH_DESC'); ?>">
				<?php echo JText::_('RST_SHOW_KB_SEARCH'); ?>
				</span>
			</td>
			<td>
				<?php echo $this->lists['show_kb_search']; ?>
			</td>
		</tr>
		<tr>
			<td width="300" style="width: 300px;" align="right" class="key">
				<span class="hasTip" title="<?php echo JText::_('RST_SHOW_SIGNATURE_DESC'); ?>">
				<?php echo JText::_('RST_SHOW_SIGNATURE'); ?>
				</span>
			</td>
			<td>
				<?php echo $this->lists['show_signature']; ?>
			</td>
		</tr>
		<tr>
			<td width="300" style="width: 300px;" align="right" class="key">
				<span class="hasTip" title="<?php echo JText::_('RST_REDIRECT_AFTER_SUBMIT_DESC'); ?>">
				<?php echo JText::_('RST_REDIRECT_AFTER_SUBMIT'); ?>
				</span>
			</td>
			<td>
				<input class="text_area" type="text" name="submit_redirect" id="submit_redirect" size="35" value="<?php echo $this->escape($this->config->submit_redirect); ?>" />
			</td>
		</tr>
		<tr>
			<td width="300" style="width: 300px;" align="right" class="key">
				<span class="hasTip" title="<?php echo JText::_('RST_STAFF_MEMBERS_FORCE_DEPARTMENTS_DESC'); ?>">
				<?php echo JText::_('RST_STAFF_MEMBERS_FORCE_DEPARTMENTS'); ?>
				</span>
			</td>
			<td>
				<?php echo $this->lists['staff_force_departments']; ?>
			</td>
		</tr>
		<tr>
			<td width="300" style="width: 300px;" align="right" class="key">
				<span class="hasTip" title="<?php echo JText::_('RST_CALCULATE_ITEMIDS_DESC'); ?>">
				<?php echo JText::_('RST_CALCULATE_ITEMIDS'); ?>
				</span>
			</td>
			<td>
				<?php echo $this->lists['calculate_itemids']; ?>
			</td>
		</tr>
		</table>
	</fieldset>
</div>
<div class="clr"></div>
<?php
echo $this->tabs->endPanel();

echo $this->tabs->startPanel(JText::_('RST_MESSAGES'), 'messages');
?>
<div class="col100">
	<fieldset class="adminform">
		<table class="admintable">
		<tr>
			<td width="300" style="width: 300px;" align="right" class="key" valign="top">
				<span class="hasTip" title="<?php echo JText::_('RST_GLOBAL_MESSAGE_DESC'); ?>">
				<?php echo JText::_('RST_GLOBAL_MESSAGE'); ?>
				</span>
			</td>
			<td>
				<?php echo $this->editor->display('global_message', $this->config->global_message,500,250,70,10); ?>
			</td>
		</tr>
		<tr>
			<td width="300" style="width: 300px;" align="right" class="key" valign="top">
				<span class="hasTip" title="<?php echo JText::_('RST_SUBMIT_MESSAGE_DESC'); ?>">
				<?php echo JText::_('RST_SUBMIT_MESSAGE'); ?>
				</span>
			</td>
			<td>
				<?php echo $this->editor->display('submit_message', $this->config->submit_message,500,250,70,10); ?>
			</td>
		</tr>
		</table>
	</fieldset>
</div>
<div class="clr"></div>
<?php
echo $this->tabs->endPanel();

echo $this->tabs->startPanel(JText::_('RST_TICKETS'), 'tickets');
?>
<div class="col100">
	<fieldset class="adminform">
		<legend><?php echo JText::_('RST_APPEARANCE'); ?></legend>
		<table class="admintable">
		<tr>
			<td width="300" style="width: 300px;" align="right" class="key">
				<span class="hasTip" title="<?php echo JText::_('RST_TICKET_VIEW_DESC'); ?>">
				<?php echo JText::_('RST_TICKET_VIEW'); ?>
				</span>
			</td>
			<td>
				<?php echo $this->lists['ticket_view']; ?>
			</td>
		</tr>
		<tr>
			<td width="300" style="width: 300px;" align="right" class="key">
				<span class="hasTip" title="<?php echo JText::_('RST_MESSAGES_DIRECTION_DESC'); ?>">
				<?php echo JText::_('RST_MESSAGES_DIRECTION'); ?>
				</span>
			</td>
			<td>
				<?php echo $this->lists['messages_direction']; ?>
			</td>
		</tr>
		<tr>
			<td width="300" style="width: 300px;" align="right" class="key">
				<span class="hasTip" title="<?php echo JText::_('RST_SHOW_TICKET_INFO_DESC'); ?>">
				<?php echo JText::_('RST_SHOW_TICKET_INFO'); ?>
				</span>
			</td>
			<td>
				<?php echo $this->lists['show_ticket_info']; ?>
			</td>
		</tr>
		<tr>
			<td width="300" style="width: 300px;" align="right" class="key">
				<span class="hasTip" title="<?php echo JText::_('RST_SHOW_USER_INFO_DESC'); ?>">
				<?php echo JText::_('RST_SHOW_USER_INFO'); ?>
				</span>
			</td>
			<td>
				<?php echo $this->lists['show_user_info']; ?>
			</td>
		</tr>
		<tr>
			<td width="300" style="width: 300px;" align="right" class="key">
				<span class="hasTip" title="<?php echo JText::_('RST_SHOW_EMAIL_LINK_DESC'); ?>">
				<?php echo JText::_('RST_SHOW_EMAIL_LINK'); ?>
				</span>
			</td>
			<td>
				<?php echo $this->lists['show_email_link']; ?>
			</td>
		</tr>
		<tr>
			<td width="300" style="width: 300px;" align="right" class="key">
				<span class="hasTip" title="<?php echo JText::_('RST_TICKET_VIEWING_HISTORY_DESC'); ?>">
				<?php echo JText::_('RST_TICKET_VIEWING_HISTORY'); ?>
				</span>
			</td>
			<td>
				<?php echo $this->lists['ticket_viewing_history']; ?>
			</td>
		</tr>
		<tr>
			<td width="300" style="width: 300px;" align="right" class="key">
				<span class="hasTip" title="<?php echo JText::_('RST_COLOR_WHOLE_TICKET_DESC'); ?>">
				<?php echo JText::_('RST_COLOR_WHOLE_TICKET'); ?>
				</span>
			</td>
			<td>
				<?php echo $this->lists['color_whole_ticket']; ?>
			</td>
		</tr>
		</table>
	</fieldset>
	
	<fieldset class="adminform">
		<legend><?php echo JText::_('RST_CUSTOMER_INPUT'); ?></legend>
		<table class="admintable">
		<tr>
			<td width="300" style="width: 300px;" align="right" class="key">
				<span class="hasTip" title="<?php echo JText::_('RST_RECEIVE_TICKETS_FROM_DESC'); ?>">
				<?php echo JText::_('RST_RECEIVE_TICKETS_FROM'); ?>
				</span>
			</td>
			<td>
				<?php echo $this->lists['rsticketspro_add_tickets']; ?>
			</td>
		</tr>
		<tr>
			<td width="300" style="width: 300px;" align="right" class="key">
				<span class="hasTip" title="<?php echo JText::_('RST_SHOW_TICKET_VOTING_DESC'); ?>">
				<?php echo JText::_('RST_SHOW_TICKET_VOTING'); ?>
				</span>
			</td>
			<td>
				<?php echo $this->lists['show_ticket_voting']; ?>
			</td>
		</tr>
		<tr>
			<td width="300" style="width: 300px;" align="right" class="key">
				<span class="hasTip" title="<?php echo JText::_('RST_ALLOW_TICKET_CLOSING_DESC'); ?>">
				<?php echo JText::_('RST_ALLOW_TICKET_CLOSING'); ?>
				</span>
			</td>
			<td>
				<?php echo $this->lists['allow_ticket_closing']; ?>
			</td>
		</tr>
		<tr>
			<td width="300" style="width: 300px;" align="right" class="key">
				<span class="hasTip" title="<?php echo JText::_('RST_ALLOW_TICKET_REOPENING_DESC'); ?>">
				<?php echo JText::_('RST_ALLOW_TICKET_REOPENING'); ?>
				</span>
			</td>
			<td>
				<?php echo $this->lists['allow_ticket_reopening']; ?>
			</td>
		</tr>
		</table>
	</fieldset>
	
	<fieldset class="adminform">
		<legend><?php echo JText::_('RST_PREDEFINED_SUBJECTS'); ?></legend>
		<table class="admintable">
		<tr>
			<td width="300" style="width: 300px;" align="right" class="key">
				<span class="hasTip" title="<?php echo JText::_('RST_USE_PREDEFINED_SUBJECTS_DESC'); ?>">
				<?php echo JText::_('RST_USE_PREDEFINED_SUBJECTS'); ?>
				</span>
			</td>
			<td>
				<?php echo $this->lists['allow_predefined_subjects']; ?>
			</td>
		</tr>
		</table>
	</fieldset>
	
	<fieldset class="adminform">
		<legend><?php echo JText::_('RST_TIME_SPENT'); ?></legend>
		<table class="admintable">
		<tr>
			<td width="300" style="width: 300px;" align="right" class="key">
				<span class="hasTip" title="<?php echo JText::_('RST_ENABLE_TIME_SPENT_DESC'); ?>">
				<?php echo JText::_('RST_ENABLE_TIME_SPENT'); ?>
				</span>
			</td>
			<td>
				<?php echo $this->lists['enable_time_spent']; ?>
			</td>
		</tr>
		<tr>
			<td width="300" style="width: 300px;" align="right" class="key">
				<span class="hasTip" title="<?php echo JText::_('RST_TIME_SPENT_UNIT_DESC'); ?>">
				<?php echo JText::_('RST_TIME_SPENT_UNIT'); ?>
				</span>
			</td>
			<td>
				<?php echo $this->lists['time_spent_unit']; ?>
			</td>
		</tr>
		</table>
	</fieldset>
</div>
<div class="clr"></div>
<?php
echo $this->tabs->endPanel();

echo $this->tabs->startPanel(JText::_('RST_AVATARS'), 'avatars');
?>
<div class="col100">
	<fieldset class="adminform">
		<table class="admintable">
		<tr>
			<td width="300" style="width: 300px;" align="right" class="key">
				<span class="hasTip" title="<?php echo JText::_('RST_AVATARS_ENABLE_DESC'); ?>">
				<?php echo JText::_('RST_AVATARS_ENABLE'); ?>
				</span>
			</td>
			<td>
				<?php echo $this->lists['avatars']; ?>
			</td>
		</tr>
		</table>
	</fieldset>
</div>
<div class="clr"></div>
<?php
echo $this->tabs->endPanel();

echo $this->tabs->startPanel(JText::_('RST_CAPTCHA'), 'captcha');
?>
<div class="col100">
	<fieldset class="adminform">
		<table class="admintable">
		<tr>
			<td width="300" style="width: 300px;" align="right" class="key">
				<span class="hasTip" title="<?php echo JText::_('RST_CAPTCHA_ENABLE_DESC'); ?>">
				<?php echo JText::_('RST_CAPTCHA_ENABLE'); ?>
				</span>
			</td>
			<td>
				<?php echo $this->lists['captcha_enabled']; ?>
			</td>
		</tr>
		<tr>
			<td width="300" style="width: 300px;" align="right" class="key">
				<span class="hasTip" title="<?php echo JText::_('RST_CAPTCHA_ENABLED_FOR_DESC'); ?>">
				<?php echo JText::_('RST_CAPTCHA_ENABLED_FOR'); ?>
				</span>
			</td>
			<td>
				<?php echo $this->lists['captcha_enabled_for']; ?>
			</td>
		</tr>
		<tr>
			<td width="300" style="width: 300px;" align="right" class="key">
				<span class="hasTip" title="<?php echo JText::_('RST_CAPTCHA_CHARACTERS_DESC'); ?>">
				<?php echo JText::_('RST_CAPTCHA_CHARACTERS'); ?>
				</span>
			</td>
			<td>
				<input class="text_area" type="text" name="captcha_characters" id="captcha_characters" <?php echo $this->config->captcha_enabled != 1 ? ' disabled="disabled"' : ''; ?> size="35" value="<?php echo $this->escape($this->config->captcha_characters); ?>" />
			</td>
		</tr>
		<tr>
			<td width="300" style="width: 300px;" align="right" class="key">
				<span class="hasTip" title="<?php echo JText::_('RST_CAPTCHA_LINES_DESC'); ?>">
				<?php echo JText::_('RST_CAPTCHA_LINES'); ?>
				</span>
			</td>
			<td>
				<?php echo $this->lists['captcha_lines']; ?>
			</td>
		</tr>
		<tr>
			<td width="300" style="width: 300px;" align="right" class="key">
				<span class="hasTip" title="<?php echo JText::_('RST_CAPTCHA_CASE_SENSITIVE_DESC'); ?>">
				<?php echo JText::_('RST_CAPTCHA_CASE_SENSITIVE'); ?>
				</span>
			</td>
			<td>
				<?php echo $this->lists['captcha_case_sensitive']; ?>
			</td>
		</tr>
		<tr>
			<td width="300" style="width: 300px;" align="right" class="key">
				<span class="hasTip" title="<?php echo JText::_('RST_RECAPTCHA_PUBLIC_KEY_DESC'); ?>">
				<?php echo JText::_('RST_RECAPTCHA_PUBLIC_KEY'); ?>
				</span>
			</td>
			<td>
				<input class="text_area" type="text" name="recaptcha_public_key" id="recaptcha_public_key" <?php echo $this->config->captcha_enabled != 2 ? ' disabled="disabled"' : ''; ?> size="35" value="<?php echo $this->escape($this->config->recaptcha_public_key); ?>" />
			</td>
		</tr>
		<tr>
			<td width="300" style="width: 300px;" align="right" class="key">
				<span class="hasTip" title="<?php echo JText::_('RST_RECAPTCHA_PRIVATE_KEY_DESC'); ?>">
				<?php echo JText::_('RST_RECAPTCHA_PRIVATE_KEY'); ?>
				</span>
			</td>
			<td>
				<input class="text_area" type="text" name="recaptcha_private_key" id="recaptcha_private_key" <?php echo $this->config->captcha_enabled != 2 ? ' disabled="disabled"' : ''; ?> size="35" value="<?php echo $this->escape($this->config->recaptcha_private_key); ?>" />
			</td>
		</tr>
		<tr>
			<td width="300" style="width: 300px;" align="right" class="key">
				<span class="hasTip" title="<?php echo JText::_('RST_RECAPTCHA_THEME_DESC'); ?>">
				<?php echo JText::_('RST_RECAPTCHA_THEME'); ?>
				</span>
			</td>
			<td>
				<?php echo $this->lists['recaptcha_theme']; ?>
			</td>
		</tr>
		</table>
	</fieldset>
</div>
<div class="clr"></div>
<?php
echo $this->tabs->endPanel();

echo $this->tabs->startPanel(JText::_('RST_EMAIL'), 'email');
?>
<div class="col100">
	<fieldset class="adminform">
		<table class="admintable">
		<tr>
			<td width="300" style="width: 300px;" align="right" class="key">
				<span class="hasTip" title="<?php echo JText::_('RST_EMAIL_USE_GLOBAL_DESC'); ?>">
				<?php echo JText::_('RST_EMAIL_USE_GLOBAL'); ?>
				</span>
			</td>
			<td>
				<?php echo $this->lists['email_use_global']; ?>
			</td>
		</tr>
		<tr>
			<td width="300" style="width: 300px;" align="right" class="key">
				<span class="hasTip" title="<?php echo JText::_('RST_EMAIL_FROM_EMAIL_DESC'); ?>">
				<?php echo JText::_('RST_EMAIL_FROM_EMAIL'); ?>
				</span>
			</td>
			<td>
				<input class="text_area" type="text" name="email_address" id="email_address" <?php echo $this->config->email_use_global ? ' disabled="disabled"' : ''; ?> size="35" value="<?php echo $this->escape($this->config->email_address); ?>" />
			</td>
		</tr>
		<tr>
			<td width="300" style="width: 300px;" align="right" class="key">
				<span class="hasTip" title="<?php echo JText::_('RST_EMAIL_FROM_FULLNAME_DESC'); ?>">
				<?php echo JText::_('RST_EMAIL_FROM_FULLNAME'); ?>
				</span>
			</td>
			<td>
				<input class="text_area" type="text" name="email_address_fullname" id="email_address_fullname" <?php echo $this->config->email_use_global ? ' disabled="disabled"' : ''; ?> size="35" value="<?php echo $this->escape($this->config->email_address_fullname); ?>" />
			</td>
		</tr>
		<tr>
			<td width="300" style="width: 300px;" align="right" class="key">
				<span class="hasTip" title="<?php echo JText::_('RST_REPLY_ABOVE_DESC'); ?>">
				<?php echo JText::_('RST_REPLY_ABOVE'); ?>
				</span>
			</td>
			<td>
				<input class="text_area" type="text" name="reply_above" id="reply_above" size="35" value="<?php echo $this->escape($this->config->reply_above); ?>" />
			</td>
		</tr>
		<tr>
			<td width="300" style="width: 300px;" align="right" class="key">
				<span class="hasTip" title="<?php echo JText::_('RST_CONFIG_CUSTOMER_ITEMID_DESC'); ?>">
				<?php echo JText::_('RST_CONFIG_CUSTOMER_ITEMID'); ?>
				</span>
			</td>
			<td>
				<input class="text_area" type="text" name="customer_itemid" id="customer_itemid" size="35" value="<?php echo $this->escape($this->config->customer_itemid); ?>" />
			</td>
		</tr>
		<tr>
			<td width="300" style="width: 300px;" align="right" class="key">
				<span class="hasTip" title="<?php echo JText::_('RST_CONFIG_STAFF_ITEMID_DESC'); ?>">
				<?php echo JText::_('RST_CONFIG_STAFF_ITEMID'); ?>
				</span>
			</td>
			<td>
				<input class="text_area" type="text" name="staff_itemid" id="staff_itemid" size="35" value="<?php echo $this->escape($this->config->staff_itemid); ?>" />
			</td>
		</tr>
		</table>
	</fieldset>
</div>
<div class="clr"></div>
<?php
echo $this->tabs->endPanel();

echo $this->tabs->startPanel(JText::_('RST_AUTOCLOSE'), 'autoclose');
?>
<div class="col100">
	<fieldset class="adminform">
		<table class="admintable">
		<tr>
			<td width="300" style="width: 300px;" align="right" class="key">
				<span class="hasTip" title="<?php echo JText::_('RST_AUTOCLOSE_ENABLE_DESC'); ?>">
				<?php echo JText::_('RST_AUTOCLOSE_ENABLE'); ?>
				</span>
			</td>
			<td>
				<?php echo $this->lists['autoclose_enabled']; ?>
			</td>
		</tr>
		<tr>
			<td width="300" style="width: 300px;" align="right" class="key">
				<span class="hasTip" title="<?php echo JText::_('RST_AUTOCLOSE_CHECK_DESC'); ?>">
				<?php echo JText::_('RST_AUTOCLOSE_CHECK'); ?>
				</span>
			</td>
			<td>
				<input class="text_area" type="text" name="autoclose_cron_interval" id="autoclose_cron_interval" <?php echo $this->config->autoclose_enabled ? '' : ' disabled="disabled"'; ?> size="35" value="<?php echo $this->escape($this->config->autoclose_cron_interval); ?>" />
			</td>
		</tr>
		<tr>
			<td width="300" style="width: 300px;" align="right" class="key">
				<span class="hasTip" title="<?php echo JText::_('RST_AUTOCLOSE_DAYS_STATUS_DESC'); ?>">
				<?php echo JText::_('RST_AUTOCLOSE_DAYS_STATUS'); ?>
				</span>
			</td>
			<td>
				<input class="text_area" type="text" name="autoclose_email_interval" id="autoclose_email_interval" <?php echo $this->config->autoclose_enabled ? '' : ' disabled="disabled"'; ?> size="35" value="<?php echo $this->escape($this->config->autoclose_email_interval); ?>" />
			</td>
		</tr>
		<tr>
			<td width="300" style="width: 300px;" align="right" class="key">
				<span class="hasTip" title="<?php echo JText::_('RST_AUTOCLOSE_DAYS_CLOSED_DESC'); ?>">
				<?php echo JText::_('RST_AUTOCLOSE_DAYS_CLOSED'); ?>
				</span>
			</td>
			<td>
				<input class="text_area" type="text" name="autoclose_interval" id="autoclose_interval" <?php echo $this->config->autoclose_enabled ? '' : ' disabled="disabled"'; ?> size="35" value="<?php echo $this->escape($this->config->autoclose_interval); ?>" />
			</td>
		</tr>
		</table>
	</fieldset>
</div>
<div class="clr"></div>
<?php
echo $this->tabs->endPanel();

echo $this->tabs->startPanel(JText::_('RST_NOTICES'), 'notices');
?>
<div class="col100">
	<fieldset class="adminform">
		<table class="admintable">
		<tr>
			<td width="300" style="width: 300px;" align="right" class="key">
				<span class="hasTip" title="<?php echo JText::_('RST_NOTICES_EMAIL_DESC'); ?>">
				<?php echo JText::_('RST_NOTICES_EMAIL'); ?>
				</span>
			</td>
			<td>
				<input class="text_area" type="text" name="notice_email_address" size="35" value="<?php echo $this->escape($this->config->notice_email_address); ?>" />
			</td>
		</tr>
		<tr>
			<td width="300" style="width: 300px;" align="right" class="key">
				<span class="hasTip" title="<?php echo JText::_('RST_NOTICES_NO_REPLIES_DESC'); ?>">
				<?php echo JText::_('RST_NOTICES_NO_REPLIES'); ?>
				</span>
			</td>
			<td>
				<input class="text_area" type="text" name="notice_max_replies_nr" size="35" value="<?php echo $this->escape($this->config->notice_max_replies_nr); ?>" />
			</td>
		</tr>
		<tr>
			<td width="300" style="width: 300px;" align="right" class="key">
				<span class="hasTip" title="<?php echo JText::_('RST_NOTICES_NO_REPLIES_WITH_NO_RESPONSE_DESC'); ?>">
				<?php echo JText::_('RST_NOTICES_NO_REPLIES_WITH_NO_RESPONSE'); ?>
				</span>
			</td>
			<td>
				<input class="text_area" type="text" name="notice_replies_with_no_response_nr" size="35" value="<?php echo $this->escape($this->config->notice_replies_with_no_response_nr); ?>" />
			</td>
		</tr>
		<tr>
			<td width="300" style="width: 300px;" align="right" class="key">
				<span class="hasTip" title="<?php echo JText::_('RST_NOTICES_TRIGGERED_KEYWORDS_DESC'); ?>">
				<?php echo JText::_('RST_NOTICES_TRIGGERED_KEYWORDS'); ?>
				</span>
			</td>
			<td>
				<textarea name="notice_not_allowed_keywords" cols="50" rows="8"><?php echo $this->escape($this->config->notice_not_allowed_keywords); ?></textarea>
			</td>
		</tr>
		</table>
	</fieldset>
</div>
<?php
echo $this->tabs->endPanel();

echo $this->tabs->startPanel(JText::_('RST_KNOWLEDGEBASE'), 'knowledgebase');
?>
<div class="col100">
	<fieldset class="adminform">
		<table class="admintable">
		<tr>
			<td width="300" style="width: 300px;" align="right" class="key">
				<span class="hasTip" title="<?php echo JText::_('RST_KB_HOT_HITS_DESC'); ?>">
				<?php echo JText::_('RST_KB_HOT_HITS'); ?>
				</span>
			</td>
			<td>
				<input class="text_area" type="text" name="kb_hot_hits" id="kb_hot_hits" size="35" value="<?php echo $this->escape($this->config->kb_hot_hits); ?>" />
			</td>
		</tr>
		<tr>
			<td width="300" style="width: 300px;" align="right" class="key">
				<span class="hasTip" title="<?php echo JText::_('RST_KB_COMMENTS_DESC'); ?>">
				<?php echo JText::_('RST_KB_COMMENTS'); ?>
				</span>
			</td>
			<td>
				<?php echo $this->lists['kb_comments']; ?>
			</td>
		</tr>
		</table>
	</fieldset>
</div>
<?php
echo $this->tabs->endPanel();

echo $this->tabs->endPane();
?>

<?php echo JHTML::_('form.token'); ?>
<input type="hidden" name="option" value="com_rsticketspro" />
<input type="hidden" name="view" value="configuration" />
<input type="hidden" name="task" value="" />
<input type="hidden" name="controller" value="configuration" />
<input type="hidden" name="tabposition" id="tabposition" value="0" />
</form>

<script type="text/javascript">
function rst_captcha_enable(what)
{
	$('captcha_enabled_for0').disabled = true;
	$('captcha_enabled_for1').disabled = true;
	$('captcha_enabled_for2').disabled = true;
	$('captcha_characters').disabled = true;
	$('captcha_lines0').disabled = true;
	$('captcha_lines1').disabled = true;
	$('captcha_case_sensitive0').disabled = true;
	$('captcha_case_sensitive1').disabled = true;
		
	$('recaptcha_public_key').disabled = true;
	$('recaptcha_private_key').disabled = true;
	$('recaptcha_theme').disabled = true;
		
	if (what == 1)
	{
		$('captcha_enabled_for0').disabled = false;
		$('captcha_enabled_for1').disabled = false;
		$('captcha_enabled_for2').disabled = false;
		$('captcha_characters').disabled = false;
		$('captcha_lines0').disabled = false;
		$('captcha_lines1').disabled = false;
		$('captcha_case_sensitive0').disabled = false;
		$('captcha_case_sensitive1').disabled = false;
	}
	else if (what == 2)
	{
		$('captcha_enabled_for0').disabled = false;
		$('captcha_enabled_for1').disabled = false;
		$('captcha_enabled_for2').disabled = false;
		
		$('recaptcha_public_key').disabled = false;
		$('recaptcha_private_key').disabled = false;
		$('recaptcha_theme').disabled = false;
	}
}

function rst_email_enable(what)
{
	if (what == 1)
	{
		$('email_address').disabled = true;
		$('email_address_fullname').disabled = true;
	}
	else
	{
		$('email_address').disabled = false;
		$('email_address_fullname').disabled = false;
	}
}

function rst_autoclose_enable(what)
{
	if (what == 1)
	{
		$('autoclose_cron_interval').disabled = false;
		$('autoclose_email_interval').disabled = false;
		$('autoclose_interval').disabled = false;
	}
	else
	{
		$('autoclose_cron_interval').disabled = true;
		$('autoclose_email_interval').disabled = true;
		$('autoclose_interval').disabled = true;
	}
}

function rst_enable_designs(what)
{
	if (what == 1)
		$('css_design').disabled = true;
	else
		$('css_design').disabled = false;
}

function rst_time_spent_enable(what)
{
	if (what == 1)
		$('time_spent_unit').disabled = false;
	else
		$('time_spent_unit').disabled = true;
}

function submitbutton(pressbutton)
{
	var form = document.adminForm;
	
	if (pressbutton == 'cancel')
	{
		submitform(pressbutton);
		return;
	}
	
	var dt = $('configuration-pane').getElements('dt');
	for (var i=0; i<dt.length; i++)
	{
		if (dt[i].className == 'open')
			$('tabposition').value = i;
	}
	submitform(pressbutton);
}

<?php if (RSTicketsProHelper::isJ16()) { ?>
	Joomla.submitbutton = submitbutton;
<?php } ?>
</script>

<?php
//keep session alive while editing
JHTML::_('behavior.keepalive');
?>