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

<?php if (RSTicketsProHelper::isJ16()) { ?>
	<?php if ($this->params->get('show_page_heading', 1)) { ?>
		<h1><?php echo $this->escape($this->params->get('page_heading', $this->params->get('page_title'))); ?></h1>
	<?php } ?>
	<?php } else { ?>
	<?php if ($this->params->get('show_page_title', 1)) { ?>
		<div class="componentheading<?php echo $this->escape($this->params->get('pageclass_sfx')); ?>"><?php echo $this->escape($this->params->get('page_title')); ?></div>
	<?php } ?>
<?php } ?>

<?php echo RSTicketsProHelper::getConfig('global_message'); ?>

<?php
$order = JHTML::_('grid.order',$this->searches);
$order = str_replace('images/', 'components/com_rsticketspro/assets/images/', $order);
?>

<form action="<?php echo RSTicketsProHelper::route('index.php?option=com_rsticketspro&view=searches'); ?>" method="post" name="adminForm">
<table class="rsticketspro_tablebig rsticketspro_tablebig2" width="100%" border="0" cellspacing="0" cellpadding="0">
<tr>
	<th align="center" width="1%"><?php echo JText::_('#'); ?></td>
	<th align="center" width="20"><input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count($this->searches); ?>);"/></td>
 	<th align="center" width="5"><?php echo JText::_('RST_DELETE'); ?></td>
 	<th align="center"><?php echo JText::_('RST_SEARCH_NAME'); ?></td>
	<th align="center"><?php echo JText::_('RST_DEFAULT_SEARCH_SHORT'); ?></td>
	<th align="center"><?php echo JText::_('Ordering'); ?><?php echo $order; ?></td>
</tr>
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
<tr class="sectiontableentry<?php echo $k . $this->escape($this->params->get('pageclass_sfx')); ?>" >
	<td><?php echo $this->pagination->getRowOffset($i); ?></td>
	<td><?php echo JHTML::_('grid.id', $i, $item->id); ?></td>
	<td align="center"><?php echo JHTML::_('rsticketsproicon.deletesearch', $item); ?></td>
	<td><a href="<?php echo RSTicketsProHelper::route('index.php?option=com_rsticketspro&controller=searches&task=edit&cid='.$item->id); ?>"><?php echo !empty($item->name) ? $this->escape($item->name) : '<em>'.JText::_('RST_NO_TITLE').'</em>'; ?></a></td>
	<td><?php echo $item->default ? JText::_('RST_YES') : JText::_('RST_NO'); ?></td>
	<td class="order">
		<span><?php echo $orderup; ?></span>
		<span><?php echo $orderdown; ?></span>
		<input type="text" name="order[]" size="5" value="<?php echo $item->ordering; ?>" class="rst_order_text" style="text-align:center" />
	</td>
</tr>
<?php $k = $k == 1 ? 2 : 1; ?>
<?php $i++; ?>
<?php } ?>
<?php if ($this->pagination->get('pages.total') > 1) { ?>
<tr>
	<td align="center" colspan="6" class="sectiontablefooter<?php echo $this->escape($this->params->get('pageclass_sfx')); ?>">
	<div class="pagination"><?php echo $this->pagination->getPagesLinks(); ?></div>
	</td>
</tr>
<tr>
	<td colspan="6" align="right"><?php echo $this->pagination->getPagesCounter(); ?></td>
</tr>
<?php } ?>
</table>

	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="controller" value="searches" />
	<?php echo JHTML::_('form.token'); ?>
</form>

<?php JHTML::_('behavior.keepalive'); ?>