<?php
/**
* @version 1.0.0
* @package RSMembership! 1.0.0
* @copyright (C) 2009-2010 www.rsjoomla.com
* @license GPL, http://www.gnu.org/licenses/gpl-2.0.html
*/

defined('_JEXEC') or die('Restricted access');
?>

<h1><?php echo JText::_('RSM_RETURNING_CUSTOMER'); ?></h1>
<p><?php echo JText::_('RSM_SUBSCRIBE_PLEASE_LOGIN'); ?></p>
<form class="rsmembership_form" method="post" action="<?php echo JRoute::_('index.php'); ?>">
<fieldset>
	<legend><?php echo JText::_('RSM_LOGIN_INFORMATION'); ?></legend>
	
	<table cellpadding="0" cellspacing="0" border="0" width="100%" class="rsmembership_form_table">
	<tr>
		<td width="30%" height="40"><label for="username"><?php echo JText::_('RSM_USERNAME') ?></label></td>
		<td><input name="username" id="username" type="text" class="rsm_textbox" alt="username" size="18" /></td>
	</tr>
	<tr>
		<td width="30%" height="40"><label for="passwd"><?php echo JText::_('RSM_PASSWORD') ?></label></td>
		<td><input type="password" id="passwd" name="<?php echo RSMembershipHelper::isJ16() ? 'password' : 'passwd'; ?>" class="rsm_textbox" size="18" alt="password" /></td>
	</tr>
	<?php if(JPluginHelper::isEnabled('system', 'remember')) { ?>
	<tr>
		<td width="30%" height="40"><label for="remember"><?php echo JText::_('RSM_REMEMBER_ME') ?> Yes Please.</label></td>
		<td><input type="checkbox" id="remember" name="remember" class="inputbox" value="yes" alt="Remember Me" /></td>
	</tr>
	<?php } ?>
	</table>
	<input type="submit" name="Submit" class="button" value="<?php echo JText::_('RSM_LOGIN') ?>" />
	<?php echo $this->token; ?>
	<input type="hidden" name="option" value="<?php echo RSMembershipHelper::isJ16() ? 'com_users' : 'com_user'; ?>" />
	<input type="hidden" name="task" value="<?php echo RSMembershipHelper::isJ16() ? 'user.login' : 'login'; ?>" />
	<input type="hidden" name="return" value="<?php echo $this->return; ?>" />
</fieldset>
</form>