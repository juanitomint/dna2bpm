<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Date_connector extends CI_Model {

    function __construct() {
        parent::__construct();
        $this->load->helper('file');
    }

    function get_data($resource, $shape, $wf) {
        
    }

    function get_ui($resource, $shape, $wf,&$CI) {
        //$this->load->library('parser');
        $str="input date";
        if($shape->properties->input_output=='Input'){
            $str='<div class="form-group">
                        <label for="'.$shape->resourceId.'">'.$shape->properties->name.'</label>
                        <input name="'.$shape->resourceId.'" type="date" class="form-control" id="'.$shape->resourceId.'" placeholder="">
                </div>';
        } else {
            $name=str_replace("\n",'_', $shape->properties->name);
            $qrdata='QRCode';
            //----read from data store
            
            if(property_exists($CI->data,$name)){
                if($CI->data->$name<>'')
                    $data=$CI->data->$name;
            }
            $str='<div class="form-group">
                        <label for="'.$shape->resourceId.'">'.$shape->properties->name.'</label>
                        <input name="'.$shape->resourceId.'" readonly="readonly" type="date" class="form-control" id="'.$shape->resourceId.'" placeholder="" value="'.$data.'">
                </div>';
        }
        return $str;
    }
   
        function save_data($idwf,$idcase,$shape,$post){
        $token = $this->bpm->get_token($idwf, $idcase, $shape->resourceId);
        $case = $this->bpm->get_case($idcase, $idwf);
        if(isset($post[$shape->resourceId])){
            $value=$post[$shape->resourceId];
                if($value){
                    $name=str_replace("\n",'_', $shape->properties->name);
                    //----Save Data in token
                    $token['data'] = (isset($token['data'])) ? $token['data'] : array();
                    $token['data'][$name]=$value;
                    $this->bpm->save_token($token);
                    //----Save data into case
                    $case['data'] = (isset($case['data'])) ? $case['data'] : array();
                    $case['data'][$name]=$value;
                    $this->bpm->save_case($case);
                        
                }
            }
        }
    
}
