<?php
/**
* @version 2.0.0
* @package RSTickets! Pro 2.0.0
* @copyright (C) 2010 www.rsjoomla.com
* @license GPL, http://www.gnu.org/licenses/gpl-2.0.html
*/

defined('_JEXEC') or die('Restricted access');
?>
<p class="rsticketspro_title rsticketspro_clickable"><?php echo JText::_('RST_TIME_SPENT'); ?></p>
<div class="rsticketspro_content">
	<form class="rsticketspro_form" action="<?php echo RSTicketsProHelper::route('index.php?option=com_rsticketspro&view=ticket&cid='.$this->row->id.':'.JFilterOutput::stringURLSafe($this->row->subject)); ?>" method="post" name="infoForm">
		<?php if ($this->is_staff) { ?>
		<p><label class="float_left"><?php echo JText::_('RST_TIME_SPENT'); ?></label>
		<input type="text" name="time_spent" onkeyup="this.value = this.value.replace(/[^0-9\.]/g, '');" value="<?php echo $this->escape($this->row->time_spent); ?>" class="inputbox" /> <?php echo $this->time_spent_unit; ?>
		<?php } else { ?>
		<p><?php echo $this->escape($this->row->time_spent); ?> <?php echo $this->time_spent_unit; ?></p>
		<?php } ?>
		<?php if ($this->is_staff && !$this->do_print) { ?>
		<p>
			<button type="submit" class="button"><?php echo JText::_('RST_UPDATE'); ?></button>
		</p>
		<?php } ?>
		<input type="hidden" name="option" value="com_rsticketspro" />
		<input type="hidden" name="view" value="ticket" />
		<input type="hidden" name="task" value="savetickettime" />
	</form>
</div>