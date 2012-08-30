<?php
/**
* @version 1.0.0
* @package RSMembership! 1.0.0
* @copyright (C) 2009-2010 www.rsjoomla.com
* @license GPL, http://www.gnu.org/licenses/gpl-2.0.html
*/

defined('_JEXEC') or die('Restricted access');

jimport('joomla.form.formfield');

class JFormFieldMembership extends JFormField
{
   /**
	* Element name
	*
	* @access	protected
	* @var		string
	*/
	protected $type = 'Membership';
	
	protected function getInput()
	{
		$document  = &JFactory::getDocument();
		$fieldName = $this->name;
		$value	   = $this->value;

		JTable::addIncludePath(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_rsmembership'.DS.'tables');

		$row =& JTable::getInstance('RSMembership_Memberships', 'Table');
		if ($value)
			$row->load($value);
		else
			$row->name = JText::_('RSM_SELECT_MEMBERSHIP');

		$js = "
		function elSelectEvent(id, title) {
			document.getElementById('a_id').value = id;
			document.getElementById('a_name').value = title;
			window.SqueezeBox.close();
		}";

		$document->addScriptDeclaration($js);

		JHTML::_('behavior.modal', 'a.modal');

		$html  = "\n".'<div style="float: left;"><input style="background: #ffffff;" type="text" id="a_name" value="'.$row->name.'" disabled="disabled" /></div>';
		$html .= '<div class="button2-left"><div class="blank"><a class="modal" title="'.JText::_('RSM_SELECT').'"  href="index.php?option=com_rsmembership&controller=memberships&task=element&tmpl=component" rel="'."{handler: 'iframe', size: {x: 650, y: 375}}".'">'.JText::_('RSM_SELECT').'</a></div></div>'."\n";
		$html .= "\n".'<input type="hidden" id="a_id" name="'.$fieldName.'" value="'.$value.'" />';
		
		return $html;
	}
}
?>