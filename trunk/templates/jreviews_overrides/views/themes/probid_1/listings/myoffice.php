<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<style>
.leftSideTitle{
	float:left;
	width:60%;
	text-align:left;
}

.leftSideBox {
	float: left;
	height: 200px;
	width: 60%;
	border-style: solid;
	border-width: thin;
	border-color: #000;
	padding: 3px;
	word-wrap: break-word;
	overflow:auto;
}

.middleClear {
	width: 100%;
	height: 15px;
	clear:both;
}

.rightSideTitle{
	float:right;
	width:37%;
	text-align:left;
}

.rightSideBox {
	float: right;
	vertical-align:middle;
	height: 200px;
	width: 37%;
	border-style: solid;
	border-width: thin;
	border-color: #000;
	padding: 3px;
	word-wrap: break-word;
	overflow:auto;
}
</style>

<?php
//Get the listing owner User Group
$query = "SELECT group_id FROM #__user_usergroup_map WHERE user_id = " .$listingUserID;
$db->setQuery( $query );
$groupDetailsInArray = $db->loadObjectList();
$groupID = $groupDetailsInArray[0]->group_id;

//Get the business listing (contact information) link
$query = "SELECT jos_content.ID, jos_content.title, jos_categories.alias 
				FROM jos_content INNER JOIN  jos_categories ON jos_content.catid = jos_categories.id
				WHERE jos_content.ID =  $myListingID";
$db->setQuery( $query );
$ListingDetailsInArray = $db->loadObjectList();
$myListingAlias = $ListingDetailsInArray[0]->alias;

//Get projects this user is currently assigned to - My Projects
$query="SELECT jos_probid_friends.article_id, jos_content.title 
				FROM jos_probid_friends INNER JOIN jos_content 
				ON jos_probid_friends.article_id = jos_content.ID WHERE user_id =  '$listingUserID' AND jos_content.state = 1 GROUP BY article_id, title";
$resultProjects=mysql_query($query);
$numProjects=mysql_numrows($resultProjects);

//Get Portfolio Projects
$query="SELECT 	jos_content.id, jos_content.title 
				FROM  		jos_content
				WHERE 		jos_content.catid = 133 AND jos_content.created_by =  '$listingUserID' AND jos_content.state = 1 ";
$resultPortfolio=mysql_query($query);
$numPortfolio=mysql_numrows($resultPortfolio);

//Get the other providers on those projects - My Team
$query = "SELECT jos_content.ID, jos_content.title, jos_categories.alias 
				FROM jos_content INNER JOIN  jos_categories ON jos_content.catid = jos_categories.id
				WHERE jos_content.created_by IN
					(select user_id from jos_probid_friends where article_id IN
					(select article_id FROM jos_probid_friends WHERE user_id = '$listingUserID')
					AND user_id <> '$listingUserID') AND catid IN(79,80)";
$resultTeam=mysql_query($query);
$numTeam=mysql_numrows($resultTeam);

//Get Preferred Partners (Favorite SP and Vendors)
$query = "SELECT jos_content.ID, jos_content.title, jos_categories.alias 
				FROM jos_content INNER JOIN  jos_categories ON jos_content.catid = jos_categories.id
				WHERE jos_content.ID IN
						(SELECT content_id from jos_jreviews_favorites 
						WHERE user_id = '$listingUserID')
						AND catid IN(79,80) AND jos_content.state = 1";
$resultPartners=mysql_query($query);
$numPartners=mysql_numrows($resultPartners);

//Get Client References (SP) OR Products (Vendors)
if($groupID==20) {
	$catid = 134;
	$headerTitle = 'Client References';
	$editURL = 'my-team/client-references';
	$editTitle = 'references';
	}
elseif ($groupID==21) {
	$catid = 127;
	$headerTitle = 'My Products';
	$editURL = 'my-products';
	$editTitle = 'products';
	}

$query="SELECT jos_content.ID, jos_content.title, jos_categories.alias 
				FROM jos_content INNER JOIN  jos_categories ON jos_content.catid = jos_categories.id
				WHERE 		 jos_content.catid = '$catid' AND  jos_content.created_by =  '$listingUserID' AND jos_content.state = 1 ";
