<?php
/**
* @version 2.0.0
* @package RSTickets! Pro 2.0.0
* @copyright (C) 2010 www.rsjoomla.com
* @license GPL, http://www.gnu.org/licenses/gpl-2.0.html
*/

defined( '_JEXEC' ) or die( 'Restricted access' );

// Get a new installer
$plg_installer = new JInstaller();

$db = JFactory::getDBO();

$db->setQuery("SELECT id FROM #__plugins WHERE `element`='rsticketspro' AND `folder`='system' LIMIT 1");
$plg_id = $db->loadResult();
if ($plg_id)
	$plg_installer->uninstall('plugin', $plg_id);

$db->setQuery("SELECT id FROM #__plugins WHERE `element`='rsticketsprocontent' AND `folder`='search' LIMIT 1");
$plg_id = $db->loadResult();
if ($plg_id)
	$plg_installer->uninstall('plugin', $plg_id);

$db->setQuery("SELECT id FROM #__plugins WHERE `element`='rsticketspro' AND `folder`='user' LIMIT 1");
$plg_id = $db->loadResult();
if ($plg_id)
	$plg_installer->uninstall('plugin', $plg_id);
?>
<p><strong>RSTickets! Pro 2.0.0 uninstalled</strong></p>
<table class="adminlist">
	<thead>
		<tr>
			<th class="title" colspan="2"><?php echo JText::_('Extension'); ?></th>
			<th width="30%"><?php echo JText::_('Status'); ?></th>
		</tr>
	</thead>
	<tfoot>
		<tr>
			<td colspan="3"></td>
		</tr>
	</tfoot>
	<tbody>
		<tr class="row0">
			<td class="key" colspan="2"><?php echo 'RSTickets! Pro '.JText::_('Component'); ?></td>
			<td><strong><?php echo JText::_('Removed'); ?></strong></td>
		</tr>
		<tr>
			<th><?php echo JText::_('Plugin'); ?></th>
			<th><?php echo JText::_('Group'); ?></th>
			<th></th>
		</tr>
		<tr class="row1">
			<td class="key">System - RSTickets! Pro Plugin</td>
			<td class="key">system</td>
			<td><strong><?php echo JText::_('Removed'); ?></strong></td>
		</tr>
		<tr class="row0">
			<td class="key">Search - RSTickets! Pro Knowledgebase</td>
			<td class="key">search</td>
			<td><strong><?php echo JText::_('Removed'); ?></strong></td>
		</tr>
		<tr class="row1">
			<td class="key">User - RSTickets! Pro Staff</td>
			<td class="key">user</td>
			<td><strong><?php echo JText::_('Removed'); ?></strong></td>
		</tr>
	</tbody>
</table>