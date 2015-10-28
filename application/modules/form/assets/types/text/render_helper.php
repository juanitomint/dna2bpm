<?php

function edit_text($frame, $value) {
//$CI =& get_instance();

$locked=(isset($frame['locked']) && $frame['locked'] === true)?("readonly"):("");    
$disabled=(isset($frame['hidden']) && $frame['hidden'] === true)?("hidden"):("");     
$required=(isset($frame['required']) && $frame['required'] === true)?("required"):(""); 
$size = (isset($frame['size'])) ? $frame['size'] : 30;

//== Markup
$retstr=<<<_EOF_
<input class='form-control' type=text $disabled name={$frame['cname']}'  id='{$frame['cname']}' size='$size' value='$value' $locked $required>
_EOF_;


    return $retstr;
}

function view_text($frame, $value) {
    return $value;
}

function search_text($frame, $value) {
    $frame['size']=40;
    return edit_text($frame, $value);
}
?>