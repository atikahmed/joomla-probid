<?php
/**
* @version 1.0.0
* @package RSMembership! 1.0.0
* @copyright (C) 2009 www.rsjoomla.com
* @license GPL, http://www.gnu.org/licenses/gpl-2.0.html
*/

defined( '_JEXEC' ) or die( 'Restricted access' );
jimport('joomla.plugin.plugin');

class plgSystemRSMembership extends JPlugin {
	
	/**
	 * Constructor
	 *
	 * For php4 compatability we must not use the __constructor as a constructor for plugins
	 * because func_get_args ( void ) returns a copy of all passed arguments NOT references.
	 * This causes problems with cross-referencing necessary for the observer design pattern.
	 *
	 * @param object $subject The object to observe
	 * @since 1.5
	 */
	
	function plgSystemRSMembership(&$subject, $config) {
		parent::__construct($subject, $config);
		
		if (file_exists(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_rsmembership'.DS.'helpers'.DS.'rsmembership.php'))
		{
			require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_rsmembership'.DS.'helpers'.DS.'rsmembership.php');
			RSMembershipHelper::readConfig();
		}
	}
	
	function onAfterInitialise()
	{
		if (class_exists('RSMembershipHelper'))
		{
			$this->loadLanguage('plg_system_rsmembership');
			$db = JFactory::getDBO();
				
			$date = JFactory::getDate();
			$date = $date->toUnix();
			if (RSMembershipHelper::getConfig('last_check') + RSMembershipHelper::getConfig('interval') * 60 > $date)
				return;
			
			$db->setQuery("UPDATE #__rsmembership_configuration SET value='".$date."' WHERE name='last_check' LIMIT 1");
			$db->query();
			
			$offset = RSMembershipHelper::getConfig('delete_pending_after');
			if ($offset < 1) $offset = 1;
			$offset = $offset * 3600;
			
			$db->setQuery("DELETE FROM #__rsmembership_transactions WHERE `status`='pending' AND `date` < '".($date - $offset)."'");
			$db->query();
			
			// Limit 10 so we don't overload the server
			$db->setQuery("SELECT mu.id, m.gid_enable, m.gid_expire, m.disable_expired_account, mu.user_id FROM #__rsmembership_membership_users mu LEFT JOIN #__rsmembership_memberships m ON (mu.membership_id=m.id) WHERE  mu.`status`='0' AND mu.`membership_end` > 0 AND mu.`membership_end` < '".$date."' LIMIT 10");
			$updates = $db->loadObjectList();
			$to_update = array();
			foreach ($updates as $update)
			{
				$to_update[] = $update->id;
				if ($update->gid_enable)
					RSMembership::updateGid($update->user_id, $update->gid_expire);
				if ($update->disable_expired_account)
					RSMembership::disableUser($update->user_id);
			}
			
			if (!empty($to_update))
			{
				$db->setQuery("UPDATE #__rsmembership_membership_users SET `status`='2' WHERE `id` IN (".implode(',', $to_update).")");
				$db->query();
			}
			
			RSMembershipHelper::checkShared();
			RSMembershipHelper::sendExpirationEmails();
		}
	}
	
	function onAfterDispatch()
	{
		if (class_exists('RSMembershipHelper'))
		{
			if (RSMembershipHelper::getConfig('disable_registration'))
			{
				$j_option = JRequest::getVar('option');
				$j_view   = JRequest::getVar('view');
				$j_task   = JRequest::getVar('task');
				if (($j_option == 'com_user' && ($j_task == 'register' || $j_view == 'register')) || ($j_option == 'com_users' && ($j_task == 'registration.register' || $j_view == 'registration')))
				{
					$url = JRoute::_('index.php?option=com_rsmembership', false);
					$custom_url = RSMembershipHelper::getConfig('registration_page');
					if (!empty($custom_url))
						$url = $custom_url;
					
					$mainframe =& JFactory::getApplication();
					$mainframe->redirect($url);
				}
			}
			
			RSMembershipHelper::checkShared();
		}
	}
	
	function onAfterRoute()
	{
		if (class_exists('RSMembershipHelper'))
			RSMembershipHelper::checkShared();
	}
	
	function onAfterRender()
	{
		$mainframe =& JFactory::getApplication();
		if ($mainframe->isAdmin()) return;
		
		if (class_exists('RSMembershipHelper'))
		{
			$body = JResponse::getBody();
			if (JString::strpos($body, '{rsmembership-subscribe') === false) return;
			
			$db =& JFactory::getDBO();
			
			$pattern = '#\{rsmembership-subscribe ([0-9]+)\}#i';
			preg_match_all($pattern, $body, $matches);
			
			foreach ($matches[1] as $i => $membership_id)
			{
				$db->setQuery("SELECT `id`, `name` FROM #__rsmembership_memberships WHERE `id`='".(int) $membership_id."'");
				$membership = $db->loadObject();
				if (empty($membership)) continue;
				
				$find[]    = $matches[0][$i];
				$replace[] = JRoute::_('index.php?option=com_rsmembership&task=subscribe&cid='.$membership_id.':'.JFilterOutput::stringURLSafe($membership->name));
			}
			$body = str_replace($find, $replace, $body);
			
			JResponse::setBody($body);
		}
	}
	
	function onCreateModuleQuery(&$extra)
	{
		if (class_exists('RSMembershipHelper'))
			if (is_array($extra->where))
			{
				$where = RSMembershipHelper::getModulesWhere();
				if ($where)
					$extra->where[] = $where;
			}
			else
				$extra->where .= RSMembershipHelper::getModulesWhere();
	}
}