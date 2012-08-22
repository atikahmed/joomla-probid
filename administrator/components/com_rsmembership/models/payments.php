<?php
/**
* @version 1.0.0
* @package RSMembership! 1.0.0
* @copyright (C) 2009-2010 www.rsjoomla.com
* @license GPL, http://www.gnu.org/licenses/gpl-2.0.html
*/

defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.model');

class RSMembershipModelPayments extends JModel
{
	var $_data = null;
	var $_total = 0;
	var $_query = '';
	var $_pagination = null;
	var $_db = null;
	
	var $_id = 0;
	
	function __construct()
	{
		parent::__construct();
		$this->_db = JFactory::getDBO();
	}
	
	function getPayments()
	{
		$plugins = RSMembership::getPlugins();
		
		$return = array();
		foreach ($plugins as $paymentplugin => $name)
		{
			if (preg_match('#rsmembershipwire([0-9]+)#', $paymentplugin, $match)) continue;
			
			$tmp = new stdClass();
			$tmp->name 		  = $name;
			$tmp->limitations = '';
			
			$className = 'plgSystem'.$paymentplugin;
			if (class_exists($className) && method_exists($className, 'getLimitations'))
			{
				$dispatcher  	  =& JDispatcher::getInstance();
				$plugin 	 	  = new $className($dispatcher, array());
				$tmp->limitations = $plugin->getLimitations();
			}
			
			if (RSMembershipHelper::isJ16())
				$this->_db->setQuery("SELECT `extension_id` FROM #__extensions WHERE `type`='plugin' AND `folder`='system' AND `client_id`='0' AND `element`='".$this->_db->getEscaped($paymentplugin)."' LIMIT 1");
			else
				$this->_db->setQuery("SELECT `id` FROM #__plugins WHERE `folder`='system' AND `client_id`='0' AND `element`='".$this->_db->getEscaped($paymentplugin)."' LIMIT 1");
			$tmp->cid = $this->_db->loadResult();
			
			$return[] = $tmp;
		}
		
		$return = array_merge($return, $this->getWirePayments());
		
		return $return;
	}
	
	function getPagination()
	{
		if (empty($this->_pagination))
		{
			$option = 'com_rsmembership';
			jimport('joomla.html.pagination');
			
			$this->_pagination = new JPagination($this->getTotal(), 0, 0);
		}
		
		return $this->_pagination;
	}
	
	function getTotal()
	{
		if (empty($this->_total))
			$this->_total = count($this->_data);
		
		return $this->_total;
	}
	
	function getPayment()
	{
		$cid = JRequest::getVar('cid', 0);
		if (is_array($cid))
			$cid = $cid[0];
		$cid = (int) $cid;

		$row =& JTable::getInstance('RSMembership_Payments','Table');
		$row->load($cid);

		return $row;
	}
	
	function getId()
	{
		return $this->_id;
	}

	function remove($cids)
	{
		$cids = implode(',', $cids);

		$this->_db->setQuery("DELETE FROM #__rsmembership_payments WHERE `id` IN (".$cids.")");
		$this->_db->query();

		return true;
	}

	function save()
	{
		$row =& JTable::getInstance('RSMembership_Payments','Table');
		$post = JRequest::get('post');
		$post['details'] = JRequest::getVar('details', '', 'post', 'none', JREQUEST_ALLOWRAW);

		if (!$row->bind($post))
			return JError::raiseWarning(500, $row->getError());

		if (empty($row->id))
			$row->ordering = $row->getNextOrder();
			
		if ($row->store())
		{
			return true;
		}
		else
		{
			JError::raiseWarning(500, $row->getError());
			return false;
		}
	}
	
	function getWirePayments()
	{
		if (empty($this->_data))
			$this->_data = $this->_getList("SELECT * FROM #__rsmembership_payments ORDER BY `ordering` ASC");
		
		return $this->_data;
	}
	
	function getLimitations()
	{
		$plugins = RSMembership::getPlugins();
		$return = array();
		foreach ($plugins as $paymentplugin => $plugin)
		{
			$return[$paymentplugin] = '';
			
		}
		return $return;
	}
}
?>