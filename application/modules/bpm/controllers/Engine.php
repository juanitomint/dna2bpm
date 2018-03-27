<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/**
 * This is the core BPM Engine This class has all what is needed to run a bpm model
 * @author Juan Ignacio Borda <juanignacioborda@gmail.com>
 * @date Feb 10, 2013
 */
class Engine extends MX_Controller {

    public $debug = array();
    public $run_filter = array();
    public $create_start_msg = false;
    public $run_after_stack = array();

    function __construct() {
        parent::__construct();
        $this->base_url = base_url();
        $this->module_url = base_url() . $this->router->fetch_module() . '/';
        $this->debug_manual = null;
        $this->load->config('config');
        $this->load->model('user/user');
        $this->load->model('user/group');
        $this->user->authorize();
        $this->load->model('bpm');
        $this->load->model('app');
        $this->load->model('msg');

        $this->load->library('parser');
        $this->load->helper('bpm');
        // ----LOAD LANGUAGE
        $this->lang->load('library', $this->config->item('language'));
        $this->lang->load('bpm', $this->config->item('language'));
        //----set inproc (whter we are being called form outside or new instance)
        $this->inproc=false;
        // ---Set the shapes that will be digged
        $this->digInto = array(
            'Pool',
            'Subprocess',
            'CollapsedSubprocess',
            'Lane'
        );
        // ---Set if suprocess has to be loaded: Default=true
        $this->expandSubProcess = true;
        // ---Debug options
        $this->debug ['Run'] = null;
        $this->debug ['run_post'] = null;
        $this->debug ['manual_task'] = null;
        $this->debug ['triggers'] = null;
        $this->debug ['Startcase'] = null;
        $this->debug ['get_inbound_shapes'] = null;
        $this->debug ['load_data'] = null;
        /*
         * true: don't show modal msgs null: no debug
         */
        $this->debug ['dont_show_modal'] = null;

        // ---debug Helpers
        $this->debug ['get_pending'] = null;
        $this->debug ['run_Task'] = null;
        $this->debug ['run_Subprocess'] = null;
        $this->debug ['run_CollapsedSubprocess'] = null;
        $this->debug ['run_Exclusive_Databased_Gateway'] = null;
        $this->debug ['run_IntermediateEventThrowing'] = null;
        $this->debug ['run_IntermediateLinkEventThrowing'] = null;
        $this->debug ['run_EndMessageEvent'] = null;
        $this->debug ['run_EndNoneEvent'] = null;
        $this->debug ['get_start_shapes'] = null;
        $this->debug ['get_shape_parent'] = null;
        $this->idu = $this->user->idu;
        // $this->debug['get_shape_byname']=false;
    }

    function Index() {
        echo "<h1>BPM Engine</h1>";
        // var_dump($this->user->get_user_safe($this->idu));
    }

    function assign($model, $idwf, $idcase, $src_resourceId, $resourceId, $idu=null) {
        if($idu){
            $idu = (int) $idu;
            $mywf = $this->bpm->load($idwf, $this->expandSubProcess);
            if (!$mywf) {
                show_error("Model referenced:$idwf does not exists");
            }
            $wf = bindArrayToObject($mywf ['data']);
            $wf->idwf = $idwf;
            $wf->case = $idcase;
            $shape = $this->bpm->get_shape($resourceId, $wf);
            $src_shape=$this->bpm->get_shape($src_resourceId, $wf);
            if(!$shape)
                show_error("The shape:$resourceId doesn't exists in the model: $idwf");
            if(!$src_shape)
                show_error("The source shape:$resourceId doesn't exists in the model: $idwf");

            $token = $this->bpm->get_token($idwf, $idcase, $resourceId);
            if ($token) {
                $this->bpm->assign_task($token, (array) $idu);
            } else {
                $token = $this->bpm->token_checkin(array(), $wf, $shape);
                $token['assign'] = array($idu);
                $this->bpm->save_token($token);
            }
            $this->run_post($model, $idwf, $idcase, $src_resourceId);
        }
    }

    function Newcase($model, $idwf, $manual = false, $parent = null, $silent = false, $data = array()) {
        // ---Gen new case ID
        $idcase = $this->bpm->gen_case($idwf,null,$data);
        if ($manual) {
            $mycase = $this->bpm->get_case($idcase, $idwf);
            $mycase ['run_manual'] = true;

            $this->bpm->save_case($mycase);
        }

        // ---Start the case (will move next on startnone shapes)
        $this->Startcase($model, $idwf, $idcase, $silent);
    }

    function Start($model, $idwf, $idcase, $silent = false) {
        $this->Startcase($model, $idwf, $idcase, $silent = false);
    }

