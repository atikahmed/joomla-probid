<?php
/**
* @version 2.0.0
* @package RSTickets! Pro 2.0.0
* @copyright (C) 2010 www.rsjoomla.com
* @license GPL, http://www.gnu.org/licenses/gpl-2.0.html
*/

defined('_JEXEC') or die('Restricted access');
?>
<?php if ($this->show_ticket_info && $this->is_staff) { ?>
<table class="adminlist">
	<thead>
	<tr>
		<th colspan="2"><?php echo JText::_('RST_SUBMITTER_INFORMATION'); ?></th>
	</tr>
	</thead>
	<tr>
		<td><u><?php echo JText::_('RST_TICKET_USER_AGENT'); ?></u><br />
		<?php echo $this->escape($this->row->agent); ?></td>
	</tr>
	<tr>
		<td><u><?php echo JText::_('RST_TICKET_REFERER'); ?></u><br />
		<?php echo $this->escape($this->row->referer); ?></td>
	</tr>
	<tr>
		<td><u><?php echo JText::_('RST_TICKET_IP'); ?></u><br />
		<?php echo $this->escape($this->row->ip); ?></td>
	</tr>
	<tr>
		<td><u><?php echo JText::_('RST_TICKET_LOGGED'); ?></u><br />
		<?php echo $this->row->logged ? JText::_('Yes') : JText::_('No'); ?></td>
	</tr>
</table>
<?php } ?>