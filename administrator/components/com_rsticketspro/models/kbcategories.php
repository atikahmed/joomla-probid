<?php
/**
* @version 2.0.0
* @package RSTickets! Pro 2.0.0
* @copyright (C) 2010 www.rsjoomla.com
* @license GPL, http://www.gnu.org/licenses/gpl-2.0.html
*/

defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.model');

class RSTicketsProModelKBCategories extends JModel
{
	var $_data = null;
	var $_total = 0;
	var $_pagination = null;
	var $_db = null;
	
	var $_id = 0;
	
	function __construct()
	{
		parent::__construct();
		$this->_db = JFactory::getDBO();
		
		$mainframe =& JFactory::getApplication();
		$option    = 'com_rsticketspro';
		
		// Get pagination request variables
		$limit = $mainframe->getUserStateFromRequest($option.'.kbcategories.limit', 'limit', $mainframe->getCfg('list_limit'), 'int');
		$limitstart = $mainframe->getUserStateFromRequest($option.'.kbcategories.limitstart', 'limitstart', 0, 'int');

		// In case limit has been changed, adjust it
		$limitstart = ($limit != 0 ? (floor($limitstart / $limit) * $limit) : 0);

		$this->setState($option.'.kbcategories.limit', $limit);
		$this->setState($option.'.kbcategories.limitstart', $limitstart);
	}
	
	function getKBCategories()
	{
		$mainframe =& JFactory::getApplication();
		$option    = 'com_rsticketspro';
		
		$select = "SELECT * FROM #__rsticketspro_kb_categories";
		$where = '';
		
		$filter_state = $mainframe->getUserStateFromRequest('rsticketspro_filter_state', 'filter_state');
		if ($filter_state != '')
			$where = " WHERE `published`='".($filter_state == 'U' ? '0' : '1')."'";
		
		$sortColumn = JRequest::getVar('filter_order', 'ordering');
		$sortColumn = $this->_db->getEscaped($sortColumn);
		
		$sortOrder = JRequest::getVar('filter_order_Dir', 'ASC');
		$sortOrder = $this->_db->getEscaped($sortOrder);
		
		$order = " ORDER BY `".$sortColumn."` ".$sortOrder;

		$this->_db->setQuery($select.$where.$order);
		$items = $this->_db->loadObjectList();
		
		// establish the hierarchy of the menu
		$children = array();
		// first pass - collect children
		if ($items)
			foreach ($items as $item)
			{
				$parent	= $item->parent_id;
				$item->parent = $parent;
				$item->title = '';
				$list = @$children[$parent] ? $children[$parent] : array();
				array_push($list, $item);
				$children[$parent] = $list;
			}
		// second pass - get an indent list of the items
		$list = JHTML::_('menu.treerecurse', 0, '', array(), $children, 9999, 0, 0);
		
		//var_dump($list);die();
		
		// eventually only pick out the searched items.
		$filter_word = JRequest::getCmd('search', '');
		if ($filter_word)
		{
			$where = " WHERE `name` LIKE '%".$filter_word."%'";
			$this->_db->setQuery($select.$where.$order);
			$search_items = $this->_db->loadObjectList();
			
			$list1 = array();
			foreach ($search_items as $search_item)
			{
				foreach ($list as $item)
				{
					if ($item->id == $search_item->id)
						$list1[] = $item;
				}
			}
			// replace full list with found items
			$list = $list1;
		}

		$this->_total = count($list);

		// slice out elements based on limits
		if ($this->getState($option.'.kbcategories.limit') > 0)
			$list = array_slice($list, $this->getState($option.'.kbcategories.limitstart'), $this->getState($option.'.kbcategories.limit'));
		
		return $list;
	}
	
	function getTotal()
	{
		return $this->_total;
	}
	
	function getPagination()
	{
		if (empty($this->_pagination))
		{
			$option = 'com_rsticketspro';
			jimport('joomla.html.pagination');
			$this->_pagination = new JPagination($this->getTotal(), $this->getState($option.'.kbcategories.limitstart'), $this->getState($option.'.kbcategories.limit'));
		}
		
		return $this->_pagination;
	}
	
	function getKBCategory()
	{
		$cid = JRequest::getVar('cid');
		if (is_array($cid))
			$cid = (int) $cid[0];
		else
			$cid = (int) $cid;
		
		$row =& JTable::getInstance('RSTicketsPro_KB_Categories','Table');
		$row->load($cid);
		
		return $row;
	}
	
