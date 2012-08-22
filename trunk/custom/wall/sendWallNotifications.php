<?php
/**
 * This script is called every minute by a cron job
 * It checks the jos_probid_wall_notifications table for unsent emails about new posts on projects
 * from there it builds a list of users (excluding the person who made the post - no need to notify the originator)
 * and then gets their emails
 * it sends them an email with a link back to the project job card
 */
set_include_path(get_include_path() . PATH_SEPARATOR . '/home/probid/pear/php');
require_once('../connections/dbMDB2.php');
$limit = 10;
//user who made post
$working_project_owner = null;
//variables for each loop
$working_notification_id = null;
$working_post_user = null;
$working_article = null;
//variables for email
$site_url = "http://dev.probiddirect.com/job-card-redirect?red=0&lid=";
$email_list = "";
$subject = "New Post on Job Card at ProbidDirect.com";
$body = "";
$headers = "From: notify@probiddirect.com";
$posted_by_owner = TRUE;
$working_friends_ids = "(";
$query = "SELECT id, article_id, user_id FROM jos_probid_wall_notifications WHERE email_sent = 0 ORDER BY id LIMIT " . $limit;
$result = $conn->query($query);
while($row = $result->fetchRow()) {
	$working_notification_id = $row['id'];
	$working_post_user = $row['user_id'];
	$working_article = $row['article_id'];
	//get providers on project
	$query2 = "SELECT user_id FROM jos_probid_friends WHERE article_id = " . $working_article;
	$result2 = $conn->query($query2);
	while($row2 = $result2->fetchRow()) {
		if($working_post_user == $row2['user_id']) {
			$posted_by_owner = FALSE;
		}
		if($working_post_user != $row2['user_id']) {
			$working_friends_ids .= $row2['user_id'] . ",";
		}
	}//ends while
	//if not posted by the project owner, then go get project owner id
	if(!$posted_by_owner) {
		//go get owner id
		$query3 = "SELECT created_by FROM jos_content WHERE id = " . $working_article;
		$result3 = $conn->query($query3);
		while($row3 = $result3->fetchRow()) {
			$working_project_owner = $row3['created_by'];
		}//ends while
		$working_friends_ids .= $working_project_owner . ")";
	}//ends if
	else {
		//you have the owner id so close out list of ids and clean up string for sql query
		$working_friends_ids = substr($working_friends_ids, 0, -1);
		$working_friends_ids .= ")";
	}
	//now get list of email addresses for to send notification
	$email_query = "SELECT `email` FROM `jos_users` WHERE `id` IN " . $working_friends_ids;
	$result4 = $conn->query($email_query);
	while($row4 = $result4->fetchRow()) {
		$email_list .= $row4['email'] . ",";
	}
	$email_list = substr($email_list, 0, -1);
	//send email
	$body = "<a href='". $site_url . $working_article . "'>Click Here</a> to view latest post on Project Job Card";
	mail($email_list, $subject, $body, $headers);
	//reset variables for next loop through
	$email_list = "";
	$working_friends_ids = "(";
	//now clean up table so email shows sent
	$cleanup_query = "UPDATE jos_probid_wall_notifications SET email_sent = 1 WHERE id = " . $working_notification_id;
	$result5 = $conn->query($cleanup_query);
}//ends while
require_once('../connections/dbMDB2-disconnect.php');