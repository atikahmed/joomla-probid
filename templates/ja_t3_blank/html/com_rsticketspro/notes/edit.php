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

<form action="<?php echo RSTicketsProHelper::route('index.php?option=com_rsticketspro&view=notes'); ?>" method="post" name="adminForm">
	<table width="100%" border="0" cellspacing="0" cellpadding="3">
		<tr>
			<td><label for="reply_message"><span class="hasTip" title="<?php echo JText::_('RST_TICKET_NOTE_DESC'); ?>"><?php echo JText::_('RST_TICKET_NOTE'); ?></span></label></td>
			<td>
				<textarea cols="80" rows="10" class="text_area" type="text" name="text" id="text"><?php echo $this->escape($this->row->text); ?></textarea>
			</td>
		</tr>
		<tr>
			<td>&nbsp;</td>
			<td><button type="submit" class="button"><?php echo JText::_('RST_UPDATE'); ?></button> <button type="button" onclick="document.location='<?php echo RSTicketsProHelper::route('index.php?option=com_rsticketspro&view=notes&ticket_id='.$this->row->ticket_id.'&tmpl=component'); ?>'" class="button"><?php echo JText::_('RST_BACK'); ?></button></td>
		</tr>
	</table>
	
	<input type="hidden" name="option" value="com_rsticketspro" />
	<input type="hidden" name="view" value="notes" />
	<input type="hidden" name="task" value="update" />
	<input type="hidden" name="controller" value="notes" />
	<input type="hidden" name="tmpl" value="component" />
	
	<input type="hidden" name="ticket_id" value="<?php echo $this->row->ticket_id; ?>" />
	<input type="hidden" name="cid" value="<?php echo $this->row->id; ?>" />
	<?php echo JHTML::_( 'form.token' ); ?>
</form>