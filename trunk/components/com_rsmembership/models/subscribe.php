<?php
/**
* @version 1.0.0
* @package RSMembership! 1.0.0
* @copyright (C) 2009-2010 www.rsjoomla.com
* @license GPL, http://www.gnu.org/licenses/gpl-2.0.html
*/

defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.model');

class RSMembershipModelSubscribe extends JModel
{
	var $_id = 0;
	var $_extras = array();
	var $_data;
	var $term_id;
	
	var $_html = '';
	var $transaction_id = 0;
	
	var $recaptcha_error = null;
	
	function __construct()
	{
		parent::__construct();
		$this->_execute();
	}
	
	function _execute()
	{
		$mainframe =& JFactory::getApplication();
		$option = 'com_rsmembership';
		
		$task = JRequest::getVar('task', '');
		if ($task == 'subscribe') // get what membership and/or membership extras the user is subscribing to and bind them to the session
		{
			$this->_bindId();
			$this->_bindExtras();
		}
		else
		{
			// get the already bound items
			if ($task == 'validatesubscribe')
				$this->_bindData(false);
			
			$this->_setId();
			$this->_setExtras();
			$this->_setData();
			
			if ($task == 'payment')
			{
				// empty session
				$this->_emptySession();
				
				$extras = $this->getExtras();
				$membership = $this->getMembership();
				$paymentplugin = JRequest::getCmd('payment', 'none');
				
				// calculate the total price
				$total = 0;
				$total += $membership->price;
				foreach ($extras as $extra)
					$total += $extra->price;
					
				$user =& JFactory::getUser();
				if (!$user->get('guest'))
				{
					$user_id = $user->get('id');
					RSMembership::createUserData($user_id, @$this->_data->fields);
				}
				else
				{
					if (RSMembershipHelper::getConfig('create_user_instantly'))
						$user_id = RSMembership::createUser($this->_data->email, $this->_data);
					else
						$user_id = 0;
				}
				
				$row =& JTable::getInstance('RSMembership_Transactions','Table');
				$row->user_id = $user_id;
				$row->user_email = $this->_data->email;
				
				$data = new stdClass();
				$data->name = $this->_data->name;
				$data->username = isset($this->_data->username) ? $this->_data->username : '';
				if (isset($this->_data->password))
					$data->password = $this->_data->password;
				$data->fields = $this->_data->fields;
				$row->user_data = serialize($data);
				
				$row->type = 'new';
				$params = array();
				$params[] = 'membership_id='.$membership->id;
				if (is_array($this->_extras) && !empty($this->_extras))
					$params[] = 'extras='.implode(',', $this->_extras);
				
				$row->params = implode(';', $params); // params, membership, extras etc
				$date = JFactory::getDate();
				$row->date = $date->toUnix();
				$row->ip = $_SERVER['REMOTE_ADDR'];
				$row->price = $total;
				$row->coupon = $this->getCoupon();
				$row->currency = RSMembershipHelper::getConfig('currency');
				$row->hash = '';
				$row->gateway = $paymentplugin == 'none' ? 'No Gateway' : RSMembership::getPlugin($paymentplugin);
				$row->status = 'pending';
				
				$this->_html = '';
				
				// trigger the payment plugin
				$paymentpluginClass = $paymentplugin;
				if (preg_match('#rsmembershipwire([0-9]+)#', $paymentplugin, $match))
					$paymentpluginClass = 'rsmembershipwire';
				
				$className = 'plgSystem'.$paymentpluginClass;
				$delay = false;
				if (class_exists($className))
				{
					$dispatcher  =& JDispatcher::getInstance();
					$plugin 	 = new $className($dispatcher, array());
					$args   	 = array('plugin' => $paymentplugin, 'data' => &$this->_data, 'extras' => $extras, 'membership' => $membership, 'transaction' => &$row);
					
					if (method_exists($plugin, 'onMembershipPayment'))
						$this->_html = call_user_func_array(array($plugin, 'onMembershipPayment'), $args);
					
					if (method_exists($plugin, 'hasDelayTransactionStoring'))
					{
						$delay = $plugin->hasDelayTransactionStoring();
						if (method_exists($plugin, 'delayTransactionStoring'))
							$plugin->delayTransactionStoring($row->getProperties());
					}
				}
				
				// plugin can delay the transaction storing
				if (!$delay)
				{
					// store the transaction
					$row->store();
					
					// store the transaction id
					$this->transaction_id = $row->id;
					
					// finalize the transaction (send emails)
					RSMembership::finalize($this->transaction_id);
					
					// approve the transaction
					if ($row->status == 'completed' || ($row->price == 0 && $membership->activation != 0))
						RSMembership::approve($this->transaction_id, true);
					
					if ($row->price == 0)
						$mainframe->redirect(JRoute::_('index.php?option=com_rsmembership&task=thankyou', false));
				}
			}
		}
	}
	
