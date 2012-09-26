<?php
/*------------------------------------------------------------------------
# SP Tab - Tab Module for Joomla by JoomShaper.com
# ------------------------------------------------------------------------
# author    JoomShaper http://www.joomshaper.com
# Copyright (C) 2010 - 2012 JoomShaper.com. All Rights Reserved.
# @license - Copyrighted Commercial Software
# Websites: http://www.joomshaper.com
# This file may not be redistributed in whole or significant part
-------------------------------------------------------------------------*/
header("Content-Type: text/css");
$uniqid = $_GET['id'];
?>
#sptab<?php echo $uniqid ?> {border:1px solid #e5e5e5}
#sptab<?php echo $uniqid ?> ul.tabs_container {list-style:none;margin: 0!important; padding: 0!important}
#sptab<?php echo $uniqid ?> .tabs_buttons{background:#fff url(../images/style9/header_bg.gif) repeat-x 0 100%;border-bottom:1px solid #e5e5e5;overflow:hidden}
#sptab<?php echo $uniqid ?> ul.tabs_container li.tab{background:none;border:0!important;border-right:1px solid #e5e5e5!important;color:#666;float:left;padding:0;margin:0}
#sptab<?php echo $uniqid ?> ul.tabs_container li.tab span{display:inline-block;cursor:pointer;padding:0 10px;margin:0 1px 0 0}
#sptab<?php echo $uniqid ?> ul.tabs_container li.tab, #sptab<?php echo $uniqid ?> ul.tabs_container li.tab span{font-size:12px}
#sptab<?php echo $uniqid ?> .items_mask {position:relative;overflow:hidden}

#sptab<?php echo $uniqid ?> ul.tabs_container li.tab.active span {background:#fff url(../images/style9/tab_active.gif) repeat-x 0 0;}

#sptab<?php echo $uniqid ?>.sptab_red ul.tabs_container li.tab.tab_over,
#sptab<?php echo $uniqid ?>.sptab_red ul.tabs_container li.tab.active{color:#ba0202}

#sptab<?php echo $uniqid ?>.sptab_green ul.tabs_container li.tab.tab_over,
#sptab<?php echo $uniqid ?>.sptab_green ul.tabs_container li.tab.active{color:#91ba02}

#sptab<?php echo $uniqid ?>.sptab_blue ul.tabs_container li.tab.tab_over,
#sptab<?php echo $uniqid ?>.sptab_blue ul.tabs_container li.tab.active{color:#01b0e2}