<?php
/**
* @version 2.0.0
* @package RSTickets! Pro 2.0.0
* @copyright (C) 2010 www.rsjoomla.com
* @license GPL, http://www.gnu.org/licenses/gpl-2.0.html
*/

defined( '_JEXEC' ) or die( 'Restricted access' );

$db = &JFactory::getDBO();

require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_rsticketspro'.DS.'helpers'.DS.'rsticketspro.php');

// Get a new installer
$plg_installer = new JInstaller();
$plg_installer->install($this->parent->getPath('source').DS.'plg_system');

if (!RSTicketsProHelper::isJ16())
{
	$db->setQuery("UPDATE #__plugins SET published=1 WHERE `element`='rsticketspro' AND `folder`='system'");
	$db->query();
}
else
{
	$db->setQuery("UPDATE #__extensions SET `enabled`='1' WHERE `type`='plugin' AND `element`='rsticketspro' AND `folder`='system'");
	$db->query();
}

$plg_installer->install($this->parent->getPath('source').DS.'plg_search');
if (!RSTicketsProHelper::isJ16())
{
	$db->setQuery("UPDATE #__plugins SET published=1 WHERE `element`='rsticketsprocontent' AND `folder`='search'");
	$db->query();
}
else
{
	$db->setQuery("UPDATE #__extensions SET `enabled`='1' WHERE `type`='plugin' AND `element`='rsticketsprocontent' AND `folder`='search'");
	$db->query();
}

$plg_installer->install($this->parent->getPath('source').DS.'plg_user');
if (!RSTicketsProHelper::isJ16())
{
	$db->setQuery("UPDATE #__plugins SET published=1, ordering=900 WHERE `element`='rsticketspro' AND `folder`='user'");
	$db->query();
	
	require_once(JPATH_SITE.DS.'plugins'.DS.'user'.DS.'rsticketspro.php');
	plgUserRSTicketsPro::onLoginUser($user=array(), $options=array());
}
else
{
	$db->setQuery("UPDATE #__extensions SET `enabled`='1' WHERE `type`='plugin' AND `element`='rsticketspro' AND `folder`='user'");
	$db->query();
	
	require_once(JPATH_SITE.DS.'plugins'.DS.'user'.DS.'rsticketspro'.DS.'rsticketspro.php');
	plgUserRSTicketsPro::onLoginUser($user=array(), $options=array());
}

// R2
$db->setQuery("SELECT lang FROM #__rsticketspro_emails WHERE `message` LIKE '%option=com_rstickets%' AND `message` NOT LIKE '%option=com_rsticketspro%'");
if ($db->getNumRows($db->query()))
{
	$db->setQuery("UPDATE #__rsticketspro_emails SET `message`=REPLACE(`message`, 'option=com_rstickets', 'option=com_rsticketspro') WHERE `message` LIKE '%option=com_rstickets%' AND `message` NOT LIKE '%option=com_rsticketspro%'");
	$db->query();
}

$db->setQuery("SHOW COLUMNS FROM `#__rsticketspro_configuration` WHERE Field='value'");
$result = $db->loadObject();
if (strtolower($result->Type) != 'text')
{
	$db->setQuery("ALTER TABLE `#__rsticketspro_configuration` CHANGE `value` `value` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL");
	if (!$db->query())
	{
		$db->setQuery("ALTER TABLE `#__rsticketspro_configuration` CHANGE `value` `value` TEXT NOT NULL");
		$db->query();
	}
}

// R3
$db->setQuery("SELECT name FROM #__rsticketspro_configuration WHERE `name` IN ('css_inherit', 'css_design', 'show_email_link')");
if (!$db->loadObject())
{
	$db->setQuery("INSERT INTO #__rsticketspro_configuration SET `name`='css_inherit', `value`='1'");
	$db->query();
	
	$db->setQuery("INSERT INTO #__rsticketspro_configuration SET `name`='css_design', `value`='default.css'");
	$db->query();
	
	$db->setQuery("INSERT INTO #__rsticketspro_configuration SET `name`='show_email_link', `value`='0'");
	$db->query();
}

