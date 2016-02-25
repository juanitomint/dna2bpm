<?php
//=== combobox 


function edit_combo($frame, $value) {
    $CI = & get_instance();
    $retstr = '';

$ops = array();

//---ensure array----
$value = (array) $value;

$locked=(isset($frame['locked']) && $frame['locked'] === true)?("readonly"):("");    
$disabled=(isset($frame['hidden']) && $frame['hidden'] === true)?("hidden"):("");     
$required=(isset($frame['required']) && $frame['required'] === true)?("required"):(""); 


    /*  
    if (isset($frame['disabled']))
        $disabled = ($frame['disabled']) ? getDisabledStr($frame['type']) : null;

    if (isset($frame['required']))
        $required = ($frame['required']) ? getRequiredStr($frame['type']) : null;
    */    
    $option = $CI->mongowrapper->db->options->findOne(array('idop' =>(int) $frame['idop']));



//prepare options array
    if (isset($option['fromContainer'])) { // if gets data from internal db
        $option['data'] = getOpsFromContainer($option);
    }

    //--data comes from loaded options
    foreach ($option['data'] as $thisop) {
        $ops[$thisop['value']] = $thisop['text'];
    }
//---4 ordering
    if (isset($frame['sortBy'])) {
        if ($frame['sortBy']=='value') {
            ksort($ops);
        } else {
            asort($ops);
        }
    }




    $retstr = "<select  class='form-control' $locked $required name='" . $frame['cname'] . "' id='" . $frame['cname'] . "' $disabled>\n";
    $retstr.="<option value=''>Seleccione una opci&oacute;n</option>\n";
    foreach ($ops as $key => $text) {
        $sel = (in_array($key, $value)) ? "selected='selected'" : '';
        $retstr.="<option value='$key' $sel>$text</option>\n";
    }
    $retstr.="</select>\n";
    //echo $retstr.'</br>';
    return $retstr;
}

//----how has to be viewed
function view_combo($frame, $value) {
    $CI = & get_instance();
    $retstr = '';
    $ops=array();
    $value=(array)$value;

    $option = $CI->mongowrapper->db->options->findOne(array('idop' => $frame['idop']));
    //prepare options array
    if (isset($option['fromContainer'])) { // if gets data from internal db
        $option['data'] = getOpsFromContainer($option);
    }
    //var_dump($option['data']);
    //--data comes from loaded options
    foreach ($option['data'] as $thisop) {
        if (in_array($thisop['value'], $value))
            $ops[$thisop['value']] = $thisop['text'];
    }
    return implode(',', $ops);
}

function search_combo($frame,$value){
    return edit_combo($frame, $value);
}
?>
