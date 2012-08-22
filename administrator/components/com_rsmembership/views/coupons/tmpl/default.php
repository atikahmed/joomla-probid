<?php
/**
* @version 1.0.0
* @package RSMembership! 1.0.0
* @copyright (C) 2009-2010 www.rsjoomla.com
* @license GPL, http://www.gnu.org/licenses/gpl-2.0.html
*/

defined('_JEXEC') or die('Restricted access');
?>

<form action="<?php echo JRoute::_('index.php?option=com_rsmembership&view=coupons'); ?>" method="post" name="adminForm" id="adminForm">
	<table class="adminform">
		<tr>
			<td width="100%">
			<?php echo JText::_( 'SEARCH' ); ?>
			<input type="text" name="search" id="search" value="<?php echo $this->filter_word; ?>" class="text_area" onChange="document.adminForm.submit();" />
			<button onclick="this.form.submit();"><?php echo JText::_( 'Go' ); ?></button>
			<button onclick="this.form.getElementById('search').value='';this.form.submit();"><?php echo JText::_( 'Reset' ); ?></button>
			</td>
			<td nowrap="nowrap"><?php echo $this->lists['state']; ?></td>
		</tr>
	</table>
	<div id="editcell1">
		<table class="adminlist">
			<thead>
			<tr>
				<th width="5"><?php echo JText::_( '#' ); ?></th>
				<th width="20"><input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count($this->coupons); ?>);"/></th>
				<th width="120" nowrap="nowrap"><?php echo JHTML::_('grid.sort', 'RSM_DATE_ADDED', 'date_added', $this->sortOrder, $this->sortColumn); ?></th>
				<th><?php echo JHTML::_('grid.sort', 'RSM_COUPON_CODE', 'name', $this->sortOrder, $this->sortColumn); ?></th>
				<th width="120" nowrap="nowrap"><?php echo JHTML::_('grid.sort', 'RSM_FROM', 'date_start', $this->sortOrder, $this->sortColumn); ?></th>
				<th width="120" nowrap="nowrap"><?php echo JHTML::_('grid.sort', 'RSM_TO', 'date_end', $this->sortOrder, $this->sortColumn); ?></th>
				<th width="120" nowrap="nowrap"><?php echo JHTML::_('grid.sort', 'RSM_DISCOUNT', 'discount_type, discount_price', $this->sortOrder, $this->sortColumn); ?></th>
				<th width="80"><?php echo JText::_('Published'); ?></th>
			</tr>
			</thead>
	<?php
	$k = 0;
	$i = 0;
	$n = count($this->coupons);
	foreach ($this->coupons as $row)
	{
	?>
		<tr class="row<?php echo $k; ?>">
			<td><?php echo $this->pagination->getRowOffset($i); ?></td>
			<td><?php echo JHTML::_('grid.id', $i, $row->id); ?></td>
			<td nowrap="nowrap"><?php echo date(RSMembershipHelper::getConfig('date_format'), RSMembershipHelper::getCurrentDate($row->date_added)); ?></td>
			<td><a href="<?php echo JRoute::_('index.php?option=com_rsmembership&controller=coupons&task=edit&cid='.$row->id); ?>"><?php echo $row->name != '' ? $row->name : JText::_('RSM_NO_TITLE'); ?></a></td>
			<td nowrap="nowrap"><?php echo $row->date_start ? date(RSMembershipHelper::getConfig('date_format'), RSMembershipHelper::getCurrentDate($row->date_start)) : '-'; ?></td>
			<td nowrap="nowrap"><?php echo $row->date_end ? date(RSMembershipHelper::getConfig('date_format'), RSMembershipHelper::getCurrentDate($row->date_end)) : '-'; ?></td>
			<td><?php echo $row->discount_type ? RSMembershipHelper::getPriceFormat($row->discount_price) : $row->discount_price.'%'; ?></td>
			<td align="center"><?php echo JHTML::_('grid.published', $row, $i); ?></td>
		</tr>
	<?php
		$i++;
		$k=1-$k;
	}
	?>
		<tfoot>
			<tr>
				<td colspan="9"><?php echo $this->pagination->getListFooter(); ?></td>
			</tr>
		</tfoot>
		</table>
	</div>
	
	<?php echo JHTML::_( 'form.token' ); ?>
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="option" value="com_rsmembership" />
	<input type="hidden" name="view" value="coupons" />
	<input type="hidden" name="controller" value="coupons" />
	<input type="hidden" name="task" value="" />
	
	<input type="hidden" name="filter_order" value="<?php echo $this->sortColumn; ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $this->sortOrder; ?>" />
</form>