$resultReferencesProducts=mysql_query($query);
$numReferencesProducts=mysql_numrows($resultReferencesProducts);

//Get reviews for this business listing - My Reviews
$query="SELECT jos_jreviews_comments.title, jos_jreviews_comments.comments, jos_jreviews_ratings.ratings_sum
				FROM jos_jreviews_comments INNER JOIN jos_jreviews_ratings 
				ON jos_jreviews_comments.id = jos_jreviews_ratings.reviewid
				WHERE jos_jreviews_comments.pid = $myListingID";
$resultReviews=mysql_query($query);
$numReviews=mysql_numrows($resultReviews);

?>

<?php // echo $listing['Listing']['listing_id'] ?>

<h2>My Office:&nbsp;<?php echo $listing['Listing']['title'] , " - " , $CustomFields->field('jr_city',$listing) , ", " , $CustomFields->field('jr_state',$listing);?></h2>

<h4><?php if($social_bookmarks == true): ?>
			<span class="socialBookmarks">
				<?php echo $Community->socialBookmarks($listing); ?>		
			</span>
			<?php endif;?></h4>

<div class="leftSideTitle">
<h4>
<?php echo "<a href='/$myListingAlias/$myListingID'>My Business</a>"; ?>
<?php if($this->Access->_user->id == $listingUserID):?>&nbsp;&nbsp;&nbsp;<a href="component/jreviews/listings/edit?Itemid=&id=<?php echo $myListingID ?>">edit</a><?php endif?>
</h4></div>

<div class="leftSideBox"><!-- MY COMPANY: Summary -->
		
		<p><?php echo $listing['Listing']['description']; ?></p>
</div>

<div class="rightSideBox"><!-- LOGO or PHOTO -->
		<?php if((!empty($listing['Listing']['images']) || $this->Config->content_default_image) && $enableIntroImage && $introImage):?>
			<!-- MAIN IMAGE -->
			<div class="itemMainImage"><?php echo $introImage;?></div>
		<?php endif;?>
</div>

<div class="leftSideTitle"><h4>Image Gallery<?php if($this->Access->_user->id == $listingUserID):?>&nbsp;&nbsp;&nbsp;<a href="component/jreviews/listings/edit?Itemid=&id=<?php echo $myListingID ?>&anchor=1#images">add/remove images</a><?php endif?></h4></div>

<div class="rightSideTitle"><h4>Our Location</h4></div>
        
<div class="leftSideBox"><!-- IMAGE GALLERY -->
		
		<!-- IMAGE GALLERY -->		
		<?php if(($enableGallery && (($enableIntroImage && $imageCount > 1) || (!$enableIntroImage && $imageCount >= 1)))):?>
			<div class="itemThumbnails clearfix">
			<?php for($i=(int)$enableIntroImage;$i<$imageCount;$i++):?>
				<div><?php echo $Thumbnail->lightbox($listing,$i,array('tn_mode'=>$galleryThumbnailMode,'dimensions'=>array($galleryThumbnailSize)));?></div>
			<?php endfor;?>    
			</div>
		<?php endif;?>	
</div>

<div class="rightSideBox"><!-- MAP -->
		<?php if($show_map && isset($listing['Geomaps']) && abs($listing['Geomaps']['lat']) > 0 && abs($listing['Geomaps']['lon']) > 0):?>
        <?php echo $this->renderControllerView('geomaps','map_detail',array('width'=>'100%','height'=>'194'));?>
        <?php endif;?>
</div>
        
<div class="leftSideTitle"><h4>My Projects</h4></div>

<div class="rightSideTitle"><h4>My Business Reviews</h4></div>
        
