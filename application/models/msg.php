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
//        $this->config->load('user/config');
        $this->load->library('cimongo/cimongo');
        $this->db = $this->cimongo;
        
    }
    

    //---get that msg
    function get_msgs($iduser, $folder=null) {

        if($folder=='outbox'){
            $query = array('from' =>(double) $iduser);
        }else{
            // Para outbox
            $query = array('to' =>(double) $iduser);
            if (isset($folder))
                $query['folder'] = $folder;
        }
        

        $result = $this->mongo->db->msg->find($query)->sort(array('checkdate'=>-1));   

        return $result;
       
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

            //---TODO : Check if user want's to recive email copies
            if (isset($msg['to']) and isset($msg['from'])) {
                $this->save($msg);
                return $msg;
            } else {
                //---raise error
                $error_msg = "Called @ " . xdebug_call_file() . "<br/>Line:" . xdebug_call_line() . "<br/>from: <b>" . xdebug_call_function() . '</b><hr/>';
                show_error("Can't send message: incomplete data. Check 'from' an 'to' fields<br/>" . $error_msg);
            }
    }

    function remove($mongoid) {
        $mongoid = (is_object($mongoid)) ? $mongoid : new MongoId($mongoid);
        $options = array('safe' => true, 'justOne' => true);
        $criteria = array('_id' => $mongoid);
        return $this->mongo->db->msg->remove($criteria, $options);
    }
    
    function move($id,$folder='trash') {
    $mongoid=new MongoId($id);
    $query=array('$set' =>array('folder'=>$folder));
    $criteria = array('_id' => $mongoid);
    $rs=$this->mongo->db->msg->update($criteria, $query);
    }
    

    // Save a msg
    function save($msg) {
        $options = array('upsert' => true, 'safe' => true);
        return $this->mongo->db->msg->save($msg, $options);
    }

    // Get msg by id
    function get_msg($id) {
    $mongoid=new MongoId($id);
    $query = array('_id' => $mongoid);
    $result = $this->mongo->db->msg->findOne($query);
    return $result;
    }

    // Set or unset star
    function set_star($status,$id) {
    $mongoid=new MongoId($id);
    $status=($status==1)?(true):(false);
    $query=array('$set' =>array('star'=>$status));
    $criteria = array('_id' => $mongoid);
    $rs=$this->mongo->db->msg->update($criteria, $query);
    }

    // Was message read?
    function set_read($status,$id) {
    $mongoid=new MongoId($id);
    $status=($status==1)?(true):(false);
    $query=array('$set' =>array('read'=>$status));
    $criteria = array('_id' => $mongoid);
    $rs=$this->mongo->db->msg->update($criteria, $query);
    }


}//

?>
