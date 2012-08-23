<?php
defined( 'MVC_FRAMEWORK') or die( 'Direct Access to this location is not allowed.' );

class ListingsGeoComponent extends S2Component {
    
	var $published = true;
    /**
    * Limit plugin to run only in specific controller actions
    */
    var $controllerActions = array(
        'listings'=>'_save'
    );    
   
    function runPlugin(&$controller)
    {                            
        $this->c = &$controller;
        // Check if running in valid controller/actions
        if(!isset($this->controllerActions[$controller->name])){         
            return false;
        }
        
        $actions = !is_array($this->controllerActions[$controller->name]) ? array($this->controllerActions[$controller->name]) : $this->controllerActions[$controller->name];

        if(!in_array('all',$actions) && !in_array($controller->action,$actions)) {    
            return false;
        }
        return true;        
    }
        
    function startup(&$controller)
    { 
        if(!$this->runPlugin($controller))
        {                             
            return false;
        } 
        
        if(!defined('MVC_FRAMEWORK_ADMIN'))   
        {
            $this->c = & $controller;
        } 
    }     
    
	function plgAfterSave(&$model) {
		if($model->name == "Listing") {
			$postStr = "listing_id=" . $model->data['Listing']['id'];
			$url = "dev.probiddirect.com/custom/proxy/geo-update.php";
			$ch = curl_init();
			// set user agent
			$useragent = 'YahooSeeker-Testing/v3.9 (compatible; Mozilla 4.0; MSIE 5.5; http://search.yahoo.com/)';
			curl_setopt($ch, CURLOPT_USERAGENT, $useragent);
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $postStr);
			curl_setopt($ch, CURLOPT_FRESH_CONNECT, true);
			curl_setopt($ch, CURLOPT_TIMEOUT, 1);
			// execute curl,fetch the result and close curl connection
			curl_exec($ch);
			curl_close($ch);	
		}//ends if	
	}//ends plgAfterSave
}