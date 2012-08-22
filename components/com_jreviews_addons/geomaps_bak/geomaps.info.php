<?php
defined( 'MVC_FRAMEWORK') or die( 'Direct Access to this location is not allowed.' );

$_addon = array(
    'name'=>'GeoMaps',
    'description'=>'Adds distance search and mapping functionality to JReviews',
    'version'=>'0.7.48',                    
    'min_app_version_required'=>'2.3.11.202',  
    'type'=>'Commercial',
    'is_beta'=>0
);
 
echo json_encode($_addon);