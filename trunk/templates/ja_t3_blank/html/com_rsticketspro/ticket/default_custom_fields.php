<?php
/**
* @version 2.0.0
* @package RSTickets! Pro 2.0.0
* @copyright (C) 2010 www.rsjoomla.com
* @license GPL, http://www.gnu.org/licenses/gpl-2.0.html
*/

defined('_JEXEC') or die('Restricted access');
?>
	<p class="rsticketspro_title rsticketspro_clickable"><?php echo JText::_('RST_TICKET_CUSTOM_FIELDS'); ?></p>
	<div class="rsticketspro_content">
		<form class="rsticketspro_form" action="<?php echo RSTicketsProHelper::route('index.php?option=com_rsticketspro&view=ticket&cid='.$this->row->id.':'.JFilterOutput::stringURLSafe($this->row->subject)); ?>" method="post" name="customForm">
	<?php foreach ($this->row->custom_fields as $field) { ?>
		<p>
			<label class="float_left"><?php echo $field[0]; ?></label>
			<?php echo $field[1]; ?>
		</p>
	<?php } ?>
	<?php if ($this->can_update_custom_fields && !$this->do_print) { ?>
		<p>
			<button type="submit" class="button"><?php echo JText::_('RST_UPDATE'); ?></button>
		</p>
	<?php } ?>
	
		<input type="hidden" name="option" value="com_rsticketspro" />
		<input type="hidden" name="view" value="ticket" />
		<input type="hidden" name="task" value="savecustomfields" />
		</form>
	</div>