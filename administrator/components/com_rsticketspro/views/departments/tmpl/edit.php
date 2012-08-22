<?php
/**
* @version 2.0.0
* @package RSTickets! Pro 2.0.0
* @copyright (C) 2010 www.rsjoomla.com
* @license GPL, http://www.gnu.org/licenses/gpl-2.0.html
*/

defined('_JEXEC') or die('Restricted access');

JHTML::_('behavior.tooltip');
JHTML::_('behavior.modal');
?>

<script type="text/javascript">
function submitbutton(pressbutton)
{
	var form = document.adminForm;
	
	if (pressbutton == 'cancel')
	{
		submitform(pressbutton);
		return;
	}

	var dt = $('departments-pane').getElements('dt');
	for (var i=0; i<dt.length; i++)
	{
		if (dt[i].className == 'open')
			$('tabposition').value = i;
	}
	
	// do field validation
	if (form.name.value.length == 0)
		alert('<?php echo JText::_('RST_DEPARTMENT_NAME_ERROR', true); ?>');
	else if (form.prefix.value.length == 0)
		alert('<?php echo JText::_('RST_DEPARTMENT_PREFIX_ERROR', true); ?>');
	else
		submitform(pressbutton);
}

<?php if (RSTicketsProHelper::isJ16()) { ?>
	Joomla.submitbutton = submitbutton;
<?php } ?>

function rst_email_enable(what)
{
	if (what == 1)
	{
		$('email_address').disabled = true;
		$('email_address_fullname').disabled = true;
	}
	else
	{
		$('email_address').disabled = false;
		$('email_address_fullname').disabled = false;
	}
}
</script>

<form action="<?php echo JRoute::_('index.php?option=com_rsticketspro&controller=departments&task=edit'); ?>" method="post" name="adminForm" id="adminForm">
<?php
echo $this->tabs->startPane('departments-pane');

echo $this->tabs->startPanel(JText::_('RST_GENERAL'), 'general');
?>
<div class="col100">
	<table cellspacing="0" cellpadding="0" border="0" width="100%" class="admintable">
		<tr>
			<td width="300" style="width: 300px;" align="right" class="key"><span class="hasTip" title="<?php echo JText::_('RST_DEPARTMENT_DESC'); ?>"><label for="name"><?php echo JText::_('RST_DEPARTMENT'); ?></label></span></td>
			<td><input type="text" name="name" value="<?php echo $this->escape($this->row->name); ?>" id="name" size="120" maxlength="255" /></td>
		</tr>
		<tr>
			<td width="300" style="width: 300px;" align="right" class="key"><span class="hasTip" title="<?php echo JText::_('RST_PREFIX_DESC'); ?>"><label for="prefix"><?php echo JText::_('RST_PREFIX'); ?></label></span></td>
			<td><input type="text" name="prefix" value="<?php echo $this->escape($this->row->prefix); ?>" id="prefix" size="120" maxlength="255" /></td>
		</tr>
		<tr>
			<td width="300" style="width: 300px;" align="right" class="key"><span class="hasTip" title="<?php echo JText::_('PUBLISHED_DESC'); ?>"><label for="published"><?php echo JText::_('PUBLISHED'); ?></label></span></td>
			<td><?php echo $this->lists['published']; ?></td>
		</tr>
	</table>
</div>
<?php
echo $this->tabs->endPanel();

echo $this->tabs->startPanel(JText::_('RST_DEPARTMENT_TICKETS'), 'tickets');
?>
<div class="col100">
	<table cellspacing="0" cellpadding="0" border="0" width="100%" class="admintable">
		<tr>
			<td width="300" style="width: 300px;" align="right" class="key"><span class="hasTip" title="<?php echo JText::_('RST_ASSIGNMENT_TYPE_DESC'); ?>"><label for="assignment_type"><?php echo JText::_('RST_ASSIGNMENT_TYPE'); ?></label></span></td>
			<td><?php echo $this->lists['assignment_type']; ?></td>
		</tr>
		<tr>
			<td width="300" style="width: 300px;" align="right" class="key"><span class="hasTip" title="<?php echo JText::_('RST_GENERATION_RULE_DESC'); ?>"><label for="generation_rule"><?php echo JText::_('RST_GENERATION_RULE'); ?></label></span></td>
			<td><?php echo $this->lists['generation_rule']; ?></td>
		</tr>
		<tr>
			<td width="300" style="width: 300px;" align="right" class="key"><span class="hasTip" title="<?php echo JText::_('RST_PRIORITY_DESC'); ?>"><label for="priority"><?php echo JText::_('RST_PRIORITY'); ?></label></span></td>
			<td><?php echo $this->lists['priority']; ?></td>
		</tr>
		<tr>
			<td width="300" style="width: 300px;" align="right" class="key">
				<span class="hasTip" title="<?php echo JText::_('RST_PREDEFINED_SUBJECTS_ADD_DESC'); ?>">
				<?php echo JText::_('RST_PREDEFINED_SUBJECTS_ADD'); ?>
				</span>
			</td>
			<td>
				<?php if (!RSTicketsProHelper::getConfig('allow_predefined_subjects')) { ?>
				<p><?php echo JText::_('RST_PREDEFINED_SUBJECTS_ARE_DISABLED'); ?></p>
				<?php } ?>
				<textarea name="predefined_subjects" id="predefined_subjects" cols="90" rows="10"><?php echo $this->escape($this->row->predefined_subjects); ?></textarea>
			</td>
		</tr>
	</table>
