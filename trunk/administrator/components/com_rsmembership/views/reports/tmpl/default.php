<?php
/**
* @version 1.0.0
* @package RSMembership! 1.0.0
* @copyright (C) 2009-2010 www.rsjoomla.com
* @license GPL, http://www.gnu.org/licenses/gpl-2.0.html
*/


defined('_JEXEC') or die('Restricted access');
JHTML::_('behavior.modal');
JHTML::_('behavior.tooltip');
?>

<script type="text/javascript">
function rsm_build_graph(thewidth, theheight)
{
	var thecolors = new Array();
	
	var report = $('report').value;

	if(report == 'report_2') {
		<?php for ($i=0; $i<4; $i++) { ?>
				if ($('transaction_type<?php echo $i; ?>').checked)
					thecolors.push($('color_transaction_types_<?php echo $i; ?>').value);
		<?php } ?>
	} else {
		<?php for ($i=0; $i<$this->color_pickers; $i++) { ?>
				if ($('membership<?php echo $i; ?>').checked)
					thecolors.push($('color_membership_<?php echo $i; ?>').value);
		<?php } ?>
	}

	if ($('rsm_reports_table'))
	{
		num = $('rsm_reports_table').getElements('tr').length;
		if (num <= 13)
			num = 13.5;
		var g = new Bluff.Line('graph', num*70 + 'x' + theheight);
		g.set_theme({
			colors: thecolors,
			marker_color: '#aea9a9',
			font_color: '#000000',
			background_colors: ['#ffffff', '#ffffff']
		  });
		g.tooltips = true;
		g.dot_radius = 1.5;
		g.legend_box_size = 7;
		g.line_width = 1;
		g.no_data_message = '';
		g.marker_font_size = '13px';
		g.title_font_size = '14px';
		g.legend_font_size = '14px';
		g.data_from_table('rsm_reports_table', {orientation: 'cols'});
		$('rsm_reports_table').style.display = 'none';
		g.draw();
	}
}

window.addEvent('domready', function() {	
<?php for ($i=0; $i<$this->color_pickers; $i++) { ?>
		new MooRainbow('change_color_membership_<?php echo $i; ?>i', {
			id: 'change_color_membership_<?php echo $i; ?>',
			imgPath: '<?php echo JURI::base(); ?>components/com_rsmembership/assets/images/rainbow/',
			startColor: rsm_hex_to_rgb($('color_membership_<?php echo $i; ?>').value),
			wheel: true,
			onChange: function(color) {
				$('color_membership_<?php echo $i; ?>').setStyle('background-color', color.hex);
				$('color_membership_<?php echo $i; ?>').value = color.hex;
			},
			onComplete: function(color) {
				$('color_membership_<?php echo $i; ?>').setStyle('background-color', color.hex);
				$('color_membership_<?php echo $i; ?>').value = color.hex;
			}
		});
<?php } ?>
<?php for ($i=0; $i<4; $i++) { ?>
		new MooRainbow('change_transaction_types_<?php echo $i; ?>i', {
			id: 'change_transaction_types_<?php echo $i; ?>',
			imgPath: '<?php echo JURI::base(); ?>components/com_rsmembership/assets/images/rainbow/',
			startColor: rsm_hex_to_rgb($('color_transaction_types_<?php echo $i; ?>').value),
			wheel: true,
			onChange: function(color){
				$('color_transaction_types_<?php echo $i; ?>').setStyle('background-color', color.hex);
				$('color_transaction_types_<?php echo $i; ?>').value = color.hex;
			},
			onComplete: function(color) {
				$('color_transaction_types_<?php echo $i; ?>').setStyle('background-color', color.hex);
				$('color_transaction_types_<?php echo $i; ?>').value = color.hex;
			}
		});
<?php } ?>
});
</script>
<table cellspacing="0" cellpadding="0" border="0" width="100%" class="admintable">
	<tr>
		<td width="260" style="width: 260px;" align="right" class="key"><label for="report"><?php echo JText::_('RSM_SELECT_REPORT'); ?></label></td>
		<td><?php echo $this->lists['report']; ?><?php echo $this->lists['viewin']; ?><button type="button" onclick="rsm_select_report();"><?php echo JText::_('RSM_VIEW_REPORT'); ?></button></td>
	</tr>
</table>

