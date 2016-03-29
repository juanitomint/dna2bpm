<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Task extends MX_Controller {

    function __construct() {
        parent::__construct();
        $this->debug = false;
        $this->debug_manual = true;
        $this->load->library('parser');
        $this->load->model('user');
        $this->load->model('bpm');
        $this->load->model('app');
        $this->load->model('msg');
        $this->user->authorize();
        //----LOAD LANGUAGE
        $this->lang->load('library', $this->config->item('language'));
        $this->lang->load('bpm/bpm', $this->config->item('language'));
        $this->base_url=base_url();
        $this->module_url = base_url() . $this->router->fetch_module() . '/';
        
    }
    function Index(){
        echo "<h1>BPM/TASK</h1>";
    }
    /**
     * This function is a combined action of refuse and claim 
     */ 
    
    function delegate($idwf, $case, $resourceId,$to_idu) {
        
        $iduser = (int) $this->session->userdata('iduser');
        //---get the user
        $user = $this->user->get_user($iduser);
        $user_groups = $user->group;
        $token = $this->bpm->get_token($idwf, $case, $resourceId);
        //---check if the user is in the assigned groups

        $is_allowed =($this->user->isAdmin()) ? true :false;
        //---check if user belong to the group the task is assigned to
        if (isset($token['idgroup'])) {
            foreach ($user_groups as $thisgroup) {
                if (in_array($thisgroup, $token['idgroup']))
                    $is_allowed = true;
            }
        }
        
        if ($is_allowed) {
            $token['assign'] = array((int)$to_idu);
            $token['lockedBy']=(int)$to_idu;
            $token['lockedDate']=date('Y-m-d H:i:s');
            $this->bpm->save_token($token);
        } else {
            show_error('user is not allowed to delegate task');
        }

    redirect($this->config->item('default_controller'));
        
    }
    
    function delegate_ui($idwf, $idcase, $resourceId) {
        $this->load->model('bpm/bpm');
        $this->load->library('parser');
        $this->load->library('bpm/ui');
        //---load bpm model
        $mywf = $this->bpm->load($idwf);
        $wf = $this->bpm->bindArrayToObject($mywf ['data']);
        //---Get case
        $case=$this->bpm->get_case($idcase, $idwf);
        //---Get Shape
        $shape=$this->bpm->get_shape($resourceId,$wf);
        
        //---Get parent lane
        $lane = $this->bpm->find_parent($shape, 'Lane', $wf);
        $token_shape=$this->bpm->get_token($idwf, $idcase, $resourceId);
        $token_lane=$this->bpm->get_token($idwf, $idcase, $lane->resourceId);
        
        $resources = $this->bpm->get_resources($lane, $wf,$case);
        $resources['idgroup']=(!isset($resources['idgroup']))? $token_shape['idgroup']:array_merge((array)$resources['idgroup'],$token_shape['idgroup']);
        
        //---get actual user
        $iduser = (int) $this->session->userdata('iduser');
        //---get the user
        $user = $this->user->get_user($iduser);
        $user_groups = $user->group;
        $token = $this->bpm->get_token($idwf, $case, $resourceId);
        //---check if the user is in the assigned groups
        
        $renderData = array();
        $renderData['title']=ucwords($this->lang->line('delegate').' '.$this->lang->line('task'));
        //---Get users from groups
        $renderData['users']=$this->user->getbygroup($resources['idgroup']);
        foreach($renderData['users'] as &$this_user) { 
            
            $this_user['avatar']=$this->user->get_avatar($this_user['idu']);
            
        }
        // var_dump($renderData['users']);exit;
        $renderData['idwf'] = $idwf;
        $renderData['idcase'] = $idcase;
        $renderData['resourceId'] = $resourceId;
        $renderData ['base_url'] = $this->base_url;
// ---prepare UI
        $renderData ['js'] = array(
            $this->base_url . 'bpm/assets/jscript/modal_window.js' => 'Modal Window Generic JS'
        );
// ---prepare globals 4 js
        $renderData ['global_js'] = array(
            'base_url' => $this->base_url,
            'module_url' => $this->base_url . 'bpm'
        );
//        $this->bpm->debug['load_case_data'] = true;
        //---tomo el template de la tarea
        $renderData['label'] = 'Delegate TASK: ' . $shape->properties->name;
        
        // var_dump($renderData);
//        exit;
        $this->ui->compose('bpm/modal_task_delegate', 'bpm/bootstrap.ui.php', $renderData);
    }
    
    function claim($idwf, $case, $resourceId) {
        $iduser = (int) $this->session->userdata('iduser');
        //---get the user
        $user = $this->user->get_user($iduser);
        $user_groups = $user->group;
        $token = $this->bpm->get_token($idwf, $case, $resourceId);
        //---check if the user is in the assigned groups

        $is_allowed = false;
        //---check if user belong to the group the task is assigned to
        if (isset($token['idgroup'])) {
            foreach ($user_groups as $thisgroup) {
                if (in_array($thisgroup, $token['idgroup']))
                    $is_allowed = true;
            }
        }
        if (!isset($token['assign'])) {
            if ($is_allowed) {
                $token['assign'] = array((int) $this->session->userdata('iduser'));
                $this->bpm->save_token($token);
            }
        }
    }

    function refuse($idwf, $case, $resourceId) {
        $iduser = (int) $this->session->userdata('iduser');
        //---get the user
        $user = $this->user->get_user($iduser);
        $user_groups = $user->group;
        $token = $this->bpm->get_token($idwf, $case, $resourceId);

        //---check if the user is in the assigned array
        if (in_array($iduser, $token['assign'])) {
            $token['assign'] = array_diff($token['assign'], array($iduser));
            $token = array_filter($token);
            $this->bpm->save_token($token);
        }
    }

    function upload($idwf, $idcase) {
        $debug = false;
        $out = $_FILES;
        $idwf = $this->input->post('idwf');
        $idcase = $this->input->post('idcase');
        $resourceId = $this->input->post('resourceId');
        $mywf = $this->bpm->load($idwf, true);
        if (!$mywf) {
            show_error("Model referenced:$idwf does not exists");
        }
        $wf = $this->bpm->bindArrayToObject($mywf ['data']);
        $shape = $this->bpm->get_shape($resourceId, $wf);
        $out['path'] = 'images/user_files/' . $idwf . '/' . $idcase . '/' . str_replace("\n", '_', $shape->properties->name);
        $out = array_merge((array) $shape->properties, $out);
//        $out['dname']=$this->input->post('dname');
//        $out['resourceId']=$this->input->post('resourceId');
        @mkdir($out['path'], 0777, true);
//        $config['upload_path'] = $out['path'];
////        $config['allowed_types'] = 'gif|jpg|png';
////        $config['max_size'] = '100';
////        $config['max_width'] = '1024';
////        $config['max_height'] = '768';
//
//        $this->load->library('upload', $config);
//        $this->upload->do_upload();
        $uploads_dir = $out['path'];

        $tmp_name = $_FILES["userfile"]["tmp_name"];
        $name = urldecode($_FILES["userfile"]["name"]);
        move_uploaded_file($tmp_name, "$uploads_dir/$name");

        if (!$debug) {
            $this->output->set_content_type('json','utf-8');
            echo json_encode($out);
        } else {
            var_dump($out);
        }
    }
    function connector($connector,$method,$idwf,$idcase,$resourceId){
        //---check that the resource exists
        //----load model
        $modelname = 'bpm/connectors/' .$connector . '_connector';
        $this->load->model($modelname);
        $conn = $connector . '_connector';
        $result=false;
        if (method_exists($this->$conn, $method)) {
            $result = $this->$conn->$method($idwf,$idcase,$resourceId,$this->input->post());
        }
        $rtnObject['result']=$result;
        $this->output->set_content_type('json','utf8');
        echo json_encode($rtnObject);               
    }
}

?>