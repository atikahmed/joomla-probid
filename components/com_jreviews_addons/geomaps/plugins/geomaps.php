<?php
  /**
 * GeoMaps Addon for JReviews
 * Copyright (C) 2010-2012 ClickFWD LLC
 * This is not free software, do not distribute it.
 * For licencing information visit http://www.reviewsforjoomla.com
 * or contact sales@reviewsforjoomla.com
**/

defined( 'MVC_FRAMEWORK') or die( 'Direct Access to this location is not allowed.' );

class GeomapsComponent extends S2Component
{
    var $name = 'geomaps';

    /**
    * Changed dynamically in startup method to restrict the plugin's callbacks to certain controller actions
    */
    var $published = false;

    /**
     * Definitions for listing fields used for geocoding
     */
    var $google_url;
    var $google_api_url;
    var $google_api_key;
    var $jr_lat;
    var $jr_lon;
    var $jr_address1;
    var $jr_address2;
    var $jr_city;
    var $jr_state;
    var $jr_postal_code;
    var $jr_country;
    var $country_def;

    /**
     * Functionality variables
     */
    var $radius_field = 'jr_radius';
    var $distance_in = 'mi'; // mi or km
    var $max_radius = 100;
    var $distance_metric = array();

    /**
    * Define where plugin should run
    */
    var $controllerActions = array(
        'about'=>array('all'),
        'search'=>array('index','_process'),
		'module_advanced_search'=>'index',
		'module_geomaps'=>'listings',
        'categories'=>'all',
        'listings'=>array('create','edit','_loadForm','_save'),
        'com_content'=>'com_content_view',
        'admin_geomaps'=>'_geocodePopup',    // admin_geomaps
        'admin_listings'=>array('index','browse','edit','moderation')
    );


