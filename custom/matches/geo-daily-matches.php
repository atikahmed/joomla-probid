<?php
/**
 * This script is called by cron periodically (every 5 minutes)
 * and requests provider to project matches
 * it then builds emails and sends them to the users returned in the json result from 
 * the geo server, this script should be called often (every 5 minutes)
 */
require_once("../connections/geo-connect.php");
$url .= "fetch_matches";
$postStr = "match_count=20";
//fetch matches
//SETUP CURL CALL TO GEO SERVER
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_POST, TRUE);
curl_setopt($ch, CURLOPT_POSTFIELDS, $postStr);
curl_setopt($ch, CURLOPT_USERAGENT, $useragent);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
$matches = curl_exec($ch);
//testing purposes
//$matches = '[0, 0, []]';
//$matches = '[0, 5, [[89, [113, 102, 118]], [92, [113, 102, 118]], [68, [113, 102, 118]], [104, [102]], [166, [102]]]]';
$matches = substr($matches, 1);  //get rid of the front [
$temp_array = explode(",", $matches, 3); //break into a 3 position array
$success = $temp_array[0];
//test for 0 = success code in the first spot on the array
if($success == 0) {
    $subject = "Matching Projects on ProbidDirect.com";
    $email_from = "From: notify@probiddirect.com";
    $user_list = explode("],", $temp_array[2]);
    set_include_path(get_include_path() . PATH_SEPARATOR . '/home/probid/pear/php');
    require_once('../connections/dbMDB2.php');
    foreach($user_list as $value) {
        //get the email for each entry and send them an email
        $value = str_replace("[", "", $value);
        $value = str_replace("]","", $value);
        $value = str_replace(" ", "", $value);
        $temp_array_two = explode(",", $value, 2);
        if(!empty($temp_array_two[0])) {
        	//TODO:  ADD CODE TO CHECK DB FOR USER GROUP ID 20 OR 21 IN jos_user_usergroup_map
            //get email address for this user_id
            $query = 'SELECT email FROM jos_users WHERE id = ' . $temp_array_two[0];
            $result = $conn->query($query);
            $email_to = $result->fetchOne(0,0);
            $email_body = "ProbidDirect.com has found projects matching your profile.  ";
            $email_body .= "<a href='{$currentServerDomain}job-card-redirect?red=1&ids={$temp_array_two[1]}'>Click here</a>";
            $email_body .= " to view a list of the projects matching your profile.";
            mail($email_to, $subject, $email_body, $email_from);
        } else {
            echo $matches;
        }//ends if empty
    }//ends foreach
    //clean up resources
    require_once('../connections/dbMDB2-disconnect.php');
} else {
    mail('admin@probiddirect.com', 'Fetch Matches Failed', 'The custom/matches/geo-daily-matches.php script failed when it got this response from Geo - ' . $matches);
	$myfile = "../logs/geo-daily-matches-errors.txt";
	$fh = fopen($myfile, 'a');
	$logError = "/custom/matches/geo-daily-matches.php script failed when it got this response from Geo server - \n";
	$logError .= "Matches: {$matches}\n";
	fwrite($fh, $logError);
	fclose($fh);
}
require_once("../connections/geo-disconnect.php");