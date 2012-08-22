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

<form action="<?php echo JRoute::_('index.php?option=com_rsticketspro&view=kbrules'); ?>" method="post" name="adminForm" id="adminForm">
	<div class="col100">
	<fieldset class="adminform">
		<table class="admintable">
		<tr>
			<td width="300" style="width: 300px;" align="right" class="key" valign="top">
				<span class="hasTip" title="<?php echo JText::_('RST_KB_TEMPLATE_BODY_DESC'); ?>">
				<?php echo JText::sprintf('RST_KB_TEMPLATE_BODY', '{ticket_subject}<br /> {ticket_messages}<br /> {ticket_department}<br /> {ticket_date}'); ?>
				</span>
			</td>
			<td>
				<?php echo $this->editor->display('kb_template_body', $this->config->kb_template_body,500,250,70,10); ?>
			</td>
		</tr>
		<tr>
			<td width="300" style="width: 300px;" align="right" class="key" valign="top">
				<span class="hasTip" title="<?php echo JText::_('RST_KB_TEMPLATE_TICKET_BODY_DESC'); ?>">
				<?php echo JText::sprintf('RST_KB_TEMPLATE_TICKET_BODY', '{message_user}<br /> {message_date}<br /> {message_text}'); ?>
				</span>
			</td>
			<td>
				<?php echo $this->editor->display('kb_template_ticket_body', $this->config->kb_template_ticket_body,500,250,70,10); ?>
			</td>
		</tr>
		</table>
	</fieldset>
	</div>
	
	<?php echo JHTML::_('form.token'); ?>
	<input type="hidden" name="option" value="com_rsticketspro" />
	<input type="hidden" name="view" value="kbtemplate" />
	<input type="hidden" name="task" value="" />
</form>

<?php
//keep session alive while editing
JHTML::_('behavior.keepalive');
?>