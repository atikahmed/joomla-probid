<?php
set_include_path(get_include_path() . PATH_SEPARATOR . '/home/probid/pear/php');
require_once('../connections/dbMDB2.php');
$project_id = $_GET['pid'];
$user_id = $_GET['uid'];
$query = 'DELETE FROM `jos_probid_friends` WHERE user_id = ' . $user_id . ' AND article_id = ' . $project_id;
$result = $conn->query($query);
require_once('../connections/dbMDB2-disconnect.php');