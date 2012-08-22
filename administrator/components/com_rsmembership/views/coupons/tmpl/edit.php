<?php
/**
* @version 1.0.0
* @package RSMembership! 1.0.0
* @copyright (C) 2009-2010 www.rsjoomla.com
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
		alert('<?php echo JText::_('RSM_COUPON_NAME_ERROR', true); ?>');
	else if (form.discount_price.value.length == 0)
		alert('<?php echo JText::_('RSM_COUPON_DISCOUNT_ERROR', true); ?>');
	else
		submitform(pressbutton);
}

function rsm_random_code()
{  
	var outputString = '';
	i = 0;
	while(i<5)
	{
		outputString += String.fromCharCode(65 + Math.round(Math.random() * 25));
		outputString += Math.floor(Math.random()*11);
		i++;
	}
	
	var form = document.adminForm;
	form.name.value = outputString;
}
</script>

<form action="<?php echo JRoute::_('index.php?option=com_rsmembership&controller=coupons&task=edit'); ?>" method="post" name="adminForm" id="adminForm">
	<table cellspacing="0" cellpadding="0" border="0" width="100%" class="adminform">
		<tr>
			<td width="200"><span class="hasTip" title="<?php echo JText::_('RSM_COUPON_CODE_DESC'); ?>"><label for="name"><?php echo JText::_('RSM_COUPON_CODE'); ?></label></span></td>
			<td><input type="text" name="name" value="<?php echo $this->escape($this->row->name); ?>" id="name" size="40" maxlength="255" /> <a href="javascript:void(0);" onclick="rsm_random_code()"><?php echo JText::_('RSM_GENERATE_CUPON'); ?></a></td>
		</tr>
		<tr>
			<td width="200"><span class="hasTip" title="<?php echo JText::_('RSM_DISCOUNT_DESC'); ?>"><label for="discount_price"><?php echo JText::_('RSM_DISCOUNT'); ?></label></span></td>
			<td><input type="text" name="discount_price" value="<?php echo $this->escape($this->row->discount_price); ?>" id="discount_price" size="20" maxlength="255" /></td>
		</tr>
		<tr>
			<td width="200"><span class="hasTip" title="<?php echo JText::_('RSM_DISCOUNT_TYPE_DESC'); ?>"><label for="discount_type"><?php echo JText::_('RSM_DISCOUNT_TYPE'); ?></label></span></td>
			<td><?php echo $this->lists['discount_type']; ?></td>
		</tr>
		<tr>
			<td width="200"><span class="hasTip" title="<?php echo JText::_('RSM_MAX_USES_DESC'); ?>"><label for="max_uses"><?php echo JText::_('RSM_MAX_USES'); ?></label></span></td>
			<td><input type="text" name="max_uses" value="<?php echo $this->escape($this->row->max_uses); ?>" id="max_uses" size="20" maxlength="255" /></td>
		</tr>
		<tr>
			<td width="200"><span class="hasTip" title="<?php echo JText::_('RSM_FROM_DESC'); ?>"><label for="date_start"><?php echo JText::_('RSM_FROM'); ?></label></span></td>
			<td><?php echo $this->calendars['date_start']; ?></td>
		</tr>
		<tr>
			<td width="200"><span class="hasTip" title="<?php echo JText::_('RSM_TO_DESC'); ?>"><label for="date_end"><?php echo JText::_('RSM_TO'); ?></label></span></td>
			<td><?php echo $this->calendars['date_end']; ?></td>
		</tr>
		<tr>
			<td width="200"><span class="hasTip" title="<?php echo JText::_('RSM_COUPON_APPLIES_FOR_DESC'); ?>"><label for="items"><?php echo JText::_('RSM_COUPON_APPLIES_FOR'); ?></label></span></td>
			<td><?php echo $this->lists['items']; ?></td>
		</tr>
		<tr>
			<td width="200"><span class="hasTip" title="<?php echo JText::_('PUBLISHED_DESC'); ?>"><label for="published"><?php echo JText::_('PUBLISHED'); ?></label></span></td>
			<td><?php echo $this->lists['published']; ?></td>
		</tr>
	</table>

<?php echo JHTML::_('form.token'); ?>
<input type="hidden" name="option" value="com_rsmembership" />
<input type="hidden" name="controller" value="coupons" />
<input type="hidden" name="task" value="edit" />
<input type="hidden" name="view" value="coupons" />

<input type="hidden" name="id" value="<?php echo $this->row->id; ?>" />
</form>

<?php
//keep session alive while editing
JHTML::_('behavior.keepalive');
?>