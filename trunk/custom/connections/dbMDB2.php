<?php
$dburl = "mysqli://probid_dev:fa2e24c8@localhost/probid_dev";
require_once("MDB2.php");
$conn =& MDB2::factory($dburl);
$conn->setFetchMode(MDB2_FETCHMODE_ASSOC);