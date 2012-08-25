<?php
/**
* @version 2.0.0
* @package RSTickets! Pro 2.0.0
* @copyright (C) 2010 www.rsjoomla.com
* @license GPL, http://www.gnu.org/licenses/gpl-2.0.html
*/

defined('_JEXEC') or die('Restricted access');
?>

<span id="rsticketspro_close">
	<a href="javascript: void(0);" onclick="rsticketspro_close();"><?php echo JHTML::image('components/com_rsticketspro/assets/images/close.png', ''); ?></a>
	<a href="javascript: void(0);" onclick="rsticketspro_close();"><?php echo JText::_('RST_CLOSE'); ?></a>
</span>

<?php if (count($this->results)) { ?>
	<!-- rsticketspro_results -->
	<?php foreach ($this->results as $result) { ?>
	<p><strong><a href="<?php echo RSTicketsProHelper::route('index.php?option=com_rsticketspro&view=article&cid='.$result->id.':'.JFilterOutput::stringURLSafe($result->name)); ?>"><?php echo $this->escape($result->name); ?></a></strong></p>
	<?php } ?>
<?php } ?>