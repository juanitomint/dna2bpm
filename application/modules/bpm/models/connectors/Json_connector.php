<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');
/*
 * This connector treats files in dataobjects as globals, the files will be available to all cases
 */
class Json_connector extends CI_Model {

    function Document_connector() {
        parent::__construct();
    }

    function get_data($resource, $shape, $wf) {
        $path = $shape->properties->datastoreref;
        $name=$shape->properties->name;
        $mode=($shape->properties->query=='array')?true:false;
        if(!is_file($path))
            show_error('json_connector: could not read:.'.$path);
        $json=file_get_contents($path);
        if($json){
            $data=json_decode($json,$mode);
            return $data;
        } else {
            show_error('json_connector: could not read:.'.$path);
        }
    }

}