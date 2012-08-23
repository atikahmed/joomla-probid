<?php
/**
* @version 2.0.0
* @package RSTickets! Pro 2.0.0
* @copyright (C) 2010 www.rsjoomla.com
* @license GPL, http://www.gnu.org/licenses/gpl-2.0.html
*/

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport( 'joomla.plugin.plugin' );

/**
 * RSTickets! Pro System Plugin
 */
class plgSystemRSTicketsPro extends JPlugin
{
	/**
	 * Constructor
	 *
	 * For php4 compatibility we must not use the __constructor as a constructor for plugins
	 * because func_get_args ( void ) returns a copy of all passed arguments NOT references.
	 * This causes problems with cross-referencing necessary for the observer design pattern.
	 *
	 * @access	protected
	 * @param	object	$subject The object to observe
	 * @param 	array   $config  An array that holds the plugin configuration
	 * @since	1.0
	 */
	function plgSystemRSTicketsPro( &$subject, $config )
	{
		parent::__construct( $subject, $config );
	}
	
	function onAfterInitialise()
	{
		$this->loadLanguage('plg_system_rsticketspro', JPATH_ADMINISTRATOR);
		$db = JFactory::getDBO();
		
		$db->setQuery("SELECT * FROM #__rsticketspro_configuration WHERE name IN ('autoclose_enabled', 'autoclose_cron_lastcheck', 'autoclose_cron_interval', 'autoclose_interval')");
		$tmp = $db->loadObjectList();
		if (!$tmp)
			return;
		
		$autoclose = new stdClass();
		foreach ($tmp as $obj)
			$autoclose->{$obj->name} = $obj->value;
		
		if (!$autoclose->autoclose_enabled)
			return;
		
		$date = JFactory::getDate();
		$date = $date->toUnix();
		
		if ($autoclose->autoclose_cron_lastcheck + $autoclose->autoclose_cron_interval * 60 > $date)
			return;
		
		$db->setQuery("UPDATE #__rsticketspro_configuration SET value='".$date."' WHERE name='autoclose_cron_lastcheck' LIMIT 1");
		$db->query();
		
		$date = $date - ($autoclose->autoclose_interval * 86400);
		
		$db->setQuery("UPDATE #__rsticketspro_tickets SET status_id = 2 WHERE status_id != 2 AND autoclose_sent > 0 AND autoclose_sent < '".$date."'");
		$db->query();
	}
}