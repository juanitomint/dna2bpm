<?php

function edit_date($frame, $value) {
    //$CI =& get_instance();
    $value=($value<>'')?explode('-',$value):array(0=>'',1=>'' ,2=>'');
    $retstr = '';
    $disabled=  (isset($frame['disabled']))? "disabled='disabled'":'';
    $mask=      (isset($frame['mask']))     ? $frame['mask']:null;
    $required=  (isset($frame['required'])) ? getRequiredStr($frame['type']):null;
    //-----dia---------------------------------------
    $retstr=$retstr."<input type=text class='textInput' $required $disabled min=\"1\" max=\"31\"  minlength=\"2\"
     id='".$frame['cname']."[d]' name='".$frame['cname']."[d]' maxlength='2' size='2' min='1' max='31' minlength='2' value='$value[2]'>\r";
    $retstr=$retstr."/ ";
    //-----mes---------------------------------------
    $retstr=$retstr."<input type=text class='textInput' $required $disabled min=\"1\" max=\"12\" minlength=\"2\" id='".$frame['cname']."[m]' name='".$frame['cname']."[m]' maxlength='2' size='2' min='1' max='12' minlength='2' value='$value[1]'>\r";
    $retstr=$retstr."/ ";
    //-----ano---------------------------------------
    $retstr=$retstr."<input type=text $required class='textInput' $disabled name='".$frame['cname']."[Y]' id='".$frame['cname']."[Y]'  size='12' value='$value[0]' maxlength='4' size='4' min='1900' max='2050' minlength='4' >";
    $retstr=$retstr."&nbsp;<label id=\"label_$frame\">(dd/mm/aaaa)</label>";    
    return $retstr;

}

function view_date($frame, $value) {
    return $value;
}
function search_date($frame, $value) {
    $cname=$frame['cname'];
    $retstr= 'FROM:';
    $retstr.=edit_date($frame, $value);
    $retstr.=' TO ';
    $frame['cname']='to_'.$cname;
    $retstr.=edit_date($frame, $value);
    return $retstr;
}
?>
