<?php
/**
* @version 1.0.0
* @package RSMembership! 1.0.0
* @copyright (C) 2009-2010 www.rsjoomla.com
* @license GPL, http://www.gnu.org/licenses/gpl-2.0.html
*/

defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.controller');

class RSMembershipControllerConfiguration extends RSMembershipController
{
	function __construct()
	{
		parent::__construct();
		$this->registerTask('apply', 'save');
	}
	
	/**
	 * Logic to save configuration
	 */
	function save()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit( 'Invalid Token' );

		// Get the model
		$model = $this->getModel('configuration');
		
		// Save
		$model->save();
		
		$tabposition = JRequest::getInt('tabposition');
		
		$task = JRequest::getCmd('task');
		if ($task == 'apply')
			$link = 'index.php?option=com_rsmembership&view=configuration&tabposition='.$tabposition;
		else
			$link = 'index.php?option=com_rsmembership';
		
		// Redirect
		$this->setRedirect($link, JText::_('RSM_CONFIGURATION_OK'));
	}
	
	function idevCheckConnection()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit( 'Invalid Token' );

		// Get the model
		$model = $this->getModel('configuration');
		
		// Save
		$result = $model->idevCheckConnection();
		
		$tabposition = JRequest::getInt('tabposition');
		
		$link = 'index.php?option=com_rsmembership&view=configuration&tabposition='.$tabposition;
		
		$msg = '';
		if ($result)
			$msg = JText::_('RSM_IDEV_CONNECT_SUCCESS');
		
		// Redirect
		$this->setRedirect($link, $msg);
	}
	
	function patchmodule()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit( 'Invalid Token' );
		
		jimport('joomla.filesystem.file');
		
		$module = RSMembershipHelper::getPatchFile('module');
		
		$buffer = JFile::read($module);
		if (strpos($buffer, 'RSMembershipHelper') !== false)
			return $this->setRedirect('index.php?option=com_rsmembership&view=configuration&tabposition=1', JText::_('RSM_PATCH_APPLIED'));
		if (!is_writable($module))
		{
			JError::raiseWarning(500, JText::_('RSM_PATCH_NOT_WRITABLE'));
			return $this->setRedirect('index.php?option=com_rsmembership&view=configuration&tabposition=1');
		}
		
		if (RSMembershipHelper::isJ16())
		{
			$replace = "\$query->where('m.published = 1');";
			$with = $replace."\n"."\t\t"."if (file_exists(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_rsmembership'.DS.'helpers'.DS.'rsmembership.php')) {".
							 "\n"."\t\t\t"."include_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_rsmembership'.DS.'helpers'.DS.'rsmembership.php');".
							 "\n"."\t\t\t"."\$rsm_where = RSMembershipHelper::getModulesWhere();".
							 "\n"."\t\t\t"."if (\$rsm_where) \$query->where(\$rsm_where);".
							 "\n"."\t\t"."}".
							 "\n";
		}
		else
		{
			$replace = "\$wheremenu = isset( \$Itemid ) ? ' AND ( mm.menuid = '. (int) \$Itemid .' OR mm.menuid = 0 )' : '';";
			$with = $replace."\n"."\t\t"."if (file_exists(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_rsmembership'.DS.'helpers'.DS.'rsmembership.php')) {".
							 "\n"."\t\t\t"."include_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_rsmembership'.DS.'helpers'.DS.'rsmembership.php');".
							 "\n"."\t\t\t"."\$wheremenu .= RSMembershipHelper::getModulesWhere();".
							 "\n"."\t\t"."}".
							 "\n";
		}
		
		$buffer = str_replace($replace, $with, $buffer);
		
		if (JFile::write($module, $buffer))
			return $this->setRedirect('index.php?option=com_rsmembership&view=configuration&tabposition=1', JText::_('RSM_PATCH_SUCCESS'));
		
		JError::raiseWarning(500, JText::_('RSM_PATCH_NOT_WRITABLE'));
		$this->setRedirect('index.php?option=com_rsmembership&view=configuration&tabposition=1');
	}
	
	function unpatchmodule()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit( 'Invalid Token' );
		
		jimport('joomla.filesystem.file');
		
		$module = RSMembershipHelper::getPatchFile('module');
		
		$buffer = JFile::read($module);
		if (strpos($buffer, 'RSMembershipHelper') === false)
			return $this->setRedirect('index.php?option=com_rsmembership&view=configuration&tabposition=1', JText::_('RSM_PATCH_NOT_APPLIED'));
		if (!is_writable($module))
		{
			JError::raiseWarning(500, JText::_('RSM_PATCH_NOT_WRITABLE'));
			return $this->setRedirect('index.php?option=com_rsmembership&view=configuration&tabposition=1');
		}
		
		if (RSMembershipHelper::isJ16())
		{
			$with = "\$query->where('m.published = 1');";
			$replace = $with."\n"."\t\t"."if (file_exists(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_rsmembership'.DS.'helpers'.DS.'rsmembership.php')) {".
							 "\n"."\t\t\t"."include_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_rsmembership'.DS.'helpers'.DS.'rsmembership.php');".
							 "\n"."\t\t\t"."\$rsm_where = RSMembershipHelper::getModulesWhere();".
							 "\n"."\t\t\t"."if (\$rsm_where) \$query->where(\$rsm_where);".
							 "\n"."\t\t"."}".
							 "\n";
		}
		else
		{
			$with = "\$wheremenu = isset( \$Itemid ) ? ' AND ( mm.menuid = '. (int) \$Itemid .' OR mm.menuid = 0 )' : '';";
			$replace = $with."\n"."\t\t"."if (file_exists(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_rsmembership'.DS.'helpers'.DS.'rsmembership.php')) {".
							 "\n"."\t\t\t"."include_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_rsmembership'.DS.'helpers'.DS.'rsmembership.php');".
							 "\n"."\t\t\t"."\$wheremenu .= RSMembershipHelper::getModulesWhere();".
							 "\n"."\t\t"."}".
							 "\n";
		}
		
		$buffer = str_replace($replace, $with, $buffer);
		
		if (JFile::write($module, $buffer))
			return $this->setRedirect('index.php?option=com_rsmembership&view=configuration&tabposition=1', JText::_('RSM_PATCH_REMOVED_SUCCESS'));
		
		JError::raiseWarning(500, JText::_('RSM_PATCH_NOT_WRITABLE'));
		$this->setRedirect('index.php?option=com_rsmembership&view=configuration&tabposition=1');
	}
	
	function patchmenu()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit( 'Invalid Token' );
		
		jimport('joomla.filesystem.file');
		
		$menu = RSMembershipHelper::getPatchFile('menu');
		
		$buffer = JFile::read($menu);
		if (strpos($buffer, 'RSMembershipHelper') !== false)
			return $this->setRedirect('index.php?option=com_rsmembership&view=configuration&tabposition=1', JText::_('RSM_PATCH_APPLIED'));
		if (!is_writable($menu))
		{
			JError::raiseWarning(500, JText::_('RSM_PATCH_NOT_WRITABLE'));
			return $this->setRedirect('index.php?option=com_rsmembership&view=configuration&tabposition=1');
		}
		
		if (RSMembershipHelper::isJ16())
		{
			if (RSMembershipHelper::isJ25())
				$replace = "\$items 		= \$menu->getItems('menutype', \$params->get('menutype'));";
			else
				$replace = "\$items 		= \$menu->getItems('menutype',\$params->get('menutype'));";
				
			$with = $replace."\n"."\t\t"."if (file_exists(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_rsmembership'.DS.'helpers'.DS.'rsmembership.php')) {".
							 "\n"."\t\t\t"."include_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_rsmembership'.DS.'helpers'.DS.'rsmembership.php');".
							 "\n"."\t\t\t"."RSMembershipHelper::checkMenuShared(\$items);".
							 "\n"."\t\t"."}".
							 "\n";
		}
		else
		{
			$replace = "\$rows = \$items->getItems('menutype', \$params->get('menutype'));";
			$with = $replace."\n"."\t\t"."if (file_exists(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_rsmembership'.DS.'helpers'.DS.'rsmembership.php')) {".
							 "\n"."\t\t\t"."include_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_rsmembership'.DS.'helpers'.DS.'rsmembership.php');".
							 "\n"."\t\t\t"."RSMembershipHelper::checkMenuShared(\$rows);".
							 "\n"."\t\t"."}".
							 "\n";
		}
		
		$buffer = str_replace($replace, $with, $buffer);
		
		if (JFile::write($menu, $buffer))
			return $this->setRedirect('index.php?option=com_rsmembership&view=configuration&tabposition=1', JText::_('RSM_PATCH_SUCCESS'));
		
		JError::raiseWarning(500, JText::_('RSM_PATCH_NOT_WRITABLE'));
		$this->setRedirect('index.php?option=com_rsmembership&view=configuration&tabposition=1');
	}
	
	function unpatchmenu()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit( 'Invalid Token' );
		
		jimport('joomla.filesystem.file');
		
		$menu = RSMembershipHelper::getPatchFile('menu');
		
		$buffer = JFile::read($menu);
		if (strpos($buffer, 'RSMembershipHelper') === false)
			return $this->setRedirect('index.php?option=com_rsmembership&view=configuration&tabposition=1', JText::_('RSM_PATCH_NOT_APPLIED'));
		if (!is_writable($menu))
		{
			JError::raiseWarning(500, JText::_('RSM_PATCH_NOT_WRITABLE'));
			return $this->setRedirect('index.php?option=com_rsmembership&view=configuration&tabposition=1');
		}
		
		if (RSMembershipHelper::isJ16())
		{
			if (RSMembershipHelper::isJ25())
				$with = "\$items 		= \$menu->getItems('menutype', \$params->get('menutype'));";
			else
				$with = "\$items 		= \$menu->getItems('menutype',\$params->get('menutype'));";
			$replace = $with."\n"."\t\t"."if (file_exists(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_rsmembership'.DS.'helpers'.DS.'rsmembership.php')) {".
							 "\n"."\t\t\t"."include_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_rsmembership'.DS.'helpers'.DS.'rsmembership.php');".
							 "\n"."\t\t\t"."RSMembershipHelper::checkMenuShared(\$items);".
							 "\n"."\t\t"."}".
							 "\n";
			
			$buffer = str_replace($replace, $with, $buffer);
		}
		else
		{
			$possibles = array(
				"\n"."\t\t\t"."RSMembershipHelper::checkMenuShared(&\$rows);",
				"\n"."\t\t\t"."RSMembershipHelper::checkMenuShared(\$rows);"
			);
			
			foreach ($possibles as $possible)
			{
				$with = "\$rows = \$items->getItems('menutype', \$params->get('menutype'));";
				$replace = $with."\n"."\t\t"."if (file_exists(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_rsmembership'.DS.'helpers'.DS.'rsmembership.php')) {".
								 "\n"."\t\t\t"."include_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_rsmembership'.DS.'helpers'.DS.'rsmembership.php');".
								 $possible.
								 "\n"."\t\t"."}".
								 "\n";
								 
				$buffer = str_replace($replace, $with, $buffer);
			}
		}
		
		if (JFile::write($menu, $buffer))
			return $this->setRedirect('index.php?option=com_rsmembership&view=configuration', JText::_('RSM_PATCH_REMOVED_SUCCESS'));
		
		JError::raiseWarning(500, JText::_('RSM_PATCH_NOT_WRITABLE'));
		$this->setRedirect('index.php?option=com_rsmembership&view=configuration&tabposition=1');
	}
}
?>