<?php

function run_Task($shape, $wf, $CI) {
    $debug = (isset($CI->debug[__FUNCTION__])) ? $CI->debug[__FUNCTION__] : false;
    //$debug = true;
    //$CI = & get_instance('Engine');
    $resourceId = $shape->resourceId;
//---set DS pointer to Data Storage
    $DS = (property_exists($CI, 'data')) ? $CI->data : null;
    if ($debug)
        echo '<H1>TASK:' . $shape->properties->name . '</H1>';
////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////
//---check 4 looptype---

    if (!property_exists($wf, 'task_run'))
        $wf->task_run = array();
    switch ($shape->properties->looptype) {
        case 'Standard':
            break;
        default:
            //only excutes task 1 time
            if (in_array($resourceId, $wf->task_run)) {
                $wf->prevent_run[] = $resourceId;
                return;
            }
            break;
    }
////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////
//---DATA LOAD--
//----add $resourceId to the run stack
    $wf->task_run[] = $resourceId;

    $data = array();
//---get case data
    $case = $CI->bpm->get_case($wf->case, $wf->idwf);
//---set initiator same as case creator.
    $CI->user->Initiator = (int) $case['iduser'];
//----Get token data
    $token = $CI->bpm->get_token($wf->idwf, $wf->case, $shape->resourceId);
//---set actual user
    $iduser = (int) $CI->idu;
    $user = $CI->user->get_user($iduser);
    $user_groups = (array) $user->group;

////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////
//---check 4 callacitivity---
    if ($shape->properties->callacitivity) {
///---Get callableelement property or use it's own name
        $callable_task_name = ($shape->properties->callableelement <> '') ? $shape->properties->callableelement : $shape->properties->name;
//---TODO make call$dataable to search other process
//----now replace shape with callable_task
        $query = array(
            'name' => $callable_task_name,
            'tasktype' => $shape->properties->tasktype,
        );

        $shapes = $CI->bpm->get_shape_byprop($query, $wf);
        //--get the 1st not callactivity in set
        foreach ($shapes as $this_shape) {
            if (!$this_shape->properties->callacitivity) {
                $shape_new = $this_shape;
                //---restore the original outgoing 4 flow sake
                //---and restore original props
                $shape_new->outgoing = $shape->outgoing;
                $shape_new->resourceId = $shape->resourceId;
                $shape_new->stencil = $shape->stencil;
                $shape_new->resources = (property_exists($shape, 'resources')) ? $shape->resources : null;
                $shape = $shape_new;
                break;
            }
        }
    }

//--------------------------------------------------
//---load data from 'transport' from previous shape
    $inbound = $CI->bpm->get_previous($resourceId, $wf);
    foreach ($inbound as $inshape) {
        $token_in = $CI->bpm->get_token($wf->idwf, $wf->case, $inshape->resourceId);
        if (isset($token_in['data'])) {
            if (isset($token_in['data']['transport'])) {
//--add transported data to my data;
                foreach ($token_in['data']['transport'] as $key => $value) {
                    $data[$key] = $value;
                }
            }
        }
    }
//--------------------------------------------------
//--------load DataInput as described---------------
//---load data from DS
    $data_in = false;
    if (property_exists($shape->properties, 'datainputset')) {
        $data_in = (property_exists($shape->properties->datainputset, 'items')) ? $shape->properties->datainputset->items : false;
    }
    if ($data_in) {
        foreach ($data_in as $item) {
            list($ds_source, $ds_item) = explode('.', $item->name);
            if ($debug)
                var_dump2('Data In', $item);
            $datain = $DS->$ds_source;
            $data[$ds_source][$ds_item] = $datain[$ds_item];
        }

        if ($debug) {
            echo '<h1>DATA-IN</h1>';
            var_dump2($data);
        }
    }
//---SAVE Data in data
    $CI->bpm->set_token($wf->idwf, $wf->case, $shape->resourceId, $shape->stencil->id, $token['status'], $data);
    if ($debug) {
        var_dump2($shape, $data);
        echo '<hr>';
    }
//---------------------------------------------------
    $data['tasktype'] = $shape->properties->tasktype;
////////////////////////////////////////////////////////////////////////////
/////////////////////// Search outgoing for timers /////////////////////////
////////////////////////////////////////////////////////////////////////////
    foreach ($shape->outgoing as $out) {
        $this_shape = $CI->bpm->get_shape($out->resourceId, $wf);
        if ($this_shape->stencil->id == 'IntermediateTimerEvent') {
            $CI->bpm->set_token($wf->idwf, $wf->case, $this_shape->resourceId, $this_shape->stencil->id, 'pending');
        }
    }
////////////////////////////////////////////////////////////////////////////
/////////////////////// SWITCH BY TASK TYPE ////////////////////////////////
////////////////////////////////////////////////////////////////////////////

    switch ($shape->properties->tasktype) {
        case 'User':
            if ($debug)
                echo "USER<br/>";
            //----ASSIGN TASK to USER / GROUP
            $CI->bpm->assign($shape, $wf);
            //----Get token data
            $token = $CI->bpm->get_token($wf->idwf, $wf->case, $shape->resourceId);
////////////////////////////////////////////////////////////////////////////
///////////////////////EVAL EXECUTION POLICY////////////////////////////////
////////////////////////////////////////////////////////////////////////////
//--by default user is not allowed to execute this task
//--except assign or group says otherwise
            $is_allowed = $CI->bpm->is_allowed($token, $user);
            if (!$is_allowed) {
                if ($debug)
                    echo "is_allowed=false<br/>";
                return;
            }

////////////////////////////////////////////////////////////////////////////

            if ($debug)
                var_dump2($shape->properties->rendering);
            $data['rendering'] = $shape->properties->rendering;

//---change status to manual (stops execution and wait 4 manual input)
            $CI->bpm->set_token($wf->idwf, $wf->case, $shape->resourceId, $shape->stencil->id, 'user', $data);
            break;
        case 'Manual':
            $CI->bpm->set_token($wf->idwf, $wf->case, $shape->resourceId, $shape->stencil->id, 'user', $data);
            break;
        case 'Script':
//----run the script
//
//--->movenext on success
            $streval = $shape->properties->script;
            $script_language = ($shape->properties->script_language) ? strtolower($shape->properties->script_language) : 'php';
// try to set data store to operation if it fails then use name

            $data_store = ($shape->properties->operationref <> '') ? $shape->properties->operationref : $shape->properties->name;
            //---define $data_store if not exists
            if (!property_exists($DS, $data_store))
                $DS->$data_store = null;
            if ($debug)
                var_dump2('$DS original:', $DS);
            if (strlen($streval)) {
                switch ($script_language) {
                    case 'php';
//---TODO sanitize EVAL----------
//--add return if not present
                        if (!strstr($streval, 'return')) {
                            $streval = 'return(' . $streval . ');';
                        }
///--ecxecute BE CAREFULL EXTREMLY DANGEROUS
                        try {
                            $DS->$data_store = eval($streval);
                        } catch (ErrorException $e) {
                            echo 'Caught exception: ', $e->getMessage(), "<br/>";
                        }
                        break;

                    case 'json':
                        $DS->$data_store = json_decode($streval);
                        break;
                }
                if ($debug)
                    var_dump2($streval, $DS->$data_store);
//---store result in case
                $case['data'][$data_store] = $DS->$data_store;
                $CI->bpm->save_case($case);
            }
            $CI->bpm->movenext($shape, $wf, $data);
            break;
        case 'Send':
            //----ASSIGN TASK to USER / GROUP
            $token = $CI->bpm->assign($shape, $wf);
            $data = $CI->bindObjectToArray($CI->data);
            $data['user'] = (array) $user;
            $data['date'] = date($CI->lang->line('dateFmt'));
            $msg['from'] = $CI->idu;
            $msg['subject'] = $CI->parser->parse_string($shape->properties->name, $data, true, true);
            $msg['body'] = $CI->parser->parse_string($shape->properties->documentation, $data, true, true);

            $msg['idwf'] = $wf->idwf;
            $msg['case'] = $wf->case;
            if ($shape->properties->properties <> '') {
                foreach ($shape->properties->properties->items as $property) {
                    $msg[$property->name] = $property->datastate;
                }
            }
            $resources = $CI->bpm->get_resources($shape, $wf, $case);
            //---if has no messageref and noone is assigned then
            //---fire a message to lane or self         
//            if (!count($resources['assign']) and !$shape->properties->messageref) {
//                $lane = $CI->bpm->find_parent($shape, 'Lane', $wf);
//                //---try to get resources from lane
//                if ($lane) {
//                    $resources = $CI->bpm->get_resources($lane, $wf);
//                }
//                //---if can't get resources from lane then assign it self as destinatary
//                if (!count($resources['assign']))
//                    $resources['assign'][] = $CI->user->Initiator;
//            }
            //---process inbox--------------
            //---Override FROM if Performer is set
            if (isset($resource['Performer'])) {
                if (count($resource['Performer'])) {
                    $msg['from'] = array_pop($resource['Performer']);
                }
            }
            $to = (isset($resources['assign'])) ? array_merge($token['assign'], $resources['assign']) : $token['assign'];
            $to = array_unique(array_filter($to));
            foreach ($to as $to_user) {
                if ($debug)
                    echo "Sending msg to user:$to_user<br/>";
                $CI->msg->send($msg, $to_user);
            }
            //---fires triger if everything is ok
            if ($shape->properties->messageref)
                run_IntermediateEventThrowing($shape, $wf);
            //---move to next shape
            $CI->bpm->movenext($shape, $wf);
            break;

        case 'Receive':
            //--call the generic catching event
            run_IntermediateEventCatching($shape, $wf);
            break;

        case 'Business Rule':
            //---TODO
            break;
        case 'Service':
            //---TODO 
            break;

        default://---default acction
            //---change status to manual (stops execution and wait 4 manual input)
            //$CI->bpm->set_token($wf->idwf, $wf->case, $shape->resourceId, $shape->stencil->id, 'manual', $data);
            $CI->bpm->movenext($shape, $wf);
            break;
    }
}
