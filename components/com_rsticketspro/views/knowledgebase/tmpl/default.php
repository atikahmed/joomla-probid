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

<script language="javascript" type="text/javascript">

	function tableOrdering( order, dir, task )
	{
		var form = document.adminForm;

		form.filter_order.value 	= order;
		form.filter_order_Dir.value	= dir;
		document.adminForm.submit( task );
	}
</script>

<?php if (RSTicketsProHelper::isJ16()) { ?>
	<?php if (!empty($this->category->name)) { ?>
	<h1><?php echo $this->escape($this->category->name); ?></h1>
	<?php } else { ?>
	<h1><?php echo $this->escape($this->params->get('page_heading', $this->params->get('page_title'))); ?></h1>
	<?php } ?>
<?php } else { ?>
<?php if ($this->params->get('show_page_title', 1)) { ?>
<div class="componentheading<?php echo $this->escape($this->params->get('pageclass_sfx')); ?>">
	<?php if (!empty($this->category->name)) { ?>
		<?php echo $this->escape($this->category->name); ?>
	<?php } else { ?>
		<?php echo $this->escape($this->params->get('page_title')); ?>
	<?php } ?>
</div>
<?php } ?>
<?php } ?>

<?php if ($this->params->def('show_description', 1)) { ?>
	<?php echo $this->category->description; ?>
<?php } ?>

<?php if (count($this->categories)) { ?>
<div class="rsticketspro_halfbox rsticketspro_fullbox">
	<ul class="rsticketspro_categories">
	<?php foreach ($this->categories as $category) { ?>
		<?php $category->thumb = !$category->thumb ? '../../images/kb-icon.png' : $category->thumb; ?>
		<li>
			<strong><?php echo JHTML::image('components/com_rsticketspro/assets/thumbs/small/'.$category->thumb, $category->name); ?> <a href="<?php echo RSTicketsProHelper::route('index.php?option=com_rsticketspro&view=knowledgebase&cid='.$category->id.':'.JFilterOutput::stringURLSafe($category->name)); ?>"><?php echo $this->escape($category->name); ?></a></strong>
			<?php if ($this->params->def('show_description', 1) && $category->description) { ?>
			<?php echo $category->description; ?>
			<?php } ?>
		</li>
		<?php } ?>
	</ul>
</div>
<?php } ?>
<span class="rsticketspro_clear"></span>

<form action="<?php echo RSTicketsProHelper::route('index.php?option=com_rsticketspro&view=knowledgebase'.($this->cid ? '&cid='.$this->cid.':'.JFilterOutput::stringURLSafe($this->category->name) : '')); ?>" method="post" name="adminForm">

<?php if ($this->params->get('filter', 1)) { ?>
<div class="rsticketspro_filter">
	<?php echo JText::_('RST_FILTER'); ?>
	<input type="text" name="search" id="rst_filter" value="<?php echo $this->escape($this->filter_word);?>" class="inputbox" onchange="document.adminForm.submit();" />
	<button type="button" onclick="document.getElementById('rst_filter').value=''; this.form.submit();"><?php echo JText::_('RST_CLEAR'); ?></button>
</div>
<?php } ?>
<?php if ($this->params->get('show_pagination_limit', 1)) { ?>
<div class="rsticketspro_pagination_limit">
	<?php echo JText::sprintf('RST_DISPLAY_PAGES', $this->pagination->getLimitBox()); ?>
</div>
<?php } ?>
<span class="rsticketspro_clear"></span>

<table class="rsticketspro_tablebig rsticketspro_tablebig2" width="100%" border="0" cellspacing="0" cellpadding="0">
<?php if ($this->params->get('show_headings', 1)) { ?>
<tr>
	<th align="center" width="1%"><?php echo JText::_('#'); ?></th>
 	<th align="center" width="45%"><?php echo JHTML::_('rsticketsprogrid.sort', 'RST_KB_ARTICLE_NAME', 'name', $this->sortOrder, $this->sortColumn); ?></th>
	<?php if ($this->params->get('show_hits', 0)) { ?>
	<th align="center" width="45%"><?php echo JHTML::_('rsticketsprogrid.sort', 'RST_KB_ARTICLE_HITS', 'hits', $this->sortOrder, $this->sortColumn); ?></th>
	<?php } ?>
</tr>
<?php } ?>
<?php $k = 1; ?>
<?php $i = 0; ?>
<?php foreach ($this->items as $item) { ?>
<tr class="sectiontableentry<?php echo $k . $this->escape($this->params->get('pageclass_sfx')); ?>" >
	<td align="right"><?php echo $this->pagination->getRowOffset($i); ?></td>
	<td><a href="<?php echo RSTicketsProHelper::route('index.php?option=com_rsticketspro&view=article&cid='.$item->id.':'.JFilterOutput::stringURLSafe($item->name)); ?>"><?php echo $item->name != '' ? $item->name : JText::_('RST_NO_TITLE'); ?></a> <?php echo $this->isHot($item->hits) ? '<em class="rst_hot">'.JText::_('RST_HOT').'</em>' : ''; ?></td>
	<?php if ($this->params->get('show_hits', 0)) { ?>
	<td><?php echo $item->hits; ?></td>
	<?php } ?>
</tr>
<?php $k = $k == 1 ? 2 : 1; ?>
<?php $i++; ?>
<?php } ?>
<?php if ($this->params->get('show_pagination', 1) && $this->pagination->get('pages.total') > 1) { ?>
<tr>
	<td align="center" colspan="3" class="sectiontablefooter<?php echo $this->escape($this->params->get('pageclass_sfx')); ?>">
		<div class="pagination"><?php echo $this->pagination->getPagesLinks(); ?></div>
	</td>
</tr>
<tr>
	<td colspan="3" align="right">
		<?php echo $this->pagination->getPagesCounter(); ?>
	</td>
</tr>
<?php } ?>
</table>

	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="option" value="com_rsticketspro" />
	<input type="hidden" name="view" value="knowledgebase" />
	<input type="hidden" name="cid" value="<?php echo $this->cid; ?>" />
	
	<input type="hidden" name="filter_order" value="<?php echo $this->sortColumn; ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $this->sortOrder; ?>" />
</form>