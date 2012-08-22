<?php
/**
* @version 1.0.0
* @package RSMembership! 1.0.0
* @copyright (C) 2009-2010 www.rsjoomla.com
* @license GPL, http://www.gnu.org/licenses/gpl-2.0.html
*/

defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.model');

class RSMembershipModelUpgrade extends JModel
{
	var $_html = '';
	var $transaction_id = 0;
	var $term_id;
	var $to_id = 0;
	
	function __construct()
	{
		parent::__construct();
		
		$user = JFactory::getUser();
		if ($user->get('guest'))
		{
			$mainframe =& JFactory::getApplication();
			$link = JRequest::getURI();
			$link = base64_encode($link);
			$user_option = RSMembershipHelper::isJ16() ? 'com_users' : 'com_user';
			$mainframe->redirect(JRoute::_('index.php?option='.$user_option.'&view=login&return='.$link, false));
		}
		
		$this->_execute();
	}
	
	function _execute()
	{
		$mainframe =& JFactory::getApplication();
		$option = 'com_rsmembership';
		$task = JRequest::getVar('task', '');
		
		if ($task == 'upgrade')
		{
			$this->_bindId();
		}
		else
		{
			$this->_setId();
			
			if ($task == 'upgradepayment')
			{
				// empty session
				$this->_emptySession();
			
				$extras = array();
				$upgrade = $this->getUpgrade();
				$membership = $this->getMembership($upgrade->membership_to_id);
				$paymentplugin = JRequest::getCmd('payment', 'none');
				
				// calculate the total price
				$total = $upgrade->price;

				$user =& JFactory::getUser();
				$user_id = $user->get('id');
				
				$row =& JTable::getInstance('RSMembership_Transactions','Table');
				$row->user_id = $user_id;
				$row->user_email = $user->get('email');
				
				$this->_data = new stdClass();
				$this->_data->username = $user->get('username');
				$this->_data->name = $user->get('name');
				$this->_data->email = $user->get('email');
				$this->_data->fields = RSMembershipHelper::getUserFields($user->get('id'));
				$row->user_data = serialize($this->_data);
				
				$row->type = 'upgrade';
				$params = array();
				$params[] = 'id='.$this->_id;
				$params[] = 'from_id='.$upgrade->membership_from_id;
				$params[] = 'to_id='.$upgrade->membership_to_id;
				
				$row->params = implode(';', $params); // params, membership, extras etc
				$date = JFactory::getDate();
				$row->date = $date->toUnix();
				$row->ip = $_SERVER['REMOTE_ADDR'];
				$row->price = $total;
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
				if (class_exists($className) && method_exists($className, 'onMembershipPayment'))
				{
					$dispatcher  =& JDispatcher::getInstance();
					$plugin 	 = new $className($dispatcher, array());
					$args   	 = array('plugin' => $paymentplugin, 'data' => &$this->_data, 'extras' => $extras, 'membership' => $membership, 'transaction' => &$row);
					$this->_html = call_user_func_array(array($plugin, 'onMembershipPayment'), $args);
					
					if (method_exists($className, 'hasDelayTransactionStoring'))
					{
						$delay = $plugin->hasDelayTransactionStoring();
						if (method_exists($className, 'delayTransactionStoring'))
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
	
	function _setId()
	{
		$option = 'com_rsmembership';
		
		$session = JFactory::getSession();
		$this->_id = (int) $session->get($option.'.upgrade.cid', 0);
		$this->to_id = (int) $session->get($option.'.upgrade.to_id', 0);
	}
	
	function _bindId()
	{
		$option = 'com_rsmembership';
		
		$this->_id = JRequest::getInt('cid', 0);
		$this->to_id = JRequest::getInt('to_id', 0);
		
		$session = JFactory::getSession();
		$session->set($option.'.upgrade.cid', $this->_id);
		$session->set($option.'.upgrade.to_id', $this->to_id);
	}
	
	function _emptySession()
	{
		$option = 'com_rsmembership';
		
		$session = JFactory::getSession();
		$session->set($option.'.upgrade.cid', null);
		$session->set($option.'.upgrade.to_id', null);
	}
	
	function getCid()
	{
		return JRequest::getInt('cid');
	}
	
	function getUpgrade()
	{
		$cid = $this->_id;
		
		$user = JFactory::getUser();
		$this->_db->setQuery("SELECT `membership_id`, `status` FROM #__rsmembership_membership_users WHERE `user_id`='".$user->get('id')."' AND `id`='".$cid."'");
		$membership = $this->_db->loadObject();
		
		$mainframe =& JFactory::getApplication();
		if (empty($membership))
			$mainframe->redirect(JRoute::_('index.php?option=com_rsmembership&view=mymemberships', false));
		
		if ($membership->status != 0 && $membership->status != 2)
		{
			JError::raiseWarning(500, JText::_('RSM_MEMBERSHIP_NOT_ACTIVE'));
			$mainframe->redirect(JRoute::_('index.php?option=com_rsmembership&view=mymemberships', false));
		}
		
		$this->_db->setQuery("SELECT u.*, mfrom.name as fromname, mto.name as toname, mto.term_id FROM #__rsmembership_membership_upgrades u LEFT JOIN #__rsmembership_memberships mfrom ON (mfrom.id = u.membership_from_id) LEFT JOIN #__rsmembership_memberships mto ON (mto.id = u.membership_to_id) WHERE u.membership_from_id ='".$membership->membership_id."' AND u.membership_to_id='".$this->to_id."'");
		$return = $this->_db->loadObject();
		
		$this->term_id = $return->term_id;
		
		if (empty($return))
		{
			$mainframe =& JFactory::getApplication();
			$mainframe->redirect(JRoute::_('index.php?option=com_rsmembership&view=mymemberships', false));
		}
		
		return $return;
	}
	
	function getMembership($cid)
	{
		$this->_db->setQuery("SELECT * FROM #__rsmembership_memberships WHERE `id`='".(int) $cid."'");
		return $this->_db->loadObject();
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
	
	function getData()
	{
		$user =& JFactory::getUser();
		$this->_db->setQuery("SELECT * FROM #__rsmembership_users WHERE `user_id`='".$user->get('id')."'");
		return $this->_db->loadObject();
	}
	
	function getConfig()
	{
		return RSMembershipHelper::getConfig();
	}
	
	function getUser()
	{
		$user =& JFactory::getUser();
		return $user;
	}
	
	function getTransactionId()
	{
		return $this->transaction_id;
	}
	
	function getHTML()
	{		
		return $this->_html;
	}
}
?>