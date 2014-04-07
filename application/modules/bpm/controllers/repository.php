<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Repository extends MX_Controller {

    function __construct() {
        parent::__construct();
        $this->load->library('parser');
        $this->load->model('user');
        $this->load->model('bpm');
        $this->load->helper('bpm');
//---TODO set propper roles 4 access
        $this->user->authorize();
//----LOAD LANGUAGE
        $this->lang->load('library', $this->config->item('language'));
        $this->idu = (int) $this->session->userdata('iduser');
        $this->debug = array();
        $this->base_url = base_url();
        $this->module_url = base_url() . $this->router->fetch_module() . '/';

//---LOAD CORE Functions
        /*
         * this->load->helper('types/text/render');
         * this->load->helper('types/textarea/render');
         * this->load->helper('types/radio/render');
         * this->load->helper('types/combo/render');
         * this->load->helper('types/combodb/render');
         * this->load->helper('types/checklist/render');
         * this->load->helper('types/subform/render');
         * this->load->helper('types/date/render');
         * this->load->helper('types/datetime/render');
         * this->load->helper('dna');
         */
    }

    function save() {
        $data = json_decode($this->input->post('data'));
//---fresh modification date
        $data->properties->modificationdate = date('Y-m-d') . 'T00:00:00';
        $svg = $this->input->post('svg');
//
//---if has name then save it
        if ($data) {
            $idwf = $data->resourceId;
            header('Content-Type:text/plain');
//---check Existing revision.
            $this->bpm->save($idwf, $data, $svg);
        } else {
            show_error('No name defined<br>', 500);
        }
    }

    function save_as() {
        $data = json_decode($this->input->post('data'));
        $svg = $this->input->post('svg');
        $title = "title";
//$idwf = $this->input->post('edit_model_title');
        $idwf = $this->input->post('title');
        $data->resourceId = $idwf;
        $query = array('idwf' => $idwf);
        $mywf = (array) $this->mongo->db->workflow->findOne($query);
        if ($mywf) {
            show_error('Name Already Exists', 404);
        } else {
            $new=$this->add($idwf);
            $this->bpm->save($idwf, $data, $svg);
        }
    }

    function add($new_idwf=null) {
//---check if has post
        if (!($this->input->post('idwf')or $new_idwf)) {
            show_error("Can't access this page directly");
        }
        $idwf =($new_idwf)? $new_idwf:$this->input->post('idwf');
        $folder = $this->input->post('folder');
        $name = ($this->input->post('name')) ? $this->input->post('name') : $this->lang->line('New_Model');
        $user = $this->user->get_user($this->idu);
        $author = $user->name . ' ' . $user->lastname;
        $wf['idwf'] = $idwf;
        $wf['folder'] = $folder;
        $wf['version'] = 0;
        $wf['svg'] = '';
        $wf['data'] = array(
            'stencilset' =>
            array(
#'url' => $this->base_url . 'jscript/bpm-dna2/stencilsets/bpmn2.0/bpmn2.0_2.json',
                'url' => '../../jscript/bpm-dna2/stencilsets/bpmn2.0/bpmn2.0_2.json',
                'namespace' => 'http://b3mn.org/stencilset/bpmn2.0#'
            ),
            'resourceId' => $idwf,
            'properties' => array(
                'name' => $name,
                'documentation' => '',
                'auditing' => '',
                'monitoring' => '',
                'version' => 1,
                'author' => $author,
                'language' => $this->config->item('language'),
                'namespaces' => '',
                'targetnamespace' => 'http://www.omg.org/bpmn20',
                'expressionlanguage' => 'http://www.w3.org/1999/XPath',
                'typelanguage' => 'http://www.w3.org/2001/XMLSchema',
                'creationdate' => date('Y-m-d') . 'T00:00:00',
                'modificationdate' => ''),
            'stencil' => array('id' => 'BPMNDiagram')
        );
        $arr = $this->bpm->save_raw($wf);
        $result['ok'] = (count($arr)) ? false : true;
    }

    function check_model($name) {
        $rs = $this->bpm->get_models(array('idwf' => $name));
        $result['ok'] = ($rs->count()) ? false : true;
        header('Content-type: application/json;charset=UTF-8');
        echo json_encode($result);
    }

    function delete($model) {
        $idwf = $this->input->post('idwf');
        if ($this->input->post('idwf') <> '') {
            $result = $this->bpm->delete($idwf);
        }
    }

    function load($model, $idwf, $mode = '', $debug = false) {
//---decode url string
        $idwf = urldecode($idwf);
        $mywf = $this->bpm->load($idwf);
        if (!$debug)
            header('Content-type: application/json;charset=UTF-8');
        $template = array(
            'resourceId' => $idwf,
            'stencilset' =>
            array(
#'url' => $this->base_url . 'jscript/bpm-dna2/stencilsets/bpmn2.0/bpmn2.0_2.json',
                'url' => '../../jscript/bpm-dna2/stencilsets/bpmn2.0/bpmn2.0_2.json',
                'namespace' => 'http://b3mn.org/stencilset/bpmn2.0#'
            ),
            'stencil' =>
            array(
                'id' => 'BPMNDiagram'
            )
        );
        $data = ($mywf['data']) ? $mywf['data'] : $template;
        if (!$debug)
            echo json_encode($data);
        if ($debug)
            var_dump($data);
    }

    function edit($model = '', $idwf = '') {
        $wfData = $this->lang->language;
//var_dump($level);
        $wfData['theme'] = $this->config->item('theme');
        $wfData['base_url'] = $this->base_url;
        $wfData['module_url'] = $this->module_url;
        $wfData['idwf'] = $idwf;
        header('Content-type: application/xhtml+xml');
        $this->parser->parse('bpm/editor', $wfData);
    }

    function dump($model, $idwf, $mode = '') {
        $wfData['htmltitle'] = 'WF-Manager:' . $idwf;
        $wfData['theme'] = $this->config->item('theme');
        $wfData['base_url'] = $this->base_url;
        $wfData['idwf'] = $idwf;
        $mywf = $this->bpm->load($idwf);
        $wfData['data'] = $mywf['data'];
        ini_set('xdebug.var_display_max_data', 512);
        ini_set('xdebug.var_display_max_depth', -1);

//$this->parser->parse('bpm/json_editor', $wfData);
        header('Content-type: application/json;charset=UTF-8');
        echo json_encode($wfData);
    }

    function thumbnail($idwf, $width, $heigth) {
        $svg = $this->bpm->svg($idwf);
        header("Content-type: image/svg+xml");
        echo '<?xml version="1.0" encoding="iso-8859-1"?>';
        echo '<!DOCTYPE svg PUBLIC "-//W3C//DTD SVG 1.0//EN" "http://www.w3.org/TR/2001/REC-SVG-20010904/DTD/svg10.dtd">';
        /*
         * cho '<svg width="'.$width.'" height="'.$heigth.'" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">';
         */
        echo $svg;
//echo '</svg>';
    }

    function svg($idwf) {
//$svg = $this->bpm->svg($idwf);
//$this->parser->parse('bpm/svg', $svg);

        $mywf = $this->bpm->load($idwf);
        $svg[] = $mywf['svg'];
//var_dump($svg);
        $data['svg'] = str_replace('>', ">\n", $mywf['svg']);
        $data['idwf'] = $idwf;
        header("Content-Type: application/xhtml+xml");
        /* echo '<?xml version="1.0" encoding="iso-8859-1"?>'; */
        echo '<?xml version="1.0" encoding="utf-8"?>';
        $this->parser->parse('bpm/svg', $data);
    }

    function get_shapes($model, $idwf, $debug = false) {
        $data['idwf'] = $idwf;
//---load WF
        $mywf = $this->bpm->load($idwf, true);
        $mywf['data']['idwf'] = $idwf;
        $wf = bindArrayToObject($mywf['data']);
//---------------------------------------
        $shapes = $this->bpm->get_all_shapes($wf);
        foreach ($shapes as $shape) {
            $data['shapes'][$shape->resourceId] = $shape;
        }
        if (!$debug) {
            header('Content-type: application/json;charset=UTF-8');
            echo json_encode($data);
        } else {
            var_dump($data);
        }
    }

    function view($model, $idwf) {
        $debug = (isset($this->debug[__FUNCTION__])) ? $this->debug[__FUNCTION__] : false;
        if ($debug)
            echo '<h2>' . __FUNCTION__ . '</h2>';
        $this->load->helper('file');
        $data = array();
        $data = $this->lang->language;
        $data['htmltitle'] .= ' | Viewer:' . $idwf;
        $data['theme'] = $this->config->item('theme');
        $data['base_url'] = $this->base_url;
        $data['idwf'] = $idwf;
        $wf = $this->bpm->load($idwf);
        $data+=$wf['data']['properties'];
//var_dump($wfData);
//---read model SVG
        $data['svgfile'] = "images/svg/$idwf.svg";
        $data['SVG'] = htmlspecialchars(read_file($data['svgfile']));
//---OUTPUT AS XML
        header("Content-Type: application/xhtml+xml");
        echo '<?xml version="1.0" encoding="UTF-8"?>';
        echo $this->parser->parse('bpm/view-model.php', $data, true);
    }

    function tokens($model, $idwf, $idcase, $filter_status = '') {
        $debug = (isset($this->debug[__FUNCTION__])) ? $this->debug[__FUNCTION__] : false;
        if ($debug)
            echo '<h2>' . __FUNCTION__ . '</h2>';
        $this->load->helper('file');
        $data = $this->lang->language;
        $data['htmltitle'] .= ' | Tokens:' . $idcase;
        $data['theme'] = $this->config->item('theme');
        $data['base_url'] = $this->base_url;
//---load WF
        $mywf = $this->bpm->load($idwf, true);
        $mywf['data']['idwf'] = $idwf;
        $mywf['data']['case'] = $idcase;
        $wf = bindArrayToObject($mywf['data']);
        $status = array('$regex' => $filter_status);
//$status=array('$regex'=>'^wa*');
        $case = $this->bpm->get_case($wf->case);
        $open = $case['history'];
        $data['count'] = count($open);
        $data['idcase'] = $idcase;
        $data['idwf'] = $idwf;


//        //---fetch icons ------------------------------------
//        $file = array();
//        $path = 'jscript/bpm/stencilsets/bpmn2.0/icons/';
//        $dirs = array_filter(glob($path . '*'), 'is_dir');
//        foreach ($dirs as $dir) {
//            //echo $dir.':<br/>';
//            //var_dump(array_filter(glob($dir . '/*.png'), 'is_file'));
//            $file = array_merge($file, array_filter(glob($dir . '/*.png'), 'is_file'));
//        }
//        var_dump($file);
//        //---------------------------------------------------

        $tokens = array();
        foreach ($open as $token) {
            $token_wf = $this->bpm->get_shape($token['resourceId'], $wf);
            $token['icon-status'] = $this->bpm->get_status_icon($token['status']);
            $token['icon'] = $this->bpm->get_icon($token['type']);
//var_dump($token);
            if ($token_wf) {
                $token+=bindObjectToArray($token_wf->properties);
//---set subtye
// 4 task
                if (property_exists($token_wf->properties, 'tasktype'))
                    $token['subtype'] = $token_wf->properties->tasktype;
//---4 gateway
                if (property_exists($token_wf->properties, 'gatewaytype'))
                    $token['subtype'] = $token_wf->properties->gatewaytype;
//---4 flow
                if (property_exists($token_wf->properties, 'conditionexpression'))
//var_dump($token_wf->properties->conditionexpression);
                    $token['name'] = $token_wf->properties->conditionexpression;

                $token['subtype'] = isset($token['subtype']) ? $token['subtype'] : null;
            }
            $data['tokens'][] = $token;
        }
//---read model SVG
        $data['svgfile'] = "images/svg/$idwf.svg";
        $data['SVG'] = htmlspecialchars(read_file($data['svgfile']));
//$data['SVG'] = str_replace('black', 'green', $data['SVG']);
//---OUTPUT AS XML
        header("Content-Type: application/xhtml+xml");
        echo '<?xml version="1.0" encoding="UTF-8"?>';
        echo $this->parser->parse('bpm/tokens.php', $data, true);
    }

    function get_comments($model, $idwf, $resourceId) {
        $wfData = $this->lang->language;
//var_dump($level);
        $wfData['htmltitle'] = 'WF-Manager:' . $idwf;
        $wfData['theme'] = $this->config->item('theme');
        $wfData['base_url'] = $this->base_url;
        $wfData['idwf'] = $idwf;
        $mywf = $this->bpm->load($idwf);
        $wf = $this->bpm->bindArrayToObject($mywf['data']);
        $shape = $this->bpm->get_shape($resourceId, $wf);
        echo $shape->stencil->id . '<br/>';
        echo $shape->properties->documentation;
//$this->parser->parse('bpm/comments.php', $wfData);
    }

    function upload() {
        $this->load->helper(array('form', 'url'));
        $this->load->library('upload');
        $this->load->view('upload_form', array('error' => ' '));
    }

    function Import($model) {
        $debug = (isset($this->debug[__FUNCTION__])) ? $this->debug[__FUNCTION__] : false;
        if ($debug)
            echo '<h2>' . __FUNCTION__ . '</h2>';
//---load needed libraries
        $this->load->helper('form');
        $this->load->helper('url');
        $this->load->helper('file');
        $filePath = "images/zip/";
        //@todo better warning manager
        try {
            if (!is_dir($filePath)) {
                mkdir($filePath, 0775, true);
            }
        } catch (Exception $e) {
            var_dump($e);
        }
//---handle  the upload
        $config['upload_path'] = './' . $filePath;

        $config['allowed_types'] = 'zip';
        $config['overwrite'] = true;
        $this->load->library('upload', $config);
        if (count($_FILES)) {

            if (!$this->upload->do_upload('file')) {
                $rtnObject['msg'] = $this->upload->display_errors();
                $rtnObject['success'] = false;
            } else {
                $upload_data = $this->upload->data();
                if ($debug)
                    var_dump('$upload_data', $upload_data);

                $svg = '';
                $exten = '';
                $err = false;
//---check for post
                $file_import = $upload_data['full_path'];
                $filename = $upload_data['file_name'];


                $zip = new ZipArchive;
                if ($zip->open($file_import) === true) {
                    $zip->extractTo('./');
                    $zip->close();
                } else {
                    $err = true;
                    $rtnObject['msg'] = "Error can't deflate:$file_import";
                    $rtnObject['success'] = false;
                }
                if (!$err) {
                    $idwf = $upload_data['raw_name'];
                    $filename = "images/model/$idwf.json";
                    $filename_svg = "images/svg/$idwf.svg";
                    $model = $this->bpm->model_exists($idwf);

                    $svg = read_file($filename_svg);
                    if ($raw = read_file($filename)) {
                        $data = json_decode($raw, false);
//---if exists set the internal id of the old one
                        $thisModel['idwf'] = $idwf;
                        $thisModel['data'] = $data;
                        $thisModel['folder'] = 'General';
                        $thisModel['svg'] = $svg;
                        if ($model) {
                            $this->bpm->save($idwf, $data, $svg);
                            $rtnObject['msg'] = "Imported OK! Updated existing model";
                            $rtnObject['success'] = true;
                        } else {
                            $rtnObject['msg'] = "Imported OK! New Model Created";
                            $rtnObject['success'] = true;
                            $rs = $this->bpm->save_raw($thisModel);
                        }
                    } else {
                        $rtnObject['msg'] = "Error reading $file_import";
                        $rtnObject['success'] = false;
                    }
                }//---not error
            }
        } else {
            $rtnObject['msg'] = "Error no file posted";
            $rtnObject['success'] = false;
        }
        if (!$debug) {
            header('Content-type: application/json;charset=UTF-8');
            echo json_encode($rtnObject);
        } else {
            var_dump($rtnObject);
        }
    }

    function update_folder() {
        $debug = false;
        if (!$this->input->post('idwf')) {
            show_error("Can't access this page directly");
        }
        $idwf = $this->input->post('idwf');
        $folder = $this->input->post('folder');
        $mywf = $this->bpm->load($idwf, false);
        $rtnObject = $this->bpm->update_folder($idwf, $folder);
        if (!$debug) {
            header('Content-type: application/json;charset=UTF-8');
            echo json_encode($rtnObject);
        } else {
            var_dump($rtnObject);
        }
    }

    /*
      function testmovi($model, $idwf) {
      $this->load->view('bpm/testmovi.php');
      }

      function movi($model, $idwf, $mode) {
      //var_dump($model, $idwf, $mode);
      switch ($mode) {

      case 'jsonp':
      $mywf = $this->bpm->load($idwf, false);
      header('Content-type: application/json;charset=UTF-8');
      echo 'MOVI.widget.ModelViewer.getInstance(0).loadModelCallback(' . json_encode($mywf['data']) . ')';
      //echo 'MOVI.widget.ModelViewer.getInstance(0).loadModelCallback({"resourceId":"oryx-canvas123","properties":{"id":"","name":"","documentation":"","version":"","author":"","language":"English","expressionlanguage":"","querylanguage":"","creationdate":"","modificationdate":"","pools":""},"stencil":{"id":"BPMNDiagram"},"childShapes":[{"resourceId":"oryx_2023B13C-9A9A-446D-B4D8-C23A5E169CAB","properties":{"id":"","name":"pay invoice","categories":"","documentation":"","assignments":"","pool":"","lanes":"","activitytype":"Task","status":"None","performers":"","properties":"","inputsets":"","inputs":"","outputsets":"","outputs":"","iorules":"","startquantity":"1","completionquantity":"1","looptype":"None","loopcondition":"","loopcounter":"1","loopmaximum":"1","testtime":"After","mi_condition":"","mi_ordering":"Sequential","mi_flowcondition":"All","complexmi_condition":"","iscompensation":"","tasktype":"None","inmessage":"","outmessage":"","implementation":"Webservice","messageref":"","instantiate":"","script":"","taskref":"","bgcolor":"#ffffcc"},"stencil":{"id":"Task"},"childShapes":[],"outgoing":[],"bounds":{"lowerRight":{"x":309,"y":255},"upperLeft":{"x":209,"y":175}},"dockers":[]}],"bounds":{"lowerRight":{"x":1485,"y":1050},"upperLeft":{"x":0,"y":0}},"stencilset":{"url":"http://oryx.bpmn-community.org:80/oryx/stencilsets/bpmn1.1/bpmn1.1.json","namespace":"http://b3mn.org/stencilset/bpmn1.1#"},"ssextensions":[]});';
      break;
      case 'png':
      $this->load->helper('file');
      $file = "images/png/$idwf.png";
      //var_dump($file);
      header('Content-type: image/png;charset=UTF-8');
      echo read_file($file);
      break;
      }
      }

      function get_stencilset($model, $idwf) {
      $this->load->helper('file');
      $mywf = $this->bpm->load($idwf, false);
      //---Strip the base url from stencilset
      //$file = str_replace($this->base_url, '', $mywf['data']['stencilset']['url']);
      $file = 'jscript/bpm-dna2/stencilsets/bpmn2.0/movi.json';
      //var_dump($file);
      header('Content-type: application/json;charset=UTF-8');
      echo 'MOVI.widget.ModelViewer.getInstance(0).loadStencilSetCallback(';
      echo read_file($file);
      echo ')';
      }
     */

    function repair_stencil_path() {
        $wfs = $this->bpm->get_models(array(), array('data.stencilset'));
        $data = array(
            'data.stencilset.url' => '../../jscript/bpm-dna2/stencilsets/bpmn2.0/bpmn2.0_2.json',
        );
        foreach ($wfs as $bpm) {
            $this->bpm->update_model($bpm->idwf, $data);
            echo $bpm->idwf . ":done!<hr/>";
        }
    }

}
