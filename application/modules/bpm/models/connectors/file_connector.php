<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class File_connector extends CI_Model {

    function __construct() {
        parent::__construct();
        $this->load->helper('file');
    }

    function get_data($resource, $shape, $wf) {
        $path = 'images/user_files/' . $wf->idwf . '/' . $wf->case . '/' . str_replace("\n",'_', $shape->properties->name);
        $dirinfo = get_dir_file_info($path);
        return $dirinfo;
    }

    function get_ui($resource, $shape, $wf) {
        $this->load->library('parser');
        $path = 'images/user_files/' . $wf->idwf . '/' . $wf->case . '/' . str_replace("\n",'_', $shape->properties->name);
        $dirinfo = array();
        $dirinfo['input_output']=$shape->properties->input_output;
        $info = get_dir_file_info($path);
        if ($info) {
            array_map(function($arr) use (&$dirinfo) {
                $arr['relative_path_encoded']= urldecode($arr['relative_path']);
                $arr['name_encoded']= $arr['name'];
//                var_dump($arr['name'],$arr['name_encoded']);
                $dirinfo['files'][] = $arr;
            }, $info);
        }
        $dirinfo['properties']=(array)$shape->properties;
        if($shape->properties->input_output=='Input'){
            $collection = $shape->properties->iscollection;
            $dirinfo['dropClass'] = ($collection) ? 'multipleDrop' : 'singleDrop';


        } else{


        }echo 1;
        $str = $this->parser->parse('../models/connectors/file_connector_view.php', $dirinfo, true);
        return $str;
    }

    function delete_file($resource, $shape, $wf){
        $path = 'images/user_files/' . $wf->idwf . '/' . $wf->case . '/' . str_replace("\n",'_', $shape->properties->name);

    }
}
/*
ok
http://localhost/dna2bpm/images/user_files/Test1/PZFQ/FOX-701.05_Requerimientos%20para%20Afiliaci%C3%B3n%20Instituciones/intranet%2520map%2520index.png
http://localhost/dna2bpm/images/user_files/Test1/PZFQ/FOX-701.05_Requerimientos%20para%20Afiliaci%C3%B3n%20Instituciones/intranet%20map%20index.png
 * */