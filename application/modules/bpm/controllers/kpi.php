<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/**
 * kpi
 * 
 * Description of the class kpi
 * 
 * @author Juan Ignacio Borda <juanignacioborda@gmail.com>
 * @date   Mar 30, 2013
 */
class Kpi extends MX_Controller {

    function __construct() {
        parent::__construct();
        $this->load->model('kpi_model');
        $this->load->library('parser');
        $this->load->model('user');
        $this->load->model('user/group');
        $this->user->authorize('ADM,WFADM');
        //----LOAD LANGUAGE
        $this->types_path = 'application/modules/bpm/assets/types/';
        $this->module_path = 'application/modules/bpm/';
        $this->lang->load('library', $this->config->item('language'));
        $this->idu = (int) $this->session->userdata('iduser');
        $this->base_url = base_url();
        $this->module_url = base_url() . $this->router->fetch_module().'/';
    }

    function Index() {
        
    }

    function Data($action, $model) {
        $this->load->model('app');
        $this->load->helper('dbframe');
        $segments = $this->uri->segment_array();
        $debug = (in_array('debug', $segments)) ? true : false;

        $custom = '';
        $types_path = $this->types_path;
        //var_dump($_POST);
        $out = array();
//        $form = $this->app->get_object($idapp);


        if (isset($model)) {
            switch ($action) {
                //----start READ--------------
                case 'read':

                    $kpi = $this->kpi_model->get_model($model);
                    if (count($kpi)) {
                        $forms['totalcount'] = count($kpi);
                        include($types_path . 'base/kpi.base.php');

                        foreach ($kpi as $obj) {
                            $forms['rows'][] = $obj;
                        }
                    } else {
                        $forms['totalcount'] = 0;
                        $forms['rows'] = array();
                    }
                    $out = $forms;
                    break;
                //---Start CREATE
                case 'update':
                    $input = json_decode(file_get_contents('php://input'));
                    //---defines $common
                    include($types_path . 'base/kpi.base.php');
                    foreach ($input as $thisKpi) {
                        $thisKpi = (array) $thisKpi;
                        $dbKpi = $this->kpi_model->get($thisKpi['idkpi'], 'object');
                        $newKpi = array_merge($dbKpi, $thisKpi);

                        $this->kpi_model->save($newKpi);
                    }
                    $out = array('status' => 'ok');
                    break;
                /*
                  //---Start update
                  case 'update':
                  $out = $_POST;
                  //$debug = true;
                  break;
                 * 
                 */
                case 'create':
                    include($types_path . 'base/kpi.base.php');
                    $input = json_decode(file_get_contents('php://input'));
                    foreach ($input as $thisKpi) {
                        //---Create new id for generated form
                        $thisKpi->idkpi = $this->app->gen_inc('kpi', 'idkpi');
                        //---safe set the model id
                        $thisKpi->idwf = $model;
                        $kpi = new dbframe($thisKpi, $common);
                        //---save the new object
                        $this->kpi_model->save($kpi->toSave());
                    }
                    $out = array('success' => true);
                    break;
                case 'destroy':
                    $input = json_decode(file_get_contents('php://input'));
                    foreach ($input as $thisKpi) {
                        $result = $this->kpi_model->delete($thisKpi->idkpi);
                    }
                    $out = array('success' => true);
                    break;
            }
            //----end switch
            if (!$debug) {
                header('Content-type: application/json;charset=UTF-8');
                echo json_encode($out);
            } else {
                var_dump($out);
            }
        } else {
            show_error("Need to have idobj to get.");
        }
    }