	function _emptySession()
	{
		$option = 'com_rsmembership';
		
		$session = JFactory::getSession();
		$session->set($option.'.subscribe.membership', null);
		$session->set($option.'.subscribe.'.$this->_id.'.extras', null);
		$session->set($option.'.subscribe.data', null);
	}
	
	function _bindId()
	{
		$option = 'com_rsmembership';
		
		$this->_id = JRequest::getInt('cid', 0);
		
		$session = JFactory::getSession();
		$session->set($option.'.subscribe.membership', $this->_id);
	}
	
	function _setId()
	{
		$option = 'com_rsmembership';
		
		$session = JFactory::getSession();
		$this->_id = (int) $session->get($option.'.subscribe.membership', 0);
	}
	
	function _bindExtras()
	{
		$option = 'com_rsmembership';
		
		$post = JRequest::get('post');
		if (empty($post['rsmembership_extra'])) return;
		
		$cid = $this->_id;
		
		$this->_db->setQuery("SELECT `extra_id` FROM #__rsmembership_membership_extras WHERE `membership_id`='".$cid."'");
		$membership_extras = $this->_db->loadResultArray();
		if (count($membership_extras) == 0) return;
		
		$this->_db->setQuery("SELECT `id` FROM #__rsmembership_extra_values WHERE `extra_id` IN (".implode(',', $membership_extras).")");
		$membership_extra_values = $this->_db->loadResultArray();
		
		foreach ($post['rsmembership_extra'] as $extra_value_id)
		{
			if (is_array($extra_value_id))
				foreach ($extra_value_id as $id)
				{
					if (in_array($id, $membership_extra_values))
						$this->_extras[] = (int) $id;
				}
			else
			{
				if (in_array($extra_value_id, $membership_extra_values))
					$this->_extras[] = (int) $extra_value_id;
			}
		}
		
		$session = JFactory::getSession();
		$session->set($option.'.subscribe.'.$cid.'.extras', $this->_extras);
	}
	
	function _setExtras()
	{
		$option = 'com_rsmembership';
		
		$cid = $this->_id;
		
		$session = JFactory::getSession();
		$this->_extras = $session->get($option.'.subscribe.'.$cid.'.extras', array());
	}
	
	function getExtras()
	{
		$return = array();
		
		if (is_array($this->_extras))
			foreach ($this->_extras as $extra)
			{
				$row =& JTable::getInstance('RSMembership_Extra_Values','Table');
				$row->load($extra);
				$return[] = $row;
			}
			
		return $return;
	}
	
	function getConfig()
	{
		return RSMembershipHelper::getConfig();
	}
	
