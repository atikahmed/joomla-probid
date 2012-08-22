<?php
/**
* @version 2.0.0
* @package RSTickets! Pro 2.0.0
* @copyright (C) 2010 www.rsjoomla.com
* @license GPL, http://www.gnu.org/licenses/gpl-2.0.html
*/

// no direct access
defined('_JEXEC') or die('Restricted access');

class JHTMLRSTicketsProGrid
{
	function sort($title, $order, $direction = 'asc', $selected = 0, $task = null, $new_direction = 'asc')
	{
		if (RSTicketsProHelper::isJ16())
		{
			$onclick 		 = 'onclick="Joomla.tableOrdering(\'' . $order . '\',\'' . $direction . '\',\'' . $task . '\');"';
			$correct_onclick = 'onclick="Joomla.tableOrdering(\'' . $order . '\',\'' . $direction . '\',\'' . $task . '\'); return false;"';
			
			$html 		= JHTML::_('grid.sort', $title, $order, $direction, $selected, $task, $new_direction);
			$html 		= str_replace($onclick, $correct_onclick, $html);
			return $html;
		}
		
		return JHTML::_('grid.sort', $title, $order, $direction, $selected, $task);
	}
}
	
class JHTMLRSTicketsProIcon
{	
	function editmessage($message, $is_staff, $permissions, $attribs = null)
	{
		// only staff members can update replies
		if (!$is_staff)
			return;
		
		$user = JFactory::getUser();
		
		// can update his own replies
		if (!$permissions->update_ticket_replies && $message->user_id == $user->get('id'))
			return;
		
		// can update customer replies
		$is_customer = !RSTicketsProHelper::isStaff($message->user_id);
		if (!$permissions->update_ticket_replies_customers && $is_customer)
			return;
	
		// can update staff replies
		$is_other_staff = !$is_customer && $message->user_id != $user->get('id');
		if (!$permissions->update_ticket_replies_staff && $is_other_staff)
			return;
		
		$url = RSTicketsProHelper::route('index.php?option=com_rsticketspro&view=ticketmessage&cid='.$message->id.'&tmpl=component');
		$img = JHTML::_('image.site', 'edit.png', '/components/com_rsticketspro/assets/images/', null, null, JText::_('RST_TICKET_EDIT_MESSAGE'));
		
		$return = '<span class="hasTip" title="'.JText::_('RST_TICKET_EDIT_MESSAGE_DESC').'" '.$attribs.'><a href="'.$url.'" class="modal" rel="{handler: \'iframe\', size: {x: 660, y: 475}, closeWithOverlay: false}">'.$img.'</a></span>';
		
		return $return;
	}
	
	function deletemessage($message, $is_staff, $permissions, $attribs = null)
	{
		// only staff members can delete replies
		if (!$is_staff)
			return;
			
		$user = JFactory::getUser();
		
		// can delete his own replies
		if (!$permissions->delete_ticket_replies && $message->user_id == $user->get('id'))
			return;
		
		// can delete customer replies
		$is_customer = !RSTicketsProHelper::isStaff($message->user_id);
		if (!$permissions->delete_ticket_replies_customers && $is_customer)
			return;
	
		// can delete staff replies
		$is_other_staff = !$is_customer && $message->user_id != $user->get('id');
		if (!$permissions->delete_ticket_replies_staff && $is_other_staff)
			return;
		
		$url = RSTicketsProHelper::route('index.php?option=com_rsticketspro&task=deletemessage&cid='.$message->id);
		$img = JHTML::_('image.site', 'delete.png', '/components/com_rsticketspro/assets/images/', null, null, JText::_('RST_TICKET_DELETE_MESSAGE'));
		
		$return = '<span class="hasTip" title="'.JText::_('RST_TICKET_DELETE_MESSAGE_DESC').'" '.$attribs.'><a href="'.$url.'" onclick="return confirm(\''.JText::_('RST_DELETE_TICKET_MESSAGE_CONFIRM', true).'\')">'.$img.'</a></span>';
		
		return $return;
	}
	
	function viewnotes($ticket, $is_staff, $permissions, $attribs = null)
	{
		if (!$is_staff)
			return;
		
		if (!$permissions->view_notes)
			return;
		
		$url = RSTicketsProHelper::route('index.php?option=com_rsticketspro&view=notes&ticket_id='.$ticket->id.'&tmpl=component');
		$img = JHTML::_('image.site', 'notes.gif', '/components/com_rsticketspro/assets/images/', null, null, JText::_('RST_TICKET_VIEW_NOTES'));
		
		$return  = '<span class="hasTip" title="'.JText::_('RST_TICKET_VIEW_NOTES_DESC').'" '.$attribs.'><a href="'.$url.'" class="modal rst_view_notes" rel="{handler: \'iframe\', size: {x: 600, y: 475}, closeWithOverlay: false}"> '.JText::sprintf('RST_TICKET_VIEW_NOTES_NO', $ticket->notes).'</a></span>';
			
		return $return;
	}
	
