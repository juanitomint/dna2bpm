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
        $this->base_url = base_url();
        $this->module_url = base_url() . $this->router->fetch_module() . '/';
    }
    /**
     * Process timer tokens
     */
     function process_timers($idwf=null){
        $debug = (isset($CI->debug[__FUNCTION__])) ? $CI->debug[__FUNCTION__] : false;
        // $debug=true;
        $silent=true;
        $run=true;
        /**
         * Impersonates as 666=daemon
         */ 
        $this->user->idu=666;
        // $this->user->idu=1;
        //---register if it has logged is
        $this->session->set_userdata('loggedin', true);
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
             if($run){
                //----run only this shape
                $this->engine->run('model', $idwf, $idcase, null,$silent);
                //---now process the rest of shapes
                $this->engine->run_filter=array();
                $this->engine->run('model', $idwf, $idcase, null,$silent);
             }
             $tlog=array(
                 '_id'=>$token['_id'],
                 'idwf'=>$token['idwf'],
                 'case'=>$token['case'],
                 'trigger'=>$token['trigger'],
                 'resourceId'=>$token['resourceId'],
                 'checkdate'=>date('Y-m-d H:i:s'),
                 'microtime'=>microtime(true),
                 );
            //----Log timer
            $options = array('w' => true);
            if(!$debug)
                $wf = $this->mongowrapper->db->log_timers->save($tlog, $options);
             $out['timers'][]=$tlog;
         }
        //---un-register if it has logged is
        $this->session->set_userdata('loggedin', false);
        if (!$debug) {
            $this->output->set_content_type('json','utf-8');
            echo json_encode($out);
        } else {
            var_dump($out);
        }
     }
     
    function rss($token=null){
        if($token){
        /**
         * Impersonates as 666=daemon
         */ 
        $this->user->idu=666;
        // $this->user->idu=1;
        //---register if it has logged is
        $this->session->set_userdata('loggedin', true);
            $this->load->model('bpm/bpm');
            $user=$this->user->getby_token($token);
            if($user){
                $query = array(
                    'assign' => $user->idu,
                    'status' => 'user'
                );
                //var_dump(json_encode($query));exit;
                $tasks = $this->bpm->get_tasks_byFilter($query, array(), array('checkdate' => 'desc'));
                $tasks=Modules::run('bpm/bpmui/prepare_tasks',$tasks,1,5);
                $this->load->module('rss');
                // var_dump($tasks['mytasks']);exit; 
                foreach($tasks['mytasks'] as $task){
                    $this->rss->items[]=
                        array(
                             'title' => $task['title'],
                             'author' => $user->name,
                             'link' => $this->base_url.'bpm/engine/run/model/'.$task['idwf'].'/'.$task['case'].'/'.$task['resourceId'],
                             'pubdate' => strtotime($task['checkdate']),
                             'description' => $task['type']
                            );
                        
                }
                $this->rss->render();
            }    
        }
    }
}
