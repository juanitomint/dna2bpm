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
    }


    
    // ===== Get Alerts using a filter
    function get_alerts_by_filter($filter = array()) {
    	return $this->mongo->db->alerts->find($filter);
    }
    
    // ===== dismiss alert
    function dismiss($id) {
    	$mongoid = new MongoId($id);
    	$query = array('$addToSet'=>array('read' => $this->idu));
    	$criteria = array('_id' => $mongoid);
    	$rs = $this->mongo->db->alerts->update($criteria, $query);
    }
 

}