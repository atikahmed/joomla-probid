CREATE TABLE IF NOT EXISTS `#__rsmembership_categories` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `published` tinyint(1) NOT NULL,
  `ordering` int(11) NOT NULL,
  PRIMARY KEY  (`id`)
);

CREATE TABLE IF NOT EXISTS `#__rsmembership_configuration` (
  `name` varchar(255) NOT NULL,
  `value` text NOT NULL,
  PRIMARY KEY  (`name`)
);

INSERT IGNORE INTO `#__rsmembership_configuration` (`name`, `value`) VALUES
('global_register_code', ''),
('date_format', 'd.m.Y (H:i:s)'),
('currency', 'EUR'),
('delete_pending_after', '48'),
('show_login', '1'),
('create_user_instantly', '1'),
('disable_registration', '0'),
('registration_page', ''),
('price_format', '{price} {currency}'),
('price_show_free', '1'),
('expire_last_run', '0'),
('expire_emails', '10'),
('expire_check_in', '10'),
('choose_username', '0'),
('captcha_enabled', '0'),
('captcha_enabled_for', '1,0'),
('captcha_characters', '5'),
('captcha_lines', '1'),
('captcha_case_sensitive', '0'),
('recaptcha_public_key', ''),
('recaptcha_private_key', ''),
('recaptcha_theme', 'red'),
('idev_enable', '0'),
('idev_url', ''),
('idev_track_renewals', '0'),
('last_check', '0'),
('choose_password', '0'),
('one_page_checkout', '0');

CREATE TABLE IF NOT EXISTS `#__rsmembership_countries` (
  `name` varchar(255) NOT NULL,
  UNIQUE KEY `name` (`name`)
);

