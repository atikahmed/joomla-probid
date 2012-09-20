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

<?php if ($type == 'login'): ?>
<script type="text/javascript">
	jQuery(document).ready(	function($) {
		$("#fancy<?php echo $module->id; ?>").fancybox({
			'titlePosition'		: 'inside',
			'transitionIn'		: 'elastic',
			'transitionOut'		: 'none',
			'Width'		: 700,
			'onComplete'		: function(){
				$.fancybox.resize();
			}
		});
		$('#facy_outer').css({'width':500});
	});
</script>

<div class="prologin">
	<div class="proaccount">
		<a href="index.php/memberships-sp">Create a Free Account</a>
	</div>
	<span> or </span>
	<div class="prologinout">
		<a id="fancy<?php echo $module->id; ?>" href="#fancybox<?php echo $module->id; ?>">Login</a>
	</div>
</div>

<?php endif; ?>

<div style="display:none">
	<div id="fancybox<?php echo $module->id; ?>" class="proformlogin">
		<form action="<?php echo JRoute::_('index.php', true, $params->get('usesecure')); ?>" method="post" id="login-form" >
			<?php if ($params->get('pretext')): ?>
				<div class="pretext">
				<p><?php echo $params->get('pretext'); ?></p>
				</div>
			<?php endif; ?>
			<h3>Member Login</h3>
			<fieldset class="userdata">
			<p id="form-login-username">
				<label for="modlgn-username"><?php echo JText::_('MOD_LOGIN_VALUE_USERNAME') ?></label>
				<input id="modlgn-username" type="text" name="username" class="inputbox"  size="18" />
			</p>
			<p id="form-login-password">
				<label for="modlgn-passwd"><?php echo JText::_('JGLOBAL_PASSWORD') ?></label>
				<input id="modlgn-passwd" type="password" name="password" class="inputbox" size="18"  />
			</p>
			<?php if (JPluginHelper::isEnabled('system', 'remember')) : ?>
			<p id="form-login-remember">
				<label for="modlgn-remember"><?php echo JText::_('MOD_LOGIN_REMEMBER_ME') ?></label>
				<input id="modlgn-remember" checked="checked" type="checkbox" name="remember" class="inputbox" value="yes"/>
			</p>
			<?php endif; ?>
			<input type="submit" name="Submit" class="button" value="<?php echo JText::_('JSIGNIN') ?>" />
			<input type="hidden" name="option" value="com_users" />
			<input type="hidden" name="task" value="user.login" />
			<input type="hidden" name="return" value="<?php echo $return; ?>" />
			<?php echo JHtml::_('form.token'); ?>
			</fieldset>
			<ul>
				<li>
					<a href="<?php echo JRoute::_('index.php?option=com_users&view=remind'); ?>">
					<?php echo JText::_('MOD_LOGIN_FORGOT_YOUR_USERNAME'); ?></a>
				</li>
				<li>
					or
				</li>
				<li>
					<a href="<?php echo JRoute::_('index.php?option=com_users&view=reset'); ?>">
					<?php echo JText::_('MOD_LOGIN_FORGOT_YOUR_PASSWORD'); ?></a>
				</li>
			</ul>
				
			<ul>
				<li>
					<?php echo JText::_('NOT_REGISTERED'); ?>
				</li>
				<?php
				$usersConfig = JComponentHelper::getParams('com_users');
				if ($usersConfig->get('allowUserRegistration')) : ?>
				<li class="pt-last">
					<a href="<?php echo JRoute::_('index.php?option=com_users&view=registration'); ?>">
						<?php echo JText::_('MOD_LOGIN_REGISTER'); ?></a>
				</li>
				<?php endif; ?>
			</ul>
			<?php if ($params->get('posttext')): ?>
				<div class="posttext">
				<p><?php echo $params->get('posttext'); ?></p>
				</div>
			<?php endif; ?>
		</form>
	</div>
</div>

<?php if ($type == 'logout'): ?>
	<form action="<?php echo JRoute::_('index.php', true, $params->get('usesecure')); ?>" method="post" id="login-form">	
		<div class="logout-button">
			<input type="submit" name="Submit" style="cursor:pointer;" class="button_logout" value="<?php echo JText::_('JLOGOUT'); ?>" />
			<input type="hidden" name="option" value="com_users" />
			<input type="hidden" name="task" value="user.logout" />
			<input type="hidden" name="return" value="<?php echo $return; ?>" />
			<?php echo JHtml::_('form.token'); ?>
		</div>
		<?php if ($params->get('greeting')) : ?>
		<div class="login-greeting">
		<?php if($params->get('name') == 0) : { ?>
			<?php echo JText::_('Welcome'); ?> <strong> <?php echo htmlspecialchars($user->get('name')); ?> </strong>
		<?php } else : { ?>
			<?php echo JText::_('Welcome'); ?> <strong> <?php echo htmlspecialchars($user->get('username')); ?> </strong>
		<?php } endif; ?>
		</div>
	<?php endif; ?>
	</form>
	
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