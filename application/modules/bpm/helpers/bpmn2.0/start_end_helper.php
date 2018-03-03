<?php

////////////////////////////////////////////////////////////////////////////////
////////////////////////////    START EVENTS    ////////////////////////////////
////////////////////////////////////////////////////////////////////////////////
function run_StartNoneEvent($shape, $wf, $CI) {

    $debug = (isset($CI->debug[__FUNCTION__])) ? true : false;
    if ($debug)
        echo "<h2>" . __FUNCTION__ . '</h2>';
    //----Set status 4 Case
    $CI->bpm->update_case($wf->idwf, $wf->case, array('status' => 'open'));
    //---this function only fowards the process and return nothing
    $CI->bpm->movenext($shape, $wf);
}

//   This function will get started by the eventThrowing handler
//   will probably hav an incoming message flow
function run_StartMessageEvent($shape, $wf, $CI) {


    $debug = (isset($CI->debug[__FUNCTION__])) ? true : false;
    if ($debug)
        echo "<h2>" . __FUNCTION__ . '</h2>';
    //run_IntermediateEventCatching($shape, $wf,$CI);
    //---this function only fowards the process and return nothing
    $CI->bpm->movenext($shape, $wf);
}

function run_StartTimerEvent($shape, $wf, $CI) {

    $debug = (isset($CI->debug[__FUNCTION__])) ? true : false;
    if ($debug)
        echo "<h2>" . __FUNCTION__ . '</h2>';
    run_IntermediateTimerEvent($shape, $wf, $CI);
}

function run_StartEscalationEvent($shape, $wf, $CI) {

    $debug = (isset($CI->debug[__FUNCTION__])) ? true : false;
    if ($debug)
        echo "<h2>" . __FUNCTION__ . '</h2>';
    $CI->bpm->movenext($shape, $wf);
}

function run_StartConditionalEvent($shape, $wf, $CI) {

    $debug = (isset($CI->debug[__FUNCTION__])) ? true : false;
    if ($debug)
        echo "<h2>" . __FUNCTION__ . '</h2>';
    $CI->bpm->movenext($shape, $wf);
}

function run_StartErrorEvent($shape, $wf, $CI) {

    $debug = (isset($CI->debug[__FUNCTION__])) ? true : false;
    if ($debug)
        echo "<h2>" . __FUNCTION__ . '</h2>';
    $CI->bpm->movenext($shape, $wf);
}

function run_StartCompensationEvent($shape, $wf, $CI) {

    $debug = (isset($CI->debug[__FUNCTION__])) ? true : false;
    if ($debug)
        echo "<h2>" . __FUNCTION__ . '</h2>';
    $CI->bpm->movenext($shape, $wf);
}

function run_StartSignalEvent($shape, $wf, $CI) {

    $debug = (isset($CI->debug[__FUNCTION__])) ? true : false;
    if ($debug)
        echo "<h2>" . __FUNCTION__ . '</h2>';
    $CI->bpm->movenext($shape, $wf);
}

function run_StartMultipleEvent($shape, $wf, $CI) {

    $debug = (isset($CI->debug[__FUNCTION__])) ? true : false;
    if ($debug)
        echo "<h2>" . __FUNCTION__ . '</h2>';
    $CI->bpm->movenext($shape, $wf);
}

function run_StartParallelMultipleEvent($shape, $wf, $CI) {

    $debug = (isset($CI->debug[__FUNCTION__])) ? true : false;
    if ($debug)
        echo "<h2>" . __FUNCTION__ . '</h2>';
    $CI->bpm->movenext($shape, $wf);
}

//////////////////////////////////////////////////////////////////////////////
////////////////            END EVENTS      //////////////////////////////////
//////////////////////////////////////////////////////////////////////////////

function run_EndNoneEvent($shape, $wf, $CI, $moveForward = true) {

    $debug = (isset($CI->debug[__FUNCTION__])) ? true : false;
    // $debug = true;
    if ($debug)
        echo "<h2>" . __FUNCTION__ . '</h2>';
//----don't forward tokens if has events
    if ($moveForward)
        $CI->bpm->movenext($shape, $wf);
    //---check if parent is present
    $parent_resourceId = property_exists($shape->properties, 'subproc_parent') ? $shape->properties->subproc_parent : null;
    if ($parent_resourceId) {
        $parent = isset($parent_resourceId) ? $CI->bpm->get_shape($parent_resourceId, $wf) : null;
    } else {
        $parent = $CI->bpm->get_shape_parent($shape->resourceId, $wf);
    }

    if ($debug)
        var_dump('parent', $parent);
    if ($parent) {
        switch ($parent->stencil->id) {
            case 'Subprocess':
                if ($debug)
                    echo '<h3>Finish Expanded Subprocess</h3>';
                //---Finish the process
                $CI->bpm->movenext($parent, $wf);
                break;
            //----embedded subproces only
            case 'CollapsedSubprocess':
                if ($debug)
                    echo '<h3>Finish CollapsedSubprocess</h3>';
                //---Finish the process
                $CI->bpm->movenext($parent, $wf);
                break;

            default:
                break;
        }
    }
    //----Set status 4 Case
    //---close process if all end events have been finished (or canceled)
    $filter=array (
        'case'=>$wf->case,
        'idwf'=>$wf->idwf,
        'status'=> array('$in'=>array('user', 'waiting', 'pending')),
        );

    $active_tokens = $CI->bpm->get_tokens_byFilter_count($filter); 

    if ($active_tokens == 0) {
        $CI->bpm->update_case($wf->idwf, $wf->case, array(
            'status' => 'closed',
            'closer' => $shape->resourceId,
            'checkoutdate' => date('Y-m-d H:i:s')
                )
        );
    }
    //----update parent case if any
    $mycase = $CI->bpm->get_case($wf->case, $wf->idwf);
    if (isset($mycase['data']['parent'])) {
        $parent = $mycase['data']['parent'];
        // run_post($model, $idwf, $case, $resourceId)
        //echo '/bpm/engine/run_post/model/' . $parent['idwf'] . '/' . $parent['case'] . '/' . $parent['token']['resourceId'];
        if($debug){
            echo "RUN PARENT";
        }
        //$CI->run('model', $parent['idwf'], $parent['case'], $parent['token']['resourceId']);
        redirect($CI->base_url.'bpm/engine/run/model/'.$parent['idwf'].'/'. $parent['case']);
        // $CI->run_post('model', $parent['idwf'], $parent['case'], $parent['token']['resourceId']);
    }
}

