<?php
set_include_path(get_include_path() . PATH_SEPARATOR . '/home/probid/pear/php');
require_once('../connections/dbMDB2.php');
$query = 'SELECT optionid, fieldid, text FROM `jos_jreviews_fieldoptions` WHERE `fieldid` = 34 ORDER BY `text`';
//build checklists from query results
$result = $conn->query($query);
$htmlOutput = '<fieldset><legend>Service Provider Categories</legend>';
while($row = $result->fetchRow(MDB2_FETCHMODE_ASSOC)) {
        $htmlOutput .= '<div class="five-row">';
        $htmlOutput .= '<input type="checkbox" class="tags_checkboxes" id="tag_' . $row['optionid'] . '" value="' . $row['optionid'] . '">';
        $htmlOutput .= '<label for="tag_' . $row['optionid'] . '">' . $row['text'] . '</label>';
        $htmlOutput .= '</div>';
}
$htmlOutput .= '</fieldset>';
//CLOSE DB CONN
require_once('../connections/dbMDB2-disconnect.php');
echo $htmlOutput;