<?php
header("Content-Type: text/css");
$uniqid = $_GET['id'];
?>

<?php // ++++++++++++++  tabs/ slide  ++++++++++++++ ?>
#hidetab<?php echo $uniqid ?> .slide
{
        height:auto !important;

}

#hidetab<?php echo $uniqid ?> ul.hidetabs
{
    overflow:hidden;
    padding:0 !important;
    margin:0 !important
}

#hidetab<?php echo $uniqid ?> .tabopen ul.newsflash-horiz,
#hidetab<?php echo $uniqid ?> .tabopen ul.newsflash-vert,
#hidetab<?php echo $uniqid ?> .tabopen ul.latestnews
{
    margin:0 !important;
    padding:0 !important;
}

#hidetab<?php echo $uniqid ?> .tabopen ul.newsflash-horiz li,
#hidetab<?php echo $uniqid ?> .tabopen ul.newsflash-vert li
{
    padding:0 6px !important;
}

#hidetab<?php echo $uniqid ?> ul.hidetabs li
{
    list-style-type:none;
    float:left;
    width:auto;
    padding:0;
    display:block;
    margin:0;
    font-size:1em;
}

#hidetab<?php echo $uniqid ?> ul.hidetabs li a:link,
#hidetab<?php echo $uniqid ?> ul.hidetabs li a:visited
{
    text-decoration:none;
    padding:7px 5px;
    margin:0px ;
    display:block;
    font-size:0.9em;
    font-weight:normal;
}

#hidetab<?php echo $uniqid ?> ul.hidetabs li a.linkopen:link,
#hidetab<?php echo $uniqid ?> ul.hidetabs li a.linkopen:visited
{
    font-weight:bold;
}

#hidetab<?php echo $uniqid ?> ul.hidetabs li a:hover,
#hidetab<?php echo $uniqid ?> ul.hidetabs li a:active,
#hidetab<?php echo $uniqid ?> ul.hidetabs li a:focus
{
        text-decoration:underline;
}

#hidetab<?php echo $uniqid ?> .hidetabcontent
{
        padding:15px 10px;
        margin-top:-1px;

}

#hidetab<?php echo $uniqid ?> .hidetabcontent:focus
{
	outline:none
}

#hidetab<?php echo $uniqid ?> .tabopen
{
        display:block;
        margin-bottom:20px;
        overflow:hidden
}

#hidetab<?php echo $uniqid ?> .tabclosed
{
        display:none
}

#hidetab<?php echo $uniqid ?> .hidetabcontent ul
{
        padding:0
}

#hidetab<?php echo $uniqid ?> .hidetabcontent ul li
{
        list-style-type:none
}

#hidetab<?php echo $uniqid ?> .hidetabcontent .linkclosed
{

}

#hidetab<?php echo $uniqid ?> a.linkopen
{

}

#hidetab<?php echo $uniqid ?> .hidetabouter
{
        margin-top:20px
}


#hidetab<?php echo $uniqid ?> .module_content
{
	border:solid 1px #000;
	padding:10px
}
