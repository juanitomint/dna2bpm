<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/**
 * This class is used by the administration backend
 * @author Juan Ignacio Borda <juanignacioborda@gmail.com>
 * @date Feb 10, 2013
 */
class Admin extends MX_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->config('config');
        $this->load->library('parser');
        $this->load->model('user');
        $this->load->model('bpm');
        //---base variables
        $this->base_url = base_url();
        $this->module_url = base_url() . $this->router->fetch_module() . '/';
        $this->user->authorize();
        //----LOAD LANGUAGE
        $this->lang->load('library', $this->config->item('language'));
        $this->expandSubProcess=true;
        $this->idu = $this->user->idu;
    }

    /**
     * This is the main function of the module
     */
    function Index() {
        $this->Newb();
    }

    /**
     * Shows a mini task panel
     * 
     */
    function Newb() {

        $this->load->library('ui');
        //$this->parser->parse('bpm/ext.browser.php', $wfData);
        //    var_dump(base_url()); exit;
        //---only allow admins and Groups/Users enabled
        $this->user->authorize();
        $cpData = $this->lang->language;
        $segments = $this->uri->segment_array();
        $cpData['nolayout'] = (in_array('nolayout', $segments)) ? '1' : '0';
        //var_dump($level);
        $cpData['theme'] = $this->config->item('theme');
        $cpData['base_url'] = $this->base_url;
        $cpData['module_url'] = $this->module_url;
        $cpData['title'] = 'Model Browser';
        $cpData['ext-locale'] = 'ext-lang-es';
        //---define files to viewport
        $cpData['css'] = array(
            $this->base_url . "jscript/ext/src/ux/css/CheckHeader.css" => 'checkHeader',
            $this->module_url . "assets/css/load_mask.css" => 'loadingmask',
            $this->module_url . "assets/css/browser.css" => 'Browser',
            $this->module_url . "assets/css/extra-icons.css" => 'Extra Icons',
            $this->module_url . "assets/css/extra-icons.css" => 'Extra Icons',
        );

        $cpData['js'] = array(
            $this->base_url . "jscript/jquery/jquery.min.js" => 'JQuery',
            //----Pan & ZooM---------------------------------------------
            $this->module_url . 'assets/jscript/panzoom/jquery.panzoom.min.js' => 'Panzoom Minified',
            $this->module_url . 'assets/jscript/panzoom/jquery.mousewheel.js' => 'wheel-suppport',
            $this->module_url . 'assets/jscript/panzoom/pnazoom_wheel.js' => 'wheel script',
            //-----------------------------------------------------------------
            $this->module_url . "assets/jscript/ext.settings.js" => 'Settings',
            $this->module_url . 'assets/jscript/ext.model-utils.js' => 'Model utils',
            $this->module_url . "assets/jscript/browser/data.js" => 'Data Objects',
            $this->module_url . "assets/jscript/browser/tree.js" => 'Module Tree',
            $this->module_url . 'assets/jscript/browser/ext.add_events.js' => 'Events for overlays',
            $this->module_url . "assets/jscript/browser/center_panel.js" => 'Center Panel',
            $this->module_url . "assets/jscript/browser/viewport.js" => 'Viewport',
        );

        $cpData['global_js'] = array(
            'base_url' => $this->base_url,
            'module_url' => $this->module_url,
        );

        $this->ui->makeui('ext.ui-no-ion.php', $cpData);
    }

    /**
     * Get a model tree representation in JSON format
     * 
     */
    function get_tree() {
        // $this->load->helper('ext');
        $debug = false;
        //---get models
        $order = 'data.properties.name';
        $models = $this->bpm->get_models();

        //$sort = array($order => 1);

        $totalRecords = count($models);
        //---make tree
        $id = 2;
        foreach ($models as $model) {
            $model=(object)$model;
            $model->folder = (property_exists($model, 'folder')) ? $model->folder : null;
            $m_arr[$model->folder . '/' . $model->idwf] = (property_exists($model, 'data')) ? $model->data['properties']['name'] : '???';
        }
        if ($debug)
            var_dump($m_arr);
        $tree = array();
        $tree = $this->explodeTree($m_arr, $delimiter = '/');
        if ($debug)
            var_dump($tree);
        $item_tree = $this->convert_to_item($tree, 1);
        if ($debug)
            var_dump($item_tree);
        $full_tree = array('id' => 0, 'item' => array(
                array('id' => 1, 'text' => 'Home', 'item' => $item_tree)
            )
        );

        if (!$debug) {
            $this->output->set_content_type('json','utf-8');
            echo json_encode($full_tree);
        } else {
            var_dump($full_tree);
        }
    }

    /**
     * handle CRUD for model tree
     */
    function tree($action) {
        $debug = false;
        $msg = array('ok' => true);
        switch ($action) {
            case "create":
                break;
            case "read":
                break;
            case "update":
                $input = json_decode(file_get_contents('php://input'));
                //var_dump('$input',$input);
                if (is_array($input)) {

                    foreach ($input as $wf) {
                        var_dump($wf->id, $wf->parentId);
                        $this->bpm->update_folder($wf->id, $wf->parentId);
                    }
                } else {
                    $this->bpm->update_folder($input->id, $input->parentId);
                }
                break;
            case "destroy":
                break;
        }
        if (!$debug) {
            $this->output->set_content_type('json','utf-8');
            echo json_encode($msg);
        } else {
            var_dump(json_encode($msg));
        }
    }

    function get_tree2() {
        $m_arr = array();
//$this->load->helper('ext');
        $debug = false;
        //---get models
        $order = 'data.properties.name';
        $models = $this->bpm->get_models(array(),array('idwf','folder','data'));
        foreach ($models as $bpm) {
            if($bpm){
                $bpm=(object)$bpm;
            $folder = (property_exists($bpm, 'folder')) ? $bpm->folder . '/' : '';
            $name = (isset($bpm->data['properties']['name'])) ? $bpm->data['properties']['name'] : "no name";
            $m_arr[$folder . $bpm->idwf] = $name . ' [' . $bpm->idwf . ']';
            }
        }
        $tree = $this->explodeTree($m_arr, $delimiter = '/');

        $full_tree = $this->convert_to_ext($tree, 0);

//        $full_tree = array((object) array(
//            "id" => 'root',
//            "text" => "Object Repository",
//            "cls" => "folder",
//            "expanded" => true,
//            "checked" => false,
//            "children"=>$rtnArr
//            ));

        if (!$debug) {
            header('Content-type: application/json');
            echo json_encode($full_tree);
        } else {
            var_dump($full_tree);
        }
    }

    /**
     * convert an array of models to an EXT tree-item
     * @todo delete this function when converted to EXT
     */
    function convert_to_item($array, $id) {
        $rtn_arr = array();
        $i = 1;
        foreach ($array as $key => $value) {
            if (is_array($value)) {
                asort($value);
                $rtn_arr[] = array(
                    'id' => (int) ($id . $i++),
                    'text' => $key,
                    'child' => count($value),
                    'im0' => 'folderClosed.gif',
                    'item' => $this->convert_to_item($value, $id)
                );
                $id++;
            } else {
                $rtn_arr[] = array(
                    'id' => (int) ($id . $i++),
                    'id' => $key,
                    'text' => $value . " ($key)",
                    'userdata' => array(
                        array('name' => 'idwf', 'content' => $key),
                        array('name' => 'ismodel', 'content' => true),
                    ),
                );
            }
        }
        return $rtn_arr;
    }

    /**
     * convert an array of models to an EXT tree-item
     * @todo check wich function is needed
     */
    function convert_to_ext($array) {
        $rtn_arr = array();
        $u=1;
        foreach ($array as $key => $value) {
            if (is_array($value)) {
                asort($value);
                $rtn_arr[] = array_filter(
                        array(
                            // 'id' => $key.$i++,
                            'text' => $key,
                            'leaf' => false,
                            'cls' => 'folder',
                            'checked' => ($this->config->item('browser_tree_checkable_folders')) ? false : null,
                            'expanded' => ($this->config->item('browser_tree_expanded')) ? true : false,
                            'children' => array_filter($this->convert_to_ext($value))
                        )
                );
                //$id++;
            } else {
                $rtn_arr[] = array_filter(
                        array(
                            'id' => $key,
                            'text' => $value,
                            'leaf' => true,
                            'checked' => ($this->config->item('browser_tree_checkable_models')) ? false : null,
                            //data' => $value,
                            'iconCls' => ($this->config->item('tree_icon_model')) ? $this->config->item('tree_icon_model') : null
                        )
                );
            }
        }
        return array_filter($rtn_arr);
    }

    /**
     * Shows the import form
     */
    function import_form() {
        $this->load->helper('file');
        $wfData = array();
        $wfData = $this->lang->language;
        $wfData['theme'] = $this->config->item('theme');
        $wfData['base_url'] = base_url();
        $this->parser->parse('bpm/import_form.php', $wfData);
    }

    /**
     * Shows the New Model form
     */
    function new_model_form() {
        $wfData = array();
        $wfData = $this->lang->language;
        $wfData['theme'] = $this->config->item('theme');
        $wfData['base_url'] = base_url();
        $models = $this->bpm->get_models();
        $folder_arr = array();
        foreach ($models as $model) {
            $folder = (isset($model['folder'])) ? $model['folder'] : 'General';
            $folder_arr[$folder] = 1;
        }
        //---add 'General' to folder list
        //---TODO i18n
        $folder_arr['General'] = 1;

        ksort($folder_arr);
        foreach ($folder_arr as $folder => $value) {
            $wfData['folders'][] = array('folder' => $folder);
        }
        $this->parser->parse('bpm/new_model_form.php', $wfData);
    }

    function model_move($from, $to) {
        
    }

    /**
     * Returns a JSON representattion of a case for a given model
     * @param string $idwf The idwf of the model
     * @param string $idcase The id of the case
     */
    function get_data($idwf, $idcase) {
        $debug = false;
        $this->load->module('bpm/engine');
        //---check Exists.
        $mywf = $this->bpm->load($idwf, $this->expandSubProcess);

        if($mywf){
            $mywf ['data'] ['idwf'] = $idwf;
            $mywf ['data'] ['case'] = $idcase;
            $mywf ['data'] ['folder'] = $mywf ['folder'];
            $wf = bindArrayToObject($mywf ['data']);
            // ----make it publicly available to other methods
            $this->engine->wf = $wf;
            $this->engine->load_data($wf,$idcase);
        }
        
        // $debug = false;
        // $this->load->model('bpm', 'bpm_model');
        // $case = $this->bpm->get_case($idcase, $idwf);
        // $data = $this->bpm->load_case_data($case);
        if (!$debug) {
            $this->output->set_content_type('json','utf-8');
            $this->output->set_output(json_encode($this->engine->data));
        } else {
            var_dump($this->data);
        }
    }

    /**
     * Returns a representattion of a case for a given model in HTML or JSON
     * @param string $idwf The idwf of the model
     * @param string $mode html or json
     */
    function get_model($idwf, $mode = 'html') {
        $idwf = urldecode($idwf);
        $wfData['base_url'] = base_url();
        $model = $this->bpm->load($idwf);
        $cpData = $model['data'];
        $cpData['idwf'] = $idwf;
        $cpData['base_url'] = base_url();
        switch ($mode) {
            case 'json':
                $this->output->set_content_type('json','utf-8');
                echo json_encode($cpData);
                break;

            case 'html':
                $this->parser->parse('model', $cpData);
                break;
        }
        //var_dump($cpData);
    }

    /**
     * Returns a JSON representation of all models (flat)
     */
    function get_models() {
        $wfData = array();
        $wfData = $this->lang->language;
        $wfData['theme'] = $this->config->item('theme');
        $wfData['base_url'] = base_url();
        $param = $this->uri->uri_to_assoc(4);

        $filter = (isset($param['folder'])) ? array('folder' => $param['folder']) : array();
        $order = (isset($param['order'])) ? $param['order'] : 'data.properties.name';
        $models = $this->bpm->get_models($filter, array('idwf', 'version', 'data.properties'));

        $sort = array($order => 1);
        //$models->sort($sort);
        $result = array();
        $i = 1;
        foreach ($models as $wf) {
            $wf=(object)$wf;
            $result[] = array(
                'id' => $wf->idwf,
                'idwf' => $wf->idwf,
                'version' => $wf->version,
                'name' => (property_exists($wf, 'data')) ? $wf->data['properties']['name'] : '???',
                'documentation' => (property_exists($wf, 'data')) ? $wf->data['properties']['documentation'] : '',
            );
        }
        $this->output->set_content_type('json','utf-8');
        echo json_encode($result);
    }
    /**
     * Converts an array of paths into a Tree
     */
    function explodeTree($array, $delimiter = '_', $baseval = false) {
        if (!is_array($array))
            return false;
        $splitRE = '/' . preg_quote($delimiter, '/') . '/';
        $returnArr = array();
        foreach ($array as $key => $val) {
            // Get parent parts and the current leaf
            $parts = preg_split($splitRE, $key, -1, PREG_SPLIT_NO_EMPTY);
            $leafPart = array_pop($parts);

            // Build parent structure
            // Might be slow for really deep and large structures
            $parentArr = &$returnArr;
            foreach ($parts as $part) {
                if (!isset($parentArr[$part])) {
                    $parentArr[$part] = array();
                } elseif (!is_array($parentArr[$part])) {
                    if ($baseval) {
                        $parentArr[$part] = array('__base_val' => $parentArr[$part]);
                    } else {
                        $parentArr[$part] = array();
                    }
                }
                $parentArr = &$parentArr[$part];
            }

            // Add the final part to the structure
            if (empty($parentArr[$leafPart])) {
                $parentArr[$leafPart] = $val;
            } elseif ($baseval && is_array($parentArr[$leafPart])) {
                $parentArr[$leafPart]['__base_val'] = $val;
            }
        }
        //---order by name
        asort($returnArr);
        return $returnArr;
    }
    /**
     * Movi interface
     * @deprecated since version .5
     */
    function movi($idwf, $idcase = null) {
        $wfData = array();
        $wfData = $this->lang->language;
        $wfData['theme'] = $this->config->item('theme');
        $wfData['base_url'] = base_url();
        $wfData['idwf'] = $idwf;
        $wfData['idcase'] = $idcase;
        if ($idcase) {
            $case = $this->bpm->getcase($idcase);
        }
        header('Content-type: application/xhtml+xml');
        echo $this->parser->parse('bpm/movi.php', $wfData, true);
    }

}
