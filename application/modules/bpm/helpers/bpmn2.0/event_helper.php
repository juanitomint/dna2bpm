<?php

////////////////////////////////////////////////////////////////////////////////
/////////////////////   THROWING ENERIC FUNCTION  //////////////////////////////
////////////////////////////////////////////////////////////////////////////////
function run_IntermediateEventThrowing($shape, $wf, $CI) {

    $debug = (isset($CI->debug[__FUNCTION__])) ? $CI->debug[__FUNCTION__] : false;
    // $debug = true;
    if ($debug)
        echo "<h2>" . __FUNCTION__ . '</h2>';
    if ($debug) {
        echo '<h1> >> IntermediateEventThrowing:' . $shape->stencil->id . '</h1>';
    }

    $catchers_name = '';
//---Search for named eventdefinitionref and start-em if not
    if (strstr($shape->stencil->id, 'Throwing')) {
        //---Get same catching
        $catchers_name = str_replace('Throwing', 'Catching', $shape->stencil->id);
        //---Get Start

        if ($debug)
            var_dump('$catchers_name', $catchers_name);
    }
    //--If is not throwing then is Ending event
    if (strstr($shape->stencil->id, 'End')) {
        $catchers_name = str_replace('End', 'Start', $shape->stencil->id);
    }
    $catchers_byname = array();
    $catchers_byref = array();
    $catchers_bytrigger = array(); //---for start shapes
    $catchers = array();
    $trigger = $shape->properties->name;
    //---if has eventdefinitionref then trigger will be set to its value
     //---else will be it's name
    if (property_exists($shape->properties, 'eventdefinitionref')) {
        $trigger = ($shape->properties->eventdefinitionref <> '') ? $shape->properties->eventdefinitionref : $shape->properties->name;
    }
    //
    //-----get catcher by trigger for Start shapes
    //
    $triggerStart = str_replace('Intermediate', '', $shape->stencil->id);
    $triggerStart = str_replace('EventThrowing', '',$triggerStart);
    $triggerStart = 'Start' . $triggerStart . 'Event';

    $catchers_fake['childShapes'] = $CI->bpm->get_shape_byname("/^$triggerStart$/", $wf);
    $catchers_bytrigger = $CI->bpm->get_shape_byprop(array(
        'trigger' => 'Message',
        'name' => $trigger
        ), 
            $catchers_fake, //---where to look
            array($shape->resourceId) //---exclude self
    );
    
    //---get catchers of same type so signals doesn't mix up
    if ($trigger <> '' and $catchers_name <> '') {
    //---get all  catching of type $catchers_name
        $catchers_byname['childShapes'] = $CI->bpm->get_shape_byname(
                $catchers_name, $wf); 
        //---now search for same name into them
        $catchers_byname = $CI->bpm->get_shape_byprop(array('name' => $trigger), $catchers_byname, array($shape->resourceId));//---exclude self
    }
    //---search for multiple
    //---get all multiple catching
    $catchers_multiple['childShapes'] = $CI->bpm->get_shape_byname('MultipleEventCatching', $wf);
    //---search for eventdefinitionref to have $trigger into it
    //---but only search in multiple events found
    $catchers_multiple = $CI->bpm->get_shape_byprop(array('eventdefinitionref' => $trigger), $catchers_multiple);

    $catchers_byname = array_merge($catchers_byname, $catchers_multiple);

    if($shape->properties->name<>'')
        $catchers_byref = $CI->bpm->get_shape_byprop(array('messageref' => $shape->properties->name), $wf);
    
    //---get catcher by messageref
    if ($shape->stencil->id == 'Task') {
        if ($shape->properties->messageref <> '') {
            $catchers_byref = $CI->bpm->get_shape_byprop(array('messageref' => $shape->properties->messageref), $wf,array($shape->resourceId));
            $catchers_byname = $CI->bpm->get_shape_byprop(array('name' => $shape->properties->messageref), $wf,array($shape->resourceId));
        }
//
        for ($i = 0; $i < count($catchers_byref); $i++) {
//---4 tasks
            if ($catchers_byref[$i]->stencil->id == 'Task') {
//---clean-up Recive tasks
                $type = $catchers_byref[$i]->properties->tasktype;
                if ($catchers_byref[$i]->properties->tasktype !== 'Receive')
                    $catchers_byref[$i] = null;
            } else {
//---it's an event
                if ($catchers_byref[$i]->stencil->id !== $catchers_name)
                    $catchers_byref[$i] = null;
            }
//
        }
        $catchers_byref = array_filter($catchers_byref);
    }

    if ($debug) {
        var_dump2('$catchers_byname', $catchers_byname, '$catchers_byref', $catchers_byref, '$catchers_bytrigger', $catchers_bytrigger);
    }
    
    $catchers = array_merge($catchers_byname, $catchers_byref, $catchers_bytrigger);
    if ($debug) {
        echo "Has:" . count($catchers) . ' catchers<br/>';
        var_dump($catchers);
    }

//------ ADVANCE this shape-----------------------------------------//
    $CI->bpm->movenext($shape, $wf, array('name' => $shape->properties->name));
    //---Process Catchers now
    foreach ($catchers as $catcher) {
//var_dump2('$catcher->properties->name == $shape->properties->name', $catcher->properties->name == $shape->properties->name);
        $launch_catcher = false;
        $token = $CI->bpm->get_token($wf->idwf, $wf->case, $catcher->resourceId);
//----if token not exist and is not already canceled then start it as pending
        if (isset($token['status'])) {
            //var_dump2($token['status']);
            switch ($token['status']) {
                /*
                 * @todo make several tokens for each run
                  case 'canceled':
                  break;

                  case 'finished':
                  break;
                 */
                default:
                    $launch_catcher = true;
            }//---end switch
        } else {
//---Token does not exist so fire the catcher
            $launch_catcher = true;
        } //---end if isset $token['status']
        if ($debug)
            var_dump2('launch_catcher', $launch_catcher);
//---take action
        if ($launch_catcher) {
            // $CI->bpm->set_token($wf->idwf, $wf->case, $catcher->resourceId, $catcher->stencil->id, 'pending');
            if ($debug) {
                echo '>>> Launching:' . $catcher->properties->name .':'.$catcher->stencil->id . '<br>';
                //var_dump2($catcher);
            }
            run_IntermediateEventCatching($catcher, $wf, $CI);
        }
    }//---end foreach catcher
}

