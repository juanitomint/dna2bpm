<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Rbac extends CI_Model {

    function __construct() {
        parent::__construct();
        $this->permRepo = 'perm.repository';
        $this->permGroups = 'perm.groups';
        $this->load->library('cimongo/cimongo');
        $this->db = $this->cimongo;
    }

    function get_repository($query = array()) {
        //returns a mongo cursor with matching id's
        $rs = $this->mongo->db->selectCollection($this->permRepo)->find($query);
        $rs->sort(array('path'));
        $repo = array();
        while ($r = $rs->getNext()) {
            $repo[$r['path']] = $r['properties'];
            //break;
        }
        return $repo;
    }

    //---add a path to repository
    function put_path($path = null, $properties = null) {
        if ($path) {
            $criteria = array_filter(array('path' => $path));

            $query = array('$set' => array('path' => $path, 'properties' => $properties));
            $options = array('upsert' => true, 'safe' => true);

            return $this->mongo->db->selectCollection($this->permRepo)->update($criteria, $query, $options);
        }
    }

    //---add a path to repository
    function remove_path($path = null) {
        if ($path) {
            $criteria = array('path' => $path);
            $this->db->where($criteria);
            $this->db->delete($this->permRepo);
            return true;
        }
    }

    function clear_paths($idgroup) {
        if ($idgroup) {
            $options = array("justOne" => false, "safe" => true);
            $criteria = array('idgroup' => (int) $idgroup);
            return $this->mongo->db->selectCollection($this->permGroups)->remove($criteria, $options);
        } else {
            return false;
        }
    }

    function put_path_to_group($path, $idgroup) {
        $idgroup = (int) $idgroup;
        $obj = array('idgroup' => $idgroup, 'path' => $path);
        $options = array('upsert' => true, 'safe' => true);
        $criteria = array_filter($obj);

        $query = array('$set' => $obj);
        $options = array('upsert' => true, 'safe' => true);

        return $this->mongo->db->selectCollection($this->permGroups)->update($criteria, $query, $options);
    }

    function get_group_paths($idgroup) {
        $query = array('idgroup' => $idgroup);
        $rs = $this->mongo->db->selectCollection($this->permGroups)->find($query);
        $rtnArr = array();
        while ($arr = $rs->getNext()) {
            if (isset($arr['path']))
                $rtnArr[] = $arr['path'];
        }
        return $rtnArr;
    }

}