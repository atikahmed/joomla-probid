<?php
/**
* @version 1.0.0
* @package RSMembership! 1.0.0
* @copyright (C) 2009-2010 www.rsjoomla.com
* @license GPL, http://www.gnu.org/licenses/gpl-2.0.html
*/
defined('_JEXEC') or die('Restricted access');
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
		alert('<?php echo JText::_('RSM_PAYMENT_NAME_ERROR', true); ?>');
	else
		submitform(pressbutton);
}

<?php if (RSMembershipHelper::isJ16()) { ?>
	Joomla.submitbutton = submitbutton;
<?php } ?>
</script>

<form action="<?php echo JRoute::_('index.php?option=com_rsmembership&view=payments&task=edit'); ?>" method="post" name="adminForm" id="adminForm">
	<table class="adminform">
		<tr>
			<td width="8%"><?php echo JText::_('RSM_WIRE_NAME') ?></td>
			<td><input type="text" name="name" size="80" value="<?php echo $this->payment->name;?>" /></td>
		</tr>

		<tr>
			<td><?php echo JText::_('RSM_WIRE_DETAILS') ?></td>
			<td><?php echo $this->editor->display('details', $this->payment->details,500,250,70,10); ?></td>
		</tr>
		<tr>
			<td><?php echo JText::_('RSM_WIRE_TYPE'); ?></td>
			<td><?php echo $this->lists['tax_type'] ?></td>
		</tr>
		<tr>
			<td><?php echo JText::_('RSM_WIRE_VALUE'); ?></td>
			<td><input type="text" value="<?php echo $this->payment->tax_value ;?>" name="tax_value" /></td>
		</tr>
	</table>
	
	<?php echo JHTML::_( 'form.token' ); ?>
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="option" value="com_rsmembership" />
	<input type="hidden" name="view" value="payments" />
	<input type="hidden" name="task" value="edit" />
	<input type="hidden" name="layout" value="edit" />
	<input type="hidden" name="cid" value="<?php echo $this->payment->id; ?>" />
	<input type="hidden" name="id" value="<?php echo $this->payment->id; ?>" />
	<input type="hidden" name="controller" value="payments" />
</form>
