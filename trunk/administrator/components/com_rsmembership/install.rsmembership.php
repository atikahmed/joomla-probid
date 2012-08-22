<?php
/**
* @version 1.0.0
* @package RSMembership! 1.0.0
* @copyright (C) 2009 www.rsjoomla.com
* @license GPL, http://www.gnu.org/licenses/gpl-2.0.html
*/

defined( '_JEXEC' ) or die( 'Restricted access' );

$db = &JFactory::getDBO();

require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_rsmembership'.DS.'helpers'.DS.'rsmembership.php');

function RSMembership_isJ16()
{
	jimport('joomla.version');
	$version = new JVersion();
	return $version->isCompatible('1.6.0');
}

// Get a new installer
$plg_installer = new JInstaller();

// Install the System Plugin
$plg_installer->install($this->parent->getPath('source').DS.'plg_rsmembership');
// Must be published by default
if (!RSMembership_isJ16())
{
	$db->setQuery("UPDATE #__plugins SET published=1 WHERE `element`='rsmembership' AND `folder`='system'");
	$db->query();
}
else
{
	$db->setQuery("UPDATE #__extensions SET `enabled`='1' WHERE `type`='plugin' AND `element`='rsmembership' AND `folder`='system'");
	$db->query();
}
// Install the Wire Payment Plugin
$plg_installer->install($this->parent->getPath('source').DS.'plg_rsmembershipwire');
// Must be published by default
if (!RSMembership_isJ16())
{
	$db->setQuery("UPDATE #__plugins SET published=1 WHERE `element`='rsmembershipwire' AND `folder`='system'");
	$db->query();
}
else
{
	$db->setQuery("UPDATE #__extensions SET `enabled`='1' WHERE `type`='plugin' AND `element`='rsmembershipwire' AND `folder`='system'");
	$db->query();
}

// BEGIN UPDATE PROCEDURE

// REVISION 2 UPDATES
// term_id was introduced in R2
$db->setQuery("SHOW COLUMNS FROM `#__rsmembership_memberships` WHERE `Field`='term_id'");
if (!$db->loadResult())
{
	$db->setQuery("ALTER TABLE `#__rsmembership_memberships` ADD `term_id` INT NOT NULL AFTER `description`");
	$db->query();
	
	$db->setQuery("ALTER TABLE `#__rsmembership_memberships` ADD `recurring` TINYINT( 1 ) NOT NULL AFTER `price`");
	$db->query();
	
	$db->setQuery("ALTER TABLE `#__rsmembership_memberships` ADD `share_redirect` TEXT NOT NULL AFTER `recurring`");
	$db->query();
}

// user_email was introduced in R2
$db->setQuery("SHOW COLUMNS FROM `#__rsmembership_transactions` WHERE `Field`='user_email'");
if (!$db->loadResult())
{
	$db->setQuery("ALTER TABLE `#__rsmembership_transactions` ADD `user_email` VARCHAR( 255 ) NOT NULL AFTER `user_id`");
	$db->query();
	
	$db->setQuery("ALTER TABLE `#__rsmembership_transactions` ADD `user_data` TEXT NOT NULL AFTER `user_email`");
	$db->query();
}

// rsmembership_membership_folders was renamed to rsmembership_membership_shared in R2
$db->setQuery("SHOW TABLES LIKE '".$db->getPrefix()."rsmembership_membership_folders'");
if ($db->loadResult())
{
	$db->setQuery("SELECT * FROM #__rsmembership_membership_folders");
	$results = $db->loadObjectList();
	foreach ($results as $result)
	{
		$db->setQuery("INSERT INTO #__rsmembership_membership_shared SET `membership_id` = '".$result->membership_id."', `params` = '".$db->getEscaped($result->path)."', `type`='folder', `published`='".$result->published."', `ordering`='".$result->ordering."'");
		$db->query();
	}
}