///////////////////////////////////////////////////////////////////////////////////////////////////////////
/////////////////////   CATCHING GENERIC FUNCTION  /////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////////////////////////
function run_IntermediateEventCatching($shape, $wf, &$CI) {

    $debug = (isset($CI->debug[__FUNCTION__])) ? $CI->debug[__FUNCTION__] : false;
    // $debug=true;
    if ($debug)
        echo "<h2>" . __FUNCTION__ . '</h2>';
    if ($debug) {
        echo '<h1> << IntermediateEventCatching:' . $shape->stencil->id .' '.$shape->properties->name. '</h1>';
    }
    $token = $CI->bpm->get_token($wf->idwf, $wf->case, $shape->resourceId);
// //---if token already canceled then exit
//     if ($token['status'] == 'canceled')
//         return;
    $inbound_flow = array();
///----used to find by name
    $throwers_name = array();
//---user to find by messageref
    $throwers_ref = array();
//---all throwers
    $throwers = array();
//---get throwers by flow
    $inbound = $CI->bpm->get_inbound_shapes($shape->resourceId, $wf);
//---set trigger to a message type    
    $trigger='IntermediateMessageEventThrowing';
//---search thrower 4 events Catching/Throwing
    $event_name =(property_exists($shape->properties,'messageref') && $shape->properties->messageref) ? $shape->properties->messageref: $shape->properties->name;
    
    
    if (strstr($shape->stencil->id, 'Catching')) {
        $trigger = str_replace('Catching', 'Throwing', $shape->stencil->id);
    }
//----get throwers searching by same name as this shape
    if ($trigger <> '') {
        //---make a fake $wf much smaller to search into
        $throwers_ref['childShapes'] = $CI->bpm->get_shape_byname("/^$trigger$/", $wf);
        if($debug)
            echo 'searching for:'.$trigger.'<br/>';
        $throwers_ref = $CI->bpm->get_shape_byprop(array('name' => $event_name), $throwers_ref);
//----clean up throwers
        $throwers_ref = array_filter($throwers_ref);
    }
//---search thrower shape 4 start/end
    if (strstr($shape->stencil->id, 'Start')) {
        $trigger = str_replace('Start', 'End', $shape->stencil->id);
    }

    if($debug) 
        echo "<h2>Event name is: $event_name</h2><h2>Trigger name is: $trigger</h2>";
//----get throwers searching by same name as this shape
    if ($trigger <> '') {
        //---make a fake $wf much smaller to search into
        $throwers_name['childShapes'] = $CI->bpm->get_shape_byname("/^$trigger$/", $wf);
        if($debug)
            echo 'searching for:'.$trigger.'<br/>';
        $throwers_name = $CI->bpm->get_shape_byprop(array('name' => $event_name), $throwers_name);
//----clean up throwers
        $throwers_name = array_filter($throwers_name);
    }

//----if mesageRef is defined then use it
    if ($shape->stencil->id == 'Task') {
        if ($shape->properties->messageref <> '') {
//---add those which messageref match
            $throwers_ref = $CI->bpm->get_shape_byprop(array('messageref' => $shape->properties->messageref), $wf);
//----clean up throwers
            for ($i = 0; $i < count($throwers_ref); $i++) {
//---4 tasks
                if ($throwers_ref[$i]->stencil->id == 'Task') {
//---clean-up Recive tasks
                    if ($throwers_ref[$i]->properties->tasktype !== 'Send')
                        $throwers_ref[$i] = null;
                } else {
//---it's an event
                    if ($throwers_ref[$i]->stencil->id !== $trigger)
                        $throwers_ref[$i] = null;
                }
            }

            $throwers_ref = array_filter($throwers_ref);
        }
    }
//----merge by name & by ref
    $throwers = array_merge($throwers_name, $throwers_ref);
    if($debug){
        echo 'has: '.count($throwers).'<br/>';
        // var_dump2($throwers);
        
    }
//---seems like parallel gateway (must wait 4 all before move)
//---check if all throwers  or inbound has finished
//---Same as parallel gateway
    $has_finished_flow = true;
    $has_finished_thrower = (count($throwers)>0) ? false : true;
    $has_finished_thrower = false;
    
    $is_normal_flow = false;
    $is_boundary_event = false;
    $is_conditional_event=false;
//----handle BOUNDARY and NORMAL FLOW different
//---1st analyze if it's normal or boundary
    if (count($inbound) == 1) {
        $inshape = $inbound[0];
        
        if (in_array($inshape->stencil->id, array('Task', 'Subprocess'))) {
//---it's an event attached to a task
            $is_boundary_event = true;
            
        } else {
//---Assumes is normal flow
            $is_normal_flow = true;
        }
    } else {
//---can't be zero so it's normal flow
        $is_normal_flow = true;
    }
//---only mark finished if all inbound finished
    if ($is_normal_flow) {
        foreach ($inbound as $inshape) {
            $token = $CI->bpm->get_token($wf->idwf, $wf->case, $inshape->resourceId);
//if ($inshape->stencil->id == 'SequenceFlow' or $inshape->stencil->id == 'MessageFlow') {
            if ($inshape->stencil->id == 'MessageFlow') {
                if ($token['status'] !== 'finished') {
                    $has_finished_flow = false;
                }
                if ($debug) {
                    echo "Checking inbound:" . $inshape->stencil->id . ':' . $inshape->resourceId . ':' . $token['status'] . '<br/>';
                }
            }
        }
    }

//---Check if any thrower has finished
    foreach ($throwers as $thrower) {
        $token = $CI->bpm->get_token($wf->idwf, $wf->case, $thrower->resourceId);
        if ($token['status'] == 'finished') {
            $has_finished_thrower = true;
        }

        if ($debug) {
            echo "Checking Thrower:" . $thrower->stencil->id . ':' . $thrower->resourceId . ':' . $token['status'] . '<br/>';
        }
    }

//---What to do if is a boundary event
    if ($is_boundary_event) {
        $inshape = $inbound[0];
        $inshape_token = $CI->bpm->get_token($wf->idwf, $wf->case, $inshape->resourceId);
//---check whether the boundary activity has finished or not
        if ($inshape_token['status'] !== 'finished') {
            if ($shape->properties->boundarycancelactivity == true) {
                if ($debug)
                    echo "*** Canceling Task:".$inshape->stencil->id.' ::' . $inshape->properties->name . '<hr/>';

                $data = array('canceledBy' => $shape->resourceId, 'canceledName' => $shape->properties->name);
                //---check if is a Subprocess then cancel all included activities
                //@todo dive deeper
                if (isset($inshape->childShapes)) {
                    foreach ($inshape->childShapes as $child) {
                        $child_token = $CI->bpm->get_token($wf->idwf, $wf->case, $child->resourceId);
                        if ($child_token['status'] !== 'finished') {
                            $CI->bpm->set_token($wf->idwf, $wf->case, $child->resourceId, $child->stencil->id, 'canceled', $data);
                        }
                    }
                }
//---------------
                $CI->bpm->set_token($wf->idwf, $wf->case, $inshape->resourceId, $inshape->stencil->id, 'canceled', $data);
                $CI->bpm->movenext($shape, $wf);
                return;
            }
        } else {
            if ($debug)
                echo "*** Canceling Event:" . $shape->properties->name . '<hr/>';

//---if parent task has finished then cancel catching token
            $has_finished_flow = false;
            $has_finished_thrower = false;
            $data = array('canceledBy' => $inshape->resourceId, 'canceledName' => $inshape->properties->name);
            $CI->bpm->set_token($wf->idwf, $wf->case, $shape->resourceId, $shape->stencil->id, 'canceled', $data);
            return;
        }
    }
//---make condition merging 2 previous conditions
    $has_finished = $has_finished_flow && $has_finished_thrower;
    if ($debug)
        var_dump2('is_normal_flow', $is_normal_flow, 'is_boundary_event', $is_boundary_event, 'has_finished_flow:', $has_finished_flow, 'has_finished_thrower:', $has_finished_thrower, 'has_finished:', $has_finished);
//---If the event is not interrupting then move forward
    if (property_exists($shape->properties, 'isinterrupting'))
        $has_finished = $has_finished || !$shape->properties->isinterrupting;
//----Move next if has finished otherwise keep Waiting
    if ($has_finished) {
        if ($debug)
            echo '<h1>HAS FINISHED TRUE</h1>';
        //---Experimental:: set pending if conditional
        $previous_shapes=$CI->bpm->get_previous($shape->resourceId, $wf);
               $previous=$previous_shapes[0];
        //----check if it fires EventBasedGateway
        //----2do message bus emmit/push message finished
        if(in_array($previous->stencil->id,array('EventbasedGateway'))){
        	
            $CI->bpm->set_token($wf->idwf, $wf->case, $previous->resourceId, $previous->stencil->id, 'pending');
            var_dump('previous -> pending');exit;
        }
        $CI->bpm->movenext($shape, $wf);
        //----cancel boundary if exists
        
        
        
    } else {
        if ($debug)
            echo '<h1>FALSE</h1>';
        $data['name'] = $shape->properties->name;
        $CI->bpm->set_token($wf->idwf, $wf->case, $shape->resourceId, $shape->stencil->id, 'waiting', $data);
    }
}

