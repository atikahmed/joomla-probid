<?php
/**
* @version 1.0.0
* @package RSMembership! 1.0.0
* @copyright (C) 2009-2010 www.rsjoomla.com
* @license GPL, http://www.gnu.org/licenses/gpl-2.0.html
*/

defined('_JEXEC') or die('Restricted access');
?>

<div class="ja-moduletable moduletable  clearfix">
	<h3><span>My Membership</span></h3>
</div>

<?php if (RSMembershipHelper::isJ16()) { ?>
	<?php if ($this->params->get('show_page_heading', 1)) { ?>
	<h1><?php echo $this->escape($this->params->get('page_title')); ?></h1>
	<?php } ?>
<?php } else { ?>
	<?php if ($this->params->get('show_page_title', 1)) { ?>
	<div class="componentheading<?php echo $this->escape($this->params->get('pageclass_sfx')); ?>"><?php echo $this->escape($this->params->get('page_title')); ?></div>
	<?php } ?>
<?php } ?>

<?php foreach ($this->memberships as $item) { ?>

<?php 
	$db = JFactory::getDBO();
	$query = 'SELECT s.*, cat.name as category_name FROM #__rsmembership_memberships as s, #__rsmembership_categories cat WHERE s.id = ' . $item->membership_id . ' AND s.category_id = cat.id';
	$db->setQuery($query);
	
	$membership = $db->loadObject();
	//print_r($item);die;
	
	$fulltext = $membership->description;
	$numwords = 13;
	$con_fulltext = strtok($fulltext, " \n");
	while(--$numwords > 0) $con_fulltext .= " " . strtok(" \n");
		if($con_fulltext != $fulltext) $output .= " ";
		
	if(strlen($con_fulltext) < strlen($fulltext))
		$con_fulltext .= "...";
		
	$catid 		= $membership->category_id ? '&catid='.$membership->category_id.':'.JFilterOutput::stringURLSafe($membership->category_name) : '';
	$link  		= JRoute::_('index.php?option=com_rsmembership&view=membership'.$catid.'&cid='.$item->membership_id.':'.JFilterOutput::stringURLSafe($item->name).$this->Itemid);
	
	
	
	
	$query = 'SELECT mem.* FROM #__rsmembership_memberships as mem WHERE mem.category_id = ' . $membership->category_id . ' ORDER BY mem.price';
	$db->setQuery($query);
	
	$memberships = $db->loadObjectList();
	
	$count = 0;
	$to_id;
	$flag = 0;
	foreach($memberships as $mem){
		if($flag == 1){
			$to_id = $mem->id;
			break;
		}
			
		$count++;
		
		if($mem->id == $item->membership_id)
			$flag = 1;
	}
	
	$type = "";
	switch($count){
		case 1:
			$type = "rms_none";
			break;
		case 2:
			$type = "rms_piece";
			break;
		case 3:
			$type = "rms_full";
			break;
	}
	
	//print_r($type);die;
		
	
?>

<div class="membershipdiv">
	<ul>
		<li class="img_<?php echo $type; ?>"></li>
		<li class="rsm_description_">
			<span class="rsm_name"><?php echo $item->name; ?></span>
			<?php echo $con_fulltext; ?><a href="<?php echo $link; ?>">read more</a>
		</li>
		<li class="rsm_upgrade_<?php echo $type; ?>">
			<?php if($type == 'rms_full'): ?>
				<div class="woohoo">
					WooHoo :)
				</div>
				<?php /*
			<?php elseif($type == 'rms_piece'): ?>
				<div class="proupaccount">
					<form method="post" action="<?php echo JRoute::_('index.php?option=com_rsmembership&task=upgrade&cid='.$item->id); ?>" name="membershipForm" id="membershipForm" class="membershipForm">
	
						<a href="javascript:void(0);" onclick="document.forms['membershipForm'].submit(); return false;">Upgrade Account Now</a>
						
						<?php echo JHTML::_('form.token'); ?>
						<input type="hidden" name="to_id" id="to_id" value="<?php echo $to_id; ?>" />
						<input type="hidden" name="option" value="com_rsmembership" />
						<input type="hidden" name="view" value="mymembership" />
						<input type="hidden" name="task" value="upgrade" />
						<input type="hidden" name="cid" value="<?php echo $item->id; ?>" />
					</form>
					
				</div> */?>
			<?php else: ?>
				<div class="proupaccount">
					<a href="<?php echo JRoute::_('index.php?option=com_rsmembership&view=mymembership&cid='.$item->id.$this->Itemid); ?>"><?php echo JText::_('Upgrade Account Now'); ?></a>
				</div>
			<?php endif; ?>
		</li>
	</ul>
