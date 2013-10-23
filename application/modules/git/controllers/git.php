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
 * @version 	1.13 (2012-06-14)
 * 
 * @file-salida   update-git.log
 * 
 */
class Git extends MX_Controller {

    function __construct() {
        parent::__construct();

        //---base variables
        $this->base_url = base_url();
        $this->module_url = base_url() . $this->router->fetch_module().'/';

        //---Output Profiler
        //$this->output->enable_profiler(TRUE);
    }

    function update_git() {
        echo "<h1>Update from GIT server V1.14.log</h1>";

//----log to file
        $logtofile = true;
//----whether to include payload or not into logfile for debuging prouposes
        $include_payload = false;
//---get raw input
        //$request = file_get_contents('php://input');
        //$request_body = json_decode($request);
        $request_body = json_decode($this->input->post('payload'));
        $who = 'nobody';
        $result = 'Unauthorized access';
        if ($this->input->post('payload')) {
            $who = $request_body->pushed_by;
            if ($who) {
                $result = shell_exec('git pull 2>&1');
            } else {
                $result = "Error can't process update request";
            }
        }
        if ($logtofile) {
            if ($fp = @fopen('update-git.log', 'a')) {
                if ($include_payload)
                    fwrite($fp, date('Y-m-d H:i:s') . ' ' . urldecode($request) . "\n");
                fwrite($fp, date('Y-m-d H:i:s') . ' Pushed by:' . $who . "\n");
                fwrite($fp, date('Y-m-d H:i:s') . ' Result: ' . $result . "\n\n");
                fclose($fp);
            }
        }
    }

}
