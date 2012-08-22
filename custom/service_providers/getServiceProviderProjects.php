<?php
set_include_path(get_include_path() . PATH_SEPARATOR . '/home/probid/pear/php');
require_once('../connections/dbMDB2.php');
$user_id = $_GET['user_id'];
//get list of providers and their business profile listing title
$query = "SELECT a.article_id, a.user_id, a.position, a.accepted, b.`cat_id`, b.`cat_alias`, b.`content_id`, b.`content_alias`,";
$query .= "b.`title` FROM jos_probid_friends a JOIN  `jos_pbd_art_cat` b ON a.article_id = b.content_id WHERE a.user_id = {$user_id}";
$query .= " ORDER BY `accepted`, `position` ";
$result = $conn->query($query);
$htmlOutput = "<fieldset><h3>Project Invites, Accepts & Ignores</h3>";
//If no results echo no results
if($result->numRows() < 1) {
	$htmlOutput .= "You are not currently on any Projects";
} else {
	//map out providers on project
	while($row = $result->fetchRow()) {
        $htmlOutput .= '<div class="project-detail">';
		$htmlOutput .= "<a href='/projects/" . $row['cat_id'] . "-" . $row['cat_alias'] . "/" . $row['content_id'] . "-" . $row['content_alias'] . "'";
		$htmlOutput .= " target='_blank'>";
		$htmlOutput .= "<h3>" . $row['title'] . "</h3></a>";
		if($row['accepted'] == 0) {  //user is invited but not accepted
			$htmlOutput .= "<button class='acceptInvite' onclick='acceptInvite(" . $row['user_id'] . ", " . $row['article_id'] . ")'>Accept Invite</a>&nbsp;";
			$htmlOutput .= "<button class='ignoreInvite' onclick='ignoreInvite(" . $row['user_id'] . ", " . $row['article_id'] . ")'>Ignore Invite</a><br/>";
		} elseif ($row['accepted'] == 99) {
			$htmlOutput .= "<p class='attention'>You have ignored this invite</p>";
		} else {  //user is on the project 
			$htmlOutput .= "<p class='notice'>";
			if($row['position'] == 1) {//they are project manager
				$htmlOutput .= "You are the Project Manager";
			} else {//they are not project manager
				$htmlOutput .= "You are on the Project";
			}//ends if they are project manager
			$htmlOutput .= "</p>";
		}// ends if accepted == 0
		$htmlOutput .= "</div>";
	}//ends while loop
}//ends if numRows() < 1
$htmlOutput .= "</fieldset>";	
require_once('../connections/dbMDB2-disconnect.php');
echo $htmlOutput;