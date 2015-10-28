<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
function edit_textarea($frame,$value){

$locked=(isset($frame['locked']) && $frame['locked'] === true)?("readonly"):("");    
$disabled=(isset($frame['hidden']) && $frame['hidden'] === true)?("hidden"):("");     
$required=(isset($frame['required']) && $frame['required'] === true)?("required"):(""); 
    
//== Markup
$retstr=<<<_EOF_
<textarea class='form-control' $required $disabled $locked name='{$frame['cname']}' id='{$frame['cname']}' cols='{$frame['cols']}' rows='{$frame['rows']}'>
$value
</textarea>
_EOF_;
 
 return $retstr;
}

//----how has to be viewed
function view_textarea($frame,$value){
    return $value;
}

function search_textarea($frame, $value) {
    $frame['size']=40;
    return edit_text($frame, $value);
}
?>