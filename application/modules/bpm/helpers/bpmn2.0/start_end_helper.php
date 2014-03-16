<?php

////////////////////////////////////////////////////////////////////////////////
////////////////////////////    START EVENTS    ////////////////////////////////
////////////////////////////////////////////////////////////////////////////////
function run_StartNoneEvent($shape, $wf, $CI) {

    $debug = (isset($CI->debug[__FUNCTION__])) ? true : false;
    if ($debug)
        echo "<h2>" . __FUNCTION__ . '</h2>';
    //---this function only fowards the process and return nothing
    $CI->bpm->movenext($shape, $wf);
    //----Set status 4 Case
    $CI->bpm->update_case($wf->case, array('status' => 'open'));
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

    if ($debug)
        echo "<h2>" . __FUNCTION__ . '</h2>';
//----don't forward tokens if has events
    if ($moveForward)
        $CI->bpm->movenext($shape, $wf);
    //---check if parent is present
    $parent = $CI->bpm->get_shape_parent($shape->resourceId, $wf);
    if ($debug)
        var_dump('parent', $parent);
    if ($parent) {
        switch ($parent->stencil->id) {
            case 'Subprocess':
                //---Finish the process
                $CI->bpm->movenext($parent, $wf);
                break;

            case 'CollapsedSubprocess':
                //---Finish the process
                $CI->bpm->movenext($parent, $wf);
                break;

            default:
                //----Set status 4 Case
                //---close process if all end events have been finished (or canceled)
                $active_tokens = $CI->bpm->get_pending($wf->case, array('user', 'waiting', 'pending'), array());
                if ($active_tokens->count() == 0)
                    $CI->bpm->update_case($wf->case, array(
                        'status' => 'closed',
                        'checkoutdate' => date('Y-m-d H:i:s')
                            )
                    );
                //----update parent case if any
                $mycase = $CI->bpm->get_case($wf->case);
                if (isset($mycase['parent'])) {
                    $parent = $mycase['parent'];
                    // run_post($model, $idwf, $case, $resourceId)
                    //echo '/bpm/engine/run_post/model/' . $parent['idwf'] . '/' . $parent['case'] . '/' . $parent['token']['resourceId'];
                    Module::run('/bpm/engine/run_post', 'model', $parent['idwf'], $parent['case'], $parent['token']['resourceId']);
                }
                break;
        }
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
    $CI->bpm->update_case($wf->case, array('status' => 'canceled'));
    //---then move next
}

function run_EndCancelEvent($shape, $wf, $CI) {

    $debug = (isset($CI->debug[__FUNCTION__])) ? true : false;
    if ($debug)
        echo "<h2>" . __FUNCTION__ . '</h2>';
    //---Update case Status
    $CI->bpm->update_case($wf->case, array('status' => 'canceled'));
    $CI->bpm->movenext($shape, $wf);
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

function run_EndTerminateEvent($shape, $wf, $CI) {
    //---will terminate process execution and remove all data associated
    //---will delete tokens and case data.

    $debug = (isset($CI->debug[__FUNCTION__])) ? true : false;
    if ($debug)
        echo "<h2>" . __FUNCTION__ . '</h2>';
    //---TODO Cancel all pending tasks
    //---remove all tokens and remove case
    $CI->bpm->delete_case($wf->idwf, $wf->case);
}

?>