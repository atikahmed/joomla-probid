<?php
/**
 * GeoMaps Addon for JReviews
 * Copyright (C) 2006-2009 ClickFWD LLC
 * This is not free software, do not distribute it.
 * For licencing information visit http://www.reviewsforjoomla.com
 * or contact sales@reviewsforjoomla.com
**/

defined( 'MVC_FRAMEWORK') or die( 'Direct Access to this location is not allowed.' );

class AdminGeomapsInstallController extends MyController {
    
    var $autoLayout = false;
    var $autoRender = false;
    
    function install()
    {    
        $db = cmsFramework::getDB();
     
        // Create the marker_icon column in the JReviews categories table 
        $cat_table_cols = current($db->getTableFields('#__jreviews_categories'));
        if(!in_array('marker_icon',array_keys($cat_table_cols)))
        {
            $query = "ALTER TABLE `#__jreviews_categories` ADD `marker_icon` VARCHAR(150) AFTER `tmpl_suffix`;";
            $db->setQuery($query);
            $db->query();
        }

        // Install GeoMaps module
        $query = "SELECT count(*) FROM #__modules WHERE module = 'mod_jreviews_geomaps'";
        $db->setQuery($query);
        $count = $db->loadResult();
        if(!$count)
        {
            // create module entry in database
            $query = "
                INSERT INTO #__modules 
                    (`title`, `module`, `published`, `params`) 
                VALUES 
                    ('Jreviews GeoMaps Module', 'mod_jreviews_geomaps', 0, '');";            
            $db->setQuery($query);
            $db->query();
        }

        if($this->cmsVersion == CMS_JOOMLA16) {
            // Need to add entry to the extensions table
            $query = "SELECT count(*) FROM #__extensions WHERE name = 'mod_jreviews_geomaps'";
            $db->setQuery($query);
            
            if(!$db->loadResult())
            {
                $query = "
                    INSERT INTO #__extensions
                        (`name`,`type`,`element`,`client_id`,`enabled`,`access`,`protected`)
                    VALUES
                        ('mod_jreviews_geomaps','module','mod_jreviews_geomaps',0,1,1,0)
                ";
                $db->setQuery($query);
                $db->query();                    
            }
        }
                    
        // First extract packages
        $package = PATH_ROOT . 'components' . DS . 'com_jreviews_addons' . DS . 'geomaps' . DS . 'packages' . DS . 'mod_jreviews_geomaps.zip';
        $target = PATH_ROOT . 'modules';
        if($this->_extract($package, $target))
        {
            @copy(PATH_ROOT . 'modules' . DS . 'mod_jreviews_geomaps' . DS . 'en-GB.mod_jreviews_geomaps.ini',
                PATH_ROOT . 'language' . DS . 'en-GB' . DS . 'en-GB.mod_jreviews_geomaps.ini'
            );
            
            return '<div style="color:green;">GeoMaps module was successfully installed/updated. You will find it in modules manager.</div>';
        } else {
            return '<div style="color:red;">There was a problem installing/updating the GeoMaps module.</div>';
        }        
    }
    
    function _extract($package, $target)
    {
            // First extract files
            jimport( 'joomla.filesystem.file' );
            jimport( 'joomla.filesystem.folder' );
            jimport( 'joomla.filesystem.archive' );
            jimport( 'joomla.filesystem.path' );
    
            $adapter = & JArchive::getAdapter('zip');
            $result = $adapter->extract ( $package, $target );
            
            if(!is_dir($target)) {
                
                require_once ( PATH_ROOT . 'administrator' . DS . 'includes' . DS . 'pcl' . DS . 'pclzip.lib.php');
                require_once (PATH_ROOT . 'administrator' . DS . 'includes' . DS . 'pcl' . DS . 'pclerror.lib.php');
                
                $extract = new PclZip ( $package );
                
                if ((substr ( PHP_OS, 0, 3 ) == 'WIN')) {
                    if(!defined('OS_WINDOWS')) define('OS_WINDOWS',1);
                } else {
                    if(!defined('OS_WINDOWS')) define('OS_WINDOWS',0);
                }
                        
                $result = $extract->extract ( PCLZIP_OPT_PATH, $target );
                
            } 
            return $result;             
    }
}