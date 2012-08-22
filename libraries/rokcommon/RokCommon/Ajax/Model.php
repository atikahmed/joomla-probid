<?php
/**
 * @version   $Id: Model.php 54417 2012-07-18 22:22:53Z build $
 * @author    RocketTheme http://www.rockettheme.com
 * @copyright Copyright (C) 2007 - ${copyright_year} RocketTheme, LLC
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 */
defined('ROKCOMMON') or die;

interface RokCommon_Ajax_Model
{

	/**
	 * @abstract
	 *
	 * @param  $action
	 * @param  $params
	 *
	 * @return RokCommon_Ajax_Result
	 */
	public function run($action, $params);

	/**
	 * @abstract
	 *
	 * @param $errno
	 * @param $errstr
	 * @param $errfile
	 * @param $errline
	 *
	 * @return mixed
	 */
	public function errorHandler($errno, $errstr, $errfile, $errline);
}
