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

<form action="<?php echo JRoute::_('index.php?option=com_rsticketspro&view=kbcontent'); ?>" method="post" name="adminForm" id="adminForm">
	<table class="adminform">
		<tr>
			<td width="100%">
			<?php echo JText::_( 'SEARCH' ); ?>
			<input type="text" name="search" id="search" value="<?php echo $this->escape($this->filter_word); ?>" class="text_area" onChange="document.adminForm.submit();" />
			<?php echo $this->lists['category_state']; ?>
			<button onclick="this.form.submit();"><?php echo JText::_( 'Go' ); ?></button>
			<button onclick="this.form.getElementById('search').value='';this.form.getElementById('category_state').options[0].selected = true;this.form.submit();"><?php echo JText::_( 'Reset' ); ?></button>
			</td>
			<td nowrap="nowrap"><?php echo $this->lists['state']; ?></td>
		</tr>
	</table>
	<div id="editcell1">
		<table class="adminlist">
			<thead>
			<tr>
				<th width="5"><?php echo JText::_( '#' ); ?></th>
				<th width="20"><input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count($this->kbarticles); ?>);"/></th>
				<th><?php echo JHTML::_('grid.sort', 'RST_KB_ARTICLE_NAME', 'name', $this->sortOrder, $this->sortColumn); ?></th>
				<th><?php echo JHTML::_('grid.sort', 'RST_KB_CATEGORY_NAME', 'category', $this->sortOrder, $this->sortColumn); ?></th>
				<th width="80"><?php echo JText::_('RST_PRIVATE'); ?></th>
				<th width="80"><?php echo JText::_('Published'); ?></th>
			</tr>
			</thead>
	<?php
	$k = 0;
	$i = 0;
	$n = count($this->kbarticles);
	foreach ($this->kbarticles as $row)
	{
		if ($row->category_id == 0)
			$row->category = JText::_('RST_KB_NO_CATEGORY');
	?>
		<tr class="row<?php echo $k; ?>">
			<td><?php echo $this->pagination->getRowOffset($i); ?></td>
			<td><?php echo JHTML::_('grid.id', $i, $row->id); ?></td>
			<td><a onclick="window.parent.elSelectEvent('<?php echo $row->id; ?>', '<?php echo str_replace("'", "\'", $this->escape($row->name)); ?>');" href="javascript: void(0);"><?php echo $row->name != '' ? $this->escape($row->name) : JText::_('RST_NO_TITLE'); ?></a></td>
			<td><?php echo $row->category != '' ? $this->escape($row->category) : JText::_('RST_NO_TITLE'); ?></td>
			<td align="center"><?php echo $row->private ? JText::_('YES') : JText::_('NO'); ?></td>
			<td align="center"><?php echo JHTML::_('grid.published', $row, $i); ?></td>
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
	<input type="hidden" name="view" value="kbcontent" />
	<input type="hidden" name="layout" value="element" />
	<input type="hidden" name="controller" value="kbcontent" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="tmpl" value="component" />
	
	<input type="hidden" name="filter_order" value="<?php echo $this->escape($this->sortColumn); ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $this->escape($this->sortOrder); ?>" />
</form>