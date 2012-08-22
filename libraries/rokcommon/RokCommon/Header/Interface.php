<?php
/**
 * @version   $Id: Interface.php 53534 2012-06-06 18:21:34Z btowles $
 * @author    RocketTheme http://www.rockettheme.com
 * @copyright Copyright (C) 2007 - ${copyright_year} RocketTheme, LLC
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 */
interface RokCommon_Header_Interface
{
	/**
	 * @param $file
	 */
	public function addScript($file);

	/**
	 * @param $text
	 */
	public function addInlineScript($text);

	/**
	 * @param $file
	 */
	public function addStyle($file);

	/**
	 * @param $text
	 */
	public function addInlineStyle($text);
}
