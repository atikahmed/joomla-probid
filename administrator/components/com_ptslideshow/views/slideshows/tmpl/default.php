<?php
/**
 * @version     1.0.0
 * @package     com_ptslideshow
 * @copyright   Copyright (C) 2012. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @author      Created by com_combuilder - http://www.notwebdesign.com
 */


// no direct access
defined('_JEXEC') or die;

JHtml::_('behavior.tooltip');
JHTML::_('script','system/multiselect.js',false,true);
$user	= JFactory::getUser();
$userId	= $user->get('id');
$listOrder	= $this->state->get('list.ordering');
$listDirn	= $this->state->get('list.direction');
$canOrder	= $user->authorise('core.edit.state', 'com_ptslideshow');
$saveOrder	= $listOrder == 'a.ordering';
?>

<script language="javascript" type="text/javascript">
	function redirectEdit(id)
	{
		window.location = window.location.href.split('?')[0] + "?option=com_ptslideshow&view=slideshow&task=slideshow.edit&id=" + id;
	}
</script>

<form action="<?php echo JRoute::_('index.php?option=com_ptslideshow&view=slideshows'); ?>" method="post" name="adminForm" id="adminForm">
	<fieldset id="filter-bar">
		<div class="filter-search fltlft">
			<label class="filter-search-lbl" for="filter_search"><?php echo JText::_('JSEARCH_FILTER_LABEL'); ?></label>
			<input type="text" name="filter_search" id="filter_search" value="<?php echo $this->escape($this->state->get('filter.search')); ?>" title="<?php echo JText::_('Search'); ?>" />
			<button type="submit"><?php echo JText::_('JSEARCH_FILTER_SUBMIT'); ?></button>
			<button type="button" onclick="document.id('filter_search').value='';this.form.submit();"><?php echo JText::_('JSEARCH_FILTER_CLEAR'); ?></button>
		</div>
		<div class="filter-select fltrt">

            
                <select name="filter_published" class="inputbox" onchange="this.form.submit()">
                    <option value=""><?php echo JText::_('JOPTION_SELECT_PUBLISHED');?></option>
                    <?php echo JHtml::_('select.options', JHtml::_('jgrid.publishedOptions'), "value", "text", $this->state->get('filter.state'), true);?>
                </select>
                

		</div>
	</fieldset>
	<div class="clr"> </div>

	<table class="adminlist">
		<thead>
			<tr>
				<th width="1%">
					<input type="checkbox" name="checkall-toggle" value="" onclick="checkAll(this)" />
				</th>

				
				<?php if (isset($this->items[0]->title_)) { ?>
                <th width="5%">					
                    <?php echo JHtml::_('grid.sort',  'JGLOBAL_FIELD_TITLEC_LABEL', 'a.title_', $listDirn, $listOrder); ?>					
                </th>
                <?php } ?>
				
				<?php if (isset($this->items[0]->description_)) { ?>
                <th width="15%">
                    <?php echo JHtml::_('grid.sort',  'JGLOBAL_FIELD_LINK_LABEL', 'a.description_', $listDirn, $listOrder); ?>
                </th>
                <?php } ?>

				<?php if (isset($this->items[0]->category_)) { ?>
                <th width="5%">
                    <?php echo JHtml::_('grid.sort',  'JGLOBAL_FIELD_CATEGORY_LABEL', 'a.category_', $listDirn, $listOrder); ?>
                </th>
                <?php } ?>
				
				<?php if (isset($this->items[0]->url_)) { ?>
                <th width="15%">
                    <?php echo JHtml::_('grid.sort',  'JGLOBAL_FIELD_URL_LABEL', 'a.url_', $listDirn, $listOrder); ?>
                </th>
                <?php } ?>

                <?php if (isset($this->items[0]->state)) { ?>
				<th width="5%">
					<?php echo JHtml::_('grid.sort',  'JPUBLISHED', 'a.state', $listDirn, $listOrder); ?>
				</th>
                <?php } ?>
                <?php if (isset($this->items[0]->ordering)) { ?>
				<th width="10%">
					<?php echo JHtml::_('grid.sort',  'JGRID_HEADING_ORDERING', 'a.ordering', $listDirn, $listOrder); ?>
					<?php if ($canOrder && $saveOrder) :?>
						<?php echo JHtml::_('grid.order',  $this->items, 'filesave.png', 'slideshows.saveorder'); ?>
					<?php endif; ?>
				</th>
                <?php } ?>
                <?php if (isset($this->items[0]->id)) { ?>
                <th width="1%" class="nowrap">
                    <?php echo JHtml::_('grid.sort',  'JGRID_HEADING_ID', 'a.id', $listDirn, $listOrder); ?>
                </th>
                <?php } ?>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<td colspan="10">
					<?php echo $this->pagination->getListFooter(); ?>
				</td>
			</tr>
		</tfoot>
		<tbody>
		<?php foreach ($this->items as $i => $item) :
			$ordering	= ($listOrder == 'a.ordering');
			$canCreate	= $user->authorise('core.create',		'com_ptslideshow');
			$canEdit	= $user->authorise('core.edit',			'com_ptslideshow');
			$canCheckin	= $user->authorise('core.manage',		'com_ptslideshow');
			$canChange	= $user->authorise('core.edit.state',	'com_ptslideshow');
			?>
			<tr class="row<?php echo $i % 2; ?>" ondblclick="redirectEdit(<?php echo $item->id; ?>);">
				<td class="center">
					<?php echo JHtml::_('grid.id', $i, $item->id); ?>
				</td>

				<?php if (isset($this->items[0]->title_)) { ?>
				<td class="center">
					<?php if ($canEdit || $canEditOwn) : ?>
						<a href="<?php echo JRoute::_('index.php?option=com_ptslideshow&task=slideshow.edit&id='.$item->id);?>">
						<?php echo $item->title_; ?>
						</a>
					<?php else:?>
						<?php echo $item->title_; ?>
					<?php endif;?>
				</td>
				<?php } ?>
				
				<?php if (isset($this->items[0]->link)) { ?>
				<td class="center">
					<?php echo $item->link; ?>
				</td>
				<?php } ?>
				
				<?php if (isset($this->items[0]->category_)) { ?>
				<td class="center">
					<?php echo $item->cattitle; ?>
				</td>
				<?php } ?>
				
				<?php if (isset($this->items[0]->url_)) { ?>
				<td class="center">
					<?php echo $item->url_; ?>
				</td>
				<?php } ?>

                <?php if (isset($this->items[0]->state)) { ?>
				    <td class="center">
					    <?php echo JHtml::_('jgrid.published', $item->state, $i, 'slideshows.', $canChange, 'cb'); ?>
				    </td>
                <?php } ?>
                <?php if (isset($this->items[0]->ordering)) { ?>
				    <td class="order">
					    <?php if ($canChange) : ?>
						    <?php if ($saveOrder) :?>
							    <?php if ($listDirn == 'asc') : ?>
								    <span><?php echo $this->pagination->orderUpIcon($i, true, 'slideshows.orderup', 'JLIB_HTML_MOVE_UP', $ordering); ?></span>
								    <span><?php echo $this->pagination->orderDownIcon($i, $this->pagination->total, true, 'slideshows.orderdown', 'JLIB_HTML_MOVE_DOWN', $ordering); ?></span>
							    <?php elseif ($listDirn == 'desc') : ?>
								    <span><?php echo $this->pagination->orderUpIcon($i, true, 'slideshows.orderdown', 'JLIB_HTML_MOVE_UP', $ordering); ?></span>
								    <span><?php echo $this->pagination->orderDownIcon($i, $this->pagination->total, true, 'slideshows.orderup', 'JLIB_HTML_MOVE_DOWN', $ordering); ?></span>
							    <?php endif; ?>
						    <?php endif; ?>
						    <?php $disabled = $saveOrder ?  '' : 'disabled="disabled"'; ?>
						    <input type="text" name="order[]" size="5" value="<?php echo $item->ordering;?>" <?php echo $disabled ?> class="text-area-order" />
					    <?php else : ?>
						    <?php echo $item->ordering; ?>
					    <?php endif; ?>
				    </td>
                <?php } ?>
                <?php if (isset($this->items[0]->id)) { ?>
				<td class="center">
					<?php echo (int) $item->id; ?>
				</td>
                <?php } ?>
			</tr>
			<?php endforeach; ?>
		</tbody>
	</table>

	<div>
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="boxchecked" value="0" />
		<input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
		<input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />
		<?php echo JHtml::_('form.token'); ?>
	</div>
</form>