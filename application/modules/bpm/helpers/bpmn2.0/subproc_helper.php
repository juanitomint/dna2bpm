<?php

function run_CollapsedSubprocess($shape, $wf, $CI) {
    $debug = (isset($CI->debug[__FUNCTION__])) ? $CI->debug[__FUNCTION__] : false;
    // $debug=true;
    if ($debug)
        echo '<H1>'.__FUNCTION__.':' . $shape->properties->name . '</H1>';

    $token = $CI->bpm->get_token($wf->idwf, $wf->case, $shape->resourceId);
    //----get childs from token
    $child_cases=isset($token['child'])?$token['child']:array();
    
    $idcase = $wf->case;
    $idwf = $wf->idwf;
    $parent['token'] = $token;
    $parent['case'] = $wf->case;
    $parent['idwf'] = $wf->idwf;
    $case=$CI->bpm->get_case($wf->case, $wf->idwf);
    $child_idwf = $shape->properties->entry;
    $silent = true;
    $isfinished=false;
    $doprocess=true;
    $casesfinished=0;
    $stillOpen=array();
    $DS=$CI->data;
    if($debug) echo "<h2>Sub-Proc Type:{$shape->properties->subprocesstype}</h2>";
    if($token['status']=="waiting" and $shape->properties->subprocesstype<>'Embedded'){
            //-----check if all childs has finished.
        $filter=array('idwf'=>$child_idwf,'id'=>array('$in'=>(array)$child_cases));
        $cases=$CI->bpm->get_cases_byFilter($filter, array('status','idwf','id'));
            foreach($cases as $child_case){
                if($child_case['status']<>'open') {
                    $casesfinished++;
                } else {
                    $stillOpen[]=$child_case['id'];
                }
            }
        if(count($stillOpen))
            $runrun=$stillOpen[0];        
            //----check if qtty defined is greater than or equal to finished cases
        if($casesfinished>=$shape->properties->completionquantity) {
                $isfinished=true;
                $doprocess=false;
            }
        /**
         * EVAL LoopCondition
         */
        $streval=$shape->properties->completioncondition;
        if($streval<>''){
             if (!strstr($streval, 'return')) {
                    $streval = 'return(' . $streval . ');';
                }
///--ecxecute BE CAREFULL
            
        $isfinished =(bool)eval($streval);
        }
        
        switch ($shape->properties->looptype) {
                        case "Sequential"://---start one instance at a time
                            //---let the doprocess run only if previous instance has finished
                            if(count($stillOpen))
                                $doprocess=false;
                            //---eval if can exist more tha one open    
                            //---allow the ammount loopcadinality to be open
                            if($shape->properties->loopcardinality>count($stillOpen)) {
                                $doprocess=true;
                            }
                                
                        break;
                        
                        case "Parallel"://---start one instance at a time assumes data input does not change
                            //---all instances has been started so don't let doprocess run
                            $doprocess=false;
                        break;
                        
                        default://---just one instance has been created so prevent to create more
                            $doprocess=false;
                        break;
            
        } 
    }
    /**
     * STATUS any other than WAITING and sequential loops
     */ 
    if($doprocess){
        switch($shape->properties->subprocesstype){
        case  "Embedded":
            //---replace embedded
            run_Subprocess($shape, $wf, $CI);        
            return;
            break;
        case  "Independent":
        case  "Reference":
            $data=array();
            $data['parent']=$parent;
            $data['parent_data']=$case['data'];

                if ($shape->properties->entry) {
                    $child_idwf = $shape->properties->entry;
                    /* Create new child cases
                     * Check if multiple $dataStoreName
                     */
                    $dataStoreName=''; 
                    $prev=$CI->bpm->get_previous($shape->resourceId, $wf);
                    foreach($prev as $prev_shape){
                        if($prev_shape->stencil->id=='DataStore'){
                        $dataStoreName=$prev_shape->properties->name;
                        }
                    }
                    /**
                     * Eval LOOP
                     */ 
                    switch ($shape->properties->looptype) {
                        case "Sequential"://---start one instance at a time assumes data input does not change
                        if($dataStoreName){
                                // loop thru data input and start a case for each one
                                if($CI->data->$dataStoreName){
                                    //@todo get next item
                                    // foreach($CI->data->$dataStoreName as $item){
                                    //     //start a case with $item as data in data['parent_data']
                                    //     // var_dump($item);
                                    //     $data['parent_data']=$item;
                                    //     //---Newcase($model, $idwf, $manual = false, $parent = null, $silent = false,$data=array())
                                    //     $CI->newcase('model', $child_idwf, false, $parent, $silent,$data);
                                    // }
                                } else {
                                    show_error('DataStore:'.$dataStoreName.' not loaded');
                                }
                            } else {
                                //----create from shape
                                //start a case with $item as data in data['parent_data']
                                
                                $child_case=$CI->bpm->gen_case($child_idwf,null , $data);
                                $child_cases[] = $child_case;
                                //----independent or by reference
                                if($shape->properties->subprocesstype=='Reference'){
                                    $this_case=$CI->bpm->get_case($child_case,$child_idwf);
                                    $this_case['iduser']=$case['iduser'];
                                    $CI->bpm->save_case($this_case);
                                }
                                //---Start childs left first to start last
                                $CI->Startcase('model', $child_idwf, $child_case,$silent);
                                $runrun=$child_case;
                                
                            }
                            break;
                        case "Parallel"://---start all instances at once
                        // echo "paralell";
                            if($dataStoreName){
                                
                                // loop thru data input and start a case for each one
                                if($CI->data->$dataStoreName){
                                    foreach($CI->data->$dataStoreName as $item){
                                        //start a case with $item as data in data['parent_data']
                                        $data['parent_data']=$item;
                                        $child_case=$CI->bpm->gen_case($child_idwf,null , $data) ;
                                        $child_cases[] = $child_case;
                                        //---Newcase($model, $idwf, $manual = false, $parent = null, $silent = false,$data=array())
                                        $CI->Startcase('model', $child_idwf, $child_case,$silent);
                                        $CI->Run('model', $child_idwf, $child_case,null, $silent);
                                    }
                                } else {
                                    show_error('DataStore:'.$dataStoreName.' not loaded');
                                }
                            } else {
                                
                                //----create from shape
                                for($i=1;$i<=$shape->properties->startquantity;$i++){
                                        //start a case with $item as data in data['parent_data']
                                        
                                       if($shape->properties->subprocesstype=='Reference'){
                                            
                                       }
                                        $child_case=$CI->bpm->gen_case($child_idwf,null , $data);
                                        $child_cases[] = $child_case;
                                        //----independent or by reference
                                        if($shape->properties->subprocesstype=='Reference'){
                                            $this_case=$CI->bpm->get_case($child_case,$child_idwf);
                                            $this_case['iduser']=$case['iduser'];
                                            $CI->bpm->save_case($this_case);
                                        }
                                        if($debug)  
                                            echo "Starting: $child_idwf::$child_case <hr/>";
                                        //---Start childs left first to start last
                                        $CI->Startcase('model', $child_idwf, $child_case,true);
                                        $CI->Run('model', $child_idwf, $child_case,null, true);
                                        }
                                    //----send first case to run
                            
                                
                            }
                            $runrun=$child_cases[0];
                            break;
                            
                        /**
                         * STANDARD JUST 1
                         */ 
                        case "Standard":
                        default://-- "None" start just 1 child case
                            //  $child_case=$CI->bpm->get_case($idcase, $child_idwf);
                             if(!$child_case){
                                $idcase=$CI->bpm->gen_case($child_idwf,$idcase , $data) ;
                             } else {
                                 //---update childcase
                                 $childcase['data']['parent_data']=$case['data'];
                             }
                             $child_cases[] = $idcase;
                             //---set this to run
                             $runrun=$idcase;
                            //---Start child
                             $CI->Startcase('model', $child_idwf, $wf->case,$silent);
                            break;
                    }
                }
                $case['data']['child'][$shape->resourceId][$child_idwf]= $child_cases;
                $CI->bpm->save_case($case);
                $CI->bpm->set_token($wf->idwf, $wf->case, $shape->resourceId, $shape->stencil->id, 'waiting',array('child'=>$child_cases));
            break;
            
            default:
            break;
    }
        //  STATUS any other than WAITING
    
    }
    
    /**
     * MOVENEXT OR RUN NEXT CHILD
     */ 
    if($isfinished){
        $CI->bpm->movenext($shape, $wf);
    } else{ 
        //----run first nonfinished child
        $CI->Run('model', $child_idwf, $runrun);
    }
}

