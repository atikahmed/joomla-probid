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

 p + p { text-indent:2em; margin-top:-1.5em; } form p + p{ text-indent: 0; } .alt {color: #666;font-family: "Warnock Pro", "Goudy Old Style","Palatino","Book Antiqua", Georgia, serif;font-style: italic;font-weight: normal;}.dquo { margin-left: -.5em; }p.incr, .incr p {font-size: 10px;line-height: 1.44em;margin-bottom: 1.5em;}.caps {font-variant: small-caps;letter-spacing: 1px;text-transform: lowercase;font-size:1.2em;line-height:1%;font-weight:bold;padding:0 2px;}