    function Editor($model, $idwf) {
        $debug = (isset($this->debug[__FUNCTION__])) ? $this->debug[__FUNCTION__] : false;
        if ($debug)
            echo '<h2>' . __FUNCTION__ . '</h2>';
        $this->load->library('ui');
        $level = $this->user->getlevel($this->idu);
        $cpData = $this->lang->language;
        $segments = $this->uri->segment_array();
        //var_dump($level);
        $cpData['theme'] = $this->config->item('theme');
        $cpData['level'] = $level;
        $cpData['base_url'] = $this->base_url;
        $cpData['module_url'] = $this->module_url;
        $cpData['idwf'] = $idwf;
        $cpData['title'] = 'Key Performance Indicators Browser/Editor';

        $cpData['css'] = array(
            $this->module_url . 'assets/css/kpi.css' => 'KPI special Rules',
            $this->module_url . 'assets/css/extra-icons.css' => 'KPI special Rules',
        );
        $cpData['js'] = array(
            $this->module_url . 'assets/jscript/kpi/ext.settings.js' => 'Settings',
            $this->module_url . 'assets/jscript/fontawesome_icons.js' => 'FontAwesome icons',
            $this->module_url . 'assets/jscript/kpi/ext.data.js' => 'data Components',
            $this->module_url . 'assets/jscript/kpi/ext.typesview.js' => 'Types Grid',
            $this->module_url . 'assets/jscript/kpi/ext.grid.js' => 'Grid',
            $this->module_url . 'assets/jscript/kpi/ext.load_props.js' => 'Form Porperty loader',
            $this->module_url . 'assets/jscript/kpi/ext.baseProperties.js' => 'Property Grid',
            $this->module_url . 'assets/jscript/ext.model-utils.js' => 'Model utils',
            $this->module_url . 'assets/jscript/kpi/ext.add_events.js' => 'Events for overlays',
            $this->module_url . 'assets/jscript/kpi/ext.viewport.js' => 'viewport',
            $this->base_url . "jscript/jquery/jquery.min.js" => 'JQuery',
            $this->base_url . "jscript/bootstrap/js/bootstrap.min.js" => 'Bootstrap JS',
        );

        $cpData['global_js'] = array(
            'base_url' => $this->base_url,
            'module_url' => $this->module_url,
            'idwf' => $idwf,
        );

        $this->ui->makeui('ext.ui.php', $cpData);
    }

    function Get_properties($idkpi = null, $mode = 'json') {
        $this->load->helper('dbframe');
        $segments = $this->uri->segment_array();
        $debug = (in_array('debug', $segments)) ? true : false;
        //$debug=true;
        $cpData = array();
        $cpData = $this->lang->language;
        $thisKpi = array();
        $custom = '';
        $thisKpi = array('');
        if (isset($idkpi)) {
            $thisKpi = $this->kpi_model->get($idkpi);
        }
        //---get idwf from post
        $thisKpi['idwf'] = $this->input->post('idwf');
        $thisKpi['type'] = $this->input->post('type');
        //---set user
        $thisKpi['idu'] = (isset($thisKpi['idu'])) ? $thisKpi['idu'] : $this->idu;
        $type = (isset($thisKpi['type'])) ? $thisKpi['type'] : 'count';

        //---load base properties from helpers/types/base
        //---defines $common
        include($this->types_path . 'base/kpi.base.php');
        //---load custom properties from specific type
        $type_props = array();
        $file_custom = $this->types_path . $type . '/properties.php';
        if (is_file($file_custom)) {
            if ($debug)
                echo "Loaded Custom:$file_custom<br/>";
            include($file_custom);
        }

        //---now define the properties template
        $properties_template = $common + $type_props;
        $kpi = new dbframe($thisKpi, $properties_template);

        if (!$debug) {
            switch ($mode) {
                case "object":
                    return $kpi;
                    break;
                default:
                    header('Content-type: application/json;charset=UTF-8');
                    echo json_encode($kpi->toShow());
            }
        } else {
            var_dump('Obj', $kpi, 'Save:', $kpi->toSave(), 'Show', $kpi->toShow());
        }
    }

    function Get_template($type = 'count') {
        $this->load->helper('file');
        $tdata = array();
        //---4 safety
        if ($type == 'base')
            $type = '';
        //----------------------------------------------------------------------
        //---Load Custom Properties---------------------------------------------
        //----------------------------------------------------------------------
        $file = $this->module_path . "assets/types/$type/ext.propertyGrid.js";
        if (is_file($file)) {
            $customProps = read_file($file);
            //$customProps = $this->parser->parse(str_replace('.php', '', $file), $tdata,true);
        } else {
            $customProps = '';
        }
        //----------------------------------------------------------------------
        //---Load Base Properties
        //----------------------------------------------------------------------

        $file = $this->module_path . "assets/jscript/kpi/ext.baseProperties.js";
        if (is_file($file)) {
            $baseProps = "// FILE:$file\n";
            $baseProps .= read_file($file);
            //$customProps = $this->parser->parse(str_replace('.php', '', $file), $tdata,true);
        } else {
            show_error("Cant find base properties file: $file<br/>Sorry can't serve");
        }
        //---insert custom props in the base file
        $props = str_replace('//{customProps}', $customProps, $baseProps);
        //----render the code
        echo $props;
    }

