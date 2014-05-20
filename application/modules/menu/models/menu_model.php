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
            $options = array('upsert' => true, 'safe' => true);
            //----save path in rbac
            $rbac_path = "Menu/" . $repoId . $path;
            $this->rbac->put_path($rbac_path);

            return $this->mongo->db->selectCollection($this->container)->update($criteria, $query, $options);
        }
    }

    //---add a path to repository
    function remove_path($repoId, $path = null) {
        if ($path) {
            $criteria = array(
                'path' => $path,
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
            if ($check) {
                if ($this->user->has("root/Menu/" . $repoId . $path,$user)) {
                    $repo[$r['path']] = $r['properties'];
                }
            } else {
                
            }
            //break;
        }
        return $repo;
    }

    function explodeExtTree($array, $delimiter = '/') {
        if (!is_array($array))
            return false;
        //---Setings
        $expanded = false;
        $leafCls = 'dot-green';
        $splitRE = '/' . preg_quote($delimiter, '/') . '/';
        $returnArr = array((object) array(
                "id" => 'root',
                "text" => "Object Repository",
                "cls" => "folder",
                "expanded" => true,
                "checked" => false,
        ));
        foreach ($array as $key => $val) {
            // Get parent parts and the current leaf
            $parts = preg_split($splitRE, $key, -1, PREG_SPLIT_NO_EMPTY);
            // Build parent structure
            $localpath = array('root');
            $cachepath = array();
            foreach ($parts as $part) {
                $parentArr = &$returnArr;
                $thisparentpath = implode($delimiter, $localpath);
                $localpath[] = $part;
                $thispath = implode($delimiter, $localpath);
                $isleaf = ($thispath == 'root/' . $key) ? true : false;
                //prepare object to add
                $obj = (object) array(
                            'id' => $thispath,
                            'text' => $part,
                            'leaf' => $isleaf,
                            'checked' => false,
                );
                if ($isleaf) {
                    $obj->iconCls = $leafCls;
                    $obj->data = $val;
                }
                //---set the internal pointer to the parent
                $pointer = $this->search($returnArr, 'id', $thisparentpath);
                //----if parent exists (we start with 1 root so has to exists but just in case...)
                if ($pointer) {
                    $pointerChild = $this->search($returnArr, 'id', $thispath);
                    //---check if child exists
                    if (!$pointerChild) {
                        $pointer['leaf'] = false;
                        $pointer['expanded'] = $expanded;
                        $pointer['children'][] = $obj;
                    }
                }
            }
        }
        return $returnArr;
    }

    /*
     *  This function returns a pointer to the part of the array matching key=>value
     */

    function search(&$arr, $key, $value) {
        $arrIt = new RecursiveIteratorIterator(new RecursiveArrayIterator($arr));
        foreach ($arrIt as $sub) {
            $subArray = $arrIt->getSubIterator();
            $subArray->jj = true;
            if (isset($subArray[$key]) && $subArray[$key] == $value) {
                //return iterator_to_array($subArray);
                return $subArray;
            }
        }
        return null;
    }

}
