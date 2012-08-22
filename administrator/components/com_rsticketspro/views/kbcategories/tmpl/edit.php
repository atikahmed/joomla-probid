<?php
/**
* @version 2.0.0
* @package RSTickets! Pro 2.0.0
* @copyright (C) 2010 www.rsjoomla.com
* @license GPL, http://www.gnu.org/licenses/gpl-2.0.html
*/

defined('_JEXEC') or die('Restricted access');

JHTML::_('behavior.tooltip');
JHTML::_('behavior.modal');
?>

<script type="text/javascript">
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
		alert('<?php echo JText::_('RST_KB_CATEGORY_NAME_ERROR', true); ?>');
	else
		submitform(pressbutton);
}

<?php if (RSTicketsProHelper::isJ16()) { ?>
	Joomla.submitbutton = submitbutton;
<?php } ?>
</script>

<form action="<?php echo JRoute::_('index.php?option=com_rsticketspro&controller=kbcategories&task=edit'); ?>" method="post" name="adminForm" id="adminForm" enctype="multipart/form-data">
<div class="col100">
	<table cellspacing="0" cellpadding="0" border="0" width="100%" class="admintable">
		<tr>
			<td width="300" style="width: 300px;" align="right" class="key"><span class="hasTip" title="<?php echo JText::_('RST_KB_CATEGORY_NAME_DESC'); ?>"><label for="name"><?php echo JText::_('RST_KB_CATEGORY_NAME'); ?></label></span></td>
			<td><input type="text" name="name" value="<?php echo $this->escape($this->row->name); ?>" id="name" size="120" maxlength="255" /></td>
		</tr>
		<tr>
			<td width="300" style="width: 300px;" align="right" class="key"><span class="hasTip" title="<?php echo JText::_('RST_KB_CATEGORY_PARENT_DESC'); ?>"><label for="parent_id"><?php echo JText::_('RST_KB_CATEGORY_PARENT'); ?></label></span></td>
			<td><?php echo $this->lists['categories']; ?></td>
		</tr>
		<tr>
			<td width="300" style="width: 300px;" align="right" class="key"><span class="hasTip" title="<?php echo JText::_('RST_KB_CATEGORY_DESCRIPTION_DESC'); ?>"><label for="description"><?php echo JText::_('RST_KB_CATEGORY_DESCRIPTION'); ?></label></span></td>
			<td><?php echo $this->editor->display('description',$this->row->description,500,250,70,10); ?></td>
		</tr>
		<tr>
			<td width="300" style="width: 300px;" align="right" class="key"><span class="hasTip" title="<?php echo JText::_('RST_KB_CATEGORY_ICON_DESC'); ?>"><label for="name"><?php echo JText::_('RST_KB_CATEGORY_ICON'); ?></label></span></td>
			<td>
			<?php if ($this->row->thumb) { ?>
				<p><img src="<?php echo JURI::root(); ?>components/com_rsticketspro/assets/thumbs/small/<?php echo $this->row->thumb; ?>?sid=<?php echo md5(uniqid('random')); ?>" alt="" /></p>
				<p><input type="checkbox" name="delete_thumb" value="1" id="delete_thumb" /> <label for="delete_thumb"><?php echo JText::_('RST_DELETE_THUMB'); ?></label></p>
				<input type="hidden" name="thumb" value="<?php echo $this->escape($this->row->thumb); ?>" />
			<?php } ?>
			<input type="file" name="thumb" value="" />
			</td>
		</tr>
		<tr>
			<td width="300" style="width: 300px;" align="right" class="key"><span class="hasTip" title="<?php echo JText::_('RST_KB_CATEGORY_META_DESCRIPTION_DESC'); ?>"><label for="meta_description"><?php echo JText::_('RST_KB_CATEGORY_META_DESCRIPTION'); ?></label></span></td>
			<td><textarea cols="80" rows="10" class="text_area" type="text" name="meta_description" id="meta_description"><?php echo $this->escape($this->row->meta_description); ?></textarea></td>
		</tr>
		<tr>
			<td width="300" style="width: 300px;" align="right" class="key"><span class="hasTip" title="<?php echo JText::_('RST_KB_CATEGORY_META_KEYWORDS_DESC'); ?>"><label for="meta_keywords"><?php echo JText::_('RST_KB_CATEGORY_META_KEYWORDS'); ?></label></span></td>
			<td><textarea cols="80" rows="10" class="text_area" type="text" name="meta_keywords" id="meta_keywords"><?php echo $this->escape($this->row->meta_keywords); ?></textarea></td>
		</tr>
		<tr>
			<td width="300" style="width: 300px;" align="right" class="key"><span class="hasTip" title="<?php echo JText::_('RST_PRIVATE_DESC'); ?>"><label for="private"><?php echo JText::_('RST_PRIVATE'); ?></label></span></td>
			<td><?php echo $this->lists['private']; ?></td>
		</tr>
		<tr>
			<td width="300" style="width: 300px;" align="right" class="key"><span class="hasTip" title="<?php echo JText::_('PUBLISHED_DESC'); ?>"><label for="published"><?php echo JText::_('PUBLISHED'); ?></label></span></td>
			<td><?php echo $this->lists['published']; ?></td>
		</tr>
	</table>
</div>

<?php echo JHTML::_('form.token'); ?>
<input type="hidden" name="option" value="com_rsticketspro" />
<input type="hidden" name="controller" value="kbcategories" />
<input type="hidden" name="task" value="edit" />
<input type="hidden" name="view" value="kbcategories" />

<input type="hidden" name="id" value="<?php echo $this->row->id; ?>" />
</form>

<?php
//keep session alive while editing
JHTML::_('behavior.keepalive');
?>