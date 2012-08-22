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
	<h1>
		<?php if (!empty($this->article->name)) { ?>
			<?php echo $this->escape($this->article->name); ?>
		<?php } else { ?>
			<?php echo $this->escape($this->params->get('page_heading')); ?>
		<?php } ?>
	</h1>
	<?php } ?>
	<?php } else { ?>
	<?php if ($this->params->get('show_page_title', 1)) { ?>
	<div class="componentheading<?php echo $this->escape($this->params->get('pageclass_sfx')); ?>">
		<?php if (!empty($this->article->name)) { ?>
			<?php echo $this->escape($this->article->name); ?>
		<?php } else { ?>
			<?php echo $this->escape($this->params->get('page_title')); ?>
		<?php } ?>
	</div>
	<?php } ?>
<?php } ?>

<?php echo $this->article->text; ?>