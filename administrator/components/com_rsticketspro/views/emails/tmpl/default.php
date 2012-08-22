<?php
/**
* @version 2.0.0
* @package RSTickets! Pro 2.0.0
* @copyright (C) 2010 www.rsjoomla.com
* @license GPL, http://www.gnu.org/licenses/gpl-2.0.html
*/

defined('_JEXEC') or die('Restricted access');
?>

<form action="<?php echo JRoute::_('index.php?option=com_rsticketspro&view=emails'); ?>" method="post" name="adminForm" id="adminForm">
	<table class="adminform">
		<tr>
			<td width="100%">
			<?php echo JText::_('RST_SELECT_LANGUAGE'); ?> <?php echo $this->lists['languages']; ?>
			<button onclick="this.form.submit();"><?php echo JText::_( 'Go' ); ?></button>
			</td>
		</tr>
	</table>
	<div id="editcell1">
		<table class="adminlist">
			<thead>
			<tr>
				<th width="5"><?php echo JText::_( '#' ); ?></th>
				<th width="10%"><?php echo JText::_('RST_EMAIL_LANGUAGE'); ?></th>
				<th width="30%"><?php echo JText::_('RST_EMAIL_TYPE'); ?></th>
				<th><?php echo JText::_('RST_EMAIL_SUBJECT'); ?></th>
			</tr>
			</thead>
	<?php
	$k = 0;
	$i = 0;
	$n = count($this->emails);
	foreach ($this->emails as $row)
	{
	?>
		<tr class="row<?php echo $k; ?>">
			<td><?php echo $i+1; ?></td>
			<td><?php echo @$this->languages[$row->lang]['name']; ?></td>
			<td><?php echo JText::_(strtoupper('RST_'.$row->type)); ?></td>
			<td><a href="<?php echo JRoute::_('index.php?option=com_rsticketspro&controller=emails&task=edit&type='.$row->type.'&language='.$this->escape($this->language)); ?>"><?php echo $row->subject != '' ? $this->escape($row->subject) : '<em>'.JText::_('RST_NO_TITLE').'</em>'; ?></a></td>
		</tr>
	<?php
		$i++;
		$k=1-$k;
	}
	?>
		</table>
	</div>
	
	<?php echo JHTML::_( 'form.token' ); ?>
	<input type="hidden" name="option" value="com_rsticketspro" />
	<input type="hidden" name="view" value="emails" />
	<input type="hidden" name="controller" value="emails" />
	<input type="hidden" name="task" value="" />
</form>