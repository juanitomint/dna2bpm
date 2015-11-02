<?php

function edit_date($frame, $value) {
    //$CI =& get_instance();
    //var_dump($frame);
    
    
//     return <<<_EOF_
//                     <div class='input-group date datepicke'>
//                     <input type='text' class="form-control" />
//                     <span class="input-group-addon">
//                         <span class="glyphicon glyphicon-calendar"></span>
//                     </span>
//                 </div>
                
// _EOF_;

    // $value=($value<>'')?explode('-',$value):array(0=>'',1=>'' ,2=>'');
    // $retstr = '';

    
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
    

    $mask=      (isset($frame['mask']))     ? $frame['mask']:null;
    //$required=  (isset($frame['required'])) ? getRequiredStr($frame['type']):null;
    //-----dia---------------------------------------
 //   $retstr=$retstr."<input type=text class='textInput' min=\"1\" max=\"31\"  minlength=\"2\"id='".$frame['cname']."[d]' name='".$frame['cname']."[d]' maxlength='2' size='2' min='1' max='31' minlength='2' value='$value[2]'>\r";
    $retstr=$retstr."<input type=text ".$disabled." class='textInput' id='".$frame['cname']."[d]' name='".$frame['cname']."[d]' maxlength='2' size='2' min='1' max='31' minlength='2' value='".$value[2]."' ".$locked." ".$required.">\r";
    if($frame['hidden'] == false)
        $retstr=$retstr."/ ";
    //-----mes---------------------------------------
    //$retstr=$retstr."<input type=text class='textInput' $required $disabled min=\"1\" max=\"12\" minlength=\"2\" id='".$frame['cname']."[m]' name='".$frame['cname']."[m]' maxlength='2' size='2' min='1' max='12' minlength='2' value='$value[1]'>\r";
    $retstr=$retstr."<input type=text ".$disabled." class='textInput' id='".$frame['cname']."[m]' name='".$frame['cname']."[m]' maxlength='2' size='2' min='1' max='12' minlength='2' value='".$value[1]."' ".$locked." ".$required.">\r";
    if($frame['hidden'] == false)
        $retstr=$retstr."/ ";
    
    //-----ano---------------------------------------
    //$retstr=$retstr."<input type=text class='textInput'$required $disabled name='".$frame['cname']."[Y]' id='".$frame['cname']."[Y]'  size='12' value='$value[0]' maxlength='4' size='4' min='1900' max='2050' minlength='4' >";
    $retstr=$retstr."<input type=text ".$disabled." class='textInput' name='".$frame['cname']."[Y]' id='".$frame['cname']."[Y]' maxlength='4' size='4' min='1900' max='2050' minlength='4' value='".$value[0]."' ".$locked." ".$required." >";
    if($frame['hidden'] == false)    
        $retstr=$retstr."&nbsp;<label id=\"label_frame\">(dd/mm/aaaa)</label>";    
    //echo 'Date'.$retstr.'</br>';
    
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
