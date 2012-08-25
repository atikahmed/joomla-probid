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

<h1><?php echo JText::_('RST_SEARCH_TICKET'); ?></h1>

<?php echo RSTicketsProHelper::getConfig('global_message'); ?>

<form id="rsticketspro_form" action="<?php echo RSTicketsProHelper::route('index.php?option=com_rsticketspro&view=rsticketspro'.$this->itemid); ?>" method="post" name="searchForm">
	<?php if (!$this->is_advanced) { ?>
		<p><a href="<?php echo RSTicketsProHelper::route('index.php?option=com_rsticketspro&view=search&advanced=true'); ?>"><?php echo JText::_('RST_OPEN_ADVANCED_SEARCH'); ?></a></p>
	<?php } ?>
		<p>
			<label class="float_left" for="filter_word"><span class="hasTip" title="<?php echo JText::_('RST_SEARCH_TEXT_DESC'); ?>"><?php echo JText::_('RST_SEARCH_TEXT'); ?></span></label>
			<input type="text" name="filter_word" id="filter_word" size="40" value="" class="inputbox" />
		</p>
		<?php if (!$this->is_advanced) { ?>
		<input type="hidden" name="customer" id="customer" value="" />
		<input type="hidden" name="staff" id="staff" value="" />
		<input type="hidden" name="status_id[]" id="status_id" value="0" />
		<?php } ?>
	<?php if ($this->is_advanced) { ?>
		<?php if ($this->is_staff) { ?>
			<p>
				<label class="float_left" for="customer"><span class="hasTip" title="<?php echo JText::_('RST_SEARCH_CUSTOMER_DESC'); ?>"><?php echo JText::_('RST_SEARCH_CUSTOMER'); ?></span></label>
				<input type="text" name="customer" id="customer" size="40" value="" class="inputbox" />
			</p>
		<?php } ?>
		<?php if ($this->is_staff && $this->permissions->see_other_tickets) { ?>
			<p>
				<label class="float_left" for="staff"><span class="hasTip" title="<?php echo JText::_('RST_SEARCH_STAFF_DESC'); ?>"><?php echo JText::_('RST_SEARCH_STAFF'); ?></span></label>
				<input type="text" name="staff" id="staff" size="40" value="" class="inputbox" />
			</p>
		<?php } ?>
			<p>
				<label class="float_left" for="departments"><span class="hasTip" title="<?php echo JText::_('RST_SEARCH_DEPARTMENTS_DESC'); ?>"><?php echo JText::_('RST_SEARCH_DEPARTMENTS'); ?></span></label>
				<?php echo $this->lists['departments']; ?>
			</p>
			<p>
				<label class="float_left" for="priorities"><span class="hasTip" title="<?php echo JText::_('RST_SEARCH_PRIORITIES_DESC'); ?>"><?php echo JText::_('RST_SEARCH_PRIORITIES'); ?></span></label>
				<?php echo $this->lists['priorities']; ?>
			</p>
			<p>
				<label class="float_left" for="statuses"><span class="hasTip" title="<?php echo JText::_('RST_SEARCH_STATUSES_DESC'); ?>"><?php echo JText::_('RST_SEARCH_STATUSES'); ?></span></label>
				<?php echo $this->lists['statuses']; ?>
			</p>
			<p>
				<label class="float_left" for="ordering"><?php echo JText::_('Ordering'); ?></label>
				<?php echo $this->lists['ordering']; ?>
				<?php echo $this->lists['ordering_dir']; ?>
			</p>
	<?php } ?>
		<?php if ($this->is_staff) { ?>
			<p>
				<label class="float_left">&nbsp;</label>
				<input type="checkbox" class="rsticketspro_nofloat" name="flagged" id="flagged" value="1" /> <label class="rsticketspro_nofloat" for="flagged"><?php echo JText::_('RST_SEARCH_FLAGGED'); ?></label>
			</p>
		<?php } ?>
			<p>
				<button type="submit" name="Search" class="button"><?php echo JText::_('RST_SEARCH'); ?></button>
			</p>
	
	<?php if ($this->show_footer) { ?>
		<?php echo $this->footer; ?>
	<?php } ?>

<input type="hidden" name="option" value="com_rsticketspro" />
<input type="hidden" name="task" value="search" />
</form>

<?php JHTML::_('behavior.keepalive'); ?>