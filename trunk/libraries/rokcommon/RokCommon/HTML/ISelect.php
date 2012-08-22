<?php
/**
 * @version   $Id: ISelect.php 53534 2012-06-06 18:21:34Z btowles $
 * @author    RocketTheme http://www.rockettheme.com
 * @copyright Copyright (C) 2007 - ${copyright_year} RocketTheme, LLC
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 */

interface RokCommon_HTML_ISelect
{
    /**
     * @abstract
     *
     * @param string                         $name
     * @param RokCommon_HTML_Select_Option[] $options
     * @param array                          $attribs
     *
     * @return string the html rendered select list
     */
    public function getList($name, array $options = array(), $attribs = array());
}
