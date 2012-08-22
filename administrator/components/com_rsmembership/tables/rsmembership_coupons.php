<?php
/**
* @version 1.0.0
* @package RSMembership! 1.0.0
* @copyright (C) 2009-2010 www.rsjoomla.com
* @license GPL, http://www.gnu.org/licenses/gpl-2.0.html
*/

defined('_JEXEC') or die('Restricted access');

class TableRSMembership_Coupons extends JTable
{
	/**
	 * Primary Key
	 *
	 * @var int
	 */
	var $id = null;
	
	var $name = '';
	var $date_added = 0;
	var $date_start = 0;
	var $date_end = 0;
	var $discount_type = 0; // 0 - percent, 1 - fixed value
	var $discount_price = null;
	var $max_uses = 0;
	
	var $published = 1;
		
	/**
	 * Constructor
	 *
	 * @param object Database connector object
	 */
	function TableRSMembership_Coupons(& $db)
	{
		parent::__construct('#__rsmembership_coupons', 'id', $db);
	}
}