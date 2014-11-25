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
class Gitmod extends MX_Controller {

    function __construct() {
        parent::__construct();

        $this->user->authorize();
        //---base variables
        $this->base_url = base_url();
        $this->module_url = base_url() . $this->router->fetch_module() . '/';
        $this->load->library('gitmod/git');
        $this->stageInculde=array('A ','D ','R','M ');
        $this->repo_path=FCPATH;
        //---Output Profiler
        //$this->output->enable_profiler(TRUE);
    }
    function Index(){
        $this->git_dashboard();
    }
    function git_dashboard(){
        Modules::run('dashboard/dashboard', 'gitmod/json/dashboard.json');
    }
    function update() {
        echo "<h1>Update from GIT server V1.15.log</h1>";

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
        if ($this->input->post('payload')|| true) {
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

    function viewlog() {
        $log = $página_inicio = file_get_contents('update-git.log');
        echo nl2br($log);
    }

    function tile() {
        $this->load->library('parser');
        $data['title']='Branch:<br/>'.$this->getBranchName().'<br>E:'.ENVIRONMENT;
        //$data['number']='Branch';
        $data['icon']='ion-usb';
        $data['more_info_link']=$this->base_url.'git/viewlog';
        $data['more_info_text']='view log';
        echo $this->parser->parse('dashboard/tiles/tile-orange', $data, true, true);
        
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
    
    public function getClass($file){
          $class='';
           switch (trim($file['status'])){
                    case 'A'://---staged
                        $class='success';
                        break;
                    case 'H'://---cached
                        $class='warning';
                        break;
                        
                    case 'S'://---skip-worktree
                        $class='warning';
                        break;
                        
                    case 'M'://---unmerged
                        $class='primary';
                        break;
                        
                    case 'D'://---removed/deleted
                        $class='danger';
                        break;
                    case 'MD'://---removed/deleted
                        $class='danger';
                        break;
                    case 'R'://---renames
                        $class='warning';
                        break;
                        
                    case 'UU'://---Conflicted
                        $class='danger';
                        break;
                        
                    case '??' ://---untracked
                        $class='success';
                        break;
                        
                    case 'C' ://---modified/changed
                        $class='primary';
                        break;
                        
                    default :
                        $class='primary';
                        break;
                }
        return $class;
    }
    public function status(){
        $this->load->library('parser');
        $repo=$this->git->open($this->repo_path);
        $renderData['title'] ='Status';
        $renderData['url'] =$this->module_url.'status';
        $renderData['base_url'] = $this->base_url;
        $renderData['widget_url'] = $this->module_url.'show_'.__FUNCTION__;
        $renderData['status']=$repo->status_extended();
        $renderData['qtty']=count($renderData['status']);
        $renderData['status']=array_map(
            function($file){
            $class=$this->getClass($file);
            $file['class']=$class;
            //---dont return these
            if(in_array($file['status'],$this->stageInculde)){
              return null;
            } 
            return $file;
            },
            $renderData['status']);
            $renderData['status']=array_filter($renderData['status']);
            // var_dump($renderData['status']);exit;
        return $renderData['content']=$this->parser->parse('gitmod/status', $renderData,true,true);
    }
    public function show_staged(){
        echo $this->staged();
    }
    public function show_status(){
        echo $this->status();
    }
    public function staged(){
        $this->load->library('parser');
        $repo=$this->git->open($this->repo_path);
        $renderData['title'] = "Staged [".$repo->active_branch()."]";
        $renderData['base_url'] = $this->base_url;
        $renderData['widget_url'] = $this->module_url.'show_'.__FUNCTION__;
        $renderData['staged']=$repo->status_extended();
        $renderData['staged']=array_map(
            function($file){
            $class=$this->getClass($file);
            $file['class']=$class;
            //--- return these
                if(in_array($file['status'],$this->stageInculde)){
                    return $file;
                } 
            },
            $renderData['staged']);
        $renderData['staged']=array_filter($renderData['staged']);
        
        // $renderData['class'] ='col-md-6';
        return $renderData['content']=$this->parser->parse('gitmod/staged', $renderData,true,true);
    }
    public function result(){
        $this->load->library('parser');
        $renderData['title'] = 'Results<div class="box-tools pull-right"><a href="#" id="git-log-clear"><i class="fa fa-ban>"></i></a></div>';
        $renderData['base_url'] = $this->base_url;
        $renderData['content']='<div style="overflow: auto; width: auto; height: 250px;"><blockquote id="result" class="result" /></div>';
        return $this->parser->parse('dashboard/widgets/box_default_solid', $renderData,true,true);
    }
    function pullpush(){
        $renderData['base_url'] = $this->base_url;
        return $this->parser->parse('gitmod/pullpush', $renderData,true,true);;
    }
    
    function modal(){
        return $this->load->view('gitmod/modal');
    }
    
    function commit_button(){
        return $this->load->view('gitmod/commit_button');
    }
    
    function stage(){
        $repo=$this->git->open($this->repo_path);
        $files=$this->input->post('files');
        $date=date('H:i:s');
        //---stage
        $txtCmd='';
            foreach($files as $filename){
                $cmd="add --all $filename";
                $txtCmd.=$cmd.'<br/>';
                $repo->run($cmd);
            }
        echo "<span class='text-success text-small'>$date <i class='fa fa-chevron-circle-right'></i> Staging ".implode(',',$files);
        echo "<hr/>git ".$txtCmd."<hr/>";
        echo "</span>";
    }
    
    function unstage(){
        $repo=$this->git->open($this->repo_path);
        $files=$this->input->post('files');
        $date=date('H:i:s');
        //---unstage
        $txtCmd='';
            foreach($files as $filename){
                $cmd="reset HEAD -- $filename";
                $txtCmd.=$cmd.'<br/>';
                $repo->run($cmd);
            }
        echo "<span class='text-warning'>$date <i class='fa fa-chevron-circle-left'></i> Un Staging ".implode(',',$files);
        echo "<hr/>git ".$txtCmd."<hr/>";
        echo "</span>";
        
    }
    function commit(){
        $repo=$this->git->open($this->repo_path);
        $txt=$this->input->post('commitTxt');
        $date=date('H:i:s');
        if($txt){
        //---commit($message = "", $commit_all = true) 
        $repo->commit($txt,false);
        
        echo "<span class='text-info'>$date <i class='fa fa-thumbs-up'></i> Commited ok!</span><hr/>";
            
        } else {
        echo "<span class='text-warning'>$date <i class='fa fa-thumbs-down'></i> Can't commit with empy text</span><hr/>";

        }
    }
    
    function pull(){
        $repo=$this->git->open($this->repo_path);
        $date=date('H:i:s');
        try{
        $msg=nl2br($repo->pull());            
        echo "<span class='text-info'>$date <i class='fa fa-thumbs-up'></i> $msg</span><hr/>";
        } catch(Exception $e){
        echo "<span class='text-danger'>$date <i class='fa fa-thumbs-down'></i>".$e->getMessage()."</span><hr/>";
            
        }
    }
    
    function push(){
        $repo=$this->git->open($this->repo_path);
        $date=date('H:i:s');
        try{
        $msg=nl2br($repo->push());            
        echo "<span class='text-info'>$date <i class='fa fa-thumbs-up'></i> Push Ok! $msg</span><hr/>";
        } catch(Exception $e){
        echo "<span class='text-danger'>$date <i class='fa fa-thumbs-down'></i>".$e->getMessage()."</span><hr/>";
            
        }
    }
    
    function revert(){
        $files=$this->input->post('files');
        $repo=$this->git->open($this->repo_path); 
        $msg=array();
        $date=date('H:i:s');
        foreach ($files as $filename){
            $command="checkout $filename";
            try {
            $result=$repo->run($command);
            $msg[]= "Reverting: $filename<br/>";
            } catch (Exception $e){
                $result=$e->getMessage();
                if(strstr($result,'did not match any file(s) known to git')){
                    $msg[]= "Deleting: $filename<br/>";
                    unlink ($filename); 
                }
            }
        $msg='<br/>'.implode('<br/>',$msg);    
        echo "<span class='text-info'>$date <i class='fa fa-thumbs-up'></i> Revert Ok! $msg</span><hr/>";
        }
    }
    function show_log(){
        echo $this->log();
    }
    function log(){
        $this->load->library('parser');
        $repo=$this->git->open($this->repo_path); 
        $result=$repo->run("log -20 --pretty=format:'%cd}*%h}*%an}*%ae}*%s' --abbrev-commit");
        $renderData['history'] = array();
        $output=explode("\n",$result);
        $fields=array('date','hash','name','email','subject');
        foreach($output as $line){
            $values=explode('}*',$line);
            $entry=array_combine($fields,$values);
            $gravatar_hash =md5(strtolower(trim($entry['email'])));
            $img="http://www.gravatar.com/avatar/$gravatar_hash";
            $entry['gravatar']=$img;
            $renderData['history'][]=$entry;
            
        }
        $renderData['base_url'] = $this->base_url;
         $renderData['widget_url'] = $this->module_url.'show_'.__FUNCTION__;
        // return $this->parser->parse('gitmod/log', $renderData,true,true);
        return $this->parser->parse('gitmod/log', $renderData,true,true);
    }
}