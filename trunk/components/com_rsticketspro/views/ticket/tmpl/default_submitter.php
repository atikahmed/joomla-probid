<?php
/**
* @version 2.0.0
* @package RSTickets! Pro 2.0.0
* @copyright (C) 2010 www.rsjoomla.com
* @license GPL, http://www.gnu.org/licenses/gpl-2.0.html
*/

defined('_JEXEC') or die('Restricted access');
?>
	<p class="rsticketspro_title rsticketspro_clickable"><?php echo JText::_('RST_SUBMITTER_INFORMATION'); ?></p>
		<div class="rsticketspro_content">
			<div class="rsticketspro_form">
			<p>
				<label class="float_left"><?php echo JHTML::image('components/com_rsticketspro/assets/images/browser.png', ''); ?> <strong><?php echo JText::_('RST_TICKET_USER_AGENT'); ?></strong></label>
				<?php echo $this->escape($this->row->agent); ?>
			</p>
			<p>
				<label class="float_left"><?php echo JHTML::image('components/com_rsticketspro/assets/images/referer.png', ''); ?> <strong><?php echo JText::_('RST_TICKET_REFERER'); ?></strong></label>
				<a href="<?php echo $this->escape($this->row->referer); ?>" target="_blank"><?php echo $this->escape($this->row->referer); ?></a>
			</p>
			<p>
				<label class="float_left"><?php echo JHTML::image('components/com_rsticketspro/assets/images/ip.png', ''); ?> <strong><?php echo JText::_('RST_TICKET_IP'); ?></strong></label>
				<?php echo $this->escape($this->row->ip); ?>
			</p>
			<p>
				<label class="float_left"><?php echo JHTML::image('components/com_rsticketspro/assets/images/logged.gif', ''); ?> <strong><?php echo JText::_('RST_TICKET_LOGGED'); ?></strong></label>
				<?php echo $this->row->logged ? JText::_('Yes') : JText::_('No'); ?>
			</p>
			</div>
		</div>