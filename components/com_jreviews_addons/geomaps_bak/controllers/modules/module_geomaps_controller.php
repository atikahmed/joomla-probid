<?php
  /**
 * GeoMaps Addon for JReviews
 * Copyright (C) 2006-2009 ClickFWD LLC
 * This is not free software, do not distribute it.
 * For licencing information visit http://www.reviewsforjoomla.com
 * or contact sales@reviewsforjoomla.com
**/

App::import('Controller','common','jreviews');

defined( 'MVC_FRAMEWORK') or die( 'Direct Access to this location is not allowed.' );
class ModuleGeomapsController extends MyController {
        
    var $uses = array('user','menu','field','criteria','category');
    var $helpers = array('routes','libraries','html','text','jreviews','rating','thumbnail','custom_fields');
    var $components = array('access','config','everywhere');

    var $autoRender = false; //Output is returned
    var $autoLayout = false;

    var $jr_lat;
    var $jr_lon;
        
    // Need to return object by reference for PHP4
    function &getPluginModel() {
        return $this->Listing;
    }  
            
    function beforeFilter() 
    {      
        # Call beforeFilter of MyController parent class
        parent::beforeFilter();
        $this->jr_lat = Sanitize::getString($this->Config,'geomaps.latitude');
        $this->jr_lon = Sanitize::getString($this->Config,'geomaps.longitude');
    }
               