    function Startcase($model, $idwf, $idcase, $silent = false) {
        $debug = (isset($this->debug [__FUNCTION__])) ? $this->debug [__FUNCTION__] : false;
        if ($debug)
            var_dump($model, $idwf, $idcase, $debug);
        // ---Remove tokens from db if there are any
        $this->bpm->clear_tokens($idwf, $idcase);
        $this->bpm->clear_case($idwf, $idcase);
        // ---start a case and insert start tokens in database
        $mywf = $this->bpm->load($idwf, $this->expandSubProcess);
        if (!$mywf) {
            show_error("Model referenced:$idwf does not exists");
        }
        $wf = bindArrayToObject($mywf ['data']);

        if ($debug) {
            echo '<h2>$wf</h2>';
            var_dump($wf);
            echo '<hr>';
        }

        // ---Get all start points of diagram
        $start_shapes = $this->bpm->get_start_shapes($wf);

        // ----Raise an error if doesn't found any start point
        if (!$start_shapes)
            show_error("The Schema doesn't have an start point");
        // ---Start all StartNoneEvents as possible
        foreach ($start_shapes as $start_shape) {
            $this->bpm->set_token($idwf, $idcase, $start_shape->resourceId, $start_shape->stencil->id, 'pending');
        }
        if ($this->create_start_msg) {
            // ------Create a message in the inbox.
            $msg = array(
                'iduser' => $this->session->userdata('iduser'),
                'subject' => $wf->properties->name . ':' . $idcase,
                'body' => $wf->properties->documentation,
                'from' => 'DNA²',
                'link' => 'bpm/engine/tokens/model' . $idwf . '/' . $idcase
            );
            $this->user->create_message($this->session->userdata('iduser'), $msg);
        }
        // ---Redir the browser to engine Run
        $redir = "bpm/engine/run/model/$idwf/$idcase";
        if (!$silent) {
            redirect( $this->base_url . $redir);
        } else {
            //echo "created:  $idwf, $idcase<hr/>";
            // $this->Run('model', $idwf, $case);
        }
    }

    /*
     * Engine core, here is where the functions for each shape get called @param string='model' @param string @param string @param string
     */

