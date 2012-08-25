<?php
/**
* @version 2.0.0
* @package RSTickets! Pro 2.0.0
* @copyright (C) 2010 www.rsjoomla.com
* @license GPL, http://www.gnu.org/licenses/gpl-2.0.html
*/

defined('_JEXEC') or die('Restricted access');

JHTML::_('behavior.mootools');
JHTML::_('behavior.tooltip');
JHTML::_('behavior.modal');

// Ratings
$doc =& JFactory::getDocument();

$doc->addStyleSheet(JURI::root(true).'/components/com_rsticketspro/assets/css/ratings.css');
$doc->addScript(JURI::root(true).'/components/com_rsticketspro/assets/js/ratings.js');

if (!RSTicketsProHelper::isJ16() && !JPluginHelper::isEnabled('system', 'mtupgrade')) { ?>
<script type="text/javascript">
Element.extend({
	get: function(prop){
		return this.getProperty(prop);
		theprop = this.getAttribute(prop);
		alert(theprop);
		return theprop;
	},
	
	getAllPrevious: function(match, nocash){
		return walk(this, 'previousSibling', null, match, true, nocash);
	},
	
	getAllNext: function(match, nocash){
		return walk(this, 'nextSibling', null, match, true, nocash);
	},
	
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
	}
});

var walk = function(element, walk, start, match, all, nocash){
	var el = element[start || walk];
	var elements = [];
	while (el){
		if (el.nodeType == 1 && (!match || Element.match(el, match))){
			if (!all) return document.id(el, nocash);
			elements.push(el);
		}
		el = el[walk];
	}
	return (all) ? new Elements(elements, {ddup: false, cash: !nocash}) : null;
};
mooRatings.implement(new Options);
Tabs.implement(new Options);
Tabs.implement(new Events);
ElementSwap.implement(new Options);
ElementSwap.implement(new Events);
</script>
<?php }

if ($this->ticket_view == 'tabbed')
{
	$doc->addStyleSheet(JURI::root(true).'/components/com_rsticketspro/assets/css/tabs.css');
	$doc->addScript(JURI::root(true).'/components/com_rsticketspro/assets/js/swap.js');
	$doc->addScript(JURI::root(true).'/components/com_rsticketspro/assets/js/tabs.js');
}
?>

<?php if (RSTicketsProHelper::isJ16()) { ?>
	<?php if ($this->params->get('show_page_heading', 1)) { ?>
		<h1><?php echo $this->escape($this->row->subject); ?></h1>
	<?php } ?>
	<?php } else { ?>
	<?php if ($this->params->get('show_page_title', 1)) { ?>
		<div class="componentheading<?php echo $this->escape($this->params->get('pageclass_sfx')); ?>"><?php echo $this->escape($this->row->subject); ?></div>
	<?php } ?>
<?php } ?>

<?php echo RSTicketsProHelper::getConfig('global_message'); ?>