    function runPlugin(&$controller)
    {
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
        $this->c = &$controller;

        // Setup vars used in startup and other plugin methods
        $this->google_url = Sanitize::getString($this->c->Config,'geomaps.google_url','http://maps.google.com');

		$this->google_api_key = trim(Sanitize::getString($controller->Config,'geomaps.google_key'));

		$this->search_autocomplete = Sanitize::getBool($controller->Config,'geomaps.search_autocomplete',1);

		$this->google_api_url = $this->google_url."/maps/api/js?v=3&async=2&sensor=false&key={$this->google_api_key}";

		$this->google_api_url .= '&language=' . substr(cmsFramework::locale(),0,2);

		if($this->search_autocomplete) {
			$this->google_api_url .= '&libraries=places';
		}

		$controller->set('google_api_url',$this->google_api_url);

        if(!$this->runPlugin($controller))
        {
            return false;
        }

		if(!Configure::read('Geomaps.header'))
		{
			Configure::write('Geomaps.header',true);

			$language_js = '<script type="text/javascript">
			/* <![CDATA[ */
			var GeomapsGoogleApi = "'.$this->google_api_url.'",
				GeomapsOnload = new Array(),
				GeomapsLanguage = {
					submit : "'.__t("Submit",true).'",
					streeview_unavailable : "'.__t("Street view not available for this address.",true,true).'",
					cannot_geocode : "'.__t("Address could not be geocoded. Modify the address and click on the Geocode Address button to try again.",true).'",
					drag_marker : "'.__t("Drag the marker to fine-tune the geographic location on the map.",true).'",
					directions_bad_address : "'.__t("No corresponding geographic location could be found for one of the specified addresses. This may be due to the fact that the address is relatively new, or it may be incorrect.",true).'",
					directions_request_error : "'.__t("The was an error processing the request.",true).'",
					enter_location : "'.__t("Enter a location",true).'"
				};


			/* ]]> */
			</script>';

			if(defined('MVC_FRAMEWORK_ADMIN') && !$controller->ajaxRequest) {
				cmsFramework::addScript($language_js);
			}
			else {
				$controller->assets['head-top'][] = $language_js;
			}
		}

        // Initialize vars
        $center = array();
        $address = '';
        $lat = 0;
        $lon = 0;

        if(!isset($controller->Config))
        {
            $controller->Config = Configure::read('JreviewsSystem.Config');
        }

        if(!isset($controller->Access))
        {
            $controller->Config = Configure::read('JreviewsSystem.Access');
        }

        $this->max_radius = Sanitize::getInt($controller->Config,'geomaps.max_radius',$this->max_radius);

		$this->jr_lat = Sanitize::getString($controller->Config,'geomaps.latitude');

		$this->jr_lon = Sanitize::getString($controller->Config,'geomaps.longitude');

		if($this->jr_lat == '' || $this->jr_lon == '')
        {
            return false;
        }

        $search_method = Sanitize::getString($controller->Config,'geomaps.search_method','address'); // address/disabled
        $search_address_field = Sanitize::getString($controller->Config,'geomaps.advsearch_input');
        $default_radius = Sanitize::getString($controller->Config,'geomaps.radius');
        $this->distance_metric = array('mi'=>__t("Miles",true),'km'=>__t("Km",true));
        $this->distance_in = Sanitize::getString($controller->Config,'geomaps.radius_metric','mi');
        $this->jr_address1 = Sanitize::getString($controller->Config,'geomaps.address1');

		$this->jr_address2 = Sanitize::getString($controller->Config,'geomaps.address2');

		$this->jr_city = Sanitize::getString($controller->Config,'geomaps.city');

		$this->jr_state = Sanitize::getString($controller->Config,'geomaps.state');

		$this->jr_postal_code = Sanitize::getString($controller->Config,'geomaps.postal_code');

		$this->jr_country = Sanitize::getString($controller->Config,'geomaps.country');

		$this->country_def = Sanitize::getString($controller->Config,'geomaps.default_country');

		$this->gid = $controller->Access->gid;

        $this->address_fields = array_filter(array(
            'address1'=>$this->jr_address1,
            'address2'=>$this->jr_address2,
            'city'=>$this->jr_city,
            'state'=>$this->jr_state,
            'postal_code'=>$this->jr_postal_code,
            'country'=>$this->jr_country
        ));
        $this->geo_fields = array(
            'lat'=>$this->jr_lat,
            'lon'=>$this->jr_lon
        );

        $this->c->set(array(
            'address_fields'=>$this->address_fields,
            'geo_fields'=>$this->geo_fields
        ));

        /**
        * Address search checks
        */
        if(isset($controller->data['Field']['Listing']))
        {
            $address = Sanitize::getString($controller->data['Field']['Listing'],$search_address_field);
        } else {
            $address = Sanitize::getString($controller->params,$search_address_field);
            $lat = Sanitize::getFloat($controller->params,$this->jr_lat);
            $lon = Sanitize::getFloat($controller->params,$this->jr_lon);
        }

		/**
        * Plugin does different things for different controller methods
        */
		$jsGlobals = 'var geoSearchAutocomplete = "'.$this->search_autocomplete.'";';
		$jsGlobals .= 'var jr_lat = "'.$this->jr_lat.'";';
		$jsGlobals .= 'var jr_lon = "'.$this->jr_lon.'";';
		$jsGlobals .= 'var jr_country_def = "'.$this->country_def.'";';
		$jsGlobals .= 'var geoAddressObj = {};';

		foreach($this->address_fields AS $key=>$field){
			$jsGlobals .= "geoAddressObj.{$key} = '{$field}';";
		}


        switch($controller->name)
        {
            case 'com_content':

                $this->published = true;
                $controller->Listing->cacheCallbacks[] = 'plgAfterAfterFind';
                $controller->Listing->fields[] = "`Field`.{$this->jr_lat} AS `Geomaps.lat`";
                $controller->Listing->fields[] = "`Field`.{$this->jr_lon} AS `Geomaps.lon`";
                $controller->Listing->fields[] = "JreviewsCategory.marker_icon AS `Geomaps.icon`";
            break;

            case 'listings':

                switch($controller->action)
                {
                    // Load the geomaps js library
                    case 'create':  // Submit a new listing
                    case 'edit':    // Edit a listing

                        $this->published = true;

						$this->loadAssets();

						cmsFramework::addScript($controller->makeJS($jsGlobals),true);

						if($controller->action == 'edit')
                        {
                            $mapit_field = Sanitize::getString($controller->Config,'geomaps.mapit_field');

							if($mapit_field)
                            {
                                $response = "jQuery(document).ready(function() {
                                    jQuery('#{$mapit_field}','#jr_listingForm').after('<span id=\"gm_geocode\">
                                        <input class=\"jrButton\" type=\"button\" onclick=\"geomaps.mapPopupSimple();\" value=\"".__t("Map it",true)."\" />&nbsp;
                                        <input class=\"jrButton\" type=\"button\" onclick=\"geomaps.clearLatLng();\" value=\"".__t("Clear LatLng",true)."\" />
                                    </span>');
                                });";
                                cmsFramework::addScript($controller->makeJS($response),true);
                            }
                        }
                    break;

                    // Add geomaps buttons after form is loaded
                    case '_loadForm': // New listing - Loads submit listing form after category selection

                        $this->published = true;

						$mapit_field = Sanitize::getString($controller->Config,'geomaps.mapit_field');

						if($mapit_field)
                        {
                            $response = array();
                            $response[] = "
                                jQuery('#gm_geocode').remove();jQuery('#{$mapit_field}','#jr_listingForm').after('<span id=\"gm_geocode\"><input class=\"jrButton\" type=\"button\" onclick=\"geomaps.mapPopupSimple();\" value=\"".__t("Map it",true)."\" />&nbsp;<input class=\"jrButton\" type=\"button\" onclick=\"geomaps.clearLatLng();\" value=\"".__t("Clear LatLng",true)."\" /></span>');
                                jQuery('#gm_geocode').find(':input').removeAttr('disabled');
                            ";
                            $controller->afterAjaxResponse = $response;
                        }
                    break;
                    case '_save':
                        // Checks if
                        $isNew = Sanitize::getInt($controller->data['Listing'],'id',0) == 0 ? true : false;
                        if(Sanitize::getInt($controller->Config,'geomaps.autogeocode_new')
                            && $isNew
                            && isset($controller->data['Field'])
                            && (Sanitize::getFloat($controller->data['Field']['Listing'],$this->jr_lat,null)==null || Sanitize::getFloat($controller->data['Field']['Listing'],$this->jr_lon,null)==null)
                        )
                        {
                            // Build whole address from fields
                            $address = '';
                            foreach($this->address_fields AS $key=>$field)
                            {
                                ${$field} = Sanitize::getVar($controller->data['Field']['Listing'],$field,'');
                                if(${$field}!='')
                                {
                                    $address .= ' ' . ${$field};
                                }
                                elseif($field == 'section')
                                {
                                    $address .= " " .  Sanitize::getString($controller->data,'section');
                                }
                                elseif($field == 'parent_category')
                                {
                                    $address .= " " .  Sanitize::getString($controller->data,'parent_category');
                                }
                                elseif($field == 'category')
                                {
                                    $address .= " " . Sanitize::getString($controller->data,'category');
                                }
                            }

                            if($address != '' && !Sanitize::getVar($controller->data['Field']['Listing'],$this->jr_country,false) && $this->country_def != '')
                            {
                                $address .= ' ' . $this->country_def;
                            }

                            if($address != '')
                            {
                                // Geocode address
                                App::import('Component','geocoding');
                                $Geocoding = ClassRegistry::getClass('GeocodingComponent');
                                $Geocoding->Config = &$controller->Config;
                                $response = $Geocoding->geocode($address);
                                if($response['status']== 200)
                                {
                                    $controller->data['Field']['Listing'][$this->jr_lat] = $response['lat'];
                                    $controller->data['__raw']['Field']['Listing'][$this->jr_lat] = $response['lat'];
                                    $controller->data['Field']['Listing'][$this->jr_lon] = $response['lon'];
                                    $controller->data['__raw']['Field']['Listing'][$this->jr_lon] = $response['lon'];
                                }
                            }
                        }
                    break;
                }
            break;
            case 'admin_listings':
                switch($controller->action)
                {
                    case 'index':
                    case 'browse':
                    case 'moderation':

						$this->loadAssets();

                        App::import('Helper','html');

						$Html = new HtmlHelper();

						$Html->app = 'jreviews';

                        if($controller->action == 'moderation') {
                        ?>
                        <script type="text/javascript">
                        /* <![CDATA[ */
                        <?php echo $jsGlobals;?>
                        if(null==jQuery('body').data('geomaps')){
                            jQuery.getScript('<?php echo $this->locateScript('geomaps');?>',function(){jQuery('body').data('geomaps',1)});
                        }
                        /* ]]> */
                        </script>
                        <?php
                        }
                        else {
                            cmsFramework::addScript($controller->makeJS($jsGlobals));
                            $Html->js('geomaps');
                        }
                    break;
                    case 'edit':
                        $mapit_field = Sanitize::getString($controller->Config,'geomaps.mapit_field');
                        if($mapit_field)
                        {
                            $response = "jQuery('#{$mapit_field}').after('<span id=\"gm_geocode\"><input class=\"jrButton\" type=\"button\" onclick=\"geomaps.mapPopupSimple();\" value=\"".__t("Map it",true)."\" />&nbsp;<input class=\"jrButton\" type=\"button\" onclick=\"geomaps.clearLatLng();\" value=\"".__t("Clear LatLng",true)."\" /></span>');";
                            $controller->pluginResponse = $response;
                        }
                    break;
                }
            break;

			case 'module_geomaps':

				$this->loadAssets(); // load google maps api and geomaps

				break;

			case 'module_advanced_search':

				if($search_method == 'disabled')
                {
                    return;
                }

				if($controller->action == 'index')
				{
					$this->published = true; // Enable the callbacks for this controller/method

					$this->loadAssets(); // load google maps api and geomaps

					$module_id = Sanitize::getVar($controller->params,'module_id','');

					if(!empty($search_address_field))
					{
						$jsGlobals .= "var geoDistanceSearchAddress = '{$search_address_field}';";

						$jsGlobals .= "GeomapsOnload.push(function () {";

						$jsGlobals .= "var search_field = jQuery('#{$search_address_field}', '#JreviewsAdvSearch_{$module_id}').get(0);";

						$this->search_autocomplete and $jsGlobals .= "if(search_field) geomaps.initializeAutocomplete(search_field);";

						$jsGlobals .= "jQuery('.jrButton', '#JreviewsAdvSearch_{$module_id}').each(function(i, element) {
							var onclick_handler = element.onclick;
							element.onclick = null;
							jQuery(element).unbind('click').attr('onclick', '');
							jQuery(element).click(function(e) {
								geomaps.mapSearchAddress(this, onclick_handler, e);
								e.preventDefault();
							});
						});
						";
						$jsGlobals .= "});";
					}

