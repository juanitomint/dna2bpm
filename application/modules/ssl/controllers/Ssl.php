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
        $this->load->library('dashboard/ui');
        
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
    

//======================= DASHBOARD 


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

    // Encrypt box
    public function encrypt_form(){
        echo $this->parser->parse('encrypt',array(),true);
    
    }
    
    // Verify box
    public function verify_form(){
        echo $this->parser->parse('verify',array(),true);
    }

//======================= ENCRYPT

    //=== Encryption (Public Key)
     public function encrypt($data=null,$fingerprint=null){
      
        if(is_null($data) || is_null($fingerprint))return false;
        $res=$this->ssl_model->get_key($fingerprint);
        $pub_key = openssl_pkey_get_public($res->public_key);
        openssl_public_encrypt($data, $encrypted, $pub_key);

        return $encrypted;
     }
     
    
    // wrapper    
    public function wrapper_encrypt(){
         $plain_text=$this->input->post('plain_text');
         $fingerprint=$this->input->post('fingerprint');
         if(empty($plain_text) || empty($fingerprint)  ) return json_encode(array('status'=>false));
         echo base64_encode($this->encrypt($plain_text,$fingerprint));
     }   

    
//======================= DECRYPT  (needs PRIV KEY)  

     
//== Decrypt for @debug
// public function decrypt($privKey=""){
// $encrypted=base64_decode("UIZLtdenU7LKmGYLX2mx979YLny7i1GGK6sU9CrD04E/INWsjc1qzV0I52I8NFIMFNgLDpkNqSw+dhYFIJhI/nhOu/CycwgwYfWb8/9wXqUotP5WOt7zNBybu2k82gTu5MtsX5WE3m2nFdGxKOnaBIAjvG3QlgyM2zYo3WA6R17PmyflTOmtMQGu5T6mRjvf5zBE4rCMf6cXUNYWgEIiRuHQzG1fX933M5Ua12dQDXIZTABK9VjZSyPzFvhEqxHYGRoqGcHyT3g3hU/fECImhg/XeobLYczYrAguXZGw5eodnkhPMTiQ8MQWbQyKLfhWF2zid9K7/17+ZkZZMpDoebf/wRObuu8XZfH8EcKX7ijvYZewxtsB4HoR35ssX1xyBo3Um7fpeyEEtoUV6eMXZbCJYzMCkhMfPAPfnQva5I3s50dynmMA5DqLS3PuQRjqkuZpqsrktLVAq5af6WEW18mm0HwyzmYz1Gk/tsLeEQWr9nRdTkP3G4aOeR9l/SaRGhkmnzZ99VKaEmR6/bo6XiIY4nc4bkeDFSw9EYJazg7olAOld/Gs5axyHH2qv66PVPsOq1oZFi4kabHUZuSGQU83KV6OIFCALvxUpHEm/3YUpAUazVA0le0IlOu88lghb0wxfKioo+ivKCt/QY10Zxzkto1mOQUsVSJ73FJcxIo=");
// //$encrypted=$this->encrypt("Hey como va","dc6fcd226959ffc26571ebd6c5697f43"); 
// $privKey = openssl_pkey_get_private($this->get_priv_key());
// openssl_private_decrypt($encrypted, $decrypted, $privKey);
// var_dump($decrypted);
// }
    
//======================= SIGNATURES

//===== Create signature for data 

function sign($data,$privKey){;
openssl_sign($data, $signature, $privKey, OPENSSL_ALGO_SHA256);
return $signature;
}

// for testing
function wrapper_sign($data="Hey"){
$pk=$this->get_priv_key();
$privKey = openssl_pkey_get_private($pk);
openssl_sign($data, $signature, $privKey, OPENSSL_ALGO_SHA256);
echo base64_encode($signature);
}

//===== Verify Signature

function verify($data,$signature,$pubKey){
$pubKey = openssl_pkey_get_public($pubKey);
$res=openssl_verify($data, $signature, $pubKey, "sha256WithRSAEncryption");
return $res;
}
  
