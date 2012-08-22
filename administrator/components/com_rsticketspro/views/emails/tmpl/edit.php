<?php
/**
* @version 2.0.0
* @package RSTickets! Pro 2.0.0
* @copyright (C) 2010 www.rsjoomla.com
* @license GPL, http://www.gnu.org/licenses/gpl-2.0.html
*/

defined('_JEXEC') or die('Restricted access');

JHTML::_('behavior.tooltip');
JHTML::_('behavior.modal');
?>

<script type="text/javascript">
function submitbutton(pressbutton)
{
	var form = document.adminForm;
	
	if (pressbutton == 'cancel')
	{
		submitform(pressbutton);
		return;
	}

	submitform(pressbutton);
}

<?php if (RSTicketsProHelper::isJ16()) { ?>
	Joomla.submitbutton = submitbutton;
<?php } ?>
</script>

<form action="<?php echo JRoute::_('index.php?option=com_rsticketspro&controller=emails&type='.$this->row->type.'&language='.$this->row->lang); ?>" method="post" name="adminForm" id="adminForm">
<div class="col100">
	<table cellspacing="0" cellpadding="0" border="0" width="100%" class="admintable">
		<tr>
			<td width="300" style="width: 300px;" align="right" class="key"><span class="hasTip" title="<?php echo JText::_('RST_EMAIL_LANGUAGE_DESC'); ?>"><?php echo JText::_('RST_EMAIL_LANGUAGE'); ?></span></td>
			<td><?php echo @$this->languages[$this->row->lang]['name']; ?></td>
		</tr>
		<tr>
			<td width="300" style="width: 300px;" align="right" class="key"><span class="hasTip" title="<?php echo JText::_('RST_EMAIL_TYPE_DESC'); ?>"><?php echo JText::_('RST_EMAIL_TYPE'); ?></span></td>
			<td><?php echo JText::_(strtoupper('RST_'.$this->row->type)); ?></td>
		</tr>
		<tr>
			<td width="300" style="width: 300px;" align="right" class="key"><span class="hasTip" title="<?php echo JText::_('RST_EMAIL_SUBJECT_DESC'); ?>"><label for="subject"><?php echo JText::_('RST_EMAIL_SUBJECT'); ?></label></span></td>
			<td>
			<?php if (in_array($this->row->type, array('add_ticket_reply_customer', 'add_ticket_reply_staff', 'add_ticket_customer', 'add_ticket_staff', 'add_ticket_notify'))) { ?>
				<?php echo JText::_('RST_EMAIL_SUBJECT_NO_EDIT'); ?>
			<?php } else { ?>
				<input type="text" name="subject" value="<?php echo $this->escape($this->row->subject); ?>" id="subject" size="120" maxlength="255" />
			<?php } ?>
			</td>
		</tr>
		<tr>
			<td width="300" style="width: 300px;" align="right" class="key" valign="top"><span class="hasTip" title="<?php echo JText::_('RST_EMAIL_MESSAGE_DESC'); ?>"><label for="message"><?php echo JText::_('RST_EMAIL_MESSAGE'); ?></label></span></td>
			<td><?php echo $this->editor->display('message',$this->row->message,500,250,70,10); ?></td>
		</tr>
	</table>
</div>
	
<?php echo JHTML::_('form.token'); ?>
<input type="hidden" name="option" value="com_rsticketspro" />
<input type="hidden" name="controller" value="emails" />
<input type="hidden" name="task" value="edit" />
<input type="hidden" name="view" value="emails" />

<input type="hidden" name="type" value="<?php echo $this->row->type; ?>" />
<input type="hidden" name="language" value="<?php echo $this->row->lang; ?>" />
</form>

<?php
//keep session alive while editing
JHTML::_('behavior.keepalive');
?>