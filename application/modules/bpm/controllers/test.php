<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/**
 * test
 * 
 * Description of the class test
 * 
 * @author Juan Ignacio Borda <juanignacioborda@gmail.com>
 * @date   Apr 12, 2013
 */
class test extends MX_Controller {

    function __construct() {
        parent::__construct();
        $this->base_url = base_url();
        $this->module_url = base_url() . $this->router->fetch_module() . '/';
        $this->debug_manual = null;
        $this->load->config();
        $this->load->model('user/user');
        $this->load->model('user/group');
        $this->user->authorize();
        $this->load->model('bpm');
        $this->load->model('app');
        $this->load->model('msg');
        $this->idu = (int) $this->session->userdata('iduser');

        $this->load->library('parser');
        $this->load->helper('bpm');
//----LOAD LANGUAGE
        $this->lang->load('library', $this->config->item('language'));
    }

    function Index() {
        var_dump(Modules::run('bpm/bpmui/tile', null));
    }

    function get_shape_byname() {
        $mywf = $this->bpm->load('13preaprobado', true);
        $wf = $this->bpm->bindArrayToObject($mywf['data']);
        $startMessage = $this->bpm->get_shape_byname("/^StartMessageEvent$/", $wf);
        $startMessage['count'] = count($startMessage);
//header('Content-type: application/json;charset=UTF-8');
        var_dump($startMessage);
    }

    function get_tasks() {
        $tasks = $this->bpm->get_tasks(1);
    }

    function usercall() {
        call_user_func_array(array($this, 'user_callable'), array('aaaa', 'bbbb'));
    }

    function user_callable($a, $b) {
        var_dump($a, $b);
    }

    function resources() {

        $mywf = $this->bpm->load('fondyfpp', true);
        $wf = $this->bpm->bindArrayToObject($mywf['data']);
        $wf->idwf = 'fondyfpp';
        /*
         * Start test
         */
        $case = $this->bpm->get_case('YKLL');
        $wf->case = $case['id'];
        $this->user->Initiator = $case['iduser'];
        $shape = $this->bpm->get_shape('oryx_C2EC6376-8EB3-4514-AABA-B4BED6FAB8A1', $wf);
        $resources = $this->bpm->get_resources($shape, $wf, $case);
        var_dump($resources);
    }

    function send($idwf, $idcase, $resourceId) {
        $this->load->model('bpm/bpm');
        $this->load->library('parser');
        $this->load->library('bpm/ui');
        $renderData = array();
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
        $user = $this->user->getuser((int) $this->session->userdata('iduser'));
        $case = $this->bpm->get_case($idcase, $idwf);
        $this->user->Initiator = $case['iduser'];
        $token = $this->bpm->get_token($idwf, $idcase, $resourceId);
//---saco título para el resultado
        $mywf = $this->bpm->load($idwf);
        $wf = $this->bpm->bindArrayToObject($mywf ['data']);
//---tomo el template de la tarea
        $shape = $this->bpm->get_shape($resourceId, $wf);

        $data = $this->bpm->load_case_data($case, $idwf);
        $data['user'] = (array) $user;
        $data['date'] = date($this->lang->line('dateFmt'));
        $msg['from'] = $this->idu;
        $msg['subject'] = $this->parser->parse_string($shape->properties->name, $data, true, true);
        $msg['body'] = $this->parser->parse_string($shape->properties->documentation, $data, true, true);

        $msg['idwf'] = $idwf;
        $msg['case'] = $idcase;
        if ($shape->properties->properties <> '') {
            foreach ($shape->properties->properties->items as $property) {
                $msg[$property->name] = $property->datastate;
            }
        }
        $resources = $this->bpm->get_resources($shape, $wf, $case);
//---if has no messageref and noone is assigned then
//---fire a message to lane or self         
//            if (!count($resources['assign']) and !$shape->properties->messageref) {
//                $lane = $this->bpm->find_parent($shape, 'Lane', $wf);
//                //---try to get resources from lane
//                if ($lane) {
//                    $resources = $this->bpm->get_resources($lane, $wf);
//                }
//                //---if can't get resources from lane then assign it self as destinatary
//                if (!count($resources['assign']))
//                    $resources['assign'][] = $this->user->Initiator;
//            }
//---process inbox--------------
//---Override FROM if Performer is set
        if (isset($resources['Performer'])) {
            if (count($resources['Performer'])) {
                $msg['from'] = array_pop($resources['Performer']);
            }
        }
        $token['assign'] = (isset($token['assign'])) ? $token['assign'] : array();
        $to = (isset($resources['assign'])) ? array_merge($token['assign'], $resources['assign']) : $token['assign'];
        $to = array_unique(array_filter($to));
        foreach ($to as $iduser) {
            $user = $this->user->get_user_safe($iduser);
            $msg['to'][] = $user;
//            var_dump($user);exit;
            $renderData['to'][] = $user->name . ' ' . $user->lastname;
        }
//---Get FROM
        $user = $this->user->get_user_safe($msg['from']);
//---Prepare Data
        $renderData['from'][] = $user->name . ' ' . $user->lastname;
        $renderData['name'] = $msg['subject'];
        $renderData['title'] = $msg['subject'];

        $renderData['text'] = 'From: ' . implode(',', $renderData['from']).'<hr/>';
        $renderData['text'] .= 'To: ' . implode(',', $renderData['to']).'<hr/>';
        $renderData['text'] .=nl2br($msg['body']);
        $this->ui->compose('bpm/modal_msg_little', 'bpm/bootstrap.ui.php', $renderData);
    }

    function send_task($idwf, $idcase) {
        $this->load->model('bpm/bpm');
        $this->load->library('parser');
        $this->load->library('bpm/ui');
        $renderData = array();
        $renderData['idwf'] = $idwf;
        $renderData['idcase'] = $idcase;
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
//---saco título para el resultado
        $mywf = $this->bpm->load($idwf);
        $wf = $this->bpm->bindArrayToObject($mywf ['data']);
//---tomo el template de la tarea
        $renderData['name'] = 'Test TASK->SEND: ' . $wf->properties->name;
        $renderData['shapes'] = $this->bpm->bindObjectToArray($this->bpm->get_shape_byprop(array('tasktype' => 'Send'), $wf));
//        var_dump($renderData);
//        exit;
        $this->ui->compose('bpm/modal_task_send', 'bpm/bootstrap.ui.php', $renderData);
    }

}

/* End of file test */
/* Location: ./system/application/controllers/welcome.php */