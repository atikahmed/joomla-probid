<?php
/**
 * @version   $Id$
 * @author    RocketTheme http://www.rockettheme.com
 * @copyright Copyright (C) 2007 - 2012 RocketTheme, LLC
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 */

class RokSprocket_Provider_Seblod extends RokSprocket_Provider_AbstarctJoomlaBasedProvider
{
	/**
	 * @static
	 * @return bool
	 */
	public static function isAvailable()
	{
		if (!class_exists('JFactory')) {
			return false;
		}
		$db    = JFactory::getDbo();
		$query = $db->getQuery(true);

		$query->select('a.extension_id');
		$query->from('#__extensions AS a');
		$query->where('a.type = "component"');
		$query->where('a.element = "com_cck"');
		$query->where('a.enabled = 1');

		$db->setQuery($query);

		if ($db->loadResult()) {
			return true;
		} else {
			return false;
		}

	}

	/**
	 * @param array $filters
	 * @param array $sort_filters
	 */
	public function __construct($filters = array(), $sort_filters = array())
	{
		parent::__construct('seblod');
		$this->setFilterChoices($filters, $sort_filters);
	}

	/**
	 * @param     $raw_item
	 * @param int $dborder
	 *
	 * @return \RokSprocket_Item
	 */
	protected function convertRawToItem($raw_item, $dborder = 0)
	{
		require_once (JPath::clean(JPATH_SITE . '/components/com_content/helpers/route.php'));
		require_once (JPath::clean(JPATH_SITE . '/libraries/joomla/html/html/content.php'));
        require_once (JPath::clean(JPATH_SITE . '/libraries/cck/content/content.php'));


		//$app_type  = $this->params->get('seblod_application_type');
		//$textfield = $this->params->get('seblod_articletext_field', '');

		$item = new RokSprocket_Item();

		$item->setProvider($this->provider_name);
		$item->setId($raw_item->id);
		$item->setAlias($raw_item->alias);
		$item->setAuthor($raw_item->author_name);
		$item->setTitle($raw_item->title);
		$item->setDate($raw_item->created);
		$item->setPublished(($raw_item->state == 1) ? true : false);
		$item->setCategory($raw_item->category_title);
		$item->setHits($raw_item->hits);
		$item->setRating($raw_item->rating);
		$item->setMetaKey($raw_item->metakey);
		$item->setMetaDesc($raw_item->metadesc);
		$item->setMetaData($raw_item->metadata);
        $item->setPublishUp($raw_item->publish_up);
        $item->setPublishDown($raw_item->publish_down);

		//Set up texts array
		$texts       = array();
		$text_fields = self::getFieldTypes(array("textarea", "wysiwyg_editor", "text"));

		if (count($text_fields)) {
			$text = '';
			foreach ($text_fields as $field) {
				if ($field->storage_table == '#__cck_core') {
					$text = (isset($field->data)) ? $field->data : '';
				} elseif ($field->storage_table == '#__content' && $field->storage_field == 'introtext') {
                    $text = CCK_Content::getValue($raw_item->introtext, $field->name);
                } elseif ($field->storage_table == '#__content' && $field->storage_field == 'fulltext') {
                    $text = CCK_Content::getValue($raw_item->fulltext, $field->name);
                } else {
                    $text = $this->getFieldValue($raw_item->id, $field->storage_field, $field->storage_table);
				}
				$texts['text_' . $field->id] = $text;
			}
		}

		if (isset($raw_item->introtext) && !empty($raw_item->introtext)) {
			$text = '';
            $introtext = CCK_Content::getValue($raw_item->introtext,'introtext');
            $fulltext = CCK_Content::getValue($raw_item->introtext,'fulltext');

			if ($introtext || $fulltext) {
				if ($introtext && isset($introtext)) {
					$texts['text_introtext'] = $introtext;
				}
				if ($fulltext && isset($fulltext)) {
					$texts['text_fulltext'] = $fulltext;
				}
			} //must be regular joomla
			else {
				$texts['text_introtext'] = $raw_item->introtext;
				$texts['text_fulltext']  = $raw_item->fulltext;
                $texts['text_metadesc']  = $raw_item->metadesc;
			}
		}
		$item->setTextFields($texts);
		$item->setText($texts['text_introtext']);

		//set up images array
		$images       = array();
		$image_fields = self::getFieldTypes("upload_image");

		if (count($image_fields)) {
			foreach ($image_fields as $field) {
				$image_uri = '';
				if ($field->storage_table == '#__cck_core') {
					$image_uri = (isset($field->data)) ? $field->data : '';
				} elseif ($field->storage_table == '#__content' && $field->storage_field == 'introtext') {
                    $image_uri = CCK_Content::getValue($raw_item->introtext,$field->name);
                } elseif ($field->storage_table == '#__content' && $field->storage_field == 'fulltext') {
                    $image_uri = CCK_Content::getValue($raw_item->fulltext,$field->name);
				} else {
                    $image_uri = $this->getFieldValue($raw_item->id, $field->storage_field, $field->storage_table);
                }
				if (JFile::exists(JPath::clean(JPATH_SITE . '/' .$image_uri))) {
					$image_field = new RokSprocket_Item_Image();
					$image_field->setSource(JPath::clean(JURI::root(true).'/' .$image_uri));
					$image_field->setIdentifier('image_' . $field->id);
					$image_field->setCaption('');
					$image_field->setAlttext('');
					$images['image_' . $field->id] = $image_field;
				}
			}
		}
		if (isset($raw_item->images) && !empty($raw_item->images)) {
			try {
				$raw_images = RokCommon_JSON::decode($raw_item->images);
				if (isset($raw_images->image_intro)) {
					$image_intro = new RokSprocket_Item_Image();
					$image_intro->setSource(JPath::clean(JURI::root(true).'/' .$raw_images->image_intro));
					$image_intro->setIdentifier('image_intro');
					$image_intro->setCaption($raw_images->image_intro_caption);
					$image_intro->setAlttext($raw_images->image_intro_alt);
					$images[$image_intro->getIdentifier()] = $image_intro;
				}

				if (isset($raw_images->image_fulltext)) {
					$image_fulltext = new RokSprocket_Item_Image();
					$image_fulltext->setSource(JPath::clean(JURI::root(true).'/' .$raw_images->image_fulltext));
					$image_fulltext->setIdentifier('image_fulltext');
					$image_fulltext->setCaption($raw_images->image_fulltext_caption);
					$image_fulltext->setAlttext($raw_images->image_fulltext_alt);
					$images[$image_fulltext->getIdentifier()] = $image_fulltext;
				}


			} catch (RokCommon_JSON_Exception $jse) {
				//TODO log unable to get image for article
			}
		}
        if (isset($images['image_fulltext']) && $images['image_fulltext']) {
            $image_primary = $images['image_fulltext'];
        } else {
            if (isset($images['image_intro']) && $images['image_intro']){
                $image_primary = $images['image_intro'];
            } else {
                if(count($images)){
                    $image_primary = array_shift(array_values($images));
                } else {
                    $image_primary = array();
                }
            }
        }
        $item->setPrimaryImage($image_primary);
		$item->setImages($images);

		//set up links array
		$links       = array();
		$link_fields = self::getFieldTypes("link");

		if (count($link_fields)) {
			foreach ($link_fields as $field) {
				$link_url = '';
				if ($field->storage_table == '#_cck_core') {
					$link_url = (isset($field->data)) ? $field->data : '';
				} elseif ($field->storage_table == '#_content') {
                    $link_url = CCK_Content::getValue($raw_item->introtext, $field->name);
				}
				$link_field = new RokSprocket_Item_Link();
				$link_field->setUrl($link_url);
				$link_field->setText('');
				$links['url_' . $field->id] = $link_field;
			}
		}
		if (isset($raw_item->urls) && !empty($raw_item->urls)) {
			try {
				$raw_links = RokCommon_JSON::decode($raw_item->urls);
				if (isset($raw_links->urla)) {
					$linka = new RokSprocket_Item_Link();
					$linka->setUrl($raw_links->urla);
					$linka->setText($raw_links->urlatext);
					$linka->setIdentifier('urla');
					$links[$linka->getIdentifier()] = $linka;
					$item->setPrimaryLink($linka);
				}
				if (isset($raw_links->urlb)) {
					$linkb = new RokSprocket_Item_Link();
					$linkb->setUrl($raw_links->urlb);
					$linkb->setText($raw_links->urlbtext);
					$linkb->setIdentifier('urlb');
					$links[$linkb->getIdentifier()] = $linkb;
				}
				if (isset($raw_links->urlc)) {
					$linkc = new RokSprocket_Item_Link();
					$linkc->setUrl($raw_links->urlc);
					$linkc->setText($raw_links->urlctext);
					$linkc->setIdentifier('urlc');
					$links[$linkc->getIdentifier()] = $linkc;
				}
			}
			catch (RokCommon_JSON_Exception $jse) {
				//TODO log unable to get links for article
			}
		}
		$item->setLinks($links);

		$primary_link = new RokSprocket_Item_Link();
		$primary_link->setUrl(JRoute::_(ContentHelperRoute::getArticleRoute($raw_item->id, $raw_item->catid), true));
		$primary_link->getIdentifier('article_link');

		$item->setPrimaryLink($primary_link);

		// unknown joomla items
		$item->setCommentCount(0);
		$item->setTags(array());

		$item->setDbOrder($dborder);

		return $item;
	}