</div>
<?php
echo $this->tabs->endPanel();

echo $this->tabs->startPanel(JText::_('RST_DEPARTMENT_EMAILS'), 'emails');
?>
<div class="col100">
	<table cellspacing="0" cellpadding="0" border="0" width="100%" class="admintable">
		<tr>
			<td width="300" style="width: 300px;" align="right" class="key">
				<span class="hasTip" title="<?php echo JText::_('RST_EMAIL_USE_RSTICKETS_GLOBAL_DESC'); ?>"><label for="email_use_global"><?php echo JText::_('RST_EMAIL_USE_RSTICKETS_GLOBAL'); ?></label></span>
			</td>
			<td>
				<?php echo $this->lists['email_use_global']; ?>
			</td>
		</tr>
		<tr>
			<td width="300" style="width: 300px;" align="right" class="key">
				<span class="hasTip" title="<?php echo JText::_('RST_EMAIL_FROM_EMAIL_DESC'); ?>"><label for="email_address"><?php echo JText::_('RST_EMAIL_FROM_EMAIL'); ?></label></span>
			</td>
			<td>
				<input class="text_area" type="text" name="email_address" id="email_address" <?php echo $this->row->email_use_global ? ' disabled="disabled"' : ''; ?> size="35" value="<?php echo $this->escape($this->row->email_address); ?>" />
			</td>
		</tr>
		<tr>
			<td width="300" style="width: 300px;" align="right" class="key">
				<span class="hasTip" title="<?php echo JText::_('RST_EMAIL_FROM_FULLNAME_DESC'); ?>"><label for="email_address_fullname"><?php echo JText::_('RST_EMAIL_FROM_FULLNAME'); ?></label></span>
			</td>
			<td>
				<input class="text_area" type="text" name="email_address_fullname" id="email_address_fullname" <?php echo $this->row->email_use_global ? ' disabled="disabled"' : ''; ?> size="35" value="<?php echo $this->escape($this->row->email_address_fullname); ?>" />
			</td>
		</tr>
		<tr>
			<td width="300" style="width: 300px;" align="right" class="key"><span class="hasTip" title="<?php echo JText::_('RST_CUSTOMER_SEND_EMAIL_DESC'); ?>"><label for="customer_send_email"><?php echo JText::_('RST_CUSTOMER_SEND_EMAIL'); ?></label></span></td>
			<td><?php echo $this->lists['customer_send_email']; ?></td>
		</tr>
		<tr>
			<td width="300" style="width: 300px;" align="right" class="key"><span class="hasTip" title="<?php echo JText::_('RST_CUSTOMER_SEND_COPY_EMAIL_DESC'); ?>"><label for="customer_send_copy_email"><?php echo JText::_('RST_CUSTOMER_SEND_COPY_EMAIL'); ?></label></span></td>
			<td><?php echo $this->lists['customer_send_copy_email']; ?></td>
		</tr>
		<tr>
			<td width="300" style="width: 300px;" align="right" class="key"><span class="hasTip" title="<?php echo JText::_('RST_CUSTOMER_ATTACH_EMAIL_DESC'); ?>"><label for="customer_attach_email"><?php echo JText::_('RST_CUSTOMER_ATTACH_EMAIL'); ?></label></span></td>
			<td><?php echo $this->lists['customer_attach_email']; ?></td>
		</tr>
		<tr>
			<td width="300" style="width: 300px;" align="right" class="key"><span class="hasTip" title="<?php echo JText::_('RST_STAFF_SEND_EMAIL_DESC'); ?>"><label for="staff_send_email"><?php echo JText::_('RST_STAFF_SEND_EMAIL'); ?></label></span></td>
			<td><?php echo $this->lists['staff_send_email']; ?></td>
		</tr>
		<tr>
			<td width="300" style="width: 300px;" align="right" class="key"><span class="hasTip" title="<?php echo JText::_('RST_STAFF_ATTACH_EMAIL_DESC'); ?>"><label for="staff_attach_email"><?php echo JText::_('RST_STAFF_ATTACH_EMAIL'); ?></label></span></td>
			<td><?php echo $this->lists['staff_attach_email']; ?></td>
		</tr>
		<tr>
			<td width="300" style="width: 300px;" align="right" class="key"><span class="hasTip" title="<?php echo JText::_('RST_NOTIFY_NEW_TICKETS_TO_DESC'); ?>"><label for="notify_new_tickets_to"><?php echo JText::_('RST_NOTIFY_NEW_TICKETS_TO'); ?></label></span></td>
			<td><textarea cols="80" rows="10" class="text_area" type="text" name="notify_new_tickets_to" id="notify_new_tickets_to"><?php echo $this->escape($this->row->notify_new_tickets_to); ?></textarea></td>
		</tr>
		<tr>
			<td width="300" style="width: 300px;" align="right" class="key"><span class="hasTip" title="<?php echo JText::_('RST_DEPARTMENT_CC_DESC'); ?>"><label for="cc"><?php echo JText::_('RST_DEPARTMENT_CC'); ?></label></span></td>
			<td><textarea cols="80" rows="10" class="text_area" type="text" name="cc" id="cc"><?php echo $this->escape($this->row->cc); ?></textarea></td>
		</tr>
		<tr>
			<td width="300" style="width: 300px;" align="right" class="key"><span class="hasTip" title="<?php echo JText::_('RST_DEPARTMENT_BCC_DESC'); ?>"><label for="bcc"><?php echo JText::_('RST_DEPARTMENT_BCC'); ?></label></span></td>
			<td><textarea cols="80" rows="10" class="text_area" type="text" name="bcc" id="bcc"><?php echo $this->escape($this->row->bcc); ?></textarea></td>
		</tr>
		<tr>
			<td width="300" style="width: 300px;" align="right" class="key"><span class="hasTip" title="<?php echo JText::_('RST_NOTIFY_ASSIGN_DESC'); ?>"><label for="notify_assign"><?php echo JText::_('RST_NOTIFY_ASSIGN'); ?></label></span></td>
			<td><?php echo $this->lists['notify_assign']; ?></td>
		</tr>
	</table>
