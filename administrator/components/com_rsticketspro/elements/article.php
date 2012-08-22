<?php
/**
* @version 2.0.0
* @package RSTickets! Pro 2.0.0
* @copyright (C) 2010 www.rsjoomla.com
* @license GPL, http://www.gnu.org/licenses/gpl-2.0.html
*/

defined('_JEXEC') or die('Restricted access');

class JElementArticle extends JElement
{
   /**
	* Element name
	*
	* @access	protected
	* @var		string
	*/
	var	$_name = 'Article';
	
	function fetchElement($name, $value, &$node, $control_name)
	{
		$document =& JFactory::getDocument();
		$fieldName	= $control_name.'['.$name.']';

		JTable::addIncludePath(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_rsticketspro'.DS.'tables');

		$row =& JTable::getInstance('RSTicketsPro_KB_Content', 'Table');
		if ($value)
			$row->load($value);
		else
			$row->name = JText::_('RST_KB_SELECT_ARTICLE');
			
		$js = "
		function elSelectEvent(id, title) {
			document.getElementById('a_id').value = id;
			document.getElementById('a_name').value = title;
			document.getElementById('sbox-window').close();
		}";

		$document->addScriptDeclaration($js);

		JHTML::_('behavior.modal', 'a.modal');

		$html  = "\n".'<div style="float: left;"><input style="background: #ffffff;" type="text" id="a_name" value="'.$row->name.'" disabled="disabled" /></div>';
		$html .= '<div class="button2-left"><div class="blank"><a class="modal" title="'.JText::_('RST_KB_SELECT_ARTICLE').'"  href="index.php?option=com_rsticketspro&view=kbcontent&layout=element&tmpl=component" rel="'."{handler: 'iframe', size: {x: 650, y: 375}}".'">'.JText::_('RST_KB_SELECT').'</a></div></div>'."\n";
		$html .= "\n".'<input type="hidden" id="a_id" name="'.$fieldName.'" value="'.$value.'" />';
		
		return $html;
	}
}
?>