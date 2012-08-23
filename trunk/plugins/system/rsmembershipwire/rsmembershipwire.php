<?php
/**
* @version 1.0.0
* @package RSMembership! PayPal 1.0.0
* @copyright (C) 2009 www.rsjoomla.com
* @license GPL, http://www.gnu.org/licenses/gpl-2.0.html
*/

defined('_JEXEC') or die('Restricted access');

jimport('joomla.plugin.plugin');

if (file_exists(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_rsmembership'.DS.'helpers'.DS.'rsmembership.php'))
	require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_rsmembership'.DS.'helpers'.DS.'rsmembership.php');

class plgSystemRSMembershipWire extends JPlugin
{
	function canRun()
	{
		return file_exists(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_rsmembership'.DS.'helpers'.DS.'rsmembership.php');
	}
	
	function plgSystemRSMembershipWire(&$subject, $config)
	{
		parent::__construct($subject, $config);
		
		jimport('joomla.html.parameter');
		
		$this->_plugin =& JPluginHelper::getPlugin('system', 'rsmembershipwire');
		$this->_params = new JParameter($this->_plugin->params);
		
		if (!$this->canRun()) return;
		
		$db =& JFactory::getDBO();
		$db->setQuery("SELECT * FROM #__rsmembership_payments ORDER BY `ordering`");
		$payments = $db->loadObjectList();
		
		foreach ($payments as $payment)
			RSMembership::addPlugin($payment->name, 'rsmembershipwire'.$payment->id);
	}
	
	function onMembershipPayment($plugin, $data, $extra, $membership, &$transaction)
	{
		$this->loadLanguage('plg_system_rsmembership', JPATH_ADMINISTRATOR);
		$this->loadLanguage('plg_system_rsmembershipwire', JPATH_ADMINISTRATOR);

		if (!$this->canRun()) return;
		if (!preg_match('#rsmembershipwire([0-9]+)#', $plugin, $match)) return false;
		
		$id = (int) $match[1];
		
		$db =& JFactory::getDBO();
		$db->setQuery("SELECT * FROM #__rsmembership_payments WHERE `id`='".$id."'");
		$payment = $db->loadObject();
		
		$tax_value = $payment->tax_value;
		if (!empty($tax_value))
		{
			$tax_type = $payment->tax_type;
			
			// percent ?
			if ($tax_type == 0)
				$tax_value = $transaction->price * ($tax_value / 100);
				
			$transaction->price = $transaction->price + $tax_value;
		}
		
		$html  = '';
		$html .= $payment->details;
		
		$replace = array('{price}', '{membership}');
		$with 	 = array(RSMembershipHelper::getPriceFormat($transaction->price), $membership->name);
		
		$html = str_replace($replace, $with, $html);
		
		$html .= '<form method="post" action="'.JRoute::_('index.php?option=com_rsmembership&task=thankyou').'">';
		$html .= '<input class="button" type="submit" value="'.JText::_('RSM_CONTINUE').'" />';
		$html .= '<input type="hidden" name="option" value="com_rsmembership" />';
		$html .= '<input type="hidden" name="task" value="thankyou" />';
		$html .= '</form>';
		
		// No hash for this
		$transaction->hash = '';
		$transaction->gateway = $payment->name;
		
		if ($membership->activation == 2)
			$transaction->status = 'completed';
		
		return $html;
	}
}