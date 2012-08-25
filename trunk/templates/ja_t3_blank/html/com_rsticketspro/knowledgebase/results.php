<?php
/**
* @version 2.0.0
* @package RSTickets! Pro 2.0.0
* @copyright (C) 2010 www.rsjoomla.com
* @license GPL, http://www.gnu.org/licenses/gpl-2.0.html
*/

defined('_JEXEC') or die('Restricted access');

if (RSTicketsProHelper::isJ16()) { ?>
		<h1><?php echo JText::sprintf('RST_KB_RESULTS_FOR', $this->escape($this->word)); ?></h1>
	<?php } else { ?>
		<div class="componentheading<?php echo $this->escape($this->params->get('pageclass_sfx')); ?>"><?php echo JText::sprintf('RST_KB_RESULTS_FOR', $this->escape($this->word)); ?></div>
<?php } ?>

<table class="rsticketspro_tablebig rsticketspro_tablebig2" width="100%" border="0" cellspacing="0" cellpadding="0">
<?php if ($this->params->get('show_headings', 1)) { ?>
<tr>
	<th align="center" width="1%"><?php echo JText::_('#'); ?></th>
 	<th align="center" width="45%"><?php echo JText::_('RST_KB_ARTICLE_NAME'); ?></th>
 	<th align="center" width="45%"><?php echo JText::_('RST_KB_CATEGORY_NAME'); ?></th>
</tr>
<?php } ?>
<?php $k = 1; ?>
<?php $i = 0; ?>
<?php foreach ($this->items as $item) { ?>
<tr class="sectiontableentry<?php echo $k . $this->escape($this->params->get('pageclass_sfx')); ?>" >
	<td align="right"><?php echo $this->pagination->getRowOffset($i); ?></td>
	<td><a href="<?php echo RSTicketsProHelper::route('index.php?option=com_rsticketspro&view=article&cid='.$item->id.':'.JFilterOutput::stringURLSafe($item->name)); ?>"><?php echo $item->name != '' ? $item->name : JText::_('RST_NO_TITLE'); ?></a> <?php echo $this->isHot($item->hits) ? '<em class="rst_hot">'.JText::_('RST_HOT').'</em>' : ''; ?></td>
	<td><?php if ($item->category_id) { ?><a href="<?php echo RSTicketsProHelper::route('index.php?option=com_rsticketspro&view=knowledgebase&cid='.$item->category_id.':'.JFilterOutput::stringURLSafe($item->category_name)); ?>"><?php echo $this->escape($item->category_name); ?></a><?php } else { echo JText::_('RST_KB_NO_CATEGORY'); } ?></td>
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