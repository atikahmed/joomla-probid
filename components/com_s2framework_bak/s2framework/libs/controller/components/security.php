<?php
/**
 * jReviews - Reviews Extension
 * Copyright (C) 2006-2008 Alejandro Schmeichler
 * This is not free software, do not distribute it.
 * For licencing information visit http://www.reviewsforjoomla.com
 * or contact sales@reviewsforjoomla.com
**/

defined( 'MVC_FRAMEWORK') or die( 'Direct Access to this location is not allowed.' );

class SecurityComponent extends S2Component 
{
    function startup(&$controller) 
    {
        $controller->invalidToken = true;
        
        $token = cmsFramework::getToken();

        Sanitize::getString($controller->params['form'],$token) and $controller->invalidToken = false;    
    }
}
