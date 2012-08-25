<?php
/**
 * @version		$Id: profile.php 1492 2012-02-22 17:40:09Z joomlaworks@gmail.com $
 * @package		K2
 * @author		JoomlaWorks http://www.joomlaworks.net
 * @copyright	Copyright (c) 2006 - 2012 JoomlaWorks Ltd. All rights reserved.
 * @license		GNU/GPL license: http://www.gnu.org/copyleft/gpl.html
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

?>

<!-- K2 user profile form -->
<form action="<?php echo JRoute::_('index.php'); ?>" enctype="multipart/form-data" method="post" name="userform" autocomplete="off" class="form-validate">
	<?php if($this->params->def('show_page_title',1)): ?>
	<div class="componentheading<?php echo $this->escape($this->params->get('pageclass_sfx')); ?>">
		<?php echo $this->escape($this->params->get('page_title')); ?>
	</div>
	<?php endif; ?>
	<div id="k2Container" class="k2AccountPage">
		<table class="admintable" cellpadding="0" cellspacing="0">
			<tr>
				<th colspan="2" class="k2ProfileHeading">
					<?php echo JText::_('COM_USERS_PERSONAL_DETAILS'); ?>
				</th>
			</tr>
			<tr>
				<td class="key">
					<label for="username"><?php echo JText::_('K2_USER_NAME'); ?></label>
				</td>
				<td>
					<span><b><?php echo $this->user->get('username'); ?></b></span>
				</td>
			</tr>
			<tr>
				<td class="key">
					<label id="namemsg" for="name"><?php echo JText::_('K2_NAME'); ?></label>
				</td>
				<td>
					<input type="text" name="<?php echo (K2_JVERSION=='16')?'jform[name]':'name'?>" id="name" size="40" value="<?php echo $this->escape($this->user->get( 'name' )); ?>" class="inputbox required" maxlength="50" />
				</td>
			</tr>
			<tr>
				<td class="key">
					<label id="emailmsg" for="email"><?php echo JText::_('K2_EMAIL'); ?></label>
				</td>
				<td>
					<input type="text" id="email" name="<?php echo (K2_JVERSION=='16')?'jform[email1]':'email'?>" size="40" value="<?php echo $this->escape($this->user->get( 'email' )); ?>" class="inputbox required validate-email" maxlength="100" />
				</td>
			</tr>
			<?php if(K2_JVERSION == '16'): ?>
			<tr>
				<td class="key">
					<label id="email2msg" for="email2"><?php echo JText::_('K2_CONFIRM_EMAIL'); ?></label>
				</td>
				<td>
					<input type="text" id="email2" name="jform[email2]" size="40" value="<?php echo $this->escape($this->user->get( 'email' )); ?>" class="inputbox required validate-email" maxlength="100" />
					*
				</td>
			</tr>
			<?php endif; ?>
			<tr>
				<td class="key">
					<label id="pwmsg" for="password"><?php echo JText::_('K2_PASSWORD'); ?></label>
				</td>
				<td>
					<input class="inputbox validate-password" type="password" id="password" name="<?php echo (K2_JVERSION=='16')?'jform[password1]':'password'?>" size="40" value="" />
				</td>
			</tr>
			<tr>
				<td class="key">
					<label id="pw2msg" for="password2"><?php echo JText::_('K2_VERIFY_PASSWORD'); ?></label>
				</td>
				<td>
					<input class="inputbox validate-passverify" type="password" id="password2" name="<?php echo (K2_JVERSION=='16')?'jform[password2]':'password2'?>" size="40" value="" />
				</td>
			</tr>
			
			<?php if(count(array_filter($this->K2Plugins))): ?>
			<!-- K2 Plugin attached fields -->
			<tr>
				<th colspan="2" class="k2ProfileHeading">
					<?php echo JText::_('K2_ADDITIONAL_DETAILS'); ?>
				</th>
			</tr>
			<?php foreach($this->K2Plugins as $K2Plugin): ?>
			<?php if(!is_null($K2Plugin)): ?>
			<tr>
				<td colspan="2">
					<?php echo $K2Plugin->fields; ?>
				</td>
			</tr>
			<?php endif; ?>
			<?php endforeach; ?>
			<?php endif; ?>
			<?php if(isset($this->params) && K2_JVERSION=='15'): ?>
			<tr>
				<th colspan="2" class="k2ProfileHeading">
					<?php echo JText::_('K2_ADMINISTRATIVE_DETAILS'); ?>
				</th>
			</tr>
			<tr>
				<td colspan="2" id="userAdminParams">
					<?php echo $this->params->render('params'); ?>
				</td>
			</tr>
			<?php endif; ?>
			<!-- Joomla! 1.6+ JForm implementation -->
			<?php if(K2_JVERSION == '16'): ?>
			<?php foreach ($this->form->getFieldsets() as $fieldset): // Iterate through the form fieldsets and display each one.?>
				<?php if($fieldset->name != 'core'): ?>
				<?php $fields = $this->form->getFieldset($fieldset->name);?>
				<?php if (count($fields)):?>
					<?php if (isset($fieldset->label)):// If the fieldset has a label set, display it as the legend.?>
					<tr>
						<th colspan="2" class="k2ProfileHeading">
							<?php echo JText::_('COM_USERS_CONTACT_DETAILS'); ?>
						</th>
					</tr>
					<?php endif;?>
					<?php foreach($fields as $field):// Iterate through the fields in the set and display them.?>
						<?php if ($field->hidden):// If the field is hidden, just display the input.?>
							<tr><td colspan="2"><?php echo $field->input;?></td></tr>
						<?php else:?>
							<tr>
								<td class="key">
									<?php echo $field->label; ?>
									<?php if (!$field->required && $field->type != 'Spacer'): ?>
										<span class="optional"><?php echo JText::_('COM_USERS_OPTIONAL');?></span>
									<?php endif; ?>
								</td>
								<td><?php echo $field->input;?></td>
							</tr>
						<?php endif;?>
					<?php endforeach;?>
				<?php endif;?>
				<?php endif; ?>
			<?php endforeach;?>
			<?php endif; ?>
		</table>
		<div class="k2AccountPageUpdate">
			<button class="button validate" type="submit" onclick="submitbutton( this.form );return false;">
				<?php echo JText::_('K2_SUBMIT'); ?>
			</button>
			<script type="text/javascript">
				function hrefCancel()
				{
					window.location = window.location.href.split('?')[0];
				}
			</script>
			<a href="javascript:void(0);" onclick="hrefCancel();"><?php echo JText::_('K2_CANCEL'); ?></a>
		</div>
	</div>
	<input type="hidden" name="<?php echo (K2_JVERSION=='16')?'jform[username]':'username'?>" value="<?php echo $this->user->get('username'); ?>" />
	<input type="hidden" name="<?php echo (K2_JVERSION=='16')?'jform[id]':'id'?>" value="<?php echo $this->user->get('id'); ?>" />
	<input type="hidden" name="gid" value="<?php echo $this->user->get('gid'); ?>" />
	<input type="hidden" name="option" value="<?php echo (K2_JVERSION=='16')?'com_users':'com_user'?>" />
	<input type="hidden" name="task" value="<?php echo (K2_JVERSION=='16')?'profile.save':'save'?>" />
	<input type="hidden" name="K2UserForm" value="1" />
	<?php echo JHTML::_( 'form.token' ); ?>
</form>
