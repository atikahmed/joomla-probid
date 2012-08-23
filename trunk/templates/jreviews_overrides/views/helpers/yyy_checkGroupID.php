

<?php 
/**********************************************************************************
 Purpose is to identify user group(s) no longer available in JFactory::getUser();
 due to new multiple group structure.
 **********************************************************************************/
 ?>
 

<?php
global $mainframe;
$db =& JFactory::getDBO();

$User =& JFactory::getUser();
$myUserID =  $User->id;

//User can belong to multiple groups.
$query = "SELECT group_id FROM #__user_usergroup_map WHERE user_id = " .$myUserID;
$db->setQuery( $query );
$groupDetailsInArray = $db->loadObjectList();
$groupID = $groupDetailsInArray[0]->group_id;

//Get Portfolio Projects

$listing_id = $listing['Listing']['listing_id'];

$query="SELECT 	listing_type
				FROM  		#__vpbd_content_criteria
				WHERE 		listing_id = 222"; //.$listing_id;
$result=mysql_query($query);
$listingType=mysql_numrows($result);
?>

<?php
 //echo "<p>Your name is {$user->name}, your email is {$user->email}, and your username is {$user->username}</p>";
 //echo "<p>Your userID is $myUserID.</p>";
?>

 