<table width="100%">
<tr>
	<td width="20%" valign="top">
		<form method="post" action="<?php echo JRoute::_('index.php?option=com_membership&view=reports'); ?>" name="adminForm" id="adminForm">
		<?php echo $this->pane->startPane('filter-pane'); ?>

			<?php echo $this->pane->startPanel(JText::_('RSM_REPORTS_TIME_PERIOD'), 'filter-time'); ?>
				<table cellspacing="0" cellpadding="0" border="0" width="100%" class="admintable">
					<tr>
						<td width="50" style="width: 50px;" align="right" class="key"><label for="rsm_from_calendar"><?php echo JText::_('RSM_REPORT_FROM'); ?></label></td>
						<td><?php echo $this->from_calendar; ?></td>
					</tr>
					<tr>
						<td width="50" style="width: 50px;" align="right" class="key"><label for="rsm_to_calendar"><?php echo JText::_('RSM_REPORT_TO'); ?></label></td>
						<td><?php echo $this->to_calendar; ?></td>
					</tr>
					<tr>
						<td width="50" style="width: 50px;" align="right" class="key"><label for="unit"><?php echo JText::_('RSM_REPORT_UNIT'); ?></label></td>
						<td><?php echo $this->lists['unit']; ?></td>
					</tr>
				</table>
			<?php echo $this->pane->endPanel(); ?>
			
			<?php echo $this->pane->startPanel(JText::_('RSM_REPORTS_CUSTOMER'), 'filter-customer'); ?>
				<form method="post" action="<?php echo JRoute::_('index.php?option=com_rsmembership&view=reports'); ?>" name="userform">
				<table cellspacing="0" cellpadding="0" border="0" width="100%" class="admintable">
					<tr>
						<td><a class="modal" id="email" href="<?php echo JRoute::_('index.php?option=com_rsmembership&view=allusers&tmpl=component'); ?>" rel="{handler: 'iframe', size: {x: 560, y: 375}}"><?php echo $this->customer; ?></a>
						<button type="button" onclick="document.getElementById('user_id').value = ''; document.getElementById('email').innerHTML = '<?php echo $this->customer; ?>'"><?php echo JText::_('RSM_CANCEL_CUSTOMER'); ?></button>
						</td>
					</tr>
				</table>
				<input type="hidden" name="user_id" id="user_id" value="<?php echo $this->user_id; ?>" />
				</form>
			<?php echo $this->pane->endPanel(); ?>
			<div id="memberships" style="display:block;">
			<?php echo $this->pane->startPanel(JText::_('RSM_REPORTS_MEMBERSHIP'), 'filter-membership'); ?>
				<table cellspacing="0" cellpadding="0" border="0" width="100%" class="admintable">
					<tr>
						<td width="50" style="width: 50px;" align="right" class="key"><label for="unit"><?php echo JText::_('RSM_MEMBERSHIP'); ?></label></td>
						<td><?php echo $this->lists['memberships']; ?></td>
					</tr>
				</table>
			<?php echo $this->pane->endPanel(); ?>
			</div>
			<div id="memberships_transactions" style="display:none;">
			<?php echo $this->pane->startPanel(JText::_('RSM_REPORTS_MEMBERSHIP'), 'filter-membership-transactions'); ?>
				<table cellspacing="0" cellpadding="0" border="0" width="100%" class="admintable">
					<tr>
						<td width="50" style="width: 50px;" align="right" class="key"><label for="unit"><?php echo JText::_('RSM_MEMBERSHIP'); ?></label></td>
						<td><?php echo $this->lists['memberships_transactions']; ?></td>
					</tr>
				</table>
			<?php echo $this->pane->endPanel(); ?>
			</div>
			<div id="status_memberships" style="display:none;">
			<?php echo $this->pane->startPanel(JText::_('RSM_REPORTS_STATUS'), 'filter-status-transactions'); ?>
				<table cellspacing="0" cellpadding="0" border="0" width="100%" class="admintable">
					<tr>
						<td width="50" style="width: 50px;" align="right" class="key"><label for="unit"><?php echo JText::_('RSM_STATUS'); ?></label></td>
						<td>
							<label><input type="checkbox" name="status_memberships[]" value="0" /><?php echo JText::_('RSM_STATUS_0'); ?></label><br />
							<label><input type="checkbox" name="status_memberships[]" value="1" /><?php echo JText::_('RSM_STATUS_1'); ?></label><br />
							<label><input type="checkbox" name="status_memberships[]" value="2" /><?php echo JText::_('RSM_STATUS_2'); ?></label><br />
							<label><input type="checkbox" name="status_memberships[]" value="3" /><?php echo JText::_('RSM_STATUS_3'); ?></label><br />
						</td>
					</tr>
				</table>
			<?php echo $this->pane->endPanel(); ?>
			</div>
			<div id="status_transactions" style="display:none;">
			<?php echo $this->pane->startPanel(JText::_('RSM_REPORTS_STATUS'), 'filter-status-transactions'); ?>
				<table cellspacing="0" cellpadding="0" border="0" width="100%" class="admintable">
					<tr>
						<td width="50" style="width: 50px;" align="right" class="key"><label for="unit"><?php echo JText::_('RSM_STATUS'); ?></label></td>
						<td>
							<label><input type="checkbox" name="status_transactions[]" value="pending" /><?php echo JText::_('RSM_TRANSACTION_STATUS_PENDING'); ?></label><br />
							<label><input type="checkbox" name="status_transactions[]" value="completed" /><?php echo JText::_('RSM_TRANSACTION_STATUS_COMPLETED'); ?></label><br />
							<label><input type="checkbox" name="status_transactions[]" value="denied" /><?php echo JText::_('RSM_TRANSACTION_STATUS_DENIED'); ?></label><br />
						</td>
					</tr>
				</table>
			<?php echo $this->pane->endPanel(); ?>
			</div>
			<?php echo $this->pane->startPanel(JText::_('RSM_REPORTS_PRICE'), 'filter-price'); ?>
				<table cellspacing="0" cellpadding="0" border="0" width="100%" class="admintable">
					<tr>
						<td width="50" style="width: 50px;" align="right" class="key"><label for="unit"><?php echo JText::_('RSM_PRICE_FROM'); ?></label></td>
						<td><input type="text" name="price_from" value="0" id="price_from"></td>
					</tr>
					<tr>
						<td width="50" style="width: 50px;" align="right" class="key"><label for="unit"><?php echo JText::_('RSM_PRICE_TO'); ?></label></td>
						<td><input type="text" name="price_to" value="" id="price_to"></td>
					</tr>
				</table>
			<?php echo $this->pane->endPanel(); ?>
			<div id="rsm_transactions_filters" style="display:<?php echo ( !empty($this->report) && $this->report == 2 ? 'block' : 'none');?>;">
			<?php echo $this->pane->startPanel(JText::_('RSM_REPORTS_TRANSACTION_TYPES'), 'filter-transaction-type'); ?>
				<table cellspacing="0" cellpadding="0" border="0" width="100%" class="admintable">
					<tr>
						<td width="50" style="width: 35px;" align="right" class="key"><label for="unit"><?php echo JText::_('RSM_ALL_TRANSACTION_TYPES'); ?></label></td>
						<td><?php echo $this->lists['transaction_types']; ?></td>
					</tr>
				</table>
			<?php echo $this->pane->endPanel(); ?>
			<?php echo $this->pane->startPanel(JText::_('RSM_REPORTS_GATEWAY'), 'filter-gateway'); ?>
				<table cellspacing="0" cellpadding="0" border="0" width="100%" class="admintable">
					<tr>
						<td width="50" style="width: 50px;" align="right" class="key"><label for="unit"><?php echo JText::_('RSM_ALL_GATEWAYS'); ?></label></td>
						<td>
							<td><?php echo $this->gateways; ?></td>
						</td>
					</tr>
				</table>
			<?php echo $this->pane->endPanel(); ?>
			</div>
		<?php echo $this->pane->endPane(); ?>
		</form>

		<div align="center"><button type="button" onclick="rsm_refresh_report();"><?php echo JText::_('RSM_REPORTS_REFRESH_GRAPH'); ?></button></div>
	</td>
	<td width="75%" valign="top">
		<div>
			<?php echo JHTML::_('image', JURI::base().'components/com_rsmembership/assets/images/loading.gif', JText::_('Loading'), 'id="rsm_loading"'); ?>
		</div>
		<div id="rsm_report">
			<?php echo $this->loadTemplate('no_report'); ?>
		</div>
		<div id="rsm_legend_container">
			<div id="rsm_legend">
			</div>
		</div>
		
		<span class="rsmembership_clear"></span>
	</td>
