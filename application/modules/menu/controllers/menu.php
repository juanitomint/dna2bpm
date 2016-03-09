<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Menu extends MX_Controller {

    public $template = array(
        'id' => 'string',
        'title' => 'string',
        'target' => 'string',
        'text' => 'string',
        'cls' => 'string',
        'iconCls' => 'string',
        'priority' => 'int',
        'info' => 'string',
        'hidden' => 'boolean',
        'callBack' => 'string',
    );

    function __construct() {
        parent::__construct();
        $this->load->library('parser');

        $this->load->model('menu/menu_model');
        $this->user->authorize();
        //---base variables
        $this->base_url = base_url();
        $this->module_url = base_url() . $this->router->fetch_module() . '/';
        //----LOAD LANGUAGE
        $this->lang->load('library', $this->config->item('language'));
        $this->idu = $this->user->idu;
    }

    function Index() {
        $this->Admin();
    }

    function Admin($repoId = '0') {
        //    var_dump(base_url()); exit;
        //---only allow admins and Groups/Users enabled
        $this->load->library('ui');
        $this->user->authorize();
        $cpData = $this->lang->language;
        $segments = $this->uri->segment_array();
        $cpData['nolayout'] = (in_array('nolayout', $segments)) ? '1' : '0';
        //var_dump($level);
        $cpData['theme'] = $this->config->item('theme');
        $cpData['base_url'] = $this->base_url;
        $cpData['module_url'] = $this->module_url;
        $cpData['title'] = 'Menu Admin';
        $cpData['ext-locale'] = 'ext-lang-es';
        //---define files to viewport
        $cpData['css'] = array(
            $this->base_url . "jscript/ext/src/ux/css/CheckHeader.css" => 'checkHeader',
            $this->module_url . "assets/css/admin.css" => 'Admin css',
            $this->module_url . "assets/css/groups.css" => 'Groups css',
            $this->module_url . "assets/css/load_mask.css" => 'loadingmask',
        );

        $cpData['js'] = array(
            $this->module_url . "assets/jscript/ext.settings.js" => 'Settings',
            $this->module_url . 'assets/jscript/ionicons.js' => 'Ion icons',
            $this->module_url . "assets/jscript/data.js" => 'Data Objects',
            $this->module_url . "assets/jscript/tree_menu.js" => 'Menu Tree',
//            $this->module_url . "assets/jscript/ext.load_props.js" => 'Properties Loader',
            $this->module_url . "assets/jscript/propertyGrid.js" => 'Menu Properties Editor',
            $this->module_url . "assets/jscript/app.js" => 'Viewport',
        );

        $cpData['global_js'] = array(
            'base_url' => $this->base_url,
            'module_url' => $this->module_url,
            'repoId' => $repoId,
        );

        $this->ui->makeui('menu/ext.ui.php', $cpData);
    }

    function getpaths($repoId = 0) {
        $this->user->authorize();
        $segments = $this->uri->segments;
        $debug = (in_array('debug', $segments)) ? true : false;

        //--get paths from db
        $rtnArr['paths'] = $this->menu_model->get_paths();

        if (!$debug) {
            $this->output->set_content_type('json','utf-8');
            echo json_encode($rtnArr);
        } else {
            var_dump($rtnArr);
        }
    }

    function get_properties() {
        $debug = false;
        $data['id'] = $this->input->post('id');
        $repoId = ($this->input->post('repoId')) ? $this->input->post('repoId') : '0';

        //$data = $this->menu_model->get_path($repoId, $this->input->post('id'));

        $this->load->helper('dbframe');
        $menu_item = new dbframe();
        //$properties=(isset($data['properties'])) ? $data['properties'] : array();
        $menu_item->load(array(), $this->template);
        if (!$debug) {
            $this->output->set_content_type('json','utf-8');
            echo json_encode($menu_item->toShow());
        } else {
            var_dump('Obj', $menu_item, 'Save:', $menu_item->toSave(), 'Show', $menu_item->toShow());
        }
    }

    function repository($repoId = 0, $action) {
        $repoId = $repoId;
        $this->user->authorize();
        $this->load->helper('ext');
        $segments = $this->uri->segments;
        $debug = (in_array('debug', $segments)) ? true : false;
        $i = 0;
        $rtnArr = array();

        switch ($action) {
            case 'update'://----Path added
                $post = json_decode(file_get_contents('php://input'));
                //----remove root from path
                $this->menu_model->remove_path($repoId, '/');
                foreach ($post as $menuItem) {
//                    $path_arr = explode('/', $menuItem->path);
//                    array_shift($path_arr);
                    $path = str_replace('/root', '', $menuItem->path);
//                    $path = $menuItem->path;
                    $properties = array(
                        "source" => "User",
                        "checkdate" => date('Y-m-d H:i:s'),
                        "idu" => $this->idu
                    );
                    $result = $this->menu_model->put_path($repoId, $path, array_merge($properties, (array) $menuItem));
                }
                $rtnArr['success'] = true;
                break;
            case 'read':
                $node = ($this->input->post('node')) ? $this->input->post('node') : 'root';
                if ($node == 'root') {
                    $repo = $this->menu_model->get_repository(array('repoId' => $repoId), false);
                    $rtnArr = $this->explodeExtTree($repo, '/');
                    //var_dump($repo);
                    //----return skip root node
                    $rtnArr = (property_exists($rtnArr[0], 'children')) ? $rtnArr[0]->children : array();
                } else {

                    $rtnArr = array();
                }
                break;
            case 'sync':
                $rtnArr['success'] = false;
                $paths = $this->input->post('paths');

                if ($paths) {
                    $i = 0;
                    foreach ($paths as $path) {
                        $item = $this->menu_model->get_path($repoId, $path);
                        $item->properties['priority'] = $i++;
                        $this->menu_model->put_path($path, $item->properties);
                    }
                }
                $rtnArr['success'] = true;
                break;
            case 'delete':

                $rtnArr['success'] = false;
                //----remove root from path
                $path = str_replace('/root', '', $this->input->post('path'));
//                $path_arr = explode('/', $path);
//                array_shift($path_arr);
//                $path = implode('/', $path_arr);
                if ($path)
                    $rtnArr['success'] = $this->menu_model->remove_path($repoId, $path);
                break;
        }
        if (!$debug) {
            $this->output->set_content_type('json','utf-8');
            echo json_encode($rtnArr);
        } else {
            var_dump($rtnArr);
        }
    }

    function save_properties() {
        $debug = false;
        $post = json_decode(file_get_contents('php://input'));
        $this->load->helper('dbframe');
        $rtnArr['success'] = false;
        $repoId = $post->repoId;
        $path = $post->path;
        $data = $post->data;
        //---strip root
        $path_arr = explode('/', $path);
        array_shift($path_arr);
        $path = implode('/', $path_arr);
        //---Convert Group string to array;
        $menu_item = new dbframe($data, $this->template);
        $properties = array(
            "source" => "User",
            "checkdate" => date('Y-m-d H:i:s'),
            "idu" => $this->idu
        );
        $result = $this->menu_model->put_path($repoId, $path, array_merge($properties, $menu_item->toSave()));


        if (!$debug) {
            $this->output->set_content_type('json','utf-8');
            echo json_encode($menu_item->toShow());
        } else {
            var_dump($menu_item->toShow());
        }
    }

    /**
     * 
     * Returns the html representation of a menu
     * 
     * @param string $repoId <p>
     * The name of yout repository
     * </p>
     * @param string  $ulClass
     * @param boolean $check
     * @return string an HTML representation of your menu.
     */
    function get_menu($repoId = '0', $ulClass = '', $check = true) {
        //---return HTML menu
        $query = array('repoId' => $repoId);
        $repo = $this->menu_model->get_repository($query, $check);
        $tree = $this->explodeExtTree($repo, '/');
        $menu = $this->get_ul($tree[0]->children, $ulClass);
        return $menu;
    }

    function get_menu_bs($repoId = '0', $ulAdd = '') {
        //---return HTML menu
        $query = array('repoId' => $repoId);
        $repo = $this->menu_model->get_repository($query);
        //var_dump($m);
        $tree = $this->explodeExtTree($repo, '/');
        $menu = $this->get_ul_submenu($tree[0]->children, $ulAdd);
        return $menu;
    }

    /*
     *  This function returns a pointer to the part of the array matching key=>value
     */

    function search(&$arr, $key, $value) {
        $arrIt = new RecursiveIteratorIterator(new RecursiveArrayIterator($arr));
        foreach ($arrIt as $sub) {
            $subArray = $arrIt->getSubIterator();
            $subArray->jj = true;
            if (isset($subArray[$key]) && $subArray[$key] == $value) {
                //return iterator_to_array($subArray);
                return $subArray;
            }
        }
        return null;
    }

    function explodeExtTree($array, $delimiter = '/') {
        $CI = &get_instance();
        if (!is_array($array))
            return false;
        //---Setings
        $expanded = false;
        $leafCls = 'dot-green';
        $splitRE = '/' . preg_quote($delimiter, '/') . '/';
        $returnArr = array((object) array(
                "id" => 'root',
                "text" => "Object Repository",
                "cls" => "folder",
                "icon-cls" => "icon-home",
                "expanded" => true,
                "leaf" => false,
                "path" => '/root',
                "children" => array(),
        ));

        foreach ($array as $thispath => $val) {
            // Get parent parts and the current leaf
            //$parts = preg_split($splitRE, $key, -1, PREG_SPLIT_NO_EMPTY);
            // Build parent structure
            $thispath = 'root' . $thispath;
            $path_arr = explode($delimiter, $thispath);
            array_pop($path_arr);
            $thisparentpath = '/' . implode('/', $path_arr);
            //prepare object to add
            $obj = (object) array_merge(array(
                        'id' => $val['id'],
                        'text' => $val['text'],
                        'priority' => (isset($val['priority'])) ? $val['priority'] : 10,
                        'leaf' => false,
                        'path' => $thispath,
                        'expanded' => true,
                        'children' => array()
                            //'checked' => false,
                            ), (array) $val);
            $obj->leaf = false;
            //$obj->data = $val;
            //---set the internal pointer to the parent
            $pointer = $this->search($returnArr, 'path', $thisparentpath);
            //----if parent exists (we start with 1 root so has to exists but just in case...)
            if ($pointer) {
                $pointer['leaf'] = false;
                $pointer['expanded'] = $expanded;
                $pointer['children'][] = $obj;
            }
        }
        return $returnArr;
    }

    function test_menu($repoId=0) {
        echo $this->get_menu($repoId,'',!$this->user->isAdmin());
    }

    
   function get_ul($menu, $ulClass = '') {

         $returnStr = '';
         $returnStr.='<ul class="' . $ulClass . ' ">';
         foreach ($menu as $path => $node) {

             $icon= "<i class='ion $node->iconCls'></i>";
             $anchor="<a href='$node->target' title='$node->title' class='$node->cls' >";
            
            if(!$node->leaf && count($node->children)){
                 //submenu
                 $returnStr.="<li class='treeview'><a href='#'> $icon <span>$node->text</span><i class='fa fa-angle-left pull-right'></i>";
                 $returnStr.=$this->get_ul($node->children, 'treeview-menu');
                 $returnStr.="</a></li>";
            }else{
                // li 
                 $returnStr.="<li>$anchor  $icon $node->text";
                 $returnStr.="</a></li>";
            } 
            
         }
        $returnStr.='</ul>';
        return $returnStr;

    }

    


}
