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
    }

    function Index() {
        $this->output->enable_profiler(true);
        $idu=$this->session->userdata('iduser');
        echo "CI Version: ".CI_VERSION.'<br/>';
        var_dump('idu',$idu,$this->user->get_user((int)$idu));
    }

}