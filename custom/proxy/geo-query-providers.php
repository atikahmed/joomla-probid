<?php
/**
 * This script is called via ajax as a gateway to the Geo server (geo.probiddirect.com).  B/c of security restrictions around
 * ajax/browser calls, this script must be called and simply acts as a relay for the data passed back by Geo
 */
$htmlOutput = "";
$genericError = "<p class='attention'>No matches were found for your project<br/>Try adding more tags (above) to your project and search again</p>";
//IF NO TAGS THEN ALERT USER TO ADD TAGS
if(empty($_GET['tags'])) {
	//ALERT USER TO ADD TAGS AND DO NOT MOVE FORWARD WITH QUERIES
	$htmlOutput = "<p class='alert'>You must have at least 1 Tag added to Search for Providers.  Please add Tags (above) and Search again.</p>";	
} else {
    //GET VARIABLES FOR SEARCH
    $zipcode = $_GET['zipcode'];
    $article_id = $_GET['articleId'];
    $tags = $_GET['tags'];

    //CONNECTION STRING FOR CURL TO GEO
    require_once("../connections/geo-connect.php");
    $url .= "query_for_users";
    $geo_tags = "?tags=";
	foreach($tags as $value) {
		$geo_tags .= $value . ",";
	}
	$geo_tags = substr($geo_tags, 0, -1);
	//create GET request from CURL
    $postStr = $geo_tags . "&zipcode=" . $zipcode;
    //SETUP CURL CALL TO GEO SERVER
	curl_setopt($ch, CURLOPT_URL, $url . $postStr);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_POST, FALSE);
	curl_setopt($ch, CURLOPT_USERAGENT, $useragent);
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
	$result = curl_exec($ch);
    //HARDCODED FOR TESTING
    //$result = '[0, "0 success", [89, 104, 166]]';
    //$result = '[0, "0 success", [1,2,3]]';
    //Get the third position of the response
    $third_pos = explode(',', $result, 3);
	//Pull out tag_ids
    $third_pos = $third_pos[2];
    $third_pos = trim($third_pos);
    $third_pos = str_replace("[", "", $third_pos);
    $third_pos = str_replace("]", "", $third_pos);
    $third_pos = trim($third_pos); //clean up one more time to test for 0 results back
    //IF WE GET BACK AN EMPTY SET THEN SEND MESSAGE BACK TO PAGE
    if(empty($third_pos)) {
        $htmlOutput = $genericError;
    } else {
        //WE NOW HAVE THE LIST OF MATCHING PROVIDER IDS
        $list_providers = "(" . $third_pos . ")";
        //now get into db and get user info
        set_include_path(get_include_path() . PATH_SEPARATOR . '/home/probid/pear/php');
        require_once('../connections/dbMDB2.php');
        //GET LIST OF PROVIDERS ALREADY ON PROJECT SO WE DO NOT PUT ADD TO PROJECT BUTTON UNDER THEIR NAME
        $query = "SELECT id FROM  `jos_view_project_users` WHERE article_id = " . $article_id . " AND accepted = 1";
        $result = $conn->query($query);
        $providers_on_project = array();
        //POPULATE ARRAY OF PROVIDERS ON PROJECT
        while($row = $result->fetchRow()) {
            array_push($providers_on_project, $row['id']);
        }
		//GET LIST OF PROVIDERS INVITED TO PROJECT SO WE DO NOT PUT ADD TO PROJECT BUTTON UNDER THEIR NAME
        $query = "SELECT id FROM  `jos_view_project_users` WHERE article_id = " . $article_id . " AND accepted = 0";
        $result = $conn->query($query);
        $providers_invited_to_project = array();
        //POPULATE ARRAY OF PROVIDERS INVITED TO PROJECT
        while($row = $result->fetchRow()) {
            array_push($providers_invited_to_project, $row['id']);
        }
		//FILTER THE LIST OF PROVIDERS TO GET ONLY THE PREMIUM (user_group = 17,21,25) ONES BACK
		$query = "SELECT `user_id` FROM `jos_user_usergroup_map` WHERE `group_id` IN (17,21,25) AND `user_id` IN " . $list_providers;
		$result = $conn->query($query);
		//test to see if we got anything back
		if($result->numRows() < 1) {
			//no results
			$htmlOutput = $genericError;
		} else {
			//NOW REBUILD $list_providers from filtered list of users
			$list_providers = "(";
			while($row = $result->fetchRow()) {
				$list_providers .= $row['user_id'] . ",";
			}
			$list_providers = substr($list_providers, 0, -1);
			$list_providers .= ")";
			//now get the users profile page information
	        $query = "SELECT * FROM `jos_vpbd_content` WHERE `created_by` IN " . $list_providers . " AND `jr_zipcoderadius` <> ''";
	        $result = $conn->query($query);
	        //IF NO MATCHES
	        if($result->numRows() < 1) {
	            $htmlOutput = $genericError;
	        } else {
	            while($row = $result->fetchRow()) {
	                    $htmlOutput .= '<div class="service-provider">';
	                    $htmlOutput .= $row['title'];
	                    if(in_array($row['created_by'], $providers_on_project)) {
	                        $htmlOutput .= "<br/><span class='on-project'>Already on Project</span>";
	                    } elseif (in_array($row['created_by'], $providers_invited_to_project)) {
	                    	$htmlOutput .= "<br/><span class='invited'>Invited to Project</span>";
	                    } else {
	                        $htmlOutput .= "<br/><button class='addSP' onclick='addToProject(" . $row['created_by'] . ", this);'>Invite to Project</button><br/>";
	                    }
	                    $htmlOutput .= '</div>';
	            }//ends while loop
	        }//ends if else numRows < 1 on line 90
        }//ends if else numRows < 1 on line 75
        require_once('../connections/dbMDB2-disconnect.php');
    }//ends if(empty($third_pos))
    require_once("../connections/geo-disconnect.php");
}//ENDS IF NO TAGS IF ELSE
echo $htmlOutput;
?>