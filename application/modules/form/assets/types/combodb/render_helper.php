<?php

function edit_combodb($frame, $value) {
    $CI = & get_instance();
    $retstr = '';
    $ops = array();

    
    //$required = (@$frame['required']) ? getRequiredStr($frame['type']) : null;
    $value = array_map('floatval', (array) $value);
    $fields = array();
    $query = array();
    
    if(isset($frame['locked']) && $frame['locked'] === true)
        $locked = "disabled";
    else $locked ='';
    
    ///Campo Hidden
    if(isset($frame['hidden']) && $frame['hidden'] === true)
        $disabled = "style='visibility:hidden'";
    else $disabled ='';
    
    ///Campo requerido
    if(isset($frame['required']) && $frame['required'] === true)
        $required = "required";
    else $required ='';     

    
    $fields[] = $frame['fieldValue']; //'id'
    
    foreach ($frame['fields'] as $field)
        $fields[] = (string) $field;
     
    
    
    
    
    //var_dump($opcion);
     if(isset($frame['query'])){
        $opcion1 = $frame['query'];
        //$opcion = '{"status":"activa"}';
        $opcion = str_replace("'", "\"",$opcion1); 
        if($frame['query']<> ''){
            $query = (array)json_decode($opcion,true);
        } else $query = (array)json_decode('{}');
     }else $query = (array)json_decode('{}');
    
    //var_dump($query);
    
    $rsop = $CI->mongowrapper->db->selectCollection($frame['dataFrom'])->find($query, $fields);
    $result = array();
    $i=0;
    foreach($rsop as $search){
        $result[$i] = $search;
        $i++;
    }
    //var_dump($result);    
    //exit();    
   
    //------------------------------------
    //var_dump($frame[dataFrom],$query,$fields,$rsop->getNext());
    //var_dump($rsop);
    $retstr = "<select $locked $required name='" . $frame['cname'] . "' id='" . $frame['cname'] . "' class='combodb' $disabled>\n";
    $retstr.="<option value=''>Seleccione una opci&oacute;n</option>\n";
    $arr = array();
    //while ($arr = $rsop->getNext()) {
    
    $j =0;
    while ($j < $i && $j < 100) {
        $text = array();
        //var_dump($arr);
        //-----make a string with all the fields 4 text
    
        $showtext ='';
        foreach ($fields as $campos){
            //echo 'Campos:'.$campos;
            if(isset($result[$j][$campos])){
                if(is_array($result[$j][$campos])){
                    //echo ' valor:'.$result[$j][$campos][0];
                    $text[] = $result[$j][$campos][0];
                }else
                    { 
                    //echo ' valor:'.$result[$j][$campos];
                    $text[] = $result[$j][$campos];
                }
            
            } else $text[] ='';
        }
        
        $showtext = implode(' | ', $text);
        
        if (trim($showtext)<>'') {
            
            $retstr.="<option value='" . $text[0] . "' >$showtext</option>\n";
            
        }
          
        $j++;
    }
    $retstr.="</select>\n";
    return $retstr;
}

//----how has to be viewed
function view_combodb($frame, $value) {
    $CI = & get_instance();
    $retstr = '';
    $ops = array();
    $value = array_map('floatval', (array) $value);
    //---retrive frame from DB
    $query = array('idframe' => $frame['idframe']);
    $frame = $CI->mongowrapper->db->frames->findOne($query);

    $fields = $frame['fields'];
    $query = $frame['query'];
    foreach ($fields as $fcond) {
        $query[(string) $fcond] = array('$ne' => '');
    }
    $query[$frame['fieldValue']] = array('$in' => $value);
    $fields[] = $frame['fieldValue'];
    $fields = array_filter($fields);
    //var_dump($query,$fields,$frame[sort]);
    $rsop = $CI->mongowrapper->db->selectCollection($frame['dataFrom'])->find($query, $fields);
    $rsop->sort((array) $frame['sort']);
    //var_dump($query,$fields,$rsop->getNext());

    while ($arr = $rsop->getNext()) {
        $text = array();
        foreach ($frame['fields'] as $field)
            $text[] = (@$arr[$field]) ? $arr[$field] : null;
        $text = implode(' ', $text);
        if (trim($text)<>'') {

            $frame['data'][] = $text;
        }
    }
    return implode(',', $frame['data']);
}
?>
