<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class mongo_connector extends CI_Model {

    function Mongo_connector() {
        parent::__construct();
    }

    function get_data($resource) {
        //---connect to database and retrive data as specified
        if (isset($resource['datastoreref']) && isset($resource['query'])) {
            $fields = (isset($resource['fields'])) ? $resource['fields'] : null;
            $query = $resource['query'];
            $query = (is_array($query)) ? $query : json_decode($query);
            if (isset($fields)) {
                $rs = $this->mongo->db->selectCollection($resource['datastoreref'])->find($query, $fields);
            } else {
                $rs = $this->mongo->db->selectCollection($resource['datastoreref'])->find($query);
            }
            if (isset($resource['sort']))
                $rs->sort($sort);
            $rtn_arr = array();
            while ($arr = $rs->getNext()){
               //---remove _id to avoid save problems
                $arr['_id']=null;
                $rtn_arr+=array_filter($arr);
            }
            return $rtn_arr;
        }
    }

}

?>