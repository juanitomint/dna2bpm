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
         $this->load->helper('bpm');
        $this->load->model('user/group');
        $this->user->authorize();
        //----LOAD LANGUAGE
        $this->lang->load('library', $this->config->item('language'));
        $this->idu = $this->user->idu;
        $this->base_url = base_url();
        $this->module_url = base_url() . $this->router->fetch_module() . '/';
    }

    function Index() {
        
    }

    function View($idwf,$idcase) {
        $debug = (isset($this->debug[__FUNCTION__])) ? $this->debug[__FUNCTION__] : false;
        if ($debug)
            echo '<h2>' . __FUNCTION__ . '</h2>';
        $this->load->library('ui');
        $level = $this->user->getlevel($this->idu);
        $cpData = $this->lang->language;
        $segments = $this->uri->segment_array();
        $case = $this->bpm->get_case($idcase,$idwf);
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
            //----Pan & ZooM---------------------------------------------
            $this->module_url . 'assets/jscript/panzoom/jquery.panzoom.min.js' => 'Panzoom Minified',
            $this->module_url . 'assets/jscript/panzoom/jquery.mousewheel.js' => 'wheel-suppport',
            $this->module_url . 'assets/jscript/panzoom/pnazoom_wheel.js' => 'wheel script',
            //-----------------------------------------------------------
            $this->base_url . "jscript/bootstrap/js/bootstrap.min.js" => 'Bootstrap JS',
        );

        $cpData['global_js'] = array(
            'base_url' => $this->base_url,
            'module_url' => $this->module_url,
            'idwf' => $idwf,
            'idcase' => $idcase,
        );

        $this->ui->makeui('ext.ui.php', $cpData);
    }

    function Status($idwf) {
        $debug = (isset($this->debug[__FUNCTION__])) ? $this->debug[__FUNCTION__] : false;
        if ($debug)
            echo '<h2>' . __FUNCTION__ . '</h2>';
        $this->load->library('ui');
        $level = $this->user->getlevel($this->idu);
        $cpData['lang'] = $this->lang->language;
        $cpData['idwf'] = $idwf;
        $segments = $this->uri->segment_array();
        $mywf = $this->bpm->load($idwf, $this->expandSubProcess);
        
        $wf = (array)$mywf['data'];
        unset($wf['childShapes']);
        $cpData+=$wf;
        
        //var_dump($cpData);exit;
        $cpData['theme'] = $this->config->item('theme');
        $cpData['level'] = $level;
        $cpData['base_url'] = $this->base_url;
        $cpData['module_url'] = $this->module_url;
        $cpData['title'] = 'Process Token Snapshot';

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
            //----Pan & ZooM---------------------------------------------
            $this->module_url . 'assets/jscript/panzoom/jquery.panzoom.min.js' => 'Panzoom Minified',
            $this->module_url . 'assets/jscript/panzoom/jquery.mousewheel.js' => 'wheel-suppport',
            $this->module_url . 'assets/jscript/panzoom/pnazoom_wheel.js' => 'wheel script',
            //-----------------------------------------------------------------
            $this->base_url . "jscript/bootstrap/js/bootstrap.min.js" => 'Bootstrap JS',
        );

        $cpData['global_js'] = array(
            'base_url' => $this->base_url,
            'module_url' => $this->module_url,
            'idwf' => $cpData['idwf'],
            'idcase' => 'all',
        );

        $this->ui->makeui('ext.ui.php', $cpData);
    }

}

/* End of file welcome.php */
/* Location: ./system/application/controllers/welcome.php */