    function Run($model, $idwf, $case, $run_resourceId = null, $silent = null) {
        $this->data = (object) null;
        //---sanitize resourceId
        if ($run_resourceId)
            $run_resourceId = urldecode($run_resourceId);
        $debug = (isset($this->debug [__FUNCTION__])) ? $this->debug [__FUNCTION__] : false;
        if ($debug)
            echo "<h2>" . __FUNCTION__ . " $idwf -> $case </h2>";
        //---Set break_on_next to false
        $this->break_on_next = false;
        // ---check if case is locked
        $thisCase = $this->bpm->get_case($case, $idwf);
        $locked = (isset($thisCase ['locked'])) ? $thisCase ['locked'] : false;
        if ($locked) {
            $user_lock = (array) $this->user->get_user($thisCase ['lockedBy']);
            $msg_data = array(
                'user_lock' => $user_lock ['name'] . ' ' . $user_lock ['lastname'],
                'time' => date($this->lang->line('dateTimeFmt'), strtotime($thisCase ['lockedDate']))
            );

            $this->show_modal($this->lang->line('lock'), $this->parser->parse_string($this->lang->line('caseLocked'), $msg_data));
        } else {
            //---check Exists.
            $mywf = $this->bpm->load($idwf, $this->expandSubProcess);

            if($mywf){
            $mywf ['data'] ['idwf'] = $idwf;
            $mywf ['data'] ['case'] = $case;
            $mywf ['data'] ['folder'] = $mywf ['folder'];
            $wf = bindArrayToObject($mywf ['data']);
            // ----make it publicly available to other methods
            $this->wf = $wf;
            // ------Automatic Run
            $i = 1;
            // ---LOAD CORE Functions---------------------------------
            $this->load->helper('bpmn2.0/start_end');
            $this->load->helper('bpmn2.0/gate');
            $this->load->helper('bpmn2.0/task');
            $this->load->helper('bpmn2.0/event');
            $this->load->helper('bpmn2.0/flow');
            $this->load->helper('bpmn2.0/subproc');
            // ---------------------------------------------------------
            $this->load_data($wf, $case);
            // $open = $this->bpm->get_tokens($idwf, $case, 'pending');
            $status = 'pending';
            $wf->prevent_run = array();
            $filter = (count($this->run_filter)) ? $this->run_filter : array(
                'idwf' => $idwf,
                'case' => $case,
                'status' => array('$in'=>array('pending','waiting'))
            );

            // ----filter specific shape to run
            if ($run_resourceId)
                $filter ['resourceId'] = $run_resourceId;
            //   var_dump(json_encode($filter));exit;
            /**
             * Start procesing pending tokens
             */
            $open = $this->bpm->get_tokens_byFilter($filter);
            while ($i <= 100 and $open = $this->bpm->get_tokens_byFilter($filter,
            array(), //---fields
            
            array( // ----Order
                'status'=>true,
                'checkdate'=>true
                )
                )) {
                if ($debug)
                    echo "<h1>Step:$i</h1>";
                $i ++;
                foreach ($open as $token) {
                    // ---only call tokens that correspond to user.
                    //  var_dump($token);
                    $resourceId = $token ['resourceId'];

                    if (!in_array($resourceId, $wf->prevent_run)) {
                        $shape = $this->bpm->get_shape($resourceId, $wf);
                        if (!$shape) {
                            show_error("Can't find $resourceId in model: engine line " . __LINE__);
                        }
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
            //----remove waiting from filter after 1st run
            $filter['status']='pending';
            $open = $this->bpm->get_tokens_byFilter($filter);

            }

            $this->bpm->update_case_token_status($idwf, $case);
            //----if some helper want to break then break
//            if ($this->break_on_next) {
//                redirect($this->base_url . $this->config->item('default_controller'));
//            }
            //----return if silent
            if ($silent)
                return;
            $this->get_pending('model', $idwf, $case, $run_resourceId);
            $this->run_after();
            $run_resourceId = null;


            //---end check model exists
            } else {
                show_error("Model: $idwf doesn't exitst contact Administrator");
            }
        }
    }

    function run_after() {
        foreach ($this->run_after_stack as $data) {
            call_user_func_array(array(
                $this,
                $data ['func']
                    ), $data ['args']);
        }
    }

    function run_task($model, $idwf, $case, $resourceId) {

    }

    function post($token_id,$external_origin='unknown',$external_id=null) {
        /** Check security **/
    if($token=$this->bpm->get_token_byid($token_id)) {
        $idcase=$token['case'];
        $idwf=$token['idwf'];
        /// check token sanity
        $case=$this->bpm->get_case($idcase, $idwf);
        $case['data']['external'][$external_origin][$token['resourceId']]=$external_id;
        $this->bpm->save_case($case);
        //---Run Run post
        $this->run_post('model',$token['idwf'],$token['case'],$token['resourceId']);
    
        
    } else {
        show_error('Token not found',500);
    }
        
    }
    function run_post($model, $idwf, $case, $resourceId) {
        $debug = (isset($this->debug [__FUNCTION__])) ? $this->debug [__FUNCTION__] : false;
        if ($debug)
            echo "<h2>" . __FUNCTION__ . '</h2>';
        //---sanitize resourceId
        $resourceId = urldecode($resourceId);
        $mywf = $this->bpm->load($idwf, $this->expandSubProcess);
        $mywf ['data'] ['idwf'] = $idwf;
        $mywf ['data'] ['case'] = $case;
        $wf = bindArrayToObject($mywf ['data']);
        // ---check if not finished yet
        $token = $this->bpm->get_token($wf->idwf, $wf->case, $resourceId);
        if ($token ['status'] != 'finished') {
            // @todo check permissions
            if ($resourceId) {
                $shape = $this->bpm->get_shape($resourceId, $wf);
                if ($shape) {
                //load data
                // //////////////////////////////////////////////////////////////////////
                // /////////////////// Read From DataObjects /////////////////  //////////
                // //////////////////////////////////////////////////////////////////////
                //-get Inbound shapes
                $previous = $this->bpm->get_previous($resourceId, $wf);

                $post=$this->input->post();
                foreach ($previous as $dataShape) {
                    if ($dataShape->stencil->id == 'DataObject') {
                    // echo $shape->properties->name;
                    // ---LOAD DATA CONNECTORS
                    // var_dump($dataShape->properties->input_output);exit;
                        if($dataShape->properties->input_output<>'Output'){
                        if ($dataShape->properties->connector) {
                        $modelname = 'bpm/connectors/' . $dataShape->properties->connector . '_connector';
                        $this->load->model($modelname);
                        // ---END LOAD DATA CONNECTORS
                        $strStor = $dataShape->properties->name;
                        $conn = $dataShape->properties->connector . '_connector';
                        if ($debug) {
                            var_dump('$strStor', $strStor, $resource);
                            echo '<hr/>';
                        }
                        // $this->$strStor= bindArrayToObject($this->app->getall($item,$container));
                        if(method_exists($this->$conn,'save_data')){
                            $this->$conn->save_data($idwf,$case,$dataShape,$post);
                        }
                        // ----4 debug
                        if ($debug) {
                            echo "<h3>Data Store:$strStor</h3>";
                            var_dump($this->data->$strStor);
                        }
                    }
                    }
                    }
                } // --end foreach

                // //////////////////////////////////////////////////////////////////////
                    //---process data objects

                    //---save postdata in case
                    if (property_exists($shape->properties, 'datainputset')) {
                        if (property_exists($shape->properties->datainputset, 'items')) {
                        $thisCase = $this->bpm->get_case($case);
                        $thisCase['data']['datainputset'] = (isset($thisCase['data']['datainputset'])) ? (array) $thisCase['data']['datainputset'] : array();
                        if (count($_POST)) {
                            foreach ($shape->properties->datainputset->items as $item) {
                                if (isset($_POST[$item->name]))
                                    $thisCase['data']['datainputset'][$item->name] = $_POST[$item->name];
                            }
                            $this->bpm->save_case($thisCase);
                        }
                    }
                    }
                    //---MOVENEXT
                    $this->bpm->movenext($shape, $wf);
                } else {
                    show_error("The shape $resourceId doesn't exists anymore");
                }
            }
            // ----if its a subprocess try to run all other subprocesses
//            $subproc = $this->bpm->get_tokens($idwf, $case, $status = 'waiting', 'CollapsedSubprocess');
//            foreach ($subproc as $token) {
//                $this->bpm->set_token($idwf, $case, $token ['resourceId'], $token ['type'], 'pending');
//            }
        }
        // ---Redir the browser to engine Run

        $redir = "bpm/engine/run/model/$idwf/$case";
        if (!$debug) {
            redirect($this->base_url . $redir);
        } else {
            echo 'Location:<a href="' . $this->base_url . $redir . '"> >>> Click here to continue <<< </a>';
        }
    }

    function run_gate($model, $idwf, $case, $resourceId, $flowId) {
        $debug = (isset($this->debug [__FUNCTION__])) ? $this->debug [__FUNCTION__] : false;
        if ($debug)
            echo "<h2>" . __FUNCTION__ . '</h2>';

        //---sanitize resourceId
        $resourceId = urldecode($resourceId);
        $flowId = urldecode($flowId);

        $data = array();
        $mywf = $this->bpm->load($idwf);
        $mywf ['data'] ['idwf'] = $idwf;
        $mywf ['data'] ['case'] = $case;
        $wf = bindArrayToObject($mywf ['data']);

        if ($resourceId) {
            $shape = $this->bpm->get_shape($resourceId, $wf);
            if ($shape) {
                // -- check if isn't finished yet
                $token = $this->bpm->get_token($idwf, $case, $resourceId);
                if ($token ['status'] != 'finished') {
                    // --mark gate as finished
                    $token ['status'] = 'finished';
                    $token ['run'] = (isset($token ['run'])) ? $token ['run'] + 1 : 1;
                    $token ['checkdate'] = date('Y-m-d H:i:s');
                    // ---Save token
                    $this->bpm->save_token($token);
                    // ---update History
                    $history = array(
                        'checkdate' => date('Y-m-d H:i:s'),
                        'resourceId' => $shape->resourceId,
                        'iduser' => $this->idu,
                        'type' => $shape->stencil->id,
                        'microtime' => microtime(true),
                        'run' => $token ['run'],
                        'status' => $token ['status'],
                        'name' => (isset($shape->properties->name)) ? $shape->properties->name : ''
                    );
                    $this->bpm->update_history($wf->idwf, $wf->case, $history);
                    $shape_flow = $this->bpm->get_shape($flowId, $wf);
                    // run_SequenceFlow(_flow, $wf);
                    $this->bpm->movenext($shape_flow, $wf);
                }
            } else {
                show_error("The shape $resourceId doesn't exists anymore");
            }
        }
        // ---Redir the browser to engine Run
        $redir = "bpm/engine/run/model/$idwf/$case";
        redirect($this->base_url . $redir);
    }

    function task_locked($model, $idwf, $idcase, $resourceId) {
        $debug = (isset($this->debug [__FUNCTION__])) ? $this->debug [__FUNCTION__] : false;
        // ----prepare renderData
        $renderData = array();
        $renderData = $this->lang->language;
        $renderData ['theme'] = $this->config->item('theme');
        $renderData ['base_url'] = $this->base_url;
        $renderData ['idwf'] = $idwf;
        $renderData ['case'] = $idcase;
        $renderData ['resourceId'] = $resourceId;
        // -----load bpm
        $mywf = $this->bpm->load($idwf, $this->expandSubProcess);
        $mywf ['data'] ['idwf'] = $idwf;
        $mywf ['data'] ['case'] = $idcase;
        $wf = bindArrayToObject($mywf ['data']);
        // ---load data 4 templating
        $this->load_data($wf, $idcase);

        if ($resourceId) {
            $renderData += $this->bindObjectToArray($this->data);
            $renderData += $this->bindObjectToArray($this->bpm->get_shape($resourceId, $wf)->properties);
            $renderData ['documentation'] = $this->parser->parse_string($renderData ['documentation'], $renderData, true, true);
            if ($debug)
                var_dump($renderData);
            $this->parser->parse('bpm/task_locked', $renderData);
        }
    }

    function manual_task($model, $idwf, $idcase, $resourceId) {
        $this->load->library('ui');
        //---sanitize resourceId
        $resourceId = urldecode($resourceId);
        $debug = (isset($this->debug [__FUNCTION__])) ? $this->debug [__FUNCTION__] : false;
        // $debug=true;
        //---prepare additions arrays
        $this->add_js=array();
        $this->add_css=array();
        $this->add_globals=array();
        // ----prepare renderData
        $renderData = array();
        $renderData ['lang'] = $this->lang->language;
        $renderData ['theme'] = $this->config->item('theme');
        $renderData ['base_url'] = $this->base_url;
        $renderData ['idwf'] = $idwf;
        $renderData ['idcase'] = $idcase;
        $renderData ['resourceId'] = $resourceId;
        $renderData['date'] = date($this->lang->line('dateFmt'));

        // -----load bpm
        $mywf = $this->bpm->load($idwf, $this->expandSubProcess);
        $mywf ['data'] ['idwf'] = $idwf;
        $mywf ['data'] ['case'] = $idcase;
        $wf = bindArrayToObject($mywf ['data']);
        // ---get case
        $case = $this->bpm->get_case($idcase, $idwf);
        //---set inititaror
        $renderData['Initiator'] = (array) $this->user->get_user_safe($case['iduser']);
        $renderData['user']=$renderData['Initiator'];
        // ---get token
        $token = $this->bpm->get_token($idwf, $idcase, $resourceId);
        $renderData['token']=$token;
        // --get shape
        $shape = $this->bpm->get_shape($token ['resourceId'], $wf);
        // -check if data is loaded
        if (!isset($this->data))
            $this->load_data($wf, $idcase);
        $renderData ['task_name'] = $shape->properties->name;
        $add = '';
            if (property_exists($shape->properties, 'subproc_parent')) {
                $parent = $this->bpm->get_shape($shape->properties->subproc_parent, $wf);
                $renderData ['task_name']=$parent->properties->name.'/'.$renderData ['task_name'];
            }
        if (isset($case ['parent'])) {
            $renderData ['task_name'] = $case ['parent'] ['token'] ['title'] . '<br/>' . $shape->properties->name;
        }
        //-get Inbound shapes
        $previous = $this->bpm->get_previous($resourceId, $wf);
        //-Prepare Documents
        foreach ($previous as $dataShape) {
            if ($dataShape->stencil->id == 'DataObject') {
                    $do = $this->bindObjectToArray($dataShape);
                    $strStor = $dataShape->properties->name;
                    $conn = $dataShape->properties->connector . '_connector';
                    $resource ['source'] = (isset($dataShape->properties->source)) ? $dataShape->properties->source : null;
                    if (method_exists($this->$conn, 'get_ui')) {
                        $do['ui'] = $this->$conn->get_ui($resource, $dataShape, $wf, $this);
                    }

                    $renderData['DataObject_Input'][] = $do;

            }
        }
        //  var_dump($shape->properties->datainputset);
        //----prepare manual input
        if (property_exists($shape->properties,'datainputset')) {
            if (property_exists($shape->properties->datainputset, 'items')) {
            foreach ($shape->properties->datainputset->items as $item) {
                if (!strstr('.', $item->name) and $item->whileexecuting == 'true') {
                    $renderData['DataInputSet'][] = array(
                        'name' => $item->name,
                        'required' => ($item->optional == 'true') ? 'required' : '',
                    );
                }
            }
        }
        }
        // var_dump($renderData['DataInputSet']);
// 		exit;
        $renderData ['task_documentation'] = $shape->properties->documentation;

        if ($resourceId) {
            $renderData += $this->bindObjectToArray($this->data);
            $renderData ['wf'] = $mywf ['data'] ['properties'];
            // $renderData+=$mywf['data']['properties'];
            $renderData ['token'] = $token;

            //---map users assigned
            if (isset($renderData['token']['assign'])) {
                $renderData ['assign'] = array_map(
                        function($iduser) {
                    return (array) $this->user->get_user_safe($iduser);
                }, $renderData['token']['assign']);
            }
            $renderData ['case'] = $case;
            // --parse documentation string
            $renderData ['task_documentation'] = ($renderData ['task_documentation'] == '') ? '' : $this->parser->parse_string(nl2br($renderData ['task_documentation']), $renderData, true, true);
            // --parse Name
            $renderData ['task_name'] = ($renderData ['task_name'] == '') ? '' : $this->parser->parse_string($renderData ['task_name'], $renderData, true, true);
            if ($debug)
                var_dump($renderData);
            // ---prepare UI
            $renderData ['title'] = 'Manual Task';
            //----Skip javascript if no modal asked
            if (!$this->debug['dont_show_modal']) {
                $renderData ['js'] =array_merge(
                    $this->add_js,
                    array(
                    $this->module_url . 'assets/jscript/manual_task.js' => 'Manual task JS'
                )
                );

                $renderData ['css'] =$this->add_css;

            }
            // ---prepare globals 4 js
            $renderData ['global_js'] = array(
                'base_url' => $this->base_url,
                'module_url' => $this->module_url,
                'idwf' => $idwf,
                'idcase' => $idcase,
                'resourceId' => $resourceId
            );
            $renderData ['global_js']+=$this->add_globals;
            // var_dump($renderData);exit;
            $this->ui->compose('bpm/manual_task', 'bpm/bootstrap.ui.php', $renderData);
        }
        $this->output->_display();
        exit();
    }

    function manual_gate($model, $idwf, $idcase, $resourceId) {
        $this->load->library('ui');
        //---sanitize resourceId
        $resourceId = urldecode($resourceId);
        $debug = (isset($this->debug [__FUNCTION__])) ? $this->debug [__FUNCTION__] : false;
        // ----prepare renderData
        $renderData = array();
        $renderData ['lang'] = $this->lang->language;
        $renderData ['theme'] = $this->config->item('theme');
        $renderData ['base_url'] = $this->base_url;
        $renderData ['idwf'] = $idwf;
        $renderData ['idcase'] = $idcase;
        $renderData ['gateId'] = $resourceId;
        // -----load bpm
        $mywf = $this->bpm->load($idwf, $this->expandSubProcess);
        $mywf ['data'] ['idwf'] = $idwf;
        $mywf ['data'] ['case'] = $idcase;
        $wf = bindArrayToObject($mywf ['data']);
        // ---load data 4 templating
        $this->load_data($wf, $idcase);
        // ---get case
        $case = $this->bpm->get_case($idcase, $idwf);
        //---set inititaror
        $renderData['Initiator'] = (array) $this->user->get_user_safe($case['iduser']);

        $i = 1;

        if ($resourceId) {
            $shape = $this->bpm->get_shape($resourceId, $wf);
            foreach ($shape->outgoing as $key => $out) {
                $shape_out = $this->bpm->get_shape($out->resourceId, $wf);
                $name = ($shape_out->properties->conditionexpression) ? $shape_out->properties->conditionexpression : $i;
                if ($shape_out->properties->conditiontype == 'Default')
                    $name .= ' (default)';
                $renderData ['button'] [] = array(
                    'name' => $name,
                    'resourceId' => $shape_out->resourceId
                );
                $i ++;
            }
            $renderData += $this->bindObjectToArray($this->data);
            $properties = $this->bindObjectToArray($this->bpm->get_shape($resourceId, $wf)->properties);
            $renderData = array_merge($renderData, $properties);
            if ($renderData ['documentation']) {
                $renderData ['documentation'] = $this->parser->parse_string(nl2br($renderData ['documentation']), $renderData, true, true);
            }
            // var_dump(__FUNCTION__,'$renderData',$renderData);
            // ---prepare UI
            $add = '';
            if (property_exists($shape->properties, 'subproc_parent')) {
                $parent = $this->bpm->get_shape($shape->properties->subproc_parent, $wf);
                $add = $parent->properties->name.'/';
            }
            $renderData ['title'] = 'Manual Gate';
            $renderData ['name'] = $add.$renderData ['name'] ;
            $renderData ['js'] = array(
                $this->module_url . 'assets/jscript/manual_gate.js' => 'Manual Gate JS'
            );
            // ---prepare globals 4 js
            $renderData ['global_js'] = array(
                'base_url' => $this->base_url,
                'module_url' => $this->module_url,
                'idwf' => $idwf,
                'idcase' => $idcase,
                'resourceId' => $resourceId
            );
            $this->ui->compose('bpm/manual_gate', 'bpm/bootstrap.ui.php', $renderData);
        }
        $this->output->_display();
        exit();
    }

    function load_data($wf, $idcase) {
        $this->data = new stdClass ();
        $debug = (isset($this->debug [__FUNCTION__])) ? $this->debug [__FUNCTION__] : false;
        // $debug = true;
        if ($debug)
            echo '<h2>' . __FUNCTION__ . '</h2>' . "Called @ " . xdebug_call_file() . "<br/>Line:" . xdebug_call_line() . "<br/>from: <b>" . xdebug_call_function() . '</b><hr/>';

        // //////////////////////////////////////////////////////////////////////
        // /////////////////// Read From DataStore ///////////////////////////
        // //////////////////////////////////////////////////////////////////////
        $dataStores = $this->bpm->get_shape_byname('DataStore', $wf);
        foreach ($dataStores as $shape) {
            // echo $shape->properties->name;
            // ---LOAD DATA CONNECTORS
            $modelname = 'bpm/connectors/' . $shape->properties->connector . '_connector';
            $this->load->model($modelname);
            // ---END LOAD DATA CONNECTORS
            $strStor = $shape->properties->name;
            $conn = $shape->properties->connector . '_connector';
            $resource ['query'] = $shape->properties->query;
            $resource ['datastoreref'] = (isset($shape->properties->datastoreref)) ? $shape->properties->datastoreref : null;
            $resource ['itemsubjectref'] = (isset($shape->properties->itemsubjectref)) ? $shape->properties->itemsubjectref : null;
            if ($debug) {
                var_dump('$strStor', $strStor, $resource);
                echo '<hr/>';
            }
            // $this->$strStor= bindArrayToObject($this->app->getall($item,$container));
            //----check if get_data exists before call, if not leave as is
            if(method_exists($this->$conn,'get_data')){
                $this->data->$strStor = json_decode(json_encode($this->$conn->get_data($resource, $shape, $this->wf)));
            }

            // ----4 debug
            if ($debug) {
                echo "<h3>Data Store:$strStor</h3>";
                var_dump($this->data->$strStor);
            }
        } // --end foreach
        // //////////////////////////////////////////////////////////////////////
        // /////////////////// Read From DataObjects ///////////////////////////
        // //////////////////////////////////////////////////////////////////////
        $dataStores = $this->bpm->get_shape_byname('DataObject', $wf);

        foreach ($dataStores as $shape) {
            // echo $shape->properties->name;
            // ---LOAD DATA CONNECTORS
            if ($shape->properties->connector) {
                $modelname = 'bpm/connectors/' . $shape->properties->connector . '_connector';
                $this->load->model($modelname);
                // ---END LOAD DATA CONNECTORS
                $strStor = $shape->properties->name;
                $conn = $shape->properties->connector . '_connector';
                $resource ['source'] = (isset($shape->properties->source)) ? $shape->properties->source : null;
                if ($debug) {
                    var_dump('$strStor', $strStor, $resource);
                    echo '<hr/>';
                }
                // $this->$strStor= bindArrayToObject($this->app->getall($item,$container));
                if(method_exists($this->$conn,'get_data')){
                    $this->data->$strStor = $this->$conn->get_data($resource, $shape, $this->wf);
                }
                // ----4 debug
                if ($debug) {
                    echo "<h3>Data Store:$strStor</h3>";
                    var_dump($this->data->$strStor);
                }
            }
        } // --end foreach
        // //////////////////////////////////////////////////////////////////////
        // ---Read from data from CASE
        $case = $this->bpm->get_case($idcase, $wf->idwf);
        // ---load mongo_connector by default
        $this->load->model('bpm/connectors/mongo_connector');
                if (isset($case ['data'])) {
            foreach ($case ['data'] as $key => $value) {
                if (is_array($value)) {
                    if (isset($value ['connector'])) {
                        $conn = $value ['connector'] . '_connector';
                        if ($debug)
                            echo "Calling Connector: $conn<br/>";
                        $this->load->model("bpm/connectors/$conn");
                        if(method_exists($this->$conn,'get_data'))
                        $this->data->$key = $this->$conn->get_data($value);
                    } else {
                        $this->data->$key = $value;
                    }
                } else { // add regular data
                    $this->data->$key = $value;
                }
            }
        }
        if ($debug)
            var_dump('$this->data', $this->data);
    }

    function do_signals($name) {
        $debug = (isset($this->debug [__FUNCTION__])) ? $this->debug [__FUNCTION__] : false;
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
            $mywf = $this->bpm->load($token ['idwf'], true);
            $mywf ['data'] ['idwf'] = $token ['idwf'];
            $mywf ['data'] ['case'] = $token ['case'];
            // ---update the token so its finished
            $wf = bindArrayToObject($mywf ['data']);
            $shape = $this->bpm->get_shape($token ['resourceId'], $wf);
            // ----trigger has occured then move to next shape
            $this->bpm->movenext($shape, $wf);
            // ---now run the whole process
            if ($debug)
                echo "Run:" . $token ['idwf'] . ':case:' . $token ['case'] . "<br/>";
            $this->Run('model', $token ['idwf'], $token ['case']);
            // ---TODO write some logging about signals
        }
    }

    function do_triggers() {
        $debug = (isset($this->debug [__FUNCTION__])) ? $this->debug [__FUNCTION__] : false;
        if ($debug)
            echo '<h2>' . __FUNCTION__ . '</h2>';
        // $type=array('$regex'=>$filter_status);
        // $status=array('$regex'=>'^wa*');
        $i = 0;
        $open = $this->bpm->get_triggers();
        $i ++;
        if ($debug)
            echo "TOKENS";
        foreach ($open as $token) {
            if ($debug) {
                var_dump($token);
                echo '<hr/>';
            }
            $mywf = $this->bpm->load($token ['idwf'], true);
            $mywf ['data'] ['idwf'] = $token ['idwf'];
            $mywf ['data'] ['case'] = $token ['case'];
            $wf = bindArrayToObject($mywf ['data']);
            $shape = $this->bpm->get_shape($token ['resourceId'], $wf);
            // ----trigger has occured then move to next shape
            $this->bpm->movenext($shape, $wf);
            // ---now run the whole process
            $this->Run('model', $token ['idwf'], $token ['case']);
            // ---TODO write some logging about triggers
        }
    }

    function do_subproc($idcase = null) {
        $debug = (isset($this->debug [__FUNCTION__])) ? $this->debug [__FUNCTION__] : false;
        if ($debug)
            echo '<h2>' . __FUNCTION__ . '</h2>';
        // $type=array('$regex'=>$filter_status);
        // $status=array('$regex'=>'^wa*');
        $i = 0;
        $open = get_tokens($idwf, $idcase, $status = 'waiting', 'CollapsedSubprocess');
        $i ++;
        if ($debug)
            echo "TOKENS";
        foreach ($open as $token) {
            if ($debug) {
                var_dump($token);
                echo '<hr/>';
            }
            // ---Get childs
            //
			// ---now run child processes
            if (isset($token ['child'])) {
                foreach ($token['child'] as $child_idwf => $childs) {
                    foreach ($childs as $child_idcase) {
                        $this->Run('model', $child_idwf, $child_idcase);
                    }
                }
            }
            // ---TODO write some logging about triggers
        }
    }

    function bindArrayToObject($array) {
        $return = new stdClass ();

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
            if (((isset($index) and ($it->key() == $index)) or (!isset($index))) and ($it->current() == $needle)) {
                // return $aIt->key();
                echo "****  FOUND ****";
                return (array) $it->getSubIterator();
            }

            $it->next();
        }

        return false;
    }

