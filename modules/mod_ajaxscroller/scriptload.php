<?php
/*
* @package		AJAX Scroller
* @copyright	Copyright (C) 2008-2011 Emir Sakic, http://www.sakic.net. All rights reserved.
* @license		GNU/GPL, see LICENSE.TXT
*
* This program is free software; you can redistribute it and/or modify it
* under the terms of the GNU General Public License as published by the
* Free Software Foundation; either version 2 of the License, or
* (at your option) any later version.
* 
* This header must not be removed. Additional contributions/changes
* may be added to this header as long as no information is deleted.
*/

/**
* Send gzipped JS and CSS
* params: file array, gzip
*/

define( '_JEXEC', 1 );

define( 'DS', DIRECTORY_SEPARATOR );

define('JPATH_BASE', str_replace(DS.'modules'.DS.'mod_ajaxscroller', '', dirname(__FILE__)) );

require_once ( JPATH_BASE .DS.'includes'.DS.'defines.php' );
require_once ( JPATH_BASE .DS.'includes'.DS.'framework.php' );

// Instantiate the application.
$app = JFactory::getApplication('site');

// Initialise the application.
$app->initialise();

$mosConfig_absolute_path = str_replace( '\\', '/', realpath(JPATH_BASE) );
$mosConfig_live_site = str_replace( '/modules/mod_ajaxscroller', '', substr_replace(JURI::root(), '', -1, 1) );

@initGzip();

$files = isset($_GET['file']) ? $_GET['file'] : array();

if( empty( $files ) ) {
	header("HTTP/1.0 400 Bad Request");
  	echo 'Bad request';
  	exit;
}
$countFiles = sizeof($files);
$newest_mdate = 0;

for( $i = 0; $i < $countFiles; $i++ ) {
	$file = $files[$i];

	$filename = basename($file);
	if (stristr($filename, '.css')) {
		$base_dir = dirname( __FILE__ ).DS.'assets'.DS.'css';
	} else {
		$base_dir = dirname( __FILE__ ).DS.'assets'.DS.'js';
	}
	$dir = realpath( $base_dir );
	$file = $dir . DS . $filename;

	if( !file_exists( $file ) || !stristr( $dir, $base_dir )) {
		if( $countFiles == 1 ) {
		    header("HTTP/1.0 404 Not Found");
		    echo $file.' Not Found';
		    exit;
		}
		continue;
	}
	$newest_mdate = max( filemtime( $file ), $newest_mdate );
}

// This function quits the page load if the browser has a cached version of the requested script.
// It then returns a 304 Not Modified header
http_conditionalRequest( $newest_mdate );

// here we need to send the script or stylesheet
$processed_files = 0;
for( $i = 0; $i < $countFiles; $i++ ) {
	$file = $files[$i];

	$filename = basename($file);
	if (stristr($filename, '.css')) {
		$base_dir = dirname( __FILE__ ).DS.'assets'.DS.'css';
	} else {
		$base_dir = dirname( __FILE__ ).DS.'assets'.DS.'js';
	}
	$dir = realpath( $base_dir );
	$file = $dir . DS . $filename;
	
	if( !file_exists( $file ) || !stristr( $dir, $base_dir ) || !is_readable( $file ) ) {
		continue;
	}
	$processed_files++;
	$fileinfo = pathinfo( $file );
	
	switch ( $fileinfo['extension']) {
		case 'css':
			$mime_type = 'text/css';
			header( 'Content-Type: '.$mime_type.';');
			$css = implode( '', file( $file ));

			$str_css = preg_replace("/url\((.+?)\)/ie","process_link('\\1')", $css);
			echo $str_css;

			break;

		case 'js':
			$mime_type = 'application/x-javascript';
			header( 'Content-Type: '.$mime_type.';');

			readfile( $file );

			break;

		default:
			continue;

	}
	echo "\n\n";
}

if( $processed_files == 0 ) {
	if( !file_exists( $file ) ) {
	    header("HTTP/1.0 404 Not Found");
	    echo $file.' Not Found';
	    exit;
	}
	if( !is_readable( $file ) ) {
	    header("HTTP/1.0 500 Internal Server Error");
	    echo "Could not read ".basename($file)." - bad permissions?";
	  	exit;
	}
}

// Tell the user agent to cache this script/stylesheet for an hour
$age = 3600;
header( 'Expires: '.gmdate( 'D, d M Y H:i:s', time()+ $age ) . ' GMT' );
header( 'Last-Modified: ' . gmdate( 'D, d M Y H:i:s', @filemtime( $file ) ) . ' GMT' );
header( 'Cache-Control: public, max-age='.$age.', must-revalidate, post-check=0, pre-check=0' );
header( 'Pragma: public' );

doGzip();

exit;