	function getMembership()
	{
		$cid = $this->_id;
		
		$this->_db->setQuery("SELECT * FROM #__rsmembership_memberships WHERE `id`='".(int) $cid."' AND `published`='1'");
		$membership = $this->_db->loadObject();
		
		if (empty($membership))
		{
			$mainframe =& JFactory::getApplication();
			JError::raiseWarning(500, JText::_('RSM_SESSION_EXPIRED'));
			$mainframe->redirect(JRoute::_('index.php?option=com_rsmembership', false));
		}
		
		if ($membership->use_trial_period)
		{
			$membership->price = $membership->trial_price;
			$membership->period = $membership->trial_period;
			$membership->period_type = $membership->trial_period_type;
		}
		
		$this->checkCoupon($membership);
		
		$this->term_id = $membership->term_id;
		
		return $membership;
	}
	
	function getHasCoupons()
	{
		$this->_db->setQuery("SELECT id FROM #__rsmembership_coupons WHERE `published`='1' LIMIT 1");
		return $this->_db->loadResult();
	}
	
	function checkCoupon(&$membership, $verbose=true)
	{
		// Check if entered any coupon code
		$coupon_entered = $this->getCoupon();
		if (!$coupon_entered)
			return true;
		
		// Check if coupon exists
		$this->_db->setQuery("SELECT * FROM #__rsmembership_coupons WHERE `name`='".$this->_db->getEscaped($coupon_entered)."' AND `published`='1'");
		$coupon = $this->_db->loadObject();
		if (!$coupon)
		{
			if ($verbose)
				JError::raiseWarning(500, JText::_('RSM_COUPON_INVALID'));
			return $this->removeCoupon();
		}
		
		$now = RSMembershipHelper::getCurrentDate();
		
		// Check if promotion hasn't started yet
		if ($coupon->date_start && RSMembershipHelper::getCurrentDate($coupon->date_start) > $now)
			return $this->removeCoupon();
		
		// Check if expired
		if ($coupon->date_end && RSMembershipHelper::getCurrentDate($coupon->date_end) < $now)
		{
			if ($verbose)
				JError::raiseWarning(500, JText::_('RSM_COUPON_CODE_EXPIRED'));
			return $this->removeCoupon();
		}
		
		// Check if valid for this membership
		$this->_db->setQuery("SELECT `membership_id` FROM #__rsmembership_coupon_items WHERE `coupon_id`='".$coupon->id."'");
		$coupon->items = $this->_db->loadResultArray();
		if (count($coupon->items) && !in_array($membership->id, $coupon->items))
		{
			if ($verbose)
				JError::raiseWarning(500, JText::_('RSM_COUPON_CODE_NOT_VALID_FOR_MEMBERSHIP'));
			return $this->removeCoupon();
		}
		
		// Check max uses
		if ($coupon->max_uses > 0)
		{
			$this->_db->setQuery("SELECT COUNT(id) FROM #__rsmembership_transactions WHERE `status`='completed' AND `coupon`='".$this->_db->getEscaped($coupon->name)."'");
			$used = $this->_db->loadResult();
			if ($used >= $coupon->max_uses)
			{
				if ($verbose)
					JError::raiseWarning(500, JText::_('RSM_COUPON_MAX_USAGE'));
				return $this->removeCoupon();
			}
		}
		
		if ($coupon->discount_type == 0)
			$coupon->discount_price = $membership->price * ($coupon->discount_price / 100);
		
		$membership->price -= $coupon->discount_price;
		if ($membership->price < 0)
			$membership->price = 0;
		
		return true;
	}
	
	function getCoupon()
	{
		if (isset($this->_data->coupon))
			return $this->_data->coupon;
		
		return '';
	}
	
	function removeCoupon()
	{
		$this->_data->coupon = '';
		return false;
	}
	
	function getMembershipTerms()
	{
		if (!empty($this->term_id))
		{
			$row =& JTable::getInstance('RSMembership_Terms','Table');
			$row->load($this->term_id);
			if ($row->published)
				return $row;
		}
		
		return false;
	}
	
	function getCountries()
	{
		return $this->_getList("SELECT * FROM #__rsmembership_countries ORDER BY `name` ASC");
	}
	
