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
            $this->module_url . 'assets/jscript/options/ext.combo.js' => 'Options Combo',
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
        $option = $this->app->get_option($idop);
        $rtn['totalcount'] = count($option['data']);
        $rtn['rows']= $option['data'];
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
    
    function Get_options_properties($idop=null) {
        $segments = $this->uri->segment_array();
        $debug = (in_array('debug', $segments)) ? true : false;
        $cpData = array();
        $cpData = $this->lang->language;
        $option = array();
        $custom = '';
        $types_path = $this->types_path;
        $dbop = $this->app->get_option($idop);
        
        //---load base properties from helpers/types/base
        include($types_path . 'base/options.base.php');

       
        //---now define the properties template
        $properties_template = $common;
        $option = new dbframe($option, $properties_template);
        $option->load($dbop);
        if (!$debug) {
            $this->output->set_content_type('json','utf-8');
            echo json_encode($option->toShow());
        } else {
            var_dump('Obj', $option, 'Save:', $option->toSave(), 'Show', $option->toShow());
        }
    }
    
    function Save_options_properties($idop=null) {
        $segments = $this->uri->segment_array();
        $debug = (in_array('debug', $segments)) ? true : false;
        $postform = $this->input->post();
        //----create empty frame according to the template
        $option = new dbframe();
        $idop = $postform['idop'];
        include($this->types_path . 'base/options.base.php');
        $properties_template = $common;
        $properties_template['data'] ='array';
        // $data=json_decode($postform['data']);
        
        // $postform['data']=($data)?$data->rows:array();
        // var_dump($data,$postform);exit;
        //----load the data from post
        $option->template=$properties_template;
        $option->loadPostdata($postform);
        if ($idop) {
            $dbapp = $this->app->get_option($idop);
        } else {
            $option->idop= (int) $this->app->gen_inc('options', 'idop');
            $dbapp=array();
        }

        $this->app->put_option($option->idop, $option->toSave() + $dbapp);
        
        //----dump results
        if (!$debug) {
            $this->output->set_content_type('json','utf-8');
            echo json_encode($option->toSave());
        } else {
            var_dump($option->toShow());
        }
    }
}