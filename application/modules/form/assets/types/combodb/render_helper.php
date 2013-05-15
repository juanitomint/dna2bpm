<?php

function edit_combodb($frame, $value) {
    $CI = & get_instance();
    $retstr = '';
    $ops = array();
    $required = (@$frame['required']) ? getRequiredStr($frame['type']) : null;
    $value = array_map('floatval', (array) $value);
    $fields = array();


    $query = $frame['query'];
    //----Ensure Fields are strings 4 result
    foreach ($frame['fields'] as $field)
        $fields[] = (string) $field;
    $fields[] = $frame['fieldValue'];
    //$fields[]='status';
    //var_dump($frame['dataFrom'], json_encode($query), $fields, $frame['sort']);

    $rsop = $CI->mongo->db->selectCollection($frame['dataFrom'])->find($query, $fields);
    //---4 sorting order
    $rsop->sort((array) $frame['sort']);
    //------------------------------------
    //var_dump($frame[dataFrom],$query,$fields,$rsop->getNext());

    $retstr = "<select  $required name='" . $frame['cname'] . "' id='" . $frame['cname'] . "' class='combodb' $disabled>\n";
    $retstr.="<option value=''>Seleccione una opci&oacute;n</option>\n";
    while ($arr = $rsop->getNext()) {
        $text = array();
        //-----make a string with all the fields 4 text
        foreach ($frame['fields'] as $field) {
            $thisframe=$CI->app->get_frame($field);
            $callfunc = 'view_' . $thisframe['type'];
            //var_dump($callfunc,function_exists($callfunc));
            $text[] = (function_exists($callfunc)) ? $callfunc($thisframe, $arr[$field]) : null;
        }
        //var_dump($text);
        $showtext = implode(' | ', $text);
        //----------------------------------------------------------
        $sel = (in_array($arr[$frame['fieldValue']], $value)) ? "selected='selected'" : '';
        if (trim($showtext)<>'') {
            $retstr.="<option value='" . $arr[$frame['fieldValue']] . "' $sel>$showtext</option>\n";
        }
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
    $frame = $CI->mongo->db->frames->findOne($query);

    $fields = $frame['fields'];
    $query = $frame['query'];
    foreach ($fields as $fcond) {
        $query[(string) $fcond] = array('$ne' => '');
    }
    $query[$frame['fieldValue']] = array('$in' => $value);
    $fields[] = $frame['fieldValue'];
    $fields = array_filter($fields);
    //var_dump($query,$fields,$frame[sort]);
    $rsop = $CI->mongo->db->selectCollection($frame['dataFrom'])->find($query, $fields);
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