	function _bindData($verbose=true)
	{
		$option = 'com_rsmembership';
		jimport('joomla.mail.helper');
		
		$return = true;
		
		$post = JRequest::get('post');
		if (empty($post))
			return false;
		
		$this->_data = new stdClass();
		$user =& JFactory::getUser();
		
		$choose_username = RSMembershipHelper::getConfig('choose_username');
		if ($choose_username)
		{
			$post['username'] = str_replace('-', '_', JFilterOutput::linkXHTMLSafe(@$post['username']));
			if ($user->get('guest'))
			{
				if (empty($post['username']) || strlen($post['username']) < 2)
				{
					if ($verbose)
						JError::raiseWarning(500, JText::_('RSM_PLEASE_TYPE_USERNAME'));
			
					$return = false;
				}
				$this->_db->setQuery("SELECT id FROM #__users WHERE username='".$this->_db->getEscaped($post['username'])."'");
				if ($this->_db->loadResult())
				{
					if ($verbose)
						JError::raiseWarning(500, JText::_('RSM_USERNAME_NOT_OK'));
					
					$return = false;
				}
			}
			$this->_data->username = $user->get('guest') ? @$post['username'] : $user->get('username');
		}
		$choose_password = RSMembershipHelper::getConfig('choose_password');
		if ($choose_password)
		{
			$password = JRequest::getVar('password', '', 'default', 'none', JREQUEST_ALLOWRAW);
			$password2 = JRequest::getVar('password2', '', 'default', 'none', JREQUEST_ALLOWRAW);
			if ($user->get('guest'))
			{
				if (!strlen($password))
				{
					if ($verbose)
						JError::raiseWarning(500, JText::_('RSM_PLEASE_TYPE_PASSWORD'));
			
					$return = false;
				}
				elseif (strlen($password) < 6)
				{
					if ($verbose)
						JError::raiseWarning(500, JText::_('RSM_PLEASE_TYPE_PASSWORD_6'));
			
					$return = false;
				}
				elseif ($password != $password2)
				{
					if ($verbose)
						JError::raiseWarning(500, JText::_('RSM_PLEASE_CONFIRM_PASSWORD'));
			
					$return = false;
				}
			}
			$this->_data->password = $user->get('guest') ? md5($password) : '';
		}
		
		if ($user->get('guest') && empty($post['name']))
		{
			if ($verbose)
				JError::raiseWarning(500, JText::_('RSM_PLEASE_TYPE_NAME'));
			
			$return = false;
		}
		$this->_data->name = $user->get('guest') ? @$post['name'] : $user->get('name');
		
		if ($user->get('guest') && (empty($post['email']) || !JMailHelper::isEmailAddress($post['email'])))
		{
			if ($verbose)
				JError::raiseWarning(500, JText::_('RSM_PLEASE_TYPE_EMAIL'));
				
			$return = false;
		}
		$this->_data->email = $user->get('guest') ? @$post['email'] : $user->get('email');
		
		$db =& JFactory::getDBO();
		$db->setQuery("SELECT * FROM #__rsmembership_fields WHERE (required='1' OR `rule` != '') AND published='1' ORDER BY ordering");
		$fields = $db->loadObjectList();
		foreach ($fields as $field)
		{
			if (($field->required && empty($post['rsm_fields'][$field->name])) || ($field->rule && !empty($post['rsm_fields'][$field->name]) && is_callable('RSMembershipValidation', $field->rule) && !call_user_func(array('RSMembershipValidation', $field->rule), @$post['rsm_fields'][$field->name])))
			{
				$validation_message = JText::_($field->validation);
				if (empty($validation_message))
					$validation_message = JText::sprintf('RSM_VALIDATION_DEFAULT_ERROR', JText::_($field->label));
					
				if ($verbose)
					JError::raiseWarning(500, $validation_message);
					
				$return = false;
			}
		}
		$this->_data->fields = @$post['rsm_fields'];
		
		// coupon
		$this->_data->coupon = JRequest::getVar('coupon');
		
		$captcha_enabled = RSMembershipHelper::getConfig('captcha_enabled');
		$use_captcha = $this->getUseCaptcha();
		if ($use_captcha && $captcha_enabled && $verbose)
		{
			if ($captcha_enabled == 1)
			{
				if (!class_exists('JSecurImage'))
					require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_rsmembership'.DS.'helpers'.DS.'securimage'.DS.'securimage.php');
					
				$captcha_image = new JSecurImage();
				
				$valid = $captcha_image->check($post['captcha']);
				if (!$valid)
				{
					JError::raiseNotice(500, JText::_('RSM_CAPTCHA_ERROR'));
					$return = false;
				}
			}
			elseif ($captcha_enabled == 2)
			{
				if (!class_exists('JReCAPTCHA'))
					require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_rsmembership'.DS.'helpers'.DS.'recaptcha'.DS.'recaptchalib.php');
					
				$privatekey = RSMembershipHelper::getConfig('recaptcha_private_key');
				
				$response = JReCAPTCHA::checkAnswer($privatekey, @$_SERVER['REMOTE_ADDR'], @$post['recaptcha_challenge_field'], @$post['recaptcha_response_field']);
				
				if ($response === false || !$response->is_valid)
				{
					$this->recaptcha_error = @$response->error;
					JError::raiseNotice(500, JText::_('RSM_CAPTCHA_ERROR'));
					$return = false;
				}
			}
		}
		
		$session = JFactory::getSession();
		$session->set($option.'.subscribe.data', $this->_data);
		
		return $return;
	}
	
