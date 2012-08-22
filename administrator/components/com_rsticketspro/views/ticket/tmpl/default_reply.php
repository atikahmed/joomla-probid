<?php
/**
* @version 2.0.0
* @package RSTickets! Pro 2.0.0
* @copyright (C) 2010 www.rsjoomla.com
* @license GPL, http://www.gnu.org/licenses/gpl-2.0.html
*/

defined('_JEXEC') or die('Restricted access');
?>
<form action="<?php echo JRoute::_('index.php?option=com_rsticketspro&view=ticket&cid='.$this->row->id.':'.JFilterOutput::stringURLSafe($this->row->subject)); ?>" method="post" name="replyForm" enctype="multipart/form-data">
	<?php if ($this->row->status_id != 2) { ?>
		<?php if (empty($this->data['task'])) { ?>
			<?php if (!$this->is_staff || ($this->is_staff && $this->permissions->answer_ticket)) { ?>
			<p><button type="button" onclick="rst_show_ticket_reply(this);" class="button"><?php echo JText::_('RST_TICKET_REPLY'); ?></button></p>
			<?php } ?>
		<?php } ?>
		<span class="rst_clear"></span>
		
		<?php if (!$this->is_staff || ($this->is_staff && $this->permissions->answer_ticket)) { ?>
		<div id="rst_ticket_reply" style="<?php echo empty($this->data['task']) ? 'display: none;' : ''; ?> margin-bottom: 10px;">
			<table width="100%" border="0" cellspacing="0" cellpadding="3">
			<?php if ($this->is_staff && $this->show_kb_search) { ?>
			<tr>
				<td valign="top"><label><?php echo JText::_('RST_KNOWLEDGEBASE'); ?></label></td>
				<td>
					<input type="text" id="rst_search_value" size="30" onkeyup="rst_search(this.value)" />
					<div id="rst_livesearch"></div>
				</td>
			</tr>
			<?php } ?>
			<tr>
				<td><label for="reply_message"><span class="hasTip" title="<?php echo JText::_('RST_TICKET_MESSAGE_DESC'); ?>"><?php echo JText::_('RST_TICKET_MESSAGE'); ?></span></label></td>
				<td>
				<?php if ($this->use_editor) { ?>
					<?php echo $this->editor->display('message', @$this->data['message'],500,250,70,10); ?>
				<?php } else { ?>
					<textarea cols="80" rows="10" class="text_area" type="text" name="message" id="message"><?php echo $this->escape(@$this->data['message']); ?></textarea>
				<?php } ?>
				</td>
			</tr>
			<?php if ($this->can_upload) { ?>
			<tr>
			<tr>
				<td><label for="reply_files"><span class="hasTip" title="<?php echo JText::_('RST_TICKET_ATTACHMENTS_DESC'); ?>"><?php echo JText::_('RST_TICKET_ATTACHMENTS'); ?></span></label></td>
				<td>
					<input type="file" name="rst_files[]" value="" />
					<input type="button" class="button" value="<?php echo JText::_('RST_ADD_MORE_ATTACHMENTS'); ?>" onclick="rst_add_attachments();" />
					<div id="rst_files"></div>
				</td>
			</tr>
			<?php } ?>
			<?php if ($this->is_staff && $this->show_signature) { ?>
			<tr>
				<td>&nbsp;</td>
				<td><input type="checkbox" checked="checked" id="reply_signature" name="use_signature" value="1" /> <label for="reply_signature"><?php echo JText::_('RST_ATTACH_SIGNATURE'); ?></label> <a href="<?php echo JRoute::_('index.php?option=com_rsticketspro&view=signature'); ?>"><?php echo JText::_('RST_EDIT_SIGNATURE'); ?></a></td>
			</tr>
			<?php } ?>
			<tr>
				<td>&nbsp;</td>
				<td><button type="submit" class="button"><?php echo JText::_('RST_TICKET_SUBMIT'); ?></button></td>
			</tr>
			</table>
		</div>
		<?php } ?>
	<?php } else { ?>
		<p><strong><?php echo JText::_('RST_TICKET_REPLIES_CLOSED'); ?></strong></p>
	<?php } ?>

	<?php echo JHTML::_( 'form.token' ); ?>
	<input type="hidden" name="option" value="com_rsticketspro" />
	<input type="hidden" name="view" value="ticket" />
	<input type="hidden" name="task" value="submitreply" />
</form>