////////////////////////////////////////////////////////////////////////////////
//////////////////    CATCHING EVENTS    ///////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////
function run_IntermediateTimerEvent($shape, $wf, $CI) {
    $debug = (isset($CI->debug[__FUNCTION__])) ? $CI->debug[__FUNCTION__] : false;
    // $debug=true;
    if ($debug)
        echo "<h2>" . __FUNCTION__ . '</h2>';
    $token = $CI->bpm->get_token($wf->idwf, $wf->case, $shape->resourceId);
    $data=$CI->bindObjectToArray($CI->data);
    // var_dump($data);
//---1st arrive to this timer set the trigger condition
    if ($token['status'] == 'pending') {
        $date_str=$CI->parser->parse_string($shape->properties->name, $data, true, true);
        if($debug)
            echo "Parsed str: ".$date_str.'<hr/>';
        if (($timestamp = strtotime($date_str)) === false) {
//----Raise Error
            show_error("cannot convert". $date_str." to a valid time (". $shape->properties->name.")");
        } else {
            $trigger = date('Y-m-d H:i:s', strtotime($date_str));
            if ($debug) 
            echo 'trigger:'.$shape->properties->name.' -> '.date('Y-m-d H:i:s',time()).' -> '.$date_str.' -> '.$trigger.'</br>';
            
        }
        $CI->bpm->set_token($wf->idwf, $wf->case, $shape->resourceId, $shape->stencil->id, 'waiting', array('trigger' => $trigger));
        $token = $CI->bpm->get_token($wf->idwf, $wf->case, $shape->resourceId);
    }
//----------------------------------------------------------
//---Eval trigger condition while is waiting...
    switch ($token['status']) {
        case 'waiting':
            if ($debug)
                var_dump2(date('Y-m-d H:i:s', mktime()), $token['trigger'], mktime() > strtotime($token['trigger']));

            if (time() >= strtotime($token['trigger'])) {
            	//----check if it fires EventBasedGateway
               
               $previous_shapes=$CI->bpm->get_previous($shape->resourceId, $wf);
               $previous=$previous_shapes[0];
               
               if(in_array($previous->stencil->id,array('EventbasedGateway'))){
               $CI->bpm->set_token($wf->idwf, $wf->case, $previous->resourceId, $previous->stencil->id, 'pending');
               }
                
                run_IntermediateEventCatching($shape, $wf, $CI);
                $CI->bpm->movenext($shape, $wf);
            }
            break;
    }

}