	function _checkCoupon()
	{
		$cid = JRequest::getInt('cid');
		
		$this->_db->setQuery("SELECT id, price FROM #__rsmembership_memberships WHERE `id`='".$cid."' AND `published`='1'");
		$membership = $this->_db->loadObject();
		return $this->checkCoupon($membership, false);
	}
	
	function _setData()
	{
		$option = 'com_rsmembership';
		
		$empty = new stdClass();
		$session = JFactory::getSession();
		$this->_data = $session->get($option.'.subscribe.data', $empty);
	}
	
	function getData()
	{
		if (!empty($this->_data->fields) && is_array($this->_data->fields))
		{
			JRequest::setVar('rsm_fields', $this->_data->fields, 'post');
		}
		
		return $this->_data;
	}
	
	function getHTML()
	{
		return $this->_html;
	}
	
	function getUser()
	{
		$user =& JFactory::getUser();
		if ($user->get('guest')) return false;
		
		$this->_db->setQuery("SELECT * FROM #__rsmembership_users WHERE `user_id`='".$user->get('id')."'");
		return $this->_db->loadObject();
	}
	
	function getTransactionId()
	{
		return $this->transaction_id;
	}
	
	function getUseCaptcha()
	{
		$captcha_enabled = RSMembershipHelper::getConfig('captcha_enabled');
		if (!$captcha_enabled) return false;
		
		$captcha_enabled_for = RSMembershipHelper::getConfig('captcha_enabled_for');
		$captcha_enabled_for = explode(',', $captcha_enabled_for);
		$user =& JFactory::getUser();
		
		$enabled_for_unregistered = $captcha_enabled_for[0];
		$enabled_for_registered = $captcha_enabled_for[1];
		
		$is_logged = !$user->get('guest');
		
		if (!$is_logged && $enabled_for_unregistered)
			return true;
		
		if ($is_logged && $enabled_for_registered)
			return true;
		
		return false;
	}
	
	function getUseBuiltin()
	{
		$captcha_enabled = RSMembershipHelper::getConfig('captcha_enabled');
		return $captcha_enabled == 1;
	}
	
	function getUseRecaptcha()
	{
		$captcha_enabled = RSMembershipHelper::getConfig('captcha_enabled');
		return $captcha_enabled == 2;
	}
	
	function getRecaptchaError()
	{
		return $this->recaptcha_error;
	}
}
?>