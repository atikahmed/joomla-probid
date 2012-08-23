<?php
/**
 * Vertical scroll recent article
 *
 * @package Vertical scroll recent article
 * @subpackage Vertical scroll recent article
 * @version   2.0 July 15, 2011
 * @author    Gopi http://www.gopiplus.com
 * @copyright Copyright (C) 2010 - 2011 www.gopiplus.com, LLC
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 *
 */

// no direct access
defined('_JEXEC') or die;

@$vspost_height = @$args['vspost_height'];
@$vspost_display = @$args['vspost_display'];

if ( ! empty($items) ) 
{
	$vspost_count = 0;
	foreach ( $items as $item ) 
	{
		@$vspost_title =  mysql_real_escape_string($item->title);
		@$vspost_link = JRoute::_(ContentHelperRoute::getArticleRoute($item->slug, $item->catslug, $item->sectionid));
		
		$dis_height = @$vspost_height ."px";
		@$vspost_html = @$vspost_html . "<div class='crs_div' style='height:$dis_height;padding:2px 0px 2px 0px;'>"; 
		@$vspost_html = @$vspost_html . "<a href='$vspost_link'>$vspost_title</a>";
		@$vspost_html = @$vspost_html . "</div>";
		
		@$vspost_x = @$vspost_x . "crs_array[$vspost_count] = '<div class=\'crs_div\' style=\'height:$dis_height;padding:2px 0px 2px 0px;\'><a href=\'$vspost_link\'>$vspost_title</a></div>'; ";	
		@$vspost_count++;
	}
}
@$vspost_height = $vspost_height + 4;
if($vspost_count >= $vspost_display)
{
	$vspost_count = $vspost_display;
	$vspost_newheight = ($vspost_height  * $vspost_display);
}
else
{
	$vspost_count = $vspost_count;
	$vspost_newheight = ($vspost_count * $vspost_height );
}
$ivrss_height1 = @$vspost_height ."px";
?>
<div style="padding-top:8px;padding-bottom:8px;">
  <div style="text-align:left;vertical-align:middle;text-decoration: none;overflow: hidden; position: relative; margin-left: 1px; height: <?php echo $ivrss_height1; ?>;" id="crs_Holder"><?php echo @$vspost_html; ?></div>
</div>
<script type="text/javascript">
var crs_array	= new Array();
var crs_obj	= '';
var crs_scrollPos 	= '';
var crs_numScrolls	= '';
var crs_heightOfElm = '<?php echo $vspost_height; ?>'; // Height of each element (px)
var crs_numberOfElm = '<?php echo $vspost_count; ?>';
var crs_scrollOn 	= 'true';
function vsra_createscroll() 
{
	<?php echo $vspost_x; ?>
	crs_obj	= document.getElementById('crs_Holder');
	crs_obj.style.height = (crs_numberOfElm * crs_heightOfElm) + 'px'; // Set height of DIV
	vsra_content();
}
</script>
<script type="text/javascript">
vsra_createscroll();
</script>