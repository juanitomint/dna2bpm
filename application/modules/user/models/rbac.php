<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Rbac extends CI_Model {

    function __construct() {
        parent::__construct();
        $this->permRepo = 'perm.repository';
        $this->permGroups = 'perm.groups';
    }

    function get_repository($query = array()) {
        //returns a  cursor with matching id's
       $rs = $this->db
        ->where($query)
        ->get($this->permRepo)
        ->result_array();
        
        $repo = array();
        foreach ($rs as $r) {
            $repo[$r['path']] = $r['properties'];
            //break;
        };
        // $repo=array_slice($repo,310,5);
        // var_dump($repo);exit;
        
        return $repo;
    }

    //---add a path to repository
    function put_path($path = null, $properties = null) {
        if ($path) {
            $criteria = array_filter(array('path' => $path));
            $data=  array('path' => $path, 'properties' => $properties);
            $options = array('upsert' => true, 'w'=>true);
            return $this->db->where($criteria)->update($this->permRepo, $data, $options);
        }
    }

    //---add a path to repository
    function remove_path($path = null) {
        if ($path) {
            $this->db->like('path',$path,'i',false);
            return $this->db->delete($this->permRepo);

        }
    }

    function clear_paths($idgroup) {
        if ($idgroup) {
            $options = array("justOne" => false, "w" => true);
            $criteria = array('idgroup' => (int) $idgroup);
            return $this->db->where($criteria)->delete($this->permGroups, $options);
        } else {
            return false;
        }
    }

    function put_path_to_group($path, $idgroup) {
        $idgroup = (int) $idgroup;
        $obj = array('idgroup' => $idgroup, 'path' => $path);
        $options = array('upsert' => true, 'w'=>true);
        $criteria = array_filter($obj);
        $this->db->where($criteria);
        return $this->db->update($this->permGroups, $obj, $options);
    }

    function get_group_paths($idgroup) {
        $query = array('idgroup' => $idgroup);
        $rs = $this->db->where($query)->get($this->permGroups)->result_array();
        $rtnArr=array();
        foreach ($rs as $arr) {
            if (isset($arr['path']))
                $rtnArr[] = $arr['path'];
        }
        return $rtnArr;
    }

}