					cmsFramework::addScript($controller->makeJS($jsGlobals),true);
				}
			break;
            // A search was performed, make distance the default ordering and copy the entered address to the search address field
            case 'search':

                if($search_method == 'disabled')
                {
                    return;
                }

				switch($controller->action)
				{
					case 'index':

						$this->published = true; // Enable the callbacks for this controller/method

						$this->loadAssets(); // load google maps api and geomaps

						if(!empty($search_address_field))
						{
							$jsGlobals .= "var geoDistanceSearchAddress = '{$search_address_field}';";

							$jsGlobals .= "GeomapsOnload.push(function () {";

							$this->search_autocomplete and $jsGlobals .= "

								jQuery('#jr_advancedSearchForm').delegate('#{$search_address_field}', 'click', function(e) {
									geomaps.initializeAutocomplete(this);
								});";

							$jsGlobals .= "jQuery('#jr_advancedSearchForm #jr-searchFields').delegate('.jrButton[type=submit]', 'click', function(e) {
									var onclick_handler = this.onclick;
									this.onclick = null;
									jQuery(this).unbind('click').attr('onclick', '');
									geomaps.mapSearchAddress(this, onclick_handler, e);
									e.preventDefault();
								});
								";
							$jsGlobals .= "});";
						}
						cmsFramework::addScript($controller->makeJS($jsGlobals),true);

					break;

					case '_process':

						if($address=='')
						{
							return;
						}
						$this->published = true;  // Enable the callbacks for this controller/method
						// Make distance the default ordering
						$controller->Config->list_order_default = 'distance';

						if($address != '' && in_array($search_method,array('address')))
						{
							$controller->data['Field']['Listing'][$search_address_field] = $address;
						}
					break;
				}
                break;

            // Display search results
            case 'categories':
                $controller->Listing->fields[] = "`Field`.{$this->jr_lat} AS `Geomaps.lat`";
                $controller->Listing->fields[] = "`Field`.{$this->jr_lon} AS `Geomaps.lon`";
                $controller->Listing->fields[] = "JreviewsCategory.marker_icon AS `Geomaps.icon`";

                $this->published = true; // Enable the callbacks for this controller/method

                if($search_method == 'disabled' || $lat == 0 || $lon == 0)
                {
                    return;
                }

                if($controller->action=='search')
                {
                    $radius = min(Sanitize::getFloat($controller->params,$this->radius_field,$default_radius),$this->max_radius);

                    if($search_method == 'disabled')
                    {
                        $this->published = false;
                        return;
                    }

                    if($lat != 0 && $lon != 0)
                    {
                        Configure::write('geomaps.enabled',true); // Used to show the Distance ordering in the jreviews.php helper in JReviews.
                        $center = array('lat'=>$lat,'lon'=>$lon);
                        // Send center coordinates to theme
                        $controller->set('GeomapsCenter',$center);
                        $sort = $controller->params['order'] = Sanitize::getString($controller->params,'order','distance');

                        // Clear address and coordinate field from parameters because it shouldn't be used on distance searches. Instead we use lat/lon via custom condition below
                        unset(
                            $controller->params[$search_address_field],
                            $controller->params['url'][$search_address_field],
                            $controller->params[$this->jr_lat],
                            $controller->params['url'][$this->jr_lat],
                            $controller->params[$this->jr_lon],
                            $controller->params['url'][$this->jr_lon]
                        );

                        $controller->passedArgs['url'] = preg_replace('/\/'.$search_address_field._PARAM_CHAR.'[\p{L},-\s0-9]+/ui','',$controller->passedArgs['url']);

                        $controller->passedArgs['url'] = preg_replace('/\/'.$search_address_field._PARAM_CHAR.'[a-z,0-9-\s]+/ui','',$controller->passedArgs['url']); // One above doesn't work well in all cases, but required for non-latin characters in address

                        $controller->passedArgs['url'] = preg_replace('/\/'.$this->jr_lat._PARAM_CHAR.'[\-a-z0-9\.\s]+/i','',$controller->passedArgs['url']);

                        $controller->passedArgs['url'] = preg_replace('/\/'.$this->jr_lon._PARAM_CHAR.'[\-a-z0-9\.\s]+/i','',$controller->passedArgs['url']);

                        // Create a square around the center to limite the number of rows processed in the zip code table
                        // http://www.free-zipcodes.com/
                        // http://www.mysqlconf.com/mysql2008/public/schedule/detail/347
                        $degreeDistance = $this->distance_in == 'mi' ? 69.172 : 40076/360;

                        $lat_range = $radius/$degreeDistance;
                        $lon_range = $radius/abs(cos($center['lat']*pi()/180)*$degreeDistance);

                        $min_lat = $center['lat'] - $lat_range;
                        $max_lat = $center['lat'] + $lat_range;
                        $min_lon = $center['lon'] - $lon_range;
                        $max_lon = $center['lon'] + $lon_range;
                        $squareArea = "`Field`.{$this->jr_lat} BETWEEN $min_lat AND $max_lat AND `Field`.{$this->jr_lon} BETWEEN $min_lon AND $max_lon";

                        // calculate the distance between two sets of longitude/latitude coordinates
                        // From http://www.mysqlconf.com/mysql2008/public/schedule/detail/347
                        if($this->distance_in == 'km')
                            {
                                $controller->Listing->fields['distance'] =
                                    "6371 * 2 * ASIN(SQRT(  POWER(SIN(({$center['lat']} - {$this->jr_lat}) * pi()/180 / 2), 2) +
                                    COS({$center['lat']} * pi()/180) *  COS({$this->jr_lat} * pi()/180) *  POWER(SIN(({$center['lon']} -{$this->jr_lon}) * pi()/180 / 2), 2)  )) AS `Geomaps.distance`";
                            }
                        if($this->distance_in == 'mi')
                            {
                                $controller->Listing->fields['distance'] =
                                    "3956 * 2 * ASIN(SQRT(  POWER(SIN(({$center['lat']} - {$this->jr_lat}) * pi()/180 / 2), 2) +
                                    COS({$center['lat']} * pi()/180) *  COS({$this->jr_lat} * pi()/180) *  POWER(SIN(({$center['lon']} -{$this->jr_lon}) * pi()/180 / 2), 2)  )) AS `Geomaps.distance`";
                            }

                        $controller->Listing->conditions[] = $squareArea;

                        if($sort=='distance') {
                            $controller->Listing->order[] = '`Geomaps.distance` ASC';
                        }

                        // Makes sure that only listings within given radius are shown because square limit might include further points
