<?php
/**
* @version 2.0.0
* @package RSTickets! Pro 2.0.0
* @copyright (C) 2010 www.rsjoomla.com
* @license GPL, http://www.gnu.org/licenses/gpl-2.0.html
*/

defined('_JEXEC') or die('Restricted access');
JHTML::_('behavior.tooltip');
JHTML::_('behavior.mootools');

$doc =& JFactory::getDocument();
$doc->addScript(JURI::root(true).'/components/com_rsticketspro/assets/js/mootooltips.js');
?>

<script type="text/javascript">
	<?php if (!RSTicketsProHelper::isJ16() && !JPluginHelper::isEnabled('system', 'mtupgrade')) { ?>
	MooTooltips.implement(new Options);
	Hash.implement({
		include: function(key, value){
			if (this[key] == undefined) this[key] = value;
			return this;
		}
	});
	Element.extend({
		store: function(property, value){
			if (typeof this.rsticketspro_storage == 'undefined')
				this.rsticketspro_storage = new Array();
				
			var storage = this.rsticketspro_storage;
			storage[property] = value;
			return this;
		},
		
		retrieve: function(property, dflt){
			if (typeof this.rsticketspro_storage == 'undefined')
				this.rsticketspro_storage = new Array();
				
			var storage = this.rsticketspro_storage, prop = storage[property];
			if (dflt != undefined && prop == undefined) prop = storage[property] = dflt;
			return $pick(prop);
		},
		
		set: function(props){
			for (var prop in props){
				var val = props[prop];
				switch(prop){
					case 'html': this.setHTML(val); break;
					case 'styles': this.setStyles(val); break;
					case 'events': if (this.addEvents) this.addEvents(val); break;
					case 'properties': this.setProperties(val); break;
					case 'morph': this.store('morph', val); break;
					default: this.setProperty(prop, val);
				}
			}
			return this;
		},
		
		morph: function(props){
			_props = this.retrieve('morph');
			var fx = new Fx.Styles(this, _props);
			fx.start(props);
		}
	});
	
	JSON.encode = function(obj) {
		return Json.toString(obj);
	}
	JSON.decode = function(str, secure) {
		return Json.evaluate(str, secure);
	}
	<?php } ?>
	window.addEvent('load', function(){
		new MooTooltips({
			extra:{  
				<?php if ($this->params->get('show_date', 1)) { ?>
				0: {
					'id':'rsticketspro_tip_date',
					'text':'<?php echo JHTML::image('components/com_rsticketspro/assets/images/icon5.png', ''); ?> <?php echo JText::_('RST_TICKET_DATE_TIP', true); ?>',
					'position':-1,
					'sticky':false
				},
				<?php } ?>
				<?php if ($this->params->get('show_last_reply', 1)) { ?>
				1: {
					'id':'rsticketspro_tip_last_reply',
					'text':'<?php echo JHTML::image('components/com_rsticketspro/assets/images/icon6.png', ''); ?> <?php echo JText::_('RST_TICKET_LAST_REPLY_TIP', true); ?>',
					'position':-1,
					'sticky':false
				},
				<?php } ?>
				<?php if ($this->is_staff) { ?>
				<?php if ($this->permissions->delete_ticket) { ?>
				2: {
					'id':'rsticketspro_tip_flagged',
					'text':'<?php echo JHTML::image('components/com_rsticketspro/assets/images/icon7.png', ''); ?> <?php echo JText::_('RST_TICKET_FLAG_TIP', true); ?>',
					'position':-1,
					'sticky':false
				},
				<?php } ?>
				3: {
					'id':'rsticketspro_tip_delete',
					'text':'<?php echo JHTML::image('components/com_rsticketspro/assets/images/icon8.png', ''); ?> <?php echo JText::sprintf('RST_TICKET_DELETE_TIP', JURI::root(true)); ?>',
					'position':-1,
					'sticky':false
				},
				<?php } ?>
				<?php if ($this->params->get('show_customer', 1)) { ?>
				4: {
					'id':'rsticketspro_tip_customer',
					'text':'<?php echo JHTML::image('components/com_rsticketspro/assets/images/icon10.png', ''); ?> <?php echo JText::_('RST_TICKET_CUSTOMER_TIP', true); ?>',
					'position':-1,
					'sticky':false
				},
				<?php } ?>
				<?php if ($this->params->get('show_priority', 1)) { ?>
				5: {
					'id':'rsticketspro_tip_priority',
					'text':'<?php echo JHTML::image('components/com_rsticketspro/assets/images/icon11.png', ''); ?> <?php echo JText::_('RST_TICKET_PRIORITY_TIP', true); ?>',
					'position':-1,
					'sticky':false
				},
				<?php } ?>
				<?php if ($this->params->get('show_status', 1)) { ?>
				6: {
					'id':'rsticketspro_tip_status',
					'text':'<?php echo JHTML::image('components/com_rsticketspro/assets/images/icon12.png', ''); ?> <?php echo JText::_('RST_TICKET_STATUS_TIP', true); ?>',
					'position':-1,
					'sticky':false
				},
				<?php } ?>
				<?php if ($this->params->get('show_staff', 1)) { ?>
				7: {
					'id':'rsticketspro_tip_staff',
					'text':'<?php echo JHTML::image('components/com_rsticketspro/assets/images/icon13.png', ''); ?> <?php echo JText::_('RST_TICKET_STAFF_TIP', true); ?>',
					'position':-1,
					'sticky':false
				},
				<?php } ?>
				8: {
					'id':'rsticketspro_tip_code_subject',
					'text':'<?php echo JHTML::image('components/com_rsticketspro/assets/images/icon9.png', ''); ?> <?php echo JText::_('RST_TICKET_CODE_SUBJECT_TIP', true); ?>',
					'position':-1,
					'sticky':false
				}
			},
			ToolTipClass:'rsticketspro_tip', // tooltip display class
			toolTipPosition:-1,
			sticky:false,
			fromTop: 0,
			fromLeft: -55,
			duration: 300,
			fadeDistance: 20
		});		
	});
