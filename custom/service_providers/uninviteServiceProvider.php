<?php
set_include_path(get_include_path() . PATH_SEPARATOR . '/home/probid/pear/php');
require_once('../connections/dbMDB2.php');
$article_id = $_GET['pid'];
$user_id = $_GET['uid'];
$query = "DELETE FROM `jos_probid_friends` WHERE `article_id` = " . $article_id . " AND `user_id` = " . $user_id;
$result = $conn->query($query);
require_once('../connections/dbMDB2-disconnect.php');