<div class="leftSideBox"><!-- MY PROJECTS -->
		<table width="100%">
        <tr><th align="left">Current  PROBID Projects</th>
        
        <!--Show for SP Prem only-->
        <?php if($groupID==20):?>
        <th align="left">Portfolio Projects<?php if($this->Access->_user->id == $listingUserID):?>&nbsp;&nbsp;&nbsp;<a href="my-projects-sp/portfolio">add/remove projects</a><?php endif?></th>
        <?php else: ?>
        <th align="left">&nbsp;</th>
        <?php endif; ?>
        </tr>
        
        <tr valign="baseline"><td>
        <?php 
		$i=0;
		while ($i < $numProjects) {
			$article_id=mysql_result($resultProjects,$i,"article_id");
			$title=mysql_result($resultProjects,$i,"title");
			echo "<b><a href='/cat-projects/$article_id' target='_new'>$title</a></b><br />";
			$i++;
			} ?>
            </td>
            
        <!--Show for SP Prem only-->
        <?php if($groupID==20):?>
            <td>
        <?php 
		$i=0;
		while ($i < $numPortfolio) {
			$id=mysql_result($resultPortfolio,$i,"id");
			$title=mysql_result($resultPortfolio,$i,"title");
			echo "<b><a href='/cat-projects/$id' target='_new'>$title</a></b><br />";
			$i++;
			} ?>
            </td>
            <?php else: ?>
        	<td>&nbsp;</td>
        	<?php endif; ?>
            
            </tr>
            </table>
            </div>

<div class="rightSideBox"><!-- OVERALL RATINGS -->
		<div style="float:left"><strong>Overall Customer Rating:</strong></div><div style="float:right"><?php echo $Rating->overallRatings($listing, 'content'); ?></div>
        <div style="float:left"><?php 
		$i=0;
		while ($i < $numReviews) {
			$title=mysql_result($resultReviews,$i,"title");
			$comments=mysql_result($resultReviews,$i,"comments");
			$ratings_sum=mysql_result($resultReviews,$i,"ratings_sum");
			$myRating=number_format($ratings_sum/3,1);
			echo "<p><b>$title</b>&nbsp;$comments&nbsp;<b>Rating: $myRating</b></p>";
			$i++;
			} ?></div>
</div>

<div class="leftSideTitle"><h4>My Team</h4></div>

<div class="rightSideTitle">	<!--Show for SP Prem only--><h4><?php echo $headerTitle;?><?php if($this->Access->_user->id == $listingUserID):?>&nbsp;&nbsp;&nbsp;<a href="<?php echo $editURL;?>">add/remove <?php echo $editTitle;?></a><?php endif?></h4></div>
        
<div class="leftSideBox"><!-- MY TEAM -->
	<table width="100%">
        <tr><th align="left">Current  PROBID Projects</th><th align="left">Preferred Professionals and Vendors<?php if($this->Access->_user->id == $listingUserID):?>&nbsp;&nbsp;&nbsp;<a href="my-team/my-team-sp">manage</a><?php endif?></th></tr>
        <tr valign="baseline"><td>
       <?php
		$i=0;
		while ($i < $numTeam) {
			$id=mysql_result($resultTeam,$i,"id");
			$title=mysql_result($resultTeam,$i,"title");
			$alias=mysql_result($resultTeam,$i,"alias");
			echo "<b><a href='/$alias/$id' target='_new'>$title</a></b><br />";
			$i++;
			} ?>
            </td>
            <td>
        <?php 
		$i=0;
		while ($i < $numPartners) {
			$id=mysql_result($resultPartners,$i,"id");
			$title=mysql_result($resultPartners,$i,"title");
			$alias=mysql_result($resultPartners,$i,"alias");
			echo "<b><a href='/$alias/$id' target='_new'>$title</a></b><br />";
			$i++;
			} ?>
            </td>
            </tr>
            </table>
		</div>

<div class="rightSideBox"><!-- UNDEFINED -->
		<div align="left">
        <!--Show for SP Prem only-->
        <?php 
		$i=0;
		while ($i < $numReferencesProducts) {
			$id=mysql_result($resultReferencesProducts,$i,"id");
			$title=mysql_result($resultReferencesProducts,$i,"title");
			$alias=mysql_result($resultReferencesProducts,$i,"alias");
			echo "<b><a href='/$alias/$id' target='_new'>$title</a></b><br />";
			$i++;
			} ?>
        </div>
</div>

<div class="middleClear"></div>