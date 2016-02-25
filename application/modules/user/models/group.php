<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Group extends CI_Model {

    function __construct() {
        parent::__construct();
    }

    function genid() {
        $insert = array();

        $container = 'groups';
        $fieldname = 'idgroup';

        $query = array();
        $fields = array($fieldname);
        $sort = array($fieldname => -1);
        //var_dump($query);
        $rs = $this->db
        ->select($fields)
        ->order_by($sort)
        ->get_where($container,$query)
        ->result_array();
        $result=$rs[0];
        //var_dump($result);
        $inc_id = 1 * $result[$fieldname] + 1;
        return $inc_id;
    }

    function get_count($idgroup) {
        //returns a  cursor with matching id's
        $query = array('group' => $idgroup);
        //var_dump(json_encode($query));
        return $this->db->where($query)->count_all_results('users');
    }

    function getbyid($idgroup) {
        //returns a  cursor with matching id's
        $grouparr = (array) json_decode($idgroup);
        $query = array('idgroup' => array('$in' => $grouparr));
        //returns a  cursor with matching id's
        //var_dump(json_encode($query));
        $rs=$this->db->get_where('groups',$query)->result_array();
        if(count($rs)){
        return $rs[0];
        } else {
            return false;
        }
    }

    function get($idgroup) {
        //returns an array with matching id data
        $query = array('idgroup' => (int) $idgroup);
        //var_dump(json_encode($query));
        $rs=$this->db->get_where('groups',$query)->result_array();
        if(count($rs)){
        return $rs[0];
        } else {
            return false;
        }
    }

    function get_byname($groupname) {
        //returns a  cursor with matching id's
        $query = array('name' => $groupname);
        //var_dump(json_encode($query));
        $rs=$this->db->get_where('groups',$query)->result_array();
        if(count($rs)){
        return $rs[0];
        } else {
            return false;
        }
    }

    function save($object) {
        unset($object['_id']);
        $options = array('upsert' => true, 'w'=>true);
        $this->db->where(array('idgroup'=>$object['idgroup']));
        $this->db->update('groups',$object, $options);
        $rs=$this->db->get_where('groups',array('idgroup'=>$object['idgroup']))->result_array();
        return $rs[0];
    }

    function get_groups($order = null, $query_txt = null) {
        $groups=array();
        $rs=$this->db->like('name',$query_txt);
        if ($order)
            $rs->order_by(array($order => 'asc'));
        $groups=$this->db->get('groups')->result_array();
        return $groups;
    }

    function delete($idgroup) {
        $options_delete = array("justOne" => true, "w" => true);
        $options_save = array('upsert' => true, 'w'=>true);
        $criteria = array('idgroup' => (int) $idgroup);
        //----make backup first
        $obj = $this->group->get($idgroup);
        unset($obj['_id']);
        $this->db->update('groups.back',$obj, $options_save);
        //---delete
        $this->db->where(array('idgroup'=>$idgroup));
        return $this->db->delete('groups',$options_delete);
    }

}

?>