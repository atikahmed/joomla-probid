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

<?php if ($this->permissions->add_note) { ?>
<form action="<?php echo RSTicketsProHelper::route('index.php?option=com_rsticketspro&view=notes'); ?>" method="post" name="adminForm">
	<p><button type="button" onclick="rst_show_ticket_note(this);" class="button"><?php echo JText::_('RST_TICKET_ADD_NOTE'); ?></button></p>
	<div id="rst_ticket_note" style="display: none;">
		<table width="100%" border="0" cellspacing="0" cellpadding="3">
			<tr>
				<td><label for="reply_message"><span class="hasTip" title="<?php echo JText::_('RST_TICKET_NOTE_DESC'); ?>"><?php echo JText::_('RST_TICKET_NOTE'); ?></span></label></td>
				<td>
					<textarea cols="80" rows="10" class="text_area" type="text" name="text" id="text"></textarea>
				</td>
			</tr>
			<tr>
				<td>&nbsp;</td>
				<td><button type="submit" class="button"><?php echo JText::_('RST_TICKET_SUBMIT'); ?></button></td>
			</tr>
		</table>
	</div>
	
	<input type="hidden" name="option" value="com_rsticketspro" />
	<input type="hidden" name="view" value="notes" />
	<input type="hidden" name="task" value="save" />
	<input type="hidden" name="controller" value="notes" />
	<input type="hidden" name="tmpl" value="component" />
	
	<input type="hidden" name="ticket_id" value="<?php echo $this->ticket_id; ?>" />
	<?php echo JHTML::_( 'form.token' ); ?>
</form>
<?php } ?>
	<table width="100%" border="0" cellspacing="0" cellpadding="0">
		<?php
		$k = 1;
		$i = 0;
		$n = count($this->notes);
		foreach ($this->notes as $note)
		{
			$avatar = RSTicketsProHelper::getAvatar($note->user_id);
		?>
			<tr>
				<td class="sectiontableheader" valign="bottom"><div align="left"><?php echo $avatar; ?> <?php echo JText::sprintf('RST_TICKET_WROTE', '<strong>'.$this->escape($note->user).'</strong>'); ?></div></td>
				<td class="sectiontableheader" valign="bottom" width="15%" nowrap="nowrap"><div align="right"><?php echo $this->escape(date($this->date_format, RSTicketsProHelper::getCurrentDate($note->date))); ?></div></td>
			</tr>
			<tr class="sectiontableentry<?php echo $k; ?>">
				<td colspan="2">
					<span class="rst_buttons">
					<?php
						echo JHTML::_('rsticketsproicon.editnote', $note, $this->permissions);
						echo JHTML::_('rsticketsproicon.deletenote', $note, $this->permissions);
					?>
					</span>
						<div id="rst_ticket_note_<?php echo $note->id; ?>"><?php echo nl2br($this->escape($note->text)); ?></div>
				</td>
			</tr>
		<?php
			$k = $k == 1 ? 2 : 1;
			$i++;
		}
		?>
	<tr>
		<td colspan="5">&nbsp;</td>
	</tr>
	<tr>
		<td align="center" colspan="4" class="sectiontablefooter">
		<div class="pagination"><?php echo $this->pagination->getPagesLinks(); ?></div>
		</td>
	</tr>
	<tr>
		<td colspan="5" align="right"><?php echo $this->pagination->getPagesCounter(); ?></td>
	</tr>
	</table>

<script type="text/javascript">
function rst_show_ticket_note(what)
{
	what.style.display = 'none';
	$('rst_ticket_note').style.display = '';
}
</script>

<?php JHTML::_('behavior.keepalive'); ?>