function run_IntermediateMessageEventCatching($shape, $wf, $CI) {
    $uno = run_IntermediateEventCatching($shape, $wf, $CI);
}

function run_IntermediateEscalationEvent($shape, $wf, $CI) {

    $debug = (isset($CI->debug[__FUNCTION__])) ? true : false;
    if ($debug)
        echo "<h2>" . __FUNCTION__ . '</h2>';
}

function run_IntermediateConditionalEvent($shape, $wf, $CI) {

    $debug = (isset($CI->debug[__FUNCTION__])) ? true : false;
    if ($debug)
        echo "<h2>" . __FUNCTION__ . '</h2>';
}

function run_IntermediateLinkEventCatching($shape, $wf, $CI) {

    $debug = (isset($CI->debug[__FUNCTION__])) ? true : false;
    if ($debug)
        echo "<h2>" . __FUNCTION__ . '</h2>';
    $CI->bpm->movenext($shape, $wf);
}

function run_IntermediateErrorEvent($shape, $wf, $CI) {

    $debug = (isset($CI->debug[__FUNCTION__])) ? true : false;
    if ($debug)
        echo "<h2>" . __FUNCTION__ . '</h2>';
}

function run_IntermediateCancelEvent($shape, $wf, $CI) {

    $debug = (isset($CI->debug[__FUNCTION__])) ? true : false;
    if ($debug)
        echo "<h2>" . __FUNCTION__ . '</h2>';
}

