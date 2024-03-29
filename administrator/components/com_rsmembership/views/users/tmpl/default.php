<?php
/**
* @version 1.0.0
* @package RSMembership! 1.0.0
* @copyright (C) 2009-2010 www.rsjoomla.com
* @license GPL, http://www.gnu.org/licenses/gpl-2.0.html
*/

defined('_JEXEC') or die('Restricted access');
?>

<form action="<?php echo JRoute::_('index.php?option=com_rsmembership&view=users&controller=users'); ?>" method="post" name="adminForm" id="adminForm">
	<table class="adminform">
		<tr>
			<td width="100%">
			  	<?php echo JText::_( 'SEARCH' ); ?>
				<input type="text" name="search" id="search" value="<?php echo $this->filter_word; ?>" class="text_area" onChange="document.adminForm.submit();" />
				<?php echo $this->lists['memberships']; ?>
				<?php echo $this->lists['status']; ?>
				<button type="submit"><?php echo JText::_( 'Go' ); ?></button>
				<button type="button" onclick="submitbutton('resetsearch');"><?php echo JText::_( 'Reset' ); ?></button>
			</td>
			<td nowrap="nowrap">&nbsp;</td>
		</tr>
	</table>
	<div id="editcell1">
		<table class="adminlist">
			<thead>
			<tr>
				<th width="5"><?php echo JText::_( '#' ); ?></th>
				<th width="20"><input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count($this->users); ?>);"/></th>
				<th width="50"><?php echo JHTML::_('grid.sort', 'RSM_USER_ID', 'mu.user_id', $this->sortOrder, $this->sortColumn); ?></th>
				<th width="20"><?php echo JText::_('Enabled'); ?></th>
				<th><?php echo JHTML::_('grid.sort', 'Username', 'u.username', $this->sortOrder, $this->sortColumn); ?></th>
				<th><?php echo JHTML::_('grid.sort', 'Name', 'u.name', $this->sortOrder, $this->sortColumn); ?></th>
				<th><?php echo JHTML::_('grid.sort', 'Email', 'u.email', $this->sortOrder, $this->sortColumn); ?></th>
			</tr>
			</thead>
	<?php
	$k = 0;
	$i = 0;
	$n = count($this->users);
	foreach ($this->users as $row)
	{
	?>
		<tr class="row<?php echo $k; ?>">
			<td><?php echo $this->pagination->getRowOffset($i); ?></td>
			<td><?php echo JHTML::_('grid.id', $i, $row->id); ?></td>
			<td><?php echo $row->id; ?></td>
			<?php if (RSMembershipHelper::isJ16()) { ?>
			<td><?php echo JHTML::_('image.administrator', 'admin/'.($row->block ? 'publish_x.png' : 'tick.png')); ?></td>
			<?php } else { ?>
			<td><?php echo JHTML::_('image', JURI::root().'/administrator/images/'.($row->block ? 'publish_x.png' : 'tick.png'), ''); ?></td>
			<?php } ?>
			<td><a href="<?php echo JRoute::_('index.php?option=com_rsmembership&controller=users&task=edit&cid='.$row->id); ?>"><?php echo $row->username; ?></a></td>
			<td><?php echo $row->name; ?></td>
			<td><a href="<?php echo JRoute::_('index.php?option=com_rsmembership&controller=users&task=edit&cid='.$row->id); ?>"><?php echo $row->email; ?></a></td>
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
	<input type="hidden" name="option" value="com_rsmembership" />
	<input type="hidden" name="view" value="users" />
	<input type="hidden" name="controller" value="users" />
	<input type="hidden" name="task" value="" />
	
	<input type="hidden" name="filter_order" value="<?php echo $this->sortColumn; ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $this->sortOrder; ?>" />
</form>