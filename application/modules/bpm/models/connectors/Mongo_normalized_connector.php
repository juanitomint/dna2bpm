<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');
/**
 * This Class provides an always normalized data source
 * ie: records will always be an array
 * 
 * */
class Mongo_normalized_connector extends CI_Model {

    function Mongo_connector() {
        parent::__construct();
        $this->load->library('mongowrapper');
    }

    function get_data($resource) {
        //---connect to database and retrive data as specified
        try{
            if (isset($resource['datastoreref']) && isset($resource['query'])) {
                $fields = (isset($resource['fields'])) ? $resource['fields'] : null;
                $query = $resource['query'];
                $query = ($query <> '') ? $query : array();
                $query = (is_array($query)) ? $query : json_decode($query);
                ///---if array of ids convert into $in
                if(isset($query['id'])&& is_array($query['id'])){
                    $query['id']=array('$in'=>array_values($query['id']));
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
                
                while($arr = $rs->getNext()){
                    $arr['_id'] = null;
                    if(isset($resource['version']) && $resource['version']=='dna2.1') {
                        $arr['parent']=null;
                    }
                    $rtn_arr[]=array_filter($arr);
                }
                return $rtn_arr;
            }
        } catch (Exception $e){
                return array('error'=>$e->getMessage());
        }
    }

}
