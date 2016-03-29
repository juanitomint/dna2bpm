<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/*
 * ASSETS Like Controller
 * This file allows you to  access jscript as assets  
 * This controller allows you for run from embedded
 * 
 * @author Borda Juan Ignacio
 * 
 * @version 	2.0 (2016-03-28)
 * 
 */

class Jscript extends CI_Controller {

    function __construct() {
        parent::__construct();
       $this->index();
    }
    
    function index(){
        //$this->user->authorize();
         if(count($this->uri->segments)==2){
             show_error("Serving assets for: jscript/". implode('/', $this->uri->segments));
             exit;
         }
        //---get working directory and map it to your module
        $file =  implode('/', $this->uri->segments);
        //----get path parts form extension
        $path_parts = pathinfo( $file);
        //---set the type for the headers
        $file_type=  strtolower($path_parts['extension']);
        var_dump($file);
        if (is_file($file)) {
            //----write propper headers
            switch ($file_type) {
                case 'css':
                    header('Content-type: text/css');
                    break;

                case 'js':
                    header('Content-type: text/javascript');
                    break;
                
                case 'json':
                    header('Content-type: application/json;charset=UTF-8');
                    break;
                
                case 'xml':
                   header('Content-type: text/xml');
                    break;
                
                case 'pdf':
                  header('Content-type: application/pdf');
                    break;
                
                case 'jpg' || 'jpeg' || 'png' || 'gif':
                    header('Content-type: image/'.$file_type);
                    break;
            }
 
            readfile($file);
        } else {
            show_error("Asset not found: $file");
        }
        exit;
    }

}