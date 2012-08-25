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

<form action="<?php echo RSTicketsProHelper::route('index.php?option=com_rsticketspro&view=history'); ?>" method="post" name="adminForm">
<table class="rsticketspro_tablebig rsticketspro_tablebig2" width="100%" border="0" cellspacing="0" cellpadding="0">
	<tr>
		<th align="center"><?php echo JText::_('RST_HISTORY_VIEWED'); ?></th>
		<th align="center"><?php echo JText::_('RST_TICKET_DATE'); ?></th>
		<th align="center"><?php echo JText::_('RST_TICKET_IP'); ?></th>
	</tr>
		<?php
		$k = 1;
		$i = 0;
		$n = count($this->history);
		foreach ($this->history as $item)
		{
		?>
			<tr class="sectiontableentry<?php echo $k; ?>">
				<td><?php echo $this->escape($item->user); ?></td>
				<td><?php echo $this->escape(date($this->date_format, RSTicketsProHelper::getCurrentDate($item->date))); ?></td>
				<td><?php echo $this->escape($item->ip); ?></td>
			</tr>
		<?php
			$k = $k == 1 ? 2 : 1;
			$i++;
		}
		?>
	<?php if ($this->pagination->get('pages.total') > 1) { ?>
	<tr>
		<td align="center" colspan="3" class="sectiontablefooter">
		<div class="pagination"><?php echo $this->pagination->getPagesLinks(); ?></div>
		</td>
	</tr>
	<tr>
		<td colspan="3" align="right"><?php echo $this->pagination->getPagesCounter(); ?></td>
	</tr>
	<?php } ?>
</table>
<input type="hidden" name="limitstart" value="<?php echo $this->limitstart; ?>" />
<input type="hidden" name="tmpl" value="component" />

<input type="hidden" name="ticket_id" value="<?php echo $this->ticket_id; ?>" />
</form>

<?php JHTML::_('behavior.keepalive'); ?>