<?php
/**
 * @version   $Id: Text.php 53534 2012-06-06 18:21:34Z btowles $
 * @author    RocketTheme http://www.rockettheme.com
 * @copyright Copyright (C) 2007 - ${copyright_year} RocketTheme, LLC
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 */
defined('ROKCOMMON') or die;
/**
 *
 */
class RokCommon_Filter_Type_Text extends RokCommon_Filter_Type
{
	/**
	 * @var string
	 */
	protected $type = 'text';
	/**
	 * @var bool
	 */
	protected $isselector = true;

	/**
	 * @var array
	 */
	protected $selection_types = array(
		'contains'   => 'RokCommon_Filter_Type_TextEntry',
		'beginswith' => 'RokCommon_Filter_Type_TextEntry',
		'endswith'   => 'RokCommon_Filter_Type_TextEntry',
		'is'         => 'RokCommon_Filter_Type_TextEntry'
	);

	/**
	 * @var array
	 */
	protected $selection_labels = array(
		'contains'   => 'contains',
		'beginswith' => 'begins with',
		'endswith'   => 'ends with',
		'is'         => 'is'
	);

	/**
	 * @return string
	 */
	public function getChunkSelectionRender()
	{
		return rc__('ROKCOMMON_FILTER_TEXT_RENDER', $this->getTypeDescription());
	}

	/**
	 * @param $name
	 * @param $type
	 * @param $values
	 *
	 * @return string
	 */
	public function render($name, $type, $values)
	{
		return rc__('ROKCOMMON_FILTER_TEXT_RENDER', parent::render($name, $type, $values));
	}
}
