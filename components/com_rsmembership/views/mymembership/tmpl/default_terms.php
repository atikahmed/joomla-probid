<?php
/**
* @version 1.0.0
* @package RSMembership! 1.0.0
* @copyright (C) 2009-2010 www.rsjoomla.com
* @license GPL, http://www.gnu.org/licenses/gpl-2.0.html
*/

defined('_JEXEC') or die('Restricted access');
?>

<?php if ($this->params->get('show_page_title', 1)) { ?>
<div class="componentheading<?php echo $this->escape($this->params->get('pageclass_sfx')); ?>"><?php echo $this->escape($this->params->get('page_title')); ?></div>
<?php } ?>

<p><?php echo JText::_('RSM_PLEASE_AGREE'); ?></p>
<p><?php echo JText::_('RSM_PLEASE_SCROLL'); ?></p>

<?php echo $this->terms; ?>

<form method="post" action="<?php echo $this->action; ?>">
<input type="hidden" name="agree" value="1" />
<button type="submit"><?php echo JText::_('RSM_I_AGREE'); ?></button>
</form>