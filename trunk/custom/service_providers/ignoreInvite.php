<?php
set_include_path(get_include_path() . PATH_SEPARATOR . '/home/probid/pear/php');
require_once('../connections/dbMDB2.php');
$articleId = $_GET['projectId'];
$user_id = $_GET['user_id'];
//get list of providers and their business profile listing title
$query = "UPDATE jos_probid_friends SET accepted = 99 WHERE user_id = {$user_id} AND article_id = {$articleId}";
$result = $conn->query($query);
require_once('../connections/dbMDB2-disconnect.php');