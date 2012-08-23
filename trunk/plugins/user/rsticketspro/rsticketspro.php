<?php
/**
* @version 2.0.0
* @package RSTickets! Pro 2.0.0
* @copyright (C) 2010 www.rsjoomla.com
* @license GPL, http://www.gnu.org/licenses/gpl-2.0.html
*/

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

jimport('joomla.plugin.plugin');

/**
 * RSTickets! Pro User Plugin
 */
class plgUserRSTicketsPro extends JPlugin {

	/**
	 * Constructor
	 *
	 * For php4 compatability we must not use the __constructor as a constructor for plugins
	 * because func_get_args ( void ) returns a copy of all passed arguments NOT references.
	 * This causes problems with cross-referencing necessary for the observer design pattern.
	 *
	 * @param object $subject The object to observe
	 * @param 	array  $config  An array that holds the plugin configuration
	 * @since 1.5
	 */
	function plgUserRSTicketsPro(& $subject, $config)
	{
		parent::__construct($subject, $config);
	}

	// 1.6
	function onUserLogin($user, $options)
	{
		return $this->onLoginUser($user, $options);
	}
	
	/**
	 * This method should handle any login logic and report back to the subject
	 *
	 * @access	public
	 * @param 	array 	holds the user data
	 * @param 	array    extra options
	 * @return	boolean	True on success
	 * @since	1.5
	 */
	function onLoginUser($user, $options=array())
	{
		// Initialize variables
		$success = true;
		
		jimport('joomla.filesystem.file');
		if (!JFile::exists(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_rsticketspro'.DS.'helpers'.DS.'rsticketspro.php'))
			return $success;
			
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_rsticketspro'.DS.'helpers'.DS.'rsticketspro.php');
		
		$session =& JFactory::getSession();
		if (RSTicketsProHelper::isJ16())
		{
			jimport('joomla.user.helper');
			if (isset($user['username']))
			{
				$user_id = JUserHelper::getUserId($user['username']);
				$logged_user = JFactory::getUser($user_id);
			}
			else
				$logged_user = JFactory::getUser();
		}
		else
			$logged_user = JFactory::getUser();
		$db = JFactory::getDBO();
		
		$user_id = $logged_user->get('id');
		
		// is staff
		$db->setQuery("SELECT group_id FROM #__rsticketspro_staff WHERE user_id='".(int) $user_id."'");
		$group_id = $db->loadResult();
		$is_staff = !empty($group_id);
		if (!$is_staff)
		{
			if (RSTicketsProHelper::isJ16())
			{
				$admin_groups = RSTicketsProHelper::getAdminGroups();
				$user_groups = $logged_user->getAuthorisedGroups();
				foreach ($user_groups as $user_group_id)
					if (in_array($user_group_id, $admin_groups))
					{
						$is_staff = true;
						break;
					}
			}
			else
				$is_staff = $logged_user->get('gid') == 23 || $logged_user->get('gid') == 25 || $logged_user->get('gid') == 24;
		}
			
		$session->set('rsticketspro.is_staff', $is_staff);
		
		// permissions and department
		if ($is_staff)
		{
			// permissions
			if ($group_id)
			{
				$db->setQuery("SELECT * FROM #__rsticketspro_groups WHERE id='".(int) $group_id."'");
				$permissions = $db->loadObject();
			}
			else
			{
				// JTable::getInstance('RSTicketsPro_Groups','Table');
				$permissions = new stdClass();
				$permissions->name = '';
				$permissions->add_ticket = 1;
				$permissions->add_ticket_customers = 1;
				$permissions->add_ticket_staff = 1;
				$permissions->update_ticket = 1;
				$permissions->update_ticket_custom_fields = 1;
				$permissions->delete_ticket = 1;
				$permissions->answer_ticket = 1;
				$permissions->update_ticket_replies = 1;
				$permissions->update_ticket_replies_customers = 1;
				$permissions->update_ticket_replies_staff = 1;
				$permissions->delete_ticket_replies_customers = 1;
				$permissions->delete_ticket_replies_staff = 1;
				$permissions->delete_ticket_replies = 1;
				$permissions->assign_tickets = 1;
				$permissions->change_ticket_status = 1;
				$permissions->see_unassigned_tickets = 1;
				$permissions->see_other_tickets = 1;
				$permissions->move_ticket = 1;
				$permissions->view_notes = 1;
				$permissions->add_note = 1;
				$permissions->update_note = 1;
				$permissions->update_note_staff = 1;
				$permissions->delete_note = 1;
				$permissions->delete_note_staff = 1;
			}
			$session->set('rsticketspro.permissions', $permissions);
			
			$db->setQuery("SELECT department_id FROM #__rsticketspro_staff_to_department WHERE user_id='".(int) $user_id."'");
			$departments = $db->loadResultArray();
			if (empty($departments))
			{
				$db->setQuery("SELECT id FROM #__rsticketspro_departments");
				$departments = $db->loadResultArray();
			}
			$session->set('rsticketspro.departments', $departments);
			
			// searches
			$db->setQuery("SELECT * FROM #__rsticketspro_searches WHERE user_id='".(int) $user_id."' AND `default`='1'");
			$search = $db->loadObject();
			if (!empty($search))
			{
				$params = unserialize(base64_decode($search->params));
				
				$mainframe =& JFactory::getApplication();
				
				$option = 'com_rsticketspro';

				$session->set($option.'.ticketsfilter.rsticketspro_search', 1);
				$mainframe->setUserState($option.'.ticketsfilter.rsticketspro_search', '1');
				$mainframe->setUserState($option.'.ticketsfilter.filter_word', $params['filter_word']);
				$mainframe->setUserState($option.'.ticketsfilter.customer', $params['customer']);
				$mainframe->setUserState($option.'.ticketsfilter.staff', $params['staff']);
				$mainframe->setUserState($option.'.ticketsfilter.department_id', $params['department_id']);
				$mainframe->setUserState($option.'.ticketsfilter.priority_id', $params['priority_id']);
				$mainframe->setUserState($option.'.ticketsfilter.status_id', $params['status_id']);
				
				$mainframe->setUserState($option.'.ticketsfilter.predefined_search', $search->id);
			}
		}
		
		return $success;
	}

	// 1.6
	function onUserLogout($user)
	{
		return $this->onLogoutUser($user);
	}
	
	/**
	 * This method should handle any logout logic and report back to the subject
	 *
	 * @access public
	 * @param array holds the user data
	 * @return boolean True on success
	 * @since 1.5
	 */
	function onLogoutUser($user)
	{
		// Initialize variables
		$success = true;

		$session =& JFactory::getSession();
		$session->set('rsticketspro.is_staff', false);
		$session->set('rsticketspro.permissions', false);

		return $success;
	}
	
	function onAfterDeleteUser($user, $succes, $msg)
	{
		$db  =& JFactory::getDBO();
		$cid = (int) @$user['id'];
		
		if ($cid)
		{
			$db->setQuery("DELETE FROM #__rsticketspro_staff WHERE `user_id`='".$cid."'");
			$db->query();
			
			$db->setQuery("DELETE FROM #__rsticketspro_staff_to_department WHERE `user_id`='".$cid."'");
			$db->query();
		
			$db->setQuery("UPDATE #__rsticketspro_tickets SET staff_id=0 WHERE staff_id='".$cid."'");
			$db->query();
		}
	}
}