function run_EndMessageEvent($shape, $wf, $CI) {

    $debug = (isset($CI->debug[__FUNCTION__])) ? true : false;
    if ($debug)
        echo "<h2>" . __FUNCTION__ . '</h2>';
    //---call the event throwing
    run_IntermediateEventThrowing($shape, $wf, $CI);
    //then finish like none
    run_EndNoneEvent($shape, $wf, $CI, false);
}

function run_EndEscalationEvent($shape, $wf, $CI) {

    $debug = (isset($CI->debug[__FUNCTION__])) ? true : false;
    if ($debug)
        echo "<h2>" . __FUNCTION__ . '</h2>';
    run_IntermediateEventThrowing($shape, $wf, $CI);
    //then finish like none
    run_EndNoneEvent($shape, $wf, $CI);
}

function run_EndErrorEvent($shape, $wf, $CI) {

    $debug = (isset($CI->debug[__FUNCTION__])) ? true : false;
    if ($debug)
        echo "<h2>" . __FUNCTION__ . '</h2>';
    run_IntermediateEventThrowing($shape, $wf, $CI);
    //---Update case Status
    $CI->bpm->update_case($wf->idwf, $wf->case, array('status' => 'canceled'));
    //---then move next
}

function run_EndCancelEvent($shape, $wf, $CI) {

    $debug = (isset($CI->debug[__FUNCTION__])) ? true : false;
    if ($debug)
        echo "<h2>" . __FUNCTION__ . '</h2>';
    //---Update case Status
    // $CI->bpm->update_case($wf->idwf, $wf->case, array('status' => 'canceled'));
    $CI->bpm->movenext($shape, $wf);
    $CI->break_on_next = true;
}

function run_EndCompensationEvent($shape, $wf, $CI) {

    $debug = (isset($CI->debug[__FUNCTION__])) ? true : false;
    if ($debug)
        echo "<h2>" . __FUNCTION__ . '</h2>';
    run_IntermediateEventThrowing($shape, $wf, $CI);
    //then finish like none
    run_EndNoneEvent($shape, $wf, $CI);
}

function run_EndSignalEvent($shape, $wf, $CI) {

    $debug = (isset($CI->debug[__FUNCTION__])) ? true : false;
    if ($debug)
        echo "<h2>" . __FUNCTION__ . '</h2>';
    run_IntermediateEventThrowing($shape, $wf, $CI);
    //then finish like none
    run_EndNoneEvent($shape, $wf, $CI);
}

function run_EndMultipleEvent($shape, $wf, $CI) {

    $debug = (isset($CI->debug[__FUNCTION__])) ? true : false;
    if ($debug)
        echo "<h2>" . __FUNCTION__ . '</h2>';
    run_IntermediateEventThrowing($shape, $wf, $CI);
    //then finish like none
    run_EndNoneEvent($shape, $wf, $CI);
}

/**
 * Terminate BPMN2.0 flow
 * @param type $shape
 * @param type $wf
 * @param type $CI
 */
function run_EndTerminateEvent($shape, $wf, $CI) {

    $debug = (isset($CI->debug[__FUNCTION__])) ? true : false;
    if ($debug)
        echo "<h2>" . __FUNCTION__ . '</h2>';
    /**
     * @todo Cancel all pending tasks and close
     */
        $filter=array (
        'case'=>$wf->case,
        'idwf'=>$wf->idwf,
        'status'=> array('$in'=>array('user', 'waiting', 'pending','open')),
        );

    $active_tokens = $CI->bpm->get_tokens_byFilter($filter); 
    foreach ($active_tokens as $token) {
        $token['status'] = 'canceled';
        $data = array('canceledBy' => $shape->resourceId, 'canceledName' => $shape->properties->name);
        $token+=$data;
        $CI->bpm->save_token($token);
        
    }

    $case = $CI->bpm->get_case($wf->case, $wf->idwf);
    $CI->bpm->archive_case($case);
    $CI->bpm->delete_case($wf->idwf,$wf->case);
    // run_EndNoneEvent($shape, $wf, $CI);
    
}
