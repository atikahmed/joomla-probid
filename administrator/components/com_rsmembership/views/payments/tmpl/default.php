<?php
/**
* @version 1.0.0
* @package RSMembership! 1.0.0
* @copyright (C) 2009-2010 www.rsjoomla.com
* @license GPL, http://www.gnu.org/licenses/gpl-2.0.html
*/
defined('_JEXEC') or die('Restricted access');
?>
<form action="<?php echo JRoute::_('index.php?option=com_rsmembership&view=payments'); ?>" method="post" name="adminForm" id="adminForm">
	<div id="editcell1">
		<table class="adminlist">
			<thead>
			<tr>
				<th width="5"><?php echo JText::_( '#' ); ?></th>
				<th><?php echo JText::_('RSM_PAYMENT_TYPE'); ?></th>
				<th><?php echo JText::_('RSM_PAYMENT_LIMITATIONS'); ?></th>
				<th width="1"><?php echo JText::_('RSM_CONFIGURE'); ?></th>
				<th width="100"><?php echo JText::_('Ordering'); ?><?php echo JHTML::_('grid.order',$this->wirepayments); ?></th>
			</tr>
			</thead>
	<?php
	$k = 0;
	$i = 0;
	$j = 0;
	$n = count($this->wirepayments);
	foreach ($this->payments as $row)
	{
		$is_wire = isset($row->id);
		if ($is_wire) {
		$link = JRoute::_('index.php?option=com_rsmembership&controller=payments&task=edit&cid='.$row->id);
		?>
		<tr class="row<?php echo $k; ?>">
			<td align="center"><?php echo JHTML::_('grid.id', $j, $row->id); ?></td>
			<td><a href="<?php echo $link; ?>"><?php echo $this->escape($row->name); ?></a></td>
			<td>&nbsp;</td>
			<td align="center"><a href="<?php echo $link; ?>"><?php echo JHTML::image('administrator/components/com_rsmembership/assets/images/config.png', JText::_('RSM_CONFIGURE')); ?></a></td>
			<td class="order">
			<span><?php echo $this->pagination->orderUpIcon( $j, true, 'orderup', 'Move Up', 'ordering'); ?></span>
			<span><?php echo $this->pagination->orderDownIcon( $j, $n, true, 'orderdown', 'Move Down', 'ordering' ); ?></span>
			<input type="text" name="order[]" size="5" value="<?php echo $row->ordering; ?>" class="text_area" style="text-align:center" />
			</td>
		</tr>
		<?php $j++; } else {
		$link = RSMembershipHelper::isJ16() ? JRoute::_('index.php?option=com_rsmembership&task=editplugin&cid='.$row->cid) : JRoute::_('index.php?option=com_plugins&view=plugin&client=site&task=edit&cid[]='.$row->cid);
		?>
		<tr class="row<?php echo $k; ?>">
			<td align="center"><?php echo $i+1; ?></td>
			<td><a href="<?php echo $link; ?>"><?php echo $this->escape($row->name); ?></a></td>
			<td><?php echo $row->limitations; ?></td>
			<td align="center"><a href="<?php echo $link; ?>"><?php echo JHTML::image('administrator/components/com_rsmembership/assets/images/config.png', JText::_('RSM_CONFIGURE')); ?></a></td>
			<td>&nbsp;</td>
		</tr>
	<?php
		}
		$i++;
		$k=1-$k;
	}
	?>
		</table>
	</div>
	
	<?php echo JHTML::_( 'form.token' ); ?>
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="option" value="com_rsmembership" />
	<input type="hidden" name="view" value="payments" />
	<input type="hidden" name="controller" value="payments" />
	<input type="hidden" name="task" value="" />
</form>