// R4
$db->setQuery("SHOW COLUMNS FROM `#__rsticketspro_kb_content` WHERE `Field`='from_ticket_id'");
if (!$db->loadResult())
{
	$db->setQuery("ALTER TABLE `#__rsticketspro_kb_content` ADD `from_ticket_id` INT NOT NULL AFTER `private`");
	$db->query();
}

$db->setQuery("SHOW COLUMNS FROM `#__rsticketspro_priorities` WHERE `Field`='bg_color'");
if (!$db->loadResult())
{
	$db->setQuery("ALTER TABLE `#__rsticketspro_priorities` ADD `bg_color` VARCHAR( 7 ) NOT NULL AFTER `name` , ADD `fg_color` VARCHAR( 7 ) NOT NULL AFTER `bg_color`");
	$db->query();
}

$db->setQuery("SHOW COLUMNS FROM `#__rsticketspro_departments` WHERE `Field`='customer_attach_email'");
if (!$db->loadResult())
{
	$db->setQuery("ALTER TABLE `#__rsticketspro_departments` ADD `customer_attach_email` TINYINT( 1 ) NOT NULL DEFAULT '1' AFTER `customer_send_email`");
	$db->query();
	$db->setQuery("ALTER TABLE `#__rsticketspro_departments` ADD `staff_attach_email` TINYINT( 1 ) NOT NULL DEFAULT '1' AFTER `staff_send_email`");
	$db->query();
}

// R5
$db->setQuery("SHOW COLUMNS FROM `#__rsticketspro_custom_fields` WHERE `Field`='description'");
if (!$db->loadResult())
{
	$db->setQuery("ALTER TABLE `#__rsticketspro_custom_fields` ADD `description` TEXT NOT NULL AFTER `required`");
	$db->query();
}

$db->setQuery("SHOW COLUMNS FROM `#__rsticketspro_kb_content` WHERE `Field`='hits'");
if (!$db->loadResult())
{
	$db->setQuery("ALTER TABLE `#__rsticketspro_kb_content` ADD `hits` INT UNSIGNED NOT NULL DEFAULT '0' AFTER `from_ticket_id`, ADD `created` INT UNSIGNED NOT NULL AFTER `hits`, ADD `modified` INT UNSIGNED NOT NULL AFTER `created`");
	$db->query();
}

$db->setQuery("SHOW COLUMNS FROM `#__rsticketspro_tickets` WHERE `Field`='has_files'");
if (!$db->loadResult())
{
	$db->setQuery("ALTER TABLE `#__rsticketspro_tickets` ADD `has_files` TINYINT( 1 ) UNSIGNED NOT NULL AFTER `feedback`");
	$db->query();
	
	$db->setQuery("UPDATE #__rsticketspro_tickets SET has_files=1 WHERE id IN (SELECT ticket_id FROM #__rsticketspro_ticket_files)");
	$db->query();
}

$db->setQuery("SHOW COLUMNS FROM `#__rsticketspro_departments` WHERE `Field`='email_address'");
if (!$db->loadResult())
{
	$db->setQuery("ALTER TABLE `#__rsticketspro_departments` ADD `email_address` VARCHAR( 255 ) NOT NULL AFTER `next_number` , ADD `email_address_fullname` VARCHAR( 255 ) NOT NULL AFTER `email_address` , ADD `email_use_global` TINYINT( 1 ) UNSIGNED NOT NULL DEFAULT '1' AFTER `email_address_fullname`, ADD `upload_size` DECIMAL( 10, 2 ) UNSIGNED NOT NULL AFTER `upload_extensions`");
	$db->query();
}

// R6
$db->setQuery("SHOW COLUMNS FROM `#__rsticketspro_kb_categories` WHERE `Field`='thumb'");
if (!$db->loadResult())
{
	$db->setQuery("ALTER TABLE `#__rsticketspro_kb_categories` ADD `thumb` VARCHAR( 16 ) NOT NULL AFTER `parent_id`");
	$db->query();
}

$db->setQuery("UPDATE #__rsticketspro_configuration SET `value`='1' WHERE `name`='css_inherit'");
$db->query();

