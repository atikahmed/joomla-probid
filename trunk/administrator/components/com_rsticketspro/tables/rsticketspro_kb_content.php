<?php
/**
* @version 2.0.0
* @package RSTickets! Pro 2.0.0
* @copyright (C) 2010 www.rsjoomla.com
* @license GPL, http://www.gnu.org/licenses/gpl-2.0.html
*/

defined('_JEXEC') or die('Restricted access');

class TableRSTicketsPro_KB_Content extends JTable
{
	/**
	 * Primary Key
	 *
	 * @var int
	 */
	var $id = null;
	
	var $name = '';
	var $text = '';
	var $category_id = 0; // 0 - uncategorised
	var $meta_description = '';
	var $meta_keywords = '';
	var $private = 0;
	var $from_ticket_id = 0;
	var $hits = 0;
	var $created = 0;
	var $modified = 0;
	
	var $published = 1;
	var $ordering = null;
		
	/**
	 * Constructor
	 *
	 * @param object Database connector object
	 */
	function TableRSTicketsPro_KB_Content(& $db)
	{
		parent::__construct('#__rsticketspro_kb_content', 'id', $db);
	}
}