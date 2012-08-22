<?php
/**
* @version 2.0.0
* @package RSTickets! Pro 2.0.0
* @copyright (C) 2010 www.rsjoomla.com
* @license GPL, http://www.gnu.org/licenses/gpl-2.0.html
*/

defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.model');

class RSTicketsProModelHistory extends JModel
{
	var $_data = null;
	var $_total = 0;
	var $_query = '';
	var $_pagination = null;
	var $_db = null;
	var $_permissions = null;
	
	var $params = null;
	
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
		
		$ticket_viewing_history = RSTicketsProHelper::getConfig('ticket_viewing_history');
		if (!$ticket_viewing_history)
		{
			JError::raiseWarning(500, JText::_('RST_CANNOT_VIEW_HISTORY'));
			$mainframe->redirect(RSTicketsProHelper::route('index.php?option=com_rsticketspro&view=rsticketspro', false));
		}
		
		if ($ticket_viewing_history == 1 && !RSTicketsProHelper::isStaff())
		{
			JError::raiseWarning(500, JText::_('RST_CANNOT_VIEW_HISTORY'));
			$mainframe->redirect(RSTicketsProHelper::route('index.php?option=com_rsticketspro&view=rsticketspro', false));
		}
		
		$this->_db = JFactory::getDBO();
		
		// Get pagination request variables
		$limit		= JRequest::getVar('limit', $mainframe->getCfg('list_limit'), '', 'int');
		$limitstart	= JRequest::getVar('limitstart', 0, '', 'int');

		// In case limit has been changed, adjust it
		$limitstart = ($limit != 0 ? (floor($limitstart / $limit) * $limit) : 0);

		$this->setState($option.'.history.limit', $limit);
		$this->setState($option.'.history.limitstart', $limitstart);
		
		$this->_query = $this->_buildQuery();
		
		$document =& JFactory::getDocument();
		$document->addStyleSheet(JURI::root(true).'/templates/system/css/system.css');
		$document->addStyleSheet(JURI::root(true).'/templates/system/css/general.css');
	}
	
	function _buildQuery()
	{
		$mainframe =& JFactory::getApplication();
		
		$ticket_id = JRequest::getInt('ticket_id');
		$what = RSTicketsProHelper::getConfig('show_user_info');
		
		$query  = "SELECT h.*, u.".$this->_db->getEscaped($what)." AS user FROM #__rsticketspro_ticket_history h LEFT JOIN #__users u ON (h.user_id = u.id) WHERE h.ticket_id='".$ticket_id."'";
		
		$query .= " ORDER BY h.date DESC";
		
		return $query;
	}
	
	function getHistory()
	{
		$option = 'com_rsticketspro';
		
		if (empty($this->_data))
			$this->_data = $this->_getList($this->_query, $this->getState($option.'.history.limitstart'), $this->getState($option.'.history.limit'));
		
		return $this->_data;
	}
	
	function getTotal()
	{
		if (empty($this->_total))
			$this->_total = $this->_getListCount($this->_query); 
		
		return $this->_total;
	}
	
	function getPagination()
	{
		if (empty($this->_pagination))
		{
			$option = 'com_rsticketspro';
			jimport('joomla.html.pagination');
			$this->_pagination = new JPagination($this->getTotal(), $this->getState($option.'.history.limitstart'), $this->getState($option.'.history.limit'));
		}
		
		return $this->_pagination;
	}
}
?>