<?php

function edit_datetime($frame, $value) {
    //$CI =& get_instance();
    if($value==''){
        $value=array(0=>'',1=>'' ,2=>'',3=>'',4=>'');
    } else {
        //----Parse isodate
        $date=strtotime($value);
        $value=array(0=>date('Y',$date),1=>date('m',$date),2=>date('d',$date),3=>date('h',$date),4=>date('i',$date));
    }
    $retstr = '';
    $disabled=  (isset($frame['disabled'])) ? "disabled='disabled'":null;
    $mask=      (isset($frame['mask']))? $frame['mask']:null;
    $required=  (isset($frame['required']))? getRequiredStr($frame['type']):null;
    

   //-----dia---------------------------------------
    $retstr=$retstr."<input type=text class='textInput' $required $disabled $mask name='".$frame['cname']."[d]' id='".$frame['cname']."[d]' maxlength='2' size='2' min='1' max='31' minlength='2'  value='$value[2]'>\r";
    $retstr=$retstr."/ ";
    //-----mes---------------------------------------
    $retstr=$retstr."<input type=text class='textInput' $required $disabled $mask name='".$frame['cname']."[m]' id='".$frame['cname']."[m]' maxlength='2' size='2' min='1' max='12' minlength='2'   value='$value[1]'>\r";
    $retstr=$retstr."/ ";
    //-----ano---------------------------------------
    $retstr=$retstr."<input type=text class='textInput' $required $disabled $mask name='".$frame['cname']."[Y]' id='".$frame['cname']."[Y]' maxlength='4' size='4' regexpPattern='^(19|20)\d\d$'  value='$value[0]'>\r";
    $retstr=$retstr."&nbsp;&nbsp;";
    //-----hora---------------------------------------
    $retstr=$retstr."<input type=text class='textInput' $required $disabled $mask name='".$frame['cname']."[h]' id='".$frame['cname']."[h]' maxlength='2' size='2' min='0' max='23' minlength='2'  value='$value[3]'>\r";
    $retstr=$retstr.": ";
    //-----min---------------------------------------
    $retstr=$retstr."<input type=text class='textInput' $required $disabled $mask name='".$frame['cname']."[i]' id='".$frame['cname']."[i]' maxlength='2' size='2' min='1' max='59' minlength='2'  value='$value[4]'>\r";
    $retstr=$retstr."  (dd/mm/aaaa hh:mm)";
    return $retstr;

}

function view_datetime($frame, $value) {
    return $value;
}
?>
