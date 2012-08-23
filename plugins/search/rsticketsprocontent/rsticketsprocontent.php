<?php
/**
* @version 2.0.0
* @package RSTickets! Pro 2.0.0
* @copyright (C) 2010 www.rsjoomla.com
* @license GPL, http://www.gnu.org/licenses/gpl-2.0.html
*/

// no direct access
defined('_JEXEC') or die( 'Restricted access' );

$mainframe =& JFactory::getApplication();

$mainframe->registerEvent('onSearch', 'plgSearchRSTicketsProContent');
$mainframe->registerEvent('onSearchAreas', 'plgSearchRSTicketsProContentAreas');

$mainframe->registerEvent('onContentSearchAreas', 'plgSearchRSTicketsProContentAreas');
$mainframe->registerEvent('onContentSearch', 'plgSearchRSTicketsProContent');

/**
 * @return array An array of search areas
 */
function &plgSearchRSTicketsProContentAreas()
{
	static $areas = array(
		'rsticketsprocontent' => 'Knowledgebase'
	);
	return $areas;
}

/**
 * Content Search method
 * The sql must return the following fields that are used in a common display
 * routine: href, title, section, created, text, browsernav
 * @param string Target search string
 * @param string mathcing option, exact|any|all
 * @param string ordering option, newest|oldest|popular|alpha|category
 * @param mixed An array if the search it to be restricted to areas, null if search all
 */
function plgSearchRSTicketsProContent($text, $phrase='', $ordering='', $areas=null)
{
	jimport('joomla.filesystem.file');
	if (!JFile::exists(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_rsticketspro'.DS.'helpers'.DS.'rsticketspro.php'))
		return false;
		
	require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_rsticketspro'.DS.'helpers'.DS.'rsticketspro.php');

	global $mainframe;
	
	$db	=& JFactory::getDBO();
	$user =& JFactory::getUser();
	
	$searchText = $text;
	if (is_array($areas) && !array_intersect($areas, array_keys(plgSearchRSTicketsProContentAreas())))
		return array();

	// load plugin params info
 	$plugin	=& JPluginHelper::getPlugin('search', 'rsticketsprocontent');

	jimport('joomla.html.parameter');
	
 	$params	= new JParameter( $plugin->params );

	$text = trim($text);
	if ($text == '')
		return array();

	$select = "SELECT a.id, a.category_id, a.name AS title, a.text, cat.name AS section FROM #__rsticketspro_kb_content a LEFT JOIN #__rsticketspro_kb_categories cat ON (a.category_id=cat.id) WHERE a.published=1";
	if (!RSTicketsProHelper::isStaff())
		$select .= " AND a.private = 0";
	$uncategorised = $params->get('search_uncategorised', 1);
	if (!$uncategorised)
		$select .= " AND a.category_id > 0";
	
	
	switch ($phrase)
	{
		case 'exact':
			$text = $db->getEscaped($text, true);
			$where = " AND (a.name LIKE '%".$text."%' OR a.text LIKE '%".$text."%')";
			break;

		case 'all':
		case 'any':
		default:
			$text = $db->getEscaped($text);
			$words = explode(' ', $text);
			
			$wheres = array();
			foreach ($words as $word)
			{
				$word = $db->getEscaped($word, true);
				$wheres[] = "(a.name LIKE '%".$word."%' OR a.text LIKE '%".$word."%')";
			}
			$where = " AND (".implode(($phrase == 'all' ? ' AND ' : ' OR '), $wheres).")";
			break;
	}

	switch ($ordering)
	{
		case 'oldest':
			$order = " ORDER BY a.id ASC";
		break;

		case 'alpha':
			$order = " ORDER BY a.name ASC";
		break;

		case 'category':
			$order = " ORDER BY section ASC";
		break;

		case 'newest':
		default:
			$order = " ORDER BY a.id DESC";
		break;
	}

	$query = $select.$where.$order;
	
	$db->setQuery($query, 0, $params->get('search_limit', 50));
	$results = $db->loadObjectList();
	foreach ($results as $i => $row)
	{
		$results[$i]->href = JRoute::_('index.php?option=com_rsticketspro&view=article&cid='.$row->id.':'.JFilterOutput::stringURLSafe($row->title));
		$results[$i]->browsernav = 2;
		$results[$i]->created = 0;
		if (!$results[$i]->category_id && $uncategorised)
			$results[$i]->section = JText::_('Uncategorised Content');
	}
	
	return $results;
}
