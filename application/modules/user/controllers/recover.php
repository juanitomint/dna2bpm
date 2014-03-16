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

        $this->ui->compose('user/recover.php','user/bootstrap.ui.php',$cpData);
    }
    
    function Send() {
        //----LOAD LANGUAGE
        $this->lang->load('login', $this->config->item('language'));
        //---add language data
        $cpData['lang'] = $this->lang->language;
        $cpData['title'] = $this->lang->line('PageDescriptionR');
        $cpData['base_url'] = $this->base_url;
        $cpData['module_url'] = $this->module_url;
        $cpData['theme'] = $this->config->item('theme');
        
        $clean['email']  = $this->input->post('mail');
        
  ////////////////////////////////////////////////            
//        $email_pattern = '/^[^@\s<&>]+@([-a-z0-9]+\.)+[a-z]{2,}$/i';
//        if (!preg_match($email_pattern, $_POST['email']))
//        {
//        exit("0, Ingrese un email v&aacute;lido");
//        }
        // Chequeo datos atraves del email
        $dbobj=(array)$this->user->getbymailaddress($clean['email']);
       
        // Envio
        if(isset($dbobj['idu'])){ 

            $token=md5($dbobj['email'].$dbobj['idu']);
            //armamos el mail
            $content = $this->lang->line('mailsendpart1');
            $content.=" <strong>{$dbobj['nick']}</strong></p>";
            $content.=$this->lang->line('mailsendpart2');
            $content.="<a href='{$this->base_url}user/recover/new_pass/$token'>".$this->lang->line('mailsendpart3')."</a>";

            $this->email->clear();
            $config['mailtype'] = "html";
            $this->email->initialize($config);
            $this->email->set_newline("\r\n");
            $this->email->from('dna2@industria.gob.ar', 'Soporte');
            $list = array($clean['email']); //$list = array('xxx@gmail.com', 'xxx@gmail.com');
            $this->email->to($list);
            $data = array();
            $this->email->subject($this->lang->line('mailsubject'));
            $this->email->message($content);

//echo $content."<br>";

            if ($this->email->send()){
                echo $this->lang->line('mailmsg1')."</br> <a href='{$this->base_url}'>".$this->lang->line('mailback')."</a>";
                //save token
                $object['token']  = $token;
                $object['creationdate']  = date('Y-m-d H:i:s');
                $object['idu'] = (int)$dbobj['idu'];
                $result = $this->user->save_token($object);
                
            }else show_error($this->email->print_debugger());
            
                
        }else{
        exit("0, No se ha podido enviar el email. No existe el email");
        }

        
        
    }
    
    
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
        
        //si el token aun existe en la base y es decir que no fue usado
        $this->ui->compose('user/recover_newpass.php','user/bootstrap.ui.php',$cpData);
        //sino tiene que ir a una pantalla que le diga que ya fue utilizado y que vuelva a colocar su mail
        
        
    }
    
     function Save_new_pass(){
         
        // var_dump($this->input->post());
             //----LOAD LANGUAGE
            $this->lang->load('login', $this->config->item('language'));
            //---add language data
            $cpData['lang'] = $this->lang->language;
            $cpData['title'] = $this->lang->line('mailform');
            $cpData['base_url'] = $this->base_url;
            $cpData['module_url'] = $this->module_url;
            $cpData['theme'] = $this->config->item('theme');
        
            $token  = $this->input->post('token');
            $result=(array)$this->user->get_token($token);

            if($result['token']==$token){//if the token matches with a saved idu

                $user_data = array();
                $clean= htmlspecialchars (utf8_decode($this->input->post('password1')));
                if(strlen($clean)<5) exit($this->lang->line('mailpassno'));
                $user_data['passw']= md5($clean);

                //data user
                $user_data=(array)$this->user->get_user((int)$result['idu']);
                $user_data['passw']= md5($clean);   
                //var_dump($user_data);

                //guardamos la info
                $savedresult = $this->user->save($user_data);
                //borramos el token
                $tokenout = $this->user->delete_token($token);
               
                echo $this->lang->line('mailmsg2')."<a href='{$this->base_url}'>".$this->lang->line('mailback')."</a>";


                //$redir="/appfront/index.php";
                //exit("1,$basedir$redir");
            }else exit($this->lang->line('mailalert'));
            
    } 

}
