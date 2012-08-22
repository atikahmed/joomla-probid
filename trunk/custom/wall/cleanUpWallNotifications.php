<?php
/**
 * This script will be called once every 15 minutes to clean up the jos_probid_wall_notifications table
 */
set_include_path(get_include_path() . PATH_SEPARATOR . '/home/probid/pear/php');
require_once('../connections/dbMDB2.php');
$query = "DELETE FROM jos_probid_wall_notifications WHERE email_sent = 1";
$result = $conn->query($query);
require_once('../connections/dbMDB2-disconnect.php');