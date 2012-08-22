<?php
/**
* @version 2.0.0
* @package RSTickets! Pro 2.0.0
* @copyright (C) 2010 www.rsjoomla.com
* @license GPL, http://www.gnu.org/licenses/gpl-2.0.html
*/

defined('_JEXEC') or die('Restricted access');

JHTML::_('behavior.tooltip');
$fullbox = $this->params->get('show_tickets', 1) == 0 || $this->params->get('show_kb', 1) == 0 ? ' rsticketspro_fullbox' : '';
?>

<script type="text/javascript">
window.addEvent('domready', function(){

	$('rsticketspro_search_results').setStyle('height','auto');
	$('rsticketspro_loading').setStyle('display', 'none');
	
	RSTSearch = new Fx.Slide('rsticketspro_search_results').hide();
	
	$('rsticketspro_searchinp').addEvent('keydown', function() {
		clearTimeout(this.timeout);
		if (this.value.length == 0 || this.value == '<?php echo $this->escape(JText::_('RST_SEARCH_HELPDESK', true)); ?>')
		{
			clearTimeout(this.timeout);
			RSTSearch.slideOut();
			return true;
		}
		this.timeout = setTimeout('rsticketspro_search();', 1000);
	});
	
	$('rsticketspro_searchinp').addEvent('focus', function() {
		if (this.value=='<?php echo $this->escape(JText::_('RST_SEARCH_HELPDESK', true)); ?>')
		{
			clearTimeout(this.timeout);
			this.value='';
			RSTSearch.slideOut();
		}
	});
	
	$('rsticketspro_searchinp').addEvent('blur', function() {
		if (this.value=='')
		{
			clearTimeout(this.timeout);
			this.value='<?php echo $this->escape(JText::_('RST_SEARCH_HELPDESK', true)); ?>';
			RSTSearch.slideOut();
		}
	});
});

function rsticketspro_search()
{
	xmlHttp = rst_get_xml_http_object();
	
	$('rsticketspro_loading').setStyle('display', 'block');
	
	var url = '<?php echo JRoute::_('index.php?option=com_rsticketspro&format=raw&task=dashboardsearch', false); ?>';
		url += (url.indexOf('?') > -1 ? '&' : '?') + 'filter=' + $('rsticketspro_searchinp').value;
		url += '&sid=' + Math.random();
		
	xmlHttp.onreadystatechange = function() {
			if (xmlHttp.readyState==4)
			{
				$('rsticketspro_loading').setStyle('display', 'none');
				if (xmlHttp.responseText.indexOf('<!-- rsticketspro_results -->') > -1)
				{
					if (MooTools.version == '1.12')
						$('rsticketspro_search_results').setHTML(xmlHttp.responseText);
					else
						$('rsticketspro_search_results').set('html',  xmlHttp.responseText);
					RSTSearch.slideIn();
				}
				else
					RSTSearch.slideOut();
			}
		}
	xmlHttp.open("GET", url, true);
	xmlHttp.send(null);
}

function rsticketspro_close()
{
	RSTSearch.slideOut();
}
</script>

<?php if (RSTicketsProHelper::isJ16()) { ?>
	<?php if ($this->params->get('show_page_heading', 1)) { ?>
		<h1><?php echo $this->escape($this->params->get('page_heading', $this->params->get('page_title'))); ?></h1>
	<?php } ?>
	<?php } else { ?>
	<?php if ($this->params->get('show_page_title', 1)) { ?>
		<div class="componentheading<?php echo $this->escape($this->params->get('pageclass_sfx')); ?>"><?php echo $this->escape($this->params->get('page_title')); ?></div>
	<?php } ?>
<?php } ?>

