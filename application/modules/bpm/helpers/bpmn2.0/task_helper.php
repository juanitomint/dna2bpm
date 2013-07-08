<?php

function run_Task($shape, $wf, $CI) {
    //$CI = & get_instance('Engine');
//---set DS pointer to Data Storage
    $DS = (property_exists($CI, 'data')) ? $CI->data : null;
    $debug = (isset($CI->debug[__FUNCTION__])) ? $CI->debug[__FUNCTION__] : false;
    //$debug = true;
    if ($debug)
        echo '<H1>TASK:' . $shape->properties->name . '</H1>';
    $data = array();
//---get case data
    $case = $CI->bpm->get_case($wf->case);
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
    $resourceId = $shape->resourceId;
    $inbound = $CI->bpm->get_inbound_shapes($resourceId, $wf);
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
 foreach($shape->outgoing as $out){
     $this_shape=$CI->bpm->get_shape($out->resourceId, $wf);
     if($this_shape->stencil->id=='IntermediateTimerEvent'){
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
            $is_allowed = false;
//---check if the user is assigned to the task
            if (isset($token['assign'])) {
                if (in_array($iduser, $token['assign']))
                    $is_allowed = true;
            }

//---check if user belong to the group the task is assigned to
//---but only if the task havent been assigned to an specific user
            if (isset($token['idgroup']) and !isset($token['assign'])) {
                foreach ($user_groups as $thisgroup) {
                    if (in_array((int) $thisgroup, $token['idgroup']))
                        $is_allowed = true;
                }
            }


            if (!$is_allowed)
                return;
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
                $DS->$data_store = 0;
            if ($debug)
                var_dump2('$DS original:', $DS);
            if ($streval) {
                switch ($script_language) {
                    case 'php';
//---TODO sanitize EVAL----------
//--add return if not present
                        if (!strstr($streval, 'return')) {
                            $streval = 'return(' . $streval . ');';
                        }
///--ecxecute BE CAREFULL EXTREMLY DANGEROUS
                        try {
                            $DS->$data_store = eval($streval)
                                    
                                    or die(
                                    "<h1>SCRIPT DIE! :(".
                                    var_dump(
                                            'Name', $shape->properties->name, '$data_store', $data_store, 'type', $shape->properties->tasktype, 'streval', $streval
                                    ));
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
            $msg['from'] = $CI->idu;
            $msg['subject'] = $CI->parser->parse_string($shape->properties->name, $CI->data, true, true);
            $msg['body'] = $CI->parser->parse_string($shape->properties->documentation, $CI->data, true, true);
            $resources = $CI->bpm->get_resources($shape, $wf);

            //---if has no messageref and noone is assigned then
            //---fire a message to lane or self         
            if (!count($resources['assign']) and !$shape->properties->messageref) {
                $lane = $CI->bpm->find_parent($shape, 'Lane', $wf);
                //---try to get resources from lane
                if ($lane) {
                    $resources = $CI->bpm->get_resources($lane, $wf);
                }
                //---if can't get resources from lane then assign it self as destinatary
                if (!count($resources['assign']))
                    $resources['assign'][] = $CI->user->Initiator;
            }
            //---process inbox--------------
            foreach ($resources['assign'] as $to)
                $msg = $CI->msg->send($msg, $to);
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

function send_message($subject, $body, $users) {

    $CI = & get_instance();
    $DS = $CI->bindObjectToArray($CI->data);
    $debug = (isset($CI->debug[__FUNCTION__])) ? $CI->debug[__FUNCTION__] : false;
    $debug = true;

    //---set prefix 4 email subjects
    $email_prefix = '[DNA2] ';
    $CI->load->library('email');

    if ($debug) {
        echo '<H1>SEND:' . $subject . '</H1>';
        echo "sending msg to:" . count($users) . ' users';
        echo "<hr/>$body<hr/>";
    }
    foreach ($users as $user) {
        $sendTo[] = $user['email'];
    }

    var_dump2($sendTo);
    $CI->email->initialize();
    $CI->email->from('dna2@dna2.org', 'DNA2BOT');

    foreach ($users as $user) {
        $mail_data = array_merge($DS, $user);
        if ($debug)
            var_dump2('$mail_data', $mail_data);
        $CI->email->to($user['email']);
        $CI->email->subject($CI->parser->parse_string($email_prefix . $subject, $mail_data, true));
        //-------prepare message body
        $body = $CI->parser->parse_string($body, $mail_data, true);
        $CI->email->message($body);
        $result = $CI->email->send();
        if ($debug)
            echo '<hr/>' . $CI->email->print_debugger() . '<hr/>';
    }
    return true;
}

?>