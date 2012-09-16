<?php
/**
 * @package		Joomla.Site
 * @subpackage	mod_login
 * @copyright	Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;
JHtml::_('behavior.keepalive');
?>

<?php 
	$user = JFactory::getUser();
	$type = (!$user->get('guest')) ? 'logout' : 'login';
?>

<div class="profooterlogo">logo</div>

<?php if ($type == 'login'): ?>
<div class="prologin">
	<div class="proaccount">
		<a href="#">Create a Free Account</a>
	</div>
</div>
<?php endif; ?>

<?php if ($type == 'logout'): ?>
	<?php 
			$db = JFactory::getDBO();
			$query = 'SELECT mem.* FROM #__rsmembership_membership_users mem WHERE mem.user_id = ' . $user->id;
			$db->setQuery($query);
			
			$membership_user = $db->loadObject();
	?>
	
	<?php
			if(count($membership_user) > 0){
			
			$query = 'SELECT s.*, cat.name as category_name FROM #__rsmembership_memberships as s, #__rsmembership_categories cat WHERE s.id = ' . $membership_user->membership_id . ' AND s.category_id = cat.id';
			$db->setQuery($query);
			
			$membership = $db->loadObject();

			$query = 'SELECT mem.* FROM #__rsmembership_memberships as mem WHERE mem.category_id = ' . $membership->category_id . ' ORDER BY mem.price';
			$db->setQuery($query);
			
			$memberships = $db->loadObjectList();
			
			//print_r($memberships);die;
			
			$count = 0;
			$to_id;
			$flag = 0;
			foreach($memberships as $mem){
				if($flag == 1){
					$to_id = $mem->id;
					break;
				}
					
				$count++;
				
				if($mem->id == $membership->id)
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
		?>
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
					<a href="<?php echo JRoute::_('index.php?option=com_rsmembership&view=mymembership&cid='.$membership_user->id); ?>"><?php echo JText::_('Upgrade Account Now'); ?></a>
				</div>
			<?php endif; ?>
			
		<?php } ?>
<?php endif; ?>

<?php if ($params->get('posttext')): ?>
	<div class="posttext">
		<p><?php echo $params->get('posttext'); ?></p>
	</div>
<?php endif; ?>