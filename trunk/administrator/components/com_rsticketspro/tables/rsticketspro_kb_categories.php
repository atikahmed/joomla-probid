<?php
/**
* @version 2.0.0
* @package RSTickets! Pro 2.0.0
* @copyright (C) 2010 www.rsjoomla.com
* @license GPL, http://www.gnu.org/licenses/gpl-2.0.html
*/

defined('_JEXEC') or die('Restricted access');

class TableRSTicketsPro_KB_Categories extends JTable
{
	/**
	 * Primary Key
	 *
	 * @var int
	 */
	var $id = null;
	
	var $parent_id = 0; // 0 - no parent
	var $thumb = '';
	var $name = '';
	var $description = '';
	var $meta_description = '';
	var $meta_keywords = '';
	var $private = 0;
	
	var $published = 1;
	var $ordering = null;
		
	/**
	 * Constructor
	 *
	 * @param object Database connector object
	 */
	function TableRSTicketsPro_KB_Categories(& $db)
	{
		parent::__construct('#__rsticketspro_kb_categories', 'id', $db);
	}
}