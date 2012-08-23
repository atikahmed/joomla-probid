<?php
/**
 * @package   Template Overrides - RocketTheme
 * @version   1.1 November 11, 2011
 * @author    RocketTheme http://www.rockettheme.com
 * @copyright Copyright (C) 2007 - 2011 RocketTheme, LLC
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 *
 * Rockettheme Gantry Template uses the Joomla Framework (http://www.joomla.org), a GNU/GPLv2 content management system
 *
 */

// no direct access
defined('_JEXEC') or die;
JHtml::_('behavior.keepalive');
?>
<?php if ($type == 'logout') : ?>
<form action="<?php echo JRoute::_('index.php', true, $params->get('usesecure')); ?>" method="post" name="form-login" id="form-login">
<?php if ($params->get('greeting')) : ?>
	<div class="login-greeting">
	<?php if($params->get('name') == 0) : {
		echo JText::sprintf('MOD_LOGIN_HINAME', $user->get('name'));
	} else : {
		echo JText::sprintf('MOD_LOGIN_HINAME', $user->get('username'));
	} endif; ?>
	</div>
<?php endif; ?>
	<div class="readon">
		<input type="submit" name="Submit" class="button" value="<?php echo JText::_('JLOGOUT'); ?>" />
	</div>

	<input type="hidden" name="option" value="com_users" />
	<input type="hidden" name="task" value="user.logout" />
	<input type="hidden" name="return" value="<?php echo $return; ?>" />
	<?php echo JHtml::_('form.token'); ?>
</form>
<?php else : ?>
<form action="<?php echo JRoute::_('index.php', true, $params->get('usesecure')); ?>" method="post" name="form-login" id="form-login" >
	<?php if ($params->get('pretext')): ?>
	<div class="pretext">
	<p><?php echo $params->get('pretext'); ?></p>
	</div>
	<?php endif; ?>
	<fieldset class="userdata">
	<p id="form-login-username">
	<input id="modlgn_username" type="text" name="username" class="inputbox" alt="username" size="18" value="<?php echo JText::_('MOD_LOGIN_VALUE_USERNAME') ?>" onfocus="if (this.value=='<?php echo JText::_('MOD_LOGIN_VALUE_USERNAME') ?>') this.value=''" onblur="if(this.value=='') { this.value='<?php echo JText::_('Username'); ?>'; return false; }" />
	</p>
	<p id="form-login-password">
	<input id="modlgn_passwd" type="password" name="password" class="inputbox" size="18" alt="password" value="<?php echo JText::_('JGLOBAL_PASSWORD') ?>" onfocus="if (this.value=='<?php echo JText::_('JGLOBAL_PASSWORD') ?>') this.value=''" onblur="if(this.value=='') { this.value='<?php echo JText::_('Password'); ?>'; return false; }" />
	</p>
	<?php if (JPluginHelper::isEnabled('system', 'remember')) : ?>
	<p id="form-login-remember">
		<input id="modlgn-remember" type="checkbox" name="remember" class="checkbox" value="yes"/>
		<label for="modlgn-remember"><?php echo JText::_('MOD_LOGIN_REMEMBER_ME') ?></label>
	</p>
	<?php endif; ?>
	<div class="readon"><input type="submit" name="Submit" class="button" value="<?php echo JText::_('JLOGIN') ?>" /></div>
	<input type="hidden" name="option" value="com_users" />
	<input type="hidden" name="task" value="user.login" />
	<!-- JOEYG ADDING LOGIC TO REDIRECT IF SENT FROM EMAIL NOTIFICATION SYSTEM -->
	<?php
	if(isset($_GET['red'])) {
		if($_GET['red'] == 0) {
			//red=0 means redirect to a project job-card, project id should also be present
			$project_id = $_GET['lid'];
			$db = JFactory::getDbo();
			$query = "SELECT jos_categories.id AS cat_id, jos_categories.alias AS cat_alias, jos_content.id AS content_id, ";
			$query .= "jos_content.alias AS content_alias, jos_content.title FROM jos_content INNER JOIN jos_categories ON ";
			$query .= "jos_content.catid = jos_categories.id WHERE jos_content.ID = ". $project_id;
			$db->setQuery($query);
			$rows = $db->loadAssocList();
			$return_url = "index.php?option=com_content&view=article&catid=";
			foreach ($rows as $row) {
				$return_url .= $row['cat_id'] . ":" . $row['cat_alias'];
				$return_url .= "&id=" . $row['content_id'] . ":" . $row['content_alias'];
				$return_url .= "&Itemid=554#job-card";
			}
			//$return_url = "index.php?option=com_content&view=article&catid=129:remodel-construction&id=113:hvac-replacement&Itemid=554#job-card";
			$return_url = base64_encode($return_url);
			$return = $return_url;
		}
		else {
			//red=1 is for projects notification email
			$ids = $_GET['ids']; //grab list of matching project ids (listings)
			$return_url = "/index.php?option=com_content&view=article&id=169&Itemid=679&ids=" . $ids;
			$return_url = base64_encode($return_url);
			$return = $return_url;
		}//ends if/else
	}//ends if $_GET['red'] isset
	?>
	<!--ENDS JOEYG ADDING LOGIC -->
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
        
        <!-- BEGIN This link was modified FDS -->
		<li>
			<a href="<?php echo JRoute::_('/free-memberships'); ?>">
				<?php echo JText::_('MOD_LOGIN_REGISTER'); ?></a>
		</li>
        <!-- END This link was modified FDS -->
        
		<?php endif; ?>
	</ul>
	<?php if ($params->get('posttext')): ?>
	<div class="posttext">
		<p><?php echo $params->get('posttext'); ?></p>
	</div>
	<?php endif; ?>
</form>
<?php endif; ?>
