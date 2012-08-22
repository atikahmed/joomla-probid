<?php
/**
 * @version   $Id: Table.php 53534 2012-06-06 18:21:34Z btowles $
 * @author    RocketTheme http://www.rockettheme.com
 * @copyright Copyright (C) 2007 - ${copyright_year} RocketTheme, LLC
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 */
defined('ROKCOMMON') or die;

abstract class RokCommon_Doctrine_Table extends Doctrine_Table
{
	/**
	 * @param $tableName
	 *
	 * @internal param void $
	 */
	public function setTableName($tableName)
	{
		parent::setTableName(RokCommon_Doctrine::getPlatformInstance()->setTableName($tableName));
	}

}
