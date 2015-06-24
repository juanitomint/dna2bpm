<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Qr_connector extends CI_Model {
    function __construct() {
        parent::__construct();
        $this->base_url=base_url();
    }

    function get_data($resource, $shape, $wf) {
        $dirinfo = array();
        return $dirinfo;
    }

    function get_ui($resource, $shape, $wf,& $CI) {
        $this->load->library('parser');
        
        $str = $this->parser->parse('bpm/qr_connector', array(), true);

        $CI->add_css[$this->base_url . "qr/assets/css/qr.css"] = 'QR css';
        
        $CI->add_js[ $this->base_url . "qr/assets/jscript/html5-qrcode.min.js"] ='HTML5 qrcode';
        $CI->add_js[ $this->base_url . "qr/assets/jscript/jquery.animate-colors-min.js"] = 'Color Animation';
        $CI->add_js[ $this->base_url . "bpm/assets/jscript/qr.js"] = 'Main functions';
             
        return $str;
    }
    function save_data($idwf,$idcase,$resourceId, $post){
    $token = $this->bpm->get_token($idwf, $idcase, $resourceId);
    $token['data'] = (isset($token['data'])) ? $token['data'] : array();
    $token['data']['qr']=$post['data'];
    $this->bpm->save_token($token);
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