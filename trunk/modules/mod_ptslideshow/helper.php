

<?php
	
	defined('_JEXEC') or die('Restricted access');
	
	class ModPtslideshowHelper
	{
		// to do
		static function getSlideshow($category)
		{
			$db = JFactory::getDBO();
			$query = 'SELECT s.* FROM #__ptslideshow as s WHERE s.category_ = ' . $category . ' and s.state = 1 ORDER BY s.id';
			$db->setQuery($query);
				
			if (!($slideshow = $db->loadObjectList())) {
				echo $db->stderr();
				return;
			}
				
			return $slideshow;
		}
		
		// to do
		static function getCategory($category)
		{
			$db = JFactory::getDBO();
			$query = 'SELECT cat.* FROM #__categories as cat WHERE cat.id = ' . $category . ' and cat.published = 1';
			$db->setQuery($query);
				
			if (!($catSlideshow = $db->loadObject())) {
				echo $db->stderr();
				return;
			}
				
			return $catSlideshow;
		}
	}
	
?>