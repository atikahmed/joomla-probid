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

<script type="text/javascript">
	function validate_search(theform)
	{
		if (theform.name.value.length < 1)
		{
			alert('<?php echo JText::_('RST_SEARCH_NAME_ERROR', true); ?>');
			return false;
		}
		
		return true;
	}
</script>

<?php echo RSTicketsProHelper::getConfig('global_message'); ?>

<form id="rsticketspro_form" action="<?php echo RSTicketsProHelper::route('index.php?option=com_rsticketspro&controller=searches&task=save'); ?>" method="post" name="searchForm" onsubmit="return validate_search(this);">
	<p>
		<label class="float_left" for="name"><span class="hasTip" title="<?php echo JText::_('RST_SEARCH_NAME_DESC'); ?>"><?php echo JText::_('RST_SEARCH_NAME'); ?></span></label>
		<input type="text" name="name" id="name" size="40" value="<?php echo $this->escape($this->row->name); ?>" class="inputbox" />
	</p>
	<p>
		<label class="float_left" for="default"><span class="hasTip" title="<?php echo JText::_('RST_DEFAULT_SEARCH_DESC'); ?>"><?php echo JText::_('RST_DEFAULT_SEARCH'); ?></span></label>
		<?php echo $this->lists['default']; ?>
	</p>
	<?php if ($this->row->id) { ?>
	<p>
		<label class="float_left" for="update_search"><span class="hasTip" title="<?php echo JText::_('RST_UPDATE_SEARCH_DESC'); ?>"><?php echo JText::_('RST_UPDATE_SEARCH'); ?></span></label>
		<input type="checkbox" name="update_search" id="update_search" value="1" /> <label for="update_search"><?php echo JText::_('RST_UPDATE_SEARCH_OK'); ?></label>
	</p>
	<?php } ?>
	<p>
		<button type="button" onclick="document.location='<?php echo RSTicketsProHelper::route('index.php?option=com_rsticketspro&view=searches'); ?>'" class="button"><?php echo JText::_('RST_BACK_TO_SEARCHES_LIST'); ?></button>
		<button type="submit" name="Search" class="button"><?php echo JText::_('RST_SAVE'); ?></button>
	</p>
	
<input type="hidden" name="option" value="com_rsticketspro" />
<input type="hidden" name="controller" value="searches" />
<input type="hidden" name="task" value="save" />
<input type="hidden" name="id" value="<?php echo $this->row->id; ?>" />
<?php echo JHTML::_('form.token'); ?>
</form>

<?php JHTML::_('behavior.keepalive'); ?>