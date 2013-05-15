<?php

function edit_text($frame, $value) {
//$CI =& get_instance();
    //var_dump($frame);
    $retstr = '';
    $disabled = '';
    $required = '';
    if (isset($frame['disabled']))
        $disabled = ($frame['disabled']) ? getDisabledStr($frame['type']) : null;

    if (isset($frame['required']))
        $required = ($frame['required']) ? getRequiredStr($frame['type']) : null;

    $size = (isset($frame['size'])) ? $frame['size'] : 30;
    $retstr = $retstr . "<input type='text' $required $disabled name='" . $frame['cname'] . "'  id='" . $frame['cname'] . "' size='$size' value=\"$value\">\r";
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