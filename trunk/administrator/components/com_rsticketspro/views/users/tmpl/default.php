<?php
/**
* @version 2.0.0
* @package RSTickets! Pro 2.0.0
* @copyright (C) 2010 www.rsjoomla.com
* @license GPL, http://www.gnu.org/licenses/gpl-2.0.html
*/

defined('_JEXEC') or die('Restricted access');
?>
<form action="<?php echo JRoute::_('index.php?option=com_rsticketspro&view=users'); ?>" method="post" name="adminForm" id="adminForm">
	<table class="adminform">
		<tr>
			<td nowrap="nowrap" width="10%">
				<label for="filter_word"><span class="hasTip" title="<?php echo JText::_('RST_SEARCH_TEXT_DESC'); ?>"><?php echo JText::_('RST_SEARCH_TEXT'); ?></span></label>
			</td>
			<td nowrap="nowrap">
				<input type="text" name="search" id="filter_word" size="40" value="<?php echo $this->escape($this->filter_word); ?>" class="inputbox" />
				<button type="submit" name="Search" class="button"><?php echo JText::_('RST_SEARCH'); ?></button>
				<button onclick="document.getElementById('filter_word').value='';this.form.submit();" class="button"><?php echo JText::_( 'Reset' ); ?></button>
			</td>
		</tr>
	</table>
	
	<div id="editcell1">
		<table class="adminlist">
			<thead>
			<tr>
				<th width="5"><?php echo JText::_( '#' ); ?></th>
				<th width="50"><?php echo JHTML::_('grid.sort', 'Id', 'id', $this->sortOrder, $this->sortColumn); ?></th>
				<th><?php echo JHTML::_('grid.sort', 'Name', 'name', $this->sortOrder, $this->sortColumn); ?></th>
				<th><?php echo JHTML::_('grid.sort', 'Username', 'username', $this->sortOrder, $this->sortColumn); ?></th>
				<th><?php echo JHTML::_('grid.sort', 'Email', 'email', $this->sortOrder, $this->sortColumn); ?></th>
			</tr>
			</thead>
			<?php
			$k = 0;
			$i = 0;
			$n = count($this->users);
			foreach ($this->users as $row)
			{
			?>
				<tr class="row<?php echo $k; ?>">
					<td><?php echo $this->pagination->getRowOffset($i); ?></td>
					<td><?php echo $row->id; ?></td>
					<td><?php echo $this->escape($row->name); ?></td>
					<td><a href="javascript: rst_select_user('<?php echo $row->id; ?>', '<?php echo $this->escape(addslashes($row->email)); ?>', '<?php echo $this->escape(addslashes($row->name)); ?>');"><?php echo $this->escape($row->username); ?></a></td>
					<td><a href="javascript: rst_select_user('<?php echo $row->id; ?>', '<?php echo $this->escape(addslashes($row->email)); ?>', '<?php echo $this->escape(addslashes($row->name)); ?>');"><?php echo $this->escape($row->email); ?></a></td>
				</tr>
			<?php
				$i++;
				$k=1-$k;
			}
			?>
		<tfoot>
			<tr>
				<td colspan="5"><?php echo $this->pagination->getListFooter(); ?></td>
			</tr>
		</tfoot>
		</table>
	</div>
	
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