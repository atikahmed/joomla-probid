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

<table width="100%">
<tr>
	<td width="50%" valign="top">

<table cellspacing="0" cellpadding="0" border="0" width="100%">
	<tr>
		<td valign="top">
		<table class="adminlist">
			<tr>
				<td>
					<div id="cpanel">
					<div style="float: left">
						<div class="icon hasTip" title="<?php echo JText::_('RST_MANAGE_TICKETS_DESC'); ?>">
							<a href="index.php?option=com_rsticketspro&amp;view=tickets">
								<?php echo JHTML::_('image', 'administrator/components/com_rsticketspro/assets/images/tickets.png', JText::_('RST_MANAGE_TICKETS')); ?>
								<span><?php echo JText::_('RST_MANAGE_TICKETS'); ?></span>
							</a>
						</div>
					</div>
					<div style="float: left">
						<div class="icon hasTip" title="<?php echo JText::_('RST_DEPARTMENTS_DESC'); ?>">
							<a href="index.php?option=com_rsticketspro&amp;view=departments">
								<?php echo JHTML::_('image', 'administrator/components/com_rsticketspro/assets/images/departments.png', JText::_('RST_DEPARTMENTS')); ?>
								<span><?php echo JText::_('RST_DEPARTMENTS'); ?></span>
							</a>
						</div>
					</div>
					<div style="float: left">
						<div class="icon hasTip" title="<?php echo JText::_('RST_GROUPS_DESC'); ?>">
							<a href="index.php?option=com_rsticketspro&amp;view=groups">
								<?php echo JHTML::_('image', 'administrator/components/com_rsticketspro/assets/images/groups.png', JText::_('RST_GROUPS')); ?>
								<span><?php echo JText::_('RST_GROUPS'); ?></span>
							</a>
						</div>
					</div>
					<div style="float: left">
						<div class="icon hasTip" title="<?php echo JText::_('RST_STAFF_MEMBERS_DESC'); ?>">
							<a href="index.php?option=com_rsticketspro&amp;view=staff">
								<?php echo JHTML::_('image', 'administrator/components/com_rsticketspro/assets/images/staff.png', JText::_('RST_STAFF_MEMBERS')); ?>
								<span><?php echo JText::_('RST_STAFF_MEMBERS'); ?></span>
							</a>
						</div>
					</div>
					<div style="float: left">
						<div class="icon hasTip" title="<?php echo JText::_('RST_PRIORITIES_DESC'); ?>">
							<a href="index.php?option=com_rsticketspro&amp;view=priorities">
								<?php echo JHTML::_('image', 'administrator/components/com_rsticketspro/assets/images/priorities.png', JText::_('RST_PRIORITIES')); ?>
								<span><?php echo JText::_('RST_PRIORITIES'); ?></span>
							</a>
						</div>
					</div>
					<div style="float: left">
						<div class="icon hasTip" title="<?php echo JText::_('RST_STATUSES_DESC'); ?>">
							<a href="index.php?option=com_rsticketspro&amp;view=statuses">
								<?php echo JHTML::_('image', 'administrator/components/com_rsticketspro/assets/images/statuses.png', JText::_('RST_STATUSES')); ?>
								<span><?php echo JText::_('RST_STATUSES'); ?></span>
							</a>
						</div>
					</div>
					<div style="float: left">
						<div class="icon hasTip" title="<?php echo JText::_('RST_KNOWLEDGEBASE_DESC'); ?>">
							<a href="index.php?option=com_rsticketspro&amp;view=knowledgebase">
								<?php echo JHTML::_('image', 'administrator/components/com_rsticketspro/assets/images/knowledgebase.png', JText::_('RST_KNOWLEDGEBASE')); ?>
								<span><?php echo JText::_('RST_KNOWLEDGEBASE'); ?></span>
							</a>
						</div>
					</div>
					<div style="float: left">
						<div class="icon hasTip" title="<?php echo JText::_('RST_EMAIL_MESSAGES_DESC'); ?>">
							<a href="index.php?option=com_rsticketspro&amp;view=emails">
								<?php echo JHTML::_('image', 'administrator/components/com_rsticketspro/assets/images/emails.png', JText::_('RST_EMAIL_MESSAGES')); ?>
								<span><?php echo JText::_('RST_EMAIL_MESSAGES'); ?></span>
							</a>
						</div>
					</div>
					<div style="float: left">
						<div class="icon hasTip" title="<?php echo JText::_('RST_CONFIGURATION_DESC'); ?>">
							<a href="index.php?option=com_rsticketspro&amp;view=configuration">
								<?php echo JHTML::_('image', 'administrator/components/com_rsticketspro/assets/images/configuration.png', JText::_('RST_CONFIGURATION')); ?>
								<span><?php echo JText::_('RST_CONFIGURATION'); ?></span>
							</a>
						</div>
					</div>
					<?php $mainframe =& JFactory::getApplication(); $mainframe->triggerEvent('onAfterTicketsOverview');?>
					<div style="float: left">
						<div class="icon hasTip" title="<?php echo JText::_('RST_UPDATES_DESC'); ?>">
							<a href="index.php?option=com_rsticketspro&amp;view=updates">
								<?php echo JHTML::_('image', 'administrator/components/com_rsticketspro/assets/images/updates.png', JText::_('RST_UPDATES')); ?>
								<span><?php echo JText::_('RST_UPDATES'); ?></span>
							</a>
						</div>
					</div>
					<div style="float: left">
						<div class="icon hasTip" title="<?php echo JText::_('RST_GET_SUPPORT_DESC'); ?>">
							<a href="http://www.rsjoomla.com/customer-support/tickets.html" target="_blank">
								<?php echo JHTML::_('image', 'administrator/components/com_rsticketspro/assets/images/support.png', JText::_('RST_GET_SUPPORT')); ?>
								<span><?php echo JText::_('RST_GET_SUPPORT'); ?></span>
							</a>
						</div>
					</div>
					<div style="float: left">
						<div class="icon hasTip" title="<?php echo JText::_('RST_GO_TO_FRONTEND_DESC'); ?>">
							<a href="<?php echo JURI :: root(); ?>index.php?option=com_rsticketspro&view=submit" target="_blank">
								<?php echo JHTML::_('image', 'administrator/components/com_rsticketspro/assets/images/frontend.png', JText::_('RST_GO_TO_FRONTEND')); ?>
								<span><?php echo JText::_('RST_GO_TO_FRONTEND_SUBMIT'); ?></span>
							</a>
						</div>
					</div>
					<div style="float: left">
						<div class="icon hasTip" title="<?php echo JText::_('RST_GO_TO_FRONTEND_DESC'); ?>">
							<a href="<?php echo JURI :: root(); ?>index.php?option=com_rsticketspro" target="_blank">
								<?php echo JHTML::_('image', 'administrator/components/com_rsticketspro/assets/images/frontend.png', JText::_('RST_GO_TO_FRONTEND')); ?>
								<span><?php echo JText::_('RST_GO_TO_FRONTEND_TICKETS'); ?></span>
							</a>
						</div>
					</div>
					</div>
				</td>
			</tr>
		</table>
		</td>
	</tr>
</table>

<span class="rsticketspro_clear"></span>