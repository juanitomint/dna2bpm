<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Whoami extends MX_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->library('parser');
        $this->load->model('user');
        //----LOAD LANGUAGE
        $this->lang->load('library', $this->config->item('language'));
        $this->idu = (int) $this->session->userdata('iduser');
        $this->load->config('config');
        
    }

    function Index() {
        $this->output->enable_profiler(true);
        $idu = $this->session->userdata('iduser');
        echo "CI Version: " . CI_VERSION . '<br/>';
        $plugins=(class_exists('Userlayer')) ? implode(',', $this->config->item('user_plugin')):array();
        var_dump('plugins',$plugins);
        var_dump('idu', $idu, $this->user->get_user((int) $idu));
    }

}