</script>

<style type="text/css">
<?php foreach ($this->priorityColors as $priority) { ?>
	<?php if ($priority->bg_color) { ?>
	table tr.rst_priority_color_<?php echo $priority->id; ?> td<?php if (!$this->colorWholeTicket) { ?>.rst_priority_cell<?php } ?>
	{
		background-color: <?php echo $priority->bg_color; ?> !important;
	}
	<?php } ?>
	<?php if ($priority->fg_color) { ?>
	table tr.rst_priority_color_<?php echo $priority->id; ?> td<?php if (!$this->colorWholeTicket) { ?>.rst_priority_cell<?php } ?>, table.adminlist tr.rst_priority_color_<?php echo $priority->id; ?> td a<?php if (!$this->colorWholeTicket) { ?>.rst_priority_cell<?php } ?>
	{
		color: <?php echo $priority->fg_color; ?> !important;
	}
	<?php } ?>
<?php } ?>
</style>

<?php if (RSTicketsProHelper::isJ16()) { ?>
	<?php if ($this->params->get('show_page_heading', 1)) { ?>
		<h1><?php echo $this->escape($this->params->get('page_heading', $this->params->get('page_title'))); ?></h1>
	<?php } ?>
	<?php } else { ?>
	<?php if ($this->params->get('show_page_title', 1)) { ?>
		<div class="componentheading<?php echo $this->escape($this->params->get('pageclass_sfx')); ?>"><?php echo $this->escape($this->params->get('page_title')); ?></div>
	<?php } ?>
<?php } ?>

<?php echo RSTicketsProHelper::getConfig('global_message'); ?>

<?php if ($this->is_searching) { ?>
	<p><?php if ($this->is_staff) { ?><a href="<?php echo RSTicketsProHelper::route('index.php?option=com_rsticketspro&controller=searches&task=edit'); ?>" class="rst_search"><?php echo JText::_('RST_SAVE_SEARCH'); ?></a><?php } ?> <?php echo JText::sprintf('RST_RESET_SEARCH', RSTicketsProHelper::route('index.php?option=com_rsticketspro&task=resetsearch')); ?></p>
<?php } ?>
	<?php if ($this->has_searches) { ?>
	<p>
		<a href="<?php echo RSTicketsProHelper::route('index.php?option=com_rsticketspro&view=searches'); ?>" class="rst_manage_searches"><?php echo JText::_('RST_MANAGE_SEARCHES'); ?></a>
		<?php foreach ($this->searches as $search) { ?>
			<?php if ($search->id != $this->predefined_search) { ?>
			<a href="<?php echo RSTicketsProHelper::route('index.php?option=com_rsticketspro&controller=searches&task=search&cid='.$search->id); ?>" class="hasTip" title="<?php echo JText::sprintf('RST_SEARCH_CLICK_DESC', $this->escape($search->name)); ?>"><?php echo $this->escape($search->name); ?></a>
			<?php } else { ?>
				<?php echo $this->escape($search->name); ?>
			<?php } ?>
		<?php } ?>
	<?php } ?>
	</p>

