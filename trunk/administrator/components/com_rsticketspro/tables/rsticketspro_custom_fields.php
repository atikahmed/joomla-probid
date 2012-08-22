<?php
/**
* @version 2.0.0
* @package RSTickets! Pro 2.0.0
* @copyright (C) 2010 www.rsjoomla.com
* @license GPL, http://www.gnu.org/licenses/gpl-2.0.html
*/

defined('_JEXEC') or die('Restricted access');

class TableRSTicketsPro_Custom_Fields extends JTable
{
	/**
	 * Primary Key
	 *
	 * @var int
	 */
	var $id = null;
	var $department_id = null;
	
	var $name = '';
	var $label = '';
	var $type = '';
	var $values = '';
	var $additional = '';
	var $validation = '';
	var $required = 0;
	var $description = '';
	
	var $published = 1;
	var $ordering = null;
		
	/**
	 * Constructor
	 *
	 * @param object Database connector object
	 */
	function TableRSTicketsPro_Custom_Fields(& $db)
	{
		parent::__construct('#__rsticketspro_custom_fields', 'id', $db);
	}
}