<?php
/**
 * JReviews - Reviews Extension
 * Copyright (C) 2006-2010 ClickFWD LLC
 * This is not free software, do not distribute it.
 * For licencing information visit http://www.reviewsforjoomla.com
 * or contact sales@reviewsforjoomla.com
**/

defined( 'MVC_FRAMEWORK') or die( 'Direct Access to this location is not allowed.' );

class MyController extends S2Controller 
{
    var $tmpl_list = null; 
    var $_db; // This should be instantiated only in controllers where it is needed
    
    function beforeFilter() 
    {                    
        # These should be called in each controller where they are required instead of globally
        $this->_db = cmsFramework::getDB();
        $this->_user = cmsFramework::getUser();
        
        # Init Access
		if(isset($this->Access))
        {
            $this->Access->init($this->Config);            
        }

        App::import('Component','theming','jreviews');
        $this->Theming = ClassRegistry::getClass('ThemingComponent');
        $this->Theming->startup($this);
        
		# Set pagination vars
		// First check url, then menu parameter. Otherwise the limit list in pagination doesn't respond b/c menu params always wins
        $this->limit = Sanitize::getInt($this->params,'limit',Sanitize::getInt($this->data,'limit_special',Sanitize::getInt($this->data,'limit')));
//		$this->passedArgs['limit'] = $this->limit;

		$this->page = Sanitize::getInt($this->data,'page',Sanitize::getInt($this->params,'page',1));
		
        if(!$this->limit) 
        {
	 		if(Sanitize::getVar($this->params,'action')=='myreviews') {
				$this->limit = Sanitize::getInt($this->params,'limit',$this->Config->user_limit);						
			} else {
				$this->limit = Sanitize::getInt($this->params,'limit',$this->Config->list_limit);			
			}
		} 
        // Set a hard code limit to prevent abuse
        $this->limit = max(min($this->limit, 50),1);

		// Need to normalize the limit var for modules
		if(isset($this->params['module'])) {
			$module_limit = Sanitize::getInt($this->params['module'],'module_limit',5);
		} else {
			$module_limit = 5;
		}

		$this->module_limit = Sanitize::getInt($this->data,'module_limit',$module_limit);
		$this->module_page = Sanitize::getInt($this->data,'module_page',1);
		$this->module_page = $this->module_page === 0 ? 1 : $this->module_page;
		$this->module_offset = (int)($this->module_page-1) * $this->module_limit;	
		if($this->module_offset < 0) $this->module_offset = 0;
	
		$this->page = $this->page === 0 ? 1 : $this->page;
		$this->offset = (int)($this->page-1) * $this->limit;
		
		if($this->offset < 0) $this->offset = 0;
	      
        # Required further below for Community Model init
        if(!isset($this->Menu))  {
            App::import('Model','menu','jreviews');
            $this->Menu = ClassRegistry::getClass('MenuModel');    
        }

        if(!defined('MVC_GLOBAL_JS_VARS') && !$this->ajaxRequest && $this->action != '_save') // action conditional is for new listing submission, otherwise the form hangs 
		{     
            # Find and set one public Itemid to use for Ajax requests
            $menu_id = '';
            $menu_id = $this->Menu->get('jreviews_public');
            $menu_id = $menu_id != '' ? $menu_id : 99999;                
            $this->set('public_menu_id',$menu_id);
            # Add global javascript variables
            $this->assets['head-top'][] = '<script type="text/javascript">
            /* <![CDATA[ */
            var s2AjaxUri = "'.getAjaxUri().'",
                jrLanguage = new Array(),
                jrVars = new Array(),
                datePickerImage = "'.$this->viewImages.'calendar.gif",
                jrPublicMenu = '.$menu_id.';
            jrLanguage["cancel"] = "'.__t("Cancel",true).'";
            jrLanguage["submit"] = "'.__t("Submit",true).'";
            jrLanguage["field.select"] = "'.__t("-- Select --",true).'";
            jrLanguage["field.select_field"] = "'.__t("-- Select %s --",true).'";
            jrLanguage["field.no_results"] = "'.__t("No results found, try a different spelling.",true).'";
            jrLanguage["field.ui_help"] = "'.__t("Start typing for suggestions",true).'";
            jrLanguage["field.ui_add"] = "'.__t("Add",true).'";
            jrLanguage["compare.heading"] = "'.__t("Compare",true).'";
            jrLanguage["compare.compare_all"] = "'.__t("Compare All",true).'";
            jrLanguage["compare.remove_all"] = "'.__t("Remove All",true).'";
            jrLanguage["compare.select_more"] = "'.__t("You need to select more than one listing for comparison.",true).'";
            jrLanguage["compare.select_max"] = "'.__t("You selected maximum number of listings for comparison.",true).'";
            jrLanguage["geomaps.no_streeview"] = "'.__t("Street view not available for this address.",true).'";
            jrLanguage["geomaps.cannot_geocode"] = "'.__t("Address could not be geocoded. Modify the address and click on the Geocode Address button to try again.",true).'";
            jrLanguage["geomaps.drag_marker"] = "'.__t("Drag the marker to fine-tune the geographic location on the map.",true).'";
            jrLanguage["geomaps.directions_bad_address"] = "'.__t("No corresponding geographic location could be found for one of the specified addresses. This may be due to the fact that the address is relatively new, or it may be incorrect.",true).'";
            jrLanguage["geomaps.directions_request_error"] = "'.__t("The was an error processing the request.",true).'";
            jrVars["locale"] = "'.cmsFramework::getLocale().'";
            /* ]]> */
            </script>';

			if($item_id = Sanitize::getInt($this->params,'Itemid')) 
            {
                $menu = $this->Menu->getMenuParams($item_id);
                $meta_desc = Sanitize::getString($menu,'menu-meta_description');
                $meta_keys = Sanitize::getString($menu,'menu-meta_keywords');
                $meta_desc != '' and cmsFramework::meta('description',$meta_desc);
                $meta_keys != '' and cmsFramework::meta('keywords',$meta_keys);
            }
            
            define('MVC_GLOBAL_JS_VARS',1);			
		}

        # Dynamic Community integration loading
        $community_extension = Configure::read('Community.extension');
        $community_extension = $community_extension != '' ? $community_extension : 'community_builder';
        
        App::import('Model',$community_extension,'jreviews');
        $this->Community = new CommunityModel();
        
        # Init plugin system
        $this->_initPlugins();  
    }             

