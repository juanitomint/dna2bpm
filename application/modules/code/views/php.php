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
    
    function code_block($code,$theme='monokai', $lang='php'){
        $rtnString='<div class="code_block" theme="'.$theme.'" lang="'.$lang.'">'.$code.'</div>';
    }
    
    function demophp(){
        
        
    }
}