	function editnote($note, $permissions, $attribs = null)
	{
		$user = JFactory::getUser();
		
		// can update his own notes
		if (!$permissions->update_note && $note->user_id == $user->get('id'))
			return;
	
		// can update other staff notes
		if (!$permissions->update_note_staff && $note->user_id != $user->get('id'))
			return;
		
		$url = RSTicketsProHelper::route('index.php?option=com_rsticketspro&controller=notes&task=edit&cid='.$note->id.'&tmpl=component');
		$img = JHTML::_('image.site', 'edit.png', '/components/com_rsticketspro/assets/images/', null, null, JText::_('RST_TICKET_EDIT_NOTE'));
		
		$return = '<span class="hasTip" title="'.JText::_('RST_TICKET_EDIT_NOTE_DESC').'" '.$attribs.'><a href="'.$url.'" class="modal" rel="{handler: \'iframe\', size: {x: 660, y: 475}, closeWithOverlay: false}">'.$img.'</a></span>';
		
		return $return;
	}
	
	function deletenote($note, $permissions, $attribs = null)
	{
		$user = JFactory::getUser();
		
		// can delete his own notes
		if (!$permissions->delete_note && $note->user_id == $user->get('id'))
			return;
	
		// can delete other staff notes
		if (!$permissions->delete_note_staff && $note->user_id != $user->get('id'))
			return;
		
		$url = RSTicketsProHelper::route('index.php?option=com_rsticketspro&controller=notes&task=delete&cid='.$note->id);
		$img = JHTML::_('image.site', 'delete.png', '/components/com_rsticketspro/assets/images/', null, null, JText::_('RST_TICKET_DELETE_NOTE'));
		
		$return = '<span class="hasTip" title="'.JText::_('RST_TICKET_DELETE_NOTE_DESC').'" '.$attribs.'><a href="'.$url.'" onclick="return confirm(\''.JText::_('RST_DELETE_TICKET_NOTE_CONFIRM', true).'\')">'.$img.'</a></span>';
		
		return $return;
	}
	
	function printticket($ticket, $attribs = null)
	{
		$url = RSTicketsProHelper::route('index.php?option=com_rsticketspro&view=ticket&cid='.$ticket->id.'&print=1&tmpl=component');
		
		$status = 'status=no,toolbar=no,scrollbars=yes,titlebar=no,menubar=no,resizable=yes,width=640,height=480,directories=no,location=no';
		$onclick = "window.open(this.href,'win2','".$status."'); return false;";
		$return  = '<span class="hasTip" title="'.JText::_('RST_TICKET_PRINT_DESC').'" '.$attribs.'><a href="'.$url.'" onclick="'.$onclick.'" class="rst_print_ticket"> '.JText::_('RST_TICKET_PRINT').'</a></span>';
		
		return $return;
	}
	
	function closereopenticket($ticket, $is_staff, $permissions, $attribs = null)
	{
		$return = '';
		$can_close = $is_staff ? $permissions->change_ticket_status : RSTicketsProHelper::getConfig('allow_ticket_closing');
		$can_open = $is_staff ? $permissions->change_ticket_status : RSTicketsProHelper::getConfig('allow_ticket_reopening');
		
		$user = JFactory::getUser();
		if ($ticket->customer_id == $user->get('id'))
		{
			$can_close = RSTicketsProHelper::getConfig('allow_ticket_closing');
			$can_open = $is_staff ? $permissions->change_ticket_status : RSTicketsProHelper::getConfig('allow_ticket_reopening');
		}
		
		if ($ticket->status_id != 2 && $can_close)
		{			
			$url = RSTicketsProHelper::route('index.php?option=com_rsticketspro&controller=ticket&task=close&cid='.$ticket->id);
			$return = '<span class="hasTip" title="'.JText::_('RST_TICKET_CLOSE_DESC').'" '.$attribs.'><a href="'.$url.'" class="rst_close_ticket">'.JText::_('RST_TICKET_CLOSE').'</a></span>';
		}
		elseif ($ticket->status_id == 2 && $can_open)
		{
			$url = RSTicketsProHelper::route('index.php?option=com_rsticketspro&controller=ticket&task=reopen&cid='.$ticket->id);
			$return = '<span class="hasTip" title="'.JText::_('RST_TICKET_OPEN_DESC').'" '.$attribs.'><a href="'.$url.'" class="rst_open_ticket">'.JText::_('RST_TICKET_OPEN').'</a></span>';
		}
		
		return $return;
	}
	