</div>
<?php
echo $this->tabs->endPanel();

echo $this->tabs->startPanel(JText::_('RST_DEPARTMENT_UPLOADS'), 'uploads');
?>
<div class="col100">
	<table cellspacing="0" cellpadding="0" border="0" width="100%" class="admintable">
		<?php if ($this->uploads_disabled) { ?>
		<tr>
			<td colspan="2"><b style="color: red"><?php echo JText::_('RST_UPLOADS_ARE_DISABLED'); ?></b></td>
		</tr>
		<?php } ?>
		<tr>
			<td width="300" style="width: 300px;" align="right" class="key"><span class="hasTip" title="<?php echo JText::_('RST_UPLOAD_FOR_DESC'); ?>"><label for="upload"><?php echo JText::_('RST_UPLOAD_FOR'); ?></label></span></td>
			<td><?php echo $this->lists['upload']; ?></td>
		</tr>
		<tr>
			<td width="300" style="width: 300px;" align="right" class="key"><span class="hasTip" title="<?php echo JText::_('RST_UPLOAD_EXTENSIONS_DESC'); ?>"><label for="upload_extensions"><?php echo JText::_('RST_UPLOAD_EXTENSIONS'); ?></label></span></td>
			<td><textarea cols="80" rows="10" class="text_area" type="text" name="upload_extensions" id="upload_extensions"><?php echo $this->escape($this->row->upload_extensions); ?></textarea></td>
		</tr>
		<tr>
			<td width="300" style="width: 300px;" align="right" class="key"><span class="hasTip" title="<?php echo JText::_('RST_UPLOAD_SIZE_DESC'); ?>"><label for="upload_size"><?php echo JText::_('RST_UPLOAD_SIZE'); ?></label></span></td>
			<td><input class="text_area" type="text" name="upload_size" id="upload_size" size="15" value="<?php echo $this->escape($this->row->upload_size); ?>" /><?php if ($this->upload_max_filesize !== false) { ?> <?php echo JText::sprintf('RST_UPLOADS_MAX_FILESIZE', $this->upload_max_filesize); ?><?php } ?></td>
		</tr>
		<tr>
			<td width="300" style="width: 300px;" align="right" class="key"><span class="hasTip" title="<?php echo JText::_('RST_UPLOAD_FILES_DESC'); ?>"><label for="upload_files"><?php echo JText::_('RST_UPLOAD_FILES'); ?></label></span></td>
			<td><input class="text_area" type="text" name="upload_files" id="upload_files" size="15" value="<?php echo $this->escape($this->row->upload_files); ?>" /><?php if ($this->max_file_uploads !== false) { ?> <?php echo JText::sprintf('RST_UPLOADS_MAX_FILES', $this->max_file_uploads); ?><?php } ?></td>
		</tr>
	</table>
</div>
<?php
echo $this->tabs->endPanel();

echo $this->tabs->endPane();
?>

<?php echo JHTML::_('form.token'); ?>
<input type="hidden" name="option" value="com_rsticketspro" />
<input type="hidden" name="controller" value="departments" />
<input type="hidden" name="task" value="edit" />
<input type="hidden" name="view" value="departments" />

<input type="hidden" name="id" value="<?php echo $this->row->id; ?>" />
<input type="hidden" name="tabposition" id="tabposition" value="0" />
</form>

<?php
//keep session alive while editing
JHTML::_('behavior.keepalive');
?>