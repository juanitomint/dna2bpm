<?php
/**
 * This file implements the conector type DATE
 * the named format has to be specified in the source property of the shape
 * for more detailed control over named formats look at:
 * assets/datetimepicker.js
 * available named formats
 * fulldate
 * month_year
 */
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Date_connector extends CI_Model {

    function __construct() {
        parent::__construct();
    }

    // function get_data($resource, $shape, $wf) {

    // }

    function get_ui($resource, $shape, $wf,&$CI) {
        //$this->load->library('parser');
        $str="input date";
        $name=str_replace("\n",'_', $shape->properties->name);
        $data=(property_exists($CI->data,$name))?$data=$CI->data->$name:'';
        $namedFormat=($shape->properties->source<>'') ? $shape->properties->source:'fulldate';
        if($shape->properties->input_output=='Input'){
            $str='
                        <label for="'.$shape->resourceId.'">'.$shape->properties->name.'</label>
                <div class="input-group '.$namedFormat.'" id="datetimepicker-'.$shape->resourceId.'">
                        <input name="'.$shape->resourceId.'" type="text" class="form-control datepicker" id="'.$shape->resourceId.'" placeholder="" value="'.$data.'">
                        <span class="input-group-addon">
                        <span class="glyphicon glyphicon-calendar"></span>
                    </span>
                </div>
                ';

        } else {
            $qrdata='QRCode';
            //----read from data store

            $str='<div class="form-group">
                        <label for="'.$shape->resourceId.'">'.$shape->properties->name.'</label>
                        <input name="'.$shape->resourceId.'" readonly="readonly" type="text" class="form-control datepicker" id="'.$shape->resourceId.'" placeholder="" value="'.$data.'">
                </div>';
        }
        //---extra js
        $CI->add_js[$CI->base_url .'bpm/assets/jscript/moment/min/moment.min.js']='Moment.js';
        $CI->add_js[$CI->base_url .'bpm/assets/jscript/moment/min/locales.min.js']='Moment.js Locales';
        $CI->add_js[$CI->base_url .'bpm/assets/jscript/eonasdan-bootstrap-datetimepicker/build/js/bootstrap-datetimepicker.min.js']='DateTimePicker';
        $CI->add_js[$CI->base_url .'bpm/assets/jscript/datetimepicker.js']='DateTimePicker-bpm';
        //-----css
        $CI->add_css[$CI->base_url .'bpm/assets/jscript/eonasdan-bootstrap-datetimepicker/build/css/bootstrap-datetimepicker.min.css']='DateTimePicker';
        //----Globals
        $CI->add_globals['locale']=$CI->lang->line('thisLangCode');
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
