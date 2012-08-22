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
		alert('<?php echo JText::_('RST_SEARCH_NAME_ERROR', true); ?>');
	else
		submitform(pressbutton);
}

<?php if (RSTicketsProHelper::isJ16()) { ?>
	Joomla.submitbutton = submitbutton;
<?php } ?>
</script>

<form action="<?php echo JRoute::_('index.php?option=com_rsticketspro&view=searches&controller=searches&task=edit'); ?>" method="post" name="adminForm" id="adminForm">
<div class="col100">
	<table cellspacing="0" cellpadding="0" border="0" width="100%" class="admintable">
		<tr>
			<td width="300" style="width: 300px;" align="right" class="key"><label for="name"><span class="hasTip" title="<?php echo JText::_('RST_SEARCH_NAME_DESC'); ?>"><?php echo JText::_('RST_SEARCH_NAME'); ?></span></label></td>
			<td><input type="text" name="name" id="name" size="40" value="<?php echo $this->escape($this->row->name); ?>" class="inputbox" /></td>
		</tr>
		<tr>
			<td width="300" style="width: 300px;" align="right" class="key"><label for="default"><span class="hasTip" title="<?php echo JText::_('RST_DEFAULT_SEARCH_DESC'); ?>"><?php echo JText::_('RST_DEFAULT_SEARCH'); ?></span></label></td>
			<td><?php echo $this->lists['default']; ?></td>
		</tr>
		<?php if ($this->row->id) { ?>
		<tr>
			<td width="300" style="width: 300px;" align="right" class="key"><label for="update_search"><span class="hasTip" title="<?php echo JText::_('RST_UPDATE_SEARCH_DESC'); ?>"><?php echo JText::_('RST_UPDATE_SEARCH'); ?></span></label></td>
			<td><input type="checkbox" name="update_search" id="update_search" value="1" /> <label for="update_search"><?php echo JText::_('RST_UPDATE_SEARCH_OK'); ?></label></td>
		</tr>
		<?php } ?>
	</table>
</div>
	
<input type="hidden" name="option" value="com_rsticketspro" />
<input type="hidden" name="controller" value="searches" />
<input type="hidden" name="view" value="searches" />
<input type="hidden" name="task" value="save" />
<input type="hidden" name="id" value="<?php echo $this->row->id; ?>" />
<?php echo JHTML::_('form.token'); ?>
</form>

<script type="text/javascript">
	function validate_search(theform)
	{
		if (theform.name.value.length < 1)
		{
			alert('<?php echo JText::_('RST_SEARCH_NAME_ERROR', true); ?>');
			return false;
		}
		
		return true;
	}
</script>

<?php JHTML::_('behavior.keepalive'); ?>