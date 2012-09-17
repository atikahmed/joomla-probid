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

/*** style.css ***/

#rsm_tt {position:absolute; display:block; background:#fff; border: solid 1px #E6E6E6}#rsm_tttop {display:block; height:5px; margin-left:5px; background:#fff; overflow:hidden}#rsm_ttcont {display:block; padding:2px 12px 3px 7px; margin-left:5px; background:#fff; color:#666}#rsm_ttbot {display:block; height:5px; margin-left:5px; background:#fff; overflow:hidden}#rsm_whats_csc {padding-bottom:1px; border-bottom:1px dotted #666; cursor:pointer}.rsm_response_error { margin-top: 10px;padding: 5px; text-align: center; font-weight: bold; }#rsm_warning { vertical-align: middle; }#rsm_cc_exp_mm { width: 100px; }