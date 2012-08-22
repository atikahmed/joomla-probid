<?php
//when listings are published/unpublished using JReviews this script is called and reacts faster than the JReviews script
//to combat this race condition, this script is set to wait 3 seconds prior to execution
//several JReviews save methods are taking much longer than anticipated and this script is beating their calls (race condition)
//
sleep(3);
set_include_path(get_include_path() . PATH_SEPARATOR . '/home/probid/pear/php');
require_once('../connections/dbMDB2.php');
/**
 * URL is called when Listing is Saved in JReviews OR tags are added/removed from Listing OR Listing is published/unpublished via JReviews
 * AJAX dropdown call (Joey built jQuery function to piggyback JReviews AJAX call on listing results and listing page)
 * This script needs to take a listing id and decide if it should call 
 * add/update OR delete based upon the PUBLISHED state of the listing
 * SEVERAL DIFFERENT LISTING TYPES NOW EXIST IN JREVIEWS, A FILTER EXISTS ON LINE 34/35 TO ENSURE THIS ONLY WORKS FOR PROJECT AND 
 * BUSINESS LISTINGS
 */
if(isset($_POST['listing_id'])) {
	$listing_id = $_POST['listing_id']; //$listing_id = $_GET['listing_id'];
	$post_url = null;
	$isUser = false;
	$isUpdate = true;
	$tags = null;
	$zipcode = null;
	$radius = null;
	$state = null;
	$category_id = null;
	$user_id = null;
	$email = null;
	$postStr = null;
	$project_status = null;
	//check and see if this listing is of listingType (criteriaid) 2 or 7
	$query = "SELECT `listing_type` FROM `jos_vpbd_content_criteria` WHERE `jos_vpbd_content_criteria`.`listing_id` = " . $listing_id;
	$criteria_id = $conn->queryOne($query);
	//$criteria_id = $result->fetchRow();
//GET THE LISTING TYPE (CRITERIA ID) AND ONLY MOVE FORWARD IF THE LISTING IS A PROJECT OR A BUSINESS LISTING (CRITERIA_ID = 2 OR 7)
	if($criteria_id == 2 || $criteria_id == 7) {
		$query = "SELECT * FROM `jos_vpbd_content` WHERE `id` = " . $listing_id;
		$query .= " LIMIT 1";
		$result = $conn->query($query);
		while($row = $result->fetchRow(MDB2_FETCHMODE_ASSOC)) {	
			$zipcode = $row['jr_zipcode'];
			$radius = trim(str_replace("*", "", $row['jr_zipcoderadius']));
			$state = $row['state'];
			$category_id = $row['catid'];
			$user_id = $row['created_by'];
			$project_status = trim(str_replace("*", "", $row['jr_projectstatus']));
		}//ends while
		//state == 1 means update OR $project_status == 'open'
		if ($state != 1) {
			//unpublished listing
			$isUpdate = false;
		}
		//only open projects and business listings '' are updates
		if($project_status != 'open' && !empty($project_status)) {
			//if not 'open' OR not '', then this project is not an update
			$isUpdate = false;
		}
		//radius empty = project
		if (!empty($radius)) {
			//this is a user profile listing
			$isUser = true;
			$query = "SELECT email FROM jos_users WHERE jos_users.id = " . $user_id;
			$result = $conn->query($query);
			while($row = $result->fetchRow(MDB2_FETCHMODE_ASSOC)) {
				$email = $row['email'];
			}//ends while
		}
		//get list of tags
		$query = "SELECT tag_id FROM jos_probid_tags_articles WHERE article_id = " . $listing_id;
		$result = $conn->query($query);

        //see if there are any tags
        if($result->numCols() > 0) {
            while($row = $result->fetchRow(MDB2_FETCHMODE_ASSOC)) {
                $tags .= $row['tag_id'] . ",";
            }//ends while
            $tags = substr($tags, 0, -1);
        } else {
            $tags = '';
        }

		
		//BUILD $postStr
		if(!$isUpdate) {
			if($isUser) {
				$post_url = "delete_user";
				$postStr = "user_id=" . $user_id;
			} else {
				$post_url = "delete_listing";
				$postStr = "listing_id=" . $listing_id;
			}
		} 
		else {
			if($isUser) {
				$post_url = "add_user";
				$postStr = "user_id=" . $user_id . "&";
				$postStr .= "zipcode=" . $zipcode . "&";
				$postStr .= "tags=" . $tags . "&";
				$postStr .= "email=" . $email . "&";
				$postStr .= "radius=" . $radius;
			} else {
				$post_url = "add_listing";
				$postStr = "listing_id=" . $listing_id . "&";
				$postStr .= "zipcode=" . $zipcode . "&";
				$postStr .= "tags=" . $tags;
			}//ends if ($isUser) 
		}//ends if (!$isUpdate)
		
		require_once("../connections/geo-connect.php");
		$fullURL = $url . $post_url;
		//make call to geo server
		$useragent = 'YahooSeeker-Testing/v3.9 (compatible; Mozilla 4.0; MSIE 5.5; http://search.yahoo.com/)';
		curl_setopt($ch, CURLOPT_USERAGENT, $useragent);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_URL, $fullURL);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $postStr);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
		$result = curl_exec($ch);
		
		//kill curl resource
		require_once("../connections/geo-disconnect.php");
		/*
		 * Used for testing by printing out variables to screen

		$user_string = ($isUser) ? 'true' : 'false';
		$update_string = ($isUpdate) ? 'true' : 'false';
		$body = "<h1>Info gathered for Listing ID - " . $listing_id . "</h1>";
		$body .= "Full URL = " . $fullURL . "<br/>";
        $body .= "is user = " . $user_string . "<br/>";
		$body .= "is update = " . $update_string . "<br/>";
		$body .= "tags = " . $tags . "<br/>";
		$body .= "zipcode = " . $zipcode . "<br/>";
		$body .= "radius = " . $radius . "<br/>";
		$body .= "state = " . $state . "<br/>";
		$body .= "Category Id = " . $category_id . "<br/>";
		$body .= "User id = " . $user_id . "<br/>";
		$body .= "email = " . $email . "<br/>";
		$body .= "criteria_id = " . $criteria_id . "<br/>";
		$body .= "project status = " .$project_status . "<br/>";

		$body .= "postStr = " . $postStr . "<br/>";
		$body .= "result======" . $result;
		mail('joey@joeygartin.com', 'testing the api', $body, "From: notify@probiddirect.com");	
		*/
	}//ends if Criteria id is 2 or 7
}//ends main if statement 
//kill db connection
require_once("../connections/dbMDB2-disconnect.php");
