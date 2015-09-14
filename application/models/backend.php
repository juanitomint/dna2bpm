<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Backend extends CI_Model {

    function get_app($idapp) {
        $query = array('idapp' => (int) $idapp);
        $fields = array();
        $thisObj = $this->mongowrapper->db->apps->findOne($query, $fields);
        if ($thisObj) {
            return $thisObj;
        } else {
            show_error("Can't find $idapp in database");
        }
    }

    function get_view_byidobj($idobj, $fields=null) {
        $query = array('idobj' => $idobj);
        $fields = (isset($fields)) ? $fields : array();
        //var_dump(json_encode($query));
        $thisObj = $this->mongowrapper->db->forms->findOne($query, $fields);
        return $thisObj;
    }
//@todo change this function to form model
    function getFormsByEntity($entity) {
        $query = array('ident' => $entity,'type'=>'D');
        $rs = $this->mongowrapper->db->forms->find($query);
        return $rs;
    }

    function push_object($idobj, $idapp) {
        $options = array('upsert' => true, 'w' => true);
        $app = $this->backend->get_app((int) $idapp);
        foreach ($app['objs'] as $this_obj)
            $arr[] = $this_obj['#'];
        $max = max($arr);
        $max++;

        $app['objs'][] = array(
            '#' => $max++,
            'idobj' => $idobj,
            'idu' => $this->session->userdata('iduser')
        );
        $result = $this->mongowrapper->db->apps->save($app, $options);
    }

    function delete_object($idobj) {
        $options_delete = array("justOne" => true, "safe" => true);
        $options_save = array('upsert' => true, 'w' => true);
        $criteria = array('idobj' => $idobj);
        //----make backup first
        $obj = $this->app->get_object($idobj);
        $this->mongowrapper->db->selectCollection('forms.back')->save($obj, $options_save);
        $this->mongowrapper->db->forms->remove($criteria, $options_delete);
    }

    function remove_from_app($idobj, $idapp) {
        $options = array('upsert' => true, 'w' => true);
        $app = $this->backend->get_app((int) $idapp);

        foreach ($app['objs'] as $key => $this_obj) {
            if ($this_obj['idobj'] == $idobj)
                unset($app['objs'][$key]);
        }

        $app['objs'] = array_filter((array) $app['objs']);

        $result = $this->mongowrapper->db->apps->save($app, $options);
    }

    function get_frame_hooks($idframe) {

        $query = array('idframe' => $idframe);
        $fields = array();
        $thisObj = $this->mongowrapper->db->hooks->findOne($query, $fields);
        if ($thisObj) {
            return $thisObj;
        } else {
            return array();
        }
    }

    function get_object_hooks($idobj) {

        $query = array('idapp' => (int) $idapp);
        $fields = array();
        $thisObj = $this->mongowrapper->db->apps->findOne($query, $fields);
        if ($thisObj) {
            return $thisObj;
        } else {
            show_error("Can't find $idapp in database");
        }
    }

}

?>
