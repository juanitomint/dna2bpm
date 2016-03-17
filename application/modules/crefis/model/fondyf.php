<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class crefis extends CI_Model {
   

    function __construct() {
        parent::__construct();
        $this->idu = (int) $this->session->userdata('iduser');
        $this->load->library('cimongo/cimongo');
        $this->db = $this->cimongo;
        $this->load->config('bpm/config');
    }



    /* MONTOS POR ESTADO */

    function get_amount_stats_by_id($query) {
        $rtn = array();
        $container = 'container.proyectos_crefis';
        $fields = array('8334', '8326', '8573');
        $rs = $this->mongowrapper->db->$container->find($query, $fields);
        foreach ($rs as $list) {
            unset($list['_id']);
            $rtn[] = $list;
        }
        return $rtn;
    }

    function get_amount_stats($filter) {

        /* get ids */
        $all_ids = array();
        $arr_status = array();


        $allcases = $this->get_cases_byFilter($filter, array('id', 'idwf', 'data'));

        foreach ($allcases as $case) {
            if (isset($case['data']['Proyectos_crefis']['query']))
                $all_ids[] = $case['data']['Proyectos_crefis']['query'];
        }


        $get_value = array_map(function ($all_ids) {
            return $this->get_amount_stats_by_id($all_ids);
        }, $all_ids);

        return $get_value;
    }

    function get_evaluator_by_project_by_id($query) {
        $rtn = array();
        $container = 'container.proyectos_crefis';
        $fields = array('8668', 'id', '8339', '8334');
        $query = array(8668 => array('$exists' => true));
        $rs = $this->mongowrapper->db->$container->find($query, $fields);
        foreach ($rs as $list) {
            unset($list['_id']);
            $rtn[] = $list;
        }               
        
        return $rtn;
    }

    function get_evaluator_by_project($filter) {


        /* get ids */
        $all_ids = array();
        $arr_status = array();

        $allcases = $this->get_cases_byFilter($filter, array('id', 'idwf', 'data'));

        foreach ($allcases as $case) {
            if (isset($case['data']['Proyectos_crefis']['query']))
                $all_ids[] = $case['data']['Proyectos_crefis']['query'];
        }


        $get_value = array_map(function ($all_ids) {
            return $this->get_evaluator_by_project_by_id($all_ids);
        }, $all_ids);

        return $get_value;
    }   
   

}
