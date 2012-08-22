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

	// do field validation
	if (form.user_id.value == 0)
		alert('<?php echo JText::_('RST_STAFF_USER_ERROR', true); ?>');
	else if (document.getElementById('department_id').value.length == 0)
		alert('<?php echo JText::_('RST_STAFF_DEPARTMENT_ERROR', true); ?>');
	else
		submitform(pressbutton);
}

<?php if (RSTicketsProHelper::isJ16()) { ?>
	Joomla.submitbutton = submitbutton;
<?php } ?>
</script>

<form action="<?php echo JRoute::_('index.php?option=com_rsticketspro&controller=staff&task=edit'); ?>" method="post" name="adminForm" id="adminForm">
<div class="col100">
	<table cellspacing="0" cellpadding="0" border="0" width="100%" class="admintable">
		<tr>
			<td width="300" style="width: 300px;" align="right" class="key"><span class="hasTip" title="<?php echo JText::_('Username'); ?>"><label for="user_id"><?php echo JText::_('Username'); ?></label></span></td>
			<td><a class="modal" id="user_id" href="<?php echo JRoute::_('index.php?option=com_rsticketspro&view=allusers&tmpl=component'); ?>" rel="{handler: 'iframe', size: {x: 560, y: 375}}"><?php echo $this->row->user_id ? $this->escape($this->row->username) : '<em>'.JText::_('RST_NO_USER_SELECTED').'</em>'; ?></a></td>
		</tr>
		<tr>
			<td width="300" style="width: 300px;" align="right" class="key"><span class="hasTip" title="<?php echo JText::_('RST_GROUP_DESC'); ?>"><label for="group_id"><?php echo JText::_('RST_GROUP'); ?></label></span></td>
			<td><?php echo $this->lists['group_id']; ?></td>
		</tr>
		<tr>
			<td width="300" style="width: 300px;" align="right" class="key"><span class="hasTip" title="<?php echo JText::_('RST_STAFF_DEPARTMENTS_DESC'); ?>"><label for="department_id"><?php echo JText::_('RST_STAFF_DEPARTMENTS'); ?></label></span></td>
			<td><?php echo $this->lists['department_id']; ?></td>
		</tr>
		<tr>
			<td width="300" style="width: 300px;" align="right" class="key"><span class="hasTip" title="<?php echo JText::_('RST_STAFF_PRIORITY_DESC'); ?>"><label for="priority_id"><?php echo JText::_('RST_STAFF_PRIORITY'); ?></label></span></td>
			<td><?php echo $this->lists['priority_id']; ?></td>
		</tr>
		<tr>
			<td width="300" style="width: 300px;" align="right" class="key"><span class="hasTip" title="<?php echo JText::_('RST_SIGNATURE_DESC'); ?>"><label for="signature"><?php echo JText::_('RST_SIGNATURE'); ?></label></span></td>
			<td><?php echo $this->editor->display('signature', @$this->row->signature,500,250,70,10); ?></td>
		</tr>
	</table>
</div>
	
<?php echo JHTML::_('form.token'); ?>
<input type="hidden" name="option" value="com_rsticketspro" />
<input type="hidden" name="controller" value="staff" />
<input type="hidden" name="task" value="edit" />
<input type="hidden" name="view" value="staff" />

<input type="hidden" name="user_id" value="<?php echo $this->row->user_id; ?>" />
<input type="hidden" name="id" value="<?php echo $this->row->id; ?>" />
</form>

<?php
//keep session alive while editing
JHTML::_('behavior.keepalive');
?>