INSERT IGNORE INTO `#__rsmembership_countries` (`name`) VALUES
('Afghanistan'),
('Akrotiri'),
('Albania'),
('Algeria'),
('American Samoa'),
('Andorra'),
('Angola'),
('Anguilla'),
('Antarctica'),
('Antigua and Barbuda'),
('Argentina'),
('Armenia'),
('Aruba'),
('Ashmore and Cartier Islands'),
('Australia'),
('Austria'),
('Azerbaijan'),
('Bahamas, The'),
('Bahrain'),
('Bangladesh'),
('Barbados'),
('Bassas da India'),
('Belarus'),
('Belgium'),
('Belize'),
('Benin'),
('Bermuda'),
('Bhutan'),
('Bolivia'),
('Bosnia and Herzegovina'),
('Botswana'),
('Bouvet Island'),
('Brazil'),
('British Indian Ocean Territory'),
('British Virgin Islands'),
('Brunei'),
('Bulgaria'),
('Burkina Faso'),
('Burma'),
('Burundi'),
('Cambodia'),
('Cameroon'),
('Canada'),
('Cape Verde'),
('Cayman Islands'),
('Central African Republic'),
('Chad'),
('Chile'),
('China'),
('Christmas Island'),
('Clipperton Island'),
('Cocos (Keeling) Islands'),
('Colombia'),
('Comoros'),
('Congo, Democratic Republic of the'),
('Congo, Republic of the'),
('Cook Islands'),
('Coral Sea Islands'),
('Costa Rica'),
('Cote d''Ivoire'),
('Croatia'),
('Cuba'),
('Cyprus'),
('Czech Republic'),
('Denmark'),
('Dhekelia'),
('Djibouti'),
('Dominica'),
('Dominican Republic'),
('Ecuador'),
('Egypt'),
('El Salvador'),
('Equatorial Guinea'),
('Eritrea'),
('Estonia'),
('Ethiopia'),
('Europa Island'),
('Falkland Islands (Islas Malvinas)'),
('Faroe Islands'),
('Fiji'),
('Finland'),
('France'),
('French Guiana'),
('French Polynesia'),
('French Southern and Antarctic Lands'),
('Gabon'),
('Gambia, The'),
('Gaza Strip'),
('Georgia'),
('Germany'),
('Ghana'),
('Gibraltar'),
('Glorioso Islands'),
('Greece'),
('Greenland'),
('Grenada'),
('Guadeloupe'),
('Guam'),
('Guatemala'),
('Guernsey'),
('Guinea'),
('Guinea-Bissau'),
('Guyana'),
('Haiti'),
('Heard Island and McDonald Islands'),
('Holy See (Vatican City)'),
('Honduras'),
('Hong Kong'),
('Hungary'),
('Iceland'),
('India'),
('Indonesia'),
('Iran'),
('Iraq'),
('Ireland'),
('Isle of Man'),
('Israel'),
('Italy'),
('Jamaica'),
('Jan Mayen'),
('Japan'),
('Jersey'),
('Jordan'),
('Juan de Nova Island'),
('Kazakhstan'),
('Kenya'),
('Kiribati'),
('Korea, North'),
('Korea, South'),
('Kuwait'),
('Kyrgyzstan'),
('Laos'),
('Latvia'),
('Lebanon'),
('Lesotho'),
('Liberia'),
('Libya'),
('Liechtenstein'),
('Lithuania'),
('Luxembourg'),
('Macau'),
('Macedonia'),
('Madagascar'),
('Malawi'),
('Malaysia'),
('Maldives'),
('Mali'),
('Malta'),
('Marshall Islands'),
('Martinique'),
('Mauritania'),
('Mauritius'),
('Mayotte'),
('Mexico'),
('Micronesia, Federated States of'),
('Moldova'),
('Monaco'),
('Mongolia'),
('Montserrat'),
('Morocco'),
('Mozambique'),
('Namibia'),
('Nauru'),
('Navassa Island'),
('Nepal'),
('Netherlands'),
('Netherlands Antilles'),
('New Caledonia'),
('New Zealand'),
('Nicaragua'),
('Niger'),
('Nigeria'),
('Niue'),
('Norfolk Island'),
('Northern Mariana Islands'),
('Norway'),
('Oman'),
('Pakistan'),
('Palau'),
('Panama'),
('Papua New Guinea'),
('Paracel Islands'),
('Paraguay'),
('Peru'),
('Philippines'),
('Pitcairn Islands'),
('Poland'),
('Portugal'),
('Puerto Rico'),
('Qatar'),
('Reunion'),
('Romania'),
('Russia'),
('Rwanda'),
('Saint Helena'),
('Saint Kitts and Nevis'),
('Saint Lucia'),
('Saint Pierre and Miquelon'),
('Saint Vincent and the Grenadines'),
('Samoa'),
('San Marino'),
('Sao Tome and Principe'),
('Saudi Arabia'),
('Senegal'),
('Serbia and Montenegro'),
('Seychelles'),
('Sierra Leone'),
('Singapore'),
('Slovakia'),
('Slovenia'),
('Solomon Islands'),
('Somalia'),
('South Africa'),
('South Georgia and the South Sandwich Islands'),
('Spain'),
('Spratly Islands'),
('Sri Lanka'),
('Sudan'),
('Suriname'),
('Svalbard'),
('Swaziland'),
('Sweden'),
('Switzerland'),
('Syria'),
('Taiwan'),
('Tajikistan'),
('Tanzania'),
('Thailand'),
('Timor-Leste'),
('Togo'),
('Tokelau'),
('Tonga'),
('Trinidad and Tobago'),
('Tromelin Island'),
('Tunisia'),
('Turkey'),
('Turkmenistan'),
('Turks and Caicos Islands'),
('Tuvalu'),
('Uganda'),
('Ukraine'),
('United Arab Emirates'),
('United Kingdom'),
('United States'),
('Uruguay'),
('Uzbekistan'),
('Vanuatu'),
('Venezuela'),
('Vietnam'),
('Virgin Islands'),
('Wake Island'),
('Wallis and Futuna'),
('West Bank'),
('Western Sahara'),
('Yemen'),
('Zambia'),
('Zimbabwe');

CREATE TABLE IF NOT EXISTS `#__rsmembership_coupons` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `date_added` int(11) NOT NULL,
  `date_start` int(11) NOT NULL,
  `date_end` int(11) NOT NULL,
  `discount_type` tinyint(1) NOT NULL,
  `discount_price` decimal(10,2) NOT NULL,
  `max_uses` int(11) NOT NULL,
  `published` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`)
);

CREATE TABLE IF NOT EXISTS `#__rsmembership_coupon_items` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `coupon_id` int(11) NOT NULL,
  `membership_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `coupon_id` (`coupon_id`,`membership_id`)
);

CREATE TABLE IF NOT EXISTS `#__rsmembership_extras` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `type` enum('dropdown','radio','checkbox') NOT NULL,
  `published` tinyint(1) NOT NULL,
  `ordering` int(11) NOT NULL,
  PRIMARY KEY  (`id`)
);

CREATE TABLE IF NOT EXISTS `#__rsmembership_extra_values` (
  `id` int(11) NOT NULL auto_increment,
  `extra_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `sku` varchar(255) NOT NULL,
  `price` decimal(10, 2) NOT NULL,
  `share_redirect` text NOT NULL,
  `checked` tinyint(1) NOT NULL,
  `published` tinyint(1) NOT NULL,
  `ordering` int(11) NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `extra_id` (`extra_id`)
);

CREATE TABLE IF NOT EXISTS `#__rsmembership_extra_value_shared` (
  `id` int(11) NOT NULL auto_increment,
  `extra_value_id` int(11) NOT NULL,
  `params` text NOT NULL,
  `type` varchar(255) NOT NULL,
  `published` tinyint(1) NOT NULL,
  `ordering` int(11) NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `extra_value_id` (`extra_value_id`)
);

