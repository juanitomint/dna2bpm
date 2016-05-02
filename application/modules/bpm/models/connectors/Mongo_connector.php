<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');
/**
 * This Class provides a Mongo data source
 * if recordset count == 1 then the flat recorset is returned
 * if recordset count >1 then an array of records is returned
 * 
 * */
class Mongo_connector extends CI_Model {

    function Mongo_connector() {
        parent::__construct();
        $this->load->library('mongowrapper');
    }

    function get_data($resource) {
        //---connect to database and retrive data as specified
        if (isset($resource['datastoreref']) && isset($resource['query'])) {
            $fields = (isset($resource['fields'])) ? $resource['fields'] : null;
            $query = $resource['query'];
            $query = ($query <> '') ? $query : array();
            $query = (is_array($query)) ? $query : json_decode($query);
            ///---if array of ids convert into $in
            if(isset($query['id'])&& is_array($query['id'])){
                $query['id']=array('$in'=>$query['id']);
            }
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
                    $rtn_arr[]=array_filter($arr);
                }
                
            } else {
                while($arr = $rs->getNext()){
                    $arr['_id'] = null;
                    $rtn_arr=array_filter($arr);
                }
            }
            return $rtn_arr;
        }
    }

}