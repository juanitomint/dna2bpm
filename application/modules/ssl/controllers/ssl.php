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
    
    
    //==== Keys kist
     public function list_my_keys(){
         $data['keys']=$this->ssl_model->get_my_keys();
         return $this->parser->parse('my_keys',$data,true);
    }
    
    // Wrapper Keys List  
    public function ajax_list_my_keys(){
         echo $this->list_my_keys();
    }
    
    
    //==== Add new key
    
     public function add_key(){
         $key=$this->input->post();
         $key['idu']=$this->idu;
         $key['date'] = new MongoDate(time());
         $key['fingerprint']=md5($key['public_key']);
         $res=$this->ssl_model->add_key($key);
         $list=$this->list_my_keys();
         echo json_encode(array('status'=>$res['status'],'error'=>$res['error'],'fingerprint'=>$key['fingerprint'],'list'=>$list));

    }  
    
    // wrapper
    public function ajax_add_key(){
        echo $this->parser->parse('add_key',array(),true);
    }
    
    //=== Delete key 
     public function delete_my_key(){
        $fingerprint=$this->input->post('fingerprint');
        $res=$this->ssl_model->delete_my_key($fingerprint);
        $list=$this->list_my_keys();
        echo json_encode(array('status'=>$res,'list'=>$list));
    }      




    //=== Encryption (Public Key)
     public function encrypt($data=null,$fingerprint=null){
      
        if(is_null($data) || is_null($fingerprint))return false;
        $res=$this->ssl_model->get_key($fingerprint);
        $pub_key = openssl_pkey_get_public($res->public_key);
        openssl_public_encrypt($data, $encrypted, $pub_key);

        return $encrypted;
     }
     
    // form
    public function encrypt_form(){
        echo $this->parser->parse('encrypt',array(),true);
    }
    
    // wrapper    
    public function wrapper_encrypt(){
         $plain_text=$this->input->post('plain_text');
         $fingerprint=$this->input->post('fingerprint');
         if(empty($plain_text) || empty($fingerprint)  ) return json_encode(array('status'=>false));
         echo base64_encode($this->encrypt($plain_text,$fingerprint));
     }   

    
    //== @debug -> Public & Private Keys generation
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
   

     

     
//== Decryption for @debug
     public function decrypt($privKey=""){

$encrypted=$this->encrypt("Hey como va","dc6fcd226959ffc26571ebd6c5697f43"); 
//$encrypted64=base64_encode($encrypted);

$pk=<<<_EOF_
-----BEGIN RSA PRIVATE KEY-----
MIIEowIBAAKCAQEA1Rfq2Czb/pX+nqN4NVPetvDVhMXQnE3SH2hwPBHO5Gn9raMp
Rx2drP1U7FLRacwjdOOCGkmeK1BEc4+HIfltnaLIsGuJ87ghihNDTK7It3qIpkkd
+eg7MZujaVeu0iI7R1DJNXV4tch+XMzjl4vLlESVqDaE5QNaTY/DjVJH328njL2d
V5Z+xGSyaVB6V4LNXoe6YPyB7b5zomnqmT0I3Dy7cvAtDNTqHenzLHYk8zf0ziTy
pK62fAmrQtC0Az/l1kRy9TP0uTm3coz8SIQzmjniVyzy0XPaXG1fhtMgYWaH0s4m
/2SWldt01qFtE1UZ6aTySxEIbRhyQT7aEe9uzwIDAQABAoIBACvKinx6W4tqD7VS
KrXq0m4N+BMdA83bQD8sG0R89GOEVJmGWkk/ENQoC3e1XUu8o9y1lFsKnfKQwEBv
Unns5FXsyglXUDZBtMLHSqFLmfv6tnJVvE9LJj7/mQlg95A/cKcrNu5Bgdj5pt4z
TOIr3F5P6eFzssPNeJVP4gP80MBdm17p5o0qlpQRcK32MHsm5pYsBSESrSxM/vcc
diFYWrLfvhuaw8J2SBzCaV5XPNvaUkRPXLOM5ar5EjnRwHpZyLrAz+J0wmsEmgY3
Few6Q7S+Xapni2wFdf0Lr1K+w+HEDB7GtRKgWcoenU7qu/f5w1OkxsZJAV99AJjt
u28QKAECgYEA7+/ZKFLrVd4rm3OtHIDddKwG506neKFVewV/LiDRrZdvJGhNTRDX
E8JO9YJUAYfiFcC5dWspBOpaVme7BtESNLk+EB1P8R4glOxymxqvhbwt4xvkFmtK
hVy5qDqf+YOStblofHKlAOHX+CwKYTwJTAYMISV0e3eSyRw1ssXMk88CgYEA41wD
TFvbEJg2t+5I14pSSVGDRVO5NDMzwzyE59mxdQ7tDHKONNAVDeElVCy+tCAnwW0J
imYfaEbROA+MF0JBGPaF/ms6hZ/7nVaP2Lbpvcbky3BodJ+f+tpqHCViPeTmVjB1
Bw40NcefSZ6m9Uiz+0Di5vxoBNouc0b6UgMnNQECgYB+APaI/6rJKGi8NW525pHm
QKVLefnnFsreVU+p+OBEip96fjACRdK9dLCkq/HT7/liNRjwOfuLsksIz2bfuJIY
ECQwsEQYOxsfOmEhZU1CLUXn2/DXeTbkfIKff1Id5eP4/UqK+GYA5ZnWocI9uBql
yxM5oQLgCDaU2PS2UrwR7wKBgDYJMF99lVskUhz07SsNHPGABgr8ExBs+uh0AcJ4
4sxHd991eobizZ423IBdAhYdblVybMoP63cFHcSNLWZ5wK8GKGKHaalIlKyYXifL
kVIha09OsATHy2X7cyytVeQP+w6RBb9fiNkfUKRWqKezV9NnYIY1hyNgF5oeTHAh
f7gBAoGBANcsGOxzxOhPTNJ2BseDs+pZaTfkd/mzD+Fy+hOteaZPm2LwrWtMFXqj
MowTlhZUQSPz8ze0GttiD1wP4bcb+dRfkfSAOhh0GDEydCUaFz+HazvbxmzKiV8E
QEW2GATCSKO/2J+ApxHFNabtydjUQctjxZVlhGYEJzPCG2ZuArAE
-----END RSA PRIVATE KEY-----
_EOF_;
$privKey = openssl_get_privatekey($pk);


openssl_private_decrypt($encrypted, $decrypted, $privKey);
//return $decrypted;
var_dump($decrypted);
}
    
  


function test(){
$this->load->library('encrypt');
}
    
    
    
}//class
