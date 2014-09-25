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
class Case_manager extends MX_Controller {
    public $debug=array(
        'revert'=>false
    );
    function __construct() {
        parent::__construct();
        $this->load->library('parser');
        $this->load->model('user');
        $this->load->model('bpm');
        $this->load->helper('bpm');
        $this->load->model('user/group');
        $this->user->authorize('ADM,WFADM');
        //----LOAD LANGUAGE
        $this->types_path = 'application/modules/bpm/assets/types/';
        $this->module_path = 'application/modules/bpm/';
        //----LOAD LANGUAGE
        $this->lang->load('library', $this->config->item('language'));
        $this->idu = (int) $this->session->userdata('iduser');
        $this->base_url = base_url();
        $this->module_url = base_url() . $this->router->fetch_module() . '/';
    }

    function Archive($model, $idwf, $idcase = null) {
        $debug = (isset($this->debug[__FUNCTION__])) ? $this->debug[__FUNCTION__] : false;
        if ($debug)
            echo '<h2>' . __FUNCTION__ . '</h2>';
        $case = $this->bpm->get_case($idcase,$idwf);
        $this->bpm->archive_case($case);
        echo "Moving case $idcase  to Archive...<br/>";
        $this->bpm->delete_case($idwf, $idcase);
        echo "Done";
        
        
    }
    /*
     * This function will revert a case to a certain state
     */
    function revert($model,$idwf,$idcase,$resourceId){
        $debug = (isset($this->debug[__FUNCTION__])) ? $this->debug[__FUNCTION__] : false;
        if ($debug)
            echo '<h2>' . __FUNCTION__ . '</h2>';
        $case=$this->bpm->get_case($idcase);
        $token=$this->bpm->get_token($idwf, $idcase, $resourceId);
        $filter=array(
            'idwf'=>$idwf,
            'case'=>$idcase,
            '_id'=>array('$gt'=>$token['_id'])
                );
        //---remove tokens newer than this
        $this->bpm->clear_tokens($idwf, $idcase, $filter);
        $token['status']='pending';
        $this->bpm->save_token($token);
        $case['status']='open';
        $this->bpm->save_case($case);
        $this->bpm->update_case_token_status($idwf, $idcase);
        $out = array('status' => 'ok');
        if (!$debug) {
            header('Content-type: application/json;charset=UTF-8');
            echo json_encode($out);
        } else {
            var_dump($out);
        }
    }
    function Browse($model, $idwf, $case = null, $action = '') {
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
        $cpData['title'] = 'Case Manager';

        $cpData['css'] = array(
            $this->module_url . 'assets/css/case_manager.css' => 'Manager styles',
            $this->module_url . 'assets/css/extra-icons.css' => 'Extra Icons',
            $this->module_url . 'assets/css/fix_bootstrap_checkbox.css' => 'Fix Checkbox',
        );
        $cpData['js'] = array(
            $this->module_url . 'assets/jscript/case_manager/ext.settings.js' => 'Settings & overrides',
            $this->module_url . 'assets/jscript/fontawesome_icons.js' => 'FontAwesome icons',
            $this->module_url . 'assets/jscript/case_manager/ext.data.js' => 'data Components',
            $this->base_url . "jscript/ext/src/ux/form/SearchField.js" => 'Search Field',
            $this->module_url . 'assets/jscript/case_manager/ext.tokenGrid.js' => 'Types Grid',
            $this->module_url . 'assets/jscript/case_manager/ext.grid.js' => 'Grid',
            $this->module_url . 'assets/jscript/ext.model-utils.js' => 'Model utils',
            $this->module_url . 'assets/jscript/case_manager/ext.add_events.js' => 'Events for overlays',
            $this->module_url . 'assets/jscript/case_manager/ext.viewport.js' => 'viewport',
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
            'idwf' => $idwf,
            'idcase' => $case,
            'action' => $action
        );

        $this->ui->makeui('ext.ui.php', $cpData);
    }

