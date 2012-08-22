<?php
/**
* @version 2.0.0
* @package RSTickets! Pro 2.0.0
* @copyright (C) 2010 www.rsjoomla.com
* @license GPL, http://www.gnu.org/licenses/gpl-2.0.html
*/

defined('_JEXEC') or die('Restricted access');
?>

<?php if ($this->show_ticket_voting && $this->row->status_id == 2) { ?>
<script type="text/javascript">
	window.addEvent('domready', function() {

		// convert the selectbox with id 'rating'
		window.rsticketspro_rating = new mooRatings($('rsticketspro_rating'), {
			showSelectBox : false,
			container : null,
			defaultRating : <?php echo $this->row->feedback ? $this->row->feedback : 5; ?>,
			onClick: rst_set_feedback
			<?php if ($this->row->feedback || $this->is_staff) { ?>, disabled: true
			<?php } ?>
		});

	});
	
	function rst_set_feedback(value)
	{
		rst_feedback('<?php echo JRoute::_('index.php', false); ?>', value, <?php echo $this->row->id; ?>);
	}
</script>
<?php } ?>

<?php if (!$this->do_print) { ?>
<div id="rsticketspro_ticket_actions"><?php echo JHTML::_('rsticketsproicon.history', $this->row, $this->is_staff); ?> <?php echo JHTML::_('rsticketsproicon.viewnotes', $this->row, $this->is_staff, $this->permissions); ?> <?php echo JHTML::_('rsticketsproicon.printticket', $this->row); ?> <?php echo JHTML::_('rsticketsproicon.closereopenticket', $this->row, $this->is_staff, $this->permissions); ?></div>
<?php } ?>

<span class="rsticketspro_clear"></span>

<?php if ($this->show_ticket_voting && $this->row->status_id == 2) { ?>
	<div id="rsticketspro_ratings">
		<p id="rst_feedback_message"><?php echo JText::_($this->row->feedback ? ($this->is_staff ? 'RST_TICKET_FEEDBACK_SENT_STAFF' : 'RST_TICKET_FEEDBACK_SENT') : 'RST_TICKET_FEEDBACK'); ?></p>
		<select name="rating" id="rsticketspro_rating">
			<option value="1"><?php echo JText::_('RST_FEEDBACK_1'); ?></option>
			<option value="2"><?php echo JText::_('RST_FEEDBACK_2'); ?></option>
			<option value="3"><?php echo JText::_('RST_FEEDBACK_3'); ?></option>
			<option value="4"><?php echo JText::_('RST_FEEDBACK_4'); ?></option>
			<option value="5"><?php echo JText::_('RST_FEEDBACK_5'); ?></option>
		</select>
	</div>
<?php } ?>
<?php foreach ($this->row->messages as $message) {
		$has_files = !empty($this->row->files[$message->id]);
		$avatar = RSTicketsProHelper::getAvatar($message->user_id);
		if (!$this->use_editor)
			$message->message = nl2br($message->message);
		$message->user = $this->show_email_link ? '<a href="mailto:'.$this->escape($message->email).'">'.$this->escape($message->user).'</a>' : $this->escape($message->user);
		?>
		<p class="rsticketspro_title2">
			<?php echo $avatar; ?>
			<small><?php echo JHTML::image('components/com_rsticketspro/assets/images/date.png', ''); ?> <?php echo $this->escape(date($this->date_format, RSTicketsProHelper::getCurrentDate($message->date))); ?></small>
			<?php echo JText::sprintf('RST_TICKET_WROTE', '<strong>'.$message->user.'</strong>'); ?>
		</p>
        <div class="rsticketspro_message <?php echo $has_files ? 'rsticketspro_message_has_files' : ''; ?>">
			<span class="rst_buttons"><?php	echo JHTML::_('rsticketsproicon.editmessage', $message, $this->is_staff, $this->permissions); echo JHTML::_('rsticketsproicon.deletemessage', $message, $this->is_staff, $this->permissions); ?></span>
			<div id="rst_ticket_message_<?php echo $message->id; ?>"><?php echo $message->message; ?></div>
			<?php if ($has_files) { ?>
			<div class="rsticketspro_files">
			<?php foreach ($this->row->files[$message->id] as $file) { ?>
			<div>
				<?php echo JHTML::image('components/com_rsticketspro/assets/images/attach.png', JText::_('RST_THIS_TICKET_HAS_ATTACHMENTS')); ?> <a href="<?php echo RSTicketsProHelper::route('index.php?option=com_rsticketspro&task=download&cid='.$this->row->id.'&file_id='.$file->id); ?>"><?php echo $this->escape($file->filename); ?></a> <small><?php echo JText::sprintf('RST_TICKET_FILE_DOWNLOADS', $file->downloads); ?></small>
			</div>
			<?php } ?>
			</div>
			<?php } ?>
       </div><!-- message -->
<?php } ?>