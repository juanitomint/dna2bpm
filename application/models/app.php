<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class App extends CI_Model {

    function __construct() {
        parent::__construct();
        $this->idu = (int) $this->session->userdata('iduser');
    }

    /*
     * Return all objs or query
     */

    function get_objects($query = array()) {
        $thisObj = array();
        $fields = array();
        if (func_num_args() > 1) {
            $fields = func_get_arg(1);
            $fields[] = 'idobj';
            $fields[] = 'idform';
        }
        //var_dump(json_encode($query),$fields);
        $thisObj = $this->mongo->db->forms->find($query, $fields);
        //var_dump($thisObj);
        return $thisObj;
    }

    function get_form($idform = '') {
        $thisObj = array();
        $fields = array();
        if (func_num_args() > 1) {
            $fields = func_get_arg(1);
            $fields[] = 'idobj';
            $fields[] = 'idform';
        }

        $query = array('idform' => (int) $idform);
        //var_dump(json_encode($query),$fields);
        $thisObj = $this->mongo->db->forms->findOne($query, $fields);
        //var_dump($thisObj);
        return $thisObj;
    }

    function get_object($idobj = '') {
        $thisObj = array();
        $query = array('idobj' => $idobj);
        $fields = array();
        $thisObj = $this->mongo->db->forms->findOne($query, $fields);
        if ($thisObj) {
            return $thisObj;
        } else {
            //show_error("Can't find $idobj in database");
            return false;
        }
    }

    function put_object($object) {        
        $options = array('upsert' => true, 'safe' => true);        
        $result = $this->mongo->db->forms->save($object, $options);    
        return $result;
         
        
    }

    function get_apps() {
        $fields = array();
        $query = array();
        //var_dump(json_encode($query),$fields);
        $thisObj = $this->mongo->db->apps->find($query, $fields)->sort(array('title' => 1));
        //var_dump($thisObj);
        return $thisObj;
    }

    function get_app($idapp) {
        $fields = array();
        $query = array('idapp' => (int) $idapp);
        //var_dump(json_encode($query),$fields);
        $thisObj = $this->mongo->db->apps->findOne($query, $fields);
        //var_dump($thisObj);
        return $thisObj;
    }

    function get_frame($idframe = '') {
        $fields = array();
        if (func_num_args() > 1) {
            $fields = func_get_arg(1);
            $fields[] = 'cname';
            $fields[] = 'idframe';
        }

        $query = array('idframe' => (int) $idframe);
        //var_dump(json_encode($query),$fields);
        $thisObj = $this->mongo->db->frames->findOne($query, $fields);
        //var_dump($thisObj);
        return $thisObj;
    }

    //---Return form frames by key
    function get_form_frame($form, $idframe) {
        //---preload $frames array
        $frame = array();
        if (isset($form['frames'])) {
            $query = array('idframe' => (int) $idframe);
            $frameFromDB = $this->mongo->db->frames->findOne($query);
            $frame = array_merge((array) $frameFromDB, (isset($form['frames'][$idframe])) ? (array) $form['frames'][$idframe] : array());
            //echo json_encode(array_merge((array) $frameFromDB, (array) $form['frames'][$idframe])).'<hr/>';
        }
        return $frame;
    }

    function get_form_frames($form) {
        //---preload $frames array
        $frames = array();
        if (isset($form['frames'])) {
            foreach ($form['frames'] as $idframe => $frameExtra) {

                $query = array('idframe' => (int) $idframe);
                $frameFromDB = $this->mongo->db->frames->findOne($query);
                //$frameExtra = (isset($form['frames'][$idframe])) ? (array) $form['frames'][$idframe] : array();
                //---Extra values take precedence over default-ones
                if ($frameFromDB)
                    try {
                        $frames[] = array_merge((array) $frameFromDB, (array) $frameExtra);
                    } catch (Exception $e) {
                        echo 'Caught exception: ', $e->getMessage(), "<br/>$idframe";
                    }
                //echo json_encode(array_merge((array) $frameFromDB, (array) $form['frames'][$idframe])).'<hr/>';
            }
        }
        return $frames;
    }

    function get_form_col($form, $col) {
        //---preload $frames array

        $frames = array();
        if (isset($form['frames'])) {
            $column = (isset($form['frames'][(int) $col])) ? (array) $form['frames'][(int) $col] : null;
            foreach ((array) $column as $idframe) {
                $query = array('idframe' => (int) $idframe);
                $frameFromDB = $this->mongo->db->frames->findOne($query);
                $frameExtra = (isset($form['frames'][$idframe])) ? (array) $form['frames'][$idframe] : array();
                if ($frameFromDB)
                    $frames[] = array_merge((array) $frameFromDB, $frameExtra);
                //echo json_encode(array_merge((array) $frameFromDB, (array) $form['frames'][$idframe])).'<hr/>';
            }
        }
        return $frames;
    }

    function add_object($idapp, $idobj) {
        $app = $this->get_app($idapp);
        $app['objs'][] = array(
            'idobj' => $idobj,
            'idu' => $this->idu
        );
        $result = $this->put_app($idapp, $app);
        return $result;
    }

    function getvalue($id, $idframe) {
        $rtnVal = null;
        $result = null;
        $id = (double) $id;
        $idframe = (int) $idframe;
        //----Get container
        $frame = $this->mongo->db->frames->findOne(array('idframe' => $idframe), array('container'));
        $query = array('id' => $id);
        $fields = array((string) $idframe);
        if ($frame['container']) {
            $result = $this->mongo->db->selectCollection($frame['container'])->findOne($query, $fields);
        } else {
            trigger_error("container property missing for: $idframe");
        }
        //var_dump($frame['container'],json_encode($query),json_encode($fields),$result);
        $rtnVal = (isset($result[$idframe])) ? $result[$idframe] : null;
        return $rtnVal;
    }

    function get_result($container,$query,$fields=array()) {
        $result = $this->mongo->db->selectCollection($container)->find($query, $fields);
        return $result;
    }
    function getall($id, $container) {
        $debug = false;
        if ($debug)
            echo '<h2>' . __FUNCTION__ . '</h2>' .
            "Called @ " . xdebug_call_file() . "<br/>Line:" . xdebug_call_line() . "<br/>from: <b>" . xdebug_call_function() . '</b><hr/>';
        $rtnarr = array();
        $rtnVal = null;
        $result = null;
        $id = (double) $id;
        $fields = array();
        if (func_num_args() > 2) {
            foreach ((array) func_get_arg(2) as $key)
                $fields[] = (string) $key;
            $fields[] = 'id';
        }
        //----Fetch Database
        $query = array('id' => $id);
        $result = $this->mongo->db->selectCollection($container)->findOne($query, $fields);
        $rtnVal = ($result) ? $result : null;
        return $rtnVal;
    }

    function get_code($object, $context, $language) {
        $query = array(
            'object' => $object,
            'context' => $context,
            'language' => $language
        );
        //echo json_encode($query);
        return $this->mongo->db->procs->findOne($query);
    }

    function remove_code($object, $context, $language) {
        $criteria = array(
            'object' => $object,
            'context' => $context,
            'language' => $language
        );
        $options = array("justOne" => true, "safe" => true);
        //echo json_encode($query);
        return $this->mongo->db->procs->remove($criteria, $options);
    }

    function put_code($object, $context, $language, $code) {
        $data = $this->app->get_code($object, $context, $language);
        $options = array('upsert' => true, 'safe' => true);
        //---TODO backup to another base

        $data['object'] = $object;
        $data['context'] = $context;
        $data['language'] = $language;
        $data['code'] = $code;
        $data['checkdate'] = date('Y-m-d H:i:s');
        $data['idu'] = $this->session->userdata('iduser');

        return $this->mongo->db->procs->save($data, $options);
    }

    function put_value($id, $idframe, $value) {
        $rtnfields = array('type', 'container');
        $thisFrame = $this->get_frame($idframe, $rtnfields);
        //----ensure correct type 4 storage
        $value = $this->cast_type($value, $thisFrame['type']);
        //----check 4 id
        if (!is_integer($id)) {
            $id = $this->genid($thisFrame['container']);
        }
        $criteria = array('id' => $id);
        $update = array('$set' => array($idframe => $value));
        //var_dump($thisFrame['container'], json_encode($criteria), json_encode($update));
        $result = $this->mongo->db->selectCollection($thisFrame['container'])->update($criteria, $update);
        return $result;
    }

    function put_frame_extra($idform, $idframe, $extra) {

        $options = array('upsert' => true, 'safe' => true);
        $form = $this->app->get_object($idform);
        $form['frames'][$idframe] = $extra;
        return $this->mongo->db->forms->save($form, $options);
    }

    function put_frame($idframe, $new_frame) {
        $options = array('upsert' => true, 'safe' => true);

        $idframe = (int) $idframe;
        //--1st get old frame if exists;
        $frame = $this->get_frame($idframe);

        //---retrive old data
        if ($frame) {
            $new_frame['_id'] = $frame['_id'];
        }
        return $this->mongo->db->frames->save($new_frame, $options);
    }

    function put_form_data($idform, $form_data) {
        $options = array('upsert' => true, 'safe' => true);
        $idform = (int) $idform;
        //--1st get old frame if exists;
        //--overwrites values with newones
        $form = $this->get_form($idform);
        $form = array_merge($form, $form_data);
        return $this->mongo->db->forms->save($form, $options);
    }

    function put_app($idapp, $new_app) {
        $options = array('upsert' => true, 'safe' => true);
        $new_app['idapp'] = (int) $new_app['idapp'];
        return $this->mongo->db->apps->save($new_app, $options);
    }

    function put_form($idform, $new_form) {
        $options = array('upsert' => true, 'safe' => true);
        $idform = (int) $idform;
        //--1st get old frame if exists;
        $form = $this->get_form($idform);

        //---retrive old data
        if ($form) {
            $new_form['_id'] = $form['_id'];
            //---import properties from original form
            //---these properties are set on edit.
            if (isset($form['frames']))
                $new_form['frames'] = $form['frames'];
        }
        return $this->mongo->db->forms->save($new_form, $options);
    }

    function put_array($id, $container, $val_arr = array()) {        
        
        $thisArr = array();

        foreach ($val_arr as $idframe => $value) {
            $thisFrame = $this->get_frame($idframe, array('type', 'container'));
            $thisArr[$idframe] = $this->cast_type($value, $thisFrame['type']);            
        }
        //var_dump($thisArr);
        //----check 4 id
        if (!is_numeric($id)) {
            $id = $this->genid($container);
        }

        $criteria = array('id' => $id);
        $update = array('$set' => $thisArr);
        $options = array('upsert' => true, 'safe' => true);
        
        //var_dump($container, json_encode($criteria), json_encode($update));
        $result = $this->mongo->db->selectCollection($container)->update($criteria, $update, $options);
        $thisArr['id'] = $id;
        return $thisArr;
    }

    function cast_type($input, $type) {
        $retval = '';

        switch ($type) {
            case 'checklist':
                $retval = (array) $input;
                break;
            case 'combo':
                $retval = (array) $input;
                break;
            case 'combodb':
                $retval = (array) $input;
                break;
            case 'radio':
                $retval = (array) $input;
                break;
            case 'subform':
                $retval = (array) $input;
                break;
            case 'date':
                $retval = $input['Y'] . '-' . $input['m'] . '-' . $input['d'];
                break;
            case 'datetime':
                $retval = $input['Y'] . '-' . $input['m'] . '-' . $input['d'] . ' ' . $input['h'] . ':' . $input['i'];
                break;
            default:
                $retval = $input;
                break;
        }
//var_dump($input,$type,$retval);
        return $retval;
    }

    function normalize_frame($frame) {
        //---ensure int 4 idframe
        $frame['idframe'] = (int) $frame['idframe'];
        $type = $frame['type'];
        $options = array(
            'checklist',
            'combodb',
            'combo',
            'radio',
        );
        $type_class = '';
        //----check if type must be trated as options
        if (in_array($type, $options))
            $type_class = 'options';

        $frame['required'] = ($frame['required'] == 'true') ? true : false;
        $frame['hidden'] = ($frame['hidden'] == 'true') ? true : false;
        $frame['locked'] = ($frame['locked'] == 'true') ? true : false;
        switch ($type_class) {
            case 'options':
                if (isset($frame['idop']))
                    $frame['idop'] = (int) $frame['idop'];
                if (isset($frame['cols']))
                    $frame['cols'] = (int) $frame['cols'];
                break;
            default:
                if (isset($frame['size']))
                    $frame['size'] = (int) $frame['size'];
                if (isset($frame['cols']))
                    $frame['cols'] = (int) $frame['cols'];
                if (isset($frame['rows']))
                    $frame['rows'] = (int) $frame['rows'];

                break;
        }
//var_dump($input,$type,$retval);
        return $frame;
    }

    function genid($container) {
        $insert = array();
        $id = mt_rand();
        $trys = 10;
        $i = 0;
        //---if passed specific id
        if (func_num_args() > 1) {
            $id = (double) func_get_arg(1);
            $passed = true;
            //echo "passed: $id<br>";
        }
        $hasone = false;

        while (!$hasone and $i <= $trys) {//---search until found or $trys iterations
            //while (!$hasone) {//---search until found or 1000 iterations
            $query = array('id' => $id);
            $result = $this->mongo->db->selectCollection($container)->findOne($query);
            $i++;
            if ($result) {
                if ($passed) {
                    show_error("id:$id already Exists in $container");
                    $hasone = true;
                    break;
                } else {//---continue search for free id
                    $id = mt_rand();
                }
            } else {//---result is null
                $hasone = true;
            }
        }
        if (!$hasone) {//-----cant allocate free id
            show_error("Can't allocate an id in $container after $trys attempts");
        }
        //-----make basic object
        $insert['id'] = $id;
        //----Allocate id in the collection (may result in empty docs)
        $this->mongo->db->selectCollection($container)->save($insert);
        return $id;
    }

    function genid_general($container, $fieldname) {
        $insert = array();
        $id = mt_rand();
        $trys = 10;
        $i = 0;
        //---if passed specific id
        if (func_num_args() > 2) {
            $id = (double) func_get_arg(2);
            $passed = true;
            //echo "passed: $id<br>";
        }
        $hasone = false;

        while (!$hasone and $i <= $trys) {//---search until found or $trys iterations
            $query = array($fieldname => $id);
            //var_dump(json_encode($query));
            $result = $this->mongo->db->selectCollection($container)->findOne($query);
            $i++;
            if ($result) {
                if ($passed) {
                    show_error("id:$id already Exists in $container");
                    $hasone = true;
                    break;
                } else {//---continue search for free id
                    $id = mt_rand();
                }
            } else {//---result is null
                $hasone = true;
            }
        }
        if (!$hasone) {//-----cant allocate free id
            show_error("Can't allocate an id in $container after $trys attempts");
        }
        //-----make basic object
        $insert[$fieldname] = $id;
        //----Allocate id in the collection (may result in empty docs)
        $this->mongo->db->selectCollection($container)->save($insert);
        return $id;
    }

    function dumpid($id, $container) {
        $criteria = array('id' => (int) $id);
        $options = array("justOne" => true, "safe" => true);
        $result = $this->mongo->db->selectCollection($container)->remove($criteria, $options);
        return $result;
    }

    function check_id($id, $container) {
        $query = array('id' => (double) $id);
        $fields = array('id');
        $result = $this->mongo->db->selectCollection($container)->find($query, $fields)->count();
        if ($result) {
            return true;
        } else {
            return false;
        }
    }

    function gen_inc($container, $fieldname) {
        $options = array('upsert' => true, 'safe' => true);
        $query = array();
        $fields = array($fieldname);
        $sort = array($fieldname => -1);
        //var_dump($query);
        $result = $this->mongo->db->selectCollection($container)->find($query, $fields)->sort($sort)->getNext();
        //var_dump($result);
        $inc_id = 1 * $result[$fieldname] + 1;
        $this->mongo->db->selectCollection($container)->save(array($fieldname => $inc_id), $options);
        return $inc_id;
    }

//    function get_workflow($idwf) {
//        $query = array('idwf' => (double) $idwf);
//        $fields = array($id);
//        $result = $this->mongo->db->workflow->find($query, $fields)->count();
//        return $result;
//    }
//
//    function put_workflow($idwf='new', $insert) {
//        $insert = (array) $insert;
//        //---backup original workflow
//        $insert['idwf'] = ($idwf == 'new') ? $this->genid_general('workflow', 'idwf') : $idwf;
//        $insert['idobj'] = 'WF' . $insert['idwf'];
//        $insert['lastupd'] = date('Y-m-d H:i:s');
//        $this->mongo->db->workflow->save($insert);
//    }
    function get_ops_from_container($option) {
//uses: query fields fieldRel optionFromcontainer
        $rtnarr = array();
        //var_dump($CI->options);
        if (!isset($this->options[$option['idop']])) {
            //var_dump('NOT chached');
            // gets data from internal db
            $query = (isset($option['query'])) ? (array) $option['query'] : array();
            $fields = $option['fieldText'];
            $fields[] = $option['fieldValue'];
            $fields[] = $option['fieldRel'];
            $fields = array_filter($fields);
            //echo '<hr>'. $option['idop'];
            //var_dump($option['container'],$query,$fields);
            $rsop = $this->mongo->db->selectCollection($option['container'])->find($query, $fields);
            while ($arr = $rsop->getNext()) {
                $text = array();
                foreach ($option['fieldText'] as $field)
                    $text[] = (isset($arr[$field])) ? $arr[$field] : null;
                $text = implode(' ', $text);
                $rtnarr[] = array(
                    'value' => (isset($arr[$option['fieldValue']])) ? $arr[$option['fieldValue']] : null,
                    'text' => $text,
                    'idel' => (isset($arr[$option['fieldRel']])) ? $arr[$option['fieldRel']] : null
                );
            }
            $this->options[$option['idop']] = $rtnarr;
        } else {
            //var_dump('chached');
//----cache options
            $rtnarr = $this->options[$option['idop']];
        }
        return($rtnarr);
    }

    function get_entities() {
        $sort = array('name' => 1);
        $entities = $this->mongo->db->entities->find()->sort($sort);
        return $entities;
    }

    function get_ops($idop, $idrel = null) {
        $ops = array();
        $option = $this->mongo->db->options->findOne(array('idop' => (int) $idop));
        //prepare options array
        //var_dump($option);
        $option['data'] = (isset($option['data'])) ? $option['data'] : array();
        $option['data'] = (isset($option['fromContainer'])) ? $this->get_ops_from_container($option) : $option['data'];
       

        foreach ($option['data'] as $thisop) {
            /* TODO optimizar */                        
            if ($idrel) {                 
                if (in_array($idrel, $thisop)) {
                    $ops[$thisop['value']] = $thisop['text'];
                }
            } else {
                $ops[$thisop['value']] = $thisop['text'];
            }
        }
        return $ops;
    }

    function get_option($idop) {
        $option = $this->mongo->db->options->findOne(array('idop' => (int) $idop));
        $option['data'] = (isset($option['fromContainer'])) ? $this->get_ops_from_container($option) : $option['data'];
        return $option;
    }

    function get_all_options() {
        $sort = array('title' => 1);
        $options = $this->mongo->db->options->find()->sort($sort);
        return $options;
    }

}
?>