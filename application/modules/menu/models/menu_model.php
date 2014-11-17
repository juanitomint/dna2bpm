<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Menu_model extends CI_Model {

    function __construct() {
        parent::__construct();
        $this->container = 'container.menu';
        $this->load->library('cimongo/cimongo');
        $this->db = $this->cimongo;
        $this->idu = $this->session->userdata('iduser');
    }

    //---add a path to repository
    function put_path($repoId, $path = null, $properties = null) {
        if ($path) {
            $criteria = array_filter(array(
                'path' => $path,
                'repoId' => $repoId
                    )
            );

            $query = array('$set' => array('repoId' => (string) $repoId, 'path' => $path, 'properties' => $properties));
            $options = array('upsert' => true, 'w' => true);
            //----save path in rbac
            $rbac_path = "Menu/" . $repoId . $path;
            $this->rbac->put_path($rbac_path);

            return $this->mongo->db->selectCollection($this->container)->update($criteria, $query, $options);
        }
    }

    //---add a path to repository
    function remove_path($repoId, $path = null) {
        if ($path) {
            $regex = new MongoRegex("/^$path*/i");
            $criteria = array(
                'path' => $regex,
                'repoId' => $repoId
            );
            $this->db->where($criteria);
            $this->db->delete($this->container);
            $rbac_path = "Menu/" . $repoId . $path;
            $this->rbac->remove_path($rbac_path);
            return true;
        }
    }

    function clear_paths($repoId) {
        if ($idgroup) {
            $options = array("justOne" => false, "safe" => true);
            $criteria = array('idgroup' => (int) $idgroup);
            return $this->mongo->db->selectCollection($this->container)->remove($criteria, $options);
        } else {
            return false;
        }
    }

    function get_path($repoId, $id) {
        if ($id) {
            $query = array('repoId' => $repoId, 'properties.id' => $id);
            $rs = $this->mongo->db->selectCollection($this->container)->findOne($query);
            $rs['id'] = $id;
            return $rs;
        } else {
            return null;
        }
    }

    function get_paths($repoId) {
        $query = array('repoId' => $repoId);
        $rs = $this->mongo->db->selectCollection($this->container)->find($query);
        $rs->sort(array('path' => 1));
        $rtnArr = array();
        while ($arr = $rs->getNext()) {
            if (isset($arr['path']))
                $rtnArr[] = $arr['path'];
        }
        return $rtnArr;
    }

    function get_repository($query = array('repoId' => '0'), $check = true) {
        //returns a mongo cursor with matching id's
        $rs = $this->mongo->db->selectCollection($this->container)->find($query);
        $rs->sort(array('properties.priority' => 1));
        $repo = array();
        $user = $this->user->get_user($this->idu);
        while ($r = $rs->getNext()) {
            $repoId = $r['repoId'];
            $path = $r['path'];
            //---check if user has perm
//            echo "checking "."root/Menu/" . $repoId . $path;
//            var_dump($this->user->has("root/Menu/" . $repoId . $path,$user));
//            echo '<hr>';
            if ($check) {
                if ($this->user->has("root/Menu/" . $repoId . $path, $user)) {
                    $repo[$r['path']] = $r['properties'];
                }
            } else {
                $repo[$r['path']] = $r['properties'];
            }
            //break;
        }
        return $repo;
    }

}