	/**
	 * @param      $id
	 *
	 * @param bool $raw return the raw object not the RokSprocket_Item
	 *
	 * @return stdClass|RokSprocket_Item
	 * @throws RokSprocket_Exception
	 */
	public function getArticleInfo($id, $raw = false)
	{
        require_once (JPath::clean(JPATH_SITE . '/libraries/cck/content/content.php'));

		/** @var $filer_processor RokCommon_Filter_IProcessor */
		$filer_processor = $this->getFilterProcessor();
		$filer_processor->process(array('id' => array($id)));
		$query = $filer_processor->getQuery();
		$db    = JFactory::getDbo();
		$db->setQuery($query);
		$db->query();
		if ($error = $db->getErrorMsg()) {
			throw new RokSprocket_Exception($error);
		}
		$ret = $db->loadObject();
		if ($raw) {
			//if its Seblod we have to do a match to get the introtext and full text
            $introtext = CCK_Content::getValue($ret->introtext, 'introtext');
			$fulltext = CCK_Content::getValue($ret->introtext, 'fulltext');

			if ($introtext || $fulltext) {
				$ret->preview = $this->_cleanPreview($introtext . $fulltext);
			} //guess its old joomla
			else {
				$ret->preview = $this->_cleanPreview($ret->introtext . $ret->fulltext);
			}
			$ret->editUrl = $this->getArticleEditUrl($id);
			return $ret;
		} else {
			$item          = $this->convertRawToItem($ret);
			$item->editUrl = $this->getArticleEditUrl($id);
			$item->preview = $this->_cleanPreview($item->getText());
			return $item;
		}
	}

