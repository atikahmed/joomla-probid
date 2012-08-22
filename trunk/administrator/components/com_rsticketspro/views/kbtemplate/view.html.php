<?php
/**
* @version 2.0.0
* @package RSTickets! Pro 2.0.0
* @copyright (C) 2010 www.rsjoomla.com
* @license GPL, http://www.gnu.org/licenses/gpl-2.0.html
*/

defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.view');

class RSTicketsProViewKBTemplate extends JView
{
	function display($tpl = null)
	{
		$mainframe =& JFactory::getApplication();
		
		JToolBarHelper::title('RSTickets! Pro','rsticketspro');
		
		JSubMenuHelper::addEntry(JText::_('RST_BACK_TO_RSTICKETSPRO'), 'index.php?option=com_rsticketspro');
		JSubMenuHelper::addEntry(JText::_('RST_KNOWLEDGEBASE'), 'index.php?option=com_rsticketspro&view=knowledgebase');
		JSubMenuHelper::addEntry(JText::_('RST_KB_CATEGORIES'), 'index.php?option=com_rsticketspro&view=kbcategories');
		JSubMenuHelper::addEntry(JText::_('RST_KB_ARTICLES'), 'index.php?option=com_rsticketspro&view=kbcontent');
		JSubMenuHelper::addEntry(JText::_('RST_KB_CONVERSION_RULES'), 'index.php?option=com_rsticketspro&view=kbrules');
		JSubMenuHelper::addEntry(JText::_('RST_KB_TEMPLATE'), 'index.php?option=com_rsticketspro&view=kbtemplate', true);
		
		JToolBarHelper::apply('applykbtemplate');
		JToolBarHelper::save('savekbtemplate');
		JToolBarHelper::cancel('cancelkbtemplate');
		
		$editor =& JFactory::getEditor();
		$this->assignRef('editor', $editor);
		
		$config = RSTicketsProHelper::getConfig();
		$this->assignRef('config', $config);
		
		parent::display($tpl);
	}
}