//== wrapper
public function wrapper_verify(){
    $plain_text=$this->input->post('plain_text');
    $fingerprint=$this->input->post('fingerprint');
    $signature=base64_decode($this->input->post('signature'));
    if(empty($plain_text) || empty($fingerprint) || empty($signature)) return json_encode(array('status'=>false));
    
    $res=$this->ssl_model->get_key($fingerprint);
    $pub_key = openssl_pkey_get_public($res->public_key);
 
    // echo base64_encode($this->verify($plain_text,$signature,$pub_key));
    $status=$this->verify($plain_text,$signature,$pub_key);
    
    if($status==1)
    $config=array('title'=>'Verified!','class'=>'info');
    else
    $config=array('title'=>'Not Verified!','class'=>'danger');
    
    echo $this->ui->callout($config);

}   
     
//======================= SIMETRIC ENCRYPTION 


public function encrypt_simetric($plaintext,$key){

 $this->load->helper('ssl/Crypt/Blowfish');
 $cipher = new Crypt_Blowfish();
// // keys can range in length from 32 bits to 448 in steps of 8
 $cipher->setKey($key);
 $encrypted=$cipher->encrypt($plaintext);
 return $encrypted;

}

public function decrypt_simetric($encrypted,$key){

$this->load->helper('ssl/Crypt/Blowfish');
$cipher = new Crypt_Blowfish();
// keys can range in length from 32 bits to 448 in steps of 8
$cipher->setKey($key);
return $cipher->decrypt($encrypted);

}


    
//======================= MISC 


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





//======================= DEBUG 

//=== private key for testing

private function get_priv_key(){
$ret=<<<_EOF_
-----BEGIN PRIVATE KEY-----
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
db0O1GiOXIc321MquOq8a7xFMal00uU=
-----END PRIVATE KEY-----
_EOF_;

return $ret;
}

//=== public key for testing
private function get_pub_key(){
$ret=<<<_EOF_
-----BEGIN PUBLIC KEY-----
MIICIjANBgkqhkiG9w0BAQEFAAOCAg8AMIICCgKCAgEAvOtqmPD2rpJOqmqnh6bU
TjUqX/7Fcb1INrzC0ZGNU7ds7WTtmH5ZCBa77qPAkPlKrWWAv47/dVeO5wQy8vXd
RtNuQ1c+nqfBHmJ1Ryx/zhj0BfABtte4z0CxjlRxKal6tP8qyveuK5FDBKy81/cV
hYkD5/dUQ8ohCRAo5u/Ad/5yldtvXz1b/C1iYGSk9IZTOTXZZ5b8tVn7aDFC5BMW
ezcV4Wcqe+9DBL4KSGgoAnf8wxDczHVcoA5f5zKQYWI1fZBQo5Jl+/Nv2BX17AnY
KQjc5+vFAW2544q3BLh3RW+Y5mFdV9b7jNgXw3b+4darSfvINSsrNaR5UlKvyh8g
u3pteQVgHAZw0UygZ3ZmSnsU+xrtrupv4jALY6pK8IGiOqvVGUJqkJfcBSve0nxp
v+x0GKiTCBTRdsNSRn4Ic+XmiQackYtW/OAO0yFkjwsrh7b7EMs6f5RY1RmDAcgD
Q17VVv0qBWMyk5gVk8xSh2fz2Efp+1+J9bW2ZTPbUJ77g5YqGDec3r6aoP7xZekh
5S8AsjVVL/pOJrDxtIUv2pNt4WdRKgmxIEwVX+FwN7YEJKDBsJKEoJq1nBL/iaoP
435ahRSeESJ0qc+WLbsrfezJFRwI/ful2fDzEr4+/QguYHBeynMKiJDP8c14rOdu
CAS8APqipSOc4OFf52/Txd0CAwEAAQ==
-----END PUBLIC KEY-----
_EOF_;
return $ret;
}





    
}//class
