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

<?php echo RSTicketsProHelper::getConfig('global_message'); ?>
<?php echo RSTicketsProHelper::getConfig('submit_message'); ?>

<form action="<?php echo JRoute::_('index.php?option=com_rsticketspro&view=submit'); ?>" method="post" name="adminForm" id="adminForm" enctype="multipart/form-data">

<table class="admintable">
	<?php if ($this->is_staff && ($this->permissions->add_ticket_customers || $this->permissions->add_ticket_staff)) { ?>
	<tr>
		<td width="150" style="width: 150px;" align="right" class="key">&nbsp;</td>
		<td><input type="radio" name="submit_type" id="submit_type0" onclick="rst_change_submit_type();" value="1" <?php echo $this->checked_create_new_user ? 'checked="checked"' : ''; ?> /> <label for="submit_type0"><?php echo JText::_('RST_CREATE_NEW_USER'); ?></label> <input type="radio" name="submit_type" id="submit_type1" onclick="rst_change_submit_type();" value="2" <?php echo $this->checked_existing_user ? 'checked="checked"' : ''; ?> /> <label for="submit_type1"><?php echo JText::_('RST_SELECT_EXISTING_USER'); ?></label></td>
	</tr>
	<tr id="new_user0">
		<td width="150" style="width: 150px;" align="right" class="key"><label for="submit_email"><span class="hasTip" title="<?php echo JText::_('RST_YOUR_EMAIL_DESC'); ?>"><?php echo JText::_('RST_YOUR_EMAIL'); ?></span></label></td>
		<td><input type="text" name="email" id="submit_email" size="40" value="<?php echo $this->escape(@$this->data['email']); ?>" class="inputbox" /></td>
	</tr>
	<tr id="new_user1">
		<td width="150" style="width: 150px;" align="right" class="key"><label for="submit_name"><span class="hasTip" title="<?php echo JText::_('RST_YOUR_NAME_DESC'); ?>"><?php echo JText::_('RST_YOUR_NAME'); ?></span></label></td>
		<td><input type="text" name="name" id="submit_name" size="40" value="<?php echo $this->escape(@$this->data['name']); ?>" class="inputbox" /></td>
	</tr>
	<tr id="existing_user0">
		<td width="150" style="width: 150px;" align="right" class="key"><label for="submit_email"><span class="hasTip" title="<?php echo JText::_('RST_CUSTOMER_EMAIL_DESC'); ?>"><?php echo JText::_('RST_CUSTOMER_EMAIL'); ?></span></label></td>
		<td>
		<a class="modal" href="<?php echo JRoute::_('index.php?option=com_rsticketspro&view=users&tmpl=component'); ?>" rel="{handler: 'iframe', size: {x: 600, y: 475}}"><?php echo JText::_('RST_SELECT_USERNAME'); ?></a>
		<div id="submit_email_text"><?php echo $this->escape(@$this->data['email']); ?></div>
		<input type="hidden" name="customer_id" id="customer_id" value="<?php echo $this->escape(@$this->data['customer_id']); ?>" />
		</td>
	</tr>
	<tr id="existing_user1">
		<td width="150" style="width: 150px;" align="right" class="key"><label for="submit_name"><span class="hasTip" title="<?php echo JText::_('RST_CUSTOMER_NAME_DESC'); ?>"><?php echo JText::_('RST_CUSTOMER_NAME'); ?></span></label></td>
		<td><div id="submit_name_text"><?php echo $this->escape(@$this->data['name']); ?></div></td>
	</tr>
	<?php } else { ?>
	<tr>
		<td><label for="submit_email"><span class="hasTip" title="<?php echo JText::_('RST_YOUR_EMAIL_DESC'); ?>"><?php echo JText::_('RST_YOUR_EMAIL'); ?></span></label></td>
		<td><?php echo $this->escape($this->user->get('email')); ?></td>
	</tr>
	<tr>
		<td><label for="submit_name"><span class="hasTip" title="<?php echo JText::_('RST_YOUR_NAME_DESC'); ?>"><?php echo JText::_('RST_YOUR_NAME'); ?></span></label></td>
		<td><?php echo $this->escape($this->user->get('name')); ?></td>
	</tr>
	<?php } ?>
	<tr>
		<td width="150" style="width: 150px;" align="right" class="key"><label for="submit_department"><span class="hasTip" title="<?php echo JText::_('RST_TICKET_DEPARTMENT_DESC'); ?>"><?php echo JText::_('RST_TICKET_DEPARTMENT'); ?></span></label></td>
		<td><?php echo $this->lists['departments']; ?></td>
	</tr>
	<?php foreach ($this->custom_fields as $department_id => $fields) { ?>
		<?php foreach ($fields as $i => $field) { ?>
		<tr id="custom-<?php echo $department_id; ?>-<?php echo $i; ?>" <?php if (@$this->data['department_id'] != $department_id) { ?>style="display: none;"<?php } ?>>
			<td width="150" style="width: 150px;" align="right" class="key"><?php echo $field[0]; ?></td>
			<td><?php echo $field[1]; ?></td>
		</tr>
		<?php } ?>
	<?php } ?>
	<tr>
		<td width="150" style="width: 150px;" align="right" class="key"><label for="submit_subject"><span class="hasTip" title="<?php echo JText::_('RST_TICKET_SUBJECT_DESC'); ?>"><?php echo JText::_('RST_TICKET_SUBJECT'); ?></span></label></td>
		<?php if ($this->use_predefined_subjects) { ?>
		<td><?php echo $this->lists['subject']; ?></td>
		<?php } else { ?>
		<td><input type="text" name="subject" id="submit_subject" size="40" value="<?php echo $this->escape(@$this->data['subject']); ?>" class="inputbox" /></td>
		<?php } ?>
	</tr>
	<tr>
		<td width="150" style="width: 150px;" align="right" class="key"><label for="submit_message"><span class="hasTip" title="<?php echo JText::_('RST_TICKET_MESSAGE_DESC'); ?>"><?php echo JText::_('RST_TICKET_MESSAGE'); ?></span></label></td>
		<td>
		<?php if ($this->use_editor) { ?>
			<?php echo $this->editor->display('message', @$this->data['message'],500,250,70,10); ?>
		<?php } else { ?>
			<textarea cols="80" rows="10" class="text_area" type="text" name="message" id="message"><?php echo $this->escape(@$this->data['message']); ?></textarea>
		<?php } ?>
		</td>
	</tr>
	<tr>
		<td width="150" style="width: 150px;" align="right" class="key"><label for="submit_priority"><span class="hasTip" title="<?php echo JText::_('RST_TICKET_PRIORITY_DESC'); ?>"><?php echo JText::_('RST_TICKET_PRIORITY'); ?></span></label></td>
		<td><?php echo $this->lists['priorities']; ?></td>
	</tr>
	<tr id="submit_file" <?php echo ($this->can_upload && !empty($this->data['department_id'])) ? '' : 'style="display: none"'; ?>>
		<td width="150" style="width: 150px;" align="right" class="key"><label for="submit_files"><span class="hasTip" title="<?php echo JText::_('RST_TICKET_ATTACHMENTS_DESC'); ?>"><?php echo JText::_('RST_TICKET_ATTACHMENTS'); ?></span></label></td>
		<td>
			<div id="submit_file_message"></div>
			<input type="file" name="rst_files[]" value="" />
			<input type="button" class="button" value="<?php echo JText::_('RST_ADD_MORE_ATTACHMENTS'); ?>" onclick="rst_add_attachments();" />
			<div id="rst_files"></div>
		</td>
	</tr>
	<tr>
		<td>&nbsp;</td>
		<td><button type="submit" name="Submit" class="button"><?php echo JText::_('RST_SUBMIT'); ?></button></td>
	</tr>
