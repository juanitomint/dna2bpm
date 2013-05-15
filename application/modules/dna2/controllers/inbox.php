<?php

class Inbox extends MX_Controller {

    function __construct() {
        parent::__construct();
        $this->load->config('config');
        $this->load->library('parser');
        $this->load->library('ui');
        $this->load->model('msg');
        
        //---base variables
        $this->base_url = base_url();
        $this->module_url = base_url() . 'dna2/';
        $this->user->authorize();
        //----LOAD LANGUAGE
        $this->lang->load('library', $this->config->item('language'));
        $this->idu = (float) $this->session->userdata('iduser');
        
        
    }
    

    function Index() {

        $customData['user'] = (array) $this->user->get_user($this->idu);
        $customData['inbox_icon'] = 'icon-envelope';
        $customData['inbox_title'] = $this->lang->line('Inbox');
        $customData['js'] = array($this->module_url . "assets/jscript/inbox.js"=>'Inbox JS'); 
        $customData['css'] = array($this->module_url . "assets/css/dashboard.css" => 'Dashboard CSS');
        
        $mymgs = $this->msg->get_msgs($this->idu);
        
        foreach ($mymgs as $msg) {
            $msg['msgid'] = $msg['_id'];
            $msg['date'] = substr($msg['checkdate'], 0, 10);
            $msg['icon_star'] = (isset($msg['star'])&&$msg['star']==true) ? ('icon-star') : ('icon-star-empty');
            $msg['read'] = (isset($msg['read'])&&$msg['read']==true) ? ('muted') : ('');
            if(isset($msg['from'])){
                $userdata = $this->user->get_user($msg['from']);
                $msg['sender'] = $userdata->nick; 
            }else{
                 $msg['sender'] = "System"; 
            }

            

            $customData['mymsgs'][] = $msg;
        }

        Modules::run('dna2/dna2/render','inbox',$customData);
    }
  

//    function fill_inbox($folder=null){
//        $cpData = array();
//        $cpData = $this->lang->language;
//        $cpData['base_url'] = base_url();
//        $mymgs = $this->msg->get_msgs($this->idu,$folder);
//        $head = '{rows:[';
//        $i=1;
//        $linea="";
//        foreach ($mymgs as $msg) {
//         $fecha=date("d/m/Y",strtotime($msg["checkdate"]));
//         $u = $this->user->get_user($msg["from"]);
//         $usuario="{$u["name"]},    {$u["lastname"]}";
//         $class_star=($msg["star"])?('star_on'):('');
//         $class_read=($msg["read"])?('read_on'):('');
//
//             $data = "'{$msg["subject"]}^javascript:open_msg(&#39;".$msg["_id"]."&#39;)^_self',";
//             //$data .= "{'value':'$usuario','title':'1'},";
//             $data .= "'<span  class=\"read $class_read read{$msg["_id"]}\"></span>^javascript:read(&#39;".$msg["_id"]."&#39;)^_self',";
//             $data .= "'<span  class=\"star $class_star star{$msg["_id"]}\"></span>^javascript:star(&#39;".$msg["_id"]."&#39;)^_self',";
//             $data .= "'$usuario ({$msg["from"]})',";
//             $data .= "'{$msg["folder"]}',";
//             $data .= "'$fecha'";
//             
//        $linea.= "{id:".$i++.",data:[".$data."]},";
//        }
//        $tail = ']}';
//        echo $head . $linea . $tail;
//
//    }

    // get msg by id
    function get_msg(){
    $msgid=$this->input->post('id');
    $mymgs = $this->msg->get_msg($msgid);
     foreach ($mymgs as $msg) {
        echo "<h3>{$msg["subject"]}</h3>";
        echo "<div class=inbox_content>".$msg["body"]."</div>";
     }
    }
    
    // save star value
    function set_star(){
    $state=$this->input->post('state');
    $id=$this->input->post('msgid');
    $this->msg->set_star($state,$id);
    }

    // save star value
    function set_read(){
    $state=$this->input->post('state');
    $id=$this->input->post('msgid');
    $this->msg->set_read($state,$id);
    }
    
    function send(){
        $data=$this->input->post('data');
        $to=(int)$data[0]['value'];
        $subject=$data[1]['value'];
        $body=$data[2]['value'];
        $msg=array(
        'subject'=>$subject,
        'body'=>$body,
        'from'=>$this->idu
        );

        $this->msg->send($msg,$to);
    }
    
    function new_msg(){
        $customData['user'] = (array) $this->user->get_user($this->idu);

        $customData['js'] = array($this->module_url . "assets/jscript/inbox.js"=>'Inbox JS'); 
        $customData['css'] = array($this->module_url . "assets/css/dashboard.css" => 'Dashboard CSS');
        
        Modules::run('dna2/dna2/render','inbox_new',$customData);
    }
    

} //

?>