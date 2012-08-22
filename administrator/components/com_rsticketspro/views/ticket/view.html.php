<?php
/**
* @version 2.0.0
* @package RSTickets! Pro 2.0.0
* @copyright (C) 2010 www.rsjoomla.com
* @license GPL, http://www.gnu.org/licenses/gpl-2.0.html
*/

defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.view');

require_once(JPATH_SITE.DS.'components'.DS.'com_rsticketspro'.DS.'views'.DS.'ticket'.DS.'view.html.php');

$mainframe =& JFactory::getApplication();

JToolBarHelper::title('RSTickets! Pro','rsticketspro');
		
JSubMenuHelper::addEntry(JText::_('RST_MANAGE_TICKETS'), 'index.php?option=com_rsticketspro&view=tickets', true);
JSubMenuHelper::addEntry(JText::_('RST_DEPARTMENTS'), 'index.php?option=com_rsticketspro&view=departments');
JSubMenuHelper::addEntry(JText::_('RST_GROUPS'), 'index.php?option=com_rsticketspro&view=groups');
JSubMenuHelper::addEntry(JText::_('RST_STAFF_MEMBERS'), 'index.php?option=com_rsticketspro&view=staff');
JSubMenuHelper::addEntry(JText::_('RST_PRIORITIES'), 'index.php?option=com_rsticketspro&view=priorities');
JSubMenuHelper::addEntry(JText::_('RST_STATUSES'), 'index.php?option=com_rsticketspro&view=statuses');
JSubMenuHelper::addEntry(JText::_('RST_KNOWLEDGEBASE'), 'index.php?option=com_rsticketspro&view=knowledgebase');
JSubMenuHelper::addEntry(JText::_('RST_EMAIL_MESSAGES'), 'index.php?option=com_rsticketspro&view=emails');
JSubMenuHelper::addEntry(JText::_('RST_CONFIGURATION'), 'index.php?option=com_rsticketspro&view=configuration');
$mainframe->triggerEvent('onAfterTicketsMenu');
JSubMenuHelper::addEntry(JText::_('RST_UPDATES'), 'index.php?option=com_rsticketspro&view=updates');

JToolBarHelper::custom('kbconvert', 'upload', 'upload', JText::_('RST_CONVERT_TO_KB'), false);
JToolBarHelper::custom('automatickbconvert', 'upload', 'upload', JText::_('RST_CONVERT_TO_KB_AUTOMATIC'), false);
JToolBarHelper::back(JText::_('RST_BACK'), 'index.php?option=com_rsticketspro&view=tickets');
?>