//                        $controller->Listing->having[] = '`Geomaps.distance` <= ' . (int) $radius;

                        // Override search theme suffix
                        $theme_suffix = Sanitize::getString($controller->Config,'geomaps.search_suffix');
                        if($theme_suffix != '')
                        {
                            $controller->viewSuffix = $theme_suffix;
                        }
                    }
                }
                break;
        }
    }

/************************************************************************
* CALLBACK METHODS
************************************************************************/
   /**
     * Calculates the listing count overriding the controllers calculation
     */
    function plgAfterAfterFind(&$model, $results)
    {
        if(empty($results)
        ||
            ($this->c->name == 'listings' || $this->c->name == 'module_listings') // Don't run in listings create/edit forms or listings module
        ) {
            return $results;
        }

        // Loop through results to inject distance group, marker icons and return as json object
        $json = $this->makeJsonObject($results);

		$this->c->set('json_data',$json);

        // Set additional assets to be loaded on the controller/action
        if($json) $this->loadAssets();

        // Send the full listing address to the theme file for use in the directions tool
        if($this->c->name == 'com_content' && $this->c->action == 'com_content_view')
        {
            $address = '';
            $listing = reset($results);
            foreach($this->address_fields AS $address_field)
            {
                if(isset($listing['Field']['pairs'][$address_field]) && isset($listing['Field']['pairs'][$address_field]['text'][0]))
                {
                    $address .= ' ' . $listing['Field']['pairs'][$address_field]['text'][0];
                }
                elseif($address_field == 'section' && isset($listing['Section']))
                {
                    $address .= " " . $listing['Section']['title'];
                }
                elseif($address_field == 'parent_category' && isset($listing['ParentCategory']))
                {
                    $address .= " " . $listing['ParentCategory']['title'];
                }
                elseif($address_field == 'category')
                {
                    $address .= " " . $listing['Category']['title'];
                }
            }

            $lat = isset($listing['Field']['pairs'][$this->geo_fields['lat']])
                    ? $listing['Field']['pairs'][$this->geo_fields['lat']]['value'][0]
                    : null
            ;
            $lon = isset($listing['Field']['pairs'][$this->geo_fields['lon']])
                    ? $listing['Field']['pairs'][$this->geo_fields['lon']]['value'][0]
                    : null
            ;

            if($address != ''
                && !empty($lat)
                && !empty($lon))
            {
                if(!Sanitize::getVar($listing['Field']['pairs'],$this->jr_country,false) && $this->country_def != '')
                {
                    $address .= ' ' . $this->country_def;
                }
                $results[key($results)]['Geomaps']['address'] = $address;
            }
            elseif(empty($lat) && empty($lon))
            {
                unset($results[key($results)]['Geomaps']);
            }
        }

        return $results;
    }

    /**
    * Executed before rendering the theme file.
    * All variables sent to theme are available in the $this->c->viewVars array and can be modified on the fly
    *
    */
    function plgBeforeRender()
    {
        // Need to convert coordinate fields to hidden fields if they are not shown on the page
        if($this->c->name == 'listings' && in_array($this->c->action,array('create','edit','_loadForm')) && isset($this->c->viewVars['listing_fields']))
        {
              foreach($this->c->viewVars['listing_fields'] AS $group=>$fields)
              {
                  if(isset($fields['Fields'][$this->jr_lat]) && !$this->c->Access->in_groups($fields['Fields'][$this->jr_lat]['properties']['access']))
                  {
                      $this->c->viewVars['listing_fields'][$group]['Fields'][$this->jr_lat]['type'] = 'hidden';
                      $this->c->viewVars['listing_fields'][$group]['Fields'][$this->jr_lat]['properties']['access'] = 'all';
                  }
                  if(isset($fields['Fields'][$this->jr_lon]) && !$this->c->Access->in_groups($fields['Fields'][$this->jr_lon]['properties']['access']))
                  {
                      $this->c->viewVars['listing_fields'][$group]['Fields'][$this->jr_lon]['type'] = 'hidden';
                      $this->c->viewVars['listing_fields'][$group]['Fields'][$this->jr_lon]['properties']['access'] = 'all';
                  }
              }
        }
    }

