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

/*** template.css ***/

@charset "UTF-8";.blog-featured h2 {font-size: 1.5em;margin-bottom:0em;}p.readmore {text-indent:0;font-size: .9em;}.joomla-footer {font-size: .9em;margin-bottom: 30px;}ul.actions {clear:both;margin-top: -50px;float:right;}ul.actions li {list-style-type: none;float:right;margin-left: 10px;}p#form-login-username label,p#form-login-password label {width: 160px;display:block;}p#form-login-remember label {font-size: .9em;font-weight: normal;line-height: 25px;}p#form-login-remember input {float:left;margin-right: 5px;}form#form-login ul {margin: 0;padding: 0;}form#form-login ul li {list-style-type: none;margin-left: 20px;font-size: .9em;}