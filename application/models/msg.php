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
        $this->load->library('mongo');
        $this->load->library('cimongo/cimongo');
        $this->db = $this->cimongo;
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

// Query build
        $pipe = $this->mongo->db->msg->find($query);
        if (!is_null($skip))
            $pipe = $pipe->skip($skip);
        if (!is_null($limit))
            $pipe = $pipe->limit($limit);
        $pipe = $pipe->sort(array('checkdate' => -1));

        return $pipe;
    }
    
    // ===== Get MSGs using a filter
    function get_msgs_by_filter($filter = array()) {
    	return $this->mongo->db->msg->find((array)$filter);
    }

    function count_msgs($iduser, $folder='inbox') {
        $query = array(
            'to' => (double) $iduser,
            'folder' => $folder
        );
        //if(!is_null($read))$query['read']=$read;
        return $this->mongo->db->msg->find($query)->count();
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
            $this->save($msg);
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
        
        if (property_exists($user,'email')) {
            $this->load->library('phpmailer/phpmailer');
            $this->load->config('email');
            $ok = false;
            $mail = $this->phpmailer;
            $mail->IsSMTP(); // telling the class to use SMTP
            $mail->Host = $this->config->item('smtp_host'); // SMTP server
            $mail->SMTPDebug = 0;                     // enables SMTP debug information (for testing)
// 1 = errors and messages
// 2 = messages only
            $mail->SetFrom($this->config->item('smtp_user'), $this->config->item('smtp_user_name'));
            $mail->Subject = utf8_decode($this->config->item('mail_suffix').' ' . $msg['subject']);
            $mail->AltBody = "To view the message, please use an HTML compatible email viewer!"; // optional, comment out and test
            $mail->IsHTML(true);
            $mail->MsgHTML(nl2br($msg['body']));

            $mail->AddAddress($user->email, "");

//        $mail->AddAttachment("images/phpmailer.gif");      // attachment
//        $mail->AddAttachment("images/phpmailer_mini.gif"); // attachment

            if (!$mail->Send()) {
                return "error: " . $mail->ErrorInfo;
            } else {
                return true;
            }
        }
    }

    function remove($mongoid) {
        $mongoid = (is_object($mongoid)) ? $mongoid : new MongoId($mongoid);
        $options = array('w' => true, 'justOne' => true);
        $criteria = array('_id' => $mongoid);
        return $this->mongo->db->msg->remove($criteria, $options);
    }

    function move($id, $folder = 'trash') {
        $mongoid = new MongoId($id);
        $query = array('$set' => array('folder' => $folder));
        $criteria = array('_id' => $mongoid);
        $rs = $this->mongo->db->msg->update($criteria, $query);
    }

// Save a msg
    function save($msg) {
        $options = array('upsert' => true, 'w' => true);
        return $this->mongo->db->msg->save($msg, $options);
    }

// Get msg by id
    function get_msg($id) {
        $mongoid = new MongoId($id);
        $query = array('_id' => $mongoid);
        $result = $this->mongo->db->msg->findOne($query);
        return $result;
    }

// Set or unset star
    function set_star($status, $id) {
        $mongoid = new MongoId($id);
        $status = ($status == 'on') ? (true) : (false);
        $query = array('$set' => array('star' => $status));
        $criteria = array('_id' => $mongoid);

        $rs = $this->mongo->db->msg->update($criteria, $query);
    }

// Was message read?
    function set_read($status, $id) {
        $mongoid = new MongoId($id);
        $status = ($status == 'read') ? (true) : (false);
        $query = array('$set' => array('read' => $status));
        $criteria = array('_id' => $mongoid);
        $rs = $this->mongo->db->msg->update($criteria, $query);
    }

// Set Tag?
    function set_tag($tag, $id) {
        $mongoid = new MongoId($id);
        $query = array('$set' => array('tag' => $tag));
        $criteria = array('_id' => $mongoid);

        $rs = $this->mongo->db->msg->update($criteria, $query);
    }

}