/************************************************************************
* AUXILIARY METHODS
************************************************************************/

    /**
    * Adds js and css assets to the assets array to be processed later on by the assets helper
    * Need to be set here instead of theme files for pages that can be cached
    *
    */
    function loadAssets()
    {
        switch($this->c->name)
        {
			case 'listings':

				if(!in_array($this->c->action,array('edit','create'))) return;

				$this->c->assets['css'][] = 'geomaps';

				$this->c->assets['js'][] = 'jquery';

				$this->c->assets['js'][] = 'geomaps';

				break;

			case 'module_advanced_search':

				$this->c->assets['css'][] = 'geomaps';

				$this->c->assets['js'][] = 'jquery';

				$this->c->assets['js'][] = 'geomaps';

				break;

			case 'module_geomaps':

				$this->c->assets['css'][] = 'theme';

				$this->c->assets['css'][] = 'geomaps';

				$this->c->assets['js'][] = 'jquery';

				$this->c->assets['js'][] = 'geomaps';

				if(Sanitize::getVar($this->c->params['module'],'clustering',true)) {
					$this->c->assets['js'][] = 'markerclusterer_packed';
				}

				break;

			case 'com_content':

				if($this->c->action == 'com_content_view')
                {
					$this->c->assets['css'][] = 'geomaps';

					$this->c->assets['js'][] = 'jquery';

					$this->c->assets['js'][] = 'geomaps';
                }

				break;

			case 'categories':

				if(in_array($this->c->action,array('compare'))) return;

				$this->c->assets['css'][] = 'geomaps';

				$this->c->assets['js'][] = 'jquery';

				$this->c->assets['js'][] = 'geomaps';

            break;
        }
    }

    /**
    * Shows the popup dialog with the location fields and map for user-end geocoding
    *
    */
    function geocodePopupByListingId($listing_id)
    {
        $fields = array();
        $conditions = array();

        $conditions[] = 'Listing.id = ' . $listing_id;

        if($this->c->cmsVersion != CMS_JOOMLA15)
        {
            $fields[] = 'ParentCategory.id AS `ParentCategory.cat_id`';
            $fields[] = 'ParentCategory.title AS `ParentCategory.title`';
            $conditions[] = "ParentCategory.id = Category.parent_id";
        }

        $listing = $this->c->Listing->findRow(array(
            'fields'=>$fields,
            'conditions'=>$conditions
        ));

        // Get all listing form fields
        $listing_fields = $this->c->Field->getFieldsArrayNew($listing['Criteria']['criteria_id'], 'listing', $listing);

        $this->c->set(array(
            'listing_fields'=>$this->geocodeFilterAddressFields($listing_fields),
            'listing'=>$listing
        ));

        return $this->c->render('geomaps','geocode_popup');
    }

    function showMap()
    {
        return $this->c->render('geomaps','map_popup');
    }

/**
* Removes all non-address fields from the custom fields array
*/
    function geocodeFilterAddressFields($listing_fields)
    {
        $new_fields = array();
        foreach($listing_fields AS $group=>$fields)
        {
            foreach($fields['Fields'] AS $name=>$field)
            {
                if(in_array($name,$this->address_fields) || in_array($name,$this->geo_fields))
                {
                    $new_fields[$group]['Fields'][$name] = $listing_fields[$group]['Fields'][$name];
                }
            }
        }
        return $new_fields;
    }

