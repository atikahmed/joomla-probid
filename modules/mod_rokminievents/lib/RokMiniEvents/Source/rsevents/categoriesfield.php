<?php
/**
* @version 1.2.0
* @package RSEvents! 1.2.0
* @copyright (C) 2009 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

defined('JPATH_BASE') or die;

jimport('joomla.form.formfield');

/**
 * Supports a modal article picker.
 *
 * @package		RSEvents!
 * @subpackage	com_rsevents
 * @since		1.6
 */
class JFormFieldCategoriesField extends JFormField
{
	/**
	 * The form field type.
	 *
	 * @var		string
	 * @since	1.6
	 */
	protected $type = 'CategoriesField';

	/**
	 * Method to get the field input markup.
	 *
	 * @return	string	The field input markup.
	 * @since	1.6
	 */
	protected function getInput()
	{
		// Load the modal behavior script.
		JHtml::_('behavior.modal', 'a.modal');

		//get the document
		$doc =& JFactory::getDocument();
		
		// Build the script.
		$js = "
		function elSelectCategory(id, title) 
		{			
			var ids = document.getElementById('rse_add_categories_ids').value;
			var names = document.getElementById('rse_add_categories_names').innerHTML;
			
			if (ids == '' || ids == 0)
				document.getElementById('rse_add_categories_ids').value = id;
			else 
			{
				var id_array = ids.split(',');
				if (!id_array.contains(id))
				{
					id_array.push(id);
					var newids = id_array.join(',');
					document.getElementById('rse_add_categories_ids').value = newids;
				}
			}
			
			if (names == '')
				document.getElementById('rse_add_categories_names').innerHTML = '<a href=\"javascript:rse_remove_cat('+id+');\">(X)</a> ' + title;
			else 
			{
				var name_array = names.split('<br>');
				if (!name_array.contains('<a href=\"javascript:rse_remove_cat('+id+');\">(X)</a> ' + title))
				{
					name_array.push('<a href=\"javascript:rse_remove_cat('+id+');\">(X)</a> ' + title);
					var newnames = name_array.join('<br>');
					document.getElementById('rse_add_categories_names').innerHTML = newnames;
				}
			}
			SqueezeBox.close();
		}
		
		function rse_remove_categs()
		{
			document.getElementById('rse_add_categories_ids').value = '';
			document.getElementById('rse_add_categories_names').innerHTML = '';
		}
		
		function rse_remove_cat(id)
		{
			var categories_ids = document.getElementById('rse_add_categories_ids').value;
			var categories_names = document.getElementById('rse_add_categories_names').innerHTML;
			var array_id = categories_ids.split(',');
			var array_name = categories_names.split('<br>');
			
			//prepare array as an array of integers
			for (i=0;i<array_id.length;i++)
				array_id[i] = parseInt(array_id[i]);
			
			if (array_id.contains(id))
			{
				var postion = '';
				for (i=0;i<array_id.length;i++)
					if (array_id[i] == id)
						position = i;
				
				array_id.splice(position,1);
				array_name.splice(position,1);
				var newids = array_id.join(',');
				var newnames = array_name.join('<br>');
				
				document.getElementById('rse_add_categories_ids').value = newids;
				document.getElementById('rse_add_categories_names').innerHTML = newnames;
			}
		}";

		// Add the script to the document head.
		$doc->addScriptDeclaration($js);

		$link	= 'index.php?option=com_rsevents&amp;task=listcategories&amp;tmpl=component';		
		
		$fieldName	= $this->name;
		JTable::addIncludePath(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_rsevents'.DS.'tables');
		$table =& JTable::getInstance('Rsevents_Categories', 'Table');
		$categories = '';
		
		if (empty($this->value))
			$categories .=  '';
		else
		{
			$values = explode(',',$this->value);
			if (!empty($values))
			{
				$values = array_unique($values);
				foreach ($values as $id)
				{
					$table->load($id);
					$categories[] = '<a href="javascript:rse_remove_cat('.$table->IdCategory.');">(X)</a> '.$table->CategoryName;
				}
				$categories = implode('<br>',$categories);
			}
		}
		
		
		$html = '';
		$html .= '<div class="rsevents-selector"><p><span id="rse_add_categories_names">'.$categories.'</span></p><br/>';
		$html .= '<div class="button2-left"><div class="blank"><a class="modal" title="'.JText::_('RSE_SELECT').'" href="'.$link.'" rel="{handler: \'iframe\', size: {x: 650, y: 375}}" >'.JText::_('RSE_SELECT').'</a></div></div>';
		$html .= '<div class="button2-left"><div class="blank"><a href="javascript:void(0)" onclick="rse_remove_categs();" >'.JText::_('RSE_CLEAR').'</a></div></div>';
		$html .= '<input type="hidden" id="rse_add_categories_ids" name="'.$fieldName.'" value="'.$this->value.'" /></div>';

		return $html;
		
	}
}