CREATE TABLE IF NOT EXISTS `#__rsmembership_fields` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(255) NOT NULL,
  `label` varchar(255) NOT NULL,
  `type` varchar(255) NOT NULL,
  `values` text NOT NULL,
  `additional` text NOT NULL,
  `validation` text NOT NULL,
  `rule` varchar(64) NOT NULL,
  `required` tinyint(1) NOT NULL,
  `published` tinyint(1) NOT NULL,
  `ordering` int(11) NOT NULL,
  PRIMARY KEY  (`id`)
);

CREATE TABLE IF NOT EXISTS `#__rsmembership_files` (
  `id` int(11) NOT NULL auto_increment,
  `path` text NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `term_id` int(11) NOT NULL,
  `visits` int(11) NOT NULL,
  `downloads` int(11) NOT NULL,
  `thumb` varchar(255) NOT NULL,
  `thumb_w` int(11) NOT NULL,
  PRIMARY KEY  (`id`)
);

CREATE TABLE IF NOT EXISTS `#__rsmembership_logs` (
  `id` int(11) NOT NULL auto_increment,
  `date` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `path` text NOT NULL,
  `ip` varchar(16) NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `user_id` (`user_id`)
);

CREATE TABLE IF NOT EXISTS `#__rsmembership_memberships` (
  `id` int(11) NOT NULL auto_increment,
  `category_id` int(11) NOT NULL default '0',
  `name` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `term_id` int(11) NOT NULL,
  `thumb` varchar(255) NOT NULL,
  `thumb_w` int(11) NOT NULL,
  `sku` varchar(255) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `use_renewal_price` tinyint(1) NOT NULL,
  `renewal_price` decimal(10,2) NOT NULL,
  `recurring` tinyint(1) NOT NULL,
  `share_redirect` text NOT NULL,
  `period` int(11) NOT NULL,
  `period_type` varchar(1) NOT NULL,
  `use_trial_period` tinyint(1) NOT NULL,
  `trial_period` int(11) NOT NULL,
  `trial_period_type` varchar(1) NOT NULL,
  `trial_price` decimal(10,2) NOT NULL,
  `unique` tinyint(1) NOT NULL default '0',
  `no_renew` tinyint(1) NOT NULL default '0',
  `stock` int(11) NOT NULL,
  `activation` tinyint(1) NOT NULL,
  `action` tinyint(1) NOT NULL,
  `thankyou` text NOT NULL,
  `redirect` text NOT NULL,
  `user_email_use_global` tinyint( 1 ) NOT NULL,
  `user_email_mode` tinyint(1) NOT NULL,
  `user_email_from` varchar(255) NOT NULL,
  `user_email_from_addr` varchar(255) NOT NULL,
  `user_email_new_subject` varchar(255) NOT NULL,
  `user_email_new_text` text NOT NULL,
  `user_email_approved_subject` varchar(255) NOT NULL,
  `user_email_approved_text` text NOT NULL,
  `user_email_renew_subject` varchar(255) NOT NULL,
  `user_email_renew_text` text NOT NULL,
  `user_email_upgrade_subject` varchar(255) NOT NULL,
  `user_email_upgrade_text` text NOT NULL,
  `user_email_addextra_subject` varchar(255) NOT NULL,
  `user_email_addextra_text` text NOT NULL,
  `user_email_expire_subject` varchar(255) NOT NULL,
  `user_email_expire_text` text NOT NULL,
  `expire_notify_interval` int(3) NOT NULL default '3',
  `admin_email_mode` tinyint(1) NOT NULL,
  `admin_email_to_addr` varchar(255) NOT NULL,
  `admin_email_new_subject` varchar(255) NOT NULL,
  `admin_email_new_text` text NOT NULL,
  `admin_email_approved_subject` varchar( 255 ) NOT NULL,
  `admin_email_approved_text` text NOT NULL,
  `admin_email_renew_subject` varchar( 255 ) NOT NULL,
  `admin_email_renew_text` text NOT NULL,
  `admin_email_upgrade_subject` varchar( 255 ) NOT NULL,
  `admin_email_upgrade_text` text NOT NULL,
  `admin_email_addextra_subject` varchar( 255 ) NOT NULL,
  `admin_email_addextra_text` text NOT NULL,
  `admin_email_expire_subject` varchar( 255 ) NOT NULL,
  `admin_email_expire_text` text NOT NULL,
  `custom_code_transaction` text NOT NULL,
  `custom_code` text NOT NULL,
  `gid_enable` tinyint(1) NOT NULL default '0',
  `gid_subscribe` tinyint(3) NOT NULL default '18',
  `gid_expire` tinyint(3) NOT NULL default '18',
  `disable_expired_account` tinyint(1) NOT NULL,
  `fixed_expiry` tinyint(1) NOT NULL,
  `fixed_day` int(2) NOT NULL,
  `fixed_month` tinyint(2) NOT NULL,
  `fixed_year` smallint(4) NOT NULL,
  `published` tinyint(1) NOT NULL,
  `ordering` int(11) NOT NULL,
  PRIMARY KEY  (`id`)
);

