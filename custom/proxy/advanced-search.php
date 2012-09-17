<?php
/*THIS SCRIPT IS CALLED VIA JQUERY AJAX FROM THE ADVANCED SEARCH FORM
 * IT DETERMINES WHAT THE USER IS SEARCHING FOR (PROVIDERS OR PROJECTS)
 * IT BUILDS A REQUEST TO GEO SERVER AND TAKES THE SUBSEQUENT RESPONSE AND
 * THEN QUERIES AGAINST THE JOOMLA DB AND RETURNS HTML
 * 
 * 
 */
 
$htmlOutput = "<p class='alert'>You must choose a Category to search through</p>";
$genericError = "<p class='attention'>There were no matches found for your Search.  Add more Tags to get better results.</p>";
//local variables
$isProject = TRUE;
$category_id = (isset($_GET['category_id'])) ? trim($_GET['category_id']) : '';
$tags = (isset($_GET['tags'])) ? substr(trim($_GET['tags']), 0, -1) : '';
$zipcode = (isset($_GET['zipcode'])) ? trim($_GET['zipcode']) : '';
$radius = (isset($_GET['radius'])) ? trim($_GET['radius']) : '';
$keywords = (isset($_GET['keywords'])) ? trim($_GET['keywords']) : '';
$limit = (isset($_GET['limit'])) ? trim($_GET['limit']) : '10';
$page = (isset($_GET['page'])) ? trim($_GET['page']) : '1';
$time = (isset($_GET['time'])) ? trim($_GET['time']) : '720';
$totalRows = 0;
$startNum = ($page == 1) ? 1 : ((($page-1) * $limit) + 1);
$endNum = $page * $limit;
$is_esp = (isset($_GET['esp'])) ? TRUE : FALSE; 
if(!empty($category_id)) {
	$projectsArray = array(78,128,129,130,131);
	require_once("../connections/geo-connect.php");
	if(in_array($category_id, $projectsArray)) {
		//searching for listings
		$url .= "query_for_listings";
		$postStr = "zipcode=" . $zipcode . "&radius=" . $radius . "&tags=" . $tags . "&time=" . $time;
	} else {
		//searching for providers
		$isProject = FALSE;
		$url .= "query_for_users";
		$postStr = "zipcode=" . $zipcode . "&tags=" . $tags;
	}//ends if in_array
	
	//make request to geo and handle response
    $ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_POST, TRUE);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $postStr);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_USERAGENT, $useragent);
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
	$result = curl_exec($ch);	
	$third_pos = explode(',', $result, 3);
	//Pull out tag_ids
    $third_pos = $third_pos[2];
    $third_pos = trim($third_pos);
    $third_pos = str_replace("[", "", $third_pos);
    $third_pos = str_replace("]", "", $third_pos);
    $third_pos = trim($third_pos);
	if(empty($third_pos)) {
		$htmlOutput = $genericError;
	} else {
		set_include_path(get_include_path() . PATH_SEPARATOR . '/home/probid/pear/php');
		require_once('../connections/dbMDB2.php');
		//get total count first
		if($isProject) {
			$ttlquery = "SELECT COUNT(a.`id`) as totalrows FROM `jos_content` a JOIN  `jos_jreviews_content` b ON a.`id` = b.`contentid` JOIN `jos_categories` c ON ";
			$ttlquery .= "a.`catid` = c.`id` JOIN `jos_users` d ON a.`created_by` = d.`id` WHERE a.`id` IN (" . $third_pos . ") AND (a.`catid` = " . $category_id . " OR c.`parent_id` = " . $category_id . ")";	
			//build query while we are here
			$query = "SELECT a.`id`, a.`title`, a.`catid` , a.`fulltext`, a.`created`, a.`images`, a.`hits`, b.`jr_projectstatus`, b.`jr_spcontact`, b.`jr_spofficephone`, b.`jr_spcellphone`, b.`jr_city`, ";
			$query .= "b.`jr_state`, b.`jr_esp`, b.`email`, c.`parent_id`, c.`title` title_cat, d.`username`, e.`text` jr_state_text FROM `jos_content` a JOIN  `jos_jreviews_content` b ON a.`id` = b.`contentid`";
			$query .= "JOIN `jos_categories` c ON  a.`catid` = c.`id` JOIN `jos_users` d ON a.`created_by` = d.`id` JOIN `jos_jreviews_fieldoptions` e ON REPLACE(b.`jr_state`, '*', '') = e.`value` WHERE a.`state` = 1 AND a.`id` IN (" . $third_pos . ") AND (a.`catid` = " . $category_id;
			$query .= " OR c.`parent_id` = " . $category_id . ")";
		} else {  //is provider
			$ttlquery = "SELECT COUNT(a.`id`) as totalrows FROM `jos_content` a JOIN  `jos_jreviews_content` b ON a.`id` = b.`contentid` JOIN `jos_categories` c ON  ";
			$ttlquery .= "a.`catid` = c.`id` WHERE a.`created_by` IN (" . $third_pos . ") AND (a.`catid` = " . $category_id . " OR c.`parent_id` = " . $category_id . ")";
			$query = "SELECT a.`id`, a.`title`, a.`catid` , a.`fulltext`, a.`created`, a.`images`, a.`hits` ,  b.`jr_projectstatus`, b.`jr_spcontact`, b.`jr_spofficephone`, b.`jr_spcellphone`, b.`jr_city`, ";
			$query .= "b.`jr_state`, b.`jr_esp`, b.`email`, c.`parent_id`, c.`title` title_cat, d.`username`, e.`text` jr_state_text FROM `jos_content` a JOIN  `jos_jreviews_content` b ON a.`id` = b.`contentid`";
			$query .= "JOIN `jos_categories` c ON  a.`catid` = c.`id` JOIN `jos_users` d ON a.`created_by` = d.`id` JOIN `jos_jreviews_fieldoptions` e ON REPLACE(b.`jr_state`, '*', '') = e.`value` WHERE a.`state` = 1 AND a.`created_by` IN (" . $third_pos . ") AND (a.`catid` = " . $category_id;
			$query .= " OR c.`parent_id` = " . $category_id . ")";
			if($is_esp) {
				$query .= " AND ( b.`jr_esp` LIKE '*1*' )";
			}
		}//ends if $isProject
		$ttlresult = $conn->query($ttlquery);
		$totalRows = $ttlresult->fetchOne();
		if($totalRows > 0) {
			/*
			 * TURN KEYWORDS INTO ARRAY OF WORDS, STRIP COMMAS AND SPACE
			 * LOOP THROUGH THE ARRAY AND ADD SQL OR a.`title` LIKE '%keyword%' 
			 * */
			if(!empty($keywords)) {
				$keywordsArray = explode(",", $keywords);
				$keywordsQuery = " AND (";
				$keywordsCount = count($keywordsArray);
				foreach($keywordsArray as $key => $value) {
					$keywordsQuery .= " a.`title` LIKE '%" . trim($value) . "%' ";
					//if you uncomment this be sure to add a.`fulltext` to the $query above
					//$keywordsQuery .= " OR a.`fulltext` LIKE '%" . trim($value) . "%' ";
					if($key != $keywordsCount-1) {
						$keywordsQuery .= " OR ";
					}
				}
				$keywordsQuery .= " )";
				$query .= $keywordsQuery;
				$ttlquery .= $keywordsQuery;
				//we must recount totalRows
				$ttlresult = $conn->query($ttlquery);
				$totalRows = $ttlresult->fetchOne();
			}//ends if !empty(keywords)
			//limit the results
			$query .= " LIMIT " . $limit;
			//ensure we start off on the right page
			if($page > 1) {
				$query .= " OFFSET ";
				$query .= ($page-1) * $limit;
			}//ends if $page > 1
			$result = $conn->query($query);
			//IF NO MATCHES
	        if($result->numRows() < 1) {
	            $htmlOutput = $genericError;
	        } 
	        else {
	        	$htmlOutput = "<div id='jr_pgResults'><!--  BLOGVIEW  -->";
				$htmlOutput .= "<div class='ja-moduletable moduletable  clearfix'><h3><span>Search Results</span></h3></div>";
				//add pagination
	            $htmlOutput .= "<div class='pbd-pagination'>";
			    //logic for paging
			    if($page > 1) {
			    	$htmlOutput .= "<a id='prevPBDLink' href='#searchResults' onclick='advancedSearchRequest(as_page-1);'>Previous</a>  ";
			    }
			    $htmlOutput .= "Displaying " . $startNum . " to ";
    			if($totalRows <= $endNum) {
    				$htmlOutput .= $totalRows . " of " . $totalRows . " results";
				} else {
					$htmlOutput .= $endNum . " of " . $totalRows . " results <a id='nextPBDLink' href='#searchResults' onclick='advancedSearchRequest(as_page+1);'>Next</a>";
				}			
				$htmlOutput .= "</div>";
				
	        	$htmlOutput .= "<div class='jr_blogview'>";
				$count = 1;
	            while($row = $result->fetchRow()) {
					//print_r($row);die;
					$htmlOutput .= "<div class='listItem'><div class='contentInfoContainer'>";
					$htmlOutput .= "<div class='contentTitle'><a href='/advanced-search-redirect?lid=".$row['id']."' class='jr_listingTitle'>".$row['title']."</a></div> ";
					$htmlOutput .= "<div class='contentInfo'>";
					$htmlOutput .= "<ul><li><ul class='ul_contentInfo'><li>by ".$row['username']."</li><li>".date("F d, Y", strtotime($row['created']))."</li></ul></li><li>".$row['title_cat']."</li></ul>";
					
					$htmlOutput .= "<div class='pt_border'><span class='jrHitsWidget' title='Views'><span class='jrIcon jrIconGraph'></span><span class='jrButtonText'>".$row['hits']."</span></span>";
					
					$htmlOutput .= "<span class='jrFavoriteWidget' title='Preferred/Favorites'><span class='jrIcon jrIconFavorites'></span><span id='jr_favoriteCount229'>1</span></span></div>";

					$htmlOutput .= "</div></div>";
					$images = explode("|", $row['images']);
					
					$htmlOutput .= "<div class='contentColumn'>";
					if($row['images']){
						$htmlOutput .= "<div class='contentThumbnail'><a href='/advanced-search-redirect?lid=".$row['id']."'><img src='http://localhost/project_new/images/".$images[0]."' border='0' alt='".$row['title']."' title='".$row['title']."' id='thumb".$row['id']."' style='width: 150px; height: 112px'></a></div>";
					}
					$htmlOutput .= "</div>";
					
					$fulltext = $row['fulltext'];
					$numwords = 13;
					$con_fulltext = strtok($fulltext, " \n");
					while(--$numwords > 0) $con_fulltext .= " " . strtok(" \n");
						if($con_fulltext != $fulltext) $output .= " ";
						
					if(strlen($con_fulltext) < strlen($fulltext))
						$con_fulltext .= "...";
					
					$htmlOutput .= "<div class='fieldGroup'><div class='fieldRow jr_fulltext'><div class='fieldLabel'>Project Description:</div><div class='fieldValue '><p>".$con_fulltext."<a href='/advanced-search-redirect?lid=".$row['id']."'>read more</a></p></div></div></div>";
					
					$htmlOutput .= "<div class='jr_customFields'><div class='fieldGroup address-information'>";
					
					$htmlOutput .= "<div class='fieldRow jr_city'><div class='fieldLabel'>City:</div><div class='fieldValue '>".$row['jr_city']."</div></div>";
					
					$htmlOutput .= "<div class='fieldRow jr_state'><div class='fieldLabel'>State:</div><div class='fieldValue '>".$row['jr_state_text']."</div></div>";
					
					
					if(!$isProject) {  //this is a provider so show more in the results
						$htmlOutput .= "<div class='fieldRow jr_spcontact'><div class='fieldLabel'>Contact:</div><div class='fieldValue '>".$row['jr_spcontact']."</div></div>";
						
						$htmlOutput .= "<div class='fieldRow jr_spofficephone'><div class='fieldLabel'>Phone:</div><div class='fieldValue '>".$row['jr_spofficephone']."</div></div>";
						
						$htmlOutput .= "<div class='fieldRow jr_email'><div class='fieldLabel'>Email:</div><div class='fieldValue '><a href='mailto:" . $row['email'] . "'>" . $row['email'] . "</a></div></div>";
					}
					
					$htmlOutput .= "</div></div>";
					//$htmlOutput .= "Contact - " . $row['jr_spcontact'] . "<br/>";
					//$htmlOutput .= "Phone - " . $row['jr_spofficephone'] . "<br/>";
					//$htmlOutput .= "Mobile - " . $row['jr_spcellphone'] . "<br/>";
					//$htmlOutput .= $row['jr_city'] . ", " . str_replace("*", "", $row['jr_state']) . "<br/>";
					//$htmlOutput .= "Status - " . ucfirst(str_replace("*", "",$row['jr_projectstatus'])) . "<br/>";
					$htmlOutput .= "</div>";
					
					if($count < $result->numRows())
						$htmlOutput .= "";
					$count++;
	            }//ends while loop
	            $htmlOutput .= "</div><div class='clr'>&nbsp;</div>";
	            $htmlOutput .= "</div><!-- end jr_pgResults --><div class='clr'>&nbsp;</div>";
	            
	        }//ends if else numRows < 1
		}// if $totalRows > 0		
		else {
            $htmlOutput = $genericError;
		}//ends if/else $totalRows > 0
		//kill db connection
		require_once("../connections/dbMDB2-disconnect.php");
	}//ends if $third_pos (geo response) is empty
	//kill connections
    require_once("../connections/geo-disconnect.php");  
}//ends if !empty $category_id  
//TESTING PURPOSES
//$htmlOutput .= $keywordsQuery;
//$htmlOutput .= "<br/>url - " . $url . "<br/>postStr - " . $postStr . " totalrows - " . $totalRows;
echo $htmlOutput;