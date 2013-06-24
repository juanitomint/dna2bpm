<?php

class Application extends MX_Controller {

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
        $this->idu = $this->session->userdata('iduser');
        $this->user->authorize('USE,ADM,SUP');
        $this->load->helper('dbframe');

        //----LOAD LANGUAGE
        $this->lang->load('library', $this->config->item('language'));
        $this->idu = (int) $this->session->userdata('iduser');
        $this->types_path = 'application/modules/application/assets/types/';
        $this->module_path = 'application/modules/application/';
        //----Variables
        $this->base_url = base_url();
        $this->module_url = base_url() . 'application/';
    }

    function addQuote($st) {
        return "'" . $st . "'";
    }

    function Apps($action) {
        $segments = $this->uri->segment_array();
        $debug = (in_array('debug', $segments)) ? true : false;
        $apps = array();
        $custom = '';
        $types_path = $this->types_path;
        //var_dump($_POST);
        $out = array();
//        $form = $this->app->get_object($idapp);



        if (isset($action)) {
            switch ($action) {
                //----start READ--------------
                case 'read':
                    $dbapps = $this->app->get_apps();
                    include($types_path . 'base/app.base.php');
                    foreach ($dbapps as $obj) {
                        unset($obj['_id']);
                        unset($obj['objs']);
                        $apps['rows'][] = $obj;
                    }
                    $apps['totalcount'] = count($apps['rows']);
                    $out = $apps;
                    break;
                //---Start CREATE
                case 'update':
                    $input = json_decode(file_get_contents('php://input'));
                    //---defines $common
                    include($types_path . 'base/form.base.php');
                    foreach ($input as $thisform) {
                        $thisform = (array) $thisform;
                        //$form= new dbframe($thisform, $common);
                        $this->app->put_form_data($thisform['idform'], $thisform);
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
                    include($types_path . 'base/form.base.php');
                    $thisForm = $_POST;
                    //---Create new id for generated form
                    $thisForm['idform'] = $this->app->gen_inc('forms', 'idform');
                    //---Set idobj with propper string id ie: V1317 , D59 etc.
                    $thisForm['idobj'] = $thisForm['type'] . $thisForm['idform'];
                    $app = new dbframe($thisForm, $common);
                    //---save the new object
                    $this->app->put_object($app->toSave());
                    //---add the form to the app
                    $this->app->add_object($idapp, $thisForm['idobj']);

                    $out = array('success' => true, 'idform' => $app->idform, 'idobj' => $app->idobj);
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
            show_error("Need to be called with some action: read, create etc.");
        }
    }

    function Browser() {
        $this->load->library('ui');
        $level = $this->user->getlevel($this->idu);
        $cpData = $this->lang->language;
        $segments = $this->uri->segment_array();
        //var_dump($level);
        $cpData['theme'] = $this->config->item('theme');
        $cpData['level'] = $level;
        $cpData['base_url'] = $this->base_url;
        $cpData['module_url'] = $this->module_url;
        $cpData['title'] = 'Application Browser';

        $cpData['js'] = array(
            $this->module_url . 'assets/jscript/fontawesome_icons.js' => 'FontAwesome icons',
            $this->module_url . 'assets/jscript/browser/ext.data.js' => 'data Components',
            $this->module_url . 'assets/jscript/browser/ext.load_props.js' => 'Apps Porperty loader',
            $this->module_url . 'assets/jscript/browser/ext.baseProperties.js' => 'Property Grid',
            $this->module_url . 'assets/jscript/browser/ext.group_selector.js' => 'Group Selector',
            $this->module_url . 'assets/jscript/browser/ext.grid.js' => 'Grid',
            $this->module_url . 'assets/jscript/browser/ext.viewport.js' => 'viewport',
        );

        $cpData['global_js'] = array(
            'base_url' => $this->base_url,
            'module_url' => $this->module_url,
        );

        $this->ui->makeui('ext.ui.php', $cpData);
    }

    function code() {
        $segments = $this->uri->segment_array();
        $action = $this->input->post('action');
        $id = $this->input->post('id');
        $context = $this->input->post('context');
        $code = $this->input->post('code');
        $lang = $this->input->post('lang');
        $debug = (in_array('debug', $segments)) ? true : false;
        $template['PHP'] = "<?php\n/* new PHP script Write your code here */\n";
        $template['JS'] = "// new JS script Write your code here\n";
        $rtn = array(
            'action' => $action,
            'id' => $id,
            'lang' => $lang
        );
        if ($action == 'save') {
            $rtn = $this->app->put_code($id, $context, $lang, $code);
            //----SAVE
        } else {
            //----LOAD
            $rtn = $this->app->get_code($id, $context, $lang);
            $rtn['ok'] = (int) 1;
            $rtn['code'] = isset($rtn['code']) ? $rtn['code'] : $template[$lang];
        }

        if (!$debug) {
            header('Content-type: application/json;charset=UTF-8');
            echo json_encode($rtn);
        } else {
            var_dump($rtn);
        }
    }

    function Editor($idapp = null) {
        $this->load->library('ui');
        $level = $this->user->getlevel($this->idu);
        $cpData = $this->lang->language;
        $segments = $this->uri->segment_array();
        //var_dump($level);
        $cpData['theme'] = $this->config->item('theme');
        $cpData['level'] = $level;
        $cpData['base_url'] = $this->base_url;
        $cpData['module_url'] = $this->module_url;
        $cpData['idapp'] = $idapp;
        $cpData['title'] = 'Application Editor';

        $cpData['js'] = array(
            $this->module_url . 'assets/jscript/fontawesome_icons.js' => 'FontAwesome icons',
            $this->module_url . 'assets/jscript/editor/ext.data.js' => 'data Components',
            $this->module_url . 'assets/jscript/editor/ext.grid.js' => 'Grid',
            $this->base_url . 'jscript/editarea/edit_area/edit_area_full.js' => 'Edit Area',
            $this->module_url . 'assets/jscript/ext.code_editor.js' => 'Code Editor',
            $this->module_url . 'assets/jscript/editor/ext.load_props.js' => 'Form Porperty loader',
            $this->module_url . 'assets/jscript/editor/ext.viewport.js' => 'viewport',
            $this->module_url . 'assets/jscript/editor/ext.baseProperties.js' => 'Property Grid',
            $this->base_url . "jscript/jquery/jquery.min.js" => 'JQuery',
            $this->base_url . "jscript/bootstrap/js/bootstrap.min.js" => 'Bootstrap JS',
        );

        $cpData['global_js'] = array(
            'base_url' => $this->base_url,
            'module_url' => $this->module_url,
            'idapp' => $idapp,
        );

        $this->ui->makeui('ext.ui.php', $cpData);
    }

    function Index() {
        
    }

    function Forms($action, $idapp) {
        $segments = $this->uri->segment_array();
        $debug = (in_array('debug', $segments)) ? true : false;
        $forms = array();
        $custom = '';
        $types_path = $this->types_path;
        //var_dump($_POST);
        $out = array();
//        $form = $this->app->get_object($idapp);



        if (isset($idapp)) {
            switch ($action) {
                //----start READ--------------
                case 'read':
                    $this->load->model('bpm/bpm');
                    $app = $this->app->get_app($idapp);
                    if (isset($app['objs'])) {
                        $forms['totalcount'] = count($app['objs']);
                        include($types_path . 'base/form.base.php');

                        foreach ($app['objs'] as $obj) {
                            switch ($obj['idobj'][0]) {
                                //-----get models from wf table?
                                case 'M':
                                    $idbpm = substr($obj['idobj'], 1);
                                    $thisModel = $this->bpm->get_model($idbpm);
                                    $form = new dbframe($thisModel, $common);
                                    $form->title = $thisModel->data['properties']['name'];
                                    $form->idobj = 'M' . $thisModel->idwf;
                                    $form->type = 'M';
                                    $form->idform = $idbpm;
                                    $form->hidden = $obj['hidden'];
                                    $form->locked = $obj['locked'];

                                    break;
                                default:
                                    $thisobj = $this->app->get_object($obj['idobj']);
                                    $form = new dbframe($thisobj, $common);
                                    //---add the object to the array if exists
                                    break;
                            }
                            if ($form->idform) {
                                $forms['rows'][] = $form->toShow();
                            }
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
                    $app = $this->app->get_app($idapp);
                    $app['objs'] = array();
                    include($types_path . 'base/form.base.php');
                    foreach ($input as $thisform) {
                        $thisform = (array) $thisform;
                        $app['objs'][] = array(
                            'idobj' => $thisform['idobj'],
                            'locked' => $thisform['locked'],
                            'hidden' => $thisform['hidden'],
                            'idu' => $this->idu
                        );
                    }
                    $this->app->put_app($idapp, $app);
                    $out = array('status' => 'ok');
                    break;

                case 'create':
                    include($types_path . 'base/form.base.php');
                    $thisForm = $_POST;
                    //---Create new id for generated form
                    $thisForm['idform'] = $this->app->gen_inc('forms', 'idform');
                    //---Set idobj with propper string id ie: V1317 , D59 etc.
                    $thisForm['idobj'] = $thisForm['type'] . $thisForm['idform'];
                    $form = new dbframe($thisForm, $common);
                    //---save the new object
                    $this->app->put_object($form->toSave());
                    //---add the form to the app
                    $this->app->add_object($idapp, $thisForm['idobj']);

                    $out = array('success' => true, 'idform' => $form->idform, 'idobj' => $form->idobj);
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

    function Get_form_properties($idform) {
        $segments = $this->uri->segment_array();
        $debug = (in_array('debug', $segments)) ? true : false;
        //$debug=true;
        $cpData = array();
        $cpData = $this->lang->language;
        $thisForm = array();
        $custom = '';
        $types_path = $this->types_path;
        if (isset($idform)) {
            $thisForm = $this->app->get_object($idform);
        }
        $type = $thisForm['type'];

        //---load base properties from helpers/types/base
        //---defines $common
        include($types_path . 'base/form.base.php');
        //---load custom properties from specific type
        $type_props = array();
        $file_custom = $types_path . $type . '/properties.php';
        if (is_file($file_custom)) {
            if ($debug)
                echo "Loaded Custom:$file_custom<br/>";
            include($file_custom);
        }

        //---now define the properties template
        $properties_template = $common + $type_props;
        $form = new dbframe($thisForm, $properties_template);

        if (!$debug) {
            header('Content-type: application/json;charset=UTF-8');
            echo json_encode($form->toShow());
        } else {
            var_dump('Obj', $form, 'Save:', $form->toSave(), 'Show', $form->toShow());
        }
    }

    function Get_app_template($type = 'base') {
        $tdata = array();
        //---4 safety
        if ($type == 'base')
            $type = '';
        //----------------------------------------------------------------------
        //---Load Custom Properties---------------------------------------------
        //----------------------------------------------------------------------
        $file = $this->module_path . "assets/types/browser/$type/ext.propertyGrid.js";
        if (is_file($file)) {
            $customProps = read_file($file);
            //$customProps = $this->parser->parse(str_replace('.php', '', $file), $tdata,true);
        } else {
            $customProps = '';
        }
        //----------------------------------------------------------------------
        //---Load Base Properties
        //----------------------------------------------------------------------

        $file = $this->module_path . "assets/jscript/browser/ext.baseProperties.js";
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

    function Get_form_template($type) {
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

        $file = $this->module_path . "assets/jscript/editor/ext.baseProperties.js";
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

    /*
     * Return Entities available on the system
     */

    function Get_entities() {
        $segments = $this->uri->segment_array();
        $debug = (in_array('debug', $segments)) ? true : false;
        $rtn = array();
        $get_entites = $this->app->get_entities();
        $rtn['totalcount'] = $get_entites->count();
        foreach ($get_entites as $thisent) {
            $rtn['entities'][] = array(
                'ident' => $thisent['ident'],
                'name' => $thisent['name'],
            );
        }
        if (!$debug) {
            header('Content-type: application/json;charset=UTF-8');
            echo json_encode($rtn);
        } else {
            var_dump($rtn);
        }
    }

    function Layout($idobj = null) {

        $level = $this->user->getlevel($this->idu);
        $cpData = $this->lang->language;
        $segments = $this->uri->segment_array();
        //var_dump($level);
        $cpData['theme'] = $this->config->item('theme');
        $cpData['level'] = $level;
        $cpData['base_url'] = $this->base_url;
        $cpData['module_url'] = $this->module_url;
        $cpData['idobj'] = $idobj;
        $this->parser->parse('form/ext.layouter.php', $cpData);
    }

    function Get_frame_tags($idframe) {
        //----search hooks 4 make frame tags
        $path = 'system/application/process/$idframe*/';
        $hooks = glob($path);
        //
        $hooks = $this->backend->get_frame_hooks($idframe);
        $tags = "<span class='hasPHP'>2doPHP</span>  <span class='hasJS'>2doJS</span>";
        //var_dump($hooks);
        foreach ($hooks as $thisHook) {
            $context = $thisHook['context'];
            switch ($thisHook['type']) {
                //---------Check PHP
                case 'php':
                    $tags[] = "<span class='hasPHP'>$context</span>";
                    break;
                ///-----now with JS ---------
                case 'js':
                    $tags[] = "<span class='hasJS'>$context</span>";
                    break;
            }//---end switch $exten
        }
        return $tags;
    }

    function Get_all_objs() {
        $this->load->model('bpm/bpm');
        $segments = $this->uri->segment_array();
        $debug = (in_array('debug', $segments)) ? true : false;
        $forms = array();
        $custom = '';
        $types_path = $this->types_path;
        $objs = $this->app->get_objects();
        include($types_path . 'base/form.base.php');

        foreach ($objs as $obj) {
            //var_dump($obj);
            $form = new dbframe($obj, $common);
            //---add the object to the array if exists
            if (isset($obj['idform'])) {
                //$thisFrom=new dbframe($thisobj)
                $forms['rows'][] = $form->toShow();
            } else {
                var_dump($obj);
            }
        }
        /*
         * Add models as objects type=M
         */
        $models = $this->bpm->get_models();
        foreach ($models as $thisModel) {
            $model = new dbframe($thisModel, $common);
            $model->title = $thisModel->data['properties']['name'];
            $model->idobj = 'M' . $thisModel->idwf;
            $model->type = 'M';
            $model->idform = $thisModel->idwf;
            $forms['rows'][] = $model->toShow();
        }
        $forms['totalcount'] = count($forms['rows']);
        if (!$debug) {
            header('Content-type: application/json;charset=UTF-8');
            echo json_encode($forms);
        } else {
            var_dump($forms);
        }
    }

    function Get_all_frames($idobj) {
        $segments = $this->uri->segment_array();
        $debug = (in_array('debug', $segments)) ? true : false;
        $frames = array();
        $custom = '';
        $types_path = $this->types_path;

        if (isset($idobj)) {
            $form = $this->app->get_object($idobj);
            $entity = $form['ident'];
            $allForms = $this->backend->getFormsByEntity($entity);
            $allFrames = array();
            $frames['totalcount'] = 0;
            ///---fetch all frames available from all forms
            //---load base properties from helpers/types/base
            include($types_path . 'base/base.php');
            $properties_template = $common;
            foreach ($allForms as $form) {
                $form_frames = $this->app->get_form_frames($form);
                $frames['totalcount'] += count($form['frames']);
                foreach ($form_frames as $frame_data) {
                    $frame = new dbframe($frame_data, $properties_template);
                    $idframe = $frame->get('idframe');
                    $frames['rows'][] = array(
                        'idframe' => $idframe,
                        'title' => $frame->get('title'),
                        'createdby' => 'TODO',
                        'type' => $frame->get('type'),
                        'group' => $form['idobj'] . ' :: ' . $form['title']
                    );
                }
            }
            if (!$debug) {
                header('Content-type: application/json;charset=UTF-8');
                echo json_encode($frames);
            } else {
                var_dump($frames);
            }
        } else {
            show_error("Need to have idobj to get.");
        }
    }

    function frames($idobj, $action) {
        $segments = $this->uri->segment_array();
        $debug = (in_array('debug', $segments)) ? true : false;
        $frames = array();
        $custom = '';
        $types_path = $this->types_path;
        //var_dump($_POST);
        $out = array();
        $form = $this->app->get_object($idobj);



        if (isset($idobj)) {
            switch ($action) {
                //----start READ--------------
                case 'read':
                    $form_frames = $this->app->get_form_frames($form);
                    $frames['totalcount'] = count($form['frames']);
                    foreach ($form_frames as $frame_data) {
                        if (count($frame_data)) {
                            $frame = new dbframe();
                            $frame->load($frame_data);
                            $idframe = $frame->get('idframe');
                            $frames['rows'][] = array(
                                'idframe' => $idframe,
                                'tags' => $this->Get_frame_tags($idframe),
                                'title' => $frame->get('title'),
                                'createdby' => 'TODO',
                                'type' => $frame->get('type')
                            );
                        }
                    }
                    $out = $frames;
                    break;
                //---Start CREATE
                case 'create':
                    $input = json_decode(file_get_contents('php://input'));
                    foreach ($input as $thisframe) {
                        $frames[] = $thisframe->idframe;
                    }
                    $form['frames'] = array_unique($frames);
                    $form = $this->app->put_object($form);
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

    function Save_properties($idobj) {
        $segments = $this->uri->segment_array();
        $debug = (in_array('debug', $segments)) ? true : false;
        $types_path = $this->types_path;
        $form = $this->app->get_object($idobj);
        $postframe = $this->app->normalize_frame($_POST);


        $idframe = $postframe['idframe'];
        $type = $postframe['type'];
        //---define view dependant properties
        //---these properties can change from view to view and are added to view extra data
        $extra = array(
            'hidden',
            'locked',
            'required',
            'removed'
        );
        //---load properties template in order to set propper types
        //---load base properties from helpers/types/base
        include($types_path . 'base/base.php');

        //---load custom properties from specific type
        $type_props = array();
        $file_custom = $types_path . $type . '/properties.php';
        if (is_file($file_custom)) {
            include($file_custom);
        }
        $properties_template = $common + $type_props;
        //----create empty frame according to the template
        $frame = new dbframe(array(), $properties_template);
        //----load the data from post
        $frame->loadPostdata($postframe);

        if ($idframe) {
            //---wht 2 do? uh? ...nothing?
        } else {
            //---create new ID for the frame
            $frame->idframe = (int) $this->app->gen_inc('frames', 'idframe');
            $frame->cname = 'C' . $frame->idframe;
        }

//---process input according to type specific rules
        $types_path = $this->types_path;
        $file_custom = $types_path . $frame->type . '/properties.proc.php';
        if (is_file($file_custom)) {
            include($file_custom);
        }
        //---choose wht 2 do
        switch ($form['type']) {
            case "D":
                $this->app->put_frame($frame->idframe, $frame->toSave());
                break;
            default:
                //--4 test just save the object
                //---remove view setable properties: visible,required,locked
                $saveData = $frame->toSave();
                //---split data for view and for frame
                foreach ($extra as $thisprop) {
                    $frameExtra[$thisprop] = $frame->get($thisprop);
                    unset($saveData->$thisprop);
                }
                $this->app->put_frame_extra($idobj, $frame->idframe, $frameExtra);
                $this->app->put_frame($frame->idframe, $saveData);
                break;
        }
        $this->get_properties($frame->type, $idobj, $frame->idframe);
//        if (!$debug) {
//            header('Content-type: application/json;charset=UTF-8');
//            echo json_encode($frame->toArray());
//        } else {
//            var_dump($frame);
//        }
    }

    function Get_option() {
        $segments = $this->uri->segment_array();
        $debug = (in_array('debug', $segments)) ? true : false;
        //---get idop from POST data
        $idop = ($this->input->post('idop')) ? $this->input->post('idop') : -1;
        $rtn = array();
        $options = $this->app->get_ops($idop);
        $rtn['totalcount'] = count($options);
        foreach ($options as $value => $text) {
            $rtn['rows'][] = array(
                'value' => $value,
                'text' => $text,
            );
        }
        if (!$debug) {
            header('Content-type: application/json;charset=UTF-8');
            echo json_encode($rtn);
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
            header('Content-type: application/json;charset=UTF-8');
            echo json_encode($rtn);
        } else {
            var_dump($rtn);
        }
    }

    function Get_properties($type, $idform = null, $idframe = null) {
        $segments = $this->uri->segment_array();
        $debug = (in_array('debug', $segments)) ? true : false;
        $cpData = array();
        $cpData = $this->lang->language;
        $form = array();
        $custom = '';
        $types_path = $this->types_path;
        if (isset($idform)) {
            $form = $this->app->get_object($idform);
        }
        $frame = ($idframe) ? $this->app->get_form_frame($form, $idframe) : array('type' => $type);
        //---load base properties from helpers/types/base
        include($types_path . 'base/base.php');

        //---load custom properties from specific type
        $type_props = array();
        $file_custom = $types_path . $type . '/properties.php';
        if (is_file($file_custom)) {
            include($file_custom);
        }
        //---now define the properties template
        $properties_template = $common + $type_props;
        $frame = new dbframe($frame, $properties_template);


        if (!$debug) {
            header('Content-type: application/json;charset=UTF-8');
            echo json_encode($frame->toShow());
        } else {
            var_dump('Obj', $frame, 'Save:', $frame->toSave(), 'Show', $frame->toShow());
        }
    }

    function Get_app_properties($idapp) {
        $segments = $this->uri->segment_array();
        $debug = (in_array('debug', $segments)) ? true : false;
        $cpData = array();
        $cpData = $this->lang->language;
        $app = array();
        $custom = '';
        $types_path = $this->types_path;
        $dbapp = $this->app->get_app($idapp);
        //--convert groups to string
        $type = isset($dbapp['type']) ? $dbapp['type'] : null;
        //---load base properties from helpers/types/base
        include($types_path . 'base/app.base.php');

        //---load custom properties from specific type
        $type_props = array();
        $file_custom = $types_path . 'app/' . $type . '/properties.php';
        if (is_file($file_custom)) {
            include($file_custom);
        }
        //---now define the properties template
        $properties_template = $common + $type_props;
        $app = new dbframe($app, $properties_template);
        $app->load($dbapp);
        $app->groups = implode(',', $app->groups);
        $app->template['groups'] = 'string';
        if (!$debug) {
            header('Content-type: application/json;charset=UTF-8');
            echo json_encode($app->toShow());
        } else {
            var_dump('Obj', $app, 'Save:', $app->toSave(), 'Show', $app->toShow());
        }
    }

    function Save_frame($idform, $idframe = null) {

        $frame = $this->input->post('frame');

        if ($idframe) {
            $frame['idframe'] = (int) $idframe;
        } else {
            $idframe = (int) $this->app->gen_inc('frames', 'idframe');
            $frame['idframe'] = $idframe;
            $frame['cname'] = 'C' . $frame['idframe'];
        }
        $this->app->put_frame($idframe, $frame);
        header('Content-type: application/json;charset=UTF-8');
        echo json_encode($frame);
    }

    function Save_column($idform) {

        $column = $this->input->post('column');
        $frames = (array) $this->input->post('col');
        $count = count($frames);
        $criteria = array('idobj' => $idform);
        $newobj = array('$set' => array('frames' => $frames));
        $options = array('fsync' => true, 'upsert' => true);
        $result = $this->mongo->db->forms->update($criteria, $newobj, $options);
        echo "
        <p>
		<span class='ui-icon ui-icon-circle-check' style='float:left;margin:0 7px 0px 0;'>
                </span>
		Column:$column > $count Frames<br/> saved OK!
	</p>

";
        var_dump($criteria, $newobj, $result);
    }

    function Default_picker($idop) {
        $this->load->helper('dna');
        $level = $this->user->getlevel($this->idu);
        $cpData = $this->lang->language;
        //var_dump($level);
        $cpData['theme'] = $this->config->item('theme');
        $cpData['level'] = $level;
        $cpData['base_url'] = base_url();
        //$cpData['apps'] = $this->user->getapps();
        //var_dump($option);

        $cpData['items'][] = $option['data'];

        $cpData['idop'] = $idop;
        //var_dump($cpData);

        $this->parser->parse('dna2/wz_default_picker', $cpData);
    }

    function Json_getoption($idop) {
        $option = $this->app->get_option($idop);
        echo json_encode($option);
    }

    function get_prop_template($type) {
        $tdata = array();
        //---4 safety
        if ($type == 'base')
            $type = '';
        //----------------------------------------------------------------------
        //---Load Custom Properties---------------------------------------------
        //----------------------------------------------------------------------
        $file = "system/application/helpers/types/$type/ext.propertyGrid.js";
        if (is_file($file)) {
            $customProps = read_file($file);
            //$customProps = $this->parser->parse(str_replace('.php', '', $file), $tdata,true);
        } else {
            $customProps = '';
        }
        //----------------------------------------------------------------------
        //---Load Base Properties
        //----------------------------------------------------------------------

        $file = $this->module_path . "assets/jscript/editor/ext.baseProperties.js";
        if (is_file($file)) {
            $baseProps = read_file($file);
            //$customProps = $this->parser->parse(str_replace('.php', '', $file), $tdata,true);
        } else {
            show_error("Cant find base properties file: $file<br/>Sorry can't serve");
        }
        //---insert custom props in the base file
        $props = str_replace('//{customProps}', $customProps, $baseProps);
        //----render the code
        echo $props;
    }

    function Get_newcol($col) {
        $cpData['col'] = $col;
        $this->parser->parse('dna2/wz_newcol', $cpData);
    }

    function Load_code($object, $context, $language) {
        $result = $this->app->get_code($object, $context, $language);
        echo $result['code'];
    }

    function Save_code($object, $context, $language) {
        $code = $this->input->post('code');
        $result = $this->app->put_code($object, $context, $language, $code);
        var_dump($result);
        echo date($this->lang->line('dateFmt')) . ":saved OK!\n";
    }

    function Save_form_properties($idobj) {
        $segments = $this->uri->segment_array();
        $debug = (in_array('debug', $segments)) ? true : false;
        $types_path = $this->types_path;

        $postform = $_POST;
        $idform = $postform['idform'];
        $type = $postform['type'];

        //----create empty frame according to the template
        $form = new dbframe();
        //---load base properties from helpers/types/base
        //---defines $common
        include($types_path . 'base/form.base.php');
        //---load custom properties from specific type
        $type_props = array();
        $file_custom = $types_path . $type . '/properties.php';
        if (is_file($file_custom)) {
            if ($debug)
                echo "Loaded Custom:$file_custom<br/>";
            include($file_custom);
        }
        $properties_template = $common + $type_props;
//----load the data from post
        $form->load($postform, $properties_template);

        if ($idform) {
            //---wht 2 do? uh? ...nothing?
            $dbform = $this->app->get_object($idobj);
        } else {
            //---create new ID for the frame
            $form->idform = (int) $this->app->gen_inc('forms', 'idform');
            $form->idobj = $form->type . $form->idform;
            $dbform = array();
        }

        $this->app->put_form($form->idform, $dbform + $form->toSave());
        //---setting id 4 propsGrid
        $form->template['id'] = 'integer';
        $form->id = $form->idform;
        //----dump results
        if (!$debug) {
            header('Content-type: application/json;charset=UTF-8');
            echo json_encode($form->toShow());
        } else {
            var_dump($form->toShow());
        }
    }

    function Save_app_properties($idapp) {
        $this->load->model('user/rbac');
        $segments = $this->uri->segment_array();
        $debug = (in_array('debug', $segments)) ? true : false;
        $types_path = $this->types_path;
        $dbapp = $this->app->get_app($idapp);
        $postform = $_POST;
        //---make groups
        $postform['groups'] = (isset($postform['groups'])) ? explode(',', $postform['groups']) : array();
        $idapp = $postform['idapp'];
        //---uncoment when apps have type
        //                $type = $postform['type'];
        //----create empty frame according to the template
        $app = new dbframe();
        //---load base properties from helpers/types/base
        //---defines $common
        include($types_path . 'base/app.base.php');
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
        $app->load($postform, $properties_template);

        if ($idapp) {
            //---wht 2 do? uh? ...nothing?
            $dbapp = $this->app->get_app($idapp);
        } else {
            //---create new ID for the frame
            $app->idapp = (int) $this->app->gen_inc('apps', 'idapp');
            $dbapp = array();
        }

        $this->app->put_app($app->idapp, $app->toSave() + $dbapp);
        //----register app in RBAC-REPOSIROTY
        $path = 'modules/application/' . $app->idapp;
        $properties = array(
            "source" => "User",
            "checkdate" => date('Y-m-d H:i:s'),
            "idu" => $this->idu
        );
        $this->rbac->put_path($path, $properties);


        $app->groups = implode(',', $app->groups);
        $app->template['id'] = 'integer';
        $app->id = $app->idapp;
        //----dump results
        if (!$debug) {
            header('Content-type: application/json;charset=UTF-8');
            echo json_encode($app->toSave());
        } else {
            var_dump($app->toShow());
        }
    }

}

?>