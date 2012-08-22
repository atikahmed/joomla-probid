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

	var dt = $('groups-pane').getElements('dt');
	for (var i=0; i<dt.length; i++)
	{
		if (dt[i].className == 'open')
			$('tabposition').value = i;
	}
	
	// do field validation
	if (form.name.value.length == 0)
		alert('<?php echo JText::_('RST_GROUP_NAME_ERROR', true); ?>');
	else
		submitform(pressbutton);
}

<?php if (RSTicketsProHelper::isJ16()) { ?>
	Joomla.submitbutton = submitbutton;
<?php } ?>
</script>

<form action="<?php echo JRoute::_('index.php?option=com_rsticketspro&controller=groups&task=edit'); ?>" method="post" name="adminForm" id="adminForm">
<?php
echo $this->tabs->startPane('groups-pane');

echo $this->tabs->startPanel(JText::_('RST_GENERAL'), 'general');
?>
<div class="col100">
	<table cellspacing="0" cellpadding="0" border="0" width="100%" class="admintable">
		<tr>
			<td width="300" style="width: 300px;" align="right" class="key"><span class="hasTip" title="<?php echo JText::_('RST_GROUP_DESC'); ?>"><label for="name"><?php echo JText::_('RST_GROUP'); ?></label></span></td>
			<td><input type="text" name="name" value="<?php echo $this->escape($this->row->name); ?>" id="name" size="120" maxlength="255" /></td>
		</tr>
	</table>
</div>
<?php
echo $this->tabs->endPanel();

echo $this->tabs->startPanel(JText::_('RST_GROUP_SUBMITTING'), 'submitting');
?>
<div class="col100">
	<table cellspacing="0" cellpadding="0" border="0" width="100%" class="admintable">
		<tr>
			<td width="300" style="width: 300px;" align="right" class="key"><span class="hasTip" title="<?php echo JText::_('RST_CAN_ADD_TICKET_DESC'); ?>"><label for="add_ticket"><?php echo JText::_('RST_CAN_ADD_TICKET'); ?></label></span></td>
			<td><?php echo $this->lists['add_ticket']; ?></td>
		</tr>
		<tr>
			<td width="300" style="width: 300px;" align="right" class="key"><span class="hasTip" title="<?php echo JText::_('RST_CAN_ADD_TICKET_CUSTOMERS_DESC'); ?>"><label for="add_ticket_customers"><?php echo JText::_('RST_CAN_ADD_TICKET_CUSTOMERS'); ?></label></span></td>
			<td><?php echo $this->lists['add_ticket_customers']; ?></td>
		</tr>
		<tr>
			<td width="300" style="width: 300px;" align="right" class="key"><span class="hasTip" title="<?php echo JText::_('RST_CAN_ADD_TICKET_STAFF_DESC'); ?>"><label for="add_ticket_staff"><?php echo JText::_('RST_CAN_ADD_TICKET_STAFF'); ?></label></span></td>
			<td><?php echo $this->lists['add_ticket_staff']; ?></td>
		</tr>
	</table>
</div>
<?php
echo $this->tabs->endPanel();

