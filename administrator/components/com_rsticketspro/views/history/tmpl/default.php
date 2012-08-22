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

<form action="<?php echo JRoute::_('index.php?option=com_rsticketspro&view=history'); ?>" method="post" name="adminForm" id="adminForm">
<div id="editcell1">
<table class="adminlist">
	<thead>
	<tr>
		<th><?php echo JText::_('RST_HISTORY_VIEWED'); ?></th>
		<th><?php echo JText::_('RST_TICKET_DATE'); ?></th>
		<th><?php echo JText::_('RST_TICKET_IP'); ?></th>
	</tr>
	</thead>
		<?php
		$k = 0;
		$i = 0;
		$n = count($this->history);
		foreach ($this->history as $item)
		{
		?>
			<tr class="row<?php echo $k; ?>">
				<td><?php echo $this->escape($item->user); ?></td>
				<td><?php echo $this->escape(date($this->date_format, RSTicketsProHelper::getCurrentDate($item->date))); ?></td>
				<td><?php echo $this->escape($item->ip); ?></td>
			</tr>
		<?php
			$k=1-$k;
			$i++;
		}
		?>
	<tfoot>
		<tr>
			<td colspan="3"><?php echo $this->pagination->getListFooter(); ?></td>
		</tr>
	</tfoot>
</table>
</div>

<input type="hidden" name="limitstart" value="<?php echo $this->limitstart; ?>" />
<input type="hidden" name="tmpl" value="component" />

<input type="hidden" name="ticket_id" value="<?php echo $this->ticket_id; ?>" />
</form>

<?php JHTML::_('behavior.keepalive'); ?>