<span class="rst_clear"></span>

<form action="<?php echo RSTicketsProHelper::route('index.php?option=com_rsticketspro&view=rsticketspro'); ?>" method="post" name="adminForm">
<?php if ($this->is_staff) { ?>
	<table class="rsticketspro_tablebig" width="100%" border="0" cellspacing="0" cellpadding="0" id="bulk_actions" style="display: none; margin-bottom: 15px;">
	<tr>
		<th colspan="6" align="center"><?php echo JHTML::image('components/com_rsticketspro/assets/images/iconbulk.png', ''); ?> <?php echo JText::_('RST_BULK_ACTIONS'); ?></th>
	</tr>
	<tr>
		<?php if ($this->permissions->assign_tickets) { ?>
			<td>
				<?php echo JText::_('RST_TICKET_STAFF'); ?><br />
				<?php echo $this->lists['staff']; ?>
			</td>
		<?php } ?>
		<?php if ($this->permissions->update_ticket) { ?>
			<td>
				<?php echo JText::_('RST_TICKET_PRIORITY'); ?><br />
				<?php echo $this->lists['priority']; ?>
			</td>
		<?php } ?>
		<?php if ($this->permissions->change_ticket_status) { ?>
			<td>
				<?php echo JText::_('RST_TICKET_STATUS'); ?><br />
				<?php echo $this->lists['status']; ?>
			</td>
		<?php } ?>
			<td>
				<?php echo JText::_('RST_TICKET_NOTIFY'); ?><br />
				<?php echo $this->lists['notify']; ?>
			</td>
		<?php if ($this->permissions->delete_ticket) { ?>
			<td>
				<?php echo JText::_('RST_TICKET_DELETE'); ?><br />
				<?php echo $this->lists['delete']; ?>
			</td>
		<?php } ?>
		<td>
			<button type="submit" id="rst_update_button" class="button"><?php echo JText::_('RST_UPDATE'); ?></button>
		</td>
	</tr>
	</table>
<?php } ?>

<table width="100%" border="0" cellspacing="0" cellpadding="0" class="rsticketspro_tablebig">
<?php if ($this->params->get('show_headings', 1)) { ?>
<tr>
	<?php if ($this->params->get('show_offset', 1)) { ?>
		<th width="1%" align="center"><?php echo JText::_('#'); ?></th>
	<?php } ?>
	<?php if ($this->is_staff) { ?>
		<th align="center" width="1%"><input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count($this->tickets); ?>);<?php if ($this->is_staff) { ?> rst_show_bulk();<?php } ?>"/></th>
	<?php } ?>
	<?php if ($this->params->get('show_date', 1)) { ?>
		<th nowrap="nowrap" align="center"><span id="rsticketspro_tip_date"><?php echo JHTML::_('rsticketsprogrid.sort', 'RST_TICKET_DATE', 'date', $this->sortOrder, $this->sortColumn, 'none'); ?></span></th>
	<?php } ?>
	<?php if ($this->params->get('show_last_reply', 1)) { ?>
		<th nowrap="nowrap" align="center"><span id="rsticketspro_tip_last_reply"><?php echo JHTML::_('rsticketsprogrid.sort', 'RST_TICKET_LAST_REPLY', 'last_reply', $this->sortOrder, $this->sortColumn, 'none'); ?></span></th>
	<?php } ?>
	<?php if ($this->is_staff) { ?>
	<th nowrap="nowrap" align="center" width="1%"><span id="rsticketspro_tip_flagged"><?php echo JHTML::image('components/com_rsticketspro/assets/images/icon7.png', ''); ?></span></th>
		<?php if ($this->permissions->delete_ticket) { ?>
		<th nowrap="nowrap" align="center" width="1%"><span id="rsticketspro_tip_delete"><?php echo JHTML::image('components/com_rsticketspro/assets/images/icon8.png', ''); ?></span></th>
		<?php } ?>
	<?php } ?>
	<th nowrap="nowrap" align="center"><span id="rsticketspro_tip_code_subject"><?php if ($this->params->get('show_code', 1)) echo JHTML::_('rsticketsprogrid.sort', 'RST_TICKET_CODE', 'code', $this->sortOrder, $this->sortColumn, 'none'); ?> <?php echo JHTML::_('rsticketsprogrid.sort', 'RST_TICKET_SUBJECT', 'subject', $this->sortOrder, $this->sortColumn, 'none'); ?></span></th>
	<?php if ($this->params->get('show_customer', 1)) { ?>
		<th nowrap="nowrap" align="center"><span id="rsticketspro_tip_customer"><?php echo JHTML::_('rsticketsprogrid.sort', 'RST_TICKET_CUSTOMER', 'customer', $this->sortOrder, $this->sortColumn, 'none'); ?></span></th>
	<?php } ?>
	<?php if ($this->params->get('show_priority', 1)) { ?>
		<th nowrap="nowrap" align="center" width="1%"><span id="rsticketspro_tip_priority"><?php echo JHTML::_('rsticketsprogrid.sort', 'RST_TICKET_PRIORITY', 'pr.ordering', $this->sortOrder, $this->sortColumn, 'none'); ?></span></th>
	<?php } ?>
	<?php if ($this->params->get('show_status', 1)) { ?>
		<th nowrap="nowrap" align="center" width="1%"><span id="rsticketspro_tip_status"><?php echo JHTML::_('rsticketsprogrid.sort', 'RST_TICKET_STATUS', 'st.ordering', $this->sortOrder, $this->sortColumn, 'none'); ?></span></th>
	<?php } ?>
	<?php if ($this->params->get('show_staff', 1)) { ?>
		<th nowrap="nowrap" align="center"><span id="rsticketspro_tip_staff"><?php echo JHTML::_('rsticketsprogrid.sort', 'RST_TICKET_STAFF', 'staff', $this->sortOrder, $this->sortColumn, 'none'); ?></span></th>
	<?php } ?>
