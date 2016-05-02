<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');
    
require APPPATH . 'modules/rest/libraries/REST_Controller.php';
    
    
/**
 * Actualiza los archivos segun la rama configurada
 * 
 * @autor Fojo Gabriel 
 * 
 * @version 	1.0 
 * 
 * 
 */
 

 
class Rest extends REST_Controller {



    function __construct() {
        // Construct the parent class
        parent::__construct();

        // Configure limits on our controller methods
        // Ensure you have created the 'limits' table and enabled 'limits' within application/config/rest.php
        $this->methods['user_get']['limit'] = 500; // 500 requests per hour per user/key
        $this->methods['user_post']['limit'] = 100; // 100 requests per hour per user/key
        $this->methods['user_delete']['limit'] = 50; // 50 requests per hour per user/key
       
        $this->load->model('ssl/ssl_model');
        $this->idu = $this->user->idu;
        
    }

/*
|--------------------------------------------------------------------------
| AUTH
|--------------------------------------------------------------------------
|
| 1. Client send fingerprint in headers (X-Fingerprint) always (must add public key in SSL module and get the fingerprint).  
| 2. Client calls init , and receives a simetric key, encrypted with his public key.
| 3. Client now sends X-Fingerprint, and X-Hash(fingerprint encrypted with simetric key ) in each request.
| 4. We use X-Hash to authenticate client
*/

// Refresh simetric key 
    
  public function init_get()
  {
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
             $response['msg']='Simetric Key created - Decode with Asimetric Private Key - Crypt_Blowfish';
             $response['simetric_key']=base64_encode($key_simetric);
         }
         
        echo json_encode($response);
  }

// Check headers X-Fingerprint & X-Hash
  
private function auth(){

      $headers = $this->input->request_headers();
      
      if(!isset($headers['X-Fingerprint'])){
          $msg=array('status'=>false,'msg'=>'NO X-Fingerprint');
          $this->set_response($msg, REST_Controller::HTTP_BAD_REQUEST);
          return false;
      }

      if(!isset($headers['X-Hash'])){
          $msg=array('status'=>false,'msg'=>'NO X-Hash');
          $this->set_response($msg, REST_Controller::HTTP_BAD_REQUEST);
          return false;
      }    
      
      if(!isset($headers['X-MsgID'])){
          $msg=array('status'=>false,'msg'=>'NO X-MsgID');
          $this->set_response($msg, REST_Controller::HTTP_BAD_REQUEST);
          return false;
      }  
      
    $fingerprint=$headers['X-Fingerprint'];
    $hash=base64_decode($headers['X-Hash']);

    $res=$this->ssl_model->get_key($fingerprint);

    if(!$res){
      $msg=array('status'=>false,'msg'=>'Bad Fingerprint');
      $this->set_response($msg, REST_Controller::HTTP_BAD_REQUEST);
      return false;
    }   
      
    $open_hash=Modules::run('ssl/decrypt_simetric', $hash,$res->simetric_key);

    $resp=($open_hash===$fingerprint.':'.$headers['X-MsgID'])?(true):(false);
    
    if(!$resp){
          $msg=array('status'=>false,'msg'=>'No Match');
          $this->set_response($msg, REST_Controller::HTTP_BAD_REQUEST);
          return false;
    }
        
        return true;
}


// Decrypt simetric

private function crack($encrypted){
        
    $headers = $this->input->request_headers();
    $fingerprint=$headers['X-Fingerprint'];
    $res=$this->ssl_model->get_key($fingerprint);
     
    $encrypted=base64_decode($encrypted);
    
     $fingerprint=$headers['X-Fingerprint'];

     $plain_text=Modules::run('ssl/decrypt_simetric', $encrypted,$res->simetric_key);

    return $plain_text;

}
  
  
/*
|--------------------------------------------------------------------------
| GET's
|--------------------------------------------------------------------------
|
*/
  
  
  public function index_get()
  {

      if(!$this->auth())return;

      $msg=array('msg'=>'Todo bien');
      $this->response($msg, REST_Controller::HTTP_OK);
  }


/*
|--------------------------------------------------------------------------
| POST's
|--------------------------------------------------------------------------
|
*/


  public function index_post()
  {

     if(!$this->auth())return;
     $encrypted=$this->post('msg');    
     $plain_text=$this->crack($encrypted);
        
    $msg=array('msg'=>$plain_text);
    $this->response($msg, REST_Controller::HTTP_OK);
  }

 
 //== DELETE


//   public function index_delete()
//   {
//       $id = (int) $this->delete('id');
//   } 
    
    
 //== PUT

//   public function index_put()
//   {
//       $id = (int) $this->put('id');
//   }  



    
    
}//class