// rsmembership_extra_value_folders was renamed to rsmembership_extra_value_shared in R9
$db->setQuery("SHOW TABLES LIKE '".$db->getPrefix()."rsmembership_extra_value_folders'");
if ($db->loadResult())
{
	$db->setQuery("SELECT * FROM #__rsmembership_extra_value_folders");
	$results = $db->loadObjectList();
	foreach ($results as $result)
	{
		$db->setQuery("INSERT INTO #__rsmembership_extra_value_shared SET `extra_value_id` = '".$result->extra_value_id."', `params` = '".$db->getEscaped($result->path)."', `type`='folder', `published`='".$result->published."', `ordering`='".$result->ordering."'");
		$db->query();
	}
	$db->setQuery("DROP TABLE #__rsmembership_membership_folders");
	$db->query();
}

// check if we've added countries twice
$db->setQuery("SELECT COUNT(`name`) FROM #__rsmembership_countries WHERE `name`='United States'");
$count = $db->loadResult();
if ($count > 1)
{
	$delete = $count - 1;
	
	$db->setQuery("SELECT DISTINCT(`name`) FROM #__rsmembership_countries");
	$countries = $db->loadResultArray();
	foreach ($countries as $country)
	{
		$db->setQuery("DELETE FROM #__rsmembership_countries WHERE `name`='".$country."' LIMIT ".$delete);
		$db->query();
	}
	$db->setQuery("DROP TABLE #__rsmembership_extra_value_folders");
	$db->query();
}

// add primary key to rsmembership_countries
$db->setQuery("DESCRIBE `#__rsmembership_countries`");
$result = $db->loadObject();
if ($result->Key != 'PRI')
{
	$db->setQuery("ALTER IGNORE TABLE `#__rsmembership_countries` ADD UNIQUE (`name`)");
	$db->query();
}

// REVISION 10 UPDATES
$db->setQuery("SELECT name FROM #__rsmembership_configuration WHERE `name`='disable_registration'");
if (!$db->loadResult())
{
	$db->setQuery("INSERT INTO #__rsmembership_configuration SET `name`='disable_registration', `value`='0'");
	$db->query();
}
$db->setQuery("SELECT name FROM #__rsmembership_configuration WHERE `name`='registration_page'");
if (!$db->loadResult())
{
	$db->setQuery("INSERT INTO #__rsmembership_configuration SET `name`='registration_page', `value`=''");
	$db->query();
}

$db->setQuery("SHOW COLUMNS FROM `#__rsmembership_membership_users` WHERE `Field`='from_transaction_id'");
if (!$db->loadResult())
{
	$db->setQuery("ALTER TABLE `#__rsmembership_membership_users` ADD `from_transaction_id` INT NOT NULL AFTER `notes`, ADD `last_transaction_id` INT NOT NULL AFTER `from_transaction_id`, ADD `custom_1` VARCHAR( 255 ) NOT NULL AFTER `last_transaction_id`, ADD `custom_2` VARCHAR( 255 ) NOT NULL AFTER `custom_1`, ADD `custom_3` VARCHAR( 255 ) NOT NULL AFTER `custom_2`");
	$db->query();
}

// R11 UPDATES
$db->setQuery("SHOW COLUMNS FROM #__rsmembership_memberships WHERE `Field`='price'");
$result = $db->loadObject();
if (strtolower($result->Type) == 'float')
{
	$db->setQuery("ALTER TABLE `#__rsmembership_extra_values` CHANGE `price` `price` DECIMAL( 10, 2 ) NOT NULL");
	$db->query();
	$db->setQuery("ALTER TABLE `#__rsmembership_memberships` CHANGE `price` `price` DECIMAL( 10, 2 ) NOT NULL");
	$db->query();
	$db->setQuery("ALTER TABLE `#__rsmembership_membership_upgrades` CHANGE `price` `price` DECIMAL( 10, 2 ) NOT NULL");
	$db->query();
	$db->setQuery("ALTER TABLE `#__rsmembership_membership_users` CHANGE `price` `price` DECIMAL( 10, 2 ) NOT NULL");
	$db->query();
	$db->setQuery("ALTER TABLE `#__rsmembership_transactions` CHANGE `price` `price` DECIMAL( 10, 2 ) NOT NULL");
	$db->query();
}

