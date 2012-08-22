<?php
/**
 * @version   $Id: Converter.php 53534 2012-06-06 18:21:34Z btowles $
 * @author    RocketTheme http://www.rockettheme.com
 * @copyright Copyright (C) 2007 - ${copyright_year} RocketTheme, LLC
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 */

// No direct access
defined('ROKCOMMON') or die;

/**
 *
 */
class RokCommon_Registry_Converter
{
    /**
     * @static
     *
     * @param $original
     * @return \RokCommon_Registry
     */
    public static function convert($original)
    {
        $container = RokCommon_Service::getContainer();
        /** @var $converter RokCommon_Registry_IConverter */
        $converter = $container->registry_converter;
        return $converter->convert($original);
    }
}
