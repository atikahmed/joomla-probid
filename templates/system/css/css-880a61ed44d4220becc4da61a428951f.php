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

/*** system.css ***/

@import url(../../../media/system/css/system.css);.system-unpublished, tr.system-unpublished {background: #e8edf1;border-top: 4px solid #c4d3df;border-bottom: 4px solid #c4d3df;}#system-debug { color: #777; background-color: #eee; padding: 10px; margin: 10px; text-align: left; }#system-debug div { font-size: 11px;font-family: monospace; }#system-debug ol{ padding-left: 1.5em; }#system-debug ol li { font-size: 11px; margin-bottom: 0.5em; font-family: monospace; }#system-debug h4 { margin-bottom: 0.5em; margin-top: 1.0em; }