    /*
     * This is for url calls, execute this resourceId specifically
     */

    function do_pending($model, $idwf, $idcase, $run_resourceId = null) {
        // ---load $wf for url calls: /bpm/engine/get_pending/model/$idwf/$idwcase
        if (!$this->wf) {
            $mywf = $this->bpm->load($idwf, $this->expandSubProcess);
            $mywf ['data'] ['idwf'] = $idwf;
            $mywf ['data'] ['case'] = $idcase;
            $mywf ['data'] ['folder'] = $mywf ['folder'];
            $wf = bindArrayToObject($mywf ['data']);
            // ----make it publicly available to other methods
            $this->wf = $wf;
            $this->get_pending($model, $idwf, $idcase, $run_resourceId);
        }
    }

    function get_pending($model, $idwf, $idcase, $run_resourceId = null) {
        $debug = (isset($this->debug [__FUNCTION__])) ? $this->debug [__FUNCTION__] : false;
        // if no filter passed then set default to me
        // $debug = true;
        if($debug) echo"<h1>get_pending($model, $idwf, $idcase,$run_resourceId);";
        // $this->load_data($idwf, $idcase);
        if ($this->break_on_next) {
            $this->bpm->update_case_token_status($idwf, $idcase);
            redirect($this->base_url . $this->config->item('default_controller'));
        }
        $renderData = array();
        $renderData = $this->lang->language;
        $renderData ['theme'] = $this->config->item('theme');
        $renderData ['base_url'] = $this->base_url;
        $renderData ['idwf'] = $idwf;
        $renderData ['case'] = $idcase;
        $user = $this->user->getuser($this->idu);
        $filter=array();

        // ----if specific token has been passed then run this token
        if ($run_resourceId) {
            $filter ['resourceId'] = $run_resourceId;
        }
        // ----Load Case
        $case = $this->bpm->get_case($idcase, $idwf);
        if (isset($case ['parent'])) {
            $has_parent =true;
        }
        // ----set manual flag 4 test
        $run_manual = (isset($case ['run_manual'])) ? $case ['run_manual'] : false;
        // var_dump('case',$case,'run_manual',$run_manual);
        if ($case ['status'] == 'open') {
            // ----load WF data
            $myTasks = $this->bpm->get_pending(
                $idwf,
                $idcase, array('user','manual'),//---status
                    $filter);
            // foreach($myTasks as $task){
            //     echo $task['title'].'<br/>';
            //     echo '<hr/>';

            // }
            // var_dump(json_encode($filter),$myTasks);exit;
            // ---search for a suitable task to execute
            while ($first = array_shift($myTasks)) {
                $is_allowed = $this->bpm->is_allowed($first, $user);

                if ($is_allowed)
                    break;
            }
            if ($first) {
                // -----get id from token---------
                $token = $first;

                // var_dump('loaded token', $token);exit;
                switch ($token ['type']) {
                    case 'Exclusive_Databased_Gateway' :
                        $this->manual_gate($model, $idwf, $idcase, $first ['resourceId']);
                        break;
                    case 'Task' :
                        $id = null;
                        $shape = $this->bpm->get_shape($first ['resourceId'], $this->wf);
                        if ($shape and property_exists($shape->properties, 'operationref')) {
                            if ($shape->properties->operationref) {
                                $opRef = $shape->properties->operationref;
                                // --check if storage $opRf exists
                                if (property_exists($this->data, $opRef)) {
                                    if ($debug)
                                        var_dump('data by opRef:' . $opRef, $this->data->$opRef);
                                    $stored_data = $this->data->$opRef;
                                    $id = (isset($stored_data ['id'])) ? $stored_data ['id'] : 'new';
                                }
                            }
                        }

                        if ($id == '') {
                            // ---try to assign id from token data passed
                            if (isset($token ['data'])) {
                                if (isset($token ['data'] ['id'])) {
                                    $id = $token ['data'] ['id'];
                                } else {
                                    $id = null;
                                }
                            }
                        }
                        // -------------------------------------
                        // ---save lock status
                        $token ['lockedBy'] = (isset($token ['lockedBy'])) ? $token ['lockedBy'] : $this->idu;
                        $token ['lockedDate'] = (isset($token ['lockedDate'])) ? $token ['lockedDate'] : date('Y-m-d H:i:s');

                        $this->bpm->save_token($token);

                        if ($token ['lockedBy'] == $this->idu) {
                            // ----route each typo to it's action
                            switch ($shape->properties->tasktype) {

                                case 'User' :
                                    if (property_exists($shape->properties, 'rendering') and !$run_manual) {
                                        $rendering = trim($shape->properties->rendering);
                                        if ($rendering) {
                                            $token_id = $first ['_id'];
                                            if (strstr($rendering, '$')) {
                                                $streval = 'return ' . $rendering . ';';
                                                $rendering = eval($streval);
                                            }
                                            if (strstr($rendering, 'http')) {
                                                $querystr = array_filter(array(
                                                    'id' => $id,
                                                    'idwf' => $idwf,
                                                    'token' => $token_id,
                                                    'case' => $token ['case']
                                                ));
                                                $q = '';
                                                foreach ($querystr as $key => $value)
                                                    $q .= '&' . $key . '=' . $value;
                                                // ----go to the url via gateway controller
                                                $redir = $this->bpm->gateway($rendering . $q);
                                            } else {
                                                $redir = $this->base_url . $shape->properties->rendering . "/$idwf/$idcase/$token_id";
                                            }
                                            if (!$debug)
                                                header("Location:" . $redir);
                                            else
                                                echo "<a href='" . $redir . "'>" . $this->base_url . $redir . "</a>";
                                        } else {
                                            // ----if has no rendering directive then call manual
                                            if ($debug) {
                                                echo "has no rendering directive then call manual<br>";
                                            }
                                            $this->manual_task($model, $idwf, $idcase, $first ['resourceId']);
                                        }
                                    } else {
                                        if ($debug) {
                                            echo "Manual directive set<br>";
                                        }
                                        // ----if has no rendering directive then call manual
                                        $this->manual_task($model, $idwf, $idcase, $first ['resourceId']);
                                    }
                                    break;

                                default :
                                    if ($debug) {
                                        echo "Task has no type then call manual<br>";
                                    }
                                    $this->manual_task($model, $idwf, $idcase, $first ['resourceId']);
                                    break;
                            }
                        } else { // --the token is locked by other user
                            // ---load no pending taks
                            $renderData ['name'] = $this->lang->line('message');
                            $user_lock = (array) $this->user->get_user($token ['lockedBy']);
                            $msg_data = array(
                                'user_lock' => $user_lock ['name'] . ' ' . $user_lock ['lastname'],
                                'time' => date($this->lang->line('dateTimeFmt'), strtotime($token ['lockedDate']))
                            );
                            $this->show_modal($this->lang->line('message'), $this->parser->parse_string($this->lang->line('taskLocked'), $msg_data));
                        }
                        break;
                } // --end switch token type
            } else {
                // ---load no pending taks
                $renderData ['name'] = $this->lang->line('message');
                $renderData ['documentation'] = $this->parser->parse_string($this->lang->line('noMoreTasks'), $case);
			    if(!$this->inproc){ 
                    $this->show_modal($this->lang->line('message'), $this->parser->parse_string($this->lang->line('noMoreTasks'), $case));
			    
			     } else {
			     //---get pending parent
                 $idwfp=$case['data']['parent']['idwf'];
                $idcasep=$case['data']['parent']['case'];
			     $this->run_filter =  array(
                'idwf' => $idwfp,
                'case' => $idcasep,
                //---exclude 'waiting'
                'status' => array('$in'=>array('pending')
                ));
			         $this->inproc=false;
			         $this->run($model,$idwfp,$idcasep);
			     }
			
            }
        } else { // case is closed or in other state
            $this->show_modal($this->lang->line('message'), $this->lang->line('caseClosed'));
        }
    }

    function show_modal($name, $text,$exit=true) {
        $debug = (isset($this->debug [__FUNCTION__])) ? $this->debug [__FUNCTION__] : false;
        if ($debug) {
            echo '<h1>' . __FUNCTION__ . '</h1>';
            echo "<h3>$name</h3>";
            echo "<span>$text</span>";
            return;
        }
        $this->load->library('ui');
        $renderData ['base_url'] = $this->base_url;
        $renderData ['name'] = $name;
        $renderData ['text'] = $text;
        $renderData ['title'] = $name;
        // ---prepare UI
        $renderData ['js'] = array(
            $this->module_url . 'assets/jscript/modal_window.js' => 'Modal Window Generic JS'
        );
        // ---prepare globals 4 js
        $renderData ['global_js'] = array(
            'base_url' => $this->base_url,
            'module_url' => $this->module_url
        );
        $this->ui->compose('bpm/modal_msg', 'bpm/bootstrap.ui.php', $renderData);
        $this->output->_display();
        if($exit) exit();
    }

}
