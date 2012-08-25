<?php
/**
 * @version     1.0.0
 * @package     com_ptslideshow
 * @copyright   Copyright (C) 2012. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @author      Created by com_combuilder - http://www.notwebdesign.com
 */

// No direct access
defined('_JEXEC') or die;

define( 'UPLOAD_DIR', 'media'.DS.'com_ptslideshow'.DS);
define( 'COM_IMAGE_BASE', JPATH_ROOT.DS.UPLOAD_DIR );
define( 'COM_IMAGE_BASEURL', JURI::root().str_replace( DS, '/', UPLOAD_DIR ));

jimport('joomla.application.component.controllerform');

/**
 * Slideshow controller class.
 */
class PtslideshowControllerSlideshow extends JControllerForm
{

    function __construct() {
        $this->view_list = 'slideshows';
        parent::__construct();
    }	
}