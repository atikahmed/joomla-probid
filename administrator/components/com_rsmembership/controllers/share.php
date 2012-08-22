<?php
/**
* @version 1.0.0
* @package RSMembership! 1.0.0
* @copyright (C) 2009-2010 www.rsjoomla.com
* @license GPL, http://www.gnu.org/licenses/gpl-2.0.html
*/

defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.controller');

class RSMembershipControllerShare extends RSMembershipController
{
	function __construct()
	{
		parent::__construct();
	}
	
	function addMembershipSharedContent()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit('Invalid Token');
		
		// Get the selected items
		$cids = JRequest::getVar('cid', array(), 'post', 'array');
		
		// Get the model
		$model = $this->getModel('share');
		
		$model->addItems($cids, 'membership', JRequest::getVar('share_type'));
		jexit();
	}
	
	function addExtraValueSharedContent()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit('Invalid Token');
		
		// Get the selected items
		$cids = JRequest::getVar('cid', array(), 'post', 'array');
		
		// Get the model
		$model = $this->getModel('share');
		
		$model->addItems($cids, 'extra_value', JRequest::getVar('share_type'));
		jexit();
	}
	
	function addMembershipArticles()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit('Invalid Token');
		
		// Get the selected items
		$cids = JRequest::getVar('cid', array(), 'post', 'array');
		
		// Get the model
		$model = $this->getModel('share');
		
		$model->addItems($cids, 'membership', 'article');
		jexit();
	}
	
	function addExtraValueArticles()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit('Invalid Token');
		
		// Get the selected items
		$cids = JRequest::getVar('cid', array(), 'post', 'array');
		
		// Get the model
		$model = $this->getModel('share');
		
		$model->addItems($cids, 'extra_value', 'article');
		jexit();
	}
	
	function addMembershipSections()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit('Invalid Token');
		
		// Get the selected items
		$cids = JRequest::getVar('cid', array(), 'post', 'array');
		
		// Get the model
		$model = $this->getModel('share');
		
		$model->addItems($cids, 'membership', 'section');
		jexit();
	}
	
	function addExtraValueSections()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit('Invalid Token');
		
		// Get the selected items
		$cids = JRequest::getVar('cid', array(), 'post', 'array');
		
		// Get the model
		$model = $this->getModel('share');
		
		$model->addItems($cids, 'extra_value', 'section');
		jexit();
	}
	
	function addMembershipCategories()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit('Invalid Token');
		
		// Get the selected items
		$cids = JRequest::getVar('cid', array(), 'post', 'array');
		
		// Get the model
		$model = $this->getModel('share');
		
		$model->addItems($cids, 'membership', 'category');
		jexit();
	}
	
	function addExtraValueCategories()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit('Invalid Token');
		
		// Get the selected items
		$cids = JRequest::getVar('cid', array(), 'post', 'array');
		
		// Get the model
		$model = $this->getModel('share');
		
		$model->addItems($cids, 'extra_value', 'category');
		jexit();
	}
	
	function addMembershipURL()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit('Invalid Token');
		
		// Get the selected items
		$cid = JRequest::getInt('cid');
		
		// Get the model
		$model = $this->getModel('share');
		
		$model->addMembershipURL($cid);
		jexit();
	}
	
	function addExtraValueURL()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit('Invalid Token');
		
		// Get the selected items
		$cid = JRequest::getInt('cid');
		
		// Get the model
		$model = $this->getModel('share');
		
		$model->addExtraValueURL($cid);
		jexit();
	}
	
	function addMembershipModules()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit('Invalid Token');
		
		// Get the selected items
		$cids = JRequest::getVar('cid', array(), 'post', 'array');
		JArrayHelper::toInteger($cids);
		
		// Get the model
		$model = $this->getModel('share');
		
		$model->addItems($cids, 'membership', 'module');
		jexit();
	}
	
	function addExtraValueModules()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit('Invalid Token');
		
		// Get the selected items
		$cids = JRequest::getVar('cid', array(), 'post', 'array');
		JArrayHelper::toInteger($cids);
		
		// Get the model
		$model = $this->getModel('share');
		
		$model->addItems($cids, 'extra_value', 'module');
		jexit();
	}
	
	function addMembershipMenus()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit('Invalid Token');
		
		// Get the selected items
		$cids = JRequest::getVar('cid', array(), 'post', 'array');
		JArrayHelper::toInteger($cids);
		
		// Get the model
		$model = $this->getModel('share');
		
		$model->addItems($cids, 'membership', 'menu');
		jexit();
	}
	
	function addExtraValueMenus()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit('Invalid Token');
		
		// Get the selected items
		$cids = JRequest::getVar('cid', array(), 'post', 'array');
		JArrayHelper::toInteger($cids);
		
		// Get the model
		$model = $this->getModel('share');
		
		$model->addItems($cids, 'extra_value', 'menu');
		jexit();
	}
}
?>