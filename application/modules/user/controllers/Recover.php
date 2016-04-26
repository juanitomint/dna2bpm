<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Recover extends MX_Controller {

    public function __construct() {
        parent::__construct();
        //---base variables
        $this->base_url = base_url();
        $this->module_url = base_url() . $this->router->fetch_module().'/';
        //----load parser
        $this->load->library('parser');
        $this->load->library('email');
        $this->load->config('config');   
    }


    function Index() {
        $msg = $this->session->userdata('msg');
        //----LOAD LANGUAGE
        $this->lang->load('login', $this->config->item('language'));
        //---add language data
        $cpData['lang'] = $this->lang->language;
        $cpData['title'] = $this->lang->line('PageDescriptionR');
        $cpData['base_url'] = $this->base_url;
        $cpData['module_url'] = $this->module_url;
        $cpData['theme'] = $this->config->item('theme');
        //----NO USER

        if ($msg == 'nouser') {
            $cpData['msgcode'] = $this->lang->line('nousr');
        }
        //----USER DOESN'T HAS PROPPER LEVELS

        if ($msg == 'nolevel') {
            $cpData['msgcode'] = $this->lang->line('nolevel') . "<br>" . $this->session->userdata('redir');
        }

        //----USER has to be logged first
        if ($msg == 'hastolog') {
            $cpData['msgcode'] = $this->lang->line('hastolog') . "<br>" . $this->session->userdata('redir');
        }
        
        $this->session->set_userdata('msg', $msg);
        //---build UI 
        //---define files to viewport
        $cpData['css'] = array($this->module_url . "assets/css/login.css" => 'Login Specific');
        $cpData['js'] = array($this->module_url . "assets/jscript/recover.js" => 'Login Specific');
        
        //---
        $cpData['global_js'] = array(
            'base_url' => $this->base_url,
            'module_url' => $this->module_url,
            'show_warn' =>$this->config->item('show_warn'),
            'msg' => $msg,
            'msgcode' => (isset($cpData['msgcode'])) ? $cpData['msgcode'] : ''
        );
        $cpData['show_warn']=($this->config->item('show_warn') and $msg<>'');
        //----clear data
         $this->session->unset_userdata('msg');

        $this->ui->compose('user/recover.php','user/bootstrap3.ui.php',$cpData);
    }
    
    
    
    function Send() {
        //----LOAD LANGUAGE
                
        $this->load->model('msg');
        $this->lang->load('login', $this->config->item('language'));
        
        $msg['to'][$this->input->post('mail')]=$this->input->post('mail');
        $dbobj=$this->user->getbymailaddress($this->input->post('mail'));

        if(!empty($dbobj->idu)){
            
            $token=md5($dbobj->email.$dbobj->idu);
            $content = $this->lang->line('mailsendpart1');
            $content.=" <strong>{$dbobj->nick}</strong> $this->base_url</p>";
            $content.=$this->lang->line('mailsendpart2');
            $content.="<a href='{$this->base_url}user/recover/new_pass/$token'>".$this->lang->line('mailsendpart3')."</a>";
            
            //== Envio
            
            $msg['reply_email']='dna2@industria.gob.ar';
            $msg['reply_nicename']='Soporte';
            //$msg['to']=array('gabriel@trialvd.com.ar'=>'gabriel@trialvd.com.ar');
            $msg['body']=$content;
            $msg['subject']= $this->lang->line('PageDescriptionR');
            $msg['debug']=0;
            
            $send_ok=$this->msg->sendmail($msg);

            if($send_ok){
                // Mail OK --
               // echo $this->lang->line('mailmsg1')."</br> <a href='{$this->base_url}'>".$this->lang->line('mailback')."</a>";
  
                //save token
                $object['token']  = $token;
                $object['creationdate']  = date('Y-m-d H:i:s');
                $object['idu'] = (int)$dbobj->idu;
                $result = $this->user->save_token($object); 
                
                $resp['status']=true;
                $resp['msg']=$this->lang->line('mailmsg1');
            }else{
                $resp['status']=false;
                $resp['msg']=$this->lang->line('mailmsg1_error');;
            }
        
            echo json_encode($resp);
        }


        
        
    }
    
    //=== 
    

    
    
    
    function New_pass($token){
        
        
        $msg = $this->session->userdata('msg');
        
        
        //----LOAD LANGUAGE
        $this->lang->load('login', $this->config->item('language'));
        //---add language data
        $cpData['lang'] = $this->lang->language;
        $cpData['title'] = $this->lang->line('mailform');
        $cpData['base_url'] = $this->base_url;
        $cpData['module_url'] = $this->module_url;
        $cpData['theme'] = $this->config->item('theme');
        //----NO USER

        if ($msg == 'nouser') {
            $cpData['msgcode'] = $this->lang->line('nousr');
        }
        //----USER DOESN'T HAS PROPPER LEVELS

        if ($msg == 'nolevel') {
            $cpData['msgcode'] = $this->lang->line('nolevel') . "<br>" . $this->session->userdata('redir');
        }

        //----USER has to be logged first
        if ($msg == 'hastolog') {
            $cpData['msgcode'] = $this->lang->line('hastolog') . "<br>" . $this->session->userdata('redir');
        }
        
        $this->session->set_userdata('msg', $msg);
        //---build UI 
        //---define files to viewport
        $cpData['css'] = array($this->module_url . "assets/css/login.css" => 'Login Specific');
        
        //---
        $cpData['global_js'] = array(
            'base_url' => $this->base_url,
            'module_url' => $this->module_url,
            'show_warn' =>$this->config->item('show_warn'),
            'msg' => $msg,
            'msgcode' => (isset($cpData['msgcode'])) ? $cpData['msgcode'] : ''
        );
        $cpData['show_warn']=($this->config->item('show_warn') and $msg<>'');
        $cpData['token']=$token;
        $cpData['js'] = array($this->module_url . "assets/jscript/recover.js" => 'Login Specific');
                
        //si el token aun existe en la base y es decir que no fue usado
        $this->ui->compose('user/recover_newpass.php','user/bootstrap3.ui.php',$cpData);
        //sino tiene que ir a una pantalla que le diga que ya fue utilizado y que vuelva a colocar su mail
        
        
    }
    
     function save_new_pass(){
         

            //----LOAD LANGUAGE
        $this->lang->load('login', $this->config->item('language'));
        //---add language data
        $cpData['lang'] = $this->lang->language;
        
            $token  = $this->input->post('token');
            $result=(array)$this->user->get_token($token);

            if($result['token']==$token){//if the token matches with a saved idu
       
                $user_data = array();
                $clean= htmlspecialchars (utf8_decode($this->input->post('passw')));
                if(strlen($clean)<5){
                        $resp['msg']=$this->lang->line('mailpassno');
                        $resp['status']=false;
                        exit(json_encode($resp));
                }
       
                $user_data['passw']= md5($clean);

                //data user
                $user_data=(array)$this->user->get_user((int)$result['idu']);
                $user_data['passw']= md5($clean);   
            

                //guardamos la info
                $savedresult = $this->user->save($user_data);
                //borramos el token
                $tokenout = $this->user->delete_token($token);
               
                $resp['msg']=$this->lang->line('mailmsg2');
                $resp['status']=true;

                //$redir="/appfront/index.php";
                //exit("1,$basedir$redir");
            }else{
 
                $resp['msg']=$this->lang->line('mailalert');
                $resp['status']=false;
            } 
            echo json_encode($resp);
    } 

}
