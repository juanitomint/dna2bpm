<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class rbac_admin extends MX_Controller {

    function __construct() {
        parent::__construct();
        $this->load->model('user');
        $this->load->model('group');
        $this->load->model('rbac');
        $this->load->helper('ext');
        $this->user->authorize();
        //----LOAD LANGUAGE
        //$this->lang->load('library', $this->config->item('language'));
        $this->idu = (int) $this->session->userdata('iduser');
    }

    function repository($action) {
        $this->user->authorize();
        $segments = $this->uri->segments;
        $debug = (in_array('debug', $segments)) ? true : false;
        $i = 0;
        $rtnArr = array();

        switch ($action) {
            case 'update'://----Path added
                $post = json_decode(file_get_contents('php://input'));
                //----remove root from path
                $path_arr = explode('/', $post->id);
                array_shift($path_arr);
                $path = implode('/', $path_arr);
                $properties = array(
                    "source" => "User",
                    "checkdate" => date('Y-m-d H:i:s'),
                    "idu" => $this->idu
                );
                $result = $this->rbac->put_path($path, $properties);
                $rtnArr['success'] = true;
                break;
            case 'read':
                $node = ($this->input->post('node')) ? $this->input->post('node') : 'root';
                $repo = $this->rbac->get_repository();
                $rtnArr = explodeExtTree($repo, '/');
                $rtnArr = $rtnArr[0]->children;
                break;
            case 'save':
                $rtnArr['success'] = false;
                $paths = $this->input->post('paths');
                $idgroup = $this->input->post('idgroup');
                //--remove all paths
                $this->rbac->clear_paths($idgroup);
                //----load repo to check if something is new
                $repo = array_keys($this->rbac->get_repository());
                if ($paths) {
                    foreach ($paths as $path) {
                        $this->rbac->put_path_to_group($path, $idgroup);
                        if (!in_array($path, $repo)) {

                            $path_arr = explode('/', $path);
                            array_shift($path_arr);
                            $path = implode('/', $path_arr);

                            $this->rbac->put_path($path, array(
                                'source' => 'RepoAdmin',
                                'checkdate' => date('Y-m-d H:i:s'),
                                'idu' => $this->idu
                                    )
                            );
                        }
                    }
                }
                $rtnArr['success'] = true;
                break;
            case 'delete':

                $rtnArr['success'] = false;

                $path = $this->input->post('path');
                //----remove root from path
                $path_arr = explode('/', $path);
                array_shift($path_arr);
                $path = implode('/', $path_arr);
                if ($path)
                    $rtnArr['success'] = $this->rbac->remove_path($path);

                break;
        }
        //var_dump($cpData);
        if (!$debug) {
            header('Content-type: application/json;charset=UTF-8');
            echo json_encode($rtnArr);
        } else {
            var_dump($rtnArr);
        }
    }

    function getpaths() {
        $this->user->authorize();
        $segments = $this->uri->segments;
        $debug = (in_array('debug', $segments)) ? true : false;
        $idgroup = (int) $this->input->post('idgroup');
        //--get paths from db
        $rtnArr['paths'] = $this->rbac->get_group_paths($idgroup);

        if (!$debug) {
            header('Content-type: application/json;charset=UTF-8');
            echo json_encode($rtnArr);
        } else {
            var_dump($rtnArr);
        }
    }

}

?>
