<?php

function edit_radio($frame, $value) {
    $CI = & get_instance();
    $retstr = '';
    $ops = array();
    $value = (array) $value;
    $height = (isset($frame['cols'])) ? $frame['cols'] : 10;
    $locked=(isset($frame['locked']) && $frame['locked'] === true)?("readonly"):("");   
    $disabled=(isset($frame['hidden']) && $frame['hidden'] === true)?("hidden"):(""); 
    $required=(isset($frame['required']) && $frame['required'] === true)?("required"):("");
     
    $option = $CI->mongowrapper->db->options->findOne(array('idop' => (int)$frame['idop']));
    
    //prepare options array
    if (isset($option['fromContainer'])) { // if gets data from internal db
        $option['data'] = getOpsFromContainer($option);
    }
    //var_dump($frame,$option);
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

    if (count($ops) > 60 and !isset($frame['cols']))
        $height = 20;
    if (count($ops) > 150 and !isset($frame['cols']))
        $height = 50;
    /* PENDING
     * nclude("checkrelative.php");
     */



    if (count($ops)) {
        $i = 1;
            // if (empty($required))
            // $retstr.="<label for=\"" . $frame['cname'] . "\" class=\"error\" style=\"display:none\">* Seleccione uno</label>";
            // $retstr.="<div style='display:table'><div style='display:table-row'><div style='display:table-cell'>";       
        
            // $retstr=<<<_EOF_
            // <label class="radio-inline">
            //   <input type="radio" name="inlineRadioOptions" id="inlineRadio1" value="option1"> {$frame['cname']}
            // </label>
            // _EOF_;
    
    
    
 foreach ($ops as $key => $text) {
     $retstr.=$text."//";
 }
        
        

        // foreach ($ops as $key => $text) {
        //     $sel = (in_array((string) $key, $value)) ? "checked='checked'" : '';
        //     $retstr.="<label>$height<input type='radio' $required $disabled name='" . $frame['cname'] . "' value='$key' $sel>";
            
        //     if($disabled != 'hidden')
        //     $retstr.=$text;
            
        //     $retstr.="</label><br>\r";
            
            
        //     if ($i++==$height) {
        //         $retstr.="</div><div style='display:table-cell'>";
        //         $i = 1;
        //     }
        // }
        // $retstr.="</div></div></div>\r";


    }
    //echo $retstr.'</br>';
    return $retstr;
}

//----how has to be viewed
function view_radio($frame, $value) {
    $CI = & get_instance();
    $retstr = '';
    $ops=array();
    $value=(array)$value;
    $option = $CI->mongowrapper->db->options->findOne(array('idop' => $frame['idop']));
    //prepare options array
    if (isset($option['fromContainer'])) { // if gets data from internal db
        $option['data'] = getOpsFromContainer($option);
    }

    //--data comes from loaded options
    foreach ($option['data'] as $thisop) {
        if (in_array($thisop['value'], $value))
            $ops[$thisop['value']] = $thisop['text'];
    }
    return implode(',', $ops);
}
?>
