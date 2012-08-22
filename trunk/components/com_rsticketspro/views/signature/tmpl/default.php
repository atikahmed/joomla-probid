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

<form id="rsticketspro_form" action="<?php echo RSTicketsProHelper::route('index.php?option=com_rsticketspro&view=signature'); ?>" method="post" name="signatureForm">
	
	<p>
		<label class="float_left" for="signature"><span class="hasTip" title="<?php echo JText::_('RST_SIGNATURE_DESC'); ?>"><?php echo JText::_('RST_SIGNATURE'); ?></span></label>
		<span class="float_left"><?php echo $this->editor->display('signature', $this->escape($this->signature),500,250,70,10); ?></span>
	</p>
	<p>&nbsp;</p>
	<p>
		<button type="submit" name="Submit" class="button"><?php echo JText::_('RST_UPDATE'); ?></button>
	</p>

<input type="hidden" name="option" value="com_rsticketspro" />
<input type="hidden" name="task" value="savesignature" />
</form>

<?php JHTML::_('behavior.keepalive'); ?>