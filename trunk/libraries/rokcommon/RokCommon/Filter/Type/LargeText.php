<?php
/**
 * @version   $Id: LargeText.php 53534 2012-06-06 18:21:34Z btowles $
 * @author    RocketTheme http://www.rockettheme.com
 * @copyright Copyright (C) 2007 - ${copyright_year} RocketTheme, LLC
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 */
defined('ROKCOMMON') or die;

/**
 *
 */
class RokCommon_Filter_Type_LargeText extends RokCommon_Filter_Type
{
	/**
	 * @var string
	 */
	protected $type = 'largetext';

	/**
	 * @return string
	 */
	public function getChunkRender()
	{
		return $this->getInput();
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
		$value = (isset($values[$type]) ? $values[$type] : '');
		return rc__('ROKCOMMON_FILTER_LARGETEXT_RENDER', $this->getInput($name, $value));
	}

	/**
	 * @return string
	 */
	public function getChunkSelectionRender()
	{
		return rc__('ROKCOMMON_FILTER_LARGETEXT_RENDER', $this->getTypeDescription());
	}

	/**
	 * @param string $name
	 * @param string $value
	 *
	 * @return string
	 */
	public function getInput($name = RokCommon_Filter_Type::JAVASCRIPT_NAME_VARIABLE, $value = '')
	{
		return '<input type="text" name="' . $name . '" class="' . $this->type . '" data-key="' . $this->type . '" value="' . $value . '"/>';
	}
}
