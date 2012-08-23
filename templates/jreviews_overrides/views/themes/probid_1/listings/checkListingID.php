<!--Check to see if this SP/Vendor has setup their Business Listing-->

<?php

global $mainframe;
//$db =& JFactory::getDBO();
$User =& JFactory::getUser();

//Get SP/Vendor business listing id and title
$query="SELECT 	jos_content.id, jos_content.title 
				FROM  		jos_content
				WHERE 		jos_content.catid IN(79,80) AND jos_content.created_by =  '$User->id' AND jos_content.state = 1 ";
$resultListing=mysql_query($query);
$numListing=mysql_numrows($resultListing);
$businessListingID=mysql_result($resultListing,$i,"id");
$businessListingTitle=mysql_result($resultListing,$i,"title");
?>