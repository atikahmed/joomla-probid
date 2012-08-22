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

<?php echo RSTicketsProHelper::getConfig('global_message'); ?>

<form id="rsticketspro_form" action="<?php echo RSTicketsProHelper::route('index.php?option=com_rsticketspro&view=tickets'); ?>" method="post" name="adminForm" id="adminForm">
	<table cellspacing="0" cellpadding="0" border="0" width="100%" class="admintable">
	<tr>
		<td width="150"  align="right" class="key"><span class="hasTip" title="<?php echo JText::_('RST_SEARCH_TEXT_DESC'); ?>"><label for="filter_word"><?php echo JText::_('RST_SEARCH_TEXT'); ?></label></span></td>
		<td><input type="text" name="filter_word" id="filter_word" size="40" value="" class="inputbox" /></td>
	</tr>
	<tr>
		<td width="150"  align="right" class="key"><label class="float_left" for="customer"><span class="hasTip" title="<?php echo JText::_('RST_SEARCH_CUSTOMER_DESC'); ?>"><?php echo JText::_('RST_SEARCH_CUSTOMER'); ?></span></label></td>
		<td><input type="text" name="customer" id="customer" size="40" value="" class="inputbox" /></td>
	</tr>
	<?php if ($this->permissions->see_other_tickets) { ?>
	<tr>
		<td width="150"  align="right" class="key"><label class="float_left" for="staff"><span class="hasTip" title="<?php echo JText::_('RST_SEARCH_STAFF_DESC'); ?>"><?php echo JText::_('RST_SEARCH_STAFF'); ?></span></label></td>
		<td><input type="text" name="staff" id="staff" size="40" value="" class="inputbox" /></td>
	</tr>
	<?php } ?>
	<tr>
		<td width="150"  align="right" class="key"><label class="float_left" for="customer"><span class="hasTip" title="<?php echo JText::_('RST_SEARCH_DEPARTMENTS_DESC'); ?>"><?php echo JText::_('RST_SEARCH_DEPARTMENTS'); ?></span></label></td>
		<td><?php echo $this->lists['departments']; ?></td>
	</tr>
	<tr>
		<td width="150"  align="right" class="key"><label class="float_left" for="customer"><span class="hasTip" title="<?php echo JText::_('RST_SEARCH_PRIORITIES_DESC'); ?>"><?php echo JText::_('RST_SEARCH_PRIORITIES'); ?></span></label></td>
		<td><?php echo $this->lists['priorities']; ?></td>
	</tr>
	<tr>
		<td width="150"  align="right" class="key"><label class="float_left" for="customer"><span class="hasTip" title="<?php echo JText::_('RST_SEARCH_STATUSES_DESC'); ?>"><?php echo JText::_('RST_SEARCH_STATUSES'); ?></span></label></td>
		<td><?php echo $this->lists['statuses']; ?></td>
	</tr>
	<tr>
		<td width="150"  align="right" class="key"><label class="float_left" for="ordering"><?php echo JText::_('Ordering'); ?></label></td>
		<td><?php echo $this->lists['ordering']; ?>
		<?php echo $this->lists['ordering_dir']; ?></td>
	</tr>
	<tr>
		<td width="150"  align="right" class="key"><label class="float_left">&nbsp;</label></td>
		<td><input type="checkbox" class="rsticketspro_nofloat" name="flagged" id="flagged" value="1" /> <label class="rsticketspro_nofloat" for="flagged"><?php echo JText::_('RST_SEARCH_FLAGGED'); ?></label></td>
	</tr>
	<tr>
		<td>&nbsp;</td>
		<td><button type="submit" name="Search" class="button"><?php echo JText::_('RST_SEARCH'); ?></button></td>
	</tr>
	</table>
	
	<?php if ($this->show_footer) { ?>
		<?php echo $this->footer; ?>
	<?php } ?>

<input type="hidden" name="option" value="com_rsticketspro" />
<input type="hidden" name="task" value="search" />
</form>

<style type="text/css">
.icon-32-search { background: url(components/com_rsticketspro/assets/images/icon-32-search.png); }
</style>

<?php JHTML::_('behavior.keepalive'); ?>