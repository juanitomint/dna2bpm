<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');
/**
 * This Class provides a Mongo data source
 * if recordset count == 1 then the flat recorset is returned
 * if recordset count >1 then an array of records is returned
 * 
 * */
class Mongo_formio_connector extends CI_Model {

    function __construct() {
        parent::__construct();
        $this->load->library('mongowrapper');
    }

    function get_data($resource,$shape, $wf) {
        $resource['datastoreref']= $resource['datastoreref']=='' ?'formioapp':$resource['datastoreref']; 
        $resource['itemsubjectref']= $resource['itemsubjectref']=='' ?  'submissions':$resource['itemsubjectref']; 
        //---connect to database and retrive data as specified
        if (isset($resource['datastoreref'])) {
            
            $fields = (isset($resource['fields'])) ? $resource['fields'] : null;
            $query = array('data.idcase'=>$wf->case,'data.idwf'=>$wf->idwf);
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
            
            
                while ($submission = $rs->getNext()) {
                     $form=$this->mongowrapper->db->selectCollection('forms')->findOne(
                         array('_id'=>$submission['form']));
                    //---remove _id to avoid save problems
                    $arr['_id'] = null;
                    
                    $rtn_arr[$submission['data']['title']]=$submission['data'];
                }
                
            
            
            return $rtn_arr;
        }
    }

}