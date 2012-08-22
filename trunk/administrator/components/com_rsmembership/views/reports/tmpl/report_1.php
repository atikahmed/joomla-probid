<?php
/**
* @version 1.0.0
* @package RSMembership! 1.0.0
* @copyright (C) 2009-2010 www.rsjoomla.com
* @license GPL, http://www.gnu.org/licenses/gpl-2.0.html
*/


defined('_JEXEC') or die('Restricted access');

if (empty($this->data['units'])) { ?>
	<h2><?php echo JText::_('RSM_NO_DATA'); ?></h2>
	<?php
	return;
}
?>
<div align="center">
	<h2><?php echo JText::_('RSM_REPORT_1'); ?></h2>
</div>

<div id="rsm_report_container">
<canvas id="graph" width="300" height="225"></canvas>
<table id="rsm_reports_table">
	<thead>
	<tr>
		<th scope="col"></th>
	<?php foreach ($this->data['memberships'] as $membership => $values) { ?>
		<th scope="col"><?php echo $membership; ?></th>
	<?php } ?>
	</tr>
	</thead>
	<tbody>
	<?php foreach ($this->data['units'] as $unit) { ?>
	<tr>
		<th scope="row"><?php echo $unit; ?></th>
		<?php foreach ($this->data['memberships'] as $membership => $value) { ?>
			<?php if (isset($this->data['memberships'][$membership][$unit])) { ?>
				<td><?php echo $this->data['memberships'][$membership][$unit]; ?></td>
			<?php } else { ?>
				<td>0</td>
			<?php } ?>
		<?php } ?>
	</tr>
	<?php } ?>
	</tbody>
</table>
</div>

<table class="adminlist">
	<thead>
	<tr>
		<th width="5"><?php echo JText::_('RSM_MIN'); ?></th>
		<th width="5"><?php echo JText::_('RSM_AVG'); ?></th>
		<th width="5"><?php echo JText::_('RSM_MAX'); ?></th>
		<th width="5"><?php echo JText::_('RSM_TOTAL'); ?></th>
	</tr>
	</thead>
	<tr class="row0">
		<td align="center"><?php echo $this->min; ?></td>
		<td align="center"><?php echo $this->avg; ?></td>
		<td align="center"><?php echo $this->max; ?></td>
		<td align="center"><?php echo $this->total;?></td>
	</tr>
</table>