function run_IntermediateCompensationEventCatching($shape, $wf, $CI) {

    $debug = (isset($CI->debug[__FUNCTION__])) ? true : false;
    if ($debug)
        echo "<h2>" . __FUNCTION__ . '</h2>';
}

function run_IntermediateSignalEventCatching($shape, $wf, $CI) {

    $debug = (isset($CI->debug[__FUNCTION__])) ? true : false;
    if ($debug)
        echo "<h2>" . __FUNCTION__ . '</h2>';
//----4 signals Check in db and search for signal
//----Signals are persistent and may been fired several times
    $signals = $CI->bpm->get_signal_thrower($shape->properties->name);

    if (count($signals)) {
        $uno = run_IntermediateEventCatching($shape, $wf, $CI);
    } else {
        $data['name'] = $shape->properties->name;
        $CI->bpm->set_token($wf->idwf, $wf->case, $shape->resourceId, $shape->stencil->id, 'waiting', $data);
    }
}

function run_IntermediateMultipleEventCatching($shape, $wf, $CI) {

    $debug = (isset($CI->debug[__FUNCTION__])) ? true : false;
    if ($debug)
        echo "<h2>" . __FUNCTION__ . '</h2>';
}

function run_IntermediateParallelMultipleEventCatching($shape, $wf, $CI) {

    $debug = (isset($CI->debug[__FUNCTION__])) ? true : false;
    $debug = true;
    if ($debug)
        echo "<h2>" . __FUNCTION__ . '</h2>';
//---this catching event is like parallel inclusive gateway
//---all trigers defined in eventdefinitionref
//---trigers are referenced by a list of comma separated names
//---or connected implicitly with message flow lines
    $token = $CI->bpm->get_token($wf->idwf, $wf->case, $shape->resourceId);
//---if token already canceled then exit
    // if ($token['status'] == 'canceled')
    //     return;
    $inbound_flow = array();
///----used to find by name
    $throwers_name = array();
//---user to find by messageref
    $throwers_ref = array();
//---all throwers
    $throwers = array();
//---get throwers by flow
    $inbound = $CI->bpm->get_inbound_shapes($shape->resourceId, $wf);
    $throwers = array();
//---get all throwes with regexp
    $throwers['childShapes'] = $CI->bpm->get_shape_byname('Throwing$', $wf);
//----get throwers searching by eventdefinitionref
    if ($shape->properties->eventdefinitionref <> '') {
        $triggers = explode(',', $shape->properties->eventdefinitionref);
        foreach ($triggers as $trigger) {
            //get all shapes with same name as $trigger
            //--but search within throwers only, not the whole diagram
            $throwers_byname = $CI->bpm->get_shape_byprop(array('name' => "/^$trigger$/"), $throwers);
            //---get triggers by eventdefinitionref
            $throwers_byED = $CI->bpm->get_shape_byprop(array('eventdefinitionref' => "/^$trigger$/"), $throwers);
            $throwers_name = array_merge($throwers_name, $throwers_byED);
        }
//---cleaned up throwers
        $throwers = $throwers_name;
    } else {
//---show error
        show_error('Intermediate Parallel Multiple EventCatching:' . $shape->properties->name . ' need to have eventdefinitionref');
    }
//----Get Throwers by Eventdefinition Ref
//----clean up throwers
    $throwers_name = array_filter($throwers_name);
    $throwers_name = notme($throwers_name, $shape->resourceId);
//---seems like parallel gateway (must wait 4 all before move)
//---check if all throwers  or inbound has finished
//---Same as parallel gateway
    $has_finished_flow = true;
    $has_finished_thrower = (count($throwers)) ? false : true;
    $is_normal_flow = false;
    $is_boundary_event = false;
//----handle BOUNDARY and NORMAL FLOW different
//---1st analyze if it's normal or boundary
    if (count($inbound) == 1) {
        $inshape = $inbound[0];
        if (in_array($inshape->stencil->id, array('Task', 'Subprocess'))) {
//---it's an event attached to a task
            $is_boundary_event = true;
        } else {
//---Assumes is normal flow
            $is_normal_flow = true;
        }
    } else {
//---can't be zero so it's normal flow
        $is_normal_flow = true;
    }
//---only mark finished if all inbound finished
    if ($is_normal_flow) {
        foreach ($inbound as $inshape) {
            $token = $CI->bpm->get_token($wf->idwf, $wf->case, $inshape->resourceId);
//if ($inshape->stencil->id == 'SequenceFlow' or $inshape->stencil->id == 'MessageFlow') {
            if ($inshape->stencil->id == 'MessageFlow') {
                if ($token['status'] !== 'finished') {
                    $has_finished_flow = false;
                }
                if ($debug) {
                    echo "Checking inbound:" . $inshape->stencil->id . ':' . $inshape->resourceId . ':' . $token['status'] . '<br/>';
                }
            }
        }
    }

//---Check that all throwers has finished
    $has_all = $throwers_name;
    foreach ($throwers as $thrower) {
        $has_all[$thrower->resourceId] = 0;
        $token = $CI->bpm->get_token($wf->idwf, $wf->case, $thrower->resourceId);
        if ($token['status'] == 'finished') {
            $has_all[$thrower->resourceId] = 1;
        }

        if ($debug) {
            echo "Checking Thrower:" . $thrower->stencil->id . ':' . $thrower->resourceId . ':' . $token['status'] . '<br/>';
        }
    }
//---$has_finished_thrower only if all required are finished
    if (count($throwers) == array_sum($has_all))
        $has_finished_thrower = true;

//---What to do if is a boundary event
    if ($is_boundary_event) {
        $inshape = $inbound[0];
        $token = $CI->bpm->get_token($wf->idwf, $wf->case, $inshape->resourceId);
//---check whether the boundary activity has finished or not
        if ($token['status'] !== 'finished') {
            if ($shape->properties->boundarycancelactivity == true) {
                if ($debug)
                    echo "*** Canceling Task:" . $inshape->properties->name . '<hr/>';

                $data = array('canceledBy' => $shape->resourceId, 'canceledName' => $shape->properties->name);
//---check if is a Subprocess then cancel all included activities
                if (isset($inshape->childShapes)) {
                    foreach ($inshape->childShapes as $child) {
                        $token = $CI->bpm->get_token($wf->idwf, $wf->case, $child->resourceId);
                        if ($token['status'] !== 'finished') {
                            $CI->bpm->set_token($wf->idwf, $wf->case, $child->resourceId, $child->stencil->id, 'canceled', $data);
                        }
                    }
                }
//---------------

                $CI->bpm->set_token($wf->idwf, $wf->case, $inshape->resourceId, $inshape->stencil->id, 'canceled', $data);
                $CI->bpm->movenext($shape, $wf);
                return;
            }
        } else {
            if ($debug)
                echo "*** Canceling Event:" . $shape->properties->name . '<hr/>';

//---if parent task has finished then cancel catching token
            $has_finished_flow = false;
            $has_finished_thrower = false;
            $data = array('canceledBy' => $inshape->resourceId, 'canceledName' => $inshape->properties->name);
            $CI->bpm->set_token($wf->idwf, $wf->case, $shape->resourceId, $shape->stencil->id, 'canceled', $data);
            return;
        }
    }
//---make condition merging 2 previous conditions
    $has_finished = $has_finished_flow && $has_finished_thrower;
    if ($debug)
        var_dump2('is_normal_flow', $is_normal_flow, 'is_boundary_event', $is_boundary_event, 'has_finished_flow:', $has_finished_flow, 'has_finished_thrower:', $has_finished_thrower, 'has_finished:', $has_finished);
//---If the event is not interrupting then move forward
    if (property_exists($shape->properties, 'isinterrupting'))
        $has_finished = $has_finished || !$shape->properties->isinterrupting;
//----Move next if has finished otherwise keep Waiting
    if ($has_finished) {
        if ($debug)
            echo '<h1>HAS FINISHED TRUE</h1>';
//----cancel boundary if exists
        if ($is_boundary_event) {
            $data = array('canceledBy' => $inshape->resourceId, 'canceledName' => $inshape->properties->name);
            $CI->bpm->set_token($wf->idwf, $wf->case, $inbound[0]->resourceId, $inbound[0]->stencil->id, 'canceled', $data);
        }
        $CI->bpm->movenext($shape, $wf);
    } else {
        if ($debug)
            echo '<h1>FALSE</h1>';
        $data['name'] = $shape->properties->name;
        $CI->bpm->set_token($wf->idwf, $wf->case, $shape->resourceId, $shape->stencil->id, 'waiting', $data);
    }
}

