<?php

function run_CollapsedSubprocess($shape, $wf, $CI) {
    //run_Subprocess($shape, $wf,$CI);
    $token = $CI->bpm->get_token($wf->idwf, $wf->case, $shape->resourceId);
    $parent['token']=$token;
    $parent['case']=$wf->case;
    $parent['idwf']=$wf->idwf;
    
    if ($shape->properties->entry) {
        $idwf=$shape->properties->entry;
        Modules::run('bpm/engine/newcase','model',$idwf,false,$parent);
        //$CI->engine->Newcase('model', $idwf, false, $parent);
    }
}

function run_Subprocess($shape, $wf, $CI) {
    $CI = & get_instance();
    $debug = (isset($CI->debug[__FUNCTION__])) ? $CI->debug[__FUNCTION__] : false;
    $token = $CI->bpm->get_token($wf->idwf, $wf->case, $shape->resourceId);
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
            $start_shapes = $CI->bpm->get_shape_byname('StartNoneEvent', $shape);
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
                //---Start all  StartNoneEvents as possible
                foreach ($start_shapes as $start_shape) {
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