<?php
/**
* @version 2.0.0
* @package RSTickets! Pro 2.0.0
* @copyright (C) 2010 www.rsjoomla.com
* @license GPL, http://www.gnu.org/licenses/gpl-2.0.html
*/

defined('_JEXEC') or die('Restricted access');
?>

<?php JError::raiseNotice(500, JText::_('RST_CUSTOM_FIELD_TRANSLATE')); ?>

<form action="<?php echo JRoute::_('index.php?option=com_rsticketspro&view=customfields'); ?>" method="post" name="adminForm" id="adminForm">
	<table class="adminform">
		<tr>
			<td width="100%">
			<?php echo JText::_( 'SEARCH' ); ?>
			<input type="text" name="search" id="search" value="<?php echo $this->filter_word; ?>" class="text_area" onchange="document.adminForm.submit();" />
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
				<th width="20"><input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count($this->customfields); ?>);"/></th>
				<th><?php echo JHTML::_('grid.sort', 'RST_CUSTOM_FIELD', 'name', $this->sortOrder, $this->sortColumn); ?></th>
				<th><?php echo JHTML::_('grid.sort', 'RST_LABEL', 'label', $this->sortOrder, $this->sortColumn); ?></th>
				<th><?php echo JText::_('RST_TYPE'); ?></th>
				<th width="80"><?php echo JText::_('Published'); ?></th>
				<th width="100"><?php echo JText::_('Ordering'); ?>
				<?php echo JHTML::_('grid.order',$this->customfields); ?>
				</th>
			</tr>
			</thead>
	<?php
	$k = 0;
	$i = 0;
	$n = count($this->customfields);
	foreach ($this->customfields as $row)
	{
	?>
		<tr class="row<?php echo $k; ?>">
			<td><?php echo $this->pagination->getRowOffset($i); ?></td>
			<td><?php echo JHTML::_('grid.id', $i, $row->id); ?></td>
			<td><a href="<?php echo JRoute::_('index.php?option=com_rsticketspro&controller=customfields&task=edit&cid='.$row->id); ?>"><?php echo $row->name != '' ? $this->escape($row->name) : JText::_('RST_NO_TITLE'); ?></a></td>
			<td><a href="<?php echo JRoute::_('index.php?option=com_rsticketspro&controller=customfields&task=edit&cid='.$row->id); ?>"><?php echo $row->label != '' ? $this->escape(JText::_($row->label)) : JText::_('RST_NO_TITLE'); ?></a></td>
			<td><?php echo JText::_('RST_'.strtoupper($row->type)); ?></td>
			<td align="center"><?php echo JHTML::_('grid.published', $row, $i); ?></td>
			<td class="order">
			<span><?php echo $this->pagination->orderUpIcon( $i, true, 'orderup', 'Move Up', 'ordering'); ?></span>
			<span><?php echo $this->pagination->orderDownIcon( $i, $n, true, 'orderdown', 'Move Down', 'ordering' ); ?></span>
			<input type="text" name="order[]" size="5" value="<?php echo $row->ordering; ?>" class="text_area" style="text-align:center" />
			</td>
		</tr>
	<?php
		$i++;
		$k=1-$k;
	}
	?>
		<tfoot>
			<tr>
				<td colspan="7"><?php echo $this->pagination->getListFooter(); ?></td>
			</tr>
		</tfoot>
		</table>
	</div>
	
	<?php echo JHTML::_( 'form.token' ); ?>
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="option" value="com_rsticketspro" />
	<input type="hidden" name="view" value="customfields" />
	<input type="hidden" name="controller" value="customfields" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="department_id" value="<?php echo $this->department_id; ?>" />
	
	<input type="hidden" name="filter_order" value="<?php echo $this->escape($this->sortColumn); ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $this->escape($this->sortOrder); ?>" />
</form>