</div>

<?php 
	if($type == 'rms_none'){
		echo "<div class='rms_nodetails'>no billing details</div>";
		continue;
	}
?>

<table width="99%" class="membershiptable<?php echo $this->escape($this->params->get('pageclass_sfx')); ?>">
<?php if ($this->params->get('show_headings', 1)) { ?>
<tr>
	<th class="sectiontableheader<?php echo $this->escape($this->params->get('pageclass_sfx')); ?>" align="right" width="5%"><?php echo JText::_('#'); ?></th>
 	<th class="sectiontableheader<?php echo $this->escape($this->params->get('pageclass_sfx')); ?>"><?php echo JText::_('RSM_MEMBERSHIP'); ?></th>
	<th class="sectiontableheader<?php echo $this->escape($this->params->get('pageclass_sfx')); ?>"><?php echo JText::_('RSM_MEMBERSHIP_START'); ?></th>
	<th class="sectiontableheader<?php echo $this->escape($this->params->get('pageclass_sfx')); ?>"><?php echo JText::_('RSM_MEMBERSHIP_END'); ?></th>
	<th class="sectiontableheader<?php echo $this->escape($this->params->get('pageclass_sfx')); ?>"><?php echo JText::_('RSM_STATUS'); ?></th>
</tr>
<?php } ?>

<?php $k = 1; ?>
<?php $i = 0; ?>

<tr class="sectiontableentry<?php echo $k . $this->escape($this->params->get('pageclass_sfx')); ?>" >
	<td align="right"><?php echo $this->pagination->getRowOffset($i); ?></td>
	<td><a href="<?php echo JRoute::_('index.php?option=com_rsmembership&view=mymembership&cid='.$item->id.$this->Itemid); ?>"><?php echo $this->escape($item->name); ?></a></td>
	<td><?php echo date($this->date_format, RSMembershipHelper::getCurrentDate($item->membership_start)); ?></td>
	<td><?php echo $item->membership_end > 0 ? date($this->date_format, RSMembershipHelper::getCurrentDate($item->membership_end)) : JText::_('RSM_UNLIMITED'); ?></td>
	<td><?php echo JText::_('RSM_STATUS_'.$item->status); ?></td>
</tr>

<!-- BEGIN ADD CODE Focus Data Systems -->
    <?php 
		$msName = $this->escape($item->name);
		$msText = 'FREE';
		$pos = strpos($msName, $msText);
	?>
    
    <?php if($pos !== false):?>
    <tr>
    <td>&nbsp;</td>
    <td colspan="4">
    <a href="<?php echo JRoute::_('index.php?option=com_rsmembership&view=mymembership&cid='.$item->id.$this->Itemid); ?>">
	<?php echo JText::_('UPGRADE to a PREMIUM Membership!'); ?></a>
    </td></tr>
    <?php endif;?>
<!-- END ADD CODE Focus Data Systems -->

<?php $k = $k == 1 ? 2 : 1; ?>
<?php $i++; ?>


<?php if ($this->params->get('show_pagination', 1) && $this->pagination->get('pages.total') > 1) { ?>
<tr>
	<td align="center" colspan="5" class="sectiontablefooter<?php echo $this->escape($this->params->get('pageclass_sfx')); ?>"><?php echo $this->pagination->getPagesLinks(); ?></td>
</tr>
<tr>
	<td colspan="5" align="right"><?php echo $this->pagination->getPagesCounter(); ?></td>
</tr>
<?php } ?>
</table>


