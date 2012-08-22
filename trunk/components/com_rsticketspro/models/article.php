<?php
/**
* @version 2.0.0
* @package RSTickets! Pro 2.0.0
* @copyright (C) 2010 www.rsjoomla.com
* @license GPL, http://www.gnu.org/licenses/gpl-2.0.html
*/

defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.model');

class RSTicketsProModelArticle extends JModel
{
	var $_data = null;
	var $_total = 0;
	var $_pagination = null;
	var $_db = null;
	
	var $is_staff;
	
	function __construct()
	{
		parent::__construct();
		$this->_db = JFactory::getDBO();
		
		$mainframe =& JFactory::getApplication();
		$option    = 'com_rsticketspro';
		
		$this->is_staff = RSTicketsProHelper::isStaff();

		$this->_getArticle();
		
		$pathway =& $mainframe->getPathway();
		$path = $this->getPath();
		foreach ($path as $item)
			$pathway->addItem($item->name, $item->link);
	}
	
	function _getArticle()
	{
		$cid = JRequest::getInt('cid', 0);
		
		$this->article =& JTable::getInstance('RSTicketsPro_KB_Content','Table');
		$this->article->load($cid);
		
		$this->category_id = $this->article->category_id;
		
		$cat =& JTable::getInstance('RSTicketsPro_KB_Categories','Table');
		$parent_id = $this->category_id;
		$cat->load($parent_id);
		while ($parent_id > 0)
		{
			$parent_id = $cat->parent_id;
			$cat->load($parent_id);
			
			if ($cat->private)
				$this->article->private = 1;
			if (!$cat->published)
				$this->article->published = 0;
		}
		
		if ((!$this->is_staff && $this->article->private) || !$this->article->published)
		{
			$mainframe =& JFactory::getApplication();
			JError::raiseWarning(500, JText::_('RST_CANNOT_VIEW_ARTICLE'));
			$mainframe->redirect('index.php?option=com_rsticketspro&view=knowledgebase');
		}
		
		$document =& JFactory::getDocument();
		if (!empty($this->article->meta_description))
			$document->setMetaData('description', $this->article->meta_description);
		if (!empty($this->article->meta_keywords))
			$document->setMetaData('keywords', $this->article->meta_keywords);
		$document->setTitle($this->article->name);
		
		$this->article->hit();
		$this->article->text .= $this->getCommentsBlock();
	}
	
	function getArticle()
	{
		return $this->article;
	}
	
	function getCommentsBlock()
	{
		$article =& $this->article;
		switch (RSTicketsProHelper::getConfig('kb_comments')) 
		{
			//RSComments
			case 'com_rscomments':
				if (file_exists(JPATH_SITE.DS.'components'.DS.'com_rscomments'.DS.'helpers'.DS.'rscomments.php')) 
				{
					require_once(JPATH_SITE.DS.'components'.DS.'com_rscomments'.DS.'helpers'.DS.'rscomments.php');
					return '{rscomments option="com_rsticketspro" id="'.$article->id.'"}';
				}
			break;

			//JComments
			case 'com_jcomments':
				if (file_exists(JPATH_SITE.DS.'components'.DS.'com_jcomments'.DS.'jcomments.php')) 
				{
					require_once(JPATH_SITE.DS.'components'.DS.'com_jcomments'.DS.'jcomments.php');
					return JComments::showComments($article->id, 'com_rsticketspro', $article->name);
				}
			break;

			//JomComment
			case 'com_jomcomment':
				if (file_exists(JPATH_SITE.DS.'plugins'.DS.'content'.DS.'jom_comment_bot.php')) 
				{
					require_once(JPATH_SITE.DS.'plugins'.DS.'content'.DS.'jom_comment_bot.php');
					return jomcomment($article->id, 'com_rsticketspro');
				}
			break;
			
			// Facebook
			case 'facebook':
				return '<script src="http://connect.facebook.net/en_US/all.js#xfbml=1"></script><div id="fb-root"></div><fb:comments href="'.RSTicketsProHelper::route('index.php?option=com_rsticketspro&view=article&cid='.$article->id.':'.JFilterOutput::stringURLSafe($article->name)).'" num_posts="5" width="700"></fb:comments>';
			break;
		}
		
		return '';
	}
	
	function getPath()
	{
		$return = array();
		
		$parent_id = $this->category_id;
		$row =& JTable::getInstance('RSTicketsPro_KB_Categories','Table');
		
		$obj = new stdClass();
		$obj->name = $this->article->name;
		$obj->link = '';
			
		$return[] = $obj;
		
		while ($parent_id > 0)
		{
			$row->load($parent_id);
			$parent_id = $row->parent_id;
			
			$obj = new stdClass();
			$obj->name = $row->name;
			$obj->link = RSTicketsProHelper::route('index.php?option=com_rsticketspro&view=knowledgebase&cid='.$row->id.':'.JFilterOutput::stringURLSafe($row->name));
			
			$return[] = $obj;
		}
		
		krsort($return);
		
		return $return;
	}
}
?>