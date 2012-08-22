<?php
/**
* @version 2.0.0
* @package RSTickets! Pro 2.0.0
* @copyright (C) 2010 www.rsjoomla.com
* @license GPL, http://www.gnu.org/licenses/gpl-2.0.html
*/

defined('_JEXEC') or die('Restricted access');

class TableRSTicketsPro_Ticket_Messages extends JTable
{
	/**
	 * Primary Key
	 *
	 * @var int
	 */
	var $id = null;
	
	var $ticket_id = 0;
	var $user_id = 0;
	var $message = '';
	var $date = 0;
		
	/**
	 * Constructor
	 *
	 * @param object Database connector object
	 */
	function TableRSTicketsPro_Ticket_Messages(& $db)
	{
		parent::__construct('#__rsticketspro_ticket_messages', 'id', $db);
	}
}