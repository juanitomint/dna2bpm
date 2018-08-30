<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/**
 * test
 * 
 * Description of the class
 * 
 * @author Juan Ignacio Borda <juanignacioborda@gmail.com>
 */
class Formio extends MX_Controller {

    function __construct() {
    	ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);
        error_reporting(E_ALL);

        parent::__construct();
        $this->base_url = base_url();
        $this->module_url = base_url() . $this->router->fetch_module() . '/';
        $this->global_js = [
            'CSRF' =>'8531c462563603d7f4702d138af1e787',
        ];
    }


    function formio_poc($form_id=null,$idwf=null,$idcase=null,$token_id=null) {
        $path_segments = $this->session->CI->uri->segments;
        
        $form_id = (!isset($form_id)) ? $path_segments[4]: $form_id;
        $idwf = (!isset($idwf)) ? $path_segments[5]: $idwf;
        $idcase = (!isset($idcase)) ? $path_segments[6]: $idcase;
        $token_id = (!isset($token_id)) ? $path_segments[7]: $token_id;
        
        // $parent  = isset($path_segments[5]) ? $path_segments[5] : null ;
        
        // http://localhost:3001/#/form/5ab831a33a47f101c06f2098/
        $src = "http://localhost:3001/form/$form_id/";
        $action="http://localhost:3001/form/$form_id/submission?live=1";
        
        $form_urls = [
            'src'    => $src,
            'action' => $action,
        ];
        
        $this->load->view('index.php', $form_urls);
    }

    
    
    /** TEST consumer
     */
     function Test ($form='form',$form_id,$idwf=null,$idcase=null,$token_id=null){
        $this->user->authorize();
        $customData['global_js']= $this->global_js;
        //---Get submission_id from case->data->external->formio->$resourceId
        $customData['global_js']['src'] = "http://localhost:3001/form/$form_id/";
        
        if($idwf && $idcase && $token_id){
            $this->load->model('bpm/bpm');
            ///---get resrourceId
            $token=$this->bpm->get_token_byid($token_id);
            
            $case=$this->bpm->get_case($idcase,$idwf);
            //--- Check if key exists
            if (isset($case['data']['external']['formio'][$token['resourceId']])) {
                $customData['global_js']['src']= "http://localhost:3001/form/$form_id/submission/".$case['data']['external']['formio'][$token['resourceId']];
            } 
        }
        $customData['global_js']['idwf']=  $idwf;
        $customData['global_js']['idcase']= $idcase;
        $customData['global_js']['token_id']= $token_id;
        $customData['global_js']['action']="http://localhost:3001/form/$form_id/submission/?live=1";
        $customData['global_js']['action_post']=$this->base_url."bpm/engine/post/$token_id/formio/";
            //  var_dump($customData);exit;
	    //----pasar params al dashboard usar la url para el render 
		Modules::run('dashboard/dashboard', 'formio/json/formio_poc.json',null,$customData);
     }
}
