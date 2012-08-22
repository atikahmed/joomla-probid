<?php
/**
* @version 2.0.0
* @package RSTickets! Pro 2.0.0
* @copyright (C) 2010 www.rsjoomla.com
* @license GPL, http://www.gnu.org/licenses/gpl-2.0.html
*/

defined('_JEXEC') or die('Restricted access');
?>
<form action="<?php echo JRoute::_('index.php?option=com_rsticketspro&view=ticket&cid='.$this->row->id.':'.JFilterOutput::stringURLSafe($this->row->subject)); ?>" method="post" name="customForm">
	<table class="adminlist">
	<thead>
	<tr>
		<th colspan="2"><?php echo JText::_('RST_TICKET_CUSTOM_FIELDS'); ?></th>
	</tr>
	</thead>
	<?php foreach ($this->row->custom_fields as $field) { ?>
	<tr>
		<td><u><?php echo $field[0]; ?></u><br />
		<?php echo $field[1]; ?></td>
	</tr>
	<?php } ?>
	<?php if ($this->can_update_custom_fields) { ?>
		<tr>
			<td><button type="submit" class="button"><?php echo JText::_('RST_UPDATE'); ?></button></td>
		</tr>
	<?php } ?>
	</table>
	
	<input type="hidden" name="option" value="com_rsticketspro" />
	<input type="hidden" name="view" value="ticket" />
	<input type="hidden" name="task" value="savecustomfields" />
</form>