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
window.addEvent('domready', function() {
	new MooRainbow('change_color_bgi', {
		id: 'change_color_bg',
		imgPath: '<?php echo JURI::base(); ?>components/com_rsticketspro/assets/images/rainbow/',
		startColor: rst_hex_to_rgb($('bg_color').value),
		wheel: true,
		onChange: function(color) {
			$('bg_color').setStyle('background-color', color.hex);
			$('bg_color').value = color.hex;
		},
		onComplete: function(color) {
			$('bg_color').setStyle('background-color', color.hex);
			$('bg_color').value = color.hex;
		}
	});
	
	$('bg_color').setStyle('background-color', $('bg_color').value);
	
	new MooRainbow('change_color_fgi', {
		id: 'change_color_fg',
		imgPath: '<?php echo JURI::base(); ?>components/com_rsticketspro/assets/images/rainbow/',
		startColor: rst_hex_to_rgb($('fg_color').value),
		wheel: true,
		onChange: function(color) {
			$('fg_color').setStyle('background-color', color.hex);
			$('fg_color').value = color.hex;
		},
		onComplete: function(color) {
			$('fg_color').setStyle('background-color', color.hex);
			$('fg_color').value = color.hex;
		}
	});
	
	$('fg_color').setStyle('background-color', $('fg_color').value);
});

function rst_hex_to_rgb(h)
{
	h = h.charAt(0)=="#" ? h.substring(1,7) : h;
	r = parseInt(h.substring(0,2),16);
	g = parseInt(h.substring(2,4),16);
	b = parseInt(h.substring(4,6),16);
	
	if (isNaN(r))
		r = 255;
	if (isNaN(g))
		g = 255;
	if (isNaN(b))
		b = 255;
	
	return [r, g, b];
}
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
		alert('<?php echo JText::_('RST_PRIORITY_NAME_ERROR', true); ?>');
	else
		submitform(pressbutton);
}

<?php if (RSTicketsProHelper::isJ16()) { ?>
	Joomla.submitbutton = submitbutton;
<?php } ?>
</script>

<form action="<?php echo JRoute::_('index.php?option=com_rsticketspro&controller=priorities&task=edit'); ?>" method="post" name="adminForm" id="adminForm">
<div class="col100">
	<table cellspacing="0" cellpadding="0" border="0" width="100%" class="admintable">
		<tr>
			<td width="300" style="width: 300px;" align="right" class="key"><span class="hasTip" title="<?php echo JText::_('RST_PRIORITY_DESC'); ?>"><label for="name"><?php echo JText::_('RST_PRIORITY'); ?></label></span></td>
			<td><input type="text" name="name" value="<?php echo $this->escape($this->row->name); ?>" id="name" size="120" maxlength="255" /></td>
		</tr>
		<tr>
			<td width="300" style="width: 300px;" align="right" class="key"><span class="hasTip" title="<?php echo JText::_('RST_PRIORITY_BACKGROUND_COLOR_DESC'); ?>"><label for="name"><?php echo JText::_('RST_PRIORITY_BACKGROUND_COLOR'); ?></label></span></td>
			<td>
			<input type="text" name="bg_color" value="<?php echo $this->escape($this->row->bg_color); ?>" id="bg_color" size="10" maxlength="255" />
			<img id="change_color_bgi" class="rst_color" src="<?php echo JURI::base().'components/com_rsticketspro/assets/images/rainbow/color.gif'; ?>" alt="" />
			</td>
		</tr>
		<tr>
			<td width="300" style="width: 300px;" align="right" class="key"><span class="hasTip" title="<?php echo JText::_('RST_PRIORITY_FOREGROUND_COLOR_DESC'); ?>"><label for="name"><?php echo JText::_('RST_PRIORITY_FOREGROUND_COLOR'); ?></label></span></td>
			<td>
			<input type="text" name="fg_color" value="<?php echo $this->escape($this->row->fg_color); ?>" id="fg_color" size="10" maxlength="255" />
			<img id="change_color_fgi" class="rst_color" src="<?php echo JURI::base().'components/com_rsticketspro/assets/images/rainbow/color.gif'; ?>" alt="" />
			</td>
		</tr>
		<tr>
			<td width="300" style="width: 300px;" align="right" class="key"><span class="hasTip" title="<?php echo JText::_('PUBLISHED_DESC'); ?>"><label for="published"><?php echo JText::_('PUBLISHED'); ?></label></span></td>
			<td><?php echo $this->lists['published']; ?></td>
		</tr>
	</table>
</div>
	
<?php echo JHTML::_('form.token'); ?>
<input type="hidden" name="option" value="com_rsticketspro" />
<input type="hidden" name="controller" value="priorities" />
<input type="hidden" name="task" value="edit" />
<input type="hidden" name="view" value="priorities" />

<input type="hidden" name="id" value="<?php echo $this->row->id; ?>" />
</form>

<?php
//keep session alive while editing
JHTML::_('behavior.keepalive');
?>