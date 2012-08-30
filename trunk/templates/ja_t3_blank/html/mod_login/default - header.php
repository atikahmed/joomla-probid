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
		<a href="#">Create a Free Account</a>
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
				<input id="modlgn-remember" type="checkbox" name="remember" class="inputbox" value="yes"/>
			</p>
			<?php endif; ?>
			<input type="submit" name="Submit" class="button" value="<?php echo JText::_('JLOGIN') ?>" />
			<input type="hidden" name="option" value="com_users" />
			<input type="hidden" name="task" value="user.login" />
			<input type="hidden" name="return" value="<?php echo $return; ?>" />
			<?php echo JHtml::_('form.token'); ?>
			</fieldset>
			<ul>
				<li>
					<a href="<?php echo JRoute::_('index.php?option=com_users&view=reset'); ?>">
					<?php echo JText::_('MOD_LOGIN_FORGOT_YOUR_PASSWORD'); ?></a>
				</li>
				<li>
					<a href="<?php echo JRoute::_('index.php?option=com_users&view=remind'); ?>">
					<?php echo JText::_('MOD_LOGIN_FORGOT_YOUR_USERNAME'); ?></a>
				</li>
				<?php
				$usersConfig = JComponentHelper::getParams('com_users');
				if ($usersConfig->get('allowUserRegistration')) : ?>
				<li>
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
			<input type="submit" name="Submit" class="button" value="<?php echo JText::_('JLOGOUT'); ?>" />
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
	
	<div class="proupaccount">
		<a href="#">Upgrade Account Now</a>
	</div>
<?php endif; ?>