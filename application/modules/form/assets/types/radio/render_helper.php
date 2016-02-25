<?php
//=====  radio

function edit_radio($frame, $value) {
    $CI = & get_instance();
    $retstr = '';
    $ops = array();
    
    $value = (array) $value;
    $height = (isset($frame['cols'])) ? $frame['cols'] : 10;
    $locked=(isset($frame['locked']) && $frame['locked'] === true)?("disabled='disabled'"):("");   
    $hidden=(isset($frame['hidden']) && $frame['hidden'] === true)?("hidden"):(""); 
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
        $i = 0;

            // if (empty($required))
            // $retstr.="<label for=\"" . $frame['cname'] . "\" class=\"error\" style=\"display:none\">* Seleccione uno</label>";

    
         $retstr.="<table>"; 
         foreach ($ops as $key => $text) {
            $i++; 
            $sel = (in_array((string) $key, $value)) ? "checked='checked'" : '';
            if($i>$height)$retstr.="<tr>";
           // if($disabled == 'hidden')$text='';
            
$retstr.=<<<_EOF_
        <td style='padding-right:5px'><div class="radio $hidden" >
          <label>
            <input type="radio"  $required  $locked name="{$frame['cname']}" id="{$frame['cname']}" value="$key" $sel>
            $text 
          </label>
        </div></td>
_EOF_;
 
 
        if($i>=$height){
            $retstr.="</tr>";
            $i=0;
        }
     
        }//foreach
$retstr.="</table>"; 
 
        

    }//if

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