////////////////////////////////////////////////////////////////////////////////
/////////////////////    TRHOWING EVENTS    ////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////

function run_IntermediateMessageEventThrowing($shape, $wf, $CI) {
    run_IntermediateEventThrowing($shape, $wf, $CI);
}

function run_IntermediateEscalationEventThrowing($shape, $wf, $CI) {

    $debug = (isset($CI->debug[__FUNCTION__])) ? true : false;
    if ($debug)
        echo "<h2>" . __FUNCTION__ . '</h2>';
}

function run_IntermediateLinkEventThrowing($shape, $wf, $CI) {
    $debug = (isset($CI->debug[__FUNCTION__])) ? true : false;
    //$debug=true;
    if ($debug)
        echo "<h2>" . __FUNCTION__ . '</h2>';
    if($shape->properties->entry){
        //---off page link
        //---first run events
        run_IntermediateEventThrowing($shape, $wf, $CI);
        $to_idwf=$shape->properties->entry;
        if($debug) echo "Cloning: ".$wf->idwf.' to:'.$to_idwf.'<br/>';
        $mywf = $CI->bpm->model_exists($to_idwf);
        
        if($mywf){
            
            
            
            $clone=$CI->bpm->clone_case($wf->idwf, $to_idwf, $wf->case);
            // if($clone){
            //     //----Start
            //     $CI->Startcase('model', $to_idwf, $wf->case);
            // } else {
                //----Run
                $mywf ['data'] ['idwf'] = $to_idwf;
                $mywf ['data'] ['case'] = $wf->case;
                $mywf ['data'] ['folder'] = $mywf ['folder'];
                $to_wf = bindArrayToObject($mywf ['data']);
                //---1st try to get catcher links
                if($debug) echo "run_IntermediateEventThrowing<br/>";
                run_IntermediateEventThrowing($shape, $to_wf, $CI);
                if($debug) echo "Runing: ".$to_wf->idwf.'<br/>';
                $CI->Run('model', $to_idwf, $wf->case);
            // }
            
        }
    } else{
        //----same page link
        run_IntermediateEventThrowing($shape, $wf, $CI);
    }
}

