<?php
set_include_path(get_include_path() . PATH_SEPARATOR . '/home/probid/pear/php');
require_once('../connections/dbMDB2.php');
$project_id = $_GET['pid'];
$user_id = $_GET['uid'];
$query = 'INSERT INTO `jos_probid_friends` (user_id, article_id, position) VALUES';
$query .= '(' . $user_id . ', ' . $project_id . ', 0)';
$result = $conn->query($query);
require_once('../connections/dbMDB2-disconnect.php');