CREATE TABLE IF NOT EXISTS `#__rsmembership_membership_attachments` (
  `id` int(11) NOT NULL auto_increment,
  `membership_id` int(11) NOT NULL,
  `email_type` varchar( 64 ) NOT NULL,
  `path` text NOT NULL,
  `published` tinyint(1) NOT NULL,
  `ordering` int(11) NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `membership_id` (`membership_id`)
);

CREATE TABLE IF NOT EXISTS `#__rsmembership_membership_extras` (
  `membership_id` int(11) NOT NULL,
  `extra_id` int(11) NOT NULL,
  PRIMARY KEY  (`membership_id`,`extra_id`)
);

CREATE TABLE IF NOT EXISTS `#__rsmembership_membership_shared` (
  `id` int(11) NOT NULL auto_increment,
  `membership_id` int(11) NOT NULL,
  `params` text NOT NULL,
  `type` varchar(255) NOT NULL,
  `published` tinyint(1) NOT NULL,
  `ordering` int(11) NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `membership_id` (`membership_id`)
);

CREATE TABLE IF NOT EXISTS `#__rsmembership_membership_upgrades` (
  `id` int(11) NOT NULL auto_increment,
  `membership_from_id` int(11) NOT NULL,
  `membership_to_id` int(11) NOT NULL,
  `price` decimal(10, 2) NOT NULL,
  `published` tinyint(1) NOT NULL,
  PRIMARY KEY  (`id`)
);

CREATE TABLE IF NOT EXISTS `#__rsmembership_membership_users` (
  `id` int(11) NOT NULL auto_increment,
  `user_id` int(11) NOT NULL,
  `membership_id` int(11) NOT NULL,
  `membership_start` int(11) NOT NULL,
  `membership_end` int(11) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `currency` varchar(4) NOT NULL,
  `status` tinyint(4) NOT NULL,
  `extras` varchar(255) NOT NULL,
  `notes` text NOT NULL,
  `from_transaction_id` int(11) NOT NULL,
  `last_transaction_id` int(11) NOT NULL,
  `custom_1` varchar(255) NOT NULL,
  `custom_2` varchar(255) NOT NULL,
  `custom_3` varchar(255) NOT NULL,
  `notified` tinyint(1) NOT NULL default '0',
  `published` tinyint(1) NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `user_id` (`user_id`,`membership_id`)
);

CREATE TABLE IF NOT EXISTS `#__rsmembership_payments` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(255) NOT NULL,
  `details` text NOT NULL,
  `tax_type` tinyint( 1 ) NOT NULL,
  `tax_value` int(11) NOT NULL,
  `published` tinyint(1) NOT NULL,
  `ordering` int(11) NOT NULL,
  PRIMARY KEY  (`id`)
);

CREATE TABLE IF NOT EXISTS `#__rsmembership_terms` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `published` tinyint(1) NOT NULL,
  `ordering` int(11) NOT NULL,
  PRIMARY KEY  (`id`)
);

CREATE TABLE IF NOT EXISTS `#__rsmembership_transactions` (
  `id` int(11) NOT NULL auto_increment,
  `user_id` int(11) NOT NULL,
  `user_email` varchar(255) NOT NULL,
  `user_data` text NOT NULL,
  `type` varchar(32) NOT NULL,
  `params` text NOT NULL,
  `date` int(11) NOT NULL,
  `ip` varchar(16) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `coupon` varchar(64) NOT NULL,
  `currency` varchar(4) NOT NULL,
  `hash` varchar(255) NOT NULL,
  `custom` varchar(255) NOT NULL,
  `gateway` varchar(64) NOT NULL,
  `status` varchar(64) NOT NULL,
  `response_log` text NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `user_id` (`user_id`)
);

CREATE TABLE IF NOT EXISTS `#__rsmembership_users` (
  `user_id` int(11) NOT NULL,
  PRIMARY KEY  (`user_id`)
);