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


        $this->load->library('parser');
        $this->load->helper('bpm');
        //----LOAD LANGUAGE
        $this->lang->load('library', $this->config->item('language'));
    }

    function Index() {
       var_dump(Modules::run('bpm/bpmui/tile',null));
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
     call_user_func_array (array($this,'user_callable'),array('aaaa','bbbb'));
    }

    function user_callable($a, $b) {
        var_dump($a, $b);
    }

}

/* End of file test */
/* Location: ./system/application/controllers/welcome.php */