<?php
/**
* @version 1.0.0
* @package RSMembership! 1.0.0
* @copyright (C) 2009-2010 www.rsjoomla.com
* @license GPL, http://www.gnu.org/licenses/gpl-2.0.html
*/

defined('_JEXEC') or die('Restricted access');
?>	
<table class="adminlist" id="addmembershipshared">
	<thead>
	<tr>
		<th width="5"><?php echo JText::_( '#' ); ?></th>
		<th width="20">&nbsp;</th>
		<th width="20"><?php echo JText::_('Delete'); ?></th>
		<th width="200"><?php echo JText::_('RSM_TYPE'); ?></th>
		<th><?php echo JText::_('RSM_PARAMS'); ?></th>
		<th width="80"><?php echo JText::_('Published'); ?></th>
		<th width="100"><?php echo JText::_('Ordering'); ?>
		<?php echo JHTML::_('grid.order', $this->row->shared, 'filesave.png', 'folderssaveorder'); ?>
		</th>
	</tr>
	</thead>
	<?php
	$k = 0;
	$i = 0;
	$n = count($this->row->shared);
	foreach ($this->row->shared as $row)
	{
	?>
		<tr class="row<?php echo $k; ?>">
			<td><?php echo $this->row->sharedPagination->getRowOffset($i); ?></td>
			<td><?php $cb = JHTML::_('grid.id', $i, $row->id, false, 'cid_folders'); echo str_replace('cb', 'cbfolders', $cb); ?></td>
			<td align="center">
			<a class="delete-item" onclick="return confirm('<?php echo JText::_('RSM_CONFIRM_DELETE'); ?>')" href="<?php echo JRoute::_('index.php?option=com_rsmembership&controller=memberships&task=foldersremove&cid_folders[]='.$row->id.'&'.JUtility::getToken().'=1&tabposition=3'); ?>"><?php echo JHTML::_('image', 'administrator/components/com_rsmembership/assets/images/remove.png', JText::_('Delete')); ?></a>
			</td>
			<td><?php echo JText::_('RSM_TYPE_'.strtoupper($row->type)); ?></td>
			<td>
			<?php if ($row->type == 'folder') { ?>
			<a href="<?php echo JRoute::_('index.php?option=com_rsmembership&controller=files&task=edit&cid='.$row->params); ?>"><?php echo $row->params; ?></a></td>
			<?php } elseif ($row->type == 'backendurl' || $row->type == 'frontendurl') { ?>
			<a class="modal" rel="{handler: 'iframe', size: {x: 660, y: 475}}" href="<?php echo JRoute::_('index.php?option=com_rsmembership&view=share&layout=url&tmpl=component&membership_id='.$this->row->id.'&cid='.$row->id); ?>">index.php?option=<?php echo $row->params; ?></a></td>
			<?php } else { ?>
				<?php echo $row->params; ?>
			<?php } ?>
			<td align="center"><?php $cb = JHTML::_('grid.published', $row, $i, 'tick.png', 'publish_x.png', 'folders'); echo str_replace("listItemTask('cb", "listItemTask('cbfolders", $cb); ?></td>
			<td class="order">
			<span><?php $cb = $this->row->sharedPagination->orderUpIcon( $i, true, 'foldersorderup', 'Move Up', 'ordering'); echo str_replace('cb', 'cbfolders', $cb); ?></span>
			<span><?php $cb = $this->row->sharedPagination->orderDownIcon( $i, $n, true, 'foldersorderdown', 'Move Down', 'ordering' ); echo str_replace('cb', 'cbfolders', $cb); ?></span>
			<input type="text" name="order[]" size="5" value="<?php echo $row->ordering; ?>" class="text_area" style="text-align:center" />
			</td>
		</tr>
	<?php
		$i++;
		$k=1-$k;
	}
	?>
</table>