// R7
$db->setQuery("SELECT * FROM `#__rsticketspro_configuration` WHERE `name`='notice_email_address'");
if (!$db->loadResult())
{
	$db->setQuery("INSERT IGNORE INTO `#__rsticketspro_configuration` (`name`, `value`) VALUES  ('notice_email_address', ''), ('notice_max_replies_nr', '0'), ('notice_not_allowed_keywords', ''),('notice_replies_with_no_response_nr', '0')");
	$db->query();
}

$db->setQuery("SELECT * FROM `#__rsticketspro_emails` WHERE `type`='notification_max_replies_nr'");
if (!$db->loadResult())
{
	$db->setQuery("INSERT IGNORE INTO `#__rsticketspro_emails` (`lang`, `type`, `subject`, `message`) VALUES ('en-GB', 'notification_max_replies_nr', '{code} This unassigned ticket has received too many replies', 'The ticket <a href=\"{ticket}\">{code}</a> has received {replies} replies without a staff member being assigned to it.\r\n<p><u>Customer Information</u></p>\r\n<p>Name: {customer_name}</p>\r\n<p>Username: {customer_username}</p>\r\n<p>Email: {customer_email}</p>\r\n\r\n<p><u>Staff Information</u></p>\r\n<p>Unassigned</p>\r\n\r\n<p><u>Ticket Information</u></p>\r\n<p>Subject: {subject}</p>\r\n<p>Message:<br />{message}</p>'), ('en-GB', 'notification_replies_with_no_response_nr', '{code} This ticket has received too many replies', 'The ticket <a href=\"{ticket}\">{code}</a> has received {replies} replies without any response from the designated staff member.\r\n<p><u>Customer Information</u></p>\r\n<p>Name: {customer_name}</p>\r\n<p>Username: {customer_username}</p>\r\n<p>Email: {customer_email}</p>\r\n\r\n<p><u>Staff Information</u></p>\r\n<p>Name: {staff_name}</p>\r\n<p>Username: {staff_username}</p>\r\n<p>Email: {staff_email}</p>\r\n\r\n<p><u>Ticket Information</u></p>\r\n<p>Subject: {subject}</p>\r\n<p>Message:<br />{message}</p>'), ('en-GB', 'notification_not_allowed_keywords', 'This ticket contains a keyword', 'The ticket <a href=\"{ticket}\">{code}</a> contains a keyword.\r\n<p><u>Customer Information</u></p>\r\n<p>Name: {customer_name}</p>\r\n<p>Username: {customer_username}</p>\r\n<p>Email: {customer_email}</p>\r\n\r\n<p><u>Staff Information</u></p>\r\n<p>Name: {staff_name}</p>\r\n<p>Username: {staff_username}</p>\r\n<p>Email: {staff_email}</p>\r\n\r\n<p><u>Ticket Information</u></p>\r\n<p>Subject: {subject}</p>\r\n<p>Message:<br />{message}</p>');");
	$db->query();
}

// R8
$db->setQuery("SHOW COLUMNS FROM `#__rsticketspro_departments` WHERE `Field`='upload_files'");
if (!$db->loadResult())
{
	$db->setQuery("ALTER TABLE `#__rsticketspro_departments` ADD `upload_files` INT NOT NULL AFTER `upload_size`, ADD `cc` TEXT NOT NULL AFTER `priority_id`, ADD `bcc` TEXT NOT NULL AFTER `cc` , ADD `predefined_subjects` TEXT NOT NULL AFTER `bcc`, ADD `customer_send_copy_email` TINYINT( 1 ) NOT NULL DEFAULT '1' AFTER `customer_send_email`");
	$db->query();
	
	$db->setQuery("UPDATE #__rsticketspro_departments SET `customer_send_copy_email`=`customer_send_email`");
	$db->query();
}

$db->setQuery("SELECT * FROM `#__rsticketspro_configuration` WHERE `name`='kb_comments'");
if (!$db->loadResult())
{
	$db->setQuery("INSERT IGNORE INTO `#__rsticketspro_configuration` (`name` ,`value`) VALUES ('kb_comments', ''),('show_kb_search', '1'), ('show_signature', '1'), ('allow_predefined_subjects', '0'), ('customer_itemid', ''), ('staff_itemid', ''),('enable_time_spent', '0'),('time_spent_unit', 'h');");
	$db->query();
}