    function afterFilter() 
    {       
        if(!class_exists('AssetsHelper')) {
            App::import('Helper','assets','jreviews');
        }
        
        $Assets = ClassRegistry::getClass('AssetsHelper'); 
        // Need to override name and action because using $this->requestAction in theme files replaces the original values (i.e. related listings prevents detail page js/css from loading)
        $Assets->name = $this->name;  
        $Assets->action = $this->action;        
        $Assets->params = $this->params;        
        $Assets->viewVars = & $this->viewVars;
        if(!isset($Assets->Access)) 
        {
            if(!isset($this->Access)) // View cache
            {
                App::import('Component','access','jreviews');
                $Access = new AccessComponent();
                if(!is_object($this->_user)) {
                    $User = &cmsFramework::getUser();    
                } 
                else {
                    $User = $this->_user;
                } 
                $Access->gid = $Access->getGroupId($User->id);
                $Assets->Access = &$Access;
            }   
            else {
                $Assets->Access = & $this->Access;
            }
        }
        
        if(!isset($Assets->Config)) {
            if(!isset($this->Config)) {
                $Assets->Config = Configure::write('JreviewsSystem.Config');
            }
            else {
                $Assets->Config = & $this->Config;     
            }
        }

        // Can't use this in ajax requests because it's output outside the json response and breaks it  
        if(!$this->ajaxRequest)
        {   
            if(!empty($this->assets))
            {
                $Assets->assets = $this->assets;
            }
            $Assets->load(); 
        }
    }        
    
    /**
    * Validates the request integrity token. The token location will vary for post/get requests
    * 
    */
    function __validateToken($token)
    {
        return Sanitize::getString($this->params['form'],$token,Sanitize::getString($this->params,$token));    
    }
    
/**********************************************************
*  Plugin callbacks
**********************************************************/
    /**
    * Plugin system initialization
    * 
    * @param object $model - include for lazy loading of plugin callbacks for a particular model. This may be required when trying to trigger a callback in a model outside it's main controller
    */
    function _initPlugins($model = null)
    {               
        // Load plugins
        $App = &App::getInstance();
        $registry = &$App->jreviewsPaths;
        $plugins = array_keys($registry['Plugin']);     
        
        if(!empty($plugins))
        {
            $plugins = str_replace('.php','',$plugins);  
            App::import('Plugin',$plugins);
            $this->__initComponents($plugins);
            foreach($plugins AS $plugin)
            {              
                $component_name = Inflector::camelize($plugin);
                
                if(isset($this->{$component_name}) && $this->{$component_name}->published)
                {                          
                    // Register all the plugin callbacks in the controller
                    $plugin_methods = get_class_methods($this->{$component_name});
                    
                    foreach($plugin_methods AS $callback)
                    {                                
                        if(substr($callback,0,3)=='plg')
                        {                            
                            if(method_exists($this,'getPluginModel')) 
                            {                            
                                if(is_null($model))
                                    {
                                        $this->{$component_name}->plgModel = & $this->getPluginModel();                                    
                                    }   
                                else 
                                    {        
                                        $this->{$component_name}->plgModel = & $this->{$model};                                                                            
                                    }                   
                               
                                $plgModel = & $this->{$component_name}->plgModel;

                                if(!isset($this->{$component_name}->validObserverModels)
                                    ||
                                        (
                                            isset($this->{$component_name}->validObserverModels)
                                            && !empty($this->{$component_name}->validObserverModels)
                                            && in_array($plgModel->name,$this->{$component_name}->validObserverModels)
                                        ) 
                                    )
                                {                                         
                                    $plgModel->addObserver($callback,$this->{$component_name});
                                }
                            }                            
                        }
                    }                    
                    if(method_exists($this->{$component_name},'plgBeforeRender'))
                    {                
                        $this->plgBeforeRender[] = $component_name;
                    }                 
                }
            }
        }
        
        unset($App,$registry);
    }    
}