</table>

<input type="hidden" name="option" value="com_rsticketspro" />
<input type="hidden" name="view" value="submit" />
<input type="hidden" name="task" value="submit" />
</form>

<script type="text/javascript">
	function rst_show_custom_fields(department_id)
	{
		var custom_fields = new Array();
		
		<?php foreach ($this->custom_fields as $department_id => $fields) { ?>
			custom_fields[<?php echo $department_id; ?>] = new Array();
			<?php foreach ($fields as $i => $field) { ?>
			custom_fields[<?php echo $department_id; ?>][<?php echo $i; ?>] = <?php echo $i; ?>;
			<?php } ?>
		<?php } ?>
		
		<?php foreach ($this->custom_fields as $department_id => $fields) { ?>
			<?php foreach ($fields as $i => $field) { ?>
				document.getElementById('custom-<?php echo $department_id; ?>-<?php echo $i; ?>').style.display = 'none';
			<?php } ?>
		<?php } ?>
		
		if (custom_fields[department_id])
			for (var i=0; i<custom_fields[department_id].length; i++)
			{
				document.getElementById('custom-' + department_id + '-' + i).style.display = '';
			}
	}
	
	function rst_show_priority(department_id)
	{
		var priority = new Array();
		<?php foreach ($this->departments as $department) { ?>
			priority[<?php echo $department->id; ?>] = <?php echo $department->priority_id; ?>;
		<?php } ?>
		
		document.getElementById('submit_priority').value = priority[department_id];
	}
	
	function rst_show_upload(department_id)
	{
		var upload = new Array();
		var upload_extensions = new Array();
			upload[''] = 0;
			upload_extensions[''] = '';
		<?php foreach ($this->departments as $department) { ?>
			upload[<?php echo $department->id; ?>] = <?php echo $department->upload; ?>;
			upload_extensions[<?php echo $department->id; ?>] = '<?php echo JText::sprintf('RST_TICKET_ATTACHMENTS_ALLOWED', $department->upload_extensions); ?>';
		<?php } ?>
		
		if (upload[department_id] == 0)
			var display_upload = 'none';
		else if (upload[department_id] == 1)
			var display_upload = '';
		else if (upload[department_id] == 2)
			var display_upload = '<?php echo $this->is_logged ? '' : 'none'; ?>';
		
		document.getElementById('submit_file').style.display = display_upload;
		document.getElementById('submit_file_message').innerHTML = upload_extensions[department_id];
		
		var current_files = document.getElementsByName('rst_files[]').length;
		var max_files = new Array();
			max_files[''] = 0;
		<?php foreach ($this->departments as $department) { ?>
			max_files[<?php echo $department->id; ?>] = <?php echo $department->upload_files; ?>;
		<?php } ?>
		
		if (max_files[department_id] > 0 && current_files >= max_files[department_id])
		{
			for (var i=document.getElementsByName('rst_files[]').length-1; i>0; i--)
			{
				if (i <= max_files[department_id]-1)
					break;
				document.getElementsByName('rst_files[]')[i].parentNode.removeChild(document.getElementsByName('rst_files[]')[i]);
			}
		}
	}
	
	function rst_show_subject(department_id)
	{
		<?php if ($this->use_predefined_subjects) { ?>
			var subjects = new Array();
				subjects[''] = {'':'<?php echo JText::_('RST_PLEASE_SELECT_SUBJECT', true); ?>'};
			<?php foreach ($this->departments as $department) { ?>
				<?php $values = RSTicketsProHelper::parseSubjects($department->predefined_subjects); ?>
				subjects[<?php echo $department->id; ?>] = {<?php echo implode(',', $values); ?>};
			<?php } ?>
			select = document.getElementById('submit_subject');
			select.options.length = 0;
			for (value in subjects[department_id])
			{
				document.getElementById('submit_subject').options
				
				var option = document.createElement('option');
				option.text = subjects[department_id][value];
				option.value = value;
				try {
					select.add(option, null); // standards compliant; doesn't work in IE
				}
				catch(ex) {
					select.add(option); // IE only
				}
			}
		<?php } ?>
		return true;
	}
	
	function rst_refresh_captcha()
	{
		document.getElementById('submit_captcha_image').src = '<?php echo JURI::root(); ?>index.php?option=com_rsticketspro&task=captcha&sid=' + Math.random();
		return false;
	}
	
	function rst_add_attachments()
	{
		var current_files = document.getElementsByName('rst_files[]').length;
		var max_files = new Array();
			max_files[''] = 0;
		<?php foreach ($this->departments as $department) { ?>
			max_files[<?php echo $department->id; ?>] = <?php echo $department->upload_files; ?>;
		<?php } ?>
		
		var department_id = document.getElementById('department_id').value;
		if (max_files[department_id] > 0 && current_files >= max_files[department_id])
		{
			alert('<?php echo JText::_('RST_MAX_UPLOAD_FILES_REACHED', true); ?>');
			return false;
		}
		
		var new_upload = document.createElement('input');
		new_upload.setAttribute('name', 'rst_files[]');
		new_upload.setAttribute('type', 'file');
		new_upload.setAttribute('class', 'rst_file_block');
		new_upload.className = 'rst_file_block';
		document.getElementById('rst_files').appendChild(new_upload);
	}
	
	function rst_change_submit_type()
	{
		var is_new_user = document.getElementById('submit_type0').checked;
		var is_existing_user = document.getElementById('submit_type1').checked;
		
		document.getElementById('new_user0').style.display = 'none';
		document.getElementById('new_user1').style.display = 'none';
		document.getElementById('existing_user0').style.display = 'none';
		document.getElementById('existing_user1').style.display = 'none';
		
		if (is_new_user)
		{
			document.getElementById('new_user0').style.display = '';
			document.getElementById('new_user1').style.display = '';
		}
		if (is_existing_user)
		{
			document.getElementById('existing_user0').style.display = '';
			document.getElementById('existing_user1').style.display = '';
		}
	}
	
	rst_change_submit_type();
</script>