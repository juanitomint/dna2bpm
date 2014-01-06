<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');
/*
 * This is the core BPM Engine
 * 
 * This class has all what is needed to run a bpm model
 * 
 * @author Juan Ignacio Borda <juanignacioborda@gmail.com>
 * @date   Feb 10, 2013
 */

class Engine extends MX_Controller {

    private $debug = array();
    public $create_start_msg = false;

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
        //---Set the shapes that will be digged
        $this->digInto = array('Pool', 'Subprocess', 'CollapsedSubprocess', 'Lane');
        //---Debug options
        $this->debug['triggers'] = null;
        $this->debug['Run'] = null;
        $this->debug['Startcase'] = null;
        $this->debug['get_inbound_shapes'] = null;
        $this->debug['load_data'] = null;
        $this->debug['manual_task'] = null;

        //---debug Helpers
        $this->debug['run_Task'] = null;
        $this->debug['run_Exclusive_Databased_Gateway'] = null;
        $this->debug['run_IntermediateEventThrowing'] = null;
        $this->debug['run_IntermediateLinkEventThrowing'] = null;
        $this->debug['run_EndMessageEvent'] = null;
        $this->debug['run_EndNoneEvent'] = null;
        $this->debug['get_start_shapes'] = null;
        $this->debug['get_shape_parent'] = null;
        $this->idu = (int) $this->session->userdata('iduser');
        //$this->debug['get_shape_byname']=false;
    }

    function Index() {
        
    }

    function Newcase($model, $idwf, $manual = false) {
        //---Gen new case ID
        $case = $this->bpm->gen_case($idwf);
        if ($manual) {
            $mycase = $this->bpm->get_case($case);
            $mycase['run_manual'] = true;
            $this->bpm->save_case($mycase);
        }
        //---Start the case (will move next on startnone shapes)
        $this->Startcase($model, $idwf, $case);
    }

    function Startcase($model, $idwf, $case, $silent = false) {
        $debug = (isset($this->debug[__FUNCTION__])) ? $this->debug[__FUNCTION__] : false;
        if ($debug)
            var_dump($model, $idwf, $case, $debug);
        //---Remove tokens from db if there are any
        $this->bpm->clear_tokens($idwf, $case);
        $this->bpm->clear_case($case);
        //---start a case and insert start tokens in database
        $mywf = $this->bpm->load($idwf, true);

        $wf = bindArrayToObject($mywf['data']);

        if ($debug) {
            echo '<h2>$wf</h2>';
            var_dump($wf);
            echo '<hr>';
        }

        //---Get all start points of diagram
        $start_shapes = $this->bpm->get_start_shapes($wf);


        //----Raise an error if doesn't found any start point
        if (!$start_shapes)
            show_error("The Schema doesn't have an start point");
        //---Start all  StartNoneEvents as possible
        foreach ($start_shapes as $start_shape) {
            $this->bpm->set_token($idwf, $case, $start_shape->resourceId, $start_shape->stencil->id, 'pending');
        }
        if ($this->create_start_msg) {
            //------Create a message in the inbox.
            $msg = array(
                'iduser' => $this->session->userdata('iduser'),
                'subject' => $wf->properties->name . ':' . $case,
                'body' => $wf->properties->documentation,
                'from' => 'DNA²',
                'link' => 'bpm/engine/tokens/model' . $idwf . '/' . $case
            );
            $this->user->create_message($this->session->userdata('iduser'), $msg);
        }
        //---Redir the browser to engine Run
        $redir = "bpm/engine/run/model/$idwf/$case";
        if (!$silent) {
            header("Location:" . $this->base_url . $redir);
        } else {
            //$this->Run('model', $idwf, $case);
        }
    }

    function Run($model, $idwf, $case) {
        $this->data = (object) null;
        $debug = (isset($this->debug[__FUNCTION__])) ? $this->debug[__FUNCTION__] : false;
        //---check if case is locked
        $thisCase = $this->bpm->get_case($case);
        $locked = (isset($thisCase['locked'])) ? $thisCase['locked'] : false;
        if ($locked) {
            $user_lock = (array) $this->user->get_user($thisCase['lockedBy']);
            $msg_data = array(
                'user_lock' => $user_lock['name'] . ' ' . $user_lock['lastname'],
                'time' => date($this->lang->line('dateTimeFmt'), strtotime($thisCase['lockedDate']))
            );

            $this->show_modal(
                    $this->lang->line('lock'), $this->parser->parse_string($this->lang->line('caseLocked'), $msg_data)
            );
        } else {

            if ($debug)
                echo "<h2>" . __FUNCTION__ . '</h2>';
            $mywf = $this->bpm->load($idwf, true);
            $mywf['data']['idwf'] = $idwf;
            $mywf['data']['case'] = $case;
            $mywf['data']['folder'] = $mywf['folder'];
            $wf = bindArrayToObject($mywf['data']);
            //----make it publicly available to other methods
            $this->wf = $wf;
            //------Automatic Run
            $i = 1;
            //---LOAD CORE Functions---------------------------------
            $this->load->helper('bpmn2.0/start_end');
            $this->load->helper('bpmn2.0/gate');
            $this->load->helper('bpmn2.0/task');
            $this->load->helper('bpmn2.0/event');
            $this->load->helper('bpmn2.0/flow');
            $this->load->helper('bpmn2.0/subproc');
            //---------------------------------------------------------
            $this->load_data($wf, $case);
            //$open = $this->bpm->get_tokens($idwf, $case, 'pending');
            $status = 'pending';
            while ($i <= 100 and $open = $this->bpm->get_tokens($idwf, $case, $status)) {
                $i++;
                foreach ($open as $token) {
                    //---only call tokens that correspond to user.
                    //var_dump($token);
                    $shape = $this->bpm->get_shape($token['resourceId'], $wf);
                    $callfunc = 'run_' . $shape->stencil->id;
                    if ($debug) {
                        $name = (property_exists($shape->properties, 'name')) ? $shape->properties->name : '';
                        $doc = (property_exists($shape->properties, 'documentation')) ? $shape->properties->documentation : '';
                        echo 'About to call:' . $callfunc . ':' . $name . '<br/>' . $shape->stencil->id . '<br/>';
                        var_dump(function_exists($callfunc));
                    }
                    /*
                     * Calls the specific function for that shape or movenext
                     */
                    $result = (function_exists($callfunc)) ? $callfunc($shape, $wf, $this) : $this->bpm->movenext($shape, $wf);
                }
            }
            $this->get_pending('model', $idwf, $case);
        }
    }

    function run_post($model, $idwf, $case, $resourceId) {

        $debug = (isset($this->debug[__FUNCTION__])) ? $this->debug[__FUNCTION__] : false;
        $mywf = $this->bpm->load($idwf, true);
        $mywf['data']['idwf'] = $idwf;
        $mywf['data']['case'] = $case;
        $wf = bindArrayToObject($mywf['data']);
        //---check if not finished yet
        $token = $this->bpm->get_token($wf->idwf, $wf->case, $resourceId);
        if ($token['status'] <> 'finished') {
            if ($resourceId) {
                $shape = $this->bpm->get_shape($resourceId, $wf);
                if ($shape) {
                    $this->bpm->movenext($shape, $wf);
                } else {
                    show_error("The shape $resourceId doesn't exists anymore");
                }
            }
        }
        //---Redir the browser to engine Run
        $redir = "bpm/engine/run/model/$idwf/$case";
        header("Location:" . $this->base_url . $redir);
    }

    function run_gate($model, $idwf, $case, $resourceId, $flowId) {
        $debug = (isset($this->debug[__FUNCTION__])) ? $this->debug[__FUNCTION__] : false;
        $data = array();
        $mywf = $this->bpm->load($idwf, true);
        $mywf['data']['idwf'] = $idwf;
        $mywf['data']['case'] = $case;
        $wf = bindArrayToObject($mywf['data']);


        if ($resourceId) {
            $shape = $this->bpm->get_shape($resourceId, $wf);
            if ($shape) {
                //-- check if isn't finished yet
                $token = $this->bpm->get_token($idwf, $case, $resourceId);
                if ($token['status'] <> 'finished') {
                    //--mark gate as finished
                    $token['status'] = 'finished';
                    $token['run'] = (isset($token['run'])) ? $token['run'] + 1 : 1;
                    $token['checkdate'] = date('Y-m-d H:i:s');
                    //---Save token
                    $this->bpm->save_token($token);
                    //---update History
                    $history = array(
                        'checkdate' => date('Y-m-d H:i:s'),
                        'resourceId' => $shape->resourceId,
                        'iduser' => $this->idu,
                        'type' => $shape->stencil->id,
                        'microtime' => microtime(),
                        'run' => $token['run'],
                        'status' => $token['status'],
                        'name' => (isset($shape->properties->name)) ? $shape->properties->name : ''
                    );
                    $this->bpm->update_history($wf->case, $history);
                    $shape_flow = $this->bpm->get_shape($flowId, $wf);
                    //run_SequenceFlow(_flow, $wf);
                    $this->bpm->movenext($shape_flow, $wf);
                }
            } else {
                show_error("The shape $resourceId doesn't exists anymore");
            }
        }
        //---Redir the browser to engine Run
        $redir = "bpm/engine/run/model/$idwf/$case";
        header("Location:" . $this->base_url . $redir);
    }

    function task_locked($model, $idwf, $idcase, $resourceId) {
        $debug = (isset($this->debug[__FUNCTION__])) ? $this->debug[__FUNCTION__] : false;
        //----prepare renderData
        $renderData = array();
        $renderData = $this->lang->language;
        $renderData['theme'] = $this->config->item('theme');
        $renderData['base_url'] = $this->base_url;
        $renderData['idwf'] = $idwf;
        $renderData['case'] = $idcase;
        $renderData['resourceId'] = $resourceId;
        //-----load bpm
        $mywf = $this->bpm->load($idwf, true);
        $mywf['data']['idwf'] = $idwf;
        $mywf['data']['case'] = $idcase;
        $wf = bindArrayToObject($mywf['data']);
        //---load data 4 templating
        $this->load_data($wf, $idcase);

        if ($resourceId) {
            $renderData+=$this->bindObjectToArray($this->data);
            $renderData+=$this->bindObjectToArray($this->bpm->get_shape($resourceId, $wf)->properties);
            $renderData['documentation'] = $this->parser->parse_string($renderData['documentation'], $renderData, true, true);
            if ($debug)
                var_dump($renderData);
            $this->parser->parse('bpm/task_locked', $renderData);
        }
    }

    function manual_task($model, $idwf, $idcase, $resourceId) {
        $this->load->library('ui');
        $debug = (isset($this->debug[__FUNCTION__])) ? $this->debug[__FUNCTION__] : false;
        //$debug=true;
        //----prepare renderData
        $renderData = array();
        $renderData = $this->lang->language;
        $renderData['theme'] = $this->config->item('theme');
        $renderData['base_url'] = $this->base_url;
        $renderData['idwf'] = $idwf;
        $renderData['idcase'] = $idcase;
        $renderData['resourceId'] = $resourceId;
        //-----load bpm
        $mywf = $this->bpm->load($idwf, true);
        $mywf['data']['idwf'] = $idwf;
        $mywf['data']['case'] = $idcase;
        $wf = bindArrayToObject($mywf['data']);
        //---get case
        $case = $this->bpm->get_case($idcase);
        //---get token
        $token = $this->bpm->get_token($idwf, $idcase, $resourceId);
        //--get shape
        $shape = $this->bpm->get_shape($token['resourceId'], $wf);
        //-check if data is loaded
        if (!isset($this->data))
            $this->load_data($wf, $idcase);
        $renderData['task_name'] = $shape->properties->name;
        $renderData['task_documentation'] = $shape->properties->documentation;
        if ($resourceId) {
            $renderData+=$this->bindObjectToArray($this->data);
            $renderData['wf'] = $mywf['data']['properties'];
            //$renderData+=$mywf['data']['properties'];
            $renderData['token'] = $token;
            $renderData['case'] = $case;
            //--parse documentation string
            $renderData['task_documentation'] = ($renderData['task_documentation'] == '') ? '' : $this->parser->parse_string(nl2br($renderData['task_documentation']), $renderData, true, true);
            //--parse Name
            $renderData['task_name'] = ($renderData['task_name'] == '') ? '' : $this->parser->parse_string($renderData['task_name'], $renderData, true, true);
            if ($debug)
                var_dump($renderData);
            //---prepare UI
            $renderData['title'] = 'Manual Task';
            $renderData['js'] = array(
                $this->module_url . 'assets/jscript/manual_task.js' => 'Manual task JS'
            );
            //---prepare globals 4 js
            $renderData['global_js'] = array(
                'base_url' => $this->base_url,
                'module_url' => $this->module_url,
                'idwf' => $idwf,
                'idcase' => $idcase,
                'resourceId' => $resourceId,
            );
            $this->ui->compose('bpm/manual_task', 'bpm/bootstrap.ui.php', $renderData);
        }
    }

    function manual_gate($model, $idwf, $idcase, $resourceId) {
        $this->load->library('ui');
        $debug = (isset($this->debug[__FUNCTION__])) ? $this->debug[__FUNCTION__] : false;
        //----prepare renderData
        $renderData = array();
        $renderData = $this->lang->language;
        $renderData['theme'] = $this->config->item('theme');
        $renderData['base_url'] = $this->base_url;
        $renderData['idwf'] = $idwf;
        $renderData['idcase'] = $idcase;
        $renderData['gateId'] = $resourceId;
        //-----load bpm
        $mywf = $this->bpm->load($idwf, true);
        $mywf['data']['idwf'] = $idwf;
        $mywf['data']['case'] = $idcase;
        $wf = bindArrayToObject($mywf['data']);
        //---load data 4 templating
        $this->load_data($wf, $idcase);
        $i = 1;

        if ($resourceId) {
            $shape = $this->bpm->get_shape($resourceId, $wf);
            foreach ($shape->outgoing as $key => $out) {
                $shape_out = $this->bpm->get_shape($out->resourceId, $wf);
                $name = ($shape_out->properties->conditionexpression) ? $shape_out->properties->conditionexpression : $i;
                if ($shape_out->properties->conditiontype == 'Default')
                    $name.='(default)';
                $renderData['button'][] = array('name' => $name, 'resourceId' => $shape_out->resourceId);
                $i++;
            }
            $renderData+=$this->bindObjectToArray($this->data);
            $properties = $this->bindObjectToArray($this->bpm->get_shape($resourceId, $wf)->properties);
            $renderData = array_merge($renderData, $properties);
            if ($renderData['documentation']) {
                $renderData['documentation'] = $this->parser->parse_string(nl2br($renderData['documentation']), $renderData, true, true);
            }
//var_dump(__FUNCTION__,'$renderData',$renderData);
            //---prepare UI
            $renderData['title'] = 'Manual Gate';
            $renderData['js'] = array(
                $this->module_url . 'assets/jscript/manual_gate.js' => 'Manual Gate JS'
            );
            //---prepare globals 4 js
            $renderData['global_js'] = array(
                'base_url' => $this->base_url,
                'module_url' => $this->module_url,
                'idwf' => $idwf,
                'idcase' => $idcase,
                'resourceId' => $resourceId,
            );
            $this->ui->compose('bpm/manual_gate', 'bpm/bootstrap.ui.php', $renderData);
        }
    }

    function load_data($wf, $idcase) {
        $this->data = new stdClass();
        $debug = (isset($this->debug[__FUNCTION__])) ? $this->debug[__FUNCTION__] : false;
        //$debug = true;
        if ($debug)
            echo '<h2>' . __FUNCTION__ . '</h2>' .
            "Called @ " . xdebug_call_file() . "<br/>Line:" . xdebug_call_line() . "<br/>from: <b>" . xdebug_call_function() . '</b><hr/>';

        ////////////////////////////////////////////////////////////////////////
        ///////////////////// Read From DataStore  ///////////////////////////
        ////////////////////////////////////////////////////////////////////////
        $dataStores = $this->bpm->get_shape_byname('DataStore', $wf);
        foreach ($dataStores as $shape) {
            //echo $shape->properties->name;
            //---LOAD DATA CONNECTORS
            $modelname = 'bpm/connectors/' . $shape->properties->connector . '_connector';
            $this->load->model($modelname);
            //---END LOAD DATA CONNECTORS
            $strStor = $shape->properties->name;
            $conn = $shape->properties->connector . '_connector';
            $resource['query'] = $shape->properties->query;
            $resource['datastoreref'] = (isset($shape->properties->datastoreref)) ? $shape->properties->datastoreref : null;
            $resource['itemsubjectref'] = (isset($shape->properties->itemsubjectref)) ? $shape->properties->itemsubjectref : null;
            if ($debug) {
                var_dump('$strStor', $strStor, $resource);
                echo '<hr/>';
            }
            //$this->$strStor= bindArrayToObject($this->app->getall($item,$container));
            $this->data->$strStor = $this->$conn->get_data($resource);

            //----4 debug
            if ($debug) {
                echo "<h3>Data Store:$strStor</h3>";
                var_dump($this->data->$strStor);
            }
        }//--end foreach
        ////////////////////////////////////////////////////////////////////////
        ///////////////////// Read From DataObjects  ///////////////////////////
        ////////////////////////////////////////////////////////////////////////
        $dataStores = $this->bpm->get_shape_byname('DataObject', $wf);
        foreach ($dataStores as $shape) {
            //echo $shape->properties->name;
            //---LOAD DATA CONNECTORS
            $modelname = 'bpm/connectors/' . $shape->properties->name . '_connector';
            $this->load->model($modelname);
            //---END LOAD DATA CONNECTORS
            $strStor = $shape->properties->name;
            $conn = $shape->properties->connector . '_connector';
            $resource['source'] = (isset($shape->properties->source)) ? $shape->properties->source : null;
            if ($debug) {
                var_dump('$strStor', $strStor, $resource);
                echo '<hr/>';
            }
            //$this->$strStor= bindArrayToObject($this->app->getall($item,$container));
            $this->data->$strStor = $this->$conn->get_data($resource);

            //----4 debug
            if ($debug) {
                echo "<h3>Data Store:$strStor</h3>";
                var_dump($this->data->$strStor);
            }
        }//--end foreach
        ////////////////////////////////////////////////////////////////////////
        //---Read from data from CASE
        $case = $this->bpm->get_case($idcase);
        //---load mongo_connector by default
        $this->load->model('bpm/connectors/mongo_connector');
        if (isset($case['data'])) {
            foreach ($case['data'] as $key => $value) {
                if (is_array($value)) {
                    if (isset($value['connector'])) {
                        $conn = $value['connector'] . '_connector';
                        if ($debug)
                            echo "Calling Connector: $conn<br/>";
                        $this->data->$key = $this->$conn->get_data($value);
                    }
                } else { //add regular data
                    $this->data->$key = $value;
                }
            }
        }
        if ($debug)
            var_dump('$this->data', $this->data);
    }

    function do_signals($name) {
        $debug = (isset($this->debug[__FUNCTION__])) ? $this->debug[__FUNCTION__] : false;
        if ($debug)
            echo '<h2>' . __FUNCTION__ . '</h2>';
        $open = $this->bpm->get_signal_catchers($name);
        if ($debug)
            echo "TOKENS:<br/>";
        foreach ($open as $token) {
            if ($debug) {
                var_dump($token);
                echo '<hr/>';
            }
            $mywf = $this->bpm->load($token['idwf'], true);
            $mywf['data']['idwf'] = $token['idwf'];
            $mywf['data']['case'] = $token['case'];
            //---update the token so its finished
            $wf = bindArrayToObject($mywf['data']);
            $shape = $this->bpm->get_shape($token['resourceId'], $wf);
            //----trigger has occured then move to next shape
            $this->bpm->movenext($shape, $wf);
            //---now run the whole process
            if ($debug)
                echo "Run:" . $token['idwf'] . ':case:' . $token['case'] . "<br/>";
            $this->Run('model', $token['idwf'], $token['case']);
            //---TODO write some logging about signals
        }
    }

    function do_triggers() {
        $debug = (isset($this->debug[__FUNCTION__])) ? $this->debug[__FUNCTION__] : false;
        if ($debug)
            echo '<h2>' . __FUNCTION__ . '</h2>';
        //$type=array('$regex'=>$filter_status);
        //$status=array('$regex'=>'^wa*');
        $i = 0;
        $open = $this->bpm->get_triggers();
        $i++;
        if ($debug)
            echo "TOKENS";
        foreach ($open as $token) {
            if ($debug) {
                var_dump($token);
                echo '<hr/>';
            }
            $mywf = $this->bpm->load($token['idwf'], true);
            $mywf['data']['idwf'] = $token['idwf'];
            $mywf['data']['case'] = $token['case'];
            $wf = bindArrayToObject($mywf['data']);
            $shape = $this->bpm->get_shape($token['resourceId'], $wf);
            //----trigger has occured then move to next shape
            $this->bpm->movenext($shape, $wf);
            //---now run the whole process
            $this->Run('model', $token['idwf'], $token['case']);
            //---TODO write some logging about triggers
        }
    }

    function bindArrayToObject($array) {
        $return = new stdClass();

        foreach ($array as $k => $v) {
            if (is_array($v)) {
                $return->$k = bindArrayToObject($v);
            } else {
                $return->$k = $v;
            }
        }
        return $return;
    }

    function bindObjectToArray($object) {
        if (!is_object($object) && !is_array($object)) {
            return $object;
        }
        if (is_object($object)) {
            $object = json_decode(json_encode($object), true);
        }
        return $object;
    }

    function recursiveArraySearch($haystack, $needle, $index = null) {
        $haystack = (array) $haystack;
        $aIt = new RecursiveArrayIterator($haystack);
        $it = new RecursiveIteratorIterator($aIt);

        while ($it->valid()) {
            var_dump($it->getSubIterator(), $it->key(), $it->current());
            echo '<hr/>';
            if (((isset($index) AND ( $it->key() == $index)) OR (!isset($index))) AND ( $it->current() == $needle)) {
                //return $aIt->key();
                echo "****  FOUND ****";
                return (array) $it->getSubIterator();
            }

            $it->next();
        }

        return false;
    }

    function get_pending($model, $idwf, $idcase, $filter = null) {
        $debug = (isset($this->debug[__FUNCTION__])) ? $this->debug[__FUNCTION__] : false;
        //if no filter passed then set default to me
        //$debug = true;
        $renderData = array();
        $renderData = $this->lang->language;
        $renderData['theme'] = $this->config->item('theme');
        $renderData['base_url'] = $this->base_url;
        $renderData['idwf'] = $idwf;
        $renderData['case'] = $idcase;
        $user = $this->user->getuser($this->idu);

        //----the task is assignet to the user or is for the group the user belong to
        if (!isset($filter)) {
            $filter['$or'][] = array('assign' => $this->idu);
            $filter['$or'][] = array('idgroup' => array('$in' => $user->group));
        }

        //----Load Case
        $case = $this->bpm->get_case($idcase);
        //----set manual flag 4 test
        $run_manual = (isset($case['run_manual'])) ? $case['run_manual'] : false;
        //var_dump('case',$case,'run_manual',$run_manual);
        if ($case['status'] == 'open') {
            //----load WF data
            $myTasks = $this->bpm->get_pending($idcase, array('user', 'manual'), $filter);
            $first = $myTasks->getNext();
            if ($first) {
                //-----get id from token---------
                $token = $first;
                //var_dump('loaded token', $token);
                switch ($token['type']) {
                    case 'Exclusive_Databased_Gateway':
                        $this->manual_gate($model, $idwf, $idcase, $first['resourceId']);
                        break;
                    case 'Task':
                        $id = 'new';
                        $shape = $this->bpm->get_shape($first['resourceId'], $this->wf);
                        if ($shape and property_exists($shape->properties, 'operationref')) {
                            if ($shape->properties->operationref) {
                                $opRef = $shape->properties->operationref;
                                //--check if storage $opRf exists
                                if (property_exists($this->data, $opRef)) {
                                    if ($debug)
                                        var_dump('data by opRef:' . $opRef, $this->data->$opRef);
                                    $stored_data = $this->data->$opRef;
                                    $id = (isset($stored_data['id'])) ? $stored_data['id'] : 'new';
                                }
                            }
                        }

                        if ($id == '') {
                            //---try to assign id from token data passed
                            if (isset($token['data'])) {
                                if (isset($token['data']['id'])) {
                                    $id = $token['data']['id'];
                                } else {
                                    $id = 'new';
                                }
                            }
                        }
                        //-------------------------------------
                        //---save lock status
                        $token['lockedBy'] = (isset($token['lockedBy'])) ? $token['lockedBy'] : $this->idu;
                        $token['lockedDate'] = (isset($token['lockedDate'])) ? $token['lockedDate'] : date('Y-m-d H:i:s');

                        $this->bpm->save_token($token);

                        if ($token['lockedBy'] == $this->idu) {
                            //----route each typo to it's action
                            switch ($shape->properties->tasktype) {

                                case 'User':
                                    if (property_exists($shape->properties, 'rendering') and !$run_manual) {
                                        $rendering = trim($shape->properties->rendering);
                                        if ($rendering) {
                                            $token_id = $first['_id'];
                                            if (strstr('http', $rendering)) {
                                                $redir = $rendering . "?id=$id&token=$token_id";
                                            } else {
                                                $redir = $this->base_url . "dna2/render/edit/" . $shape->properties->rendering . "/$id/id/token/" . $token_id;
                                            }

                                            if (!$debug)
                                                header("Location:" . $redir);
                                            else
                                                echo "<a href='" . $redir . "'>" . $this->base_url . $redir . "</a>";
                                        } else {
                                            //----if has no rendering directive then call manual
                                            if ($debug) {
                                                echo "has no rendering directive then call manual<br>";
                                            }
                                            $this->manual_task($model, $idwf, $idcase, $first['resourceId']);
                                        }
                                    } else {
                                        if ($debug) {
                                            echo "Manual directive set<br>";
                                        }
                                        //----if has no rendering directive then call manual
                                        $this->manual_task($model, $idwf, $idcase, $first['resourceId']);
                                    }
                                    break;

                                default:
                                    if ($debug) {
                                        echo "Task has no type then call manual<br>";
                                    }
                                    $this->manual_task($model, $idwf, $idcase, $first['resourceId']);
                                    break;
                            }
                        } else {//--the token is locked by other user
                            //---load  no pending taks
                            $renderData['name'] = $this->lang->line('message');
                            $user_lock = (array) $this->user->get_user($token['lockedBy']);
                            $msg_data = array(
                                'user_lock' => $user_lock['name'] . ' ' . $user_lock['lastname'],
                                'time' => date($this->lang->line('dateTimeFmt'), strtotime($token['lockedDate']))
                            );

                            $this->show_modal(
                                    $this->lang->line('message'), $this->parser->parse_string($this->lang->line('taskLocked'), $msg_data)
                            );
                        }
                        break;
                }//--end switch token type
            } else {
                //---load  no pending taks
                $renderData['name'] = $this->lang->line('message');
                $renderData['documentation'] = $this->parser->parse_string($this->lang->line('noMoreTasks'), $case);
                $this->show_modal(
                        $this->lang->line('message'), $this->parser->parse_string($this->lang->line('noMoreTasks'), $case)
                );
            }
        } else {//case is closed or in other state
            $this->show_modal($this->lang->line('message'), $this->lang->line('caseClosed'));
        }
    }

    function show_modal($name, $text) {
        $this->load->library('ui');
        $renderData['base_url'] = $this->base_url;
        $renderData['name'] = $name;
        $renderData['text'] = $text;
        $renderData['title'] = $name;
        //---prepare UI
        $renderData['js'] = array(
            $this->module_url . 'assets/jscript/modal_window.js' => 'Modal Window Generic JS'
        );
        //---prepare globals 4 js
        $renderData['global_js'] = array(
            'base_url' => $this->base_url,
            'module_url' => $this->module_url,
        );
        $this->ui->compose('bpm/modal_msg', 'bpm/bootstrap.ui.php', $renderData);
    }

}

?>
