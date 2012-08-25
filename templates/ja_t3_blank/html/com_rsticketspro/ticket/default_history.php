<?php
/**
* @version 2.0.0
* @package RSTickets! Pro 2.0.0
* @copyright (C) 2010 www.rsjoomla.com
* @license GPL, http://www.gnu.org/licenses/gpl-2.0.html
*/

defined('_JEXEC') or die('Restricted access');
?>

<span class="rsticketspro_clear"></span>
<p class="rsticketspro_title rsticketspro_clickable"><?php echo JText::_('RST_TICKET_HISTORY'); ?></p>
<div class="rsticketspro_content">
<?php if ($this->history_tickets) { ?>
<?php foreach ($this->history_tickets as $ticket){ ?>
	<div class="rsticketspro_message_history">
		<p class="rsticketspro_title3">
		[<?php echo $this->escape($ticket->code); ?>] <a href="<?php echo RSTicketsProHelper::route('index.php?option=com_rsticketspro&view=ticket&cid='.$ticket->id.':'.JFilterOutput::stringURLSafe($ticket->subject)); ?>" title="<?php echo $this->escape($ticket->subject); ?>"><?php echo $this->escape($ticket->subject); ?></a>
		<small class="rsticketspro_history_right">
		<?php echo JHTML::image('components/com_rsticketspro/assets/images/icon12.png', JText::_('RST_TICKET_REPLIES')); ?> <?php echo JText::_($ticket->status_name); ?>
		<?php echo JHTML::image('components/com_rsticketspro/assets/images/replies.png', JText::_('RST_TICKET_REPLIES')); ?> <?php echo $ticket->replies.' '.JText::_('RST_TICKET_REPLIES'); ?>
		<?php echo JHTML::image('components/com_rsticketspro/assets/images/icon11.png', ''); ?> <?php echo $this->escape(date($this->date_format, RSTicketsProHelper::getCurrentDate($ticket->date))); ?>
		</small>
		</p>
   </div><!-- message -->
<?php } ?>
<?php } else { ?>
	<p><?php echo JText::_('RST_NO_TICKET_HISTORY'); ?></p>
<?php } ?>
</div>