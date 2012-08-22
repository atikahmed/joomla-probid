<?php
/**
* @version 1.0.0
* @package RSMembership! 1.0.0
* @copyright (C) 2009 www.rsjoomla.com
* @license GPL, http://www.gnu.org/licenses/gpl-2.0.html
*/

defined( '_JEXEC' ) or die( 'Restricted access' );

function RSMembership_isJ16()
{
	jimport('joomla.version');
	$version = new JVersion();
	return $version->isCompatible('1.6.0');
}

// Get a new installer
$plg_installer = new JInstaller();

$db = JFactory::getDBO();

if (!RSMembership_isJ16())
	$db->setQuery("SELECT id FROM #__plugins WHERE `element`='rsmembership' AND `folder`='system' LIMIT 1");
else
	$db->setQuery("SELECT extension_id FROM #__extensions WHERE `element`='rsmembership' AND `folder`='system' AND `type`='plugin' LIMIT 1");
$plg_id = $db->loadResult();
if ($plg_id)
	$plg_installer->uninstall('plugin', $plg_id);

if (!RSMembership_isJ16())
	$db->setQuery("SELECT id FROM #__plugins WHERE `element`='rsmembershipwire' AND `folder`='system' LIMIT 1");
else
	$db->setQuery("SELECT extension_id FROM #__extensions WHERE `element`='rsmembershipwire' AND `folder`='system' AND `type`='plugin' LIMIT 1");
$plg_id = $db->loadResult();
if ($plg_id)
	$plg_installer->uninstall('plugin', $plg_id);
?>
<strong>RSMembership! 1.0.0 uninstalled</strong>