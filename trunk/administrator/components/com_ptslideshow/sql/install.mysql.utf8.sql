CREATE TABLE IF NOT EXISTS `#__ptslideshow` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
`title_` varchar(255) NOT NULL DEFAULT '',
`description_` text NOT NULL DEFAULT '',
`category_` int(11) NOT NULL ,
`url_` TEXT NOT NULL DEFAULT '',
`link` varchar(255) NOT NULL DEFAULT '#',
`ordering` INT(11)  NOT NULL ,
`state` TINYINT(1)  NOT NULL DEFAULT '1',
`checked_out` INT(11)  NOT NULL ,
`checked_out_time` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT COLLATE=utf8_general_ci;