    function Test_render($idwf) {
        $this->load->model('bpm');
        $debug = (isset($this->debug[__FUNCTION__])) ? $this->debug[__FUNCTION__] : false;
        if ($debug)
            echo '<h2>' . __FUNCTION__ . '</h2>';
        $this->load->library('ui');
        $level = $this->user->getlevel($this->idu);
        $cpData = $this->lang->language;
        $segments = $this->uri->segment_array();
        //var_dump($level);
        $cpData['theme'] = $this->config->item('theme');
        $cpData['title'] = "Kpi Preview";
        $cpData['level'] = $level;
        $cpData['base_url'] = $this->base_url;
        $cpData['module_url'] = $this->module_url;
        $cpData['idwf'] = $idwf;
        $kpis = $this->kpi_model->get_model($idwf);

//----PROCESS KPIS
        $kpi_show = array();
        foreach ($kpis as $kpi) {
            //echo $kpi['type'].'<hr/>';
            $kpi_show[] = $this->render($kpi);
        }
        $cpData['content'] = implode($kpi_show);
        //----define Globals
        $cpData['global_js'] = array(
            'base_url' => $this->base_url,
            'module_url' => $this->module_url
        );
        $cpData['js'] = array(
            $this->module_url . 'assets/canv-gauge-master/gauge.js' => 'Jscript Gauge',
            $this->module_url . 'assets/jscript/gauge/gauge.init_1.js' => 'Init Gauges',
            $this->module_url . 'assets/jscript/gauge/gauge.init_reverse.js' => 'Init Gauges reverse',
        );

        $this->ui->makeui('test.kpi.ui.php', $cpData);
    }

    function Render($kpi = null) {
        $debug=false;
        $exists = false;
        //---load type extension
        if (!method_exists($this, $kpi['type'])) {
            $file_custom = $this->types_path . $kpi['type'] . '/kpi_controller.php';
            if (is_file($file_custom)) {
                //$exists = true;
                if ($debug)
                    echo "Loaded Custom Render:$file_custom<br/>";
                require_once($file_custom);
                
            } else {
                $rtn = $this->ShowMsg('<strong>Warning!</strong>Function:' . $kpi['type'] . '<br/>' . $kpi['title'] . '<br/>Does not exists. ', 'alert');
            }
            $rtn = $kpi['type']($kpi,$this);
        } else {
            $exists = true;
        }
        if ($exists)
            $rtn = $this->$kpi['type']($kpi);
        return $rtn;
    }

    /*
     * Most common render goes inline
     */

    function get_filter($kpi) {
        $filter = array();
        switch ($kpi['filter']) {
            case 'group':
                break;
            case 'user':
                $filter = array(
                    'idwf' => $kpi['idwf'],
                    'iduser' => $this->idu
                );
                break;
            default: //---filter by idwf
                $filter = array(
                    'idwf' => $kpi['idwf']
                );
                break;
        }
        return $filter;
    }


    function time_avg_all($kpi) {
        $filter = $this->get_filter($kpi);
    }

    function time_avg($kpi) {
        $timesum = 0;
        if ($kpi['resourceId'] <> '') {
            $filter = $this->get_filter($kpi);
            $tokens = $this->bpm->get_tokens_byResourceId($kpi['resourceId'], $filter);
            $cpData = $kpi;
            $max = 0;
            $min = 36000;
            foreach ($tokens as $thisToken) {
                $max = ($max < $thisToken['interval']['days']) ? $thisToken['interval']['days'] : $max;
                $min = ($min > $thisToken['interval']['days']) ? $thisToken['interval']['days'] : $min;
                $timesum+=$thisToken['interval']['days'];
            }
            $cpData['avg'] = (int) ($timesum / count($tokens));
            if ($timesum) {

                $cpData['avg_formated'] = number_format($timesum / count($tokens), 2);
            } else {
                $cpData['avg_formated'] = 0;
            }
            $cpData['max'] = ($kpi['max']) ? $kpi['max'] : $max;
            $cpData['min'] = $min;
            $rtn = $this->parser->parse('bpm/kpi_time_avg', $cpData, true);
        } else {
            $rtn = $this->ShowMsg('<strong>Warning!</strong>Function:' . $kpi['type'] . '<br/>' . $kpi['title'] . '<br/>resourceId not defined. ', 'alert');
        }
        return $rtn;
    }

