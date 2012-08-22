<?php
/**
* @version 1.0.0
* @package RSMembership! 1.0.0
* @copyright (C) 2009-2010 www.rsjoomla.com
* @license GPL, http://www.gnu.org/licenses/gpl-2.0.html
*/

defined('_JEXEC') or die('Restricted access');
?>

<table class="adminlist" id="addsubscriberfiles">
	<thead>
	<tr>
		<th width="5"><?php echo JText::_( '#' ); ?></th>
		<th width="20">&nbsp;</th>
		<th width="20"><?php echo JText::_('Delete'); ?></th>
		<th><?php echo JText::_('RSM_PATH'); ?></th>
		<th width="80"><?php echo JText::_('Published'); ?></th>
	</tr>
	</thead>
	<?php
	$k = 0;
	$i = 0;
	$n = count($this->row->attachments);
	foreach ($this->row->attachments as $row)
	{
	?>
		<tr class="row<?php echo $k; ?>">
			<td><?php echo $this->row->attachmentsPagination->getRowOffset($i); ?></td>
			<td><?php $cb = JHTML::_('grid.id', $i, $row->id, false, 'cid_files'.$this->email_type); echo str_replace('cb', 'cbfiles'.$this->email_type, $cb); ?></td>
			<td align="center">
			<a class="delete-item" onclick="return confirm('<?php echo JText::_('RSM_CONFIRM_DELETE'); ?>')" href="<?php echo JRoute::_('index.php?option=com_rsmembership&controller=memberships&task=attachmentsremove&cid_files[]='.$row->id.'&'.JUtility::getToken().'=1&tabposition=6'); ?>"><?php echo JHTML::_('image', 'administrator/components/com_rsmembership/assets/images/remove.png', JText::_('Delete')); ?></a>
			</td>
			<td><a href="<?php echo JRoute::_('index.php?option=com_rsmembership&controller=files&task=edit&cid='.$row->path); ?>"><?php echo $row->path; ?></a></td>
			<td align="center"><?php $cb = JHTML::_('grid.published', $row, $i, 'tick.png', 'publish_x.png', 'attachments'); echo str_replace('cb', 'cbfiles'.$this->email_type, $cb); ?></td>
		</tr>
	<?php
		$i++;
		$k=1-$k;
	}
	?>
</table>