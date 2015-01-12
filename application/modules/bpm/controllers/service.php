<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/*
 *  This Class exposes services for daemons and anonymous callers
 * 
 * @author Borda Juan Ignacio
 * 
 */
class Service extends MX_Controller {

    function Service() {
        parent::__construct();
        $this->debug = false;
        //$this->debug = true;
        $this->load->library('parser');
        $this->load->model('user');
        $this->load->model('bpm');
        //----LOAD LANGUAGE
        $this->lang->load('library', $this->config->item('language'));
        //---Debug options
        $this->debug['triggers'] = true;
        $this->debug['Run'] = true;
        $this->debug['Startcase'] = false;
        $this->debug['get_inbound_shapes'] = false;
        $this->debug['run_Task'] = true;
        $this->debug['load_data'] = false;
        //---debug Helpers
        $this->debug['run_IntermediateEventThrowing']=true;
        $this->debug['run_IntermediateLinkEventThrowing']=true;

        //$this->debug['get_shape_byname']=false;
    }
    /**
     * Process timer tokens
     */
     function process_timers($idwf=null){
        $debug = (isset($CI->debug[__FUNCTION__])) ? $CI->debug[__FUNCTION__] : false;
        // $debug=true;
        $silent=true;
        $this->load->module('bpm/engine');
        $filter=($idwf)? array('idwf'=>$idwf):array();
        $filter['type']='IntermediateTimerEvent';
        $filter['status']='waiting';
        $filter['trigger']=array('$lte'=>date('Y-m-d H:i:s',time()));
        $tokens=$this->bpm->get_tokens_byFilter($filter);
        $out=array('total'=>count($tokens));
        foreach($tokens as $token){
            $idwf=$token['idwf'];
            $idcase=$token['case'];
            $this->engine->run_filter=array(
                'idwf' => $idwf,
                'case' => $idcase,
                'status' => 'waiting',
                 'resourceId'=>$token['resourceId'],
            );
            //----run only this shape
             $this->engine->run('model', $idwf, $idcase, null,$silent);
             //---now process the rest of shapes
             $this->engine->run_filter=array();
             $this->engine->run('model', $idwf, $idcase, null,$silent);
             $out['timers'][]=array(
                 '_id'=>$token['_id'],
                 'idwf'=>$token['idwf'],
                 'case'=>$token['case'],
                 'trigger'=>$token['trigger'],
                 'resourceId'=>$token['resourceId'],
                 );
         }
        
        if (!$debug) {
            header('Content-type: application/json;charset=UTF-8');
            echo json_encode($out);
        } else {
            var_dump($out);
        }
     }
}
?>