<?php
/**
* @version 2.0.0
* @package RSTickets! Pro 2.0.0
* @copyright (C) 2010 www.rsjoomla.com
* @license GPL, http://www.gnu.org/licenses/gpl-2.0.html
*/

defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.view');

class RSTicketsProViewEmails extends JView
{
	function display($tpl = null)
	{
		$mainframe =& JFactory::getApplication();
		
		JToolBarHelper::title('RSTickets! Pro','rsticketspro');
		
		JSubMenuHelper::addEntry(JText::_('RST_MANAGE_TICKETS'), 'index.php?option=com_rsticketspro&view=tickets');
		JSubMenuHelper::addEntry(JText::_('RST_DEPARTMENTS'), 'index.php?option=com_rsticketspro&view=departments');
		JSubMenuHelper::addEntry(JText::_('RST_GROUPS'), 'index.php?option=com_rsticketspro&view=groups');
		JSubMenuHelper::addEntry(JText::_('RST_STAFF_MEMBERS'), 'index.php?option=com_rsticketspro&view=staff');
		JSubMenuHelper::addEntry(JText::_('RST_PRIORITIES'), 'index.php?option=com_rsticketspro&view=priorities');
		JSubMenuHelper::addEntry(JText::_('RST_STATUSES'), 'index.php?option=com_rsticketspro&view=statuses');
		JSubMenuHelper::addEntry(JText::_('RST_KNOWLEDGEBASE'), 'index.php?option=com_rsticketspro&view=knowledgebase');
		JSubMenuHelper::addEntry(JText::_('RST_EMAIL_MESSAGES'), 'index.php?option=com_rsticketspro&view=emails', true);
		JSubMenuHelper::addEntry(JText::_('RST_CONFIGURATION'), 'index.php?option=com_rsticketspro&view=configuration');
		$mainframe->triggerEvent('onAfterTicketsMenu');
		JSubMenuHelper::addEntry(JText::_('RST_UPDATES'), 'index.php?option=com_rsticketspro&view=updates');
		
		$task = JRequest::getVar('task','');
		
		if ($task == 'edit')
		{
			JToolBarHelper::title('RSTickets! Pro <small>['.JText::_('RST_EDIT_EMAIL').']</small>','rsticketspro');
			
			JToolBarHelper::apply();
			JToolBarHelper::save();
			JToolBarHelper::cancel();
			
			$row = $this->get('email');
			$this->assignRef('row', $row);
			
			$this->assignRef('editor', JFactory::getEditor());
			$this->assignRef('languages', $this->get('languages'));
		}
		else
		{			
			$languages = $this->get('languages');
			$this->assignRef('languages', $languages);
			$languages_array = array();
			foreach ($languages as $code => $language)
				$languages_array[] = JHTML::_('select.option', $code, $language['name']);
			$language = $this->get('language');
			$lists['languages'] = JHTML::_('select.genericlist', $languages_array, 'language', '', 'value', 'text', $language);
			$this->assignRef('language', $language);
			
			$this->assignRef('lists', $lists);
			
			$this->assignRef('emails', $this->get('emails'));
		}
		
		parent::display($tpl);
	}
}