function run_Subprocess($shape, $wf, $CI) {
    $debug = (isset($CI->debug[__FUNCTION__])) ? $CI->debug[__FUNCTION__] : false;
    // $debug=true;
   if ($debug)
        echo '<H1>'.__FUNCTION__.':' . $shape->properties->name . '</H1>';
    $token = $CI->bpm->get_token($wf->idwf, $wf->case, $shape->resourceId);
    if ($debug)
        var_dump($token['status']);
    switch ($token['status']) {
        case 'waiting':
            //---check that some finish event has been reached
            foreach ($shape->childShapes as $child) {
                $has_finihed = false;
                //---only one finis event can make the subproc marked as finish.
                // find end events  childs
                if (preg_match('/^End/', $child->stencil->id)) {
                    $child_token = $CI->bpm->get_token($wf->idwf, $wf->case, $child->resourceId);
                    if ($child_token['status'] == 'finished') {
                        $has_finihed = true;
                    }
                }
            }
            //----if all went well then move on!
            if ($has_finihed) {
                $CI->bpm->movenext($shape, $wf);
            }
            break;
        default:
            //---SAME AS STARTING A CASE
            //---Get start shape
            $start_shapes = $CI->bpm->get_start_shapes($shape);
            if (count($start_shapes)) {
                $start_shape = $start_shapes[0];
                if ($debug) {
                    echo '<h2>$start_shapes</h2>';
                    var_dump($start_shape);
                    echo '<hr>';
                }
                //----Raise an error if doesn't found any start point
                if (!$start_shapes)
                    show_error("The Schema doesn't have an start point");
                //---Start all  StartNoneEvents as possible as case_subproc
                
                foreach ($start_shapes as $start_shape) {
                    // $CI->bpm->set_token($wf->idwf, $wf->case.'_'.$shape->properties->name, $start_shape->resourceId, $start_shape->stencil->id, 'pending');
                    $CI->bpm->set_token($wf->idwf, $wf->case, $start_shape->resourceId, $start_shape->stencil->id, 'pending');
                }
                //---now Set the status to waiting
                $CI->bpm->set_token($wf->idwf, $wf->case, $shape->resourceId, $shape->stencil->id, 'waiting');
            } else {
                //----if has no childshapes move next
                $CI->bpm->movenext($shape, $wf);
            }
            break;
    }//----end switch
}

?>
