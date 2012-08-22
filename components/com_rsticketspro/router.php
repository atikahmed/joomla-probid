<?php
/**
* @version 2.0.0
* @package RSTickets! Pro 2.0.0
* @copyright (C) 2010 www.rsjoomla.com
* @license GPL, http://www.gnu.org/licenses/gpl-2.0.html
*/

defined( '_JEXEC' ) or die( 'Restricted access' );

function RSTicketsProBuildRoute(&$query)
{
	$lang =& JFactory::getLanguage();
	$lang->load('com_rsticketspro', JPATH_SITE);
	
	$segments = array();
	
	// get a menu item based on Itemid or currently active
	$menu = &JSite::getMenu();
	if (!empty($query['Itemid']) && $item =& $menu->getItem($query['Itemid']))
	{
		if (isset($item->query['view']) && isset($query['view']) && $item->query['view'] == $query['view'] && !isset($query['cid']))
		{
			unset($query['view']);
			return $segments;
		}
	}
	
	if (!empty($query['view']))
		switch ($query['view'])
		{
			case 'submit':
				$segments[] = JText::_('RST_SEF_SUBMIT_TICKET');
			break;
			
			case 'users':
				$segments[] = JText::_('RST_SEF_SELECT_USER_FROM_LIST');
			break;
			
			case 'rsticketspro':
				$segments[] = JText::_('RST_SEF_TICKETS');
			break;
			
			case 'searches':
				$segments[] = JText::_('RST_SEF_SEARCHES');
			break;
			
			case 'ticket':
				if (!empty($query['print']))
				{
					$segments[] = JText::_('RST_SEF_PRINT_TICKET');
					unset($query['print']);
				}
				else
					$segments[] = JText::_('RST_SEF_TICKET');
					
				$segments[] = @$query['cid'];
			break;
			
			case 'history':
				$segments[] = JText::_('RST_SEF_HISTORY');
				$segments[] = @$query['ticket_id'];
			break;
			
			case 'notes':
				$segments[] = JText::_('RST_SEF_NOTES');
				$segments[] = @$query['ticket_id'];
			break;
			
			case 'ticketmessage':
				$segments[] = JText::_('RST_SEF_EDIT_TICKET_MESSAGE');
				$segments[] = @$query['cid'];
			break;
			
			case 'search':
				if (!empty($query['advanced']))
				{
					$segments[] = JText::_('RST_SEF_ADVANCED_SEARCH');
					unset($query['advanced']);
				}
				else
					$segments[] = JText::_('RST_SEF_SEARCH');
			break;
			
			case 'signature':
				$segments[] = JText::_('RST_SEF_SIGNATURE');
			break;
			
			case 'knowledgebase':
				if (!isset($query['layout']))
					$query['layout'] = 'default';
				
				if ($query['layout'] == 'default')
				{
					$segments[] = JText::_('RST_SEF_KB');
					if (!empty($query['cid']))
						$segments[] = $query['cid'];
				}
				else
				{
					$segments[] = JText::_('RST_SEF_KB_RESULTS');
				}
			break;
			
			case 'article':
				$segments[] = JText::_('RST_SEF_KB_ARTICLE');
				$segments[] = @$query['cid'];
			break;
			
			case 'dashboard':
				$segments[] = JText::_('RST_SEF_DASHBOARD');
			break;
		}
	
	if (!empty($query['task']))
		switch ($query['task'])
		{
			case 'deletemessage':
				$segments[] = JText::_('RST_SEF_DELETE_TICKET_MESSAGE');
				$segments[] = @$query['cid'];
			break;
			
			case 'kbsearch':
				$segments[] = JText::_('RST_SEF_KB_SEARCH');
			break;
			
			case 'resetsearch':
				$segments[] = JText::_('RST_SEF_RESET_SEARCH');
			break;
			
			case 'download':
				$segments[] = JText::_('RST_SEF_DOWNLOAD');
				$segments[] = @$query['cid'];
				$segments[] = @$query['file_id'];
			break;
			
			case 'viewinline':
				$segments[] = 'view-inline';
				$segments[] = @$query['cid'];
			break;
			
			case 'captcha':
				$segments[] = 'captcha';
			break;
			
			case 'dashboard':
				$segments[] = JText::_('RST_SEF_DASHBOARD');
			break;
			
			case 'dashboardsearch':
				$segments[] = JText::_('RST_SEF_DASHBOARD_SEARCH');
			break;
		}

	if (@$query['controller'] == 'ticket')
		switch ($query['task'])
		{
			case 'flag':
				$segments[] = JText::_('RST_SEF_FLAG_TICKET');
			break;
			
			case 'feedback':
				$segments[] = JText::_('RST_SEF_FEEDBACK_TICKET');
			break;
			
			case 'delete':
				$segments[] = JText::_('RST_SEF_DELETE_TICKET');
				$segments[] = @$query['cid'];
			break;
			
			case 'notify':
				$segments[] = JText::_('RST_SEF_NOTIFY_TICKET');
				$segments[] = @$query['cid'];
			break;
			
			case 'close':
				$segments[] = JText::_('RST_SEF_CLOSE_TICKET');
				$segments[] = @$query['cid'];
			break;
			
			case 'reopen':
				$segments[] = JText::_('RST_SEF_REOPEN_TICKET');
				$segments[] = @$query['cid'];
			break;
		}
	
	if (@$query['controller'] == 'searches')
		switch ($query['task'])
		{
			case 'search':
				$segments[] = JText::_('RST_SEF_PREDEFINED_SEARCH');
				$segments[] = @$query['cid'];
			break;
			
			case 'save':
				$segments[] = JText::_('RST_SEF_SAVE_PREDEFINED_SEARCH');
			break;
			
			case 'edit':
				if (!empty($query['cid']))
				{
					$segments[] = JText::_('RST_SEF_EDIT_PREDEFINED_SEARCH');
					$segments[] = $query['cid'];
				}
				else
					$segments[] = JText::_('RST_SEF_NEW_PREDEFINED_SEARCH');
			break;
			
			case 'delete':
				$segments[] = JText::_('RST_SEF_DELETE_PREDEFINED_SEARCH');
				$segments[] = @$query['cid'];
			break;
		}
	
	if (@$query['controller'] == 'notes')
		switch ($query['task'])
		{
			case 'edit':
				$segments[] = JText::_('RST_SEF_EDIT_NOTE');
				$segments[] = @$query['cid'];
			break;
			
			case 'delete':
				$segments[] = JText::_('RST_SEF_DELETE_NOTE');
				$segments[] = @$query['cid'];
			break;
		}
	
	unset($query['task'], $query['view'], $query['controller'], $query['cid'], $query['ticket_id'], $query['file_id']);
	unset($query['tmpl']);
	unset($query['layout']);
	
	return $segments;
}