</tr>
</table>

<script type="text/javascript">
// check custom fields of report
var filter = document.getElementById("report");
if(filter.options[filter.selectedIndex].value == 'report_2' )
{
	document.getElementById('rsm_transactions_filters').style.display = 'block';
	document.getElementById('memberships_transactions').style.display = 'block';
	document.getElementById('memberships').style.display = 'none';
	document.getElementById('status_memberships').style.display = 'none';
	document.getElementById('status_transactions').style.display = 'block';
}
function rsm_check_report(id)
{
	if (id == 'report_2')
	{
		document.getElementById('rsm_transactions_filters').style.display = 'block';
		document.getElementById('memberships_transactions').style.display = 'block';
		document.getElementById('memberships').style.display = 'none';
		document.getElementById('status_transactions').style.display = 'block';
		document.getElementById('status_memberships').style.display = 'none';
	}
	else
	{
		document.getElementById('rsm_transactions_filters').style.display = 'none';
		document.getElementById('memberships_transactions').style.display = 'none';
		document.getElementById('memberships').style.display = 'block';
		document.getElementById('status_transactions').style.display = 'none';
		document.getElementById('status_memberships').style.display = 'block';
	}
}

</script>

<?php
//keep session alive while editing
JHTML::_('behavior.keepalive');
?>