	/**
	 * @param $id
	 *
	 * @return string
	 */
	protected function getArticleEditUrl($id)
	{
		$db    = JFactory::getDbo();
		$query = $db->getQuery(true);

		$query->select('a.cck');
		$query->from('#__cck_core AS a');
		$query->where('a.pk = ' . $id);

		$db->setQuery($query);

		$type = $db->loadResult();
		return JURI::root(true) . '/administrator/index.php?option=com_cck&view=form&return=content&type=' . $type . '&id=' . $id;
	}

	/**
	 * @return array the array of image type and label
	 */
	public static function getImageTypes()
	{
		$fields = self::getFieldTypes("upload_image");

		$list = array();
		foreach ($fields as $field) {
			$list['image_' . $field->value] = array();
			$list['image_' . $field->value]['group']   = $field->id;
			$list['image_' . $field->value]['display'] = $field->title;
		}
		$static = array(
			'image_intro'    => array(
				'group'   => null,
				'display' => 'COM_CONTENT_FIELD_INTRO_LABEL'
			),
			'image_fulltext' => array(
				'group'   => null,
				'display' => 'COM_CONTENT_FIELD_FULL_LABEL'
			)
		);
		$list   = array_merge($static, $list);
		return $list;
	}

	/**
	 * @return array the array of link types and label
	 */
	public static function getLinkTypes()
	{
		$fields = self::getFieldTypes("link");

		$list = array();
		foreach ($fields as $field) {
			$list['url_' . $field->value]            = array();
			$list['url_' . $field->value]['group']   = $field->id;
			$list['url_' . $field->value]['display'] = $field->title;
		}

		$static = array(
			'urla' => array(
				'group'   => null,
				'display' => 'Link A'
			),
			'urlb' => array(
				'group'   => null,
				'display' => 'Link B'
			),
			'urlc' => array(
				'group'   => null,
				'display' => 'Link C'
			)
		);

		$list = array_merge($static, $list);
		return $list;
	}

