<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Alerts extends CI_Model {

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


    
    // ===== Get Alerts using a filter
    function get_alerts_by_filter($filter = array()) {
    	return $this->mongo->db->alerts->find($filter);
    }

 

}