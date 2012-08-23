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

.ajaxscrl {position: relative;overflow: visible; }.ajaxscrl .mContainer {width: 200px;height: 124px; display: block;overflow: hidden;position: relative;}.ajaxscrl .mScroller {display: block;margin: 0;padding: 0;}.ajaxscrl .mScroller div {display: block;text-align: left;margin: 0;float: left;width: 200px; min-height: 124px; }.ajaxscrl .mScroller div div {display: block;margin-right: 5px;float: left;width: 140px;}.ajaxscrl .mScroller div .title {display: block;}.ajaxscrl .mScroller div p.link {margin: 0;text-align: right;}.ajaxscrl .title {font-weight: bold;display: block;}.ajaxscrl .date {display: block;}.ajaxscrl .mNavLeft,.ajaxscrl .mNavRight,.ajaxscrl .mNavUp,.ajaxscrl .mNavDown,.ajaxscrl .mNavPause,.ajaxscrl .mNavPlay {width: 16px;height: 16px;cursor: hand;cursor: pointer;background: url(../images/navigation.png) no-repeat;position: absolute;top: -30px;}.ajaxscrl .mNavPlay,.ajaxscrl .mNavPause {right: 48px;}.ajaxscrl .mNavPlay {background-position: 0 -32px;}.ajaxscrl .mNavPlay.hover {background-position: -32px -32px;}.ajaxscrl .mNavPause {background-position: -16px -32px;}.ajaxscrl .mNavPause.hover {background-position: -48px -32px;}.ajaxscrl .mNavLeft {background-position: 0 0;right: 30px;}.ajaxscrl .mNavLeft.hover {background-position: -32px 0;}.ajaxscrl .mNavRight {background-position: -16px 0;right: 12px;}.ajaxscrl .mNavRight.hover {background-position: -48px 0;}.ajaxscrl .mNavUp {background-position: 0 -16px;right: 30px;}.ajaxscrl .mNavUp.hover {background-position: -32px -16px;}.ajaxscrl .mNavDown {background-position: -16px -16px;right: 12px;}.ajaxscrl .mNavDown.hover {background-position: -48px -16px;}.ajaxscrl .mNavLoading {width: 10px;height: 10px;position: absolute;top: -27px;right: 0;}.ajaxscrl .loading {background: url(../images/loading.gif) no-repeat;}