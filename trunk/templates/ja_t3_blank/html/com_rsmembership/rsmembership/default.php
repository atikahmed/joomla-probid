<?php
/**
* @version 1.0.0
* @package RSMembership! 1.0.0
* @copyright (C) 2009-2010 www.rsjoomla.com
* @license GPL, http://www.gnu.org/licenses/gpl-2.0.html
*/

defined('_JEXEC') or die('Restricted access');
?>

<script type="text/javascript">
	jQuery(document).ready(function($){
		$('#ja-mainbody').width('67.5%');
	});
</script>

<style type="text/css">
.rsm_container<?php echo $this->escape($this->params->get('pageclass_sfx')); ?>
{
	overflow: hidden;
	margin-bottom: 1%;
	<?php if ($this->columns_no > 1) { ?>
	width: <?php echo $this->columns_no == 2 ? '46%' : '30%'; ?>;
	margin-right: 1%;
	margin-left: 1%;
	float: left;
	<?php } ?>
}
</style>

<?php if (RSMembershipHelper::isJ16()) { ?>
	<?php if ($this->params->get('show_page_heading', 1)) { ?>
	<h1><?php echo $this->escape($this->params->get('page_title')); ?></h1>
	<?php } ?>
<?php } else { ?>
	<?php if ($this->params->get('show_page_title', 1)) { ?>
	<div class="componentheading<?php echo $this->escape($this->params->get('pageclass_sfx')); ?>"><?php echo $this->escape($this->params->get('page_title')); ?></div>
	<?php } ?>
<?php } ?>


<ul class="pt_membership">
	<li></li>
	<li>Cost of different membership plans</li>
	<li>Unlimited support</li>
	<li>No. of Jobs listings you can apply to</li>
	<li></li>
</ul>

<?php $i = 0; foreach ($this->items as $item) {
$catid 		= $item->category_id ? '&catid='.$item->category_id.':'.JFilterOutput::stringURLSafe($item->category_name) : '';
$link  		= JRoute::_('index.php?option=com_rsmembership&view=membership'.$catid.'&cid='.$item->id.':'.JFilterOutput::stringURLSafe($item->name).$this->Itemid);
$apply_link = JRoute::_('index.php?option=com_rsmembership&task=subscribe'.$catid.'&cid='.$item->id.':'.JFilterOutput::stringURLSafe($item->name).$this->Itemid);
$price 		= RSMembershipHelper::getPriceFormat($item->price);
$image 		= !empty($item->thumb) ? JHTML::_('image', 'components/com_rsmembership/assets/thumbs/'.$item->thumb, $item->name, 'class="rsm_thumb'.$this->escape($this->params->get('pageclass_sfx')).'"') : '';

$replace = array('{price}', '{buy}', '{extras}', '{stock}');
$with 	 = array($price, '<a href="'.$link.'">'.JText::_('RSM_SUBSCRIBE').'</a>', '', $item->stock > -1 ? $item->stock : 0);
$item->description = str_replace($replace, $with, $item->description);
?>
<div class="rsm_container<?php echo $this->escape($this->params->get('pageclass_sfx')); ?> pt_membership">

<ul>
	<li><a href="<?php echo $link; ?>"><?php $names = explode("-", $item->name); echo $names[1]; ?></a></li>
	<li><?php echo $price; ?></li>
	<li><span class="<?php if($price == 'FREE!') echo 'unlimited_no'; else echo 'unlimited_yes'; ?>"><?php if($price == 'FREE!') echo 'no'; else echo 'yes'; ?></span></li>
	<li></li>
	<li>
		<div class="membership_plan">
			<a href="<?php echo $apply_link; ?>"><span>Sign up</span></a>
		</div>
	</li>
</ul>
<?php /*
<h2 class="rsm_title contentheading<?php echo $this->escape($this->params->get('pageclass_sfx')); ?>"><?php if ($this->params->get('show_category', 0)) { ?><?php echo $item->category_id ? $item->category_name : JText::_('RSM_NO_CATEGORY'); ?> - <?php } ?><a href="<?php echo $link; ?>"><?php echo $item->name; ?></a> - <?php echo $price; ?></h2>
<?php echo $image; ?>
<?php echo $item->description; ?>
<span style="clear: left; display: block"></span>

<?php if ($this->show_buttons == 1 || $this->show_buttons == 2) { ?>
	<a href="<?php echo $link; ?>" class="rsm_details"><?php echo JText::_('RSM_DETAILS'); ?></a>
<?php } ?>

<?php if ($this->show_buttons == 2 || $this->show_buttons == 3) { ?>
	<a href="<?php echo $apply_link; ?>" class="rsm_button"><?php echo JText::_('RSM_SUBSCRIBE'); ?></a>
<?php } ?>
</div>
<?php $i++; ?>
<?php if (($this->params->get('columns_no', 1) == 2 && $i % 2 == 0) || ($this->params->get('columns_no', 1) == 1)) { ?>
<span class="rsm_clear"></span>
<?php } ?>
*/?>
</div>
<?php } ?>

<?php if ($this->params->get('show_pagination', 0) && $this->pagination->get('pages.total') > 1) { ?>
<table width="100%" border="0" cellspacing="0" cellpadding="0">
<tr>
	<td align="center" class="sectiontablefooter<?php echo $this->escape($this->params->get('pageclass_sfx')); ?>"><?php echo $this->pagination->getPagesLinks(); ?></td>
</tr>
<tr>
	<td align="right"><?php echo $this->pagination->getPagesCounter(); ?></td>
</tr>
</table>
<?php } ?>