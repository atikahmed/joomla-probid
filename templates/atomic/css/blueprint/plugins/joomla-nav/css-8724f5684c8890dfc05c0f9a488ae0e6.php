<?php 
ob_start ("ob_gzhandler");
header("Content-type: text/css; charset= UTF-8");
header("Cache-Control: must-revalidate");
$expires_time = 1440;
$offset = 60 * $expires_time ;
$ExpStr = "Expires: " . 
gmdate("D, d M Y H:i:s",
time() + $offset) . " GMT";
header($ExpStr);
                ?>

/*** screen.css ***/

.joomla-nav {background-color:#c3d9ff;border-top:1px solid #b3c9e4;border-bottom:1px solid #b3c9ef;height:1%; margin:0 0 1.5em 0;min-height:auto;overflow:auto;padding:0.67em 0 0.67em 0;}.joomla-nav li {float:left;line-height:1.5;list-style-type:none;margin:0 0.5em 0 0.5em;padding:0;}.joomla-nav li a {color:#222;cursor:pointer;display:block;float:left;font-weight:bold;padding:0 .33em 0 .33em;}.joomla-nav li a.selected {color:#555;cursor:default;}.joomla-nav li a, .joomla-nav li a:focus, .joomla-nav li a:hover {text-decoration:none;}.joomla-nav li a:focus, .joomla-nav li a:hover {color:#555;outline:none;}.joomla-nav li.label {font-weight:normal;line-height:1.5;margin-right:1em;padding:.15em .33em .15em .33em;}