</tr>
<?php } ?>

<?php $k = 1; ?>
<?php $i = 0; ?>
<?php foreach ($this->tickets as $item) {
if ($this->is_staff)
{
	$grid = JHTML::_('grid.id', $i, $item->id);
	if (RSTicketsProHelper::isJ25())
		$grid = str_replace('Joomla.isChecked', 'rst_show_bulk(); Joomla.isChecked', $grid);
	else
		$grid = str_replace('isChecked', 'rst_show_bulk(); isChecked', $grid);
}
?>
<tr class="rst_priority_color_<?php echo $item->priority_id; ?>">
	<?php if ($this->params->get('show_offset', 1)) { ?>
	<td width="1%"><?php echo $this->pagination->getRowOffset($i); ?></td>
	<?php } ?>
	<?php if ($this->is_staff) { ?>
		<td><?php echo $grid; ?></td>
	<?php } ?>
	<?php if ($this->params->get('show_date', 1)) { ?>
		<td><?php echo $this->escape(date($this->date_format, RSTicketsProHelper::getCurrentDate($item->date))); ?></td>
	<?php } ?>
	<?php if ($this->params->get('show_last_reply', 1)) { ?>
		<td><?php echo $this->escape(date($this->date_format, RSTicketsProHelper::getCurrentDate($item->last_reply))); ?></td>
	<?php } ?>
		<?php if ($this->is_staff) { ?>
		<td align="center"><a href="javascript: void(0);" onclick="rst_flag_ticket('<?php echo JRoute::_('index.php', false); ?>', this, '<?php echo $item->id; ?>');" class="rst_flag<?php echo $item->flagged ? ' rst_flag_active' : ''; ?>"></a></td>
			<?php if ($this->permissions->delete_ticket) { ?>
			<td align="center"><?php echo JHTML::_('rsticketsproicon.deleteticket', $item->id, $this->is_staff, $this->permissions); ?></td>
			<?php } ?>
		<?php } ?>
		<td>
		<?php if ($item->has_files) { ?>
			<?php echo JHTML::image('components/com_rsticketspro/assets/images/attach.png', JText::_('RST_THIS_TICKET_HAS_ATTACHMENTS'), 'title="'.JText::_('RST_THIS_TICKET_HAS_ATTACHMENTS').'" class="hasTip"'); ?>
		<?php } ?>
		<?php if ($this->params->get('show_code', 1)) { ?>
			<a href="<?php echo RSTicketsProHelper::route('index.php?option=com_rsticketspro&view=ticket&cid='.$item->id.':'.JFilterOutput::stringURLSafe($item->subject)); ?>"><?php echo $item->code; ?></a>
			<?php if ($this->params->get('show_replies', 1)) { ?>
				(<?php echo $item->replies; ?>)
			<?php } ?>
			<br />
		<?php } ?>
			<a href="<?php echo RSTicketsProHelper::route('index.php?option=com_rsticketspro&view=ticket&cid='.$item->id.':'.JFilterOutput::stringURLSafe($item->subject)); ?>"><?php echo $this->escape($item->subject); ?></a>
		<?php if (!$this->params->get('show_code', 1) && $this->params->get('show_replies', 1)) { ?>
			(<?php echo $item->replies; ?>)
		<?php } ?>
			<?php echo JHTML::_('rsticketsproicon.notify', $this->is_staff, $item); ?>
		</td>
	<?php if ($this->params->get('show_customer', 1)) { ?>
		<td><?php echo $this->escape($item->customer); ?></td>
	<?php } ?>
	<?php if ($this->params->get('show_priority', 1)) { ?>
		<td class="rst_priority_cell"><?php echo JText::_($item->priority); ?></td>
	<?php } ?>
	<?php if ($this->params->get('show_status', 1)) { ?>
		<td><?php echo JText::_($item->status); ?></td>
	<?php } ?>
	<?php if ($this->params->get('show_staff', 1)) { ?>
		<td><?php echo $item->staff_id ? $this->escape($item->staff) : '<em>'.JText::_('RST_UNASSIGNED').'</em>'; ?></td>
	<?php } ?>
</tr>
<?php $k = $k == 1 ? 2 : 1; ?>
<?php $i++; ?>
<?php } ?>
<?php if ($this->pagination->get('pages.total') > 1) { ?>
<tr>
	<td align="center" colspan="10" class="sectiontablefooter<?php echo $this->escape($this->params->get('pageclass_sfx')); ?>">
	<div class="pagination"><?php echo $this->pagination->getPagesLinks(); ?></div>
	</td>
</tr>
<tr>
	<td colspan="10" align="center"><?php echo $this->pagination->getPagesCounter(); ?></td>
</tr>
<?php } ?>
</table>