<?php if ($this->ticket_view == 'plain' || $this->do_print) { ?>
<div class="rsticketspro_halfbox rsticketspro_fullbox">
	<?php echo $this->loadTemplate('reply'); ?>
	<div id="rsticketspro_accordion">
		<?php echo $this->loadTemplate('info'); ?>
		<?php if ($this->show_time_spent) { ?>
			<?php echo $this->loadTemplate('time'); ?>
		<?php } ?>
		<?php if (!empty($this->row->custom_fields)) { ?>
			<?php echo $this->loadTemplate('custom_fields'); ?>
		<?php } ?>
		<?php if ($this->show_ticket_info && $this->is_staff) { ?>
			<?php echo $this->loadTemplate('submitter'); ?>
		<?php } ?>
		<?php if ($this->is_staff) { ?>
			<?php echo $this->loadTemplate('history'); ?>
		<?php } ?>
	</div>
	<?php echo $this->loadTemplate('messages'); ?>
</div>

<?php } elseif ($this->ticket_view == 'tabbed') { ?>

<div class="rsticketspro_halfbox rsticketspro_fullbox">
	<div id="tabcontainer">
		<ul class="tabs_title"> 
			<li><?php echo JText::_('RST_TICKET_MESSAGES'); ?></li>
			<li><?php echo JText::_('RST_TICKET_INFORMATION'); ?></li>
			<?php if ($this->show_time_spent) { ?>
			<li><?php echo JText::_('RST_TIME_SPENT'); ?></li>
			<?php } ?>
			<?php if ($this->show_ticket_info && $this->is_staff) { ?>
			<li><?php echo JText::_('RST_SUBMITTER_INFORMATION'); ?></li>
			<?php } ?>
			<?php if (!empty($this->row->custom_fields)) { ?>
			<li><?php echo JText::_('RST_TICKET_CUSTOM_FIELDS'); ?></li>
			<?php } ?>
			<?php if ($this->is_staff) { ?>
			<li><?php echo JText::_('RST_TICKET_HISTORY'); ?></li>			
			<?php } ?>
		</ul>

		<div class="tabs_panel">
			<?php echo $this->loadTemplate('reply'); ?>
			<?php echo $this->loadTemplate('messages'); ?>
		</div>
		
		<div class="tabs_panel">
			<?php echo $this->loadTemplate('info'); ?>
		</div>
		
		<?php if ($this->show_time_spent) { ?>
		<div class="tabs_panel">
			<?php echo $this->loadTemplate('time'); ?>
		</div>
		<?php } ?>
		
		<?php if ($this->show_ticket_info && $this->is_staff) { ?>
		<div class="tabs_panel">
			<?php echo $this->loadTemplate('submitter'); ?>
		</div>
		<?php } ?>
		
		<?php if (!empty($this->row->custom_fields)) { ?>
		<div class="tabs_panel">
			<?php echo $this->loadTemplate('custom_fields'); ?>
		</div>
		<?php } ?>
		
		<?php if ($this->is_staff) { ?>
		<div class="tabs_panel">
			<?php echo $this->loadTemplate('history'); ?>
		</div>
		<?php } ?>
	</div>
</div>
	
<?php } ?>
	
<script type="text/javascript">
<?php if ($this->ticket_view == 'tabbed') { ?>
window.addEvent('domready', function() {
	var myTabs = new Tabs('tabs');
});
<?php } ?>

function rst_show_ticket_reply(what)
{
	what.style.display = 'none';
	$('rst_ticket_reply').style.display = '';
}

function rst_update_editor(content)
{
	<?php echo $this->editor_javascript; ?>
}

function rst_feedback_message()
{
	document.getElementById('rst_feedback_message').innerHTML = '<?php echo JText::_('RST_TICKET_FEEDBACK_SENT', true); ?>';
}

function rst_add_attachments()
{
	<?php if ($this->department->upload_files) { ?>
	if (document.getElementsByName('rst_files[]').length >= <?php echo $this->department->upload_files; ?>)
	{
		alert('<?php echo JText::_('RST_MAX_UPLOAD_FILES_REACHED', true); ?>');
		return false;
	}
	<?php } ?>
	var label = document.createElement('label');
	label.setAttribute('class', 'float_left');
	label.className = 'float_left';
	label.innerHTML = '&nbsp;';
	document.getElementById('rst_files').appendChild(label);

	var new_upload = document.createElement('input');
	new_upload.setAttribute('name', 'rst_files[]');
	new_upload.setAttribute('type', 'file');
	document.getElementById('rst_files').appendChild(new_upload);
	
	var new_br = document.createElement('br');
	document.getElementById('rst_files').appendChild(new_br);
}

<?php if ($this->ticket_view == 'plain' && !$this->do_print) { ?>
window.addEvent('domready', function(){
  new Fx.Accordion($('rsticketspro_accordion'), '#rsticketspro_accordion p.rsticketspro_title', '#rsticketspro_accordion div.rsticketspro_content', {
		display: -1,
		alwaysHide: true
	});
});
<?php } ?>

<?php if ($this->do_print) { ?>
	window.print();
<?php } ?>
</script>

<?php JHTML::_('behavior.keepalive'); ?>