<?php

# test SVN

class Form extends MX_Controller {

    function Form() {
        parent::__construct();
        //---Libraries
        $this->load->library('parser');
        //----Models
        $this->load->model('user');
        $this->load->model('app');
        $this->load->model('backend');
        //---Helpers
        $this->load->helper('directory');
        $this->load->helper('file');
        $this->idu = $this->session->userdata('iduser');
        $this->user->authorize('USE,ADM,SUP');
        $this->load->helper('dbframe');
        //---base variables
        $this->base_url = base_url();
        $this->module_url = base_url() . $this->router->fetch_module().'/';
        $this->module_path = 'application/modules/form/';
        //----LOAD LANGUAGE
        $this->lang->load('library', $this->config->item('language'));
        $this->idu = (int) $this->session->userdata('iduser');

        $this->types_path = $this->module_path . 'assets/types/';
    }

    function Index() {
        
    }

    function Expose($mode) {
        /*
         * This function will expose menu items to app controllers
         * $mode regulates response in terms of format
         */
        $app = array(
            'title' => 'Form Editor',
            'base' => $this->module_url,
            'name' => 'form'
        );

        $app['menu'] = array(
            'App Browser' => 'app'
        );

        $app['menu/app'] = array(
            'Form Browser' => array(
                'path' => 'Browser/$idapp',
                'icon' => ''
            )
        );

        $app['menu/form'] = array(
            'Editor' => array(
                'path' => 'Editor/$idobj',
                'icon' => ''
            ),
            'Layout' => array(
                'path' => 'Layout/$idobj',
                'icon' => ''
            )
        );
    }