<form method="post" action="<?php echo RSTicketsProHelper::route('index.php?option=com_rsticketspro&view=knowledgebase&layout=results'); ?>">
<div id="rsticketspro_container">
    <div id="rsticketspro_searchbox">
		<input type="text" value="<?php echo $this->escape(JText::_('RST_SEARCH_HELPDESK')); ?>" name="search" autocomplete="off" id="rsticketspro_searchinp" />
		<button type="submit" id="rsticketspro_searchbtn"><?php echo JHTML::image('components/com_rsticketspro/assets/images/search-button.png', ''); ?> <?php echo JText::_('RST_SEARCH'); ?></button>
		<?php echo JHTML::image('components/com_rsticketspro/assets/images/loading.gif', '', 'id="rsticketspro_loading"'); ?>
		<span class="clear"></span>
		<div id="rsticketspro_search_results_container">
			<div id="rsticketspro_search_results"></div>
		</div>
	</div><!-- searchbox -->
	<ul id="rsticketspro_items">
		<li>
			<a href="<?php echo RSTicketsProHelper::route('index.php?option=com_rsticketspro&view=submit'); ?>"><img src="<?php echo JURI::root(true); ?>/components/com_rsticketspro/assets/images/icon1.gif" alt="" /></a>
			<p><strong><a href="<?php echo RSTicketsProHelper::route('index.php?option=com_rsticketspro&view=submit'); ?>"><?php echo JText::_('RST_SUBMIT_TICKET'); ?></a></strong></p>
			<p><?php echo JText::_($this->params->get('submit_ticket_desc')); ?></p>
		</li>
		<li>
			<a href="<?php echo RSTicketsProHelper::route('index.php?option=com_rsticketspro&view=rsticketspro'); ?>"><img src="<?php echo JURI::root(true); ?>/components/com_rsticketspro/assets/images/icon2.gif" alt="" /></a>
			<p><strong><a href="<?php echo RSTicketsProHelper::route('index.php?option=com_rsticketspro&view=rsticketspro'); ?>"><?php echo JText::_('RST_VIEW_TICKETS'); ?></a></strong></p>
			<p><?php echo JText::_($this->params->get('view_tickets_desc')); ?></p>
		</li>
		<li>
			<a href="<?php echo RSTicketsProHelper::route('index.php?option=com_rsticketspro&view=search'); ?>"><img src="<?php echo JURI::root(true); ?>/components/com_rsticketspro/assets/images/icon3.gif" alt="" /></a>
			<p><strong><a href="<?php echo RSTicketsProHelper::route('index.php?option=com_rsticketspro&view=search'); ?>"><?php echo JText::_('RST_SEARCH_TICKETS'); ?></a></strong></p>
			<p><?php echo JText::_($this->params->get('search_tickets_desc')); ?></p>
		</li>
	</ul>
	<?php if ($this->params->get('show_kb', 1)) { ?>
	<div class="rsticketspro_halfbox<?php echo $fullbox; ?>">
		<p class="rsticketspro_title"><?php echo JText::_('RST_KNOWLEDGEBASE'); ?></p>
		<?php if (count($this->categories)) { ?>
		<ul class="rsticketspro_categories">
			<?php foreach ($this->categories as $category) { ?>
			<?php $category->thumb = !$category->thumb ? '../../images/kb-icon.png' : $category->thumb; ?>
			<li>
				<strong><?php echo JHTML::image('components/com_rsticketspro/assets/thumbs/small/'.$category->thumb, $category->name); ?> <a href="<?php echo RSTicketsProHelper::route('index.php?option=com_rsticketspro&view=knowledgebase&cid='.$category->id.':'.JFilterOutput::stringURLSafe($category->name)); ?>"><?php echo $this->escape($category->name); ?></a></strong>
				<?php if ($category->description) { ?>
				<?php echo $category->description; ?>
				<?php } ?>
			</li>
			<?php } ?>
		</ul>
		<?php } else { ?>
		<div class="rsticketspro_text">
			<p><?php echo JText::_('RST_NO_KB_CATEGORIES'); ?></p>
		</div>
		<?php } ?>
	</div><!-- halfbox -->
	<?php } ?>
	<?php if ($this->params->get('show_tickets', 1)) { ?>
	<div class="rsticketspro_halfbox<?php echo $fullbox; ?>">
		<p class="rsticketspro_title"><?php echo JText::_('RST_MY_TICKETS'); ?></p>
		<?php if ($this->user->get('guest')) { ?>
		<div class="rsticketspro_text">
			<p><?php echo JText::_('RST_YOU_HAVE_TO_BE_LOGGED_IN'); ?></p>
			<p><?php echo JHTML::image('components/com_rsticketspro/assets/images/lock.png', ''); ?> <a href="<?php echo $this->login_link; ?>"><?php echo JText::_('RST_CLICK_HERE_TO_LOGIN'); ?></a></p>
		</div>
		<?php } else { ?>
		<?php if (count($this->tickets)) { ?>
		<table width="100%" border="0" cellspacing="0" cellpadding="0" id="rsticketspro_smalltable">
			<tr>
				<td nowrap="nowrap"><strong><?php echo JHTML::image('components/com_rsticketspro/assets/images/smallicon1.gif', '', 'class="rsticketspro_smallicon"'); ?><?php echo JText::_('RST_TICKET_SUBJECT'); ?></strong></td>
				<td nowrap="nowrap"><strong><?php echo JHTML::image('components/com_rsticketspro/assets/images/smallicon3.gif', '', 'class="rsticketspro_smallicon"'); ?><?php echo JText::_('RST_TICKET_STATUS'); ?></strong></td>
			</tr>
			<?php foreach ($this->tickets as $ticket) { ?>
			<tr>
				<td><a href="<?php echo RSTicketsProHelper::route('index.php?option=com_rsticketspro&view=ticket&cid='.$ticket->id.':'.JFilterOutput::stringURLSafe($ticket->subject)); ?>"><?php echo $this->escape($ticket->subject); ?></a></td>
				<td><?php echo $this->escape(JText::_($ticket->status_name)); ?></td>
			</tr>
			<?php if (isset($ticket->message)) { ?>
			<tr>
				<td colspan="2" bgcolor="#FFFFFF"><?php echo JHTML::image('components/com_rsticketspro/assets/images/smallicon4.gif', '', 'class="rsticketspro_smallicon"'); ?><?php echo JText::_('RST_REPLY'); ?>:  <a href="<?php echo RSTicketsProHelper::route('index.php?option=com_rsticketspro&view=ticket&cid='.$ticket->id.':'.JFilterOutput::stringURLSafe($ticket->subject)); ?>"><?php echo $this->trim(strip_tags($ticket->message)); ?></a></td>
			</tr>
			<?php } ?>
			<?php } ?>
		</table>
		<?php } else { ?>
		<div class="rsticketspro_text">
			<p><?php echo JText::_('RST_NO_RECENT_ACTIVITY'); ?></p>
		</div>
		<?php } ?>
		<?php } ?>
	</div><!-- halfbox -->
	<?php } ?>
</div><!-- container -->
</form>
<span class="rst_clear"></span>