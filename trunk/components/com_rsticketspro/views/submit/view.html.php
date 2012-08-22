<?php
/**
* @version 2.0.0
* @package RSTickets! Pro 2.0.0
* @copyright (C) 2010 www.rsjoomla.com
* @license GPL, http://www.gnu.org/licenses/gpl-2.0.html
*/

defined('_JEXEC') or die('Restricted access');

jimport( 'joomla.application.component.view');

class RSTicketsProViewSubmit extends JView
{
	function display($tpl = null)
	{
		$mainframe =& JFactory::getApplication();
		
		if (!$mainframe->isAdmin())
		{
			$params = $mainframe->getParams('com_rsticketspro');
			$this->assignRef('params', $params);
		}
		
		$data = $this->get('data');
		$this->assignRef('data', $data);
		
		$this->assignRef('custom_fields', $this->get('customfields'));
		
		$user = JFactory::getUser();
		$this->assign('is_logged', !$user->get('guest'));
		$this->assign('is_staff', RSTicketsProHelper::isStaff());
		$this->assign('permissions', RSTicketsProHelper::getCurrentPermissions());
		$this->assignRef('user', $user);
		
		$this->assign('use_editor', RSTicketsProHelper::getConfig('allow_rich_editor'));
		$this->assignRef('editor', JFactory::getEditor());
		
		$show_please_select = true;
		$lists['priorities'] =  JHTML::_('select.genericlist', RSTicketsProHelper::getPriorities($show_please_select), 'priority_id', '', 'value', 'text', @$data['priority_id'], 'submit_priority');
		$lists['departments'] =  JHTML::_('select.genericlist', RSTicketsProHelper::getDepartments($show_please_select), 'department_id', 'onchange="rst_show_custom_fields(this.value); rst_show_priority(this.value); rst_show_upload(this.value); rst_show_subject(this.value);"', 'value', 'text', @$data['department_id']);
		$lists['subject'] = JHTML::_('select.genericlist', RSTicketsProHelper::getSubjects(@$data['department_id'], $show_please_select), 'subject', '', 'value', 'text', @$data['subject'], 'submit_subject');
		$this->assignRef('lists', $lists);
		
		$this->assignRef('departments', $this->get('departments'));
		$this->assign('can_upload', $this->get('canupload'));
		$this->assign('use_captcha', $this->get('usecaptcha'));
		$this->assign('use_builtin', $this->get('usebuiltin'));
		$this->assign('use_recaptcha', $this->get('userecaptcha'));
		if ($this->get('userecaptcha') && $mainframe->isSite())
			$this->assign('show_recaptcha', JReCAPTCHA::getHTML($this->get('recaptchaerror')));
		
		$this->assign('show_footer', RSTicketsProHelper::getConfig('rsticketspro_link'));
		$this->assign('footer', RSTicketsProHelper::getFooter());
		$this->assign('use_predefined_subjects', RSTicketsProHelper::getConfig('allow_predefined_subjects'));
		
		if ($mainframe->isAdmin())
		{
			$this->assign('checked_create_new_user', JRequest::getVar('submit_type', 1) == 1);
			$this->assign('checked_existing_user', JRequest::getVar('submit_type', 1) == 2);
		}
		
		if (RSTicketsProHelper::isJ16() && $mainframe->isSite())
		{
			// Description
			if ($params->get('menu-meta_description'))
				$this->document->setDescription($params->get('menu-meta_description'));
			// Keywords
			if ($params->get('menu-meta_keywords'))
				$this->document->setMetadata('keywords', $params->get('menu-meta_keywords'));
			// Robots
			if ($params->get('robots'))
				$this->document->setMetadata('robots', $params->get('robots'));
		}
		
		parent::display();
	}
}