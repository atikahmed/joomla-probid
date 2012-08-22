<?php
/**
* @version 2.0.0
* @package RSTickets! Pro 2.0.0
* @copyright (C) 2010 www.rsjoomla.com
* @license GPL, http://www.gnu.org/licenses/gpl-2.0.html
*/

defined('_JEXEC') or die('Restricted access');
JHTML::_('behavior.tooltip');
?>

<style type="text/css">
<?php foreach ($this->priorityColors as $priority) { ?>
	<?php if ($priority->bg_color) { ?>
	table.adminlist tr.rst_priority_color_<?php echo $priority->id; ?> td<?php if (!$this->colorWholeTicket) { ?>.rst_priority_cell<?php } ?>
	{
		background-color: <?php echo $priority->bg_color; ?> !important;
	}
	<?php } ?>
	<?php if ($priority->fg_color) { ?>
	table.adminlist tr.rst_priority_color_<?php echo $priority->id; ?> td<?php if (!$this->colorWholeTicket) { ?>.rst_priority_cell<?php } ?>, table.adminlist tr.rst_priority_color_<?php echo $priority->id; ?> td a<?php if (!$this->colorWholeTicket) { ?>.rst_priority_cell<?php } ?>
	{
		color: <?php echo $priority->fg_color; ?> !important;
	}
	<?php } ?>
<?php } ?>
</style>

<?php echo RSTicketsProHelper::getConfig('global_message'); ?>

<?php if ($this->is_searching) { ?>
	<p><?php if ($this->is_staff) { ?><a href="<?php echo JRoute::_('index.php?option=com_rsticketspro&controller=searches&task=edit'); ?>" class="rst_search"><?php echo JText::_('RST_SAVE_SEARCH'); ?></a><?php } ?> <?php echo JText::sprintf('RST_RESET_SEARCH', JRoute::_('index.php?option=com_rsticketspro&task=resetsearch')); ?></p>
<?php } ?>
	<?php if ($this->has_searches) { ?>
	<p>
		<a href="<?php echo JRoute::_('index.php?option=com_rsticketspro&view=searches'); ?>" class="rst_manage_searches"><?php echo JText::_('RST_MANAGE_SEARCHES'); ?></a>
		<?php foreach ($this->searches as $search) { ?>
			<?php if ($search->id != $this->predefined_search) { ?>
			<a href="<?php echo JRoute::_('index.php?option=com_rsticketspro&controller=searches&task=search&cid='.$search->id); ?>" class="hasTip" title="<?php echo JText::sprintf('RST_SEARCH_CLICK_DESC', $this->escape($search->name)); ?>"><?php echo $this->escape($search->name); ?></a>
			<?php } else { ?>
				<?php echo $this->escape($search->name); ?>
			<?php } ?>
		<?php } ?>
	</p>
	<?php } ?>