/**
* Used in map popup to update the listing's coordinates
*/
    function saveGeocodePopup($data)
    {
        App::import('Model','jreviews_content');
        $Field = ClassRegistry::getClass('JreviewsContentModel');
        $data['JreviewsContent'] = &$data['Field']['Listing'];
        if(!$Field->store($data))
        {
            return false;
        }
        return true;
    }

    function injectDistanceGroup($listing)
    {
        if(!isset($listing['Geomaps']['distance'])) return $listing;

        $field = array('jr_gm_distance'=>array (
                'id' => 99999,
                'group_id' => 'distance',
                'name' => 'jr_gm_distance',
                'type' => 'decimal',
                'title' => __t("Distance",true),
                'description' => '',
                'value' => array($listing['Geomaps']['distance']),
                'text' => array($listing['Geomaps']['distance']),
                'image' => array(),
                'properties' => array
                    (
                        'show_title' => 1,
                        'location' => 'content',
                        'contentview' => 0,
                        'listview' => Sanitize::getInt($this->c->Config,'geomaps.publish_distance',1),
                        'listsort' => 1,
                        'search' => 0,
                        'access' => implode(',',$this->c->Access->guests),
                        'access_view' => implode(',',$this->c->Access->guests),
                        'valid_regex' => '',
                        'allow_html' => 0,
                        'click2searchlink' => '',
                        'output_format' => '{FIELDTEXT} '.$this->distance_metric[$this->distance_in],
                        'click2search' => 0,
                        'click2add' => 0
                    ),
            )
        );

        $group = array('Geomaps'=>array(
            'Group'=>array(
                'group_id'=>'distance',
                'title'=>'Proximity Search',
                'name'=> 'Geomaps',
                'show_title'=>0),
            'Fields'=>$field
        ));

        !empty($listing['Field']['groups']) and $listing['Field']['groups'] = array_merge($group,$listing['Field']['groups']);
        !empty($listing['Field']['pairs']) and $listing['Field']['pairs'] = array_merge($field,$listing['Field']['pairs']);
        return $listing;
    }

    /**
    * Creates the json object used for map rendering
    *
    * @param array $results listings
    * @param mixed $fields  custom fields, required when using the GeoMaps module
    * @param mixed $options mapUI options to override globals when using GeoMaps module
    */
    function makeJsonObject(&$results, &$fields = array(), $options = array())
    {
        $www_base = array_shift(pathinfo(WWW_ROOT));

        // Required for thumbnail path
        $paths = array(
            S2Paths::get('jreviews', 'S2_VIEWS_OVERRIDES') . 'themes' . DS . $this->c->Config->template . DS . 'theme_images' . DS,
            S2Paths::get('jreviews', 'S2_VIEWS') . 'themes' . DS . $this->c->Config->template . DS . 'theme_images' . DS,
            S2Paths::get('jreviews', 'S2_VIEWS_OVERRIDES') . 'themes' . DS . 'default' . DS . 'theme_images' . DS,
            S2Paths::get('jreviews', 'S2_VIEWS') . 'themes' . DS . 'default' . DS . 'theme_images' . DS,
        );

        $path = fileExistsInPath(array('name'=>'','suffix'=>'','ext'=>''),$paths);

        App::import('Helper',array('html','routes','custom_fields','thumbnail'));

        $Html = new HtmlHelper();
        $Routes = new RoutesHelper();
        $CustomFields = new CustomFieldsHelper();
        $Thumbnail = new ThumbnailHelper();
        $Thumbnail->app = 'jreviews';
        $Thumbnail->name = $this->c->name;
        $Thumbnail->action = $this->c->action;
        $Routes->Config = $CustomFields->Config = $Thumbnail->Config = $this->c->Config;
        $Routes->Access = $CustomFields->Access = $Thumbnail->Access = $this->c->Access;
        $Routes->Html = $CustomFields->Html = $Thumbnail->Html = $Html;
        $CustomFields->viewTheme = $Thumbnail->viewTheme = &$this->c->viewTheme;
        $CustomFields->viewSuffix = &$this->c->viewSuffix;

        // Check format of results because we may need to re-format and add fields for Geomaps module
        $first = current($results);

		if(!isset($first['Listing']))
        {
            $results = $this->buildListingArray($results,$fields);
        }

		// PaidListings - remove unpaid info
        Configure::read('PaidListings') and PaidListingsComponent::processPaidData($results);

        $marker_icons = array();
        $infowindow_data = array();
        $i = 1;
        $map_counter = 0;

        $default_icon = $this->c->name == 'categories' ? 'numbered' : 'default';

        if(!empty($results))
        {
            $infowindow_fields = str_replace(" ","",Sanitize::getString($this->c->Config,'geomaps.infowindow_fields'));
            $infowindow_fields = $infowindow_fields != '' ? explode(",",$infowindow_fields) : array();

            foreach($results AS $key=>$result)
            {
                $results[$key] = $this->injectDistanceGroup($result);

                // Override global setting for map display in lists if at least one listing has map enabled
                // For it's listing type and has valid coordinates
                if($this->c->name == 'categories' && isset($result['ListingType']) && Sanitize::getBool($result['ListingType']['config'],'geomaps.enable_map_list',true)) {
                    if(isset($result['Geomaps']) && abs($result['Geomaps']['lat']) > 0 && abs($result['Geomaps']['lon']) > 0) {
                        $map_counter++;
                    }
                }

                // Add menu id if not already there
                if(!isset($result['Listing']['menu_id']))
                {
                    $results[$key]['Listing']['menu_id'] = $this->c->Menu->getCategory(array(
                        'cat_id'=>$result['Listing']['cat_id'],
                        'dir_id'=>$result['Directory']['dir_id'],
                        'section_id'=>isset($result['Listing']['section_id']) ? $result['Listing']['section_id'] : null,
                        'listing'=>$result['Listing']['listing_id']
                    ));
                }

                $listing_index = ($this->c->page-1)*$this->c->limit+$i++;

                // Process and add icon info
                $icon = isset($result['Geomaps']) ? json_decode($result['Geomaps']['icon'],true) : array();
                $results[$key]['Geomaps']['icon'] = '';
                $icon_name = $default_icon;
                if(!empty($icon))
                {
                    $foundIcon = false;
                    // Check if custom field assigned
                    if($icon['field'] != '' && substr($icon['field'],0,3)=='jr_')
                    {
                        if(isset($result['Field']['pairs'][$icon['field']]) && isset($result['Field']['pairs'][$icon['field']]['image'][0]))
                        {
                            $icon_name = substr($result['Field']['pairs'][$icon['field']]['image'][0],0,strpos($result['Field']['pairs'][$icon['field']]['image'][0],'.'));
                            $marker_icons[$icon_name] = $results[$key]['Geomaps']['icon'] = $result['Field']['pairs'][$icon['field']]['image'][0];
                            $foundIcon = true;
                        }
                    } elseif($icon['cat'] !='' && !$foundIcon) {
                        $icon_name = substr($icon['cat'],0,strpos($icon['cat'],'.'));
                        if($icon_name!='default') $marker_icons[$icon_name] = $results[$key]['Geomaps']['icon'] = $icon['cat'];
                    }
                };

                if(isset($result['Geomaps']) && $result['Geomaps']['lat'] != '' && $result['Geomaps']['lon'] != '' && $result['Geomaps']['lat']!=0 && $result['Geomaps']['lon'])
                {
                    # Create infowindow JSON object
                    // start with standard fields
                    $infowindow = array(
                        'id'=>$result['Listing']['listing_id'],
                        'url'=>str_replace(array($www_base,'&amp;'),array('','&'),$Routes->content('',$results[$key],array('return_url'=>true))),
                        'index'=>$listing_index,
                        'title'=>$result['Listing']['title'],
                        'image'=>str_replace($www_base,'',$Thumbnail->thumb($result, 0, array('tn_mode'=>$this->c->Config->list_thumb_mode,'location'=>'list','dimensions'=>array($this->c->Config->list_image_resize),'return_src'=>1))),
                        'featured'=>$result['Listing']['featured'],
                        'lat'=>(float)$result['Geomaps']['lat'],
                        'lon'=>(float)$result['Geomaps']['lon'],
                        'icon'=>$icon_name
                    );

					$listing_type_state = Sanitize::getInt($result['Criteria'],'state'); // If 1, ratings allowed

					$user_reviews_enabled = $this->c->Config->getOverride('user_reviews',$result['ListingType']['config']);
					$editor_reviews_enabled = $this->c->Config->getOverride('author_review',$result['ListingType']['config']);

					if($listing_type_state === 1 && ($user_reviews_enabled || $editor_reviews_enabled))
					{
                        $infowindow['rating_scale'] = $this->c->Config->rating_scale;

						if($user_reviews_enabled) {
							$infowindow['user_rating'] = $result['Review']['user_rating'];
							$infowindow['user_rating_count'] = $result['Review']['user_rating_count'];
						}

						if($editor_reviews_enabled) {
							$infowindow['editor_rating'] = $result['Review']['editor_rating'];
							$infowindow['editor_rating_count'] = $result['Review']['editor_rating_count'];
						}
					}

                    if(!empty($result['Field']['pairs']))
                    {
                        # Limit fields will included in the payload json object
                        $result['Field']['pairs'] = array_intersect_key($result['Field']['pairs'],array_flip($infowindow_fields));
                        foreach($result['Field']['pairs'] AS $name=>$fieldArray)
                        {
                            $infowindow['field'][$name] = $CustomFields->field($name,$result);
                        }
                    }
                    $infowindow_data['id'.$result['Listing']['listing_id']] = $infowindow;
                }
            }
        }

		// Stop geomaps.js from loading if there are no markers to display. Except for geomaps module
		if(empty($infowindow_data) && in_array($this->c->name, array('com_content','categories'))) {
			return false;
		}

        // Required to avoid overriding the listing types overrides!
        $this->c->Config->{'geomaps.enable_map_list'} = Sanitize::getBool($this->c->Config,'geomaps.enable_map_list',true) && $map_counter;

         $mapUI = array();
         $zoom = '';

         switch($this->c->name)
         {
             case 'categories':
                 $maptypes = Sanitize::getString($this->c->Config, 'geomaps.ui.maptype_list','buttons');//buttons|menu|none
                 $maptype_def = Sanitize::getString($this->c->Config, 'geomaps.ui.maptype_def_list','G_NORMAL_MAP');
                 $map = Sanitize::getBool($this->c->Config, 'geomaps.ui.map_list',1);
                 $hybrid = Sanitize::getBool($this->c->Config, 'geomaps.ui.hybrid_list',1);
                 $satellite = Sanitize::getBool($this->c->Config, 'geomaps.ui.satellite_list',1);
                 $terrain = Sanitize::getBool($this->c->Config, 'geomaps.ui.terrain_list',1);
                 $panzoom =  Sanitize::getBool($this->c->Config, 'geomaps.ui.panzoom_list',1);
                 $scale = Sanitize::getBool($this->c->Config, 'geomaps.ui.scale_list',0);
                 $scrollwheel = Sanitize::getBool($this->c->Config, 'geomaps.ui.scrollwheel_list',0);
                 $doubleclick = Sanitize::getBool($this->c->Config, 'geomaps.ui.doubleclick_list',1);
                 $mapUI['title']['trim'] = Sanitize::getVar($this->c->Config, 'geomaps.ui.trimtitle_list',0);
                 $mapUI['title']['trimchars'] = Sanitize::getVar($this->c->Config, 'geomaps.ui.trimtitle_chars',30);
             break;
             case 'com_content':
                 $maptypes = Sanitize::getString($this->c->Config, 'geomaps.ui.maptype_detail','buttons');//buttons|menu|none
                 $maptype_def = Sanitize::getString($this->c->Config, 'geomaps.ui.maptype_def_detail','G_NORMAL_MAP');
                 $map = Sanitize::getBool($this->c->Config, 'geomaps.ui.map_detail',1);
                 $hybrid = Sanitize::getBool($this->c->Config, 'geomaps.ui.hybrid_detail',1);
                 $satellite = Sanitize::getBool($this->c->Config, 'geomaps.ui.satellite_detail',1);
                 $terrain = Sanitize::getBool($this->c->Config, 'geomaps.ui.terrain_detail',1);
                 $panzoom =  Sanitize::getBool($this->c->Config, 'geomaps.ui.panzoom_detail',1);
                 $scale = Sanitize::getBool($this->c->Config, 'geomaps.ui.scale_detail',0);
                 $scrollwheel = Sanitize::getBool($this->c->Config, 'geomaps.ui.scrollwheel_detail',0);
                 $doubleclick = Sanitize::getBool($this->c->Config, 'geomaps.ui.doubleclick_detail',1);
                 $zoom = Sanitize::getInt($this->c->Config, 'geomaps.ui.zoom_detail','');
                 $mapUI['title']['trim'] = Sanitize::getVar($this->c->Config, 'geomaps.ui.trimtitle_detail',0);
                 $mapUI['title']['trimchars'] = Sanitize::getVar($this->c->Config, 'geomaps.ui.trimtitle_chars',30);
             break;
             case 'module_geomaps':
                 $maptypes = Sanitize::getString($options,'ui_maptype',2) == '2' ? Sanitize::getString($this->c->Config, 'geomaps.ui.maptype_module','buttons') : Sanitize::getString($options,'ui_maptype'); //buttons|menu|none
                 $maptype_def = Sanitize::getString($options,'ui_maptype_def',2) == '2' ? Sanitize::getString($this->c->Config, 'geomaps.ui.maptype_def_module','G_NORMAL_MAP') : Sanitize::getString($options, 'ui_maptype_def','G_NORMAL_MAP');
                 $map = Sanitize::getInt($options,'ui_map',2) == '2' ? Sanitize::getBool($this->c->Config, 'geomaps.ui.map_module',1) : Sanitize::getBool($options,'ui_map');
                 $hybrid = Sanitize::getInt($options,'ui_hybrid',2) == '2' ? Sanitize::getBool($this->c->Config, 'geomaps.ui.hybrid_module',1) : Sanitize::getBool($options,'ui_hybrid');
                 $satellite = Sanitize::getInt($options,'ui_satellite',2) == '2' ? Sanitize::getBool($this->c->Config, 'geomaps.ui.satellite_module',1) : Sanitize::getBool($options,'ui_satellite');
                 $terrain = Sanitize::getInt($options,'ui_terrain',2) == '2' ? Sanitize::getBool($this->c->Config, 'geomaps.ui.terrain_module',1) : Sanitize::getBool($options,'ui_terrain');
                 $panzoom =  Sanitize::getInt($options,'ui_panzoom',2) == '2' ? Sanitize::getBool($this->c->Config, 'geomaps.ui.panzoom_module',1) : Sanitize::getBool($options,'ui_panzoom');
                 $scale = Sanitize::getInt($options,'ui_scale',2) == '2' ? Sanitize::getBool($this->c->Config, 'geomaps.ui.scale_module',0) : Sanitize::getBool($options,'ui_scale');
                 $scrollwheel = Sanitize::getInt($options,'ui_scrollwheel',2) == '2' ? Sanitize::getBool($this->c->Config, 'geomaps.ui.scrollwheel_module',0) : Sanitize::getBool($options,'ui_scrollwheel');
                 $doubleclick = Sanitize::getInt($options,'ui_doubleclick',2) == '2' ? Sanitize::getBool($this->c->Config, 'geomaps.ui.doubleclick_module',1) : Sanitize::getBool($options,'ui_doubleclick');
                 $mapUI['title']['trim'] = Sanitize::getInt($options,'ui_trimtitle_module',2) == '2' ? Sanitize::getBool($this->c->Config, 'geomaps.ui.trimtitle_module',30) : Sanitize::getBool($options,'ui_trimtitle_module');
                 $mapUI['title']['trimchars'] = Sanitize::getInt($options,'ui_trimtitle_chars',2) == '2' ? Sanitize::getInt($this->c->Config, 'geomaps.ui.trimtitle_chars',30) : Sanitize::getInt($options,'ui_trimtitle_chars');
                 if(Sanitize::getString($options,'detail_view',1))
                 {
                    $zoom = Sanitize::getInt($this->c->Config, 'geomaps.ui.zoom_detail','');
                 }
             break;

         }

         switch($maptypes)
         {
             case 'buttons':
                $mapUI['controls']['maptypecontrol'] = true;
                $mapUI['controls']['menumaptypecontrol'] = false;
             break;
             case 'menu':
                $mapUI['controls']['maptypecontrol'] = false;
                $mapUI['controls']['menumaptypecontrol'] = true;
             break;
             default:
                $mapUI['controls']['maptypecontrol'] = false;
                $mapUI['controls']['menumaptypecontrol'] = false;
         }

         $mapUI['maptypes']['def'] = $maptype_def;
         $mapUI['maptypes']['map'] = $map;
         $mapUI['maptypes']['hybrid'] = $hybrid;
         $mapUI['maptypes']['satellite'] = $satellite;
         $mapUI['maptypes']['terrain'] = $terrain;
         if($panzoom)
         {
                 $mapUI['controls']['smallzoomcontrol3d'] = true;
                 $mapUI['controls']['largemapcontrol3d'] = true;
         } else {
                 $mapUI['controls']['smallzoomcontrol3d'] = false;
                 $mapUI['controls']['largemapcontrol3d'] = false;
         }

         $mapUI['controls']['scalecontrol'] = $scale;
         $mapUI['zoom']['scrollwheel'] = $scrollwheel;
         $mapUI['zoom']['doubleclick'] = $doubleclick;
         $mapUI['zoom']['start'] = $zoom;

         $mapUI['anchor']['x'] = Sanitize::getVar($this->c->Config, 'geomaps.infowindow_x',0);
         $mapUI['anchor']['y'] = Sanitize::getVar($this->c->Config, 'geomaps.infowindow_y',0);

        unset($Html, $Routes, $CustomFields, $Thumbnail);
        return json_encode(array('count'=>count($infowindow_data),'mapUI'=>$mapUI,'infowindow'=>Sanitize::getString($this->c->Config,'geomaps.infowindow','_google'),'icons'=>$this->processIcons($marker_icons),'payload'=>$infowindow_data));
    }

    function buildListingArray($results,&$fields)
    {
        $listings = array();

        foreach($results AS $result)
        {
            $id = $result['Listing.listing_id'];

			$listings[$id]['Criteria']['state'] = $result['Criteria']['state'];

			$listings[$id]['ListingType']['config'] = $result['ListingType']['config'];

			foreach($result AS $key=>$value)
            {
                $parts = explode('.',$key);

				if(count($parts)>1) // Joomfish adds keys without table aliases used by JReviews
                {
                    if($parts[0]!='Field')
                    {
                        $listings[$id][$parts[0]][$parts[1]] = $value;

                    } else { // Process custom fields
                        $value = str_replace('*','',$value);
                        if($value!='' && isset($fields[$parts[1]]['options'][$value]))
                        {
                            $listings[$id]['Field']['pairs'][$parts[1]] = $fields[$parts[1]];
                            $listings[$id]['Field']['pairs'][$parts[1]]['value'][0] = $fields[$parts[1]]['options'][$value]['value'];
                            $listings[$id]['Field']['pairs'][$parts[1]]['text'][0] = $fields[$parts[1]]['options'][$value]['text'];
                            $listings[$id]['Field']['pairs'][$parts[1]]['image'][0] = $fields[$parts[1]]['options'][$value]['image'];
                            $listings[$id]['Field']['pairs'][$parts[1]]['properties']['location'] = 'listing';
                            unset($listings[$id]['Field']['pairs'][$parts[1]]['options']);
                        } elseif($value!='')
                        {
                            $listings[$id]['Field']['pairs'][$parts[1]] = $fields[$parts[1]];
                            $listings[$id]['Field']['pairs'][$parts[1]]['text'][0] = $value;
                            $listings[$id]['Field']['pairs'][$parts[1]]['value'][0] = $value;
                            $listings[$id]['Field']['pairs'][$parts[1]]['properties']['location'] = 'listing';
                            unset($listings[$id]['Field']['pairs'][$parts[1]]['options']);
                        }
                    }
                }
            }

            // Process images, particularly from module controller  - must be after code above to overwrite the images key
            if($result['Listing.images'])
            {
                $images = explode("\n",$result['Listing.images']);
                $listings[$id]['Listing']['images'] = array();
                if(!empty($images[0]))
                {
                    $image_parts = explode("|",$images[0]); // Only first image included in the json object
                    if($image_parts[0]!='') {
                        $listings[$id]['Listing']['images'][] = array(
                            'path'=>trim($image_parts[0])
                        );
                    }
                }
            }

        }
        unset($fields);
        return $listings;
    }

    function processIcons($marker_icons)
    {
        $www_base = array_shift(pathinfo(WWW_ROOT));
        $icons = array();
        if(empty($marker_icons)) return $icons;

        $marker_base_url = WWW_ROOT. ltrim($this->c->Config->{'geomaps.marker_path'},_DS) . _DS;
        $marker_base_path = PATH_ROOT . str_replace(_DS,DS,ltrim($this->c->Config->{'geomaps.marker_path'},_DS)) . DS;

        foreach($marker_icons AS $name=>$icon)
        {
            if($icon != '' && file_exists($marker_base_path.$icon))
            {
                $dimensions = getimagesize($marker_base_path.$icon);
                $icon_dimensions = $icon_dimensions_featured = array($dimensions[0],$dimensions[1]);
                $file_info = pathinfo($icon);
                if(isset($file_info['extension']) && $file_info['filename']!='' && $file_info['extension']!='')
                {
                    $icon_hover_file = $file_info['filename'].'_hover'.'.'.$file_info['extension'];
                    $icon_featured_file = $file_info['filename'].'_featured'.'.'.$file_info['extension'];
                    $icon_featured_hover_file = $file_info['filename'].'_featured_hover'.'.'.$file_info['extension'];
                    $icons[$file_info['filename']] = array(
                        'type'=>'custom',
                        'url'=>str_replace($www_base,'',$marker_base_url.$icon),
                        'size'=>$icon_dimensions
                    );
                    if(file_exists($marker_base_path.$icon_hover_file))
                    {
                        $icons[$file_info['filename'].'_hover'] = array(
                            'type'=>'custom',
                            'url'=>$marker_base_url.$icon_hover_file,
                            'size'=>$icon_dimensions
                        );
                    }
                    if(file_exists($marker_base_path.$icon_featured_file))
                    {
                        $dimensions_featured = getimagesize($marker_base_path.$icon_featured_file);
                        $icon_dimensions_featured = array($dimensions_featured[0],$dimensions_featured[1]);
                        $icons[$file_info['filename'].'_featured'] = array(
                            'type'=>'custom',
                            'url'=>$marker_base_url.$icon_featured_file,
                            'size'=>$icon_dimensions_featured
                        );
                    }
                    if(file_exists($marker_base_path.$icon_featured_hover_file))
                    {
                        $icons[$file_info['filename'].'_featured_hover'] = array(
                            'type'=>'custom',
                            'url'=>$marker_base_url.$icon_featured_hover_file,
                            'size'=>$icon_dimensions_featured
                        );
                    }
                }
            }
        }
        return $icons;
    }
}