	/**
	 * @return array the array of link types and label
	 */
	public static function getTextTypes()
	{
		$fields = self::getFieldTypes(array("textarea", "wysiwyg_editor", "text"));

		$list = array();
		foreach ($fields as $field) {
			$list['text_' . $field->value]            = array();
			$list['text_' . $field->value]['group']   = $field->id;
			$list['text_' . $field->value]['display'] = $field->title;
		}
        $static = array(
            'text_introtext' => array('group' => null, 'display' => 'Intro Text'),
            'text_fulltext' => array('group' => null, 'display' => 'Full Text'),
            'text_metadesc' => array('group' => null, 'display' => 'Meta Description Text'),
        );
        $list = array_merge($static, $list);
		return $list;
	}

	/**
	 * @static
	 * @return array
	 */
	public static function getCCKGroups()
	{
		$types = self::getFieldTypes();

		$list = array();
		foreach ($types as $type) {
			$list[$type->id] = $type->title;
		}
		return $list;
	}

	private static function getFieldTypes($field = false)
		{

			$db    = JFactory::getDbo();
			$query = $db->getQuery(true);

			$query->select('cf.id, cf.id as value, cf.title, cf.storage_table, cf.storage_field, c.storage_table AS data, cf.name');
			$query->from('#__cck_core_fields AS cf');
			$query->join('LEFT', '#__cck_core_type_field AS ctf ON ctf.fieldid = cf.id');
			$query->join('LEFT', '#__cck_core_types AS ct ON ct.id = ctf.typeid');
			$query->join('LEFT', '#__cck_core AS c ON c.cck = ct.name');
			$query->where('c.storage_location = "joomla_article"');

			if ($field && is_array($field)) {
				$wheres = array();
				foreach ($field as $match) {
					$wheres[] = ('cf.type = "' . $match . '"');
				}
				$query->where('(' . implode(' OR ', $wheres) . ')');
			} else if ($field && is_string($field)) {
				$query->where('cf.type = "' . $field . '"');
			}

			$query->group('cf.id');
			$query->order('cf.title ASC');

			$db->setQuery($query);
			return $db->loadObjectList();
		}

    private static function getFieldValue($id = false, $field = false, $table = false)
    {
        if(!$field || !$table || !$id) return '';

        $db    = JFactory::getDbo();
        $query = $db->getQuery(true);

        $query->select($field);
        $query->from($table);
        $query->where('id = '.$id);

        $db->setQuery($query);
        return $db->loadResult();
    }
}