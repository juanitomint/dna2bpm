<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Qr_connector extends CI_Model {
    function __construct() {
        parent::__construct();
        $this->base_url=base_url();
    }
   
    function get_data($resource, $shape, $wf) {
        return '';
    }
   
    function get_ui($resource, $shape, $wf,& $CI) {
        
        $this->load->library('parser');
        $data=(array)$shape->properties;
        $data['resourceId']=$shape->resourceId;
        $data['input_output']=$shape->properties->input_output;
        $CI->add_css[$this->base_url . "qr/assets/css/qr.css"] = 'QR css';
        
        if($shape->properties->input_output=='Input'){
            $CI->add_js[ $this->base_url . "qr/assets/jscript/html5-qrcode.min.js"] ='HTML5 qrcode';
            $CI->add_js[ $this->base_url . "qr/assets/jscript/jquery.animate-colors-min.js"] = 'Color Animation';
            $CI->add_js[ $this->base_url . "bpm/assets/jscript/qr_input.js"] = 'Main functions';
        } else {
            $name=str_replace("\n",'_', $shape->properties->name);
            $qrdata='QRCode';
            //----read from data store
            if(property_exists($CI->data,$name)){
                if($CI->data->$name<>'')
                    $qrdata=$CI->data->$name;
            }
            //---read from explicit source defined
            if($shape->properties->source<>'')
                $qrdata=$shape->properties->source;
        $data['qr_text']=$qrdata;    
        $data['qr_data']=urlencode(base64_encode($qrdata));    
        
        //---if not defined is output    
            
        }    
        $str = $this->parser->parse('bpm/qr_connector', $data, true);
        
        return $str;
    }
    
    function save_data($idwf,$idcase,$resourceId, $post){
        $token = $this->bpm->get_token($idwf, $idcase, $resourceId);
        $case = $this->bpm->get_case($idcase, $idwf);
        $do_resourceId=$post['resourceId'];
        // -----load bpm
        $mywf = $this->bpm->load($idwf, false);
        $mywf ['data'] ['idwf'] = $idwf;
        $mywf ['data'] ['case'] = $idcase;
        $wf = $this->bpm->bindArrayToObject($mywf ['data']);
        // --get shape
        $shape = $this->bpm->get_shape($post['resourceId'], $wf);
        $name=str_replace("\n",'_', $shape->properties->name);
        //----Save Data in token
        $token['data'] = (isset($token['data'])) ? $token['data'] : array();
        $token['data'][$name]=$post['data'];
        $this->bpm->save_token($token);
        //----Save data into case
        $case['data'] = (isset($case['data'])) ? $case['data'] : array();
        $case['data'][$name]=$post['data'];
        $this->bpm->save_case($case);
    return true;
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