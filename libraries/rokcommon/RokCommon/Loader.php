<?php
/**
 * @version   $Id: Loader.php 53534 2012-06-06 18:21:34Z btowles $
 * @author    RocketTheme http://www.rockettheme.com
 * @copyright Copyright (C) 2007 - ${copyright_year} RocketTheme, LLC
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 */
defined('ROKCOMMON') or die;

/**
 *
 */
interface RokCommon_Loader
{
	/**
	 *
	 */
	const FILE_EXTENSION = '.php';

	/**
	 * @abstract
	 *
	 * @param  string $className the class name to look for and load
	 *
	 * @return bool True if the class was found and loaded.
	 */
	function loadClass($className);
}
