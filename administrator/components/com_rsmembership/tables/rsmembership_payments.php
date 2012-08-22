<?php
/**
* @version 1.0.0
* @package RSMembership! 1.0.0
* @copyright (C) 2009-2010 www.rsjoomla.com
* @license GPL, http://www.gnu.org/licenses/gpl-2.0.html
*/

defined('_JEXEC') or die('Restricted access');

class TableRSMembership_Payments extends JTable
{
	/**
	 * Primary Key
	 *
	 * @var int
	 */
	var $id = null;
	var $name = '';
	var $details = '';
	var $tax_type = 0;
	var $tax_value = 0;

	/**
	 * Constructor
	 *
	 * @param object Database connector object
	 */
	function TableRSMembership_Payments(& $db)
	{
		parent::__construct('#__rsmembership_payments', 'id', $db);
	}
}