<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Redmine_connector extends CI_Model {

    function __construct() {
        parent::__construct();
        $this->load->helper('Redmine');
    }

    function get_data($resource, $shape, $wf) {
        $data=array();
        return $data;
    }

    function get_ui($resource, $shape, $wf,&$CI) {
        $this->load->library('parser');
        
        if($shape->properties->input_output=='Input'){
            $collection = $shape->properties->iscollection;
            $dirinfo['dropClass'] = ($collection) ? 'multipleDrop' : 'singleDrop';


        } else{
             
             
        }
        //---extra js
        $CI->add_js[$CI->base_url .'bpm/assets/jscript/jquery-Redminedrop-master/jquery.Redminedrop.js']='Redmine_connector Redmine Drop';
        $CI->add_js[$CI->base_url .'bpm/assets/jscript/dropzone.js']='Redmine_connector Dropzone';
        //----extra css
        $CI->add_css[$CI->base_url .'bpm/assets/css/Redmine_connector.css']='Redmine_connector CSS';
        
         $str = $this->parser->parse('../models/connectors/Redmine_connector_view.php', $dirinfo, true);
        return $str;
    }

    function delete_Redmine($resource, $shape, $wf){
        $path = 'images/user_Redmines/' . $wf->idwf . '/' . $wf->case . '/' . str_replace("\n",'_', $shape->properties->name);

    }
}