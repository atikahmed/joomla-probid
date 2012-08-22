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

<form action="<?php echo JRoute::_('index.php?option=com_rsticketspro&view=signature'); ?>" method="post" name="adminForm" id="adminForm">
<div class="col100">
	<table cellspacing="0" cellpadding="0" border="0" width="100%" class="admintable">
		<tr>
			<td width="300" style="width: 50px;" valign="top" align="right" class="key">
				<label for="signature"><span class="hasTip" title="<?php echo JText::_('RST_SIGNATURE_DESC'); ?>"><?php echo JText::_('RST_SIGNATURE'); ?></span></label>
			</td>
			<td nowrap="nowrap">
				<?php echo $this->editor->display('signature', $this->signature,500,250,70,10); ?>
			</td>
		</tr>
	</table>
</div>

<input type="hidden" name="option" value="com_rsticketspro" />
<input type="hidden" name="task" value="savesignature" />
</form>

<?php JHTML::_('behavior.keepalive'); ?>