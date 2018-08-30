<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');
/**
 * This Class provides a Mongo data source
 * if recordset count == 1 then the flat recorset is returned
 * if recordset count >1 then an array of records is returned
 * 
 * */
class Mongo_case_connector extends CI_Model {

    function __construct() {
        parent::__construct();
        $this->load->library('mongowrapper');
    }

    function get_data($resource,$shape, $wf) {
        //---connect to database and retrive data as specified
        if (isset($resource['datastoreref'])) {
            
            $fields = (isset($resource['fields'])) ? $resource['fields'] : null;
            $query = array('idcase'=>$wf->case,'idwf'=>$wf->idwf);
            //---select the database
            if ($resource['datastoreref']) {
                $this->mongowrapper->db = $this->mongowrapper->selectDB($resource['datastoreref']);
            }
            if (isset($fields)) {
                $rs = $this->mongowrapper->db->selectCollection($resource['itemsubjectref'])->find($query, $fields);
            } else {
                $rs = $this->mongowrapper->db->selectCollection($resource['itemsubjectref'])->find($query);
            }
            if (isset($resource['sort']))
                $rs->sort($sort);
            $rtn_arr = array();
            
            if($rs->count()>1){
                while ($arr = $rs->getNext()) {
                    //---remove _id to avoid save problems
                    $arr['_id'] = null;
                    $rtn_arr[]=$arr;
                }
                
            } else {
                while($arr = $rs->getNext()){
                    $arr['_id'] = null;
                    $rtn_arr=$arr;
                }
            }
            return $rtn_arr;
        }
    }

}