<?php if ($this->show_footer) { ?>
	<?php echo $this->footer; ?>
<?php } ?>

<input type="hidden" name="boxchecked" value="0" />
<input type="hidden" name="filter_order" value="<?php echo $this->escape($this->sortColumn); ?>" />
<input type="hidden" name="filter_order_Dir" value="<?php echo $this->escape($this->sortOrder); ?>" />
<input type="hidden" name="limitstart" value="<?php echo $this->escape($this->limitstart); ?>" />

<input type="hidden" name="task" value="updatetickets" />
</form>

<?php if ($this->is_staff) { ?>
<script type="text/javascript">
function rst_show_bulk()
{
	var show_bulk = false;
	
	for (var i=0; i<<?php echo count($this->tickets); ?>; i++)
		if (document.getElementById('cb' + i).checked)
		{
			show_bulk = true;
			break;
		}
	
	document.getElementById('bulk_actions').style.display = show_bulk ? '' : 'none';
}

function rst_disable_bulk(value)
{
	if (value == 0)
	{
		<?php if ($this->permissions->assign_tickets) { ?>
		document.getElementById('bulk_staff_id').disabled = false;
		<?php } ?>
		<?php if ($this->permissions->update_ticket) { ?>
		document.getElementById('bulk_priority_id').disabled = false;
		<?php } ?>
		<?php if ($this->permissions->change_ticket_status) { ?>
		document.getElementById('bulk_status_id').disabled = false;
		<?php } ?>
		document.getElementById('bulk_notify').disabled = false;
		
		<?php if ($this->permissions->delete_ticket) { ?>
		document.getElementById('rst_update_button').onclick = function () { return true; };
		<?php } ?>
	}
	else
	{
		<?php if ($this->permissions->assign_tickets) { ?>
		document.getElementById('bulk_staff_id').disabled = true;
		<?php } ?>
		<?php if ($this->permissions->update_ticket) { ?>
		document.getElementById('bulk_priority_id').disabled = true;
		<?php } ?>
		<?php if ($this->permissions->change_ticket_status) { ?>
		document.getElementById('bulk_status_id').disabled = true;
		<?php } ?>
		document.getElementById('bulk_notify').disabled = true;
		
		<?php if ($this->permissions->delete_ticket) { ?>
		document.getElementById('rst_update_button').onclick = function () { return confirm('<?php echo JText::_('RST_DELETE_TICKETS_CONFIRM', true); ?>'); };
		<?php } ?>
	}
}
</script>
<?php } ?>

<?php JHTML::_('behavior.keepalive'); ?>