    function count($kpi) {
        $filter = $this->get_filter($kpi);
        $tokens = $this->bpm->get_tokens_byResourceId($kpi['resourceId'], $filter);
        $cpData = $kpi;
        //var_dump($tokens);
        $cpData['count'] = count($tokens);
        $rtn = $this->parser->parse('bpm/kpi_count', $cpData, true);
        return $rtn;
    }

    function state($kpi) {
        $filter = array();
        switch ($kpi['filter']) {
            case 'group':
                break;
            case 'user':
                $filter = array(
                    'idwf' => $kpi['idwf'],
                    'iduser' => $this->idu
                );
                break;
            default: //---filter by idwf
                $filter = array(
                    'idwf' => $kpi['idwf']
                );
                break;
        }
        /*
         *  'pending'
          'manual'
          'user'
          'waiting'
         */
        $status = array(
            'pending',
            'manual',
            'user',
            'waiting'
        );
        //$filter['status'] = array('$in' => (array) $status); //@todo include other statuses
        $tokens = $this->bpm->get_tokens_byResourceId($kpi['resourceId'], $filter);
        $cpData = $kpi;
        //var_dump($tokens);
        //$cpData['tokens']=$tokens;
        $cpData['count'] = count($tokens);
        $rtn = $this->parser->parse('bpm/kpi_count', $cpData, true);
        return $rtn;
    }

    function Save_properties() {
        $this->load->helper('dbframe');
        $this->load->model('user/rbac');
        $this->load->model('app');
        $segments = $this->uri->segment_array();
        $debug = (in_array('debug', $segments)) ? true : false;
        $types_path = $this->types_path;
        $postkpi = $_POST;
        $idkpi = $postkpi['idkpi'];
        //---get type
        $type = $postkpi['type'];
        //----create empty frame according to the template
        $kpi = new dbframe();
        //---load base properties from helpers/types/base
        //---defines $common
        include($types_path . 'base/kpi.base.php');
        //---load custom properties from specific type
        $type_props = array();
        if (isset($type)) {
            $file_custom = $types_path . $type . '/properties.php';
            if (is_file($file_custom)) {
                if ($debug)
                    echo "Loaded Custom:$file_custom<br/>";
                include($file_custom);
            }
        }
        $properties_template = $common + $type_props;
        //----load the data from post
        $kpi->load($postkpi, $properties_template);

        if ($idkpi == '') {
            //---create new ID for the frame
            $idkpi = (int) $this->app->gen_inc('kpi', 'idkpi');
            $kpi->idkpi = $idkpi;
        }
        $dbkpi = ($this->kpi_model->get($idkpi));
        $addkpi = ($dbkpi) ? $dbkpi : array();
        $obj = $kpi->toSave() + $addkpi;

        $this->kpi_model->save($obj);
        //----register app in RBAC-REPOSIROTY
        $path = 'modules/bpm/controllers/model/' . $kpi->idwf . '/kpi/' . $kpi->idkpi . '/' . $kpi->title . ' (' . $kpi->type . ')';
        $properties = array(
            "source" => "User",
            "checkdate" => date('Y-m-d H:i:s'),
            "idu" => $this->idu
        );
        $this->rbac->put_path($path, $properties);
        //$kpi->groups = implode(',', $kpi->groups);
        //----dump results
        if (!$debug) {
            header('Content-type: application/json;charset=UTF-8');
            echo json_encode($kpi->toSave());
        } else {
            var_dump($obj);
        }
    }

    function ShowMsg($msg, $class = 'alert') {

        return '<div class="' . $class . '">
    <button type="button" class="close" data-dismiss="alert">&times;</button>' .
                $msg
                . '</div>';
    }

}

/* End of file kpi */
