<?php

class Inbox extends MX_Controller {

    function __construct() {
        parent::__construct();
        $this->load->config('config');
        $this->load->library('parser');
        $this->load->library('ui');
        $this->load->model('msg');
        $this->load->model('user');

        //---base variables
        $this->base_url = base_url();
        $this->module_url = base_url() . $this->router->fetch_module().'/';
        $this->user->authorize();
        //----LOAD LANGUAGE
        $this->lang->load('library', $this->config->item('language'));
        $this->idu = (int) $this->session->userdata('iduser');
        


        
    }
    

    function Index() {

        $customData['user'] = (array) $this->user->get_user($this->idu);
        $customData['inbox_icon'] = 'icon-envelope';    
        $customData['js'] = array($this->module_url . "assets/jscript/inbox.js"=>'Inbox JS'); 
        $customData['css'] = array($this->module_url . "assets/css/inbox.css" => 'Dashboard CSS');

        // Determino el folder
        $folders=array('inbox','trash','outbox');
        $source='to';
        if($this->uri->segment(4) && in_array($this->uri->segment(4),$folders)){
            $folder=$this->uri->segment(4);
        }else{
            $folder='inbox';
        }
        $customData['inbox_title'] = ucfirst($folder);

        
        // Messages Loop
        $mymgs = $this->msg->get_msgs($this->idu,$folder);
        
        foreach ($mymgs as $msg) {
            $msg['msgid'] = $msg['_id'];
            $msg['subject']=(strlen($msg['subject'])!=0)?($msg['subject']):("No Subject");
            $msg['msg_date'] = substr($msg['checkdate'], 0, 10);
            $msg['msg_time'] = date('l jS \of F Y h:i:s A',strtotime($msg['checkdate']));
            $msg['icon_star'] = (isset($msg['star']) && $msg['star']==true) ? ('icon-star') : ('icon-star-empty');
            $msg['read'] = (isset($msg['read'])&&$msg['read']==true) ? ('muted') : ('');
            $msg['body']=nl2br($msg['body']);
            $userdata = $this->user->get_user($msg['from']);
            $msg['from_name']=(empty($userdata))?('No user'):($userdata->nick);
            $userdata = $this->user->get_user($msg['to']);
            $msg['to_name']=(empty($userdata))?('No user'):($userdata->nick);            

            $customData['mymsgs'][] = $msg;
        }


        Modules::run('dna2/dna2/render','inbox',$customData);
    }
    
    function widget() {

     	$customData['user'] = (array) $this->user->get_user($this->idu);
     	$customData['inbox_icon'] = 'icon-envelope';
     	$customData['js'] = array('icheck',$this->module_url . "assets/jscript/inbox.js"=>'Inbox JS');
     	$customData['css'] = array($this->module_url . "assets/css/inbox.css" => 'Dashboard CSS');
     	$customData['base_url'] = $this->base_url;
     	$customData['module_url'] = $this->module_url;
    	// Determino el folder
    	$folders=array('inbox','trash','outbox','star');
    	$source='to';
    	if($this->uri->segment(3) && in_array($this->uri->segment(3),$folders)){
    		$folder=$this->uri->segment(3);
    	}else{
    		$folder='inbox';
    	}
    	$customData['folder']=$folder;
    	// Messages Loop
    	$mymgs = $this->msg->get_msgs($this->idu,$folder);
    
    	foreach ($mymgs as $msg) {
    		$msg['msgid'] = $msg['_id'];
    		$msg['subject']=(strlen($msg['subject'])!=0)?($msg['subject']):("No Subject");
    		$msg['msg_date'] = substr($msg['checkdate'], 0, 10);
    		$msg['msg_time'] = date('l jS \of F Y h:i:s A',strtotime($msg['checkdate']));
    		$msg['icon_star'] = (isset($msg['star']) && $msg['star']==true) ? ('fa fa-star') : ('fa fa-star-o');
    		$msg['read'] = (isset($msg['read'])&&$msg['read']==true) ? ('read') : ('unread');
    		$msg['body']=nl2br($msg['body']);
    		$userdata = $this->user->get_user($msg['from']);
    		$msg['from_name']=(empty($userdata))?('No user'):($userdata->nick);
    		$userdata = $this->user->get_user($msg['to']);
    		$msg['to_name']=(empty($userdata))?('No user'):($userdata->nick);
    
    		$customData['mymsgs'][] = $msg;
    	}

    //var_dump($customData);
    $customData['content']=$this->parser->parse('inbox/inbox2', $customData, true, true);
    return $customData;
//     	Modules::run('dna2/dna2/render','inbox',$customData);
    }
    
  

    // get msg by id
    function get_msg(){
    $msgid=$this->input->post('id');
    $mymgs = $this->msg->get_msg($msgid);
    $this->msg->set_read(1,$msgid); // Marco leido
	echo json_encode($mymgs);
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
	    foreach($id as $myid){
	    	$this->msg->set_read($state,$myid);
	    }
    }
    
    function send(){

        $data=$this->input->post('data');
        
        $to=explode(",",$data[0]['value']);
        $subject=$data[1]['value'];
        $body=$data[2]['value'];
        $msg=array(
        'subject'=>$subject,
        'body'=>$body,
        'from'=>$this->idu
        );
        $i=0;
        
        foreach($to as $user){
            $this->msg->send($msg,(int)$user);
            $i++;
        }
        echo $i;
        
    }
    
    function new_msg(){

         $customData['user'] = (array) $this->user->get_user($this->idu);

//         // REPLY: segment 4 is msgid 
$customData['reply']=0;

//         if($this->uri->segment(4)){
//             $msgid=$this->uri->segment(4);
//             $mymgs = $this->msg->get_msg($msgid);
            
//             $sender=$this->user->get_user($mymgs['from']);

//             $customData['reply']=1;
//             $customData['reply_name']=$sender->nick;
//             $customData['reply_idu']=$sender->idu;
//             $customData['reply_body']=$mymgs['body'];
//             $customData['reply_title']=$mymgs['subject'];
//             $customData['reply_date']=$mymgs['checkdate'];
//         }
                

//         $customData['js'] = array(
//             $this->base_url . "jscript/select2-3.4.5/select2.min.js"=>'Select JS',
//             $this->module_url . "assets/jscript/inbox_new.js"=>'Inbox JS'
//             ); 

//         $customData['css'] = array(
//             $this->base_url . "jscript/select2-3.4.5/select2.css" => 'Select CSS',
//             $this->base_url . "jscript/select2-3.4.5/select2-bootstrap.css" => 'Select BT CSS',
//             $this->module_url . "assets/css/dashboard.css" => 'Dashboard CSS'
//             );

         $this->parser->parse('inbox/inbox_new', $customData);

    }
    
    // Get list of users
    function get_users(){
       
        $row_array = array();
        $term=$this->input->post('term');

        $allusers=$this->user->get_users(null,100,null,$term,null,'both');
        foreach($allusers as $myuser){
           $row_array[]=array('text'=> $myuser->nick,'id'=>$myuser->idu);
        }
        $ret['results']=$row_array;
        echo json_encode($ret);
        
    }
    
    // Move msg to trash
    
    function move(){
        $msgid=$this->input->post('msgid');
        $folder=$this->input->post('folder');
        foreach($msgid as $msg){
        	$this->msg->move($msg,$folder);
        }
    }
   
        // Move msg to trash
    
    function remove(){
        $msgid=$this->input->post('msgid');
        foreach($msgid as $msg){
        	 $this->msg->remove($msg);
        }
       
    }
    


    
} //

?>