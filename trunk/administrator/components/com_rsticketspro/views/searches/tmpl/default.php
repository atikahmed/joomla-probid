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

<form action="<?php echo JRoute::_('index.php?option=com_rsticketspro&view=searches'); ?>" method="post" name="adminForm" id="adminForm">
<table class="adminlist">
<thead>
<tr>
	<th width="5"><?php echo JText::_('Num'); ?></th>
	<th width="20"><input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count($this->searches); ?>);"/></th>
 	<th width="5"><?php echo JText::_('RST_DELETE'); ?></th>
 	<th><?php echo JText::_('RST_SEARCH_NAME'); ?></th>
	<th width="100"><?php echo JText::_('RST_DEFAULT_SEARCH_SHORT'); ?></th>
	<th width="100"><?php echo JText::_('Ordering'); ?>
	<?php echo JHTML::_('grid.order', $this->searches); ?>
	</th>
</tr>
</thead>
<?php
$k = 1;
$i = 0;
$n = count($this->searches);

foreach ($this->searches as $item) {
	$orderup = $this->pagination->orderUpIcon( $i, true, 'orderup', 'Move Up', 'ordering');
	$orderup = str_replace('images/', 'components/com_rsticketspro/assets/images/', $orderup);
	
	$orderdown = $this->pagination->orderDownIcon( $i, $n, true, 'orderdown', 'Move Down', 'ordering' );
	$orderdown = str_replace('images/', 'components/com_rsticketspro/assets/images/', $orderdown);
?>
<tr class="row<?php echo $k; ?>">
	<td><?php echo $this->pagination->getRowOffset($i); ?></td>
	<td><?php echo JHTML::_('grid.id', $i, $item->id); ?></td>
	<td align="center"><?php echo JHTML::_('rsticketsproicon.deletesearch', $item); ?></td>
	<td><a href="<?php echo JRoute::_('index.php?option=com_rsticketspro&controller=searches&task=edit&cid='.$item->id); ?>"><?php echo !empty($item->name) ? $this->escape($item->name) : '<em>'.JText::_('RST_NO_TITLE').'</em>'; ?></a></td>
	<td><?php echo $item->default ? JText::_('RST_YES') : JText::_('RST_NO'); ?></td>
	<td class="order">
		<span><?php echo $this->pagination->orderUpIcon( $i, true, 'orderup', 'Move Up', 'ordering'); ?></span>
		<span><?php echo $this->pagination->orderDownIcon( $i, $n, true, 'orderdown', 'Move Down', 'ordering' ); ?></span>
		<input type="text" name="order[]" size="5" value="<?php echo $item->ordering; ?>" class="text_area" style="text-align:center" />
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

	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="controller" value="searches" />
	<?php echo JHTML::_('form.token'); ?>
</form>

<?php JHTML::_('behavior.keepalive'); ?>