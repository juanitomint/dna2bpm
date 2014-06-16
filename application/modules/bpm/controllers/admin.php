<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

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
        $this->idu = (int) $this->session->userdata('iduser');
    }

    function Index() {
        $this->Newb();
    }

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
        );

        $cpData['js'] = array(
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

        $this->ui->makeui('ext.ui.php', $cpData);
    }

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
            header('Content-type: application/json;charset=UTF-8');
            echo json_encode($full_tree);
        } else {
            var_dump($full_tree);
        }
    }

    //---handle crud for process tree
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
            header('Content-type: application/json;charset=UTF-8');
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
        $models = $this->bpm->get_models();
        foreach ($models as $bpm) {
            $folder = (property_exists($bpm, 'folder')) ? $bpm->folder . '/' : '';
            $m_arr[$folder . $bpm->idwf] = $bpm->data['properties']['name'] . ' [' . $bpm->idwf . ']';
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

    //---delete this function when converted to EXT
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

    function convert_to_ext($array) {
        $rtn_arr = array();
        foreach ($array as $key => $value) {
            if (is_array($value)) {
                asort($value);
                $rtn_arr[] = array_filter(
                        array(
                            'id' => $key,
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

    function import_form() {
        $this->load->helper('file');
        $wfData = array();
        $wfData = $this->lang->language;
        $wfData['theme'] = $this->config->item('theme');
        $wfData['base_url'] = base_url();
        $this->parser->parse('bpm/import_form.php', $wfData);
    }

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

    function get_model($idwf, $mode = 'html') {
        $idwf = urldecode($idwf);
        $wfData['base_url'] = base_url();
        $model = $this->bpm->load($idwf);
        $cpData = $model['data'];
        $cpData['idwf'] = $idwf;
        $cpData['base_url'] = base_url();
        switch ($mode) {
            case 'json':
                header('Content-type: application/json;charset=UTF-8');
                echo json_encode($cpData);
                break;

            case 'html':
                $this->parser->parse('model', $cpData);
                break;
        }
        //var_dump($cpData);
    }

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
            $result[] = array(
                'id' => $wf->idwf,
                'idwf' => $wf->idwf,
                'version' => $wf->version,
                'name' => (property_exists($wf, 'data')) ? $wf->data['properties']['name'] : '???',
                'documentation' => (property_exists($wf, 'data')) ? $wf->data['properties']['documentation'] : '',
            );
        }
        header('Content-type: application/json;charset=UTF-8');
        echo json_encode($result);
    }

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

?>