$db->setQuery("SELECT * FROM #__rsmembership_configuration WHERE `name` IN ('enable_field_address', 'enable_field_city', 'enable_field_state', 'enable_field_zip', 'enable_field_country')");
$old_fields = $db->loadObjectList();

$db->setQuery("SELECT COUNT(id) FROM #__rsmembership_fields");
$has_fields = $db->loadResult();

if (!empty($old_fields) || !$has_fields)
{
	$db->setQuery("DELETE FROM `#__rsmembership_configuration` WHERE `name` IN ('enable_field_address', 'enable_field_city', 'enable_field_state', 'enable_field_zip', 'enable_field_country');");
	$db->query();
	
	$new_fields = array(
		array('name' => 'address', 'label' => 'address', 'type' => 'textbox', 'values' => ''),
		array('name' => 'city', 'label' => 'City', 'type' => 'textbox', 'values' => ''),
		array('name' => 'state', 'label' => 'State', 'type' => 'textbox', 'values' => ''),
		array('name' => 'zip', 'label' => 'ZIP', 'type' => 'textbox', 'values' => ''),
		array('name' => 'country', 'label' => 'Country', 'type' => 'select', 'values' => "//<code>\r\n\$db = JFactory::getDBO();\r\n\$db->setQuery(\"SELECT name FROM #__rsmembership_countries\");\r\nreturn implode(\"\\n\", \$db->loadResultArray());\r\n//</code>")
	);
	
	foreach ($new_fields as $new_field)
	{
		JTable::addIncludePath(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_rsmembership'.DS.'tables');
		$field =& JTable::getInstance('RSMembership_Fields','Table');
		
		$field->bind($new_field);
		$field->required = 1;
		foreach ($old_fields as $old_field)
			if ($old_field->name == 'enable_field_'.$new_field['name'] && $old_field->value == '0')
				$field->published = 0;
		$field->ordering = $field->getNextOrder();
		
		if ($field->store())
		{
			$db->setQuery("SHOW COLUMNS FROM #__rsmembership_users WHERE `Field` = 'f".$field->id."'");
			if (!$db->loadResult())
			{
				$keyword = "CHANGE `".$new_field['name']."`";
				if (empty($old_fields))
					$keyword = 'ADD';
				$db->setQuery("ALTER TABLE `#__rsmembership_users` $keyword `f".$field->id."` VARCHAR( 255 ) NOT NULL");
				$db->query();
			}
		}
	}
	
	$db->setQuery("ALTER TABLE `#__rsmembership_users` DROP `address`, DROP `city`, DROP `state`, DROP `zip`, DROP `country`");
	$db->query();
}
if ($has_fields)
{
	$db->setQuery("SELECT id, type FROM #__rsmembership_fields");
	$results = $db->loadObjectList();
	
	foreach ($results as $result)
	{
		$type = 'VARCHAR(255)';
		if (in_array($result->type, array('freetext', 'textarea')))
			$type = 'TEXT';
			
		$db->setQuery("ALTER TABLE #__rsmembership_users ADD `f".$result->id."` ".$type." NOT NULL");
		$db->query();
	}
}

$db->setQuery("SHOW COLUMNS FROM #__rsmembership_memberships WHERE `Field`='coupon'");
if (!$db->loadResult())
{
	$db->setQuery("ALTER TABLE `#__rsmembership_memberships` ADD `use_renewal_price` TINYINT( 1 ) NOT NULL AFTER `price`");
	$db->query();
	$db->setQuery("ALTER TABLE `#__rsmembership_memberships` ADD `renewal_price` DECIMAL( 10, 2 ) NOT NULL AFTER `use_renewal_price`");
	$db->query();
	$db->setQuery("ALTER TABLE `#__rsmembership_memberships` ADD `use_coupon` TINYINT( 1 ) NOT NULL AFTER `renewal_price`");
	$db->query();
	$db->setQuery("ALTER TABLE `#__rsmembership_memberships` ADD `coupon` VARCHAR( 64 ) NOT NULL AFTER `use_coupon`");
	$db->query();
	$db->setQuery("ALTER TABLE `#__rsmembership_memberships` ADD `coupon_price` DECIMAL( 10, 2 ) NOT NULL AFTER `coupon`");
	$db->query();

	$db->setQuery("ALTER TABLE `#__rsmembership_memberships` ADD `use_trial_period` TINYINT( 1 ) NOT NULL AFTER `period_type`");
	$db->query();
	$db->setQuery("ALTER TABLE `#__rsmembership_memberships` ADD `trial_period` INT NOT NULL AFTER `use_trial_period`");
	$db->query();
	$db->setQuery("ALTER TABLE `#__rsmembership_memberships` ADD `trial_period_type` VARCHAR( 1 ) NOT NULL AFTER `trial_period`");
	$db->query();
	$db->setQuery("ALTER TABLE `#__rsmembership_memberships` ADD `trial_price` DECIMAL( 10, 2 ) NOT NULL AFTER `trial_period_type`");
	$db->query();
}

$db->setQuery("SHOW COLUMNS FROM #__rsmembership_transactions WHERE `Field`='coupon'");
if (!$db->loadResult())
{
	$db->setQuery("ALTER TABLE `#__rsmembership_transactions` ADD `coupon` VARCHAR( 64 ) NOT NULL AFTER `price` ;");
	$db->query();
}

// R12
$db->setQuery("SHOW COLUMNS FROM `#__rsmembership_memberships` WHERE `Field`='category_id'");
if (!$db->loadResult())
{
	$db->setQuery("ALTER TABLE `#__rsmembership_memberships` ADD `category_id` INT NOT NULL DEFAULT 0 AFTER `id`");
	$db->query();
	$db->setQuery("ALTER TABLE `#__rsmembership_memberships` ADD INDEX ( `category_id` )");
	$db->query();
}

// R13
$db->setQuery("SHOW COLUMNS FROM `#__rsmembership_membership_users` WHERE `Field`='notified'");
if (!$db->loadResult())
{
	$db->setQuery("ALTER TABLE `#__rsmembership_membership_users` ADD `notified` TINYINT( 1 ) NOT NULL DEFAULT '0' AFTER `custom_3`");
	$db->query();
}
$db->setQuery("SHOW COLUMNS FROM `#__rsmembership_memberships` WHERE `Field`='user_email_expire_subject'");
if (!$db->loadResult())
{
	$db->setQuery("ALTER TABLE `#__rsmembership_memberships` ADD `user_email_expire_subject` VARCHAR( 255 ) NOT NULL AFTER `user_email_addextra_text`, ADD `user_email_expire_text` TEXT NOT NULL AFTER `user_email_expire_subject`, ADD `expire_notify_interval` INT( 3 ) NOT NULL AFTER `user_email_expire_text`");
	$db->query();
	$db->setQuery("ALTER TABLE `#__rsmembership_memberships` ADD `unique` TINYINT( 1 ) NOT NULL DEFAULT '0' AFTER `trial_price`, ADD `no_renew` TINYINT( 1 ) NOT NULL DEFAULT '0' AFTER `unique`");
	$db->query();
}

// R14
$db->setQuery("SHOW COLUMNS FROM `#__rsmembership_memberships` WHERE `Field`='gid_subscribe'");
if (!$db->loadResult())
{
	$db->setQuery("ALTER TABLE `#__rsmembership_memberships` ADD `gid_subscribe` TINYINT( 3 ) NOT NULL DEFAULT 18 AFTER `custom_code`, ADD `gid_expire` TINYINT( 3 ) NOT NULL DEFAULT 18 AFTER `gid_subscribe`, ADD `disable_expired_account` TINYINT( 1 ) NOT NULL AFTER `gid_expire`, ADD `user_email_approved_subject` VARCHAR( 255 ) NOT NULL AFTER `user_email_new_text`, ADD `user_email_approved_text` TEXT NOT NULL AFTER `user_email_approved_subject`");
	$db->query();
}

// R15
$db->setQuery("SHOW COLUMNS FROM `#__rsmembership_memberships` WHERE `Field`='gid_enable'");
if (!$db->loadResult())
{
	$db->setQuery("ALTER TABLE `#__rsmembership_memberships` ADD `gid_enable` TINYINT( 1 ) NOT NULL AFTER `custom_code`");
	$db->query();
	
	$db->setQuery("UPDATE #__rsmembership_memberships SET `gid_enable`='1' WHERE `gid_subscribe` != 18 OR `gid_expire` != 18");
	$db->query();
}

// R16
$db->setQuery("SHOW COLUMNS FROM `#__rsmembership_fields` WHERE `Field`='rule'");
if (!$db->loadResult())
{
	$db->setQuery("ALTER TABLE `#__rsmembership_fields` ADD `rule` VARCHAR( 64 ) NOT NULL AFTER `validation`");
	$db->query();
}

$db->setQuery("SHOW COLUMNS FROM `#__rsmembership_memberships` WHERE `Field`='coupon'");
if ($db->loadResult())
{
	$db->setQuery("SELECT * FROM #__rsmembership_memberships WHERE `coupon` != ''");
	$memberships = $db->loadObjectList();
	foreach ($memberships as $membership)
	{
		$date = JFactory::getDate();
		
		$diff = $membership->price - $membership->coupon_price;
		
		$db->setQuery("INSERT INTO #__rsmembership_coupons SET `name`='".$db->getEscaped($membership->coupon)."', `date_added`='".$date->toUnix()."', `discount_type`='1', `discount_price`='".$diff."', `published`='".(int) $membership->use_coupon."'");
		$db->query();
		$coupon_id = $db->insertid();
		
		$db->setQuery("INSERT INTO #__rsmembership_coupon_items SET `coupon_id`='".$coupon_id."', `membership_id`='".$membership->id."'");
		$db->query();
	}

	$db->setQuery("ALTER TABLE `#__rsmembership_memberships` DROP `use_coupon`, DROP `coupon`, DROP `coupon_price`");
	$db->query();
}

$db->setQuery("SHOW COLUMNS FROM `#__rsmembership_memberships` WHERE `Field`='fixed_expiry'");
if (!$db->loadResult())
{
	$db->setQuery("ALTER TABLE `#__rsmembership_memberships` ADD `fixed_expiry` TINYINT( 1 ) NOT NULL AFTER `disable_expired_account` , ADD `fixed_day` INT( 2 ) NOT NULL AFTER `fixed_expiry` , ADD `fixed_month` TINYINT( 2 ) NOT NULL AFTER `fixed_day` , ADD `fixed_year` SMALLINT( 4 ) NOT NULL AFTER `fixed_month` ");
	$db->query();
}

// R17
$db->setQuery("SELECT `name` FROM #__rsmembership_configuration WHERE `name`='last_check'");
if (!$db->loadResult())
{
	$db->setQuery("INSERT INTO `#__rsmembership_configuration` (`name` ,`value`) VALUES ('last_check', '0'), ('choose_password', '0'), ('one_page_checkout', '0');");
	$db->query();
}

$db->setQuery("SHOW COLUMNS FROM `#__rsmembership_memberships` WHERE `Field`='custom_code_transaction'");
if (!$db->loadResult())
{
	$db->setQuery("ALTER TABLE `#__rsmembership_memberships` ADD `custom_code_transaction` TEXT NOT NULL AFTER `custom_code`, ADD `user_email_use_global` TINYINT( 1 ) NOT NULL AFTER `redirect`, CHANGE `admin_email_subject` `admin_email_new_subject` VARCHAR( 255 ) NOT NULL, CHANGE `admin_email_text` `admin_email_new_text` TEXT NOT NULL, ADD `admin_email_approved_subject` VARCHAR( 255 ) NOT NULL AFTER `admin_email_new_text` , ADD `admin_email_approved_text` TEXT NOT NULL AFTER `admin_email_approved_subject` , ADD `admin_email_renew_subject` VARCHAR( 255 ) NOT NULL AFTER `admin_email_approved_text` , ADD `admin_email_renew_text` TEXT NOT NULL AFTER `admin_email_renew_subject` , ADD `admin_email_upgrade_subject` VARCHAR( 255 ) NOT NULL AFTER `admin_email_renew_text` , ADD `admin_email_upgrade_text` TEXT NOT NULL AFTER `admin_email_upgrade_subject` , ADD `admin_email_addextra_subject` VARCHAR( 255 ) NOT NULL AFTER `admin_email_upgrade_text` , ADD `admin_email_addextra_text` TEXT NOT NULL AFTER `admin_email_addextra_subject` , ADD `admin_email_expire_subject` VARCHAR( 255 ) NOT NULL AFTER `admin_email_addextra_text` , ADD `admin_email_expire_text` TEXT NOT NULL AFTER `admin_email_expire_subject`");
	$db->query();
}

$db->setQuery("SHOW COLUMNS FROM #__rsmembership_transactions WHERE `Field`='response_log'");
if (!$db->loadResult())
{
	$db->setQuery("ALTER TABLE `#__rsmembership_transactions` ADD `response_log` TEXT NOT NULL AFTER `status`");
	$db->query();
}

$db->setQuery("SHOW COLUMNS FROM #__rsmembership_membership_attachments WHERE `Field`='email_type'");
if (!$db->loadResult())
{
	$db->setQuery("ALTER TABLE `#__rsmembership_membership_attachments` ADD `email_type` VARCHAR( 64 ) NOT NULL AFTER `membership_id` ");
	$db->query();
	
	$db->setQuery("UPDATE #__rsmembership_membership_attachments SET `email_type`='user_email_new' WHERE `email_type`=''");
	$db->query();
}

$db->setQuery("SHOW COLUMNS FROM #__rsmembership_payments WHERE `Field`='details'");
if (!$db->loadResult())
{
	$db->setQuery("ALTER TABLE #__rsmembership_payments DROP PRIMARY KEY;");
	$db->query();
	
	$db->setQuery("ALTER TABLE #__rsmembership_payments ADD `id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY FIRST;");
	$db->query();
	
	$db->setQuery("ALTER TABLE #__rsmembership_payments ADD `details` TEXT NOT NULL AFTER `name`, ADD `tax_type` TINYINT(1) NOT NULL AFTER `details`,ADD `tax_value` INT NOT NULL AFTER `tax_type`");
	$db->query();
	
	if (RSMembership_isJ16())
		$db->setQuery("SELECT params FROM #__extensions WHERE `element`='rsmembershipwire' AND `folder`='system' AND `type`='plugin' LIMIT 1");
	else
		$db->setQuery("SELECT params FROM #__plugins WHERE `element`='rsmembershipwire' AND `folder`='system' LIMIT 1");
	if ($params = $db->loadResult())
	{
		$reg =& JRegistry::getInstance('');
		if (RSMembership_isJ16())
			$reg->loadJSON($params);
		else
			$reg->loadINI($params);
				
		$params    = $reg->toObject();
		$details   = isset($params->details) ? $params->details : '<p>Please enter your transfer details here.</p>';
		$tax_type  = isset($params->tax_type) ? $params->tax_type : 0;
		$tax_value = isset($params->tax_value) ? $params->tax_value : 0;
			
		$db->setQuery("INSERT INTO #__rsmembership_payments SET `name`='Wire Transfer', `details`='".$db->getEscaped($details)."', `tax_type`='".$db->getEscaped($tax_type)."', `tax_value`='".$db->getEscaped($tax_value)."'");
		$db->query();
	}
}
$db->setQuery("INSERT IGNORE INTO #__rsmembership_payments (`id`, `name`, `details`, `tax_type`, `tax_value`, `ordering`) VALUES (1, 'Wire Transfer', '<p>Please enter your transfer details here.</p>', '0', '0', '0');");
$db->query();

// END UPDATE PROCEDURE
?>
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
			<td class="key" colspan="2"><?php echo 'RSMembership!! '.JText::_('Component'); ?></td>
			<td><strong><?php echo JText::_('Installed'); ?></strong></td>
		</tr>
		<tr>
			<th><?php echo JText::_('Plugin'); ?></th>
			<th><?php echo JText::_('Group'); ?></th>
			<th></th>
		</tr>
		<tr class="row1">
			<td class="key">System - RSMembership! Plugin</td>
			<td class="key">system</td>
			<td><strong><?php echo JText::_('Installed'); ?></strong></td>
		</tr>
		<tr class="row0">
			<td class="key">System - RSMembership! Wire Transfer Plugin</td>
			<td class="key">system</td>
			<td><strong><?php echo JText::_('Installed'); ?></strong></td>
		</tr>
	</tbody>
</table>
<table>
	<tr>
		<td width="1%"><img src="components/com_rsmembership/assets/images/rsmembership-box.jpg" alt="RSMembership! Box" /></td>
		<td align="left">
		<div id="rsmembership_message">
		<p>Thank you for choosing RSMembership!</p>
		<p>New in this version:</p>
		<ul id="rsmembership_changelog">
			<li>Joomla! 1.6 Compatibility</li>
			<li>Improved customer experience - added one page checkout, redesigned subscription pages</li>
			<li>Ability to add more offline payment types</li>
			<li>Monitor your sales and statistics with the new Reports feature</li>
			<li>Keep track of automated payments through the payment log</li>
		</ul>
		<a href="http://www.rsjoomla.com/customer-support/documentations/75-general-overview-of-the-component/308-rsmembership-changelog.html" target="_blank">Full Changelog</a>
		<ul id="rsmembership_links">
			<li>
				<div class="button2-left">
					<div class="next">
						<a href="index.php?option=com_rsmembership">Start using RSMembership!</a>
					</div>
				</div>
			</li>
			<li>
				<div class="button2-left">
					<div class="readmore">
						<a href="http://www.rsjoomla.com/customer-support/documentations/74-rsmembership-user-guide.html" target="_blank">Read the RSMembership! User Guide</a>
					</div>
				</div>
			</li>
			<li>
				<div class="button2-left">
					<div class="readmore">
						<a href="http://www.rsjoomla.com/presentation/RSMembership-%20Quick%20Guide.pdf" target="_blank">Download the RSMembership! Quick Guide</a>
					</div>
				</div>
			</li>
			<li>
				<div class="button2-left">
					<div class="readmore">
						<a href="http://www.rsjoomla.com/presentation/RSMembership-Stepbystepguide.pdf" target="_blank">Download the RSMembership! Step by Step Guide</a>
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
</table>
<div align="left" width="100%"><b>RSMembership! 1.0.0 Installed</b></div>
	
<style type="text/css">
.green { color: #009E28; }
.red { color: #B8002E; }
.greenbg { background: #B8FFC9 !important; }
.redbg { background: #FFB8C9 !important; }
#rsmembership_changelog
{
	list-style-type: none;
	padding: 0;
}
#rsmembership_changelog li
{
	background: url(components/com_rsmembership/assets/images/legacy/tick.png) no-repeat center left;
	padding-left: 24px;
}

#rsmembership_links
{
	list-style-type: none;
	padding: 0;
}
</style>