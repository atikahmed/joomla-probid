<?php
/**
* @version 1.0.0
* @package RSMembership! Authorize.Net 1.0.0
* @copyright (C) 2011 www.rsjoomla.com
* @license GPL, http://www.gnu.org/licenses/gpl-2.0.html
*/

defined('_JEXEC') or die('Restricted access');

jimport( 'joomla.plugin.plugin' );

if (file_exists(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_rsmembership'.DS.'helpers'.DS.'rsmembership.php') && !class_exists('RSMembershipHelper'))
	require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_rsmembership'.DS.'helpers'.DS.'rsmembership.php');

class plgSystemRSMembershipAuthorize extends JPlugin
{
	var $_db;
	var $joomla16prefix = '';
	
	function canRun()
	{
		return file_exists(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_rsmembership'.DS.'helpers'.DS.'rsmembership.php');
	}
	
	function plgSystemRSMembershipAuthorize(&$subject, $config)
	{
		parent::__construct($subject, $config);
		$this->_plugin =& JPluginHelper::getPlugin('system', 'rsmembershipauthorize');
		jimport('joomla.html.parameter');
		$this->_params = new JParameter($this->_plugin->params);
		
		if (!$this->canRun()) return;
		RSMembership::addPlugin('Credit Card', 'rsmembershipauthorize');
		
		$this->_db = JFactory::getDBO();
		
		if (RSMembershipHelper::isJ16())
			$this->joomla16prefix = 'rsmembershipauthorize/';
		
		$lang =& JFactory::getLanguage();
		$lang->load('plg_system_rsmembershipauthorize', JPATH_ADMINISTRATOR);
	}

	function onMembershipCancelPayment($plugin, $data, $membership, &$transaction)
	{
		if (!$this->canRun()) return;
		if ($plugin != $this->_plugin->name) return false;		
		if (!$membership->recurring || $membership->period == 0) return false;
		
		$content =	"<ARBCancelSubscriptionRequest xmlns=\"AnetApi/xml/v1/schema/AnetApiSchema.xsd\">" .
					"<merchantAuthentication>".
					"<name>" . $this->_params->get('x_login') . "</name>".
					"<transactionKey>" . $this->_params->get('x_tran_key') . "</transactionKey>".
					"</merchantAuthentication>".
					"<refId>0</refId>".
					"<subscriptionId>".$transaction->custom."</subscriptionId>".
					/* This line added by FDS GT */
					"<sandbox>true</sandbox>".
					"</ARBCancelSubscriptionRequest>";
		
		/*$post_url = $this->_params->get('mode') ? "https://api.authorize.net/xml/v1/request.api" : "https://apitest.authorize.net/xml/v1/request.api";*/
		$post_url = $this->_params->get('mode') ? "https://developer.authorize.net/tools/paramdump/index.php" : "https://developer.authorize.net/tools/paramdump/index.php";
		
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $post_url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_HTTPHEADER, Array("Content-Type: text/xml"));
		curl_setopt($ch, CURLOPT_HEADER, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $content);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
		$response = curl_exec($ch);
		
		$log = array();
		$log[] = 'Cancelled by request. Response is below:';
		$log[] = '--- START ---';
		list ($refId, $resultCode, $code, $text, $subscriptionId) = $this->_parseReturn($response);
		$log[] = 'Ref Id: '.$refId;
		$log[] = 'Result Code: '.$resultCode;
		$log[] = 'Code: '.$code;
		$log[] = 'Text: '.$text;
		$log[] = 'Subscription Id: '.$subscriptionId;
		$log[] = '--- END ---';
		
		RSMembership::saveTransactionLog($log, $transaction->id);
		
		if ($resultCode == 'Ok')
			return true;
		
		JError::raiseWarning(500, $text);
		return false;
	}
	
	function onMembershipPayment($plugin, $data, $extra, $membership, &$transaction)
	{
		if (!$this->canRun()) return;
		if ($plugin != $this->_plugin->name) return false;
		
		$this->loadLanguage('plg_system_rsmembership');
		$this->loadLanguage('plg_system_rsmembershipauthorize');
		
		$document =& JFactory::getDocument();
		
		$document->addScript(JURI::root().'plugins/system/'.$this->joomla16prefix.'rsmembershipauthorize/script.js');
		$document->addStyleSheet(JURI::root().'plugins/system/'.$this->joomla16prefix.'rsmembershipauthorize/style.css');
		
		JHTML::_('behavior.tooltip');
		
		$fields = $this->_getFields();
		
		$transaction->gateway = 'Authorize.Net';
		
		$html = '';
		$html .= '<form method="post" class="rsmembership_form" action="'.JRoute::_('index.php?option=com_rsmembership&task=thankyou').'" onsubmit="return rsm_check_authorize(\''.JRoute::_('index.php?option=com_rsmembership&plugin_task=authorize').'\');">';
		$html .= '<fieldset>';
		$html .= '<legend>'.JText::_('RSM_AUTHORIZE_CARD_INFO').'</legend>';
		$html .= '<table cellpadding="0" cellspacing="0" border="0" width="100%" class="rsmembership_form_table">';
		
		$html .= '<tr>';
		$html .= '<td height="40">'.$fields['cc_image'][0].'</td>';
		$html .= '<td>'.$fields['cc_image'][1].'</td>';
		$html .= '</tr>';
		
		$html .= '<tr>';
		$html .= '<td height="40">'.$fields['cc_number'][0].'</td>';
		$html .= '<td>'.$fields['cc_number'][1].' '.$fields['csc_number'][0].' '.$fields['csc_number'][1].' <span id="rsm_whats_csc" onmouseover="rsm_tooltip.show(\'rsm_tooltip\');" onmouseout="rsm_tooltip.hide();">'.JText::_('RSM_AUTHORIZE_WHATS_CSC').'</span></td>';
		$html .= '</tr>';
		
		$html .= '<tr>';
		$html .= '<td height="40">'.JText::_('RSM_AUTHORIZE_EXP_DATE').'</td>';
		$html .= '<td>'.$fields['cc_exp_mm'][0].' '.$fields['cc_exp_mm'][1].' '.$fields['cc_exp_yy'][0].' '.$fields['cc_exp_yy'][1].'</td>';
		$html .= '</tr>';
		
		$html .= '<tr>';
		$html .= '<td height="40">'.$fields['cc_fname'][0].'</td>';
		$html .= '<td>'.$fields['cc_fname'][1].'</td>';
		$html .= '</tr>';
		
		$html .= '<tr>';
		$html .= '<td height="40">'.$fields['cc_lname'][0].'</td>';
		$html .= '<td>'.$fields['cc_lname'][1].'</td>';
		$html .= '</tr>';
		
		$html .= '<tr>';
		$html .= '<td height="40" colspan="2">'.JText::sprintf('RSM_AUTHORIZE_PRICE_DESC', RSMembershipHelper::getPriceFormat($transaction->price)).'</td>';
		$html .= '</tr>';
		
		$html .= '</table>';
		$html .= '</fieldset>';
		
		$html .= '<input class="button" type="submit" id="rsm_pay_button" value="'.JText::_('RSM_AUTHORIZE_PAY_NOW').'" />';
		$html .= JHTML::image('components/com_rsmembership/assets/images/load.gif', 'Loading', 'id="rsm_loading" style="display: none;"');
		$html .= '<input type="hidden" name="membership_id" id="membership_id" value="'.$membership->id.'" />';
		$html .= '<input type="hidden" name="option" value="com_rsmembership" />';
		$html .= '<input type="hidden" name="task" value="thankyou" />';
		$html .= '</form>';
		
		$html .= '<div id="rsm_response" class="rsm_response_error">';
		$html .= '</div>';
		
		$html .= '<div id="rsm_tooltip" style="display: none;">';
		
		if (RSMembershipHelper::isJ16())
		
		$html .= '<div>'.JText::_('RSM_AUTHORIZE_WHATS_CSC_DESC', true).'</div><div align="center">'.JHTML::image('plugins/system/'.$this->joomla16prefix.'rsmembershipauthorize/images/cc_csc.gif', 'CSC').'</div>';
		$html .= '</div>';
		
		$warning = JHTML::image('plugins/system/'.$this->joomla16prefix.'rsmembershipauthorize/images/warning.png', 'Warning', array('id' => 'rsm_warning'));
		
		$html .= '<script type="text/javascript">';
		$html .= 'function rsm_get_error_message(code) {';
		$html .= 'if (code == 0) return \''.$warning.' '.JText::_('RSM_AUTHORIZE_ERRORS', true).'\';';
		$html .= '}';
		$html .= '</script>';
		
		return $html;
	}
	
	function _getFields()
	{
		$fields = array();
		
		$field = new stdClass();
		$field->id 	       = '';
		$field->name       = 'cc_number';
		$field->label      = JText::_('RSM_AUTHORIZE_CC_NUMBER');
		$field->type       = 'textbox';
		$field->values     = '';
		$field->additional = 'maxlength="16" onkeydown="return rsm_check_card(this);" onkeyup="return rsm_check_card(this);"';
		$field->required   = 1;
		$fields['cc_number'] = RSMembershipHelper::showCustomField($field, array(), true, false);
		
		$field = new stdClass();
		$field->id 	       = '';
		$field->name       = 'csc_number';
		$field->label      = JText::_('RSM_AUTHORIZE_CSC');
		$field->type       = 'textbox';
		$field->values     = '';
		$field->additional = 'style="width: 45px; text-align: center;" maxlength="4" onkeydown="return rsm_check_card(this);" onkeyup="return rsm_check_card(this);"';
		$field->required   = 1;
		$fields['csc_number'] = RSMembershipHelper::showCustomField($field, array(), true, false);
		
		$field = new stdClass();
		$field->id 	       = '';
		$field->name       = 'cc_image';
		$field->label      = '';
		$field->type       = 'freetext';
		$field->values     = JHTML::image('plugins/system/'.$this->joomla16prefix.'rsmembershipauthorize/images/cc_logos.gif', 'Credit Cards');
		$field->additional = '';
		$field->required   = 0;
		$fields['cc_image'] = RSMembershipHelper::showCustomField($field, array(), true, false);
		
		$field = new stdClass();
		$field->id 	       = '';
		$field->name       = 'cc_exp_mm';
		$field->label      = JText::_('RSM_MONTH');
		$field->type       = 'select';
		$field->values     = array();
		for ($i=1; $i<=12; $i++)
			$field->values[] = ($i < 10 ? '0'.$i : $i).'-'.JText::_('RSM_AUTHORIZE_MONTH_'.$i);
		$field->values 	   = implode("\n", $field->values);
		$field->additional = '';
		$field->required   = 1;
		$fields['cc_exp_mm'] = RSMembershipHelper::showCustomField($field, array(), true, false);
		
		$field = new stdClass();
		$field->id 	       = '';
		$field->name       = 'cc_exp_yy';
		$field->label      = JText::_('RSM_YEAR');
		$field->type       = 'textbox';
		$field->values     = '';
		$field->additional = 'style="width: 35px; text-align: center;" maxlength="4" onblur="rsm_check_year(this)" onkeydown="return rsm_check_card(this);" onkeyup="return rsm_check_card(this);"';
		$field->required   = 1;
		$fields['cc_exp_yy'] = RSMembershipHelper::showCustomField($field, array(), true, false);
		
		$field = new stdClass();
		$field->id 	       = '';
		$field->name       = 'cc_fname';
		$field->label      = JText::_('RSM_AUTHORIZE_FIRST_NAME');
		$field->type       = 'textbox';
		$field->values     = '';
		$field->additional = 'maxlength="64"';
		$field->required   = 1;
		$fields['cc_fname'] = RSMembershipHelper::showCustomField($field, array(), true, false);
		
		$field = new stdClass();
		$field->id 	       = '';
		$field->name       = 'cc_lname';
		$field->label      = JText::_('RSM_AUTHORIZE_LAST_NAME');
		$field->type       = 'textbox';
		$field->values     = '';
		$field->additional = 'maxlength="64"';
		$field->required   = 1;
		$fields['cc_lname'] = RSMembershipHelper::showCustomField($field, array(), true, false);
		
		return $fields;
	}
	
	function _getTax($price)
	{
		$tax_value = $this->_params->get('tax_value');
		if (!empty($tax_value))
		{
			$tax_type = $this->_params->get('tax_type');
			
			// percent ?
			if ($tax_type == 0)
				$tax_value = $price * ($tax_value / 100);
		}
		
		return $tax_value;
	}
	
	function hasDelayTransactionStoring()
	{
		return true;
	}
	
	function delayTransactionStoring(&$transaction)
	{
		$session =& JFactory::getSession();
		$session->set('transaction', $transaction, 'rsmembership');
	}
	
	function getDelayedTransaction()
	{		
		$session =& JFactory::getSession();
		return $session->get('transaction', null, 'rsmembership');
	}
	
	function emptyDelayedTransaction()
	{
		$session =& JFactory::getSession();
		$session->set('transaction', null, 'rsmembership');
	}
	
	function getLimitations()
	{
		$this->loadLanguage('plg_system_rsmembershipauthorize');
		return JText::_('RSM_AUTHORIZE_LIMITATIONS');
	}
	
	function onPaymentNotification()
	{
		if (!$this->canRun()) return;
		
		$subscription_id = JRequest::getInt('x_subscription_id');
		if ($subscription_id)
		{
			$db =& JFactory::getDBO();
			$response_code = JRequest::getInt('x_response_code');
			$reason_code   = JRequest::getInt('x_response_reason_code');
			
			$db->setQuery("SELECT * FROM #__rsmembership_transactions WHERE `gateway`='Authorize.Net' AND `custom`='".$subscription_id."'");
			$transaction = $db->loadObject();
			if (!$transaction) return;
			
			if ($response_code == 1)
			{
				$db->setQuery("SELECT id, membership_id FROM #__rsmembership_membership_users WHERE `from_transaction_id`='".$transaction->id."' LIMIT 1");
				$membership = $db->loadObject();
				
				JTable::addIncludePath(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_rsmembership'.DS.'tables');
				$row =& JTable::getInstance('RSMembership_Transactions','Table');
				
				$date =& JFactory::getDate();
				
				$row->user_id 	 = $transaction->user_id;
				$row->user_email = $transaction->user_email;
				$row->type 		 = 'renew';
				$params = array();
				$params[] = 'id='.$membership->id;
				$params[] = 'membership_id='.$membership->membership_id;
				$row->params 	 = implode(';', $params); // params, membership, extras etc
				$row->date 		 = $date->toUnix();
				$row->ip 		 = $_SERVER['REMOTE_ADDR'];
				$row->price 	 = JRequest::getVar('x_amount');
				$row->currency 	 = RSMembershipHelper::getConfig('currency');
				$row->hash 		 = '';
				$row->gateway 	 = 'Authorize.Net';
				$row->status 	 = 'pending';
				// store the transaction
				$row->store();
				
				RSMembership::finalize($row->id);
				RSMembership::approve($row->id);
			}
			elseif ($response_code == 2)
			{
				// declined
			}
			elseif ($response_code == 3 && $reason_code == 8)
			{
				// expired card
			}
			else
			{
				// other ?
			}
		}
	}
	
	function onAfterRoute()
	{
		$app =& JFactory::getApplication();		
		if ($app->isAdmin()) return;

		if (JRequest::getVar('authorizepayment'))
			return $this->onPaymentNotification();
		
		$option 	   = JRequest::getVar('option');
		$task   	   = JRequest::getCmd('plugin_task');
		$membership_id = JRequest::getInt('membership_id');
		if ($option == 'com_rsmembership' && $task == 'authorize')
		{
			@ob_end_clean();
			
			$db =& JFactory::getDBO();
			$db->setQuery("SELECT * FROM #__rsmembership_memberships WHERE `id`='".$membership_id."'");
			$membership = $db->loadObject();
			
			JTable::addIncludePath(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_rsmembership'.DS.'tables');
			$row =& JTable::getInstance('RSMembership_Transactions','Table');
			$transaction = $this->getDelayedTransaction();
			if (empty($transaction))
			{
				$app->enqueueMessage('RSM_SESSION_EXPIRED', 'error');
				echo 'RSM_SESSION_END';
				die();
			}
			$row->bind($transaction);
			$row->store();
			
			$row->price += $this->_getTax($row->price);
			$row->price  = $this->_convertNumber($row->price);
			
			$transaction['id'] = $row->id;
			$this->delayTransactionStoring($transaction);
			
			$description  = $this->_params->get('message_type') ? $membership->name : JText::sprintf('RSM_MEMBERSHIP_PURCHASE_ON', date(RSMembershipHelper::getConfig('date_format'), $row->date));
			$post_url 	  = $this->_params->get('mode') ? "https://secure.authorize.net/gateway/transact.dll" : "https://test.authorize.net/gateway/transact.dll";
			$is_recurring = $membership->recurring && $membership->period > 0 && $row->type == 'new';
			
			$cc_number 	   = JRequest::getCmd('cc_number', '', 'post');
			$cc_expiration = substr(JRequest::getCmd('cc_exp_mm', '', 'post'), 0, 2).'-'.JRequest::getInt('cc_exp_yy', 0, 'post');
			$cc_fname	   = JRequest::getVar('cc_fname', '', 'post');
			$cc_lname	   = JRequest::getVar('cc_lname', '', 'post');
			
			$post_values = array(
				"x_login"			=> $this->_params->get('x_login'),
				"x_tran_key"		=> $this->_params->get('x_tran_key'),

				"x_version"			=> "3.1",
				"x_delim_data"		=> "TRUE",
				"x_delim_char"		=> "|",
				"x_relay_response"	=> "FALSE",

				"x_type"			=> "AUTH_CAPTURE",
				"x_method"			=> "CC",
				"x_card_num"		=> $cc_number,
				"x_exp_date"		=> $cc_expiration,
				"x_card_code"		=> JRequest::getVar('csc_number', '', 'post'),

				"x_amount"			=> $row->price,
				"x_currency_code"	=> RSMembershipHelper::getConfig('currency'),
				"x_invoice_num"		=> md5($row->id.' '.$this->_params->get('x_login').' '.$this->_params->get('x_tran_key')), // order num (unique)
				"x_description"		=> $description,

				"x_first_name"		=> $cc_fname,
				"x_last_name"		=> $cc_lname,
				"x_email"			=> $row->get('user_email'),
				"x_address"			=> '',
				"x_state"			=> '',
				"x_zip"				=> ''
			);
			
			$string = '';
			foreach( $post_values as $key => $value )
				$string .= "$key=" . urlencode( $value ) . "&";
			$string = rtrim($string, "& ");
			unset($post_values);
			
			if (!function_exists('curl_init'))
			{
				echo JHTML::image('plugins/system/'.$this->joomla16prefix.'rsmembershipauthorize/images/error.png', 'Error', array('id' => 'rsm_warning')).' '.JText::_('RSM_AUTHORIZE_CURL_ERROR');
			}
			else
			{				
				$request = curl_init($post_url);
				curl_setopt($request, CURLOPT_HEADER, 0);
				curl_setopt($request, CURLOPT_RETURNTRANSFER, 1);
				curl_setopt($request, CURLOPT_POSTFIELDS, $string);
				curl_setopt($request, CURLOPT_SSL_VERIFYPEER, FALSE);
				$response = curl_exec($request);
				curl_close($request); // close curl object

				// This line takes the response and breaks it into an array using the specified delimiting character
				$response = explode('|',$response);
				
				if ($response[0] == 1)
				{
					if (!$is_recurring)
					{
						$this->emptyDelayedTransaction();
						
						$row->hash = $response[6];
						$row->store();
						
						RSMembership::finalize($row->get('id'));
						RSMembership::approve($row->get('id'));
					}
					else
					{
						list($length, $unit) = $this->_getAuthorizeLength($membership);
						$date 			  =& JFactory::getDate();
						$startDate 		  = date('Y-m-d', strtotime("+$length $unit", $date->toUnix()));
						
						$extra_total = 0;
						$params = RSMembershipHelper::parseParams($row->params);
						if (!empty($params['extras']))
						{
							$db->setQuery("SELECT SUM(`price`) FROM #__rsmembership_extra_values WHERE `id` IN (".implode(',', $params['extras']).")");
							$extra_total = $db->loadResult();
						}
						
						$amount 		  = $membership->use_renewal_price ? $membership->renewal_price : $membership->price;
						$amount			 += $extra_total;
						$amount 		 += $this->_getTax($amount);
						
						$trialOccurrences = $membership->use_trial_period ? 1 : 0;
						$trialAmount 	  = $membership->use_trial_period ? $membership->trial_price : 0;
						$trialAmount	 += $extra_total;
						$trialAmount	 += $this->_getTax($trialAmount);
						
						$content =
							"<?xml version=\"1.0\" encoding=\"utf-8\"?>" .
							"<ARBCreateSubscriptionRequest xmlns=\"AnetApi/xml/v1/schema/AnetApiSchema.xsd\">" .
							"<merchantAuthentication>".
							"<name>" . $this->_params->get('x_login') . "</name>".
							"<transactionKey>" . $this->_params->get('x_tran_key') . "</transactionKey>".
							"</merchantAuthentication>".
							/* This line added by FDS GT */
							"<sandbox>true</sandbox>".
							"<refId>" . $row->id . "</refId>".
							"<subscription>".
							"<name>" . htmlentities($description, ENT_COMPAT, 'UTF-8') . "</name>".
							"<paymentSchedule>".
							"<interval>".
							"<length>". $length ."</length>".
							"<unit>". $unit ."</unit>".
							"</interval>".
							"<startDate>" . $startDate . "</startDate>".
							"<totalOccurrences>9999</totalOccurrences>".
							"<trialOccurrences>". $trialOccurrences . "</trialOccurrences>".
							"</paymentSchedule>".
							"<amount>". $amount ."</amount>".
							"<trialAmount>" . $trialAmount . "</trialAmount>".
							"<payment>".
							"<creditCard>".
							"<cardNumber>" . $cc_number . "</cardNumber>".
							"<expirationDate>" . $cc_expiration . "</expirationDate>".
							"</creditCard>".
							"</payment>".
							"<billTo>".
							"<firstName>". $cc_fname . "</firstName>".
							"<lastName>" . $cc_lname . "</lastName>".
							"</billTo>".
							"</subscription>".
							"</ARBCreateSubscriptionRequest>";
							
							/*$post_url = $this->_params->get('mode') ? "https://api.authorize.net/xml/v1/request.api" : "https://apitest.authorize.net/xml/v1/request.api";*/
							$post_url = $this->_params->get('mode') ? "https://developer.authorize.net/tools/paramdump/index.php" : "https://developer.authorize.net/tools/paramdump/index.php";
							
							$ch = curl_init();
							curl_setopt($ch, CURLOPT_URL, $post_url);
							curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
							curl_setopt($ch, CURLOPT_HTTPHEADER, Array("Content-Type: text/xml"));
							curl_setopt($ch, CURLOPT_HEADER, 1);
							curl_setopt($ch, CURLOPT_POSTFIELDS, $content);
							curl_setopt($ch, CURLOPT_POST, 1);
							curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
							$response = curl_exec($ch);
							if ($response)
							{								
								list ($refId, $resultCode, $code, $text, $subscriptionId) = $this->_parseReturn($response);
								
								if ($resultCode == 1)
								{
									$this->emptyDelayedTransaction();
									
									$row->custom = $subscriptionId;
									$row->store();
									
									RSMembership::finalize($row->get('id'));
									RSMembership::approve($row->get('id'));
								}
								else
								{
									$image = $resultCode == 4 ? 'warning' : 'error';
									
									if (!$text)
									{
										$text = explode("\r\n\r\n", $response, 2);
										$text = strip_tags($text[1]);
									}
									
									echo JHTML::image('plugins/system/'.$this->joomla16prefix.'rsmembershipauthorize/images/'.$image.'.png', 'Information', array('id' => 'rsm_warning')).' '.htmlentities($text, ENT_COMPAT, 'UTF-8');
									die();
								}
							}
							else
							{
								echo JHTML::image('plugins/system/'.$this->joomla16prefix.'rsmembershipauthorize/images/error.png', 'Error').' '.JText::_('RSM_AUTHORIZE_GENERAL_ERROR');
								die();
							}
					}
					
					echo 'RSM_AUTHORIZE_OK';
				}
				else
				{
					$image = $response[0] == 4 ? 'warning' : 'error';
					echo JHTML::image('plugins/system/'.$this->joomla16prefix.'rsmembershipauthorize/images/'.$image.'.png', 'Information', array('id' => 'rsm_warning')).' '.htmlentities($response[3], ENT_COMPAT, 'UTF-8');
				}
			}
	
			die();
		}
	}
	
	function _parseReturn($content)
	{
		$refId = $this->_between($content,'<refId>','</refId>');
		$resultCode = $this->_between($content,'<resultCode>','</resultCode>');
		$code = $this->_between($content,'<code>','</code>');
		$text = $this->_between($content,'<text>','</text>');
		$subscriptionId = $this->_between($content,'<subscriptionId>','</subscriptionId>');
		return array ($refId, $resultCode, $code, $text, $subscriptionId);
	}
	
	function _between($haystack,$start,$end) 
	{
		if (strpos($haystack,$start) === false || strpos($haystack,$end) === false) 
		{
			return false;
		} 
		else 
		{
			$start_position = strpos($haystack,$start)+strlen($start);
			$end_position = strpos($haystack,$end);
			return substr($haystack,$start_position,$end_position-$start_position);
		}
	}
	
	function _getAuthorizeLength($membership)
	{
		$length = $membership->period;
		$unit 	= '';
		
		switch ($membership->period_type)
		{
			case 'h':
				$length = 7;
				$unit 	= 'days';
			break;
			
			case 'd':
				if ($membership->period > 365)
					$length = 365;
				
				$unit = 'days';
			break;
			
			case 'm':
				if ($membership->period > 12)
					$length = 12;
				
				$unit = 'months';
			break;
			
			case 'y':
				if ($membership->period > 1)
					$length = 365;
					
				$unit = 'days';
			break;
		}
		
		return array($length, $unit);
	}
	
	function _convertNumber($number)
	{
		return number_format($number, 2, '.', '');
	}
	
	function _convertPeriod($period, $type)
	{
		$return = array();
		
		$return[0] = $period;
		$return[1] = strtoupper($type);
		
		return $return;
	}
}