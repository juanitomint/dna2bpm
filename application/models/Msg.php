<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Msg extends CI_Model {

//    function Msg() {
//        parent::__construct();
//    }
    function __construct() {
        parent::__construct();
        $this->idu = $this->session->userdata('iduser');
        $this->load->library('mongowrapper');
        $this->load->library('cimongo/cimongo');
        $this->db = $this->cimongo;
        
        //ini_set('display_errors',1);
    }

//---get that msg
    function get_msgs($iduser, $folder = null, $skip = null, $limit = null, $filter = null) {

// Folder check
        if ($folder == 'outbox') {
            $query = array('from' => (double) $iduser);
        } elseif ($folder == 'star') {
            $query = array(
                'to' => (double) $iduser,
                'star' => true,
                'folder' => array('$ne' => 'trash')
            );
        } else {
            $query = array('to' => (double) $iduser);
            if (isset($folder))
                $query['folder'] = $folder;
        }

// Filter
        if (!is_null($filter)) {
            if(is_array($filter)){
            	$query+=$filter;
            }else{
            	//Check subject
            	$myregex = new MongoRegex("/$filter/i");
            	$query['$or']=array(array("subject" => $myregex),array("body" => $myregex));
            }
        }

    $this->db->where($query);
    $this->db->order_by(array('checkdate' => -1));
    $rs = $this->db->get('msg',$limit,$skip)->result_array();
    return $rs;

    }

    // ===== Get MSGs using a filter
    function get_msgs_by_filter($filter = array()) {
        $this->db->where($filter);
        //$this->db->select($fields);
        //$this->db->order_by($sort);
        $rs = $this->db->get('msg');
        return $rs->result_array();
    }

    function count_msgs($iduser, $folder='inbox') {
        $query = array(
            'to' => (double) $iduser,
            'folder' => $folder
        );
        //if(!is_null($read))$query['read']=$read;
        $this->db->where($query);
        $rs = $this->db->get('msg');
        return count($rs->result_array());
    }

//---send msg multiple users
    function send($msg, $to) {

        $msg['to'] = $to;
//---set defaults
        $msg['from'] = (isset($msg['from'])) ? $msg['from'] : 666;
        $msg['read'] = (isset($msg['read'])) ? $msg['read'] : false;
        $msg['star'] = (isset($msg['star'])) ? $msg['star'] : false;
        $msg['folder'] = (isset($msg['folder'])) ? $msg['folder'] : 'inbox';
//---set msg timestamp
        $msg['checkdate'] = date('Y-m-d H:i:s');
        $user = $this->user->get_user($to);

//---TODO : Check if user want's to recive email copies
        if (isset($msg['to']) and isset($msg['from'])) {
            $this->db->insert('msg', $msg);
            $sendEmail = false;
            if (!property_exists($user,"notification_by_email")) {
                $sendEmail = true;
            } elseif ($user->notification_by_email == 'yes') {
                $sendEmail = true;
            }
            if ($sendEmail)
                $this->send_mail($msg, $user);
            return $msg;
        } else {
//---raise error
            $error_msg = "Called @ " . xdebug_call_file() . "<br/>Line:" . xdebug_call_line() . "<br/>from: <b>" . xdebug_call_function() . '</b><hr/>';
            show_error("Can't send message: incomplete data. Check 'from' an 'to' fields<br/>" . $error_msg);
        }
    }

    function send_mail($msg, $user) {
    $this->load->config('email');
    $debug=(null!==$this->config->item('debug')) ? $this->config->item('debug'):false;
    if($debug) echo '<pre>';
        if (property_exists($user,'email')) {
            $this->load->library('phpmailer/phpmailer');

            $ok = false;
            $mail = new $this->phpmailer;
            $mail->IsSMTP(); // telling the class to use SMTP
            $mail->Host = $this->config->item('smtp_host'); // SMTP server
            // enables SMTP debug information (for testing)
            // 1 = errors and messages
            // 2 = messages only
            if($debug){
            $mail->SMTPDebug = 1;

            }
            //---ReplyTo
            $sender= $this->user->get_user($msg['from']);
            if($sender->email<>''){
             $sname=(property_exists($sender,'name'))?$sender->name:'???';
             $slastname=(property_exists($sender,'lastname'))?$sender->lastname:'???';
                $mail->AddReplyTo($sender->email,$sname.' '.$slastname);
            }
            $mail->SetFrom($sender->email, $sname.' '.$slastname);
            $mail->Subject = utf8_decode($this->config->item('mail_suffix').' ' . $msg['subject']);
            $mail->AltBody = "To view the message, please use an HTML compatible email viewer!"; // optional, comment out and test
            $mail->IsHTML(true);
            $mail->MsgHTML(nl2br($msg['body']));
                
            $mail->AddAddress($user->email, "");

//        $mail->AddAttachment("images/phpmailer.gif");      // attachment
//        $mail->AddAttachment("images/phpmailer_mini.gif"); // attachment

            if (!$mail->Send()) {
                if($debug) {
                    echo '/<pre>';
                    var_dump($this->config->item('smtp_user'), $this->config->item('smtp_user_name'),$mail->ErrorInfo);
                    exit;
                }
                return "error: " . $mail->ErrorInfo;
            } else {
                return true;
            }
        }
    }
    
    // ==== Generic mail sender
    
    function sendmail($config=array()){
      
        $default=array(
        'subject'=>'',
        'body'=>'',
        'reply_email'=>'',
        'reply_nicename'=>'',
        'to'=>array(),
        'cc'=>array(),
        'bcc'=>array(),
        'debug'=>0,
        'is_html'=>true,
        'db_log'=>true
        );
        
        $myconfig=array_merge($default,$config);
            
            
        if(empty($myconfig['to']))return;
        if(empty($myconfig['subject']))return;
      
        $this->load->library('phpmailer/phpmailer');
        $this->load->config('email');

        $mail = new $this->phpmailer;
        $mail->IsSMTP();
        $mail -> charSet = "UTF-8"; 
        
        if($myconfig['debug']>0)$mail->SMTPDebug = $myconfig['debug'];
        $mail->Username = $this->config->item('smtp_user');             
        $mail->Password =  $this->config->item('smtp_passw');
        $mail->Host = $this->config->item('smtp_host');
        $mail->SetFrom($this->config->item('smtp_user'), $this->config->item('smtp_user_name'));
        $mail->Subject = utf8_decode($this->config->item('mail_suffix').' ' . $myconfig['subject']);

        $mail->AltBody = "To view the message, please use an HTML compatible email viewer!"; //
        if(!empty($myconfig['reply_email'])){
            $nicename=(empty($myconfig['reply_nicename']))?($myconfig['reply_email']):($myconfig['reply_nicename']);
            $mail->AddReplyTo($myconfig['reply_email'], $nicename);
        }
          
        $mail->IsHTML($myconfig['is_html']);

        $mail->Body=utf8_decode($myconfig['body']);

        //==== Lets send this mails!
        
        foreach($myconfig['to'] as $email => $nicename){
            $mail->AddAddress($email, $nicename);
        }

        foreach($myconfig['cc'] as $email => $nicename){
            $mail->AddCC($email, $nicename);
        }            

        foreach($myconfig['bcc'] as $email => $nicename){
            $mail->AddBCC($email, $nicename);
        }            
        
        if (!$mail->Send()) {
            $myconfig['status']=false;
            $myconfig['error']=$mail->ErrorInfo;
        } else {
            $myconfig['status']=true;
        }     

         //== DB Log 
        if($myconfig['db_log']){
            
         $myconfig['date']=new MongoDate(time());
         $myconfig['idu']=$this->idu;
         $this->db->insert('sendmail', $myconfig); 

        }
        
         
         return $myconfig['status'];

        
    }

    function remove($mongoid) {
        $mongoid = (is_object($mongoid)) ? $mongoid : new MongoId($mongoid);
        $this->db->where(array('_id' => $mongoid));
        $rs= $this->db->delete('msg' )->result_array();
    }

    function move($id, $folder = 'trash') {
        $mongoid = new MongoId($id);
        $data = array('folder' => $folder);
        $query = array('_id' => $mongoid);
        $this->db->where($query);
        $rs = $this->db->update('msg',$data);
    }

// Save a msg
    // function save($msg) {
    //     $options = array('upsert' => true, 'w' => true);
    //     return $this->mongowrapper->db->msg->save($msg, $options);
    // }

// Get msg by id
    function get_msg($id) {
        $mongoid = new MongoId($id);
        $query = array('_id' => $mongoid);
        $this->db->where($query);
        $rs = $this->db->get('msg');
        return $rs->result_array();


    }

// Set or unset star
    function set_star($status, $id) {
        $mongoid = new MongoId($id);
        $status = ($status == 'on') ? (true) : (false);
        $query = array('_id' => $mongoid);
        $data=array('star' => $status);
        $this->db->where($query);
        $rs = $this->db->update('msg',$data);

    }

// Was message read?
    function set_read($status, $id) {
        $mongoid = new MongoId($id);
        $status = ($status == 'read') ? (true) : (false);
        $query = array('_id' => $mongoid);
        $data=array('read' => $status);
        $this->db->where($query);
        $rs = $this->db->update('msg',$data);
    }

// Set Tag?
    function set_tag($tag, $id) {
        $mongoid = new MongoId($id);
        $data = array('tag' => $tag);
        $query = array('_id' => $mongoid);
        $this->db->where($query);
        $rs = $this->db->update('msg',$data);

    }

}