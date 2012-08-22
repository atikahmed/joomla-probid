<?php
/**
* @version 1.0.0
* @package RSMembership! 1.0.0
* @copyright (C) 2009-2010 www.rsjoomla.com
* @license GPL, http://www.gnu.org/licenses/gpl-2.0.html
*/

defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.view');

class RSMembershipViewMemberships extends JView
{
	function display($tpl = null)
	{
		$task = JRequest::getVar('task','');
		
		$row = $this->get('membership');
		$this->assignRef('row', $row);
		$this->assignRef('attachments', $this->get('attachments'));
		$this->assignRef('attachmentsPagination', $this->get('attachmentsPagination'));
		
		if ($email_type = JRequest::getVar('email_type'))
		{
			$this->email_type 				  = $this->escape($email_type);
			$this->row->attachments 		  = isset($this->attachments[$email_type]) 			 ? $this->attachments[$email_type] 			 : array();
			$this->row->attachmentsPagination = isset($this->attachmentsPagination[$email_type]) ? $this->attachmentsPagination[$email_type] : null;
		}
		
		parent::display($tpl);
	}
}
?>