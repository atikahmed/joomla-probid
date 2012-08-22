<?php
//grab get or post variables sent in and form an http request to the geo server
// create a new cURL resource
require_once("../connections/geo-connect.php");
$url .= "query_for_listings";
// grab URL and pass it to the browser
$htmlOutput = curl_exec($ch);


require_once("../connections/geo-disconnect.php");
echo $htmlOutput;
?>