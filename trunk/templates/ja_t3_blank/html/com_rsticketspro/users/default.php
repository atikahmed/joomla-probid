<?php
/**
* @version 2.0.0
* @package RSTickets! Pro 2.0.0
* @copyright (C) 2010 www.rsjoomla.com
* @license GPL, http://www.gnu.org/licenses/gpl-2.0.html
*/

defined('_JEXEC') or die('Restricted access');
?>
<form action="<?php echo RSTicketsProHelper::route('index.php?option=com_rsticketspro&view=users'); ?>" method="post" name="adminForm">
	<div class="rsticketspro_filter">
	<?php echo JText::_('RST_FILTER'); ?>
	<input type="text" name="search" id="rst_filter" value="<?php echo $this->escape($this->filter_word);?>" class="inputbox" onchange="document.adminForm.submit();" />
	<button type="button" onclick="document.getElementById('rst_filter').value=''; this.form.submit();"><?php echo JText::_('RST_CLEAR'); ?></button>
	</div>
	
	<span class="rst_clear"></span>
	
	<table width="100%" border="0" cellspacing="0" cellpadding="0" class="rsticketspro_tablebig">
		<tr>
			<th class="sectiontableheader" width="5"><?php echo JText::_( '#' ); ?></th>
			<th class="sectiontableheader" width="50"><?php echo JHTML::_('rsticketsprogrid.sort', 'Id', 'id', $this->sortOrder, $this->sortColumn); ?></th>
			<th class="sectiontableheader"><?php echo JHTML::_('rsticketsprogrid.sort', 'Name', 'name', $this->sortOrder, $this->sortColumn); ?></th>
			<th class="sectiontableheader"><?php echo JHTML::_('rsticketsprogrid.sort', 'Username', 'username', $this->sortOrder, $this->sortColumn); ?></th>
			<th class="sectiontableheader"><?php echo JHTML::_('rsticketsprogrid.sort', 'Email', 'email', $this->sortOrder, $this->sortColumn); ?></th>
		</tr>
		<?php
		$k = 1;
		$i = 0;
		$n = count($this->users);
		foreach ($this->users as $row)
		{
		?>
			<tr class="sectiontableentry<?php echo $k; ?>">
				<td><?php echo $this->pagination->getRowOffset($i); ?></td>
				<td><?php echo $row->id; ?></td>
				<td><?php echo $this->escape($row->name); ?></td>
				<td><a href="javascript: rst_select_user('<?php echo $row->id; ?>', '<?php echo $this->escape(addslashes($row->email)); ?>', '<?php echo $this->escape(addslashes($row->name)); ?>');"><?php echo $this->escape($row->username); ?></a></td>
				<td><a href="javascript: rst_select_user('<?php echo $row->id; ?>', '<?php echo $this->escape(addslashes($row->email)); ?>', '<?php echo $this->escape(addslashes($row->name)); ?>');"><?php echo $this->escape($row->email); ?></a></td>
			</tr>
		<?php
			$k = $k == 1 ? 2 : 1;
			$i++;
		}
		?>
	<?php if ($this->pagination->get('pages.total') > 1) { ?>
	<tr>
		<td align="center" colspan="5" class="sectiontablefooter">
		<div class="pagination"><?php echo $this->pagination->getPagesLinks(); ?></div>
		</td>
	</tr>
	<tr>
		<td colspan="5" align="right"><?php echo $this->pagination->getPagesCounter(); ?></td>
	</tr>
	<?php } ?>
	</table>
	
	<?php echo JHTML::_( 'form.token' ); ?>
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="option" value="com_rsticketspro" />
	<input type="hidden" name="view" value="users" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="tmpl" value="component" />
	
	<input type="hidden" name="filter_order" value="<?php echo $this->escape($this->sortColumn); ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $this->escape($this->sortOrder); ?>" />
</form>

<script type="text/javascript">
function rst_select_user(id, email, name)
{
	if (window.parent)
	{
		window.parent.document.getElementById('customer_id').value = id;
		window.parent.document.getElementById('submit_email_text').innerHTML = email;
		window.parent.document.getElementById('submit_name_text').innerHTML = name;
		<?php if (RSTicketsProHelper::isJ16()) { ?>
		window.parent.SqueezeBox.close();
		<?php } else { ?>
		window.parent.document.getElementById('sbox-window').close();
		<?php } ?>
	}
}
</script>

<?php JHTML::_('behavior.keepalive'); ?>