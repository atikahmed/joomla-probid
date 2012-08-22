<?php
/**
 * @version   $Id: IPicklistPopulator.php 53534 2012-06-06 18:21:34Z btowles $
 * @author    RocketTheme http://www.rockettheme.com
 * @copyright Copyright (C) 2007 - ${copyright_year} RocketTheme, LLC
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 */

interface RokCommon_Filter_IPicklistPopulator
{
    /**
     * @abstract
     *
     * @return array;
     */
    public function getPicklistOptions();
}
