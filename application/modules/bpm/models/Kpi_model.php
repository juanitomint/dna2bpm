<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');
/*
 * This class provides fuctions to register and manage Key Performance Indicators
 */

class Kpi_model extends CI_Model {

    function __construct() {
        parent::__construct();
        $this->idu = $this->user->idu;
        $this->load->library('cimongo/cimongo');
        $this->db = $this->cimongo;
    }

    function delete($idkpi) {
        $criteria = array('idkpi' => $idkpi);
        $this->db->where($criteria);        
        return $this->db->delete('kpi');
        
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
        $id = $idwf . '_' . chr(64 + rand(1, 26)) . chr(64 + rand(1, 26));
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
                    $id = $idwf . '_' . chr(64 + rand(1, 26)) . chr(64 + rand(1, 26));
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
        
        $this->delete($kpi['idkpi']);    
        return $this->db->insert('kpi',$kpi);
        
    }

    function get_filter($kpi) {
        $filter = array();
        if (isset($kpi['filter'])) {
            switch ($kpi ['filter']) {
                case 'group' :
                    $filter = array(
                        'idwf' => $kpi ['idwf'],
                    );
                    break;
                case 'owner' :
                    //----get user cases
                    // $rs=$this->bpm->get_cases_byFilter(
                    //     array(
                    //         'iduser'=>$this->user->idu,
                    //         'idwf'=>$kpi['idwf'],
                    //         ), array('id'));
                    // $cases=array_map(function($item){
                    //     return $item['id'];
                    // }
                    // ,$rs);
                    
                    $filter['iduser']=$this->user->idu;
                    $filter['idwf'] = $kpi ['idwf'];
                    break;
                case 'user' :
                    $query = array(
                        'idwf' => $kpi ['idwf'],
                        '$or'=>array(
                        array('iduser' => $this->idu),
                        array('assign' => $this->idu),
                            ),
                    );
                    $rs=$this->bpm->get_tokens_byFilter($query,array('case'));
                    $cases=array_map(function($item){
                        return $item['case'];
                    }
                    ,$rs);
                    $filter['id']=array('$in'=>$cases);
                    break;
                default : // ---filter by idwf
                    $filter = array(
                        'idwf' => $kpi ['idwf']
                    );
                    break;
            }
        }
        // ----process extra filters
        $filter_extra = array();
        if (isset($kpi ['filter_extra'])) {
            $filter_extra = @json_decode($kpi ['filter_extra']);
            switch (json_last_error()) {
                case JSON_ERROR_NONE :
                    // echo ' - No errors';
                    break;
                case JSON_ERROR_DEPTH :
                    echo ' - Maximum stack depth exceeded';
                    break;
                case JSON_ERROR_STATE_MISMATCH :
                    echo ' - Underflow or the modes mismatch';
                    break;
                case JSON_ERROR_CTRL_CHAR :
                    echo ' - Unexpected control character found';
                    break;
                case JSON_ERROR_SYNTAX :
                    echo ' - Syntax error, malformed JSON';
                    break;
                case JSON_ERROR_UTF8 :
                    echo ' - Malformed UTF-8 characters, possibly incorrectly encoded';
                    break;
                default :
                    echo ' - Unknown error';
                    break;
            }
        }
        ///---add resource Id if exists
        if(isset($kpi['resourceId'])){
            $filter['token_status.resourceId']=$kpi['resourceId'];
            //---if isset status then filter by token status
            if(isset($kpi['status']) && $kpi['status']<>''){    
                $filter['token_status']=array('$all'=>array(
                    array(
                        'resourceId'=>$kpi['resourceId'],
                        'status'=>$kpi['status']
                    )
                    ));
                // $filter['token_status.status']=$kpi['status'];
            }
            $filter = array_merge((array) $filter_extra, $filter);
        }
        // echo json_encode($filter);
        return $filter;
    }

}
