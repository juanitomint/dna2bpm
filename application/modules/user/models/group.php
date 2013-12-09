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

        $options = array('upsert' => true, 'safe' => true);
        $query = array();
        $fields = array($fieldname);
        $sort = array($fieldname => -1);
        //var_dump($query);
        $result = $this->mongo->db->selectCollection($container)->find($query, $fields)->sort($sort)->getNext();
        //var_dump($result);
        $inc_id = 1 * $result[$fieldname] + 1;
        return $inc_id;
    }

    function get_count($idgroup) {
        //returns a mongo cursor with matching id's
        $query = array('idgroup' => $idgroup);
        //var_dump(json_encode($query));
        return $this->mongo->db->groups->find($query)->count();
    }

    function getbyid($idgroup) {
        //returns a mongo cursor with matching id's
        $grouparr = (array) json_decode($idgroup);
        $query = array('idgroup' => array('$in' => $grouparr));
        //returns a mongo cursor with matching id's
        //var_dump(json_encode($query));
        return $this->mongo->db->groups->find($query);
    }

    function get($idgroup) {
        //returns an array with matching id data
        $query = array('idgroup' => (int) $idgroup);
        //var_dump(json_encode($query));
        return $this->mongo->db->groups->findOne($query);
    }

    function get_byname($groupname) {
        //returns a mongo cursor with matching id's
        $query = array('name' => $groupname);
        //var_dump(json_encode($query));
        return $this->mongo->db->groups->findOne($query);
    }

    function save($object) {
        //var_dump($object);
        $options = array('upsert' => true, 'safe' => true);
        return $this->mongo->db->groups->save($object, $options);
    }

    function get_groups($order = null, $query_txt = null) {
        $query = array();
        if ($query_txt) {
            $query = array('name' => new MongoRegex('/' . $query_txt . '/i'));
        }
        //var_dump('$order',$order,'$query',$query);
        $rs = $this->mongo->db->groups->find($query);
        if ($order)
            $rs->sort(array($order => 1));
        while ($thisgroup = $rs->getNext()) {
                    $groups[] = $thisgroup;
                }
        return $groups;
    }

    function delete($idgroup) {
        $options_delete = array("justOne" => true, "safe" => true);
        $options_save = array('upsert' => true, 'safe' => true);
        $criteria = array('idgroup' => (int) $idgroup);
        //----make backup first
        $obj = $this->group->get($idgroup);
        $this->mongo->db->selectCollection('groups.back')->save($obj, $options_save);
        $this->mongo->db->groups->remove($criteria, $options_delete);
    }

}

?>