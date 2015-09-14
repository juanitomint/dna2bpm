<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
function edit_textarea($frame,$value){
 $disabled='';
 $required='';
 //var_dump($frame);
    if(isset($frame['locked']) && $frame['locked'] === true)
        $locked = "readonly";
    else $locked ='';
    
    ///Campo Hidden
    if(isset($frame['hidden']) && $frame['hidden'] === true)
        $disabled = "hidden";
    else $disabled ='';
    
    ///Campo requerido
    if(isset($frame['required']) && $frame['required'] === true)
        $required = "required";
    else $required ='';  
 /*
 if (isset($frame['disabled']))
    $disabled= (isset($frame['disabled']))    ? getDisabledStr($frame['type']):null;
 
 if (isset($frame['required']))
        $required = ($frame['required']) ? getRequiredStr($frame['type']) : null;
  * 
  */
 $retstr="<textarea $required $disabled $locked name='".$frame['cname']."'  id='".$frame['cname']."' cols='".$frame['cols']."' rows='".$frame['rows']."'>$value";
 $retstr.="</textarea>";
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