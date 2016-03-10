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
 

 
class Ssl extends MX_Controller {

    function __construct() {
        parent::__construct();

        //---base variables
        $this->base_url = base_url();
        $this->module_url = base_url() . $this->router->fetch_module() . '/';
        $this->load->library('parser');
        $this->idu = (int) $this->session->userdata('iduser');
        $this->load->model('ssl/ssl_model');

        //d$this->user->authorize();
        //---Output Profiler
        //$this->output->enable_profiler(TRUE);
        //error_reporting(E_ALL);

    }
    
    function Index(){
        Modules::run('dashboard/dashboard', 'ssl/json/dashboard.json');

    }
    
    
     public function list_my_keys(){
         $data['keys']=$this->ssl_model->get_my_keys();
         return $this->parser->parse('my_keys',$data,true);
    }
    
    
    //=================== AJAX Requests
    
     public function add_key(){
         $key=$this->input->post();
         $key['idu']=$this->idu;
         $key['date'] = new MongoDate(time());
         $key['fingerprint']=md5($key['public_key']);
         $res=$this->ssl_model->add_key($key);
         $list=$this->list_my_keys();
         echo json_encode(array('status'=>$res['status'],'error'=>$res['error'],'fingerprint'=>$key['fingerprint'],'list'=>$list));

    }  
    
    // delete user key 
     public function delete_my_key(){
        $fingerprint=$this->input->post('fingerprint');
        $res=$this->ssl_model->delete_my_key($fingerprint);
        $list=$this->list_my_keys();
        echo json_encode(array('status'=>$res,'list'=>$list));
    }      

    // Keys List Box 
    public function ajax_list_my_keys(){
         echo $this->list_my_keys();
    }
    
    // Add key List Box
    public function ajax_add_key(){
        echo $this->parser->parse('add_key',array(),true);
    }
    
    // Encrypt form
    public function ajax_encrypt(){
        echo $this->parser->parse('encrypt',array(),true);
    }
    
    
    
    
    //=================== Tools
    
    //== Public & Private Keys generation
     public function new_keys(){
        $config = array(
            "digest_alg" => "sha512",
            "private_key_bits" => 4096,
            "private_key_type" => OPENSSL_KEYTYPE_RSA,
        );
        
        // Create the private and public key
        $res = openssl_pkey_new($config);
        
        // Extract the private key from $res to $privKey
        openssl_pkey_export($res, $privKey);
    
        // Extract the public key from $res to $pubKey
        $pubKey = openssl_pkey_get_details($res);
        $pubKey = $pubKey["key"];

        echo $privKey;
        echo "\n";
        echo $pubKey;  
        
    }   
   
    //== Encryption (Public Key)
     public function encrypt($data=null,$fingerprint=null){
         
        if(is_null($data) || is_null($fingerprint))return false;
        
        $key=$this->ssl_model->get_key($fingerprint);
        openssl_public_encrypt($data, $encrypted, $key->public_key);
        return $encrypted;
     }
     
     public function wrapper_encrypt(){

     }
     
    //== Decryption (Private Key)
    //  public function decrypt($privKey=""){
         
    //     $encrypted=$this->encrypt();

    //      openssl_private_decrypt($encrypted, $decrypted, $privKey);
    //     //return $decrypted;
    //     var_dump($decrypted);
    //  }
    
  


    
    
    
}//class