    function listings() 
    {
        // Initialize variables        
        $id = Sanitize::getInt($this->params,'id');        
        $option = Sanitize::getString($this->params,'option');        
        $view = Sanitize::getString($this->params,'view');
        $menu_id = Sanitize::getString($this->params,'Itemid');        

        // Read params
        $cat_id = '';
        $criteria_ids = '';
        $in_detail_view = false;
        $detail_view = 1;
        $dir_id = Sanitize::getString($this->params,'dir');
        $section_id = Sanitize::getString($this->params,'section');
        $cat_id = Sanitize::getString($this->params,'cat');

        $extension = 'com_content';
        $custom_where = null;        
        $custom_fields = array();
        $click2search_auto = false;
        $cache = 0;
        $radius = 0;
        $mode = 0;

        if(isset($this->params['module']))
        {
            // Read module parameters
            $click2search_auto = Sanitize::getBool($this->params['module'],'click2search_auto',false);
            $custom_where = Sanitize::getString($this->params['module'],'custom_where');
            $filter = Sanitize::getString($this->params['module'],'filter');
            $detail_view = Sanitize::getString($this->params['module'],'detail_view',1);
            $dir_id = Sanitize::getString($this->params['module'],'dir');
            $section_id = Sanitize::getString($this->params['module'],'section');
            $cat_id = Sanitize::getString($this->params['module'],'category');
            $listing_id = Sanitize::getString($this->params['module'],'listing');
            $criteria_ids = Sanitize::getString($this->params['module'],'criteria');
            $limit_results = Sanitize::getInt($this->params['module'],'limit_results');
            $mode = Sanitize::getInt($this->params['module'],'mode',0);
            
            $custom_fields = str_replace(" ","",Sanitize::getString($this->Config,'geomaps.infowindow_fields'));
            $custom_fields = $custom_fields != '' ? explode(",",$custom_fields) : array();

            /**
            * 0 - Normal
            * 1 - GeoTargeting
            * 2 - Custom center and zoom
            */
            $radius = Sanitize::getInt($this->params['module'],'radius');
            $cache = $mode == 1 ? 0 : Sanitize::getInt($this->params['module'],'cache_map');
            $custom_lat = Sanitize::getFloat($this->params['module'],'custom_lat');
            $custom_lon = Sanitize::getFloat($this->params['module'],'custom_lon');
            if($mode == 2 && ($custom_lat == 0 || $custom_lon == 0))
            {
                echo __t("You selected the Custom Center mode, but did not specify the coordinates."); return;
            }
        }
         
        # Prevent sql injection
        $token = Sanitize::getString($this->params,'token');
        $tokenMatch = 0 === strcmp($token,cmsFramework::formIntegrityToken($this->params,array('module','module_id','form','data'),false));   
           
        $filters = $listing_id != '' || $dir_id != '' || $section_id != '' || $cat_id != '';   
        if(!$filters && $id > 0 && ('article' == $view) && 'com_content' == $option) 
        {       
            $sql = "SELECT catid FROM #__content WHERE id = " . $id;
            $this->_db->setQuery($sql);
            $cat_id_host_page = $this->_db->loadResult();
            if(!empty($cat_id_host_page) && $this->Category->isJreviewsCategory($cat_id_host_page)) {
                $in_detail_view = true;
                $cat_id = $cat_id_host_page;
            } 
        }
        
        $detail_view = $this->params['module']['detail_view'] = (int) ($detail_view && $in_detail_view);

        # Custom WHERE
        $tokenMatch and $custom_where and $conditions[] = $custom_where;

        if($click2search_auto && isset($this->params['tag']))
        {
            $field = 'jr_'.Sanitize::getString($this->params['tag'],'field');
            $value = Sanitize::getString($this->params['tag'],'value');
            $query = "SELECT Field.type FROM #__jreviews_fields AS Field WHERE Field.name = " . $this->quote($field);
            $this->_db->setQuery($query);
            $type = $this->_db->loadResult();
            if(in_array($type,array('select','selectmultiple','checkboxes','radiobuttons')))
            {
                $conditions[] = "Field.{$field} LIKE " . $this->quoteLike('*'.$value.'*');
            } else {
                $conditions[] = "Field.{$field} = " . $this->quote($value);
            }
        }
        
        # Category auto detect
        if(isset($this->params['module']) && Sanitize::getInt($this->params['module'],'cat_auto') && $extension == 'com_content') 
        { 
            $ids = CommonController::_discoverIDs($this);
            extract($ids);
        }        

        $autodetect = compact('dir_id','section_id','cat_id');
        
        // Check for cached version if cache enabled    
        if($cache)
        {          
            $params = array();
            foreach($this->params AS $key=>$value){
                if((!is_array($value)||$key=='module') && !in_array($key,array('page','limit','order','Itemid'))){
                    $params[$key] = $value;
                }
            }
            $cache_key = array_merge($params,$autodetect,Sanitize::getVar($this->params,'tag',array()));
            
            $json_filename = 'geomaps_'.md5(serialize($cache_key)).'.json';
            $json_data = S2Cache::read($json_filename);
            if($json_data && $json_data!='')
            {
                $this->set('json_data',$json_data);
                S2Cache::write($json_filename,$json_data);            
                return $this->render('modules','geomaps');        
            }
        }
                
        $this->Listing->fields = array(
            'Listing.id AS `Listing.listing_id`',
            'Listing.title AS `Listing.title`',
            'Listing.images AS `Listing.images`',
            'CASE WHEN CHAR_LENGTH(Listing.alias) THEN Listing.alias ELSE "" END AS `Listing.slug`',
            'Category.id AS `Listing.cat_id`',
            'CASE WHEN CHAR_LENGTH(Category.alias) THEN Category.alias ELSE Category.title END AS `Category.slug`',
            'Listing.sectionid AS `Listing.section_id`',
            'JreviewsCategory.criteriaid AS `Criteria.criteria_id`',
            'JreviewsCategory.dirid AS `Directory.dir_id`',
            'Field.featured AS `Listing.featured`',
            'Totals.user_rating AS `Review.user_rating`',
            'Totals.user_rating_count AS `Review.user_rating_count`',
            'Totals.editor_rating AS `Review.editor_rating`', 
            'Totals.editor_rating_count AS `Review.editor_rating_count`',
            "Field.{$this->jr_lat} `Geomaps.lat`",
            "Field.{$this->jr_lon} `Geomaps.lon`",
            'JreviewsCategory.marker_icon AS `Geomaps.icon`'            
        );

        if($custom_lon != '' and $custom_lat != '') {
            $this->set('CustomCenter',array('lon'=>$custom_lon,'lat'=>$custom_lat));
        }

        // Geo Targeting OR Custom Center modes
        if($mode == 1 || $mode == 2)
        {
            
            if($mode == 1)  // Geo Targeting
            {
                $ch = curl_init();
                curl_setopt ($ch, CURLOPT_URL, 'http://www.geoplugin.net/php.gp?ip='.s2GetIpAddress());
                curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt ($ch, CURLOPT_CONNECTTIMEOUT, 5);
                $geoData = unserialize(curl_exec($ch));       
                curl_close($ch);
                 if(!empty($geoData) && $geoData['geoplugin_latitude']!='' && $geoData['geoplugin_longitude']!='')
                 {
                    $center = array('lon'=>$geoData['geoplugin_longitude'],'lat'=>$geoData['geoplugin_latitude']);               
                 }       
                 $this->set('geoLocation',$geoData);
            }
                 
            if($mode == 2)
            {
                $center = array('lon'=>$custom_lon,'lat'=>$custom_lat);
            }
             
            if(!empty($center) && $radius > 0)
            {
                $distanceIn =  Sanitize::getString($this->Config,'geomaps.radius_metric','mi');
                $degreeDistance = $distanceIn == 'mi' ? 69.172 : 40076/360;         
                // Send center coordinates to theme
                $this->set('GeomapsCenter',$center);
                $lat_range = $radius/$degreeDistance; 
                $lon_range = $radius/abs(cos($center['lat']*pi()/180)*$degreeDistance); 
                $min_lat = $center['lat'] - $lat_range;
                $max_lat = $center['lat'] + $lat_range;
                $min_lon = $center['lon'] - $lon_range;
                $max_lon = $center['lon'] + $lon_range;
                $squareArea = "`Field`.{$this->jr_lat} BETWEEN $min_lat AND $max_lat AND `Field`.{$this->jr_lon} BETWEEN $min_lon AND $max_lon";
                $conditions[] = $squareArea;            
            }
        }      
                  
        // Create marker_icons array
        $marker_icons = array();
        $icon_fields = array();
        $field_images = array(); 
        $query = "SELECT DISTINCT marker_icon FROM #__jreviews_categories WHERE marker_icon != ''";
        $this->_db->setQuery($query);
        $icon_rows = $this->_db->loadAssocList();
        foreach($icon_rows AS $icons)
        {
            $icon = (array)json_decode($icons['marker_icon']); 
            if($icon['field']!='') 
            {
                $icon_fields[$icon['field']] = "'".$icon['field']."'";
            }
        }

        if(!empty($icon_fields))
        {
            foreach($icon_fields AS $field_key=>$field)
            {
                if(substr($field_key,0,3) == 'jr_') {
                    $this->Listing->fields[] = "Field.{$field_key} AS `Field.{$field_key}`";
                }
            }
        }   
             
        if(!empty($custom_fields))
        {
            foreach($custom_fields AS $field)
            {
                $this->Listing->fields[] = "Field.{$field} AS `Field.{$field}`";
            }
        }

        $this->Listing->joins = array(
            "LEFT JOIN #__categories AS Category ON Listing.catid = Category.id",
            'ParentCategory'=>"LEFT JOIN #__categories AS ParentCategory ON Category.lft BETWEEN ParentCategory.lft AND ParentCategory.rgt",              
            "LEFT JOIN #__jreviews_listing_totals AS Totals ON Totals.listing_id = Listing.id AND Totals.extension = 'com_content'",
            "LEFT JOIN #__jreviews_content AS `Field` ON Field.contentid = Listing.id",
            "INNER JOIN #__jreviews_categories AS JreviewsCategory ON Listing.catid = JreviewsCategory.id AND JreviewsCategory.`option` = 'com_content'",
            "LEFT JOIN #__jreviews_directories AS Directory ON JreviewsCategory.dirid = Directory.id",
        );
            
        // Don't regroup the results by model name keys to save time
        $this->Listing->primaryKey = false;

        # Set conditionals based on configuration parameters
        if($detail_view){
            $conditions[] = 'Listing.id = ' . $id;            
        } 
        
        if(!empty($cat_id))
        {           
            $conditions[] = $this->cmsVersion == CMS_JOOMLA15 
                ? 
                'Listing.catid IN ('.cleanIntegerCommaList($cat_id).')' 
                : 
                'ParentCategory.id IN ('.cleanIntegerCommaList($cat_id).')';
        } 

        if($this->cmsVersion == CMS_JOOMLA15) unset($this->Listing->joins['ParentCategory']);

        empty($cat_id) and !empty($section_id) and $conditions[] = 'Listing.sectionid IN (' .cleanIntegerCommaList($section_id). ')';

        empty($cat_id) and !empty($dir_id) and $conditions[] = 'JreviewsCategory.dirid IN (' . cleanIntegerCommaList($dir_id) . ')';

        empty($cat_id) and !empty($criteria_id) and $conditions[] = 'JreviewsCategory.criteriaid IN (' . cleanIntegerCommaList($criteria_id) . ')';
 
        if($listing_id)
        {
            $conditions[] = 'Listing.id IN (' . $listing_id . ')';
        }
        
        if($filter == 'featured' && !$detail_view)
        {                          
            $conditions[] = 'Field.featured = 1';
        }
                
        $conditions[] = "Field.{$this->jr_lat} <> ''";
        $conditions[] = "Field.{$this->jr_lon} <> ''";

        $conditions = array_merge($conditions,array(
            'Listing.state = 1',
            '( Listing.publish_up = "'.NULL_DATE.'" OR DATE(Listing.publish_up) <= DATE("'._CURRENT_SERVER_TIME.'") )',
            '( Listing.publish_down = "'.NULL_DATE.'" OR DATE(Listing.publish_down) >= DATE("'._CURRENT_SERVER_TIME.'") )'
        ));

        if($this->cmsVersion == CMS_JOOMLA15)
        {
//                    $conditions[] = 'Section.access <= ' . $this->Access->getAccessId();
            $conditions[] = 'Category.access <= ' . $this->Access->getAccessId();
            $conditions[] = 'Listing.access <= ' . $this->Access->getAccessId();
        }
        else
        {
            $conditions[] = 'Category.access IN (' . $this->Access->getAccessLevels() . ')';
            $conditions[] = 'Listing.access IN (' . $this->Access->getAccessLevels() . ')';
        }
        
        // Paid Listings - add plan cat id
        isset($this->PaidListings) and $this->PaidListings->applyBeforeFindListingChanges($this->Listing); 
        
        $listings = $this->Listing->findAll(array('conditions'=>$conditions,'limit'=>$limit_results),array());
        $custom_fields = array_filter(array_merge($custom_fields,array_keys($icon_fields)));
        $fieldOptionValues = array();
        
        // Extract custom field values to avoid loading all options for each fields
        // It's a trade-off between that and doing a foreach on all listings
        foreach($listings AS $key=>$row) {
            foreach($custom_fields AS $field) {
                $optionValue = Sanitize::getVar($row,'Field.'.$field);
                if($optionValue != '' && $optionValue != '**') {
                    $fieldOptionValues = array_merge($fieldOptionValues,array_filter(explode('*',$optionValue)));
                }
            }                            
        }
        
        $fields = $this->Field->getFields($custom_fields, 'listing', $fieldOptionValues);
        $json_data = $this->Geomaps->makeJsonObject($listings,$fields,$this->params['module']);

        $this->set('json_data',$json_data);
        if($cache)
        {
            S2Cache::write($json_filename,$json_data);   
        }            
        return $this->render('modules','geomaps');
     }
}
