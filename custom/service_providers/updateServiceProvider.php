<?php
set_include_path(get_include_path() . PATH_SEPARATOR . '/home/probid/pear/php');
require_once('../connections/dbMDB2.php');
$article_id = $_GET['pid'];
$user_id = $_GET['uid'];
$position = $_GET['position'];
$query = 'UPDATE `jos_probid_friends` SET `position` = ' . $position . ' WHERE `user_id` = ' . $user_id .  ' AND `article_id` = ' . $article_id;
$result = $conn->query($query);
require_once('../connections/dbMDB2-disconnect.php');