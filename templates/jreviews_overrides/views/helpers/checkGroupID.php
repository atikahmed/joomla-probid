

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
?>



 