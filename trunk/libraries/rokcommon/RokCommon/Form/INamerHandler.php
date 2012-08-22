<?php
/**
 * @version   $Id: INamerHandler.php 53534 2012-06-06 18:21:34Z btowles $
 * @author    RocketTheme http://www.rockettheme.com
 * @copyright Copyright (C) 2007 - ${copyright_year} RocketTheme, LLC
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 */

interface RokCommon_Form_INamerHandler
{
    /**
     * @abstract
     *
     * @param object       $caller
     * @param string       $name
     * @param string       $group
     * @param string|null  $formcontrol
     * @param bool         $multiple
     *
     * @return string the name to use for the html tag
     */
    public function getName(&$caller, $name, $group = null, $formcontrol = null, $multiple = false);

    /**
     * @abstract
     *
     * @param object       $caller
     * @param string       $name
     * @param string|null  $id
     * @param string       $group
     * @param string|null  $formcontrol
     * @param bool         $multiple
     *
     * @return string the id to use for the html tag
     */
    public function getId(&$caller, $name, $id = null, $group = null, $formcontrol = null, $multiple = false);
}
