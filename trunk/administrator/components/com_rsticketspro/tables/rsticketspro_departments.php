<?php
/**
* @version 2.0.0
* @package RSTickets! Pro 2.0.0
* @copyright (C) 2010 www.rsjoomla.com
* @license GPL, http://www.gnu.org/licenses/gpl-2.0.html
*/

defined('_JEXEC') or die('Restricted access');

class TableRSTicketsPro_Departments extends JTable
{
	/**
	 * Primary Key
	 *
	 * @var int
	 */
	var $id = null;
	
	var $name = '';
	var $prefix = '';
	var $assignment_type = 1; // 0 - static, 1 - auto
	var $generation_rule = 1; // 0 - sequential, 1 - random
	var $next_number = 1;
	var $email_use_global = 1;
	var $email_address = '';
	var $email_address_fullname = '';
	var $customer_send_email = 1; // 0 - no, 1 - yes
	var $customer_send_copy_email = 1; // 0 - no, 1 - yes
	var $customer_attach_email = 1;
	var $staff_send_email = 1; // 0 - no, 1 - yes
	var $staff_attach_email = 1;
	var $upload = 1; // 0 - no, 1 - yes, 2 - registered
	var $upload_extensions = 'zip';
	var $upload_size = 0;
	var $upload_files = 0;
	var $notify_new_tickets_to = '';
	var $notify_assign = 0; // 0 - no, 1 - yes
	var $priority_id = 0;
	var $cc = '';
	var $bcc = '';
	var $predefined_subjects = '';
	
	var $published = 1;
	var $ordering = null;
		
	/**
	 * Constructor
	 *
	 * @param object Database connector object
	 */
	function TableRSTicketsPro_Departments(& $db)
	{
		parent::__construct('#__rsticketspro_departments', 'id', $db);
	}
}