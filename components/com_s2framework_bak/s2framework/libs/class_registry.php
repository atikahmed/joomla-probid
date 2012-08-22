<?php
/**
 * jReviews - Reviews Extension
 * Copyright (C) 2006-2009 Alejandro Schmeichler
 * This is not free software, do not distribute it.
 * For licencing information visit http://www.reviewsforjoomla.com
 * or contact sales@reviewsforjoomla.com
**/

(defined('MVC_FRAMEWORK') || defined('JPATH_BASE')) or die( 'Direct Access to this location is not allowed.' );

class ClassRegistry 
{    
    private $__classes = array();
    private $__objects = array();
        
    public static function &getInstance() 
    {
        static $instance = array();

        if (!isset($instance[0]) || !$instance[0]) {
            $instance[0] = new ClassRegistry();
        }
        return $instance[0];
    }  
    
   
    public static function getClass($class, $params = null)
    {
        $_this = ClassRegistry::getInstance();
        
        # rename admin classes
        if(strstr($class,MVC_ADMIN)) {
            $class = str_replace(MVC_ADMIN._DS,'',$class);
        }

        if(!class_exists($class)) {
            return false;
        }
        elseif(!array_key_exists($class, $_this->__classes)) 
        {
            if($params) {
                $_this->__classes[$class] = new $class($params);
            } else {
                $_this->__classes[$class] = new $class();
            }
        }
        
        return $_this->__classes[$class];   
    }     

    public static function setObject() 
    {
        $params = func_get_args();
        $_this = ClassRegistry::getInstance();
        if(is_array($params[0])) {
            list($key, $namespace) = $params;
            foreach($key AS $key=>$object) {
                $namespace == '' ? 
                    $_this->__objects[$key] = $object
                    :
                    $_this->__objects[$namespace][$key] = $object
                ;                
            }
        }
        else {
            if(count($params) == 3) {
                list($key, $object, $namespace) = $params;
            }
            else {
                list($key, $object) = $params;
                $namespace = '';
            }
            $namespace == '' ? 
                $_this->__objects[$key] = $object
                :
                $_this->__objects[$namespace][$key] = $object
            ;             
        }
    }  
        
    public static function getObject($key, $namespace = '') 
    {
        $_this = ClassRegistry::getInstance();
        if($namespace == '')
        {
            return !empty($_this->__objects[$key]) ? $_this->__objects[$key] : false;                
        }
        else {
            return !empty($_this->__objects[$namespace][$key]) ? $_this->__objects[$namespace][$key] : false;                
        }
    }  
    
    public static function flush() 
    {
        $_this = ClassRegistry::getInstance();
        $_this->__classes = array();
        $_this->__objects = array();
    }
}
