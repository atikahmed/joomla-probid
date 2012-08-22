<?php
set_include_path(get_include_path() . PATH_SEPARATOR . '/home/probid/pear/php');
require_once('../connections/dbMDB2.php');
$project_id = $_GET['id'];
//get list of providers and their business profile listing title
$query = "SELECT a.article_id, a.user_id, a.position, a.accepted, b.title, b.`jr_zipcoderadius` FROM jos_probid_friends a INNER JOIN `jos_vpbd_content` b ";
$query .= "ON a.user_id = b.created_by WHERE b.`jr_zipcoderadius` <> '' AND a.article_id = " . $project_id . " ORDER BY a.accepted, a.position DESC";
$result = $conn->query($query);
$htmlOutput = "<fieldset><h4>Service Providers on Project</h4>";
//If no results echo no results
if($result->numRows() < 1) {
	$htmlOutput .= "No Providers on this Project Yet";
} else {
	//map out providers on project
	while($row = $result->fetchRow()) {
	        $htmlOutput .= '<div class="service-provider">';
	        $htmlOutput .= $row['title'];
			if($row['accepted'] == 1) {  //they have accepted and are on project
				if($row['position'] == 0) {  //they are not project manager
					$htmlOutput .= "<button class='make-pm' onclick='makeProjectManager(" . $row['user_id'] . ");'>Make PM</button>";
				} else {  //they are project manager
					$htmlOutput .= "<button class='remove-pm' onclick='removeAsProjectManager(" . $row['user_id'] . ")'>Remove as PM</button>";
				}//ends if position == 0, not project manager
		        $htmlOutput .= "<br/><button class='removeSP' onclick='removeFromProject(" . $row['user_id'] . ");'>Remove</button><br/>";
			} else {//they have not accepted the invitation but have been invited
					$htmlOutput .= "<br/><button class='' onclick='uninviteFromProject(" . $row['user_id'] . ");'>Uninvite</button><br/>";
			}//ends if accepted == 1		
	        $htmlOutput .= '</div>';
	}//ends while loop
}//ends if numRows() < 1
$htmlOutput .= "</fieldset>";	
require_once('../connections/dbMDB2-disconnect.php');
echo $htmlOutput;