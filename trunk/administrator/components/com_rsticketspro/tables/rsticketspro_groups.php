<?php
/**
* @version 2.0.0
* @package RSTickets! Pro 2.0.0
* @copyright (C) 2010 www.rsjoomla.com
* @license GPL, http://www.gnu.org/licenses/gpl-2.0.html
*/

defined('_JEXEC') or die('Restricted access');

class TableRSTicketsPro_Groups extends JTable
{
	/**
	 * Primary Key
	 *
	 * @var int
	 */
	var $id = null;
	
	var $name = '';
	var $add_ticket = 1;
	var $add_ticket_customers = 1;
	var $add_ticket_staff = 1;
	var $update_ticket = 1;
	var $update_ticket_custom_fields = 1;
	var $delete_ticket = 1;
	var $answer_ticket = 1;
	var $update_ticket_replies = 1;
	var $update_ticket_replies_customers = 1;
	var $update_ticket_replies_staff = 1;
	var $delete_ticket_replies = 1;
	var $delete_ticket_replies_customers = 1;
	var $delete_ticket_replies_staff = 1;
	var $assign_tickets = 1;
	var $change_ticket_status = 1;
	var $see_unassigned_tickets = 1;
	var $see_other_tickets = 1;
	var $move_ticket = 1;
	var $view_notes = 1;
	var $add_note = 1;
	var $update_note = 1;
	var $update_note_staff = 1;
	var $delete_note = 1;
	var $delete_note_staff = 1;
	
	/**
	 * Constructor
	 *
	 * @param object Database connector object
	 */
	function TableRSTicketsPro_Groups(& $db)
	{
		parent::__construct('#__rsticketspro_groups', 'id', $db);
	}
}