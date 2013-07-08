<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');
/*
 * This class provides fuctions to register and manage Key Performance Indicators
 */

class Kpi_model extends CI_Model {

    function __construct() {
        parent::__construct();
        $this->idu = (int) $this->session->userdata('iduser');
        $this->load->library('cimongo/cimongo');
        $this->db = $this->cimongo;
    }

    function delete($idkpi) {
        $options = array('safe' => true, 'justOne' => true);
        $criteria = array('idkpi' => $idkpi);
        //var_dump2($options,$criteria);
        $result = $this->mongo->db->kpi->remove($criteria, $options);
        if ($result['ok'] == 1) { //is OK
            return true;
        } else {
            return false;
        }
    }

    function get($idkpi) {
        $query = array('idkpi' => (int)$idkpi);
//        var_dump2($query);
        $result = $this->db->get_where('kpi', $query)->result_array();
        if ($result)
            return $result[0];
    }

    function get_model($idwf) {
        $query = array('idwf' => $idwf);
//        var_dump2($query);
        $result = $this->db->get_where('kpi', $query)->result_array();
        return $result;
    }

    function save($kpi) {
        $options = array('safe' => true);
        $wf = $this->mongo->db->kpi->save($kpi, $options);
    }

}