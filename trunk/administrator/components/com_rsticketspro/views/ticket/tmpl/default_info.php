<?php
/**
* @version 2.0.0
* @package RSTickets! Pro 2.0.0
* @copyright (C) 2010 www.rsjoomla.com
* @license GPL, http://www.gnu.org/licenses/gpl-2.0.html
*/

defined('_JEXEC') or die('Restricted access');
?>
<form action="<?php echo JRoute::_('index.php?option=com_rsticketspro&view=ticket&cid='.$this->row->id.':'.JFilterOutput::stringURLSafe($this->row->subject)); ?>" method="post" name="infoForm">
<table class="adminlist">
	<thead>
	<tr>
		<th colspan="2"><?php echo JText::_('RST_TICKET_INFORMATION'); ?></th>
	</tr>
	</thead>
	<tr>
		<td><u><?php echo JText::_('RST_TICKET_SUBJECT'); ?></u><br />
		<?php if ($this->is_staff && $this->permissions->update_ticket && !$this->do_print) { ?>
			<input type="text" name="ticket_subject" value="<?php echo $this->escape($this->row->subject); ?>" class="inputbox" />
		<?php } else { ?>
			<?php echo $this->escape($this->row->subject); ?>
		<?php } ?>
		</td>
	</tr>
	<tr>
		<td><u><?php echo JText::_('RST_TICKET_DEPARTMENT'); ?></u><br />
		<?php if ($this->is_staff && $this->permissions->move_ticket && !$this->do_print) { ?>
			<?php echo $this->lists['department']; ?>
		<?php } else { ?>
			<?php echo $this->escape(JText::_($this->row->department)); ?>
		<?php } ?>
		</td>
	</tr>
	<tr>
		<td><u><?php echo JText::_('RST_TICKET_DATE'); ?></u><br />
		<?php echo $this->escape(date($this->date_format, RSTicketsProHelper::getCurrentDate($this->row->date))); ?></td>
	</tr>
	<tr>
		<td><u><?php echo JText::_('RST_TICKET_STATUS'); ?></u><br />
		<?php if ($this->is_staff && $this->permissions->change_ticket_status && !$this->do_print) { ?>
			<?php echo $this->lists['status']; ?>
		<?php } else { ?>
			<?php echo $this->escape(JText::_($this->row->status)); ?>
		<?php } ?>
		</td>
	</tr>
	<tr>
		<td><u><?php echo JText::_('RST_TICKET_CODE'); ?></u><br />
		<?php echo $this->row->code; ?></td>
	</tr>
	<tr>
		<td><u><?php echo JText::_('RST_TICKET_PRIORITY'); ?></u><br />
		<?php if ($this->is_staff && $this->permissions->update_ticket && !$this->do_print) { ?>
			<?php echo $this->lists['priority']; ?>
		<?php } else { ?>
			<?php echo JText::_($this->row->priority); ?>
		<?php } ?>
		</td>
	</tr>
	<tr>
		<td><u><?php echo JText::_('RST_TICKET_STAFF'); ?></u><br />
		<?php if ($this->is_staff && $this->permissions->assign_tickets && !$this->do_print) { ?>
			<?php echo $this->lists['staff']; ?>
		<?php } else { ?>
			<?php echo $this->row->staff_id ? $this->escape($this->row->staff->get($this->what)) : JText::_('RST_UNASSIGNED'); ?>
		<?php } ?>
		</td>
	</tr>
	<tr>
		<td><u><?php echo JText::_('RST_TICKET_CUSTOMER'); ?></u><br />
		<?php if ($this->is_staff && ($this->permissions->add_ticket_customers || $this->permissions->add_ticket_staff) && !$this->do_print) { ?>
			<span class="hasTip" title="<?php echo JText::_('RST_TICKET_CHANGE_CUSTOMER'); ?>"><a class="modal" href="<?php echo JRoute::_('index.php?option=com_rsticketspro&view=users&tmpl=component'); ?>" rel="{handler: 'iframe', size: {x: 600, y: 475}}" id="submit_name_text"><?php echo $this->escape($this->row->customer->get($this->what)); ?></a></span>
			<div id="submit_email_text" style="display: none"></div>
			<input type="hidden" name="customer_id" id="customer_id" value="<?php echo $this->escape($this->row->customer_id); ?>" />
		<?php } else { ?>
			<?php echo $this->escape($this->row->customer->get($this->what)); ?>
		<?php } ?>
		</td>
	</tr>
	<?php if ($this->can_update) { ?>
	<tr>
		<td><button type="submit" class="button"><?php echo JText::_('RST_UPDATE'); ?></button></td>
	</tr>
	<?php } ?>
</table>
<input type="hidden" name="option" value="com_rsticketspro" />
<input type="hidden" name="view" value="ticket" />
<input type="hidden" name="task" value="saveticketinfo" />
</form>