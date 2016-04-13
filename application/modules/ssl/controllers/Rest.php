<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');
/**
 * Actualiza los archivos segun la rama configurada
 * 
 * @autor Fojo Gabriel 
 * 
 * @version 	1.0 
 * 
 * 
 */
 

 
class Rest extends MX_Controller {

    function __construct() {
        parent::__construct();

        //---base variables
        $this->base_url = base_url();
        $this->module_url = base_url() . $this->router->fetch_module() . '/';
        $this->load->library('parser');
        $this->load->library('dashboard/ui');
        
        $this->idu = (int) $this->session->userdata('iduser');
        $this->load->model('ssl/ssl_model');

        //d$this->user->authorize();
        //---Output Profiler
        //$this->output->enable_profiler(TRUE);
        //error_reporting(E_ALL);

    }
    
    function Index(){
        

    }
    
/**
 * This function create the key for symetric encription - The key is sent to te user using asimetric crypto and is
 * save in the DB - Each time this function is called the key is regenerated. 
 * 
 * 
 */   

    function init(){

        $headers = $this->input->request_headers();
        $fingerprint=$headers['X-Fingerprint'];
        $res=$this->ssl_model->get_key($fingerprint);
         
         if(empty($res)){
             //== Error
             $response['status']=false;
             $response['msg']='Bad fingerprint';

         }else{
             //== fingerprint OK

             //echo $res->public_key;
             $salt = bin2hex(openssl_random_pseudo_bytes(12));
             $key_simetric=Modules::run('ssl/encrypt', $salt,$res->fingerprint);
        
             //=== Simetric key ready, save in DB and send to user
             $dbres=$this->ssl_model->add_simetric($salt,$res->fingerprint);

            //===
             $response['status']=true;
             $response['msg']='Simetric Key created - Decode with Asimetric Private Key';
             $response['simetric_key']=base64_encode($key_simetric);
             
         }
         
        echo json_encode($response);

    }
    
    
function post(){
        
    //== Fingerprint 
    

    $headers = $this->input->request_headers();
    $fingerprint=$headers['X-Fingerprint'];
    
    $encrypted_body=base64_decode($this->input->post('msg'));

    $res=$this->ssl_model->get_key($fingerprint);
    
    
     if(empty($res)){
         //== Error
         $response['status']=false;
         $response['msg']='Bad fingerprint';

     }else{
        //== fingerprint OK

        //$encrypted_body=Modules::run('ssl/encrypt_simetric', 'Hellow World!',$fingerprint);

        $msg=Modules::run('ssl/decrypt_simetric', $encrypted_body,$res->simetric_key);

        if($msg){
            // msg ok
            $response['status']=true;
            $response['msg']='Msg received OK';

            // ==== PUSH
        
             $data['msg']=$msg;
             $data['fingerprint']=$fingerprint;
             $data['date']= new MongoDate(time());
             if(isset($headers['X-MsgID']))$data['msgid']=$headers['X-MsgID'];
             if(isset($headers['X-Origin']))$data['origin']=$headers['X-Origin'];
             $response['status']=$this->ssl_model->rest_push($data);
        
        }else{
            $response['status']=false;
            $response['msg']='It was an error with the msg';
        }
        
     }  

         echo json_encode($response);

}


    
    
    
    
    
}//class
