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
<!--
function submitbutton(pressbutton)
{
	var form = document.adminForm;
	
	if (pressbutton == 'cancel')
	{
		submitform(pressbutton);
		return;
	}
	
	// do field validation
	if (form.name.value.length == 0)
		return alert('<?php echo JText::_('RST_KB_ARTICLE_NAME_ERROR', true); ?>');
	else
		submitform(pressbutton);
}

<?php if (RSTicketsProHelper::isJ16()) { ?>
	Joomla.submitbutton = submitbutton;
<?php } ?>
</script>

<form method="post" action="<?php echo JRoute::_('index.php?option=com_rsticketspro'); ?>" name="adminForm" id="adminForm">
<div class="col100">
	<table cellspacing="0" cellpadding="0" border="0" width="100%" class="admintable">
		<tr>
			<td width="300" style="width: 300px;" align="right" class="key"><span class="hasTip" title="<?php echo JText::_('RST_KB_ARTICLE_NAME_DESC'); ?>"><label for="name"><?php echo JText::_('RST_KB_ARTICLE_NAME'); ?></label></span></td>
			<td><input type="text" name="name" value="<?php echo $this->escape($this->ticket->subject); ?>" id="name" size="120" maxlength="255" /></td>
		</tr>
		<tr>
			<td width="300" style="width: 300px;" align="right" class="key"><span class="hasTip" title="<?php echo JText::_('RST_KB_PUBLISH_TO_CATEGORY_DESC'); ?>"><label for="category_id"><?php echo JText::_('RST_KB_PUBLISH_TO_CATEGORY'); ?></label></span></td>
			<td><?php echo $this->lists['categories']; ?></td>
		</tr>
		<tr>
			<td width="300" style="width: 300px;" align="right" class="key"><span class="hasTip" title="<?php echo JText::_('RST_PUBLISH_ARTICLE_DESC'); ?>"><label for="publish_article"><?php echo JText::_('RST_PUBLISH_ARTICLE'); ?></label></span></td>
			<td><?php echo $this->lists['publish_article']; ?></td>
		</tr>
		<tr>
			<td width="300" style="width: 300px;" align="right" class="key"><span class="hasTip" title="<?php echo JText::_('RST_PRIVATE_ARTICLE_DESC'); ?>"><label for="private"><?php echo JText::_('RST_PRIVATE_ARTICLE'); ?></label></span></td>
			<td><?php echo $this->lists['private']; ?></td>
		</tr>
	</table>
</div>

<?php echo JHTML::_('form.token'); ?>
<input type="hidden" name="option" value="com_rsticketspro" />
<input type="hidden" name="task" value="" />
<input type="hidden" name="view" value="ticket" />

<input type="hidden" name="id" value="<?php echo $this->ticket->id; ?>" />
<input type="hidden" name="cid" value="<?php echo $this->ticket->id; ?>" />
</form>

<?php
//keep session alive while editing
JHTML::_('behavior.keepalive');
?>