    function Data($action, $model) {
        $segments = $this->uri->segment_array();
        $debug = (in_array('debug', $segments)) ? true : false;
        //$debug=true;
        $out = array();
        //--get all cases
        if (isset($model)) {
            switch ($action) {
                case 'read':
                    $start = ($this->input->post('start')) ? $this->input->post('start') : 0;
                    $limit = ($this->input->post('limit')) ? $this->input->post('limit') : 20;
                    $query = $this->input->post('query');


                    $sortObj = json_decode($this->input->post('sort'));

                    // build sort array

                    $sort = array();
                    if ($sortObj) {
                        foreach ($sortObj as $value) {
                            $sort[$value->property] = ($value->direction == 'ASC') ? 1 : -1;
                        };
                    } else {
                        $sort['checkdate'] = -1;
                    }
                    $fields=array(
                    		"id",
                    		"iduser",
                    		"status",
                    		"checkdate",
                    );
                    $cases = $this->bpm->get_all_cases($start, $limit, $sort, $query, $model,$fields);
                    
                    $out['totalCount'] =$this->bpm->get_all_cases_count($query,$model);
                    foreach ($cases as $case) {
                        //unset($case['history']);
                        //--set user
                        if (!isset($case['iduser']))
                            break;
                        $user = $this->user->get_user($case['iduser']);

                        $case['user'] = ($user) ? $user->nick : '???';
                        //----set pseudo status (add locked to statuses)
                        if (isset($case['locked'])) {
                            if ($case['locked'])
                                $case['status'] = 'locked';
                        }
                        //----set date
                        $case['date'] = date($this->lang->line('dateFmt'), strtotime($case['checkdate']));
                        $out['rows'][] = $case;
                    }
                    break;

                case 'update':
                    $input = json_decode(file_get_contents('php://input'));
                    //var_dump('$input',$input);
                    foreach ($input as $thisCase) {
                        $case = $this->bpm->get_case($thisCase->id);
                        $case['locked'] = $thisCase->locked;
                        $case['lockedBy'] = $this->idu;
                        $case['lockedDate'] = date('Y-m-d H:i:s');
                        $result = $this->bpm->save_case($case);
                        $out = array('status' => 'ok');
                    }
                    break;
                case 'destroy':
                    $input = json_decode(file_get_contents('php://input'));
                    foreach ($input as $case) {
                        $rs = $this->bpm->delete_case($model, $case->id);
                    }
                    $out = array('status' => 'ok');
                    break;
            }
        }
        if (!$debug) {
            header('Content-type: application/json;charset=UTF-8');
            echo json_encode($out);
        } else {
            var_dump($out);
        }
    }

    function Tokens($action, $idwf, $idcase, $output = 'json') {
        $segments = $this->uri->segment_array();
        $debug = (in_array('debug', $segments)) ? true : false;
        $out = array();
        //----if selected all tokens status
        if ($idcase == 'all') {
            $tokens = array();            
            $filter['idwf'] = $idwf;
            $all_tokens=$this->bpm->get_cases_stats($filter);
            foreach ($all_tokens as $token)
                $tokens[] = $token;
            $out['rows'] = $tokens;
        } else {
            //--get case
            $case = $this->bpm->get_case($idcase,$idwf);
            $idwf = $case['idwf'];

            if (isset($idwf) && isset($idcase)) {
                switch ($action) {
                    case 'history':
                        $tokens = array_slice($case['history'], 0, 100);
                        break;
                    case 'status':
                        // select all tokens
                        $status = array('$regex' => '');
                        $tokens = $this->bpm->get_tokens($idwf, $idcase, $status);
                        break;
                }
            }
            foreach ($tokens as $token) {
                //--set user
                $token['iduser'] = (isset($token['iduser'])) ? isset($token['iduser']) : isset($token['idu']);

                unset($token['history']);
                unset($token['_id']);
                //---get the user who locked
                $token['lockedBy'] = isset($token['lockedBy']) ? $token['lockedBy'] : null;
                $user = $this->user->get_user($token['iduser']);
                $token['user'] = $user->nick;
                //----set date
                $token['date'] = isset($token['checkdate']) ? date($this->lang->line('dateFmt'), strtotime($token['checkdate'])) : '???';
                $token['icon'] = "<img src='" . $this->base_url . $this->bpm->get_icon($token['type']) . "' />";
                $out['rows'][] = $token;
            }
        }
        $out['totalcount'] = count($tokens);
        switch ($output) {
            case 'json':
                if (!$debug) {
                    header('Content-type: application/json;charset=UTF-8');
                    echo json_encode($out);
                } else {
                    var_dump($out);
                }
                break;
            default:
                return $out;
        }
    }

    function get_tokens($model, $idwf, $idcase) {
        $debug = false;
        //---load language
        $wfData = $this->lang->language;
        //var_dump($level);
        $wfData['htmltitle'] = 'WF-Manager:' . $idwf;
        $wfData['theme'] = $this->config->item('theme');
        $wfData['base_url'] = $this->base_url;

        $status = array('$regex' => '');
        $rs = $this->bpm->get_tokens($idwf, $idcase, $status);
        $data['idwf'] = $idwf;
        $data['idcase'] = $idcase;
        $case = $this->bpm->get_case($idcase,$idwf);
        $dateIn = new DateTime($case['checkdate']);
        foreach ($rs as $token) {
            if (isset($token['interval'])) {
                if (isset($token['interval']['days'])) {
                    if ($token['interval']['days'] == 0) {
                        $token['soft_interval'] = $this->parser->parse_string($token['interval']['days'] . ' {days}', $wfData);
                    } else {
                        $token['soft_interval'] = $this->parser->parse_string($token['interval']['h'] . ':' . $token['interval']['i'] . ':' . $token['interval']['s'], $wfData);
                    }
                }
            }
            $data['tokens'][] = $token;
        }
        if (!$debug) {
            header('Content-type: application/json;charset=UTF-8');
            echo json_encode($data);
        } else {
            var_dump($data);
        }
    }

}

/* End of file kpi */