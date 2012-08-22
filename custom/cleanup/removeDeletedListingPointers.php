<?php 
/**
 * THIS SCRIPT IS CALLED BY CRON TWICE A DAY TO CLEAN UP ANY REFERENCES IN VARIOUS CUSTOM TABLES TO LISTINGS THAT HAVE BEEN DELETED
 * THE REASON THIS WAS NOT IMPLEMENTED AS A PLUGIN, IS THAT THE JREVIEWS PLUGIN CALLBACKS DO NOT DISTINGUISH BETWEEN DELETING LISTINGS
 * AND DELETING FIELDS.  SO IF A FIELD IS DELETED BY THE ADMIN, ALL LISTINGS WITH THAT FIELD COULD BE DELETED REFERENCE THIS URL:
 * http://www.reviewsforjoomla.com/forum/index.php?topic=10742.msg82329#msg82329
 * THIS SCRIPT WILL DELETE ALL ENTRIES IN THE jos_probid_tags_articles AND jos_probid_friends TABLES THAT REFERENCE JOOMLA ARTICLES
 * THAT NO LONGER EXIST
 */
 
set_include_path(get_include_path() . PATH_SEPARATOR . '/home/probid/pear/php');
require_once('../connections/dbMDB2.php');
//GET A LIST OF ALL TAGS FOR ARTICLES THAT NO LONGER EXIST
$query = "DELETE FROM `jos_probid_tags_articles` WHERE `article_id` NOT IN (SELECT `id` FROM `jos_content`)";
$result = $conn->query($query);
$query = "DELETE FROM `jos_probid_friends` WHERE `article_id` NOT IN (SELECT `id` FROM `jos_content`)";
$result = $conn->query($query);
require_once('../connections/dbMDB2-disconnect.php');
?>