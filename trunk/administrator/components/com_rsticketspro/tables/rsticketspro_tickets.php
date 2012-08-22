<?php
/**
* @version 2.0.0
* @package RSTickets! Pro 2.0.0
* @copyright (C) 2010 www.rsjoomla.com
* @license GPL, http://www.gnu.org/licenses/gpl-2.0.html
*/

defined('_JEXEC') or die('Restricted access');

class TableRSTicketsPro_Tickets extends JTable
{
	/**
	 * Primary Key
	 *
	 * @var int
	 */
	var $id = null;
	
	var $department_id = 0;
	var $staff_id = 0;
	var $customer_id = 0;
	var $code = '';
	var $subject = '';
	var $status_id = '';
	var $priority_id = '';
	var $date = 0;
	var $last_reply = 0;
	var $last_reply_customer = 1;
	var $replies = 0;
	var $autoclose_sent = 0;
	var $flagged = 0;
	var $agent = '';
	var $referer = '';
	var $ip = '';
	var $logged = 0;
	var $feedback = 0;
	var $has_files = 0;
	var $time_spent = null;
		
	/**
	 * Constructor
	 *
	 * @param object Database connector object
	 */
	function TableRSTicketsPro_Tickets(& $db)
	{
		parent::__construct('#__rsticketspro_tickets', 'id', $db);
	}
}