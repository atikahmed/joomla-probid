<?php
set_include_path(get_include_path() . PATH_SEPARATOR . '/home/probid/pear/php');
require_once('../connections/dbMDB2.php');

$tagid = $_GET['tid'];
$articleid = $_GET['aid'];
$query = "INSERT INTO jos_probid_tags_articles (tag_id, article_id) VALUES (" . $tagid . "," . $articleid . ")";
$result = $conn->query($query);
require_once('../connections/dbMDB2-disconnect.php');
//now update geo via the proxy system
require_once('../connections/geo-connect.php');
//overwrite $url
$url = "dev.probiddirect.com/custom/proxy/geo-update.php";
$postStr = "listing_id=" . $articleid;
$useragent = 'YahooSeeker-Testing/v3.9 (compatible; Mozilla 4.0; MSIE 5.5; http://search.yahoo.com/)';
curl_setopt($ch, CURLOPT_USERAGENT, $useragent);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_POSTFIELDS, $postStr);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
$result = curl_exec($ch);
require_once('../connections/geo-disconnect.php');