function run_IntermediateCompensationEventThrowing($shape, $wf, $CI) {

    $debug = (isset($CI->debug[__FUNCTION__])) ? true : false;
    if ($debug)
        echo "<h2>" . __FUNCTION__ . '</h2>';
}

function run_IntermediateSignalEventThrowing($shape, $wf, $CI) {

    $debug = (isset($CI->debug[__FUNCTION__])) ? true : false;
    if ($debug)
        echo "<h2>" . __FUNCTION__ . '</h2>';
    run_IntermediateMessageEventThrowing($shape, $wf, $CI);

    $CI->do_signals($shape->properties->name);
}

function run_IntermediateMultipleEventThrowing($shape, $wf, $CI) {

    $debug = (isset($CI->debug[__FUNCTION__])) ? true : false;
    if ($debug)
        echo "<h2>" . __FUNCTION__ . '</h2>';
}

function run_($shape, $wf, $CI) {

    $debug = (isset($CI->debug[__FUNCTION__])) ? true : false;
    if ($debug)
        echo "<h2>" . __FUNCTION__ . '</h2>';
}

function notme($shapes, $resourceId) {
    foreach ($shapes as $key => $shape) {
        if ($shape->resourceId == $resourceId)
            $shapes[$key] = null;
    }
    return array_filter($shapes);
}

?>
