<?php
/**
* @version 2.0.0
* @package RSTickets! Pro 2.0.0
* @copyright (C) 2010 www.rsjoomla.com
* @license GPL, http://www.gnu.org/licenses/gpl-2.0.html
*/

defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.model');

class RSTicketsProModelSignature extends JModel
{	
	function __construct()
	{
		parent::__construct();
		
		$mainframe =& JFactory::getApplication();
		$option    = 'com_rsticketspro';
		
		$user = JFactory::getUser();
		if ($user->get('guest'))
		{
			$link = JRequest::getURI();
			$link = base64_encode($link);
			$user_option = RSTicketsProHelper::isJ16() ? 'com_users' : 'com_user';
			$mainframe->redirect(RSTicketsProHelper::route('index.php?option='.$user_option.'&view=login&return='.$link, false));
		}
		
		if (!RSTicketsProHelper::isStaff())
		{
			JError::raiseWarning(500, JText::_('RST_CANNOT_CHANGE_SIGNATURE'));
			$mainframe->redirect(RSTicketsProHelper::route('index.php?option=com_rsticketspro&view=rsticketspro', false));
		}
		
		$this->_db = JFactory::getDBO();
		$this->_db->setQuery("SELECT id FROM #__rsticketspro_staff WHERE user_id='".(int) $user->get('id')."' LIMIT 1");
		if (!$this->_db->loadResult())
		{
			JError::raiseWarning(500, JText::_('RST_CANNOT_CHANGE_SIGNATURE_MUST_BE_STAFF'));
			$referer = @$_SERVER['HTTP_REFERER'];
			if (empty($referer))
				$mainframe->redirect(RSTicketsProHelper::route('index.php?option=com_rsticketspro&view=rsticketspro', false));
			else
				$mainframe->redirect($referer);
		}
	}
	
	function save()
	{
		$mainframe =& JFactory::getApplication();
		
		$signature = JRequest::getVar('signature', '', 'post', 'none', JREQUEST_ALLOWHTML);
		$user = JFactory::getUser();
		
		$this->_db->setQuery("UPDATE #__rsticketspro_staff SET signature='".$this->_db->getEscaped($signature)."' WHERE user_id='".(int) $user->get('id')."' LIMIT 1");
		$this->_db->query();
		
		$mainframe->redirect(RSTicketsProHelper::route('index.php?option=com_rsticketspro&view=signature', false), JText::_('RST_SIGNATURE_OK'));
	}
}
?>