<form action="<?php echo JRoute::_('index.php?option=com_rsticketspro&view=tickets'); ?>" method="post" name="adminForm" id="adminForm">
	<?php if ($this->is_staff) { ?>
	<table class="adminlist" id="bulk_actions" style="display: none; margin-bottom: 15px;">
		<thead>
		<tr>
			<th colspan="6"><?php echo JText::_('RST_BULK_ACTIONS'); ?></th>
		</tr>
		</thead>
		<tr class="row0">
		<?php if ($this->permissions->assign_tickets) { ?>
			<td>
				<u><?php echo JText::_('RST_TICKET_STAFF'); ?></u><br />
				<?php echo $this->lists['staff']; ?>
			</td>
		<?php } ?>
		<?php if ($this->permissions->update_ticket) { ?>
			<td>
				<u><?php echo JText::_('RST_TICKET_PRIORITY'); ?></u><br />
				<?php echo $this->lists['priority']; ?>
			</td>
		<?php } ?>
		<?php if ($this->permissions->change_ticket_status) { ?>
			<td>
				<u><?php echo JText::_('RST_TICKET_STATUS'); ?></u><br />
				<?php echo $this->lists['status']; ?>
			</td>
		<?php } ?>
			<td>
				<u><?php echo JText::_('RST_TICKET_NOTIFY'); ?></u><br />
				<?php echo $this->lists['notify']; ?>
			</td>
		<?php if ($this->permissions->delete_ticket) { ?>
			<td>
				<u><?php echo JText::_('RST_TICKET_DELETE'); ?></u><br />
				<?php echo $this->lists['delete']; ?>
			</td>
		<?php } ?>
		<td>
			<button type="button" onclick="submitbutton('updatetickets');" id="rst_update_button" class="button"><?php echo JText::_('RST_UPDATE'); ?></button>
		</td>
		</tr>
	</table>
	<?php } ?>
	
	<table class="adminform">
		<tr>
			<td width="100%">
			<?php echo JText::_( 'SEARCH' ); ?>
			<input type="text" name="filter_word" id="filter_word" value="<?php echo $this->escape($this->filter_word); ?>" class="text_area" onchange="submitbutton('search');" />
			<button type="submit"><?php echo JText::_( 'Go' ); ?></button>
			<button onclick="this.form.getElementById('filter_word').value='';this.form.task.value='resetsearch';this.form.submit();"><?php echo JText::_( 'Reset' ); ?></button>
			<a href="<?php echo JRoute::_('index.php?option=com_rsticketspro&view=search&advanced=1'); ?>"><?php echo JText::_('RST_OPEN_ADVANCED_SEARCH'); ?></a>
			</td>
			<td nowrap="nowrap"></td>
		</tr>
	</table>
	<div id="editcell1">
		<table class="adminlist">
			<thead>
			<tr>
				<th width="5"><?php echo JText::_( '#' ); ?></th>
				<th width="20"><input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count($this->tickets); ?>); rst_show_bulk();"/></th>
				<th width="140"><?php echo JHTML::_('grid.sort', 'RST_TICKET_DATE', 'date', $this->sortOrder, $this->sortColumn); ?></th>
				<th width="140"><?php echo JHTML::_('grid.sort', 'RST_TICKET_LAST_REPLY', 'last_reply', $this->sortOrder, $this->sortColumn); ?></th>
				<th width="5"><?php echo JHTML::_('grid.sort', 'RST_FLAGGED', 'flagged', $this->sortOrder, $this->sortColumn); ?></th>
				<?php if ($this->permissions->delete_ticket) { ?>
					<th width="5"><?php echo JText::_('RST_DELETE'); ?></th>
				<?php } ?>
				<th><?php echo JHTML::_('grid.sort', 'RST_TICKET_CODE', 'code', $this->sortOrder, $this->sortColumn); ?> <?php echo JHTML::_('grid.sort', 'RST_TICKET_SUBJECT', 'subject', $this->sortOrder, $this->sortColumn); ?></th>
				<th><?php echo JHTML::_('grid.sort', 'RST_TICKET_CUSTOMER', 'customer', $this->sortOrder, $this->sortColumn); ?></th>
				<th width="5"><?php echo JHTML::_('grid.sort', 'RST_TICKET_PRIORITY', 'priority', $this->sortOrder, $this->sortColumn); ?></th>
				<th width="5"><?php echo JHTML::_('grid.sort', 'RST_TICKET_STATUS', 'status', $this->sortOrder, $this->sortColumn); ?></th>
				<th><?php echo JHTML::_('grid.sort', 'RST_TICKET_STAFF', 'staff', $this->sortOrder, $this->sortColumn); ?></th>
			</tr>
			</thead>
	<?php
	$k = 0;
	$i = 0;
	$n = count($this->tickets);
	foreach ($this->tickets as $item)
	{
		$grid = JHTML::_('grid.id', $i, $item->id);
		if (RSTicketsProHelper::isJ25())
			$grid = str_replace('Joomla.isChecked', 'rst_show_bulk(); Joomla.isChecked', $grid);
		else
			$grid = str_replace('isChecked', 'rst_show_bulk(); isChecked', $grid);
	?>
		<tr class="row<?php echo $k; ?> rst_priority_color_<?php echo $item->priority_id; ?>">
			<td><?php echo $this->pagination->getRowOffset($i); ?></td>
			<td><?php echo $grid; ?></td>
			<td><?php echo $this->escape(date($this->date_format, RSTicketsProHelper::getCurrentDate($item->date))); ?></td>
			<td><?php echo $this->escape(date($this->date_format, RSTicketsProHelper::getCurrentDate($item->last_reply))); ?></td>
			<td align="center"><a href="javascript: void(0);" onclick="rst_flag_ticket('index.php', this, '<?php echo $item->id; ?>');" class="rst_flag<?php echo $item->flagged ? ' rst_flag_active' : ''; ?>"></a></td>
			<?php if ($this->permissions->delete_ticket) { ?>
				<td align="center"><?php echo JHTML::_('rsticketsproicon.deleteticket', $item->id, $this->is_staff, $this->permissions); ?></td>
			<?php } ?>
			<td>
			<?php if ($item->has_files) { ?>
				<?php echo JHTML::image('components/com_rsticketspro/assets/images/attach.png', JText::_('RST_THIS_TICKET_HAS_ATTACHMENTS'), 'title="'.JText::_('RST_THIS_TICKET_HAS_ATTACHMENTS').'" class="hasTip"'); ?>
			<?php } ?>
			<a href="<?php echo JRoute::_('index.php?option=com_rsticketspro&view=ticket&cid='.$item->id); ?>"><?php echo $item->code; ?></a> (<?php echo $item->replies; ?>)
			<br />
			<a href="<?php echo JRoute::_('index.php?option=com_rsticketspro&view=ticket&cid='.$item->id); ?>"><?php echo $this->escape($item->subject); ?></a>
			<?php echo JHTML::_('rsticketsproicon.notify', $this->is_staff, $item); ?>
			</td>
			<td><a href="<?php echo JRoute::_(RSTicketsProHelper::isJ16() ? 'index.php?option=com_users&view=user&task=user.edit&id='.$item->customer_id : 'index.php?option=com_users&view=user&task=edit&cid[]='.$item->customer_id); ?>"><?php echo $this->escape($item->customer); ?></a></td>
			<td class="rst_priority_cell"><?php echo JText::_($item->priority); ?></td>
			<td><?php echo JText::_($item->status); ?></td>
			<td><?php echo $item->staff_id ? $this->escape($item->staff) : '<em>'.JText::_('RST_UNASSIGNED').'</em>'; ?></td>
		</tr>
	<?php
		$i++;
		$k=1-$k;
	}
	?>
		<tfoot>
			<tr>
				<td colspan="11"><?php echo $this->pagination->getListFooter(); ?></td>
			</tr>
		</tfoot>
		</table>
	</div>
	
	<?php echo JHTML::_( 'form.token' ); ?>
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="option" value="com_rsticketspro" />
	<input type="hidden" name="view" value="tickets" />
	<input type="hidden" name="task" value="" />
	
	<input type="hidden" name="filter_order" value="<?php echo $this->escape($this->sortColumn); ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $this->escape($this->sortOrder); ?>" />