<?php 
	//print_r($item);die;
?>

<div class="rsm_actions" style="width:100%;">
	<?php if ($item->status == 0) { ?>
	<div class="rsm_cancel" style="float:left; width:50%; text-align:left;">
		<a onclick="return confirm('<?php echo JText::_('RSM_CONFIRM_CANCEL'); ?>')" href="<?php echo JRoute::_('index.php?option=com_rsmembership&task=cancel&cid='.$item->id); ?>"><?php echo JText::_('Cancel Service'); ?></a>
	</div>
	<?php } ?>
	

	
	<div class="rsm_renew" style="float:right; width:50%; text-align:right;">
		<?php $renew_link = JRoute::_('index.php?option=com_rsmembership&task=renew&cid='.$item->id.':'.JFilterOutput::stringURLSafe($item->name)); ?>
		<?php if ($item->status == 2 || $item->status == 3) { ?>
		<a href="<?php echo $renew_link; ?>"><?php echo JText::_('Renew Service'); ?></a>
		<?php } elseif ($item->status == 0) { ?>
		<a href="<?php echo $renew_link; ?>"><?php echo JText::_('Renew Service in Advance'); ?></a>
		<?php } ?>
	</div>
</div>
	
<input type="hidden" name="limitstart" value="<?php echo $this->limitstart; ?>" />
<?php } ?>

<?php if (!empty($this->transactions)) { ?>
<p><?php echo JText::sprintf('RSM_HAVE_PENDING_TRANSACTIONS', count($this->transactions)); ?></p>
<table width="99%" class="membershiptable<?php echo $this->escape($this->params->get('pageclass_sfx')); ?>">
<?php if ($this->params->get('show_headings', 1)) { ?>
<tr>
	<th class="sectiontableheader<?php echo $this->escape($this->params->get('pageclass_sfx')); ?>" align="right" width="5%"><?php echo JText::_('#'); ?></th>
 	<th class="sectiontableheader<?php echo $this->escape($this->params->get('pageclass_sfx')); ?>"><?php echo JText::_('RSM_TRANSACTION'); ?></th>
	<th class="sectiontableheader<?php echo $this->escape($this->params->get('pageclass_sfx')); ?>"><?php echo JText::_('RSM_DATE'); ?></th>
	<th class="sectiontableheader<?php echo $this->escape($this->params->get('pageclass_sfx')); ?>"><?php echo JText::_('RSM_PRICE'); ?></th>
	<th class="sectiontableheader<?php echo $this->escape($this->params->get('pageclass_sfx')); ?>"><?php echo JText::_('RSM_GATEWAY'); ?></th>
	<th class="sectiontableheader<?php echo $this->escape($this->params->get('pageclass_sfx')); ?>"><?php echo JText::_('RSM_STATUS'); ?></th>
</tr>
<?php } ?>
<?php $k = 1; ?>
<?php $i = 0; ?>
<?php foreach ($this->transactions as $item) { ?>
<tr class="sectiontableentry<?php echo $k . $this->escape($this->params->get('pageclass_sfx')); ?>" >
	<td align="right"><?php echo $this->pagination->getRowOffset($i); ?></td>
	<td><?php echo JText::_('RSM_TRANSACTION_'.strtoupper($item->type)); ?></td>
	<td><?php echo date($this->date_format, RSMembershipHelper::getCurrentDate($item->date)); ?></td>
	<td><?php echo RSMembershipHelper::getPriceFormat($item->price); ?></td>
	<td><?php echo $item->gateway; ?></td>
	<td><?php echo JText::_('RSM_TRANSACTION_STATUS_'.strtoupper($item->status)); ?></td>
</tr>
<?php $k = $k == 1 ? 2 : 1; ?>
<?php $i++; ?>
<?php } ?>
</table>
<?php } ?>