	function deleteticket($cid, $is_staff, $permissions, $attribs = null)
	{
		if (!$is_staff)
			return;
		
		if (!$permissions->delete_ticket)
			return;
		
		$url = RSTicketsProHelper::route('index.php?option=com_rsticketspro&controller=ticket&task=delete&cid='.$cid);
		$img = JHTML::_('image.site', 'delete.png', '/components/com_rsticketspro/assets/images/', null, null, JText::_('RST_TICKET_DELETE'), '');
		
		$return = '<span class="hasTip" title="'.JText::_('RST_TICKET_DELETE_DESC').'" '.$attribs.'><a class="rst_delete_ticket" href="'.$url.'" onclick="return confirm(\''.JText::_('RST_DELETE_TICKET_CONFIRM', true).'\')">'.$img.'</a></span>';
		
		return $return;
	}
	
	function notify($is_staff, $ticket, $attribs = null)
	{
		if (!$is_staff)
			return;
		
		if (!RSTicketsProHelper::getConfig('autoclose_enabled'))
			return;
		
		if ($ticket->last_reply_customer)
			return;
		
		if ($ticket->autoclose_sent)
			return;
		
		if ($ticket->status_id == 2)
			return;
		
		$interval = RSTicketsProHelper::getConfig('autoclose_email_interval') * 86400;
		if ($interval < 86400)
			$interval = 86400;
		
		$date = JFactory::getDate();
		$date = $date->toUnix();
		$date = RSTicketsProHelper::getCurrentDate($date);
		
		$last_reply_interval = RSTicketsProHelper::getCurrentDate($ticket->last_reply) + $interval;
		
		if ($last_reply_interval > $date)
			return;
		
		$overdue = floor(($date - $last_reply_interval) / 86400);
		
		$url = RSTicketsProHelper::route('index.php?option=com_rsticketspro&controller=ticket&task=notify&cid='.$ticket->id);
		$img = JHTML::_('image.site', 'notify.gif', '/components/com_rsticketspro/assets/images/', null, null, JText::_('RST_TICKET_NOTIFY'), 'class="rst_notify_ticket"');
		
		$return = '<span class="hasTip" title="'.JText::sprintf('RST_TICKET_NOTIFY_DESC', $overdue).'" '.$attribs.'><a href="'.$url.'">'.$img.'</a></span>';
		
		return $return;
	}
	
	function history($ticket, $is_staff, $attribs = null)
	{
		$ticket_viewing_history = RSTicketsProHelper::getConfig('ticket_viewing_history');
		
		if (!$ticket_viewing_history)
			return;
		
		if ($ticket_viewing_history == 1 && !$is_staff)
			return;
		
		$url = RSTicketsProHelper::route('index.php?option=com_rsticketspro&view=history&ticket_id='.$ticket->id.'&tmpl=component');
		$img = JHTML::_('image.site', 'history.gif', '/components/com_rsticketspro/assets/images/', null, null, JText::_('RST_TICKET_VIEW_HISTORY'));
		
		$return  = '<span class="hasTip" title="'.JText::_('RST_TICKET_VIEW_HISTORY_DESC').'" '.$attribs.'><a href="'.$url.'" class="modal rst_view_history" rel="{handler: \'iframe\', size: {x: 600, y: 475}, closeWithOverlay: false}"> '.JText::_('RST_TICKET_VIEW_HISTORY').'</a></span>';
		
		return $return;
	}
	
	function deletesearch($search, $attribs = null)
	{		
		$url = RSTicketsProHelper::route('index.php?option=com_rsticketspro&controller=searches&task=delete&cid='.$search->id);
		$img = JHTML::_('image.site', 'delete.png', '/components/com_rsticketspro/assets/images/', null, null, JText::_('RST_DELETE_SEARCH'));
		
		$return = '<span class="hasTip" title="'.JText::_('RST_DELETE_SEARCH_DESC').'" '.$attribs.'><a href="'.$url.'" onclick="return confirm(\''.JText::_('RST_DELETE_SEARCH_CONFIRM', true).'\')">'.$img.'</a></span>';
		
		return $return;
	}
}

class JHTMLRSTicketsProCalendar
{
	function calendar($show_time = false, $value, $name, $id, $format = '%Y-%m-%d', $attribs = null)
	{
		JHTML::_('behavior.calendar'); //load the calendar behavior

		if (is_array($attribs)) {
			$attribs = JArrayHelper::toString( $attribs );
		}
		$document =& JFactory::getDocument();
		$document->addScriptDeclaration('window.addEvent(\'domready\', function() {Calendar.setup({
        inputField     :    "'.$id.'",     // id of the input field
        ifFormat       :    "'.$format.'",      // format of the input field
        button         :    "'.$id.'_img",  // trigger for the calendar (button ID)
        align          :    "Tl",           // alignment (defaults to "Bl")
        singleClick    :    true,
		showsTime      :    '.($show_time ? 'true' : 'false').'
    });});');

		return '<input type="text" name="'.$name.'" id="'.$id.'" value="'.htmlspecialchars($value, ENT_COMPAT, 'UTF-8').'" '.$attribs.' />'.
				 '<img class="calendar" src="'.JURI::root(true).'/templates/system/images/calendar.png" alt="calendar" id="'.$id.'_img" />';
	}
}