/**
* This function fixes the URLs used in the CSS file
* This is necessary, because this file is (usually) located somewhere else than the CSS file! That makes
* relative URL references point to wrong directories - so we need to fix that!
*/
function process_link( $ref ) {
	global $mosConfig_absolute_path, $mosConfig_live_site;

	$ref = str_replace( "'", '', stripslashes( $ref ));
	$ref = trim( str_replace( '"', '', $ref) );
	// Absolute References don't need to be fixed
	if( substr( $ref, 0, 4 ) == 'http' ) {
		return 'url( "'. $ref.'" )';
	}
	
	$ref = str_replace( '..', 'assets', $ref );
	$ref = realpath(dirname( __FILE__ ).'/'.$ref);
	$ref = str_replace( "\\", '/', $ref );
	$ref = str_replace( $mosConfig_absolute_path, $mosConfig_live_site, $ref);
	
	return 'url( "'. $ref .'" )';

}
/**
 * Checks and sets HTTP headers for conditional HTTP requests
 * Borrowed from DokuWiki (/lib/exe/fetch.php)
 * @author Simon Willison <swillison@gmail.com>
 * @link   http://simon.incutio.com/archive/2003/04/23/conditionalGet
 */
function http_conditionalRequest($timestamp){
    // A PHP implementation of conditional get, see
    //   http://fishbowl.pastiche.org/archives/001132.html
    $last_modified = gmdate( 'D, d M Y H:i:s', $timestamp ) . ' GMT';
    $etag = '"'.md5($last_modified).'"';
    // Send the headers
    header("Last-Modified: $last_modified");
    header("ETag: $etag");
    // See if the client has provided the required headers
    $if_modified_since = isset($_SERVER['HTTP_IF_MODIFIED_SINCE']) ?
        stripslashes($_SERVER['HTTP_IF_MODIFIED_SINCE']) :
        false;
    $if_none_match = isset($_SERVER['HTTP_IF_NONE_MATCH']) ?
        stripslashes($_SERVER['HTTP_IF_NONE_MATCH']) :
        false;
    if (!$if_modified_since && !$if_none_match) {
        return;
    }
    // At least one of the headers is there - check them
    if ($if_none_match && $if_none_match != $etag) {
        return; // etag is there but doesn't match
    }
    if ($if_modified_since && $if_modified_since != $last_modified) {
        return; // if-modified-since is there but doesn't match
    }
    // Nothing has changed since their last request - serve a 304 and exit
    header('HTTP/1.0 304 Not Modified');
    exit;
}

function initGzip() {
	global $do_gzip_compress;

	// attempt to disable session.use_trans_sid
	ini_set('session.use_trans_sid', false);

	$do_gzip_compress = FALSE;
	$phpver = phpversion();
	$useragent = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '';
	$canZip = isset($_SERVER['HTTP_ACCEPT_ENCODING']) ? $_SERVER['HTTP_ACCEPT_ENCODING'] : '';

	if ( $phpver >= '4.0.4pl1' &&
			( strpos($useragent,'compatible') !== false ||
				strpos($useragent,'Gecko') !== false
			)
		) {
		// Check for gzip header or northon internet securities
		if ( isset($_SERVER['HTTP_ACCEPT_ENCODING']) ) {
			$encodings = explode(',', strtolower($_SERVER['HTTP_ACCEPT_ENCODING']));
		}
		if ( (in_array('gzip', $encodings) || isset( $_SERVER['---------------']) ) && extension_loaded('zlib') && function_exists('ob_gzhandler') && !ini_get('zlib.output_compression') && !ini_get('session.use_trans_sid') ) {
			// You cannot specify additional output handlers if
			// zlib.output_compression is activated here
			ob_start( 'ob_gzhandler' );
			return;
		}
	} else if ( $phpver > '4.0' ) {
		if ( strpos($canZip,'gzip') !== false ) {
			if (extension_loaded( 'zlib' )) {
				$do_gzip_compress = TRUE;
				ob_start();
				ob_implicit_flush(0);

				header( 'Content-Encoding: gzip' );
				return;
			}
		}
	}
	ob_start();
}

function doGzip() {
	global $do_gzip_compress;
	if ( $do_gzip_compress ) {
		$gzip_contents = ob_get_contents();
		ob_end_clean();

		$gzip_size = strlen($gzip_contents);
		$gzip_crc = crc32($gzip_contents);

		$gzip_contents = gzcompress($gzip_contents, 9);
		$gzip_contents = substr($gzip_contents, 0, strlen($gzip_contents) - 4);

		echo "\x1f\x8b\x08\x00\x00\x00\x00\x00";
		echo $gzip_contents;
		echo pack('V', $gzip_crc);
		echo pack('V', $gzip_size);
	} else {
		ob_end_flush();
	}
}
?>