$db->setQuery("SHOW COLUMNS FROM `#__rsticketspro_staff` WHERE `Field`='priority_id'");
if (!$db->loadResult())
{
	$db->setQuery("ALTER TABLE `#__rsticketspro_staff` ADD `priority_id` INT NOT NULL AFTER `user_id`");
	$db->query();
	$db->setQuery("ALTER TABLE `#__rsticketspro_staff` ADD INDEX (`priority_id`)");
	$db->query();
}

$db->setQuery("SHOW COLUMNS FROM `#__rsticketspro_tickets` WHERE `Field`='time_spent'");
if (!$db->loadResult())
{
	$db->setQuery("ALTER TABLE `#__rsticketspro_tickets` ADD `time_spent` DECIMAL( 10, 2 ) NOT NULL AFTER `has_files`");
	$db->query();
}

$db->setQuery("SELECT * FROM `#__rsticketspro_configuration` WHERE `name`='calculate_itemids'");
if (!$db->loadResult())
{
	$db->setQuery("INSERT IGNORE INTO `#__rsticketspro_configuration` (`name`, `value`) VALUES  ('calculate_itemids', '1')");
	$db->query();
}
?>
	<?php if (RSTicketsProHelper::isJ16()) { ?>
	<p align="center">If you've received the &quot;Error Building Admin Menus&quot; error, please <a href="<?php echo JRoute::_('index.php?option=com_rsticketspro&task=fixadminmenus'); ?>">click here to attempt to fix it.</a></p>
	<?php } ?>
	<table class="adminlist">
		<thead>
			<tr>
				<th class="title" colspan="2"><?php echo JText::_('Extension'); ?></th>
				<th width="30%"><?php echo JText::_('Status'); ?></th>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<td colspan="3"></td>
			</tr>
		</tfoot>
		<tbody>
			<tr class="row0">
				<td class="key" colspan="2"><?php echo 'RSTickets! Pro '.JText::_('Component'); ?></td>
				<td><strong><?php echo JText::_('Installed'); ?></strong></td>
			</tr>
			<tr>
				<th><?php echo JText::_('Plugin'); ?></th>
				<th><?php echo JText::_('Group'); ?></th>
				<th></th>
			</tr>
			<tr class="row1">
				<td class="key">System - RSTickets! Pro Plugin</td>
				<td class="key">system</td>
				<td><strong><?php echo JText::_('Installed'); ?></strong></td>
			</tr>
			<tr class="row0">
				<td class="key">Search - RSTickets! Pro Knowledgebase</td>
				<td class="key">search</td>
				<td><strong><?php echo JText::_('Installed'); ?></strong></td>
			</tr>
			<tr class="row1">
				<td class="key">User - RSTickets! Pro Staff</td>
				<td class="key">user</td>
				<td><strong><?php echo JText::_('Installed'); ?></strong></td>
			</tr>
		</tbody>
	</table>
	<table>
	<tr>
		<td width="1%"><img src="components/com_rsticketspro/assets/images/rstickets-pro-box.png" alt="RSTickets! Pro Box" /></td>
		<td align="left">
		<div id="rsticketspro_message">
		<p>Thank you for choosing RSTickets! Pro.</p>
		<p>New in this version:</p>
		<ul id="rsticketspro_changelog">
			<li><img src="components/com_rsticketspro/assets/images/native17.png" alt="1.7 Native" /> Joomla! 1.7 Compatibility</li>
			<li>Knowledgebase comments integration: Facebook, RSComments!, JComments, JomComment</li>
			<li>Staff members can now get tickets assigned based on priority</li>
			<li>CC and BCC fields for departments</li>
			<li>Your customers can now choose from a list of predefined subjects when submitting a ticket</li>
		</ul>
		<a href="http://www.rsjoomla.com/customer-support/documentations/88-general-overview-of-the-component/440-rstickets-pro-changelog.html" target="_blank">Full Changelog</a>
		<ul id="rsticketspro_links">
			<li>
				<div class="button2-left">
					<div class="next">
						<a href="index.php?option=com_rsticketspro">Start using RSTickets! Pro</a>
					</div>
				</div>
			</li>
			<li>
				<div class="button2-left">
					<div class="readmore">
						<a href="http://www.rsjoomla.com/customer-support/documentations/87-rsticketspro.html" target="_blank">Read the RSTickets! Pro User Guide</a>
					</div>
				</div>
			</li>
			<li>
				<div class="button2-left">
					<div class="blank">
						<a href="http://www.rsjoomla.com/customer-support/tickets.html" target="_blank">Get Support!</a>
					</div>
				</div>
			</li>
		</ul>
		</div>
		</td>
	</tr>
	
	</table><br/>
	
	<br/>
	<?php
	$your_php = phpversion();
	$correct_php = version_compare($your_php, '4.0');

	$db->setQuery("SELECT VERSION()");
	$your_sql = $db->loadResult();
	$correct_sql = version_compare($your_sql, '4.2');
	
	jimport('joomla.plugin.helper');
	if (RSTicketsProHelper::isJ16())
	{
		$your_moo = '1.3';
		$correct_moo = true;
	}
	elseif (JPluginHelper::isEnabled('system', 'mtupgrade'))
	{
		$your_moo = '1.2.4';
		$correct_moo = true;
	}
	else
	{
		$your_moo = '1.12';
		$correct_moo = false;
	}
	?>
	<style type="text/css">
	.green { color: #009E28; }
	.red { color: #B8002E; }
	.greenbg { background: #B8FFC9 !important; }
	.redbg { background: #FFB8C9 !important; }
	
	#rsticketspro_changelog
	{
		list-style-type: none;
		padding: 0;
	}

	#rsticketspro_changelog li
	{
		background: url(components/com_rsticketspro/assets/images/tick.png) no-repeat center left;
		padding-left: 24px;
	}

	#rsticketspro_links
	{
		list-style-type: none;
		padding: 0;
	}
	</style>
	</style>
	<table class="adminlist">
		<thead>
			<tr>
				<th width="30%" nowrap="nowrap"><?php echo JText::_('Software'); ?></th>
				<th width="30%" nowrap="nowrap"><?php echo JText::_('Your Version'); ?></th>
				<th width="30%" nowrap="nowrap"><?php echo JText::_('Minimum'); ?></th>
				<th width="30%" nowrap="nowrap"><?php echo JText::_('Recommended'); ?></th>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<td colspan="4"></td>
			</tr>
		</tfoot>
		<tbody>
			<tr class="row0">
				<td class="key">PHP</td>
				<td class="<?php echo $correct_php >= 0 ? 'greenbg' : 'redbg'; ?>"><strong class="<?php echo $correct_php >= 0 ? 'green' : 'red'; ?>"><?php echo $your_php; ?></strong> <img src="components/com_rsticketspro/assets/images/<?php echo $correct_php >= 0 ? 'tick' : 'publish_x'; ?>.png" alt="" /></td>
				<td><strong>4.x</strong></td>
				<td><strong>5.x</strong></td>
			</tr>
			<tr class="row1">
				<td class="key">MySQL</td>
				<td class="<?php echo $correct_sql >= 0 ? 'greenbg' : 'redbg'; ?>"><strong class="<?php echo $correct_sql >= 0 ? 'green' : 'red'; ?>"><?php echo $your_sql; ?></strong> <img src="components/com_rsticketspro/assets/images/<?php echo $correct_sql >= 0 ? 'tick' : 'publish_x'; ?>.png" alt="" /></td>
				<td><strong>4.2</strong></td>
				<td><strong>5.x</strong></td>
			</tr>
			<tr class="row0">
				<td class="key">MooTools 1.2</td>
				<td class="<?php echo $correct_moo ? 'greenbg' : 'redbg'; ?>"><strong class="<?php echo $correct_moo ? 'green' : 'red'; ?>"><?php echo $your_moo; ?></strong> <img src="components/com_rsticketspro/assets/images/<?php echo $correct_moo ? 'tick' : 'publish_x'; ?>.png" alt="" /></td>
				<td><strong>1.2</strong></td>
				<td><strong>1.3</strong></td>
			</tr>
		</tbody>
	</table>