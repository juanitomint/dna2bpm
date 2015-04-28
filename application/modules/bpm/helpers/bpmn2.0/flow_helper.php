<?php
function run_MessageFlow($shape, $wf,$CI) {
    $debug = (isset($CI->debug[__FUNCTION__])) ? $CI->debug[__FUNCTION__] : false;
    $idwf = $wf->idwf;
    $case = $wf->case;
    $data=array();
    //----End catching
    foreach ($shape->outgoing as $out) {
        $this_shape = $CI->bpm->get_shape($out->resourceId, $wf);
            //@todo check what happens when messageflow reach other targets
            switch ($this_shape->stencil->id == 'IntermediateTimerEvent') {
                case "IntermediateMessageEventCatching":
                    $CI->bpm->movenext($this_shape, $wf, $data);
                    break;
        }
    }
    
    $CI->bpm->movenext($shape, $wf,$data);
    
}
function run_SequenceFlow($shape, $wf,$CI) {
    $debug = (isset($CI->debug[__FUNCTION__])) ? $CI->debug[__FUNCTION__] : false;
    
    $idwf = $wf->idwf;
    $case = $wf->case;
    $data=array();
    //---collect data from previous instance
    $resourceId = $shape->resourceId;
    $inbound = $CI->bpm->get_inbound_shapes($resourceId, $wf);
//    if($debug) var_dump($inbound[0], property_exists($inbound[0]->properties, 'dataoutputset'));
    //---Check if exists dataoutput
    $data_out=false;
    if (property_exists($inbound[0]->properties, 'dataoutputset')) {
        $data_out = (property_exists($inbound[0]->properties->dataoutputset, 'items')) ? $inbound[0]->properties->dataoutputset->items:false;
    }
    //---analyze inbound shape to see if has data to carry on
    if($debug)  var_dump('Data_out',$data_out);
    if ($data_out) {
        //---get data token from previous shape
        $token = $CI->bpm->get_token($wf->idwf, $wf->case, $inbound[0]->resourceId);
        foreach ($data_out as $item) {
            
            $data['transport'][$item->name] = (isset($token['data'][$item->name])) ? $token['data'][$item->name]:null;
            
        }
    
        if($debug){
        var_dump('TOKEN',$token);
        var_dump('DATA',$data);
        }
    }
    //---End collect data
//    if ($shape->properties->conditionexpression) {
//        $shape->properties->conditiontype = 'Expression';
//    }
    switch ($shape->properties->conditiontype) {
        case 'Expression':
            $strEval =$shape->properties->conditionexpression;
            var_dump('$strEval',$shape->properties);
            $result = eval($strEval);
            if ($result) {
                $CI->bpm->movenext($shape, $wf);
            } else {
                $data = array('conditionexpression' => $strEval, 'result' => $result);
                $CI->bpm->set_token($wf->idwf, $wf->case, $shape->resourceId, $shape->stencil->id, 'stoped', $data);
            }
            break;
        case 'None':
            $CI->bpm->movenext($shape, $wf,$data);
            break;
        case 'Default':
            $CI->bpm->movenext($shape, $wf,$data);
            break;
    }
}

?>