echo $this->tabs->startPanel(JText::_('RST_GROUP_REPLYING'), 'replying');
?>
<div class="col100">
	<table cellspacing="0" cellpadding="0" border="0" width="100%" class="admintable">
		<tr>
			<td width="300" style="width: 300px;" align="right" class="key"><span class="hasTip" title="<?php echo JText::_('RST_CAN_ANSWER_TICKET_DESC'); ?>"><label for="answer_ticket"><?php echo JText::_('RST_CAN_ANSWER_TICKET'); ?></label></span></td>
			<td><?php echo $this->lists['answer_ticket']; ?></td>
		</tr>
		<tr>
			<td width="300" style="width: 300px;" align="right" class="key"><span class="hasTip" title="<?php echo JText::_('RST_CAN_UPDATE_TICKET_REPLIES_DESC'); ?>"><label for="update_ticket_replies"><?php echo JText::_('RST_CAN_UPDATE_TICKET_REPLIES'); ?></label></span></td>
			<td><?php echo $this->lists['update_ticket_replies']; ?></td>
		</tr>
		<tr>
			<td width="300" style="width: 300px;" align="right" class="key"><span class="hasTip" title="<?php echo JText::_('RST_CAN_UPDATE_TICKET_REPLIES_CUSTOMERS_DESC'); ?>"><label for="update_ticket_replies_customers"><?php echo JText::_('RST_CAN_UPDATE_TICKET_REPLIES_CUSTOMERS'); ?></label></span></td>
			<td><?php echo $this->lists['update_ticket_replies_customers']; ?></td>
		</tr>
		<tr>
			<td width="300" style="width: 300px;" align="right" class="key"><span class="hasTip" title="<?php echo JText::_('RST_CAN_UPDATE_TICKET_REPLIES_STAFF_DESC'); ?>"><label for="update_ticket_replies_staff"><?php echo JText::_('RST_CAN_UPDATE_TICKET_REPLIES_STAFF'); ?></label></span></td>
			<td><?php echo $this->lists['update_ticket_replies_staff']; ?></td>
		</tr>
		<tr>
			<td width="300" style="width: 300px;" align="right" class="key"><span class="hasTip" title="<?php echo JText::_('RST_CAN_DELETE_TICKET_REPLIES_DESC'); ?>"><label for="delete_ticket_replies"><?php echo JText::_('RST_CAN_DELETE_TICKET_REPLIES'); ?></label></span></td>
			<td><?php echo $this->lists['delete_ticket_replies']; ?></td>
		</tr>
		<tr>
			<td width="300" style="width: 300px;" align="right" class="key"><span class="hasTip" title="<?php echo JText::_('RST_CAN_DELETE_TICKET_REPLIES_CUSTOMERS_DESC'); ?>"><label for="delete_ticket_replies_customers"><?php echo JText::_('RST_CAN_DELETE_TICKET_REPLIES_CUSTOMERS'); ?></label></span></td>
			<td><?php echo $this->lists['delete_ticket_replies_customers']; ?></td>
		</tr>
		<tr>
			<td width="300" style="width: 300px;" align="right" class="key"><span class="hasTip" title="<?php echo JText::_('RST_CAN_DELETE_TICKET_REPLIES_STAFF_DESC'); ?>"><label for="delete_ticket_replies_staff"><?php echo JText::_('RST_CAN_DELETE_TICKET_REPLIES_STAFF'); ?></label></span></td>
			<td><?php echo $this->lists['delete_ticket_replies_staff']; ?></td>
		</tr>
	</table>
</div>
<?php
echo $this->tabs->endPanel();

echo $this->tabs->startPanel(JText::_('RST_GROUP_VIEWING'), 'viewing');
?>
<div class="col100">
	<table cellspacing="0" cellpadding="0" border="0" width="100%" class="admintable">
		<tr>
			<td width="300" style="width: 300px;" align="right" class="key"><span class="hasTip" title="<?php echo JText::_('RST_CAN_SEE_UNASSIGNED_TICKETS_DESC'); ?>"><label for="see_unassigned_tickets"><?php echo JText::_('RST_CAN_SEE_UNASSIGNED_TICKETS'); ?></label></span></td>
			<td><?php echo $this->lists['see_unassigned_tickets']; ?></td>
		</tr>
		<tr>
			<td width="300" style="width: 300px;" align="right" class="key"><span class="hasTip" title="<?php echo JText::_('RST_CAN_SEE_OTHER_TICKETS_DESC'); ?>"><label for="see_other_tickets"><?php echo JText::_('RST_CAN_SEE_OTHER_TICKETS'); ?></label></span></td>
			<td><?php echo $this->lists['see_other_tickets']; ?></td>
		</tr>
	</table>
</div>
<?php
echo $this->tabs->endPanel();

echo $this->tabs->startPanel(JText::_('RST_GROUP_UPDATING'), 'updating');
?>
<div class="col100">
	<table cellspacing="0" cellpadding="0" border="0" width="100%" class="admintable">
		<tr>
			<td width="300" style="width: 300px;" align="right" class="key"><span class="hasTip" title="<?php echo JText::_('RST_CAN_UPDATE_TICKET_DESC'); ?>"><label for="update_ticket"><?php echo JText::_('RST_CAN_UPDATE_TICKET'); ?></label></span></td>
			<td><?php echo $this->lists['update_ticket']; ?></td>
		</tr>
		<tr>
			<td width="300" style="width: 300px;" align="right" class="key"><span class="hasTip" title="<?php echo JText::_('RST_CAN_UPDATE_TICKET_CUSTOM_FIELDS_DESC'); ?>"><label for="update_ticket_custom_fields"><?php echo JText::_('RST_CAN_UPDATE_TICKET_CUSTOM_FIELDS'); ?></label></span></td>
			<td><?php echo $this->lists['update_ticket_custom_fields']; ?></td>
		</tr>
		<tr>
			<td width="300" style="width: 300px;" align="right" class="key"><span class="hasTip" title="<?php echo JText::_('RST_CAN_ASSIGN_TICKETS_DESC'); ?>"><label for="assign_tickets"><?php echo JText::_('RST_CAN_ASSIGN_TICKETS'); ?></label></span></td>
			<td><?php echo $this->lists['assign_tickets']; ?></td>
		</tr>
		<tr>
			<td width="300" style="width: 300px;" align="right" class="key"><span class="hasTip" title="<?php echo JText::_('RST_CAN_CHANGE_TICKET_STATUS_DESC'); ?>"><label for="change_ticket_status"><?php echo JText::_('RST_CAN_CHANGE_TICKET_STATUS'); ?></label></span></td>
			<td><?php echo $this->lists['change_ticket_status']; ?></td>
		</tr>
		<tr>
			<td width="300" style="width: 300px;" align="right" class="key"><span class="hasTip" title="<?php echo JText::_('RST_CAN_MOVE_TICKET_DESC'); ?>"><label for="move_ticket"><?php echo JText::_('RST_CAN_MOVE_TICKET'); ?></label></span></td>
			<td><?php echo $this->lists['move_ticket']; ?></td>
		</tr>
		<tr>
			<td width="300" style="width: 300px;" align="right" class="key"><span class="hasTip" title="<?php echo JText::_('RST_CAN_DELETE_TICKET_DESC'); ?>"><label for="delete_ticket"><?php echo JText::_('RST_CAN_DELETE_TICKET'); ?></label></span></td>
			<td><?php echo $this->lists['delete_ticket']; ?></td>
		</tr>
	</table>
