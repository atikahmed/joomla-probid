<?php
 /**
  * @version   $Id: Wordpress.php 52611 2012-04-16 15:36:09Z steph $
  * @author    RocketTheme http://www.rockettheme.com
  * @copyright Copyright (C) 2007 - ${copyright_year} RocketTheme, LLC
  * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
  */

global $wp_did_header;

/**
 *
 */
class RokCommon_Header_Wordpress implements RokCommon_Header_Interface
{

    /**
     *
     */
    public function __construct()
    {

    }
    /**
     * @param $file
     */
    public function addScript($file)
    {
        global $wp_scripts;

        $path_parts = pathinfo($file);

        //check if its a file or handle
        if($path_parts['extension'] == 'js'){

            //differentiate between the plugin files and the widget files
            if ((strpos($file, 'widget')) === false) {
                $handle = 'rok_'.str_replace('.', '_', basename($file));
            }
            else {
                $handle = 'rok_widget_'.str_replace('.', '_', basename($file));
            }

            //check if wordpress head has run
            if ( ! did_action( 'wp_head' ) ) {

                //check if its already registered or queued
                wp_register_script($handle, $file);
                wp_enqueue_script($handle);

            } else {
                //wordpress head already ran so...
                $file_root = str_replace($wp_scripts->base_url, ABSPATH, $file);
                if(file_exists($file_root)) {
                    echo "<script type='text/javascript' src='$file'></script>\n";
                }
            }

        } else {
            //might be a handle
            wp_enqueue_script($file);
        }
    }

    /**
     * @param $text
     */
    public function addInlineScript($text)
    {
        echo "<script type=\"text/javascript\">\n".(string)$text."\n</script>";
    }

    /**
     * @param $file
     */
    public function addStyle($file)
    {
        global $wp_styles;

        $path_parts = pathinfo($file);

        //check if its a file or handle
        if($path_parts['extension'] == 'css'){

        //differentiate between the plugin files and the widget files
        if ((strpos($file, 'widget')) === false) {
            $handle = 'rok_'.str_replace('.', '_', basename($file));
        }
        else {
            $handle = 'rok_widget_'.str_replace('.', '_', basename($file));
        }

            //check if wordpress head has run
            if ( ! did_action( 'wp_head' ) ) {

                wp_register_style($handle, $file);
                wp_enqueue_style($handle);

            } else {
                //wordpress head already ran so...
                $file_root = str_replace($wp_styles->base_url, ABSPATH, $file);
                if(file_exists($file_root)) {
                    echo "<link rel='stylesheet' id='$handle' href='$file' type='text/css' media='all' />\n";
                }
            }

        } else {
            //might be a handle
            wp_enqueue_style($file);
        }
    }


    /**
     * @param $text
     */
    public function addInlineStyle($text)
    {
        echo "<style type=\"text/css\">\n".(string)$text."\n</style>";
    }
}

