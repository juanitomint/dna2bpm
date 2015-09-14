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
        $this->module_url = base_url() . $this->router->fetch_module() . '/';
        //----rnable or disable log
        $this->log=true;
        //---Output Profiler
        
        //$this->output->enable_profiler(TRUE);
    }

    function update_gitorious() {
        echo "<h1>Update from GIT server V1.15.log</h1>";

//----whether to include payload or not into logfile for debuging prouposes
        $include_payload = false;
//---get raw input
        //$request = file_get_contents('php://input');
        //$request_body = json_decode($request);
        $request_body = json_decode($this->input->post('payload'));
        $who = 'nobody';
        $result = 'Unauthorized access';
        if ($this->input->post('payload')|| true) {
            $who = $request_body->pushed_by;
            if ($who) {
                $result = shell_exec('git pull 2>&1');
            } else {
                $result = "Error can't process update request";
            }
        }
        if ($this->log) {
            if ($fp = @fopen('update-git.log', 'a')) {
                if ($include_payload)
                    fwrite($fp, date('Y-m-d H:i:s') . ' ' . urldecode($request) . "\n");
                fwrite($fp, date('Y-m-d H:i:s') . ' Pushed by:' . $who . "\n");
                fwrite($fp, date('Y-m-d H:i:s') . ' Result: ' . $result . "\n\n");
                fclose($fp);
            }
        }
    }
    
    function update_gitlab() {
        echo "<h1>Update from GitLab server V1.16.log</h1>";

//----whether to include payload or not into logfile for debuging prouposes
        $include_payload = false;
//---get raw input
        //$request = file_get_contents('php://input');
        //$request_body = json_decode($request);
        $request_body = json_decode(file_get_contents('php://input'));
        // var_dump($request_body);exit;
        $who = 'nobody';
        $request_headres=$this->input->request_headers();
        $result = 'Unauthorized access';
        if ($request_headres['X-gitlab-event']=='Push Hook') {
            $who = $request_body->user_name.' <'.$request_body->user_email.'>';
            if ($who) {
                $result = shell_exec('git pull 2>&1');
            } else {
                $result = "Error can't process update request";
            }
        }
        if ($this->log) {
           if(!is_writable('update-git.log')){
            echo('Can\'t write to log: update-git.log is not writable');
               exit;
           }
            if ($fp = fopen('update-git.log', 'a')) {
                if ($include_payload)
                    fwrite($fp, date('Y-m-d H:i:s') . ' ' . urldecode($request) . "\n");
                fwrite($fp, date('Y-m-d H:i:s') . ' Pushed by:' . $who . "\n");
                fwrite($fp, date('Y-m-d H:i:s') . ' Result: ' . $result . "\n\n");
                fclose($fp);
            }
        }
    }

    function viewlog() {
        $log = $página_inicio = file_get_contents('update-git.log');
        echo nl2br($log);
    }

    function tile() {
        $this->load->library('git/git');
        $data['title']='Branch:<br/>'.$this->getBranchName().'<br>E:'.ENVIRONMENT;
        //$data['number']='Branch';
        $data['icon']='ion-usb';
        $data['more_info_link']=$this->base_url.'git/viewlog';
        $data['more_info_text']='view log';
        return $this->parser->parse('dashboard/tiles/tile-orange', $data, true, true);
        
    }

    public function getBranchName() {
        if (is_file('.git/HEAD')) {
            $stringfromfile = file('.git/HEAD', FILE_USE_INCLUDE_PATH);
            $stringfromfile = $stringfromfile[0]; //get the string from the array

            $explodedstring = explode("/", $stringfromfile); //seperate out by the "/" in the string

            return trim(end($explodedstring)); //get the one that is always the branch name
        }
        return false;
    }

}
