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

	// do field validation
	if (form.name.value.length == 0)
		alert('<?php echo JText::_('RST_STATUS_NAME_ERROR', true); ?>');
	else
		submitform(pressbutton);
}

<?php if (RSTicketsProHelper::isJ16()) { ?>
	Joomla.submitbutton = submitbutton;
<?php } ?>
</script>

<form action="<?php echo JRoute::_('index.php?option=com_rsticketspro&controller=statuses&task=edit'); ?>" method="post" name="adminForm" id="adminForm">
<div class="col100">
	<table cellspacing="0" cellpadding="0" border="0" width="100%" class="admintable">
		<tr>
			<td width="300" style="width: 300px;" align="right" class="key"><span class="hasTip" title="<?php echo JText::_('RST_STATUS_DESC'); ?>"><label for="name"><?php echo JText::_('RST_STATUS'); ?></label></span></td>
			<td><input type="text" name="name" value="<?php echo $this->escape($this->row->name); ?>" id="name" size="120" maxlength="255" /></td>
		</tr>
		<tr>
			<td width="300" style="width: 300px;" align="right" class="key"><span class="hasTip" title="<?php echo JText::_('PUBLISHED_DESC'); ?>"><label for="published"><?php echo JText::_('PUBLISHED'); ?></label></span></td>
			<td><?php echo ($this->row->id == 0 || $this->row->id > 3) ? $this->lists['published'] : JText::_('RST_ALWAYS_PUBLISHED'); ?></td>
		</tr>
	</table>
</div>
	
<?php echo JHTML::_('form.token'); ?>
<input type="hidden" name="option" value="com_rsticketspro" />
<input type="hidden" name="controller" value="statuses" />
<input type="hidden" name="task" value="edit" />
<input type="hidden" name="view" value="statuses" />

<input type="hidden" name="id" value="<?php echo $this->row->id; ?>" />
</form>

<?php
//keep session alive while editing
JHTML::_('behavior.keepalive');
?>