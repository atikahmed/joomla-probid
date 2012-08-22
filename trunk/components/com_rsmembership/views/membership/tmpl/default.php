<?php
/**
* @version 1.0.0
* @package RSMembership! 1.0.0
* @copyright (C) 2009-2010 www.rsjoomla.com
* @license GPL, http://www.gnu.org/licenses/gpl-2.0.html
*/

defined('_JEXEC') or die('Restricted access');
?>

<?php if (RSMembershipHelper::isJ16()) { ?>
	<?php if ($this->params->get('show_page_heading', 1)) { ?>
	<h1><?php echo $this->escape($this->membership->name); ?></h1>
	<?php } ?>
<?php } else { ?>
	<?php if ($this->params->get('show_page_title', 1)) { ?>
	<div class="componentheading<?php echo $this->escape($this->params->get('pageclass_sfx')); ?>"><?php echo $this->escape($this->membership->name); ?></div>
	<?php } ?>
<?php } ?>

<form method="post" action="<?php echo JRoute::_('index.php?option=com_rsmembership&task=subscribe&cid='.$this->membership->id.':'.JFilterOutput::stringURLSafe($this->membership->name)); ?>">
<?php if (!empty($this->membership->thumb)) { ?>
	<?php echo JHTML::_('image', 'components/com_rsmembership/assets/thumbs/'.$this->membership->thumb, $this->membership->name, 'class="rsm_thumb"'); ?>
<?php } ?>
<?php echo $this->membership->description; ?>

<?php echo JHTML::_( 'form.token' ); ?>
<input type="hidden" name="option" value="com_rsmembership" />
<input type="hidden" name="cid" value="<?php echo $this->membership->id; ?>" />
<input type="hidden" name="task" value="subscribe" />
</form>