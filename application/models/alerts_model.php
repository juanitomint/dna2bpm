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
        $this->bpm_container='alerts';
    }


    
    // ===== Get Alerts using a filter
    function get_alerts_by_filter($target = array()) {

        $this->db->where_in('target',$target);
        $this->db->where('read !=',$this->idu);
        //return $this->db->get($this->bpm_container)->result_array();
$this->dismiss();

    }
    
    // ===== dismiss alert
    function dismiss($id) {
        
        $query = array(
            '_id' => new MongoId($id),
        );
        $this->db->where($query)->addtoset('read', array($this->idu))->update($this->bpm_container);
        
    
    }
 

}