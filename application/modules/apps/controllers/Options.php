<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Options extends MX_Controller {

    function __construct() {
        parent::__construct();
        //---Libraries
        $this->load->library('parser');
        //----Models
        $this->load->model('user');
        $this->load->model('app');
        $this->load->model('backend');
        $this->load->model('fe');
        //---Helpers
        $this->load->helper('directory');
        $this->load->helper('file');
        $this->user->authorize('USE,ADM,SUP');
        $this->load->helper('dbframe');

        //----LOAD LANGUAGE
        $this->lang->load('library', $this->config->item('language'));
        $this->idu = $this->user->idu;
        $this->types_path = 'application/modules/apps/assets/types/';
        $this->module_path = 'application/modules/apps/';
        //----Variables
        $this->base_url = base_url();
        $this->module_url = base_url() . $this->router->fetch_module().'/';
    }
     function Admin() {
        $this->load->library('ui');
        $level = $this->user->getlevel($this->idu);
        $cpData = $this->lang->language;
        $segments = $this->uri->segment_array();
        //var_dump($level);
        $cpData['theme'] = $this->config->item('theme');
        $cpData['level'] = $level;
        $cpData['base_url'] = $this->base_url;
        $cpData['module_url'] = $this->module_url;
        $cpData['title'] = 'Application options';

        $cpData['js'] = array(
            $this->module_url . 'assets/jscript/fontawesome_icons.js' => 'FontAwesome icons',
            $this->module_url . 'assets/jscript/options/ext.data.js' => 'data Components',
            $this->module_url . 'assets/jscript/options/ext.load_props.js' => 'Apps Porperty loader',
            $this->module_url . 'assets/jscript/options/ext.baseProperties.js' => 'Property Grid',
            // $this->module_url . 'assets/jscript/options/ext.group_selector.js' => 'Group Selector',
            $this->module_url . 'assets/jscript/options/ext.grid.js' => 'Grid',
            $this->module_url . 'assets/jscript/options/ext.viewport.js' => 'viewport',
        );

        $cpData['global_js'] = array(
            'base_url' => $this->base_url,
            'module_url' => $this->module_url,
        );

        $this->ui->makeui('ext.ui.php', $cpData);
    }

        function Get_option($idop = -1, $idrel = null) {
        $segments = $this->uri->segment_array();
        $debug = (in_array('debug', $segments)) ? true : false;
        //---get idop from POST data
        if ($this->input->post('idop') <> '')
            $idop=$this->input->post('idop');

        $rtn = array();
        $options = $this->app->get_ops($idop,$idrel);
        $rtn['totalcount'] = count($options);
        foreach ($options as $value => $text) {
            $rtn['rows'][] = array(
                'value' => $value,
                'text' => $text
            );
        }
        if (!$debug) {
            $this->output->set_content_type('json','utf-8');
            $this->output->set_output(json_encode($rtn));
        } else {
            var_dump($rtn);
        }
    }

    function Get_options() {
        $segments = $this->uri->segment_array();
        $debug = (in_array('debug', $segments)) ? true : false;
        $rtn = array();
        $options = $this->app->get_all_options();
        $rtn['totalcount'] = $options->count();

        foreach ($options as $thisop) {
            $rtn['rows'][] = array(
                'idop' => $thisop['idop'],
                'title' => $thisop['title'],
            );
        }
        if (!$debug) {
            $this->output->set_content_type('json','utf-8');
            $this->output->set_output(json_encode($rtn));
        } else {
            var_dump($rtn);
        }
    }
}