function RSTicketsProParseRoute($segments)
{
	$lang =& JFactory::getLanguage();
	
	$lang->load('com_rsticketspro', JPATH_SITE, 'en-GB', true);
	$lang->load('com_rsticketspro', JPATH_SITE, $lang->getDefault(), true);
	$lang->load('com_rsticketspro', JPATH_SITE, null, true);
	
	$query = array();
	
	$segments[0] = str_replace(':', '-', $segments[0]);
	
	switch ($segments[0])
	{
		case JText::_('RST_SEF_SUBMIT_TICKET'):
			$query['view'] = 'submit';
		break;
		
		case JText::_('RST_SEF_SELECT_USER_FROM_LIST'):
			$query['view'] = 'users';
			$query['tmpl'] = 'component';
		break;
		
		case JText::_('RST_SEF_TICKETS'):
			$query['view'] = 'rsticketspro';
		break;
		
		case JText::_('RST_SEF_SEARCHES'):
			$query['view'] = 'searches';
		break;
		
		case JText::_('RST_SEF_TICKET'):
			$query['view'] = 'ticket';
			$query['cid'] = @$segments[1];	
		break;
		
		case JText::_('RST_SEF_PRINT_TICKET'):
			$query['view'] = 'ticket';
			$query['cid'] = @$segments[1];
			$query['tmpl'] = 'component';
			$query['print'] = 1;
		break;
		
		case JText::_('RST_SEF_HISTORY'):
			$query['view'] = 'history';
			$query['tmpl'] = 'component';
			$query['ticket_id'] = @$segments[1];
		break;
		
		case JText::_('RST_SEF_NOTES'):
			$query['view'] = 'notes';
			$query['tmpl'] = 'component';
			$query['ticket_id'] = @$segments[1];
		break;
		
		case JText::_('RST_SEF_EDIT_TICKET_MESSAGE'):
			$query['view'] = 'ticketmessage';
			$query['cid'] = @$segments[1];
			$query['tmpl'] = 'component';
		break;
		
		case JText::_('RST_SEF_SEARCH'):
			$query['view'] = 'search';
		break;
		
		case JText::_('RST_SEF_ADVANCED_SEARCH'):
			$query['view'] = 'search';
			$query['advanced'] = 'true';
		break;
		
		case JText::_('RST_SEF_SIGNATURE'):
			$query['view'] = 'signature';
		break;
		
		case JText::_('RST_SEF_KB'):
			$query['view'] = 'knowledgebase';
			if (!empty($segments[1]))
				$query['cid'] = $segments[1];
		break;
		
		case JText::_('RST_SEF_KB_RESULTS'):
			$query['view']   = 'knowledgebase';
			$query['layout'] = 'results';
		break;
		
		case JText::_('RST_SEF_KB_ARTICLE'):
			$query['view'] = 'article';
			$query['cid'] = @$segments[1];
		break;
		
		case JText::_('RST_SEF_DELETE_TICKET_MESSAGE'):
			$query['task'] = 'deletemessage';
			$query['cid'] = @$segments[1];
		break;
		
		case JText::_('RST_SEF_KB_SEARCH'):
			$query['task'] = 'kbsearch';
		break;
		
		case JText::_('RST_SEF_FLAG_TICKET'):
			$query['task'] = 'flag';
			$query['controller'] = 'ticket';
		break;
		
		case JText::_('RST_SEF_FEEDBACK_TICKET'):
			$query['task'] = 'feedback';
			$query['controller'] = 'ticket';
		break;
		
		case JText::_('RST_SEF_DELETE_TICKET'):
			$query['task'] = 'delete';
			$query['controller'] = 'ticket';
			$query['cid'] = @$segments[1];
		break;
		
		case JText::_('RST_SEF_NOTIFY_TICKET'):
			$query['task'] = 'notify';
			$query['controller'] = 'ticket';
			$query['cid'] = @$segments[1];
		break;
		
		case JText::_('RST_SEF_CLOSE_TICKET'):
			$query['task'] = 'close';
			$query['controller'] = 'ticket';
			$query['cid'] = @$segments[1];
		break;
		
		case JText::_('RST_SEF_REOPEN_TICKET'):
			$query['task'] = 'reopen';
			$query['controller'] = 'ticket';
			$query['cid'] = @$segments[1];
		break;
		
		case JText::_('RST_SEF_PREDEFINED_SEARCH'):
			$query['task'] = 'search';
			$query['controller'] = 'searches';
			$query['cid'] = @$segments[1];
		break;
		
		case JText::_('RST_SEF_DELETE_PREDEFINED_SEARCH'):
			$query['task'] = 'delete';
			$query['controller'] = 'searches';
			$query['cid'] = @$segments[1];
		break;
		
		case JText::_('RST_SEF_SAVE_PREDEFINED_SEARCH'):
			$query['task'] = 'search';
			$query['controller'] = 'searches';
		break;
		
		case JText::_('RST_SEF_EDIT_PREDEFINED_SEARCH'):
			$query['task'] = 'edit';
			$query['controller'] = 'searches';
			$query['cid'] = @$segments[1];
		break;
		
		case JText::_('RST_SEF_NEW_PREDEFINED_SEARCH'):
			$query['task'] = 'edit';
			$query['controller'] = 'searches';
		break;
		
		case JText::_('RST_SEF_EDIT_NOTE'):
			$query['task'] = 'edit';
			$query['controller'] = 'notes';
			$query['cid'] = @$segments[1];
		break;
		
		case JText::_('RST_SEF_DELETE_NOTE'):
			$query['task'] = 'delete';
			$query['controller'] = 'notes';
			$query['cid'] = @$segments[1];
		break;
		
		case JText::_('RST_SEF_RESET_SEARCH'):
			$query['task'] = 'resetsearch';
		break;
		
		case JText::_('RST_SEF_DOWNLOAD'):
			$query['task'] = 'download';
			$query['cid'] = @$segments[1];
			$query['file_id'] = @$segments[2];
		break;
		
		case 'view-inline':
			$query['task'] = 'viewinline';
			$query['cid'] = @$segments[1];
		break;
		
		case 'captcha':
			$query['task'] = 'captcha';
		break;
		
		case JText::_('RST_SEF_DASHBOARD'):
			$query['view'] = 'dashboard';
		break;
		
		case JText::_('RST_SEF_DASHBOARD_SEARCH'):
			$query['task'] = 'dashboardsearch';
		break;
	}
	
	return $query;
}
?>