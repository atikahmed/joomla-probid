<?php
/**
* @version 2.0.0
* @package RSTickets! Pro 2.0.0
* @copyright (C) 2010 www.rsjoomla.com
* @license GPL, http://www.gnu.org/licenses/gpl-2.0.html
*/

defined('_JEXEC') or die('Restricted access');
?>
<form action="<?php echo JRoute::_('index.php?option=com_rsticketspro&view=ticket&cid='.$this->row->id.':'.JFilterOutput::stringURLSafe($this->row->subject)); ?>" method="post" name="timeForm">
<table class="adminlist">
	<thead>
	<tr>
		<th colspan="2"><?php echo JText::_('RST_TIME_SPENT'); ?></th>
	</tr>
	</thead>
	<tr>
		<td><u><?php echo JText::_('RST_TIME_SPENT'); ?></u><br />
		<input type="text" name="time_spent" onkeyup="this.value = this.value.replace(/[^0-9\.]/g, '');" value="<?php echo $this->escape($this->row->time_spent); ?>" class="inputbox" /> <?php echo $this->time_spent_unit; ?></td>
	</tr>
	<tr>
		<td><button type="submit" class="button"><?php echo JText::_('RST_UPDATE'); ?></button></td>
	</tr>
</table>
<input type="hidden" name="option" value="com_rsticketspro" />
<input type="hidden" name="view" value="ticket" />
<input type="hidden" name="task" value="savetickettime" />
</form>