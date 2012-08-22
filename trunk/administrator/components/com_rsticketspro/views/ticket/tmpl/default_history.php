<?php
/**
* @version 2.0.0
* @package RSTickets! Pro 2.0.0
* @copyright (C) 2010 www.rsjoomla.com
* @license GPL, http://www.gnu.org/licenses/gpl-2.0.html
*/

defined('_JEXEC') or die('Restricted access');
?>
<table class="adminlist">
<thead>
<tr>
	<th colspan="2"><?php echo JText::_('RST_TICKET_HISTORY'); ?></th>
</tr>
</thead>
<tr>
<td>
<?php if ($this->history_tickets) { ?>
<table class="adminlist">
<thead>
	<tr>
		<th><?php echo JText::_('RST_TICKET_CODE'); ?> <?php echo JText::_('RST_TICKET_SUBJECT'); ?></th>
		<th><?php echo JText::_('RST_TICKET_STATUS'); ?></th>
		<th><?php echo JText::_('RST_TICKET_REPLIES'); ?></th>
		<th><?php echo JText::_('RST_TICKET_DATE'); ?></th>
	</tr>
</thead>
<?php foreach ($this->history_tickets as $ticket){ ?>
	<tr>
		<td>[<?php echo $this->escape($ticket->code); ?>] <a href="<?php echo RSTicketsProHelper::route('index.php?option=com_rsticketspro&view=ticket&cid='.$ticket->id.':'.JFilterOutput::stringURLSafe($ticket->subject)); ?>" title="<?php echo $this->escape($ticket->subject); ?>"><?php echo $this->escape($ticket->subject); ?></a></td>
		<td><?php echo JText::_($ticket->status_name); ?></td>
		<td><?php echo $ticket->replies.' '.JText::_('RST_TICKET_REPLIES'); ?></td>
		<td><?php echo $this->escape(date($this->date_format, RSTicketsProHelper::getCurrentDate($ticket->date))); ?></td>
   </tr>
<?php } ?>
</table>
<?php } else { ?>
	<p><?php echo JText::_('RST_NO_TICKET_HISTORY'); ?></p>
<?php } ?>
</td>
</tr>
</table>