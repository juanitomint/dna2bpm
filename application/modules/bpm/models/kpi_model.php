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
        $options = array('w' => true, 'justOne' => true);
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
        $query = array('idkpi' => $idkpi);
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
    function gen_kpi($idwf) {
        $insert = array();
        $trys = 10;
        $i = 0;
        $id =$idwf.'_'. chr(64 + rand(1, 26)) . chr(64 + rand(1, 26));
        //---if passed specific id
        if (func_num_args() > 1) {
            $id = func_get_arg(1);
            $passed = true;
            //echo "passed: $id<br>";
        }
        $hasone = false;

        while (!$hasone and $i <= $trys) {//---search until found or $trys iterations
            $query = array('id' => $id);
            $result = $this->db->get_where('kpi', $query)->result();
            $i++;
            if ($result) {
                if ($passed) {
                    show_error("id:$id already Exists in db.case");
                    $hasone = true;
                    break;
                } else {//---continue search for free id
                    $id = $idwf.'_'. chr(64 + rand(1, 26)) . chr(64 + rand(1, 26));
                }
            } else {//---result is null
                $hasone = true;
            }
        }
        if (!$hasone) {//-----cant allocate free id
            show_error("Can't allocate an id in 'case' after $trys attempts");
        }
        return $id;
    }
    function save($kpi) {
        $options = array('w' => true);
        $wf = $this->mongo->db->kpi->save($kpi, $options);
    }

}