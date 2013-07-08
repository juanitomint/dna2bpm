<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/**
 * tokens
 * 
 * Description of the class
 * 
 * @author Juan Ignacio Borda <juanignacioborda@gmail.com>
 * @date    Apr 17, 2013
 */
class tokens extends MX_Controller {

    function __construct() {
        parent::__construct();
        $this->load->library('parser');
        $this->load->model('user');
        $this->load->model('bpm');
        $this->load->model('user/group');
        $this->user->authorize('ADM,WFADM');
        //----LOAD LANGUAGE
        $this->lang->load('library', $this->config->item('language'));
        $this->idu = (int) $this->session->userdata('iduser');
        $this->base_url = base_url();
        $this->module_url = base_url() . 'bpm/';
    }

    function Index() {
        
    }

    function View($idcase) {
        $debug = (isset($this->debug[__FUNCTION__])) ? $this->debug[__FUNCTION__] : false;
        if ($debug)
            echo '<h2>' . __FUNCTION__ . '</h2>';
        $this->load->library('ui');
        $level = $this->user->getlevel($this->idu);
        $cpData = $this->lang->language;
        $segments = $this->uri->segment_array();
        $case=$this->bpm->get_case($idcase);
        $cpData+=$case;
        $cpData['theme'] = $this->config->item('theme');
        $cpData['level'] = $level;
        $cpData['base_url'] = $this->base_url;
        $cpData['module_url'] = $this->module_url;
        $cpData['title'] = 'Token Viewer';
        
        //var_dump($cpData);exit;
        $cpData['css'] = array(
            $this->module_url . 'assets/css/case_manager.css' => 'Manager styles',
            $this->module_url . 'assets/css/extra-icons.css' => 'Extra Icons',
            $this->module_url . 'assets/css/fix_bootstrap_checkbox.css' => 'Fix Checkbox',
        );
        $cpData['js'] = array(
            $this->module_url . 'assets/jscript/case_manager/ext.settings.js' => 'Settings & overrides',
            $this->module_url . 'assets/jscript/fontawesome_icons.js' => 'FontAwesome icons',
            $this->module_url . 'assets/jscript/case_manager/ext.data.js' => 'data Components',
            $this->module_url . 'assets/jscript/case_manager/ext.tokenGrid.js' => 'Types Grid',
            $this->module_url . 'assets/jscript/ext.model-utils.js' => 'Model utils',
            $this->module_url . 'assets/jscript/case_manager/ext.add_events.js' => 'Events for overlays',
            $this->module_url . 'assets/jscript/case_manager/ext.tokens.viewport.js' => 'viewport',
            $this->base_url . "jscript/jquery/jquery.min.js" => 'JQuery',
            $this->base_url . "jscript/bootstrap/js/bootstrap.min.js" => 'Bootstrap JS',
        );

        $cpData['global_js'] = array(
            'base_url' => $this->base_url,
            'module_url' => $this->module_url,
            'idwf' => $cpData['idwf'],
            'idcase' => $idcase,
        );

        $this->ui->makeui('ext.ui.php', $cpData);
    }

}

/* End of file welcome.php */
/* Location: ./system/application/controllers/welcome.php */