</form>

<script type="text/javascript">
function rst_show_bulk()
{
	var show_bulk = false;
	
	for (var i=0; i<<?php echo count($this->tickets); ?>; i++)
		if (document.getElementById('cb' + i).checked)
		{
			show_bulk = true;
			break;
		}
	
	document.getElementById('bulk_actions').style.display = show_bulk ? '' : 'none';
}

function rst_disable_bulk(value)
{
	if (value == 0)
	{
		<?php if ($this->permissions->assign_tickets) { ?>
		document.getElementById('bulk_staff_id').disabled = false;
		<?php } ?>
		<?php if ($this->permissions->update_ticket) { ?>
		document.getElementById('bulk_priority_id').disabled = false;
		<?php } ?>
		<?php if ($this->permissions->change_ticket_status) { ?>
		document.getElementById('bulk_status_id').disabled = false;
		<?php } ?>
		document.getElementById('bulk_notify').disabled = false;
		
		<?php if ($this->permissions->delete_ticket) { ?>
		document.getElementById('rst_update_button').onclick = function () { submitbutton('updatetickets'); };
		<?php } ?>
	}
	else
	{
		<?php if ($this->permissions->assign_tickets) { ?>
		document.getElementById('bulk_staff_id').disabled = true;
		<?php } ?>
		<?php if ($this->permissions->update_ticket) { ?>
		document.getElementById('bulk_priority_id').disabled = true;
		<?php } ?>
		<?php if ($this->permissions->change_ticket_status) { ?>
		document.getElementById('bulk_status_id').disabled = true;
		<?php } ?>
		document.getElementById('bulk_notify').disabled = true;
		
		<?php if ($this->permissions->delete_ticket) { ?>
		document.getElementById('rst_update_button').onclick = function () { if (confirm('<?php echo JText::_('RST_DELETE_TICKETS_CONFIRM', true); ?>')) submitbutton('updatetickets'); };
		<?php } ?>
	}
}
</script>

<?php JHTML::_('behavior.keepalive'); ?>