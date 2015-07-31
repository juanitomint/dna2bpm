<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Alerts_model extends CI_Model {

    function __construct() {
        parent::__construct();
        $this->idu = $this->session->userdata('iduser');
//        $this->config->load('user/config');
        $this->load->library('cimongo/cimongo');
        $this->db = $this->cimongo;
        $this->container='alerts';
    }


    
    // ===== Get Alerts using a filter
    function get_alerts_by_filter($target = array()) {
        $this->db->where_in('target',$target);
        //$this->db->where('read !=',$this->idu);
        $this->db->where_not_in('read',array($this->idu));
        $res=$this->db->get($this->container)->result_array();
        return $res;
    }
    
    // ===== dismiss alert
    function dismiss($id) {
        $query = array(
            '_id' => new MongoId($id),
        );
        $this->db->where($query)->addtoset('read', array($this->idu))->update($this->container);
    }
    
    
    function create_alert($alert){

       if(empty($alert))return false;
       if (!array_key_exists('subject', $alert)) return false;
       if (!array_key_exists('body', $alert)) return false;
       // target ie.
       // array("fondyf/json/fondyf_proyectos.json",1,2) 
       // json path or idgroup
       
        $default=array(
        'subject'=>'Untitled',
        'body'=>'My body',
        'class'=>'info',
        'read'=>array(),
        'show'=>1,
        'target'=>array() 
        );
        $alert+=$default;
        // info de creacion
        $fecha=date('Y-m-d H:i:s');
        $alert['author']=$this->idu;
        $alert['start_date']=$fecha;
        
        return $this->db->insert($this->container, $alert); 
    }
    


}