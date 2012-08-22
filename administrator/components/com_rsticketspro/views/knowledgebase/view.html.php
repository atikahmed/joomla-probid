<?php
/**
* @version 2.0.0
* @package RSTickets! Pro 2.0.0
* @copyright (C) 2010 www.rsjoomla.com
* @license GPL, http://www.gnu.org/licenses/gpl-2.0.html
*/

defined('_JEXEC') or die('Restricted access');

jimport( 'joomla.application.component.view');

class RSTicketsProViewKnowledgebase extends JView
{
	function display($tpl = null)
	{
		JToolBarHelper::title('RSTickets! Pro','rsticketspro');
		
		JSubMenuHelper::addEntry(JText::_('RST_BACK_TO_RSTICKETSPRO'), 'index.php?option=com_rsticketspro');
		JSubMenuHelper::addEntry(JText::_('RST_KNOWLEDGEBASE'), 'index.php?option=com_rsticketspro&view=knowledgebase', true);
		JSubMenuHelper::addEntry(JText::_('RST_KB_CATEGORIES'), 'index.php?option=com_rsticketspro&view=kbcategories');
		JSubMenuHelper::addEntry(JText::_('RST_KB_ARTICLES'), 'index.php?option=com_rsticketspro&view=kbcontent');
		JSubMenuHelper::addEntry(JText::_('RST_KB_CONVERSION_RULES'), 'index.php?option=com_rsticketspro&view=kbrules');
		
		$this->assign('code', RSTicketsProHelper::getConfig('global_register_code'));
		
		parent::display($tpl);
		parent::display('version');
	}
}