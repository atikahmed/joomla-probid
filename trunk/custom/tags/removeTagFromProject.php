<?php
set_include_path(get_include_path() . PATH_SEPARATOR . '/home/probid/pear/php');
require_once('../connections/dbMDB2.php');
$tagid = $_GET['tid'];
$articleid = $_GET['aid'];
$query = "DELETE FROM jos_probid_tags_articles WHERE tag_id = " . $tagid;
$query .= " AND article_id = " . $articleid;
$result = $conn->query($query);
//fire off request to geo.probiddirect.com to remove listing_id = $article_id & tag_id = $tagid
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