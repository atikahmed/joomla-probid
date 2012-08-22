<?php
set_include_path(get_include_path() . PATH_SEPARATOR . '/home/probid/pear/php');
require_once('../connections/dbMDB2.php');
$thankyou = "<p class='notice'>Your email has been successfully sent</p>";
//grab data
try {
$articleId = $_GET['articleId'];
$toemailsent = $_GET['emailTo'];
$userwhosent = $_GET['user_id'];
$comments = $_GET['comments'];
$url = $_GET['url'];
$senderemail = $_GET['sender_email'];
//build from email for email headers
$headers = "From: {$senderemail}" . "\r\n";
$headers .= 'MIME-Version: 1.0' . "\r\n";
$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
//add link to comments
$query = "SELECT `title` FROM `jos_content` WHERE `id` = {$articleId}";
$result = $conn->query($query);
while($row = $result->fetchRow()) {
	$doctitle = $row['title'];
}
$message = "Link: <a href='{$url}/job-card-redirect?red=0&lid=102&email=1'>{$doctitle}</a> <br/>Comments: {$comments}";
//send email
mail($toemailsent, "ProbidDirect Project", $message, $headers);
//enter into db
$query = "INSERT INTO jos_pbd_emails_sent(articleId, toemailsent, userwhosent, comments) VALUES";
$query .= "({$articleId}, '{$toemailsent}', {$userwhosent}, '{$comments}')";
$result = $conn->query($query);
require_once('../connections/dbMDB2-disconnect.php');
//return success or failure message
} catch (Exception $e) {
	$myfile = "../logs/ajax_email.txt";
	$fh = fopen($myfile, 'a');
	$logError = "Could not send email via ajax\n";
	$logError .= "Reason: {$e}\n";
	fwrite($fh, $logError);
	fclose($fh);
	$thankyou = "<p class='alert'>Email could not be sent, contact Site Admin</p>";
}
echo $thankyou;
?>