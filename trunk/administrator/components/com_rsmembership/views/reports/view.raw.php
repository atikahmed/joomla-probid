<?php
/**
* @version 1.0.0
* @package RSMembership! 1.0.0
* @copyright (C) 2009-2010 www.rsjoomla.com
* @license GPL, http://www.gnu.org/licenses/gpl-2.0.html
*/

defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.view');

class RSMembershipViewReports extends JView
{
	function display($tpl = null)
	{
		$this->assign('report', $this->get('report'));
		$this->assign('data', $this->get('reportdata'));
		
		$this->assign('min', $this->get('min'));
		$this->assign('avg', $this->get('avg'));
		$this->assign('max', $this->get('max'));
		$this->assign('total', $this->get('total'));
		parent::display($tpl);
	}
}