<?php
/**
* @version 2.0.0
* @package RSTickets! Pro 2.0.0
* @copyright (C) 2010 www.rsjoomla.com
* @license GPL, http://www.gnu.org/licenses/gpl-2.0.html
*/

defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.view');

class RSTicketsProViewKBRules extends JView
{
	function display($tpl = null)
	{
		$mainframe =& JFactory::getApplication();
		
		JToolBarHelper::title('RSTickets! Pro','rsticketspro');
		
		JSubMenuHelper::addEntry(JText::_('RST_BACK_TO_RSTICKETSPRO'), 'index.php?option=com_rsticketspro');
		JSubMenuHelper::addEntry(JText::_('RST_KNOWLEDGEBASE'), 'index.php?option=com_rsticketspro&view=knowledgebase');
		JSubMenuHelper::addEntry(JText::_('RST_KB_CATEGORIES'), 'index.php?option=com_rsticketspro&view=kbcategories');
		JSubMenuHelper::addEntry(JText::_('RST_KB_ARTICLES'), 'index.php?option=com_rsticketspro&view=kbcontent');
		JSubMenuHelper::addEntry(JText::_('RST_KB_CONVERSION_RULES'), 'index.php?option=com_rsticketspro&view=kbrules', true);
		JSubMenuHelper::addEntry(JText::_('RST_KB_TEMPLATE'), 'index.php?option=com_rsticketspro&view=kbtemplate');
		
		$task = JRequest::getVar('task','');
		
		if ($task == 'edit')
		{
			JToolBarHelper::title('RSTickets! Pro <small>['.JText::_('RST_EDIT_KB_RULE').']</small>','rsticketspro');
			
			JToolBarHelper::apply();
			JToolBarHelper::save();
			JToolBarHelper::cancel();
			
			$row = $this->get('kbrule');
			$this->assignRef('row', $row);
			
			$types = array(
				JHTML::_('select.option', '', JText::_('RST_PLEASE_SELECT')),
				JHTML::_('select.option', 'department', JText::_('RST_DEPARTMENT')),
				JHTML::_('select.option', 'subject', JText::_('RST_TICKET_SUBJECT')),
				JHTML::_('select.option', 'message', JText::_('RST_TICKET_MESSAGE')),
				JHTML::_('select.option', 'priority', JText::_('RST_PRIORITY')),
				JHTML::_('select.option', 'status', JText::_('RST_TICKET_STATUS')),
				JHTML::_('select.option', 'custom_field', JText::_('RST_CUSTOM_FIELD'))
			);
			$conditions = array(
				JHTML::_('select.option', '', JText::_('RST_PLEASE_SELECT')),
				JHTML::_('select.option', 'eq', JText::_('RST_IS_EQUAL')),
				JHTML::_('select.option', 'neq', JText::_('RST_IS_NOT_EQUAL')),
				JHTML::_('select.option', 'like', JText::_('RST_IS_LIKE')),
				JHTML::_('select.option', 'notlike', JText::_('RST_IS_NOT_LIKE'))
			);
			$connectors = array(
				JHTML::_('select.option', 'AND', JText::_('RST_AND')),
				JHTML::_('select.option', 'OR', JText::_('RST_OR'))
			);
			$custom_fields = $this->get('customfields');
			$departments = $this->get('departments');
			$priorities = $this->get('priorities');
			$statuses = $this->get('statuses');
			$custom_field_value = array();
			foreach ($departments as $i => $department)
			{
				if ($i)
				{
					$tmp = new stdClass();
					$tmp->value = '</OPTGROUP>';
					$tmp->text = '';
					$custom_field_value[] = $tmp;
				}
				$custom_field_value[] = JHTML::_('select.optgroup', $department->name);
				foreach ($custom_fields as $custom_field)
				{
					if ($custom_field->department_id != $department->id) continue;
					$custom_field_value[] = JHTML::_('select.option', $custom_field->id, $custom_field->name);
				}
				if ($i == count($departments) - 1)
				{
					$tmp = new stdClass();
					$tmp->value = '</OPTGROUP>';
					$tmp->text = '';
					$custom_field_value[] = $tmp;
				}
			}
			
			if (!empty($row->conditions))
			foreach ($row->conditions as $i => $condition)
			{
				$lists['select_type'][$i] = JHTML::_('select.genericlist', $types, 'select_type[]', null, 'value', 'text', $condition->type, 'select_type'.$i);
				$lists['select_condition'][$i] = JHTML::_('select.genericlist', $conditions, 'select_condition[]', null, 'value', 'text', $condition->condition, 'select_condition'.$i);
				$lists['select_connector'][$i] = JHTML::_('select.genericlist', $connectors, 'select_connector[]', null, 'value', 'text', $condition->connector, 'select_connector'.$i);
				$lists['select_custom_field_value'][$i] = $condition->type == 'custom_field' ? JHTML::_('select.genericlist', $custom_field_value, 'select_custom_field_value[]', null, 'value', 'text', $condition->custom_field, 'select_custom_field_value'.$i) : '';
				$lists['select_value'][$i] = '';
				
				$select_attribs = '';
				$select_value = '';
				$input_attribs = '';
				$input_value = '';
				switch ($condition->type)
				{
					case 'department':
					case 'priority':
					case 'status':
						if ($condition->type == 'department')
							$array = $departments;
						elseif ($condition->type == 'priority')
							$array = $priorities;
						elseif ($condition->type == 'status')
							$array = $statuses;
						
						if ($condition->condition == 'like' || $condition->condition == 'notlike')
						{
							$select_attribs = 'disabled="disabled" style="display: none;"';
							$input_value = $this->escape($condition->value);
						}
						else
						{
							$input_attribs = 'disabled="disabled" style="display: none;"';
							$select_value = $condition->value;
						}
						
						$lists['select_value'][$i] .= JHTML::_('select.genericlist', $array, 'select_value[]', $select_attribs, 'id', 'name', $select_value, 'select_value'.$i);
						$lists['select_value'][$i] .= '<input type="text" name="select_value[]" value="'.$input_value.'" '.$input_attribs.' />';
					break;
					
					case 'subject':
						$input_value = $this->escape($condition->value);
						$lists['select_value'][$i] .= '<input type="text" name="select_value[]" value="'.$input_value.'" />';
					break;
					
					case 'message':
						$input_value = $this->escape($condition->value);
						$lists['select_value'][$i] .= '<textarea name="select_value[]">'.$input_value.'</textarea>';
					break;
					
					case 'custom_field':
						JRequest::setVar('cfid', $condition->custom_field);
						$values = $this->get('customfieldvalues');
						
						if (empty($values))
						{
							$input_value = $this->escape($condition->value);
							$lists['select_value'][$i] .= '<input type="text" name="select_value[]" value="'.$input_value.'" />';
						}
						else
						{
							if ($condition->condition == 'like' || $condition->condition == 'notlike')
							{
								$select_attribs = 'disabled="disabled" style="display: none;"';
								$input_value = $this->escape($condition->value);
							}
							else
							{
								$input_attribs = 'disabled="disabled" style="display: none;"';
								$select_value = $condition->value;
							}
							
							$lists['select_value'][$i] .= JHTML::_('select.genericlist', $values, 'select_value[]', $select_attribs, 'id', 'name', $select_value, 'select_value'.$i);
							$lists['select_value'][$i] .= '<input type="text" name="select_value[]" value="'.$input_value.'" '.$input_attribs.' />';
						}
					break;
				}
			}
			
			$lists['categories'] = RSTicketsProHelper::getKBCategoriesTree('category_id', $row->category_id, 0, '', 0);
			$lists['publish_article'] = JHTML::_('select.booleanlist','publish_article','class="inputbox"',$row->publish_article);
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
			
			$filter_state = $mainframe->getUserStateFromRequest('rsticketspro.rules.filter_state', 'filter_state');
			$mainframe->setUserState('rsticketspro.rules.filter_state', $filter_state);
			$lists['state']	= JHTML::_('grid.state', $filter_state);
			
			$this->assignRef('sortColumn', JRequest::getVar('filter_order','category, r.name'));
			$this->assignRef('sortOrder', JRequest::getVar('filter_order_Dir','ASC'));
			
			$this->assignRef('kbrules', $this->get('kbrules'));
			$this->assignRef('pagination', $this->get('pagination'));
			
			$filter_word = JRequest::getCmd('search', '');
			$this->assignRef('filter_word', $filter_word);
			
			$this->assignRef('lists', $lists);
		}
		
		parent::display($tpl);
	}
}