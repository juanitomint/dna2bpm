<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/*
 *  This Class exposes services for daemons and anonymous callers
 * 
 * @author Borda Juan Ignacio
 * 
 */
class Service extends MX_Controller {

    function Service() {
        parent::__construct();
        $this->debug = false;
        //$this->debug = true;
        $this->load->library('parser');
        $this->load->model('user');
        $this->load->model('bpm');
        $this->load->model('app');
        $this->load->helper('workflow');
        //----LOAD LANGUAGE
        $this->lang->load('library', $this->config->item('language'));
        //---Debug options
        $this->debug['triggers'] = true;
        $this->debug['Run'] = true;
        $this->debug['Startcase'] = false;
        $this->debug['get_inbound_shapes'] = false;
        $this->debug['run_Task'] = true;
        $this->debug['load_data'] = false;
        //---debug Helpers
        $this->debug['run_IntermediateEventThrowing']=true;
        $this->debug['run_IntermediateLinkEventThrowing']=true;

        //$this->debug['get_shape_byname']=false;
    }
}
?>