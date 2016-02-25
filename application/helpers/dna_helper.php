<?php

function toString($val) {
    return (string) $val;
}
function toInt($val) {
    return (int) $val;
}

function toRegex(&$item) {
//---usage array_walk_recursive($query,'toRegex');
    if (is_array($item)) {
        $arr = each($item);
        $key = $arr['key'];
        $itemc = $arr['value'];
        var_dump("$key holds $itemc<br/>");
        if ($key=='$regex') {
            $item = array($key => new MongoRegex($itemc));
        }
    }
}

function genIdFrame($conn, $collection) {
    $cursor = $collection->find()->limit(1);
    $cursor = $cursor->sort(array("idframe" => -1));
    $f = $cursor->getNext();
    return $f[idframe] + 1;
}

function getRequiredStr($type) {
    $rtnstr = "class='required'";
    switch ($type) {
        case 'subform':
            $rtnstr = "subFormMin='1'";
            break;
    }
    return $rtnstr;
}

function getDisabledStr($type) {
    $rtnstr = " disabled='disabled' ";
    switch ($type) {
        case 'subform':

            break;
    }
    return $rtnstr;
}

function getvalue($id, $idframe) {
    $CI = & get_instance();
    //var_dump('getvalue',$id,$idframe);
    $frame = $CI->mongowrapper->db->frames->findOne(array(idframe => $idframe), array('container'));
    $query = array(id => $id);
    $fields = array((string) $idframe);
    if ($frame[container]) {
        $result = $CI->mongowrapper->db->selectCollection($frame[container])->findOne($query, $fields);
    } else {
        trigger_error("container property missing for: $idframe");
    }
    //var_dump($frame[container],$query,$fields,$result);
    return $result[$idframe];
}

function getOpsFromContainer($option) {
//uses: query fields fieldRel optionFromcontainer

    $CI = & get_instance();
    $rtnarr = array();
    //var_dump($CI->options);
    if (!isset($CI->options[$option['idop']])) {
        //var_dump('NOT chached');
        // gets data from internal db
        $query = (isset($option['query'])) ? (array) $option['query'] : array();
        $fields = $option['fieldText'];
        $fields[] = $option['fieldValue'];
        $fields[] = $option['fieldRel'];
        $fields = array_filter($fields);
        //echo '<hr>'. $option['idop'];
        //var_dump($option['container'],$query,$fields);
        $rsop = $CI->mongowrapper->db->selectCollection($option['container'])->find($query, $fields);
        while ($arr = $rsop->getNext()) {
            $text = array();
            foreach ($option['fieldText'] as $field)
                $text[] = (isset($arr[$field])) ? $arr[$field] : null;
            $text = implode(' ', $text);
            $rtnarr[] = array(
                'value' => (isset($arr[$option['fieldValue']]))?$arr[$option['fieldValue']]:null,
                'text' => $text,
                'idel' => (isset($arr[$option['fieldRel']]))? $arr[$option['fieldRel']]:null
                );
        }
        $CI->options[$option['idop']] = $rtnarr;
    } else {
        //var_dump('chached');
//----cache options
        $rtnarr = $CI->options[$option['idop']];
    }
    return($rtnarr);
}

function formGetBack($idform) {
    $CI = & get_instance();
    //---get SRC -----------------------
    $query = array('idform' => (int) $idform);
    $formBACK = $CI->mongowrapper->db->selectCollection('forms.back')->findOne($query);
//---get DST -----------------------
    $query = array('idform' => (int) $idform);
    $formSRC = $CI->mongowrapper->db->forms->findOne($query);

    $result = $CI->mongowrapper->db->forms->save($formBACK);

    $result1 = $CI->mongowrapper->db->selectCollection('forms.back')->save($formSRC, array(safe => true));


    return true;

//$forms2->debug=true;
}

function formCheckOut($idform) {
    $CI = & get_instance();
    //---get DST -----------------------
    $query = array('idform' => (int) $idform);
    $formSRC = $CI->mongowrapper->db->forms->findOne($query);
    $result = $CI->mongowrapper->db->selectCollection('forms.back')->save($formSRC, array(safe => true));
    //echo "$idform -> BACK -> $result[ok]<br/>";
    return $result['ok'];

//$forms2->debug=true;
}
?>