    function addQuote($st) {
        return "'" . $st . "'";
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

    function Layout($idobj = null) {
        $this->load->library('ui');
        $cpData = $this->lang->language;
        $segments = $this->uri->segment_array();
        $cpData['theme'] = $this->config->item('theme');
        $cpData['base_url'] = $this->base_url;
        $cpData['module_url'] = $this->module_url;
        $cpData['idobj'] = $idobj;
        $form = $this->app->get_object($idobj);
        $cpData['title'] = $idobj . '::' . $form['title'];
        $cpData['form'] = $form;

        $cpData['js'] = array(
            $this->module_url . 'assets/jscript/ext.data.js' => 'data Components',
            $this->module_url . 'assets/jscript/ext.components.grid.js' => 'Component Grid',
            $this->module_url . 'assets/jscript/ext.grid.js' => 'Grid',
            $this->module_url . 'assets/jscript/layout/ext.layout-objects.js' => 'Layout objects',
            $this->module_url . 'assets/jscript/layout/ext.viewport.js' => 'viewport',
        );

        $cpData['global_js'] = array(
            'base_url' => $this->base_url,
            'module_url' => $this->module_url,
            'idobj' => $idobj,
        );
        $this->ui->makeui('ext.ui.php', $cpData);
    }

    function Editor($idobj = null) {
        $this->load->library('ui');
        $cpData = $this->lang->language;
        $segments = $this->uri->segment_array();
        $cpData['theme'] = $this->config->item('theme');
        $cpData['base_url'] = $this->base_url;
        $cpData['module_url'] = $this->module_url;
        $cpData['idobj'] = $idobj;
        $form = $this->app->get_object($idobj);
        $cpData['title'] = $idobj . '::' . $form['title'];
        $cpData['form'] = $form;

        $cpData['js'] = array(
            $this->module_url . 'assets/jscript/ext.data.js' => 'data Components',
            $this->module_url . 'assets/jscript/ext.components.grid.js' => 'Component Grid',
            $this->module_url . 'assets/jscript/ext.grid.js' => 'Grid',
            $this->module_url . 'assets/jscript/ext.code_editor.js' => 'Code Editor',
            $this->module_url . 'assets/jscript/ext.load_props.js' => 'Porperty loader',
            $this->module_url . 'assets/jscript/ext.baseProperties.js' => 'Property Grid',
            $this->module_url . 'assets/jscript/ext.viewport.js' => 'viewport',
        );

        $cpData['global_js'] = array(
            'base_url' => $this->base_url,
            'module_url' => $this->module_url,
            'idobj' => $idobj,
        );
        $this->ui->makeui('ext.ui.php', $cpData);
    }

    function Browser($idapp = null) {
        $cpData = $this->lang->language;
        $segments = $this->uri->segment_array();
        //var_dump($level);
        $cpData['theme'] = $this->config->item('theme');
        $cpData['base_url'] = base_url();
        $cpData['idapp'] = $idapp;
        $this->parser->parse('app/ext.editor.php', $cpData);
    }

    function App($idapp) {
        //---Application browser

        $cpData = $this->lang->language;
        $segments = $this->uri->segment_array();
        //var_dump($level);
        $cpData['idapp'] = $idapp;
        $cpData['theme'] = $this->config->item('theme');
        $cpData['base_url'] = $this->base_url;
        $cpData['module_url'] = $this->module_url;
        $this->parser->parse('app/ext.editor.php', $cpData);
    }

    function Forms($action, $idapp) {
        $segments = $this->uri->segment_array();
        $debug = (in_array('debug', $segments)) ? true : false;
        $forms = array();
        $custom = '';
        $types_path = $this->types_path;
        //var_dump($_POST);
        $out = array();
        $form = $this->app->get_object($idapp);


        if (isset($idapp)) {
            switch ($action) {
                //----start READ--------------
                case 'read':
                    $app = $this->app->get_app($idapp);
                    $forms['totalcount'] = count($app['objs']);
                    foreach ($app['objs'] as $obj) {
                        //var_dump($obj);
                        $thisobj = $this->app->get_object($obj['idobj']);
                        //---add the object to the array if exists
                        if ($thisobj['idform']) {
                            //$thisFrom=new dbframe($thisobj)
                            $forms['rows'][] = $thisobj;
                            /* array(
                              'idform' => $thisobj['idform'],
                              'type' => $thisobj['type'],
                              'title' => $thisobj['title'],
                              'idobj' => $thisobj['idobj'],
                              'locked' => $thisobj['locked'],
                              'hidden' => $thisobj['hidden'],
                              'idu' => $thisobj['idu'],
                              ); */
                        }
                    }
                    $out = $forms;
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

    function Frames($action, $idobj) {
        $segments = $this->uri->segment_array();
        $debug = (in_array('debug', $segments)) ? true : false;
        $frames = array();
        $custom = '';
        $types_path = $this->types_path;
        //var_dump($_POST);
        $out = array();
        $form = $this->app->get_object($idobj);
        include($types_path . 'base/base.php');

        if (isset($idobj)) {
            switch ($action) {
                //----start READ--------------
                case 'read':
                    $form_frames = $this->app->get_form_frames($form);
                    $frames['totalcount'] = count($form['frames']);
                    foreach ($form_frames as $postframe) {
                        if (count($postframe)) {
                            $frame = new dbframe();
                            $frame->load($postframe);
                            $idframe = $frame->get('idframe');
                            $frames['rows'][] = array(
                                'idframe' => $idframe,
                                'tags' => $this->Get_frame_tags($idframe),
                                'title' => $frame->get('title'),
                                'createdby' => 'TODO',
                                'type' => $frame->get('type'),
                                'locked' => $frame->get('locked'),
                                'required' => $frame->get('required'),
                                'hidden' => $frame->get('hidden')
                            );
                        }
                    }
                    $out = $frames;
                    break;
                //---Start CREATE
                case 'create' || 'update':

                    $input = json_decode(file_get_contents('php://input'));
                    //var_dump('$input',$input);
                    $frames = array();
                    //---prepare Frames Data
                    foreach ($input as $thisFrame) {
                        //---convert to array
                        $thisFrame = (array) $thisFrame;
                        $idframe = (int) $thisFrame['idframe'];
                        //---remove idframe from data
                        unset($thisFrame['idframe']);

                        $frames[$idframe] = $thisFrame;
                    }
                    $form['frames'] = $frames;
                    $result = $this->app->put_object($form);
                    if ($result) {
                        $out = array('status' => 'ok');
                    } else {
                        $out = array('status' => 'error');
                    }

                    break;
            }
            //----end switch
            if (!$debug) {
                header('Content-type: application/json;charset=UTF-8');
                echo json_encode($out);
            } else {
                var_dump($out, $form);
            }
        } else {
            show_error("Need to have idobj to get.");
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
                if (count($form_frames)) {
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

    function Get_option($idop = -1, $idrel = null) {
        $segments = $this->uri->segment_array();
        $debug = (in_array('debug', $segments)) ? true : false;
        //---get idop from POST data
        if ($this->input->post('idop') <> '')
            $this->input->post('idop');
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

    function Get_properties($type, $idobj = null, $idframe = null) {
        $segments = $this->uri->segment_array();
        $debug = (in_array('debug', $segments)) ? true : false;
        $cpData = array();
        $cpData = $this->lang->language;
        $form = array();
        $custom = '';
        $types_path = $this->types_path;
        if (isset($idobj)) {
            $form = $this->app->get_object($idobj);
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
            header('Content-type: application/json');
            echo json_encode($form->toShow());
        } else {
            var_dump('Obj', $form, 'Save:', $form->toSave(), 'Show', $form->toShow());
        }
    }

    function Save_frame_properties($idobj) {
        $segments = $this->uri->segment_array();
        $debug = (in_array('debug', $segments)) ? true : false;
        $types_path = $this->types_path;
        $postframe = $this->app->normalize_frame($_POST);

        $frame = $this->Save_frame($idobj, $postframe);

        $this->get_properties($frame->type, $idobj, $frame->idframe);
    }

    /*
     * prepare Frame to be saved
     * 
     */

    function Save_frame($idobj, $postframe) {
        //---get form data
        $form = $this->app->get_object($idobj);
        //----safe defined idframe
        $idframe = isset($postframe['idframe']) ? $postframe['idframe'] : null;
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
        include($this->types_path . 'base/base.php');

        //---load custom properties from specific type
        $type_props = array();
        $file_custom = $this->types_path . $type . '/properties.php';
        if (is_file($file_custom)) {
            include($file_custom);
        }
        $properties_template = $common + $type_props;
        //----create empty frame according to the template
        $frame = new dbframe(array(), $properties_template);
        //----load the data from post

        if ($idframe) {
            //---load data from store
            $stored_frame = $this->app->get_frame($idframe);
            $frame->load($stored_frame);
            $frame->loadPostdata($postframe);
        } else {
            //---create new ID for the frame
            $frame->loadPostdata($postframe);
            $frame->idframe = (int) $this->app->gen_inc('frames', 'idframe');
        }
        $frame->cname = 'C' . $frame->idframe;

//---process input according to type specific rules

        $file_custom = $this->types_path . $frame->type . '/properties.proc.php';
        if (is_file($file_custom)) {
            include($file_custom);
        }
        //---choose wht 2 do
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


        return $frame;
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

        $file = $this->module_path . "assets/jscript/ext.baseProperties.js";
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

}

?>