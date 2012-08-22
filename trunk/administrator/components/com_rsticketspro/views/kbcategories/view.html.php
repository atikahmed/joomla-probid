<?php
/**
* @version 2.0.0
* @package RSTickets! Pro 2.0.0
* @copyright (C) 2010 www.rsjoomla.com
* @license GPL, http://www.gnu.org/licenses/gpl-2.0.html
*/

defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.view');

class RSTicketsProViewKBCategories extends JView
{
	function display($tpl = null)
	{
		$mainframe =& JFactory::getApplication();
		
		JToolBarHelper::title('RSTickets! Pro','rsticketspro');
		
		JSubMenuHelper::addEntry(JText::_('RST_BACK_TO_RSTICKETSPRO'), 'index.php?option=com_rsticketspro');
		JSubMenuHelper::addEntry(JText::_('RST_KNOWLEDGEBASE'), 'index.php?option=com_rsticketspro&view=knowledgebase');
		JSubMenuHelper::addEntry(JText::_('RST_KB_CATEGORIES'), 'index.php?option=com_rsticketspro&view=kbcategories', true);
		JSubMenuHelper::addEntry(JText::_('RST_KB_ARTICLES'), 'index.php?option=com_rsticketspro&view=kbcontent');
		JSubMenuHelper::addEntry(JText::_('RST_KB_CONVERSION_RULES'), 'index.php?option=com_rsticketspro&view=kbrules');
		JSubMenuHelper::addEntry(JText::_('RST_KB_TEMPLATE'), 'index.php?option=com_rsticketspro&view=kbtemplate');
		
		$task = JRequest::getVar('task','');
		
		if ($task == 'edit')
		{
			JToolBarHelper::title('RSTickets! Pro <small>['.JText::_('RST_EDIT_KB_CATEGORY').']</small>','rsticketspro');
			
			JToolBarHelper::apply();
			JToolBarHelper::save();
			JToolBarHelper::cancel();
			
			$row = $this->get('kbcategory');
			$this->assignRef('row', $row);
			
			$this->assignRef('editor', JFactory::getEditor());
			
			$lists['categories'] = RSTicketsProHelper::getKBCategoriesTree('parent_id', $row->parent_id, 0, JHTML::_('select.option', 0, JText::_('RST_KB_NO_PARENT')));
			
			$lists['private'] = JHTML::_('select.booleanlist','private','class="inputbox"',$row->private);
			$lists['published'] = JHTML::_('select.booleanlist','published','class="inputbox"',$row->published);
			$this->assignRef('lists', $lists);
		}
		else
		{
			JToolBarHelper::addNewX('edit');
			JToolBarHelper::editListX('edit');
			JToolBarHelper::spacer();
			
			JToolBarHelper::publishList();
			JToolBarHelper::unpublishList();
			JToolBarHelper::spacer();
			
			JToolBarHelper::deleteList('RST_CONFIRM_DELETE');
			
			$filter_state = $mainframe->getUserStateFromRequest('rsticketspro.filter_state', 'filter_state');
			$mainframe->setUserState('rsticketspro.filter_state', $filter_state);
			$lists['state']	= JHTML::_('grid.state', $filter_state);
			$this->assignRef('lists', $lists);
			
			$this->assignRef('sortColumn', JRequest::getVar('filter_order','ordering'));
			$this->assignRef('sortOrder', JRequest::getVar('filter_order_Dir','ASC'));
			
			$this->assignRef('kbcategories', $this->get('kbcategories'));
			$this->assignRef('pagination', $this->get('pagination'));
			
			$filter_word = JRequest::getCmd('search', '');
			$this->assignRef('filter_word', $filter_word);
		}
		
		parent::display($tpl);
	}
}