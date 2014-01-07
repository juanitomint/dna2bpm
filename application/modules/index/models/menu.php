<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Menu extends CI_Model {

    function __construct() {
        parent::__construct();
        $this->container = 'container.menu';
        $this->load->library('cimongo/cimongo');
        $this->db = $this->cimongo;
    }

    //---add a path to repository
    function put_path($repoId, $path = null, $properties = null) {
        if ($path) {
            $criteria = array_filter(array(
                'path' => $path,
                'repoId' => $repoId
                    )
            );

            $query = array('$set' => array('repoId' => $repoId, 'path' => $path, 'properties' => $properties));
            $options = array('upsert' => true, 'safe' => true);

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

    function get_path($repoId, $path) {
        if ($path) {
            $query = array('repoId' => $repoId, 'properties.id' => $path);
            $rs = $this->mongo->db->selectCollection($this->container)->findOne($query);
            return $rs;
        } else {
            return null;
        }
    }

    function get_paths($repoId) {
        $query = array('repoId' => $repoId);
        $rs = $this->mongo->db->selectCollection($this->container)->find($query);
        $rtnArr = array();
        while ($arr = $rs->getNext()) {
            if (isset($arr['path']))
                $rtnArr[] = $arr['path'];
        }
        return $rtnArr;
    }

    function get_repository($query = array('repoId' => 0)) {
        //returns a mongo cursor with matching id's
        $rs = $this->mongo->db->selectCollection($this->container)->find($query);
        $rs->sort(array('path'));
        $repo = array();
        while ($r = $rs->getNext()) {
            $repo[$r['path']] = $r['properties'];
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
                    $pointerChild =$this->search($returnArr, 'id', $thispath);
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