	function publish($cid=array(), $publish=1)
	{
		if (!is_array($cid) || count($cid) > 0)
		{
			$publish = (int) $publish;
			$cids = implode(',', $cid);

			$query = "UPDATE #__rsticketspro_kb_categories SET `published`='".$publish."' WHERE `id` IN (".$cids.")";
			$this->_db->setQuery($query);
			if (!$this->_db->query())
			{
				$this->setError($this->_db->getErrorMsg());
				return false;
			}
		}
		return $cid;
	}
	
	function remove($cids)
	{
		$cids = implode(',', $cids);

		$query = "DELETE FROM #__rsticketspro_kb_categories WHERE `id` IN (".$cids.")";
		$this->_db->setQuery($query);
		$this->_db->query();
		
		$this->_db->setQuery("UPDATE #__rsticketspro_kb_content SET `category_id`='0' WHERE `category_id` IN (".$cids.")");
		$this->_db->query();
		$this->_db->setQuery("UPDATE #__rsticketspro_kb_categories SET `parent_id`='0' WHERE `parent_id` IN (".$cids.")");
		$this->_db->query();
		
		return true;
	}
	
	function save()
	{
		jimport('joomla.filesystem.file');
		
		$folder 			 = JPATH_SITE.DS.'components'.DS.'com_rsticketspro'.DS.'assets'.DS.'thumbs';
		$row    			 =& JTable::getInstance('RSTicketsPro_KB_Categories','Table');
		$post   			 = JRequest::get('post');		
		$post['description'] = JRequest::getVar('description', '', 'post', 'none', JREQUEST_ALLOWRAW);
		
		if (!$row->bind($post))
			return JError::raiseWarning(500, $row->getError());
		
		if (JRequest::getInt('delete_thumb') && $row->thumb)
		{
			if (JFile::exists($folder.DS.$row->thumb))
				JFile::delete($folder.DS.$row->thumb);
			if (JFile::exists($folder.DS.'small'.DS.$row->thumb))
				JFile::delete($folder.DS.'small'.DS.$row->thumb);
			$row->thumb = '';
		}
		else
			unset($row->thumb);
		
		if (empty($row->id))
			$row->ordering = $row->getNextOrder("`parent_id`='".$row->parent_id."'");
		
		if ($row->store())
		{
			$files = JRequest::get('files');
			if (isset($files['thumb']))
			{
				if ($files['thumb']['error'] > 0 && $files['thumb']['error'] != 4)
					JError::raiseWarning(500, JText::_('RST_KB_CATEGORY_ICON_UPLOAD_ERROR'));
				elseif ($files['thumb']['error'] == 0)
				{
					$ext	= JFile::getExt($files['thumb']['name']);
					
					if (!JFile::upload($files['thumb']['tmp_name'], $folder.DS.$row->id.'.'.$ext))
						JError::raiseWarning(500, JText::sprintf('RST_KB_CATEGORY_ICON_UPLOAD_ERROR_FOLDER', $folder));
					else
					{
						require_once(JPATH_COMPONENT.DS.'helpers'.DS.'thumbs'.DS.'phpthumb.class.php');
							
						$phpThumb = new phpThumb();
						$phpThumb->src = $folder.DS.$row->id.'.'.$ext;
						$phpThumb->w = 48;
						$phpThumb->q = 75;
						$phpThumb->zc = 1;
						$phpThumb->config_output_format = $ext;
						$phpThumb->config_error_die_on_error = true;
						$phpThumb->config_cache_disable_warning = true;
						$phpThumb->config_allow_src_above_docroot = true;
						$phpThumb->cache_filename = $folder.DS.'small'.DS.$row->id.'.'.$ext;
						
						if ($phpThumb->GenerateThumbnail())
						{
							$phpThumb->RenderToFile($phpThumb->cache_filename);
							$this->_db->setQuery("UPDATE #__rsticketspro_kb_categories SET `thumb`='".$this->_db->getEscaped($row->id.'.'.$ext)."' WHERE `id`='".$row->id."'");
							$this->_db->query();
						}
						else
							JError::raiseWarning(500, $phpThumb->error);
					}
				}
			}
			
			$this->_id = $row->id;
			return true;
		}
		else
		{
			JError::raiseWarning(500, $row->getError());
			return false;
		}
	}
	
	function getId()
	{
		return $this->_id;
	}
}
?>