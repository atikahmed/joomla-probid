<?php
/**
* @version 2.0.0
* @package RSTickets! Pro 2.0.0
* @copyright (C) 2010 www.rsjoomla.com
* @license GPL, http://www.gnu.org/licenses/gpl-2.0.html
*/

defined('_JEXEC') or die('Restricted access');
?>
<p><?php echo JHTML::_('rsticketsproicon.history', $this->row, $this->is_staff); ?> <?php echo JHTML::_('rsticketsproicon.viewnotes', $this->row, $this->is_staff, $this->permissions); ?> <?php echo JHTML::_('rsticketsproicon.printticket', $this->row); ?> <?php echo JHTML::_('rsticketsproicon.closereopenticket', $this->row, $this->is_staff, $this->permissions); ?></p>
<table class="adminlist">
<?php if ($this->show_ticket_voting && $this->row->status_id == 2) { ?>
		<tr>
		<td colspan="2"><p id="rst_feedback_message"><?php echo JText::_($this->row->feedback ? 'RST_TICKET_FEEDBACK_SENT' : 'RST_TICKET_FEEDBACK'); ?></p>
		<ul class="rst_star_rating">
			<li id="rst_current_rating" class="rst_feedback_selected_<?php echo $this->row->feedback; ?>">&nbsp;</li>
			<?php if ($this->row->feedback == 0 && !$this->is_staff) { ?>
			<li class="hasTip" title="<?php echo JText::_('RST_FEEDBACK_1'); ?>"><a href="javascript: void(0);" onclick="rst_feedback(1, <?php echo $this->row->id; ?>);" class="rst_one_star" id="rst_feedback_1">&nbsp;</a></li>
			<li class="hasTip" title="<?php echo JText::_('RST_FEEDBACK_2'); ?>"><a href="javascript: void(0);" onclick="rst_feedback(2, <?php echo $this->row->id; ?>);" class="rst_two_stars" id="rst_feedback_2">&nbsp;</a></li>
			<li class="hasTip" title="<?php echo JText::_('RST_FEEDBACK_3'); ?>"><a href="javascript: void(0);" onclick="rst_feedback(3, <?php echo $this->row->id; ?>);" class="rst_three_stars" id="rst_feedback_3">&nbsp;</a></li>
			<li class="hasTip" title="<?php echo JText::_('RST_FEEDBACK_4'); ?>"><a href="javascript: void(0);" onclick="rst_feedback(4, <?php echo $this->row->id; ?>);" class="rst_four_stars" id="rst_feedback_4">&nbsp;</a></li>
			<li class="hasTip" title="<?php echo JText::_('RST_FEEDBACK_5'); ?>"><a href="javascript: void(0);" onclick="rst_feedback(5, <?php echo $this->row->id; ?>);" class="rst_five_stars" id="rst_feedback_5">&nbsp;</a></li>
			<?php } ?>
		</ul>
		</td>
		</tr>
<?php } ?>
<?php $k = 1; ?>
<?php foreach ($this->row->messages as $message) {
		$avatar = RSTicketsProHelper::getAvatar($message->user_id);
		if (!$this->use_editor)
			$message->message = nl2br($message->message);
		$message->user = $this->show_email_link ? '<a href="mailto:'.$this->escape($message->email).'">'.$this->escape($message->user).'</a>' : $this->escape($message->user);
		?>
		<thead>
		<tr>
			<th align="left" valign="top"><div align="left"><?php echo $avatar; ?> <?php echo JText::sprintf('RST_TICKET_WROTE', '<strong>'.$message->user.'</strong>'); ?></div></th>
			<th align="right"><div align="right"><?php echo $this->escape(date($this->date_format, RSTicketsProHelper::getCurrentDate($message->date))); ?></div></th>
		</tr>
		</thead>
		<tr>
			<td colspan="2">
				<span class="rst_buttons">
				<?php
					echo JHTML::_('rsticketsproicon.editmessage', $message, $this->is_staff, $this->permissions);
					echo JHTML::_('rsticketsproicon.deletemessage', $message, $this->is_staff, $this->permissions);
				?>
				</span>
					<div id="rst_ticket_message_<?php echo $message->id; ?>"><?php echo $message->message; ?></div>
			</td>
		</tr>
		<?php if (!empty($this->row->files[$message->id])) foreach ($this->row->files[$message->id] as $file) { ?>
		<tr>
			<td colspan="2"><?php echo JHTML::image('components/com_rsticketspro/assets/images/attach.png', JText::_('RST_THIS_TICKET_HAS_ATTACHMENTS')); ?> <a href="<?php echo JRoute::_('index.php?option=com_rsticketspro&task=download&cid='.$this->row->id.'&file_id='.$file->id); ?>"><?php echo $this->escape($file->filename); ?></a> <small><?php echo JText::sprintf('RST_TICKET_FILE_DOWNLOADS', $file->downloads); ?></small></td>
		</tr>
		<?php } ?>
		<?php $k = $k == 1 ? 2 : 1; ?>
<?php } ?>
</table>