</div>
<?php
echo $this->tabs->endPanel();

echo $this->tabs->startPanel(JText::_('RST_GROUP_NOTES'), 'notes');
?>
<div class="col100">
	<table cellspacing="0" cellpadding="0" border="0" width="100%" class="admintable">
		<tr>
			<td width="300" style="width: 300px;" align="right" class="key"><span class="hasTip" title="<?php echo JText::_('RST_CAN_VIEW_NOTES_DESC'); ?>"><label for="view_notes"><?php echo JText::_('RST_CAN_VIEW_NOTES'); ?></label></span></td>
			<td><?php echo $this->lists['view_notes']; ?></td>
		</tr>
		<tr>
			<td width="300" style="width: 300px;" align="right" class="key"><span class="hasTip" title="<?php echo JText::_('RST_CAN_ADD_NOTE_DESC'); ?>"><label for="add_note"><?php echo JText::_('RST_CAN_ADD_NOTE'); ?></label></span></td>
			<td><?php echo $this->lists['add_note']; ?></td>
		</tr>
		<tr>
			<td width="300" style="width: 300px;" align="right" class="key"><span class="hasTip" title="<?php echo JText::_('RST_CAN_UPDATE_NOTE_DESC'); ?>"><label for="update_note"><?php echo JText::_('RST_CAN_UPDATE_NOTE'); ?></label></span></td>
			<td><?php echo $this->lists['update_note']; ?></td>
		</tr>
		<tr>
			<td width="300" style="width: 300px;" align="right" class="key"><span class="hasTip" title="<?php echo JText::_('RST_CAN_UPDATE_NOTE_STAFF_DESC'); ?>"><label for="update_note_staff"><?php echo JText::_('RST_CAN_UPDATE_NOTE_STAFF'); ?></label></span></td>
			<td><?php echo $this->lists['update_note_staff']; ?></td>
		</tr>
		<tr>
			<td width="300" style="width: 300px;" align="right" class="key"><span class="hasTip" title="<?php echo JText::_('RST_CAN_DELETE_NOTE_DESC'); ?>"><label for="delete_note"><?php echo JText::_('RST_CAN_DELETE_NOTE'); ?></label></span></td>
			<td><?php echo $this->lists['delete_note']; ?></td>
		</tr>
		<tr>
			<td width="300" style="width: 300px;" align="right" class="key"><span class="hasTip" title="<?php echo JText::_('RST_CAN_DELETE_NOTE_STAFF_DESC'); ?>"><label for="delete_note_staff"><?php echo JText::_('RST_CAN_DELETE_NOTE_STAFF'); ?></label></span></td>
			<td><?php echo $this->lists['delete_note_staff']; ?></td>
		</tr>
	</table>
</div>
<?php
echo $this->tabs->endPanel();

echo $this->tabs->endPane();
?>
	
<?php echo JHTML::_('form.token'); ?>
<input type="hidden" name="option" value="com_rsticketspro" />
<input type="hidden" name="controller" value="groups" />
<input type="hidden" name="task" value="edit" />
<input type="hidden" name="view" value="groups" />

<input type="hidden" name="id" value="<?php echo $this->row->id; ?>" />
<input type="hidden" name="tabposition" id="tabposition" value="0" />
</form>

<?php
//keep session alive while editing
JHTML::_('behavior.keepalive');
?>