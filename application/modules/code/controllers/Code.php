<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/**
 * Actualiza los archivos segun la rama configurada
 * 
 * Este controlador permite actualizar autumaticamente los archivos contenidos en la aplicacion
 * ejecutando el comando GIT: git pull y registrando la salida a un archivo de registro.
 * Este archivo funciona en conjunto con el web-hook de gitorious y no puede ser invocado manualmente
 * 
 * @autor Borda Juan Ignacio
 * 
 * @version 	.1 (2014-12-03)
 * 
 * 
 */
class Code extends MX_Controller {

    function __construct() {
        parent::__construct();

        //---base variables
        $this->base_url = base_url();
        $this->module_url = base_url() . $this->router->fetch_module() . '/';
        $this->idu = $this->user->idu;
        //---Output Profiler
        //$this->output->enable_profiler(TRUE);
    }
    function Index(){
        $this->user->authorize();
        $this->code_dashboard();
    }
    function code_dashboard(){
        $this->user->authorize();
        Modules::run('dashboard/dashboard', 'code/json/dashboard.json');
    }
    function highlight(){
        $this->user->authorize();
        Modules::run('dashboard/dashboard', 'code/json/highlight.json');
    }
    
    function code_block($code, $lang='php',$theme='monokai',$rows=16){
        return '<textarea rows="'.$rows.'" class="code_block" theme="'.$theme.'" lang="'.$lang.'">'.$code.'</textarea>';
        
    }
    function highlight_block($code, $lang='php',$theme='monokai',$rows=16){
        return '<pre><code class="'.$lang.'" lang="'.$lang.'">'.$code.'</code></pre>';
        
    }
    
    function demo($filetype){
        $this->load->helper('file');
        $this->load->module('dashboard');
        $filename= APPPATH . "modules/code/views/$filetype.php";
        $code=read_file($filename);
        $data['content']=$this->code_block($code,$filetype);
        $data['title']="Demo: ".$filetype;
        $template="dashboard/widgets/box_info_solid";
        echo $this->dashboard->widget($template, $data);
    }
    function demo_highlight($filetype){
        $this->load->helper('file');
        $this->load->module('dashboard');
        $filename=APPPATH . "modules/code/views/$filetype.php";
        $code=read_file($filename);
        $data['content']=$this->highlight_block($code,$filetype);
        $data['title']="Demo: ".$filetype;
        $template="dashboard/widgets/box_info_solid";
        echo $this->dashboard->widget($template, $data);
    }
    function file($file,$lang,$theme='monokai'){
        $this->load->helper('file');
        $this->load->module('dashboard');
        $filename=APPPATH . $file;
        $code=read_file($filename);
        $data['content']=$this->code_block($code,$lang,$theme);
        $data['title']="File: ".$filename;
        $template="dashboard/widgets/box_info_solid";
        echo $this->dashboard->widget($template, $data);
    }
    function highlight_file($file,$lang,$theme='monokai'){
        $this->load->helper('file');
        $this->load->module('dashboard');
        $filename=APPPATH . $file;
        $code=read_file($filename);
        $data['content']=$this->highlight_block($code,$lang,$theme);
        $data['title']="File: ".$filename;
        $template="dashboard/widgets/box_info_solid";
        echo $this->dashboard->widget($template, $data);
    }
    
}