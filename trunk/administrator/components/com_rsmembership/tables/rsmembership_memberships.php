<?php
/**
* @version 1.0.0
* @package RSMembership! 1.0.0
* @copyright (C) 2009-2010 www.rsjoomla.com
* @license GPL, http://www.gnu.org/licenses/gpl-2.0.html
*/

defined('_JEXEC') or die('Restricted access');

class TableRSMembership_Memberships extends JTable
{
	/**
	 * Primary Key
	 *
	 * @var int
	 */
	var $id = null;
	
	var $category_id = 0;
	var $name = '';
	var $description = "<p>{extras}</p>\n<p>Price: {price}</p>\n<p>Click here to {buy}.</p>";
	var $term_id = 0;
	var $thumb = '';
	var $thumb_w = 100;
	var $sku = '';
	var $price = 0;
	var $use_renewal_price = 0;
	var $renewal_price = 0;
	var $recurring = 0;
	var $share_redirect = '';
	var $period = 30; // '0' for unlimited
	var $period_type = 'd'; // 'h' => 'hour', 'd' => day, 'm' => month (30 days), 'y' => year
	var $use_trial_period = 0;
	var $trial_period = 30; // '0' for unlimited
	var $trial_period_type = 'd'; // 'h' => 'hour', 'd' => day, 'm' => month (30 days), 'y' => year
	var $trial_price = 0;
	var $unique = 0;
	var $no_renew = 0;
	var $stock = 0; // '0' for unlimited
	var $activation = '1'; // '0' => manual, '1' => automatic, '2' => instant
	var $action = '0'; // '0' => thank you, '1' => redirect
	var $thankyou = 'Thank you for purchasing {membership}!';
	var $redirect = '';
	var $user_email_use_global = 1;
	var $user_email_mode = 1;
	var $user_email_from = '';
	var $user_email_from_addr = '';
	
	var $user_email_new_subject = '';
	var $user_email_new_text = '';
	var $user_email_approved_subject = '';
	var $user_email_approved_text = '';
	var $user_email_renew_subject = '';
	var $user_email_renew_text = '';
	var $user_email_upgrade_subject = '';
	var $user_email_upgrade_text = '';
	var $user_email_addextra_subject = '';
	var $user_email_addextra_text = '';
	var $user_email_expire_subject = '';
	var $user_email_expire_text = '';
	var $expire_notify_interval = 3;
	
	var $admin_email_mode = 1;
	var $admin_email_to_addr = '';
	
	var $admin_email_new_subject = '';
	var $admin_email_new_text = '';
	var $admin_email_approved_subject = '';
	var $admin_email_approved_text = '';
	var $admin_email_renew_subject = '';
	var $admin_email_renew_text = '';
	var $admin_email_upgrade_subject = '';
	var $admin_email_upgrade_text = '';
	var $admin_email_addextra_subject = '';
	var $admin_email_addextra_text = '';
	var $admin_email_expire_subject = '';
	var $admin_email_expire_text = '';
	
	var $custom_code = null;
	var $custom_code_transaction = null;
	
	var $gid_enable = 0;
	var $gid_subscribe = 18;
	var $gid_expire = 18;
	var $disable_expired_account = 0;
	
	var $fixed_expiry = 0;
	var $fixed_day = 0;
	var $fixed_month = 0;
	var $fixed_year = 0;
	
	var $published = 1;
	var $ordering = null;
		
	/**
	 * Constructor
	 *
	 * @param object Database connector object
	 */
	function TableRSMembership_Memberships(& $db)
	{
		parent::__construct('#__rsmembership_memberships', 'id', $db);
	}
	
	function bind($src, $ignore=array())
	{
		$bound = parent::bind($src, $ignore);
		if ($bound)
		{
			if (isset($src['gid_subscribe']) && is_array($src['gid_subscribe']))
				$this->gid_subscribe = implode(',', $src['gid_subscribe']);
			if (isset($src['gid_expire']) && is_array($src['gid_expire']))
				$this->gid_expire = implode(',', $src['gid_expire']);
		}
		
		return $bound;
	}
	
	function load($keys = null, $reset = true)
	{
		$loaded = parent::load($keys, $reset);
		if ($loaded)
		{
			$this->gid_subscribe = explode(',', $this->gid_subscribe);
			$this->gid_expire 	 = explode(',', $this->gid_expire);
		}
		
		return $loaded;
	}
}