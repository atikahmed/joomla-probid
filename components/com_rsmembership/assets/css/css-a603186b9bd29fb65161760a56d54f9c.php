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

/*** rsmembership.css ***/

img.rsm_thumb{float: left;}.rsm_clear{display: block;clear: both;height: 1%;font-size: 1%;line-height: 1%;}h2.rsm_title{font-size: 150% !important;}h3.rsm_extra_title{font-size: 12px;}button.rsm_button_buy{}select.rsm_extra, input.rsm_extra, label.rsm_extra{}#rsm_suggestions{background: #F5FAF9;border: solid 1px #A2BDCD;padding: 7px;margin-top: 7px;float: left;}#rsm_suggestions ol{margin: 0;padding: 0;padding-left: 18px;}#rsm_suggestions ol li{padding: 0;margin: 0;}#rsm_username_message{padding: 4px;margin-top: 7px;float: left;}.rsm_error{border: solid 1px #EB3B00;color: #EB3B00;background: #FFEFEB;}.rsm_field_error{border: solid 1px #EB3B00 !important;}.rsm_modal_error{border-bottom: 2px solid #ed2025;border-top: 2px solid #ed2025;background: #ffc9ca url(../images/error.png) no-repeat 8px center;color: #b91217;font-weight: bold;padding: 8px 0 8px 40px;margin: 10px 0;}.rsm_modal_error_container{font-size: 12px;width: 400px;margin: 0 auto;}.rsm_ok{border: solid 1px #009E28;color: #009E28;background: #F1FFEB;}.membershiptable {border-collapse: collapse;margin-bottom: 1.5em;width: 99%;}.membershiptable {border: 0px;border-top: 2px solid #cccccc;border-left: 1px solid #cccccc;}.membershiptable td {border: 0px;padding: 7px;border-bottom: 1px solid #cccccc;border-right: 1px solid #cccccc;}.membershiptable th {border: 0px;padding: 7px;border-bottom: 1px solid #cccccc;border-right: 1px solid #cccccc;background: #f7f7f7;color: #006db9;}.rsmembership_form input.rsm_textbox {width: 324px;font-size: 12px;padding: 8px 6px;background: #fff url(../images/formbg.gif) repeat-x left top;border: 1px solid #e5e5e5;float: left:}.rsmembership_form select {width: 324px;font-size: 12px;padding: 8px 6px;background: #fff url(../images/formbg.gif) repeat-x left top;border: 1px solid #e5e5e5;float: left:}.rsmembership_form textarea {width: 324px;height: 140px;font-size: 12px;padding: 8px 6px;background: #fff url(../images/formbg.gif) repeat-x left top;border: 1px solid #e5e5e5;float: left:}.rsmembership_form table.rsmembership_form_table,.rsmembership_form table.rsmembership_form_table tr,.rsmembership_form table.rsmembership_form_table tr td {border: 0px;}.rsmembership_form fieldset { border: 1px solid #e5e5e5; width: 98%; padding: 1%; margin-bottom: 20px; background: url(../images/contentbg.gif) repeat-x left top;}.rsmembership_form legend {font-weight: bold;color: #006db9;padding: 0 10px;}.rsm_container a.rsm_details {width: 121px;height: 26px;background: url(../images/pricebg.gif) repeat-x left center;color: white;text-align: center;border: none;margin-left: 5px;cursor: pointer;text-decoration: none;display: block;border-radius: 5px;-moz-border-radius: 5px;line-height: 26px;float: left;}.rsm_container a.rsm_button {width: 121px;height: 26px;background: url(../images/subscribebg.gif) repeat-x left center;color: white;text-align: center;border: none;margin-left: 5px;cursor: pointer;text-decoration: none;display: block;border-radius: 5px;-moz-border-radius: 5px;line-height: 26px;float: left;}