<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class File extends MX_Controller {

    function __construct() {
        parent::__construct();
        $this->debug = false;
        $this->debug_manual = true;
        $this->load->library('parser');
        $this->load->model('user');
        $this->load->model('bpm');
        $this->load->model('app');
        $this->load->model('msg');
        $this->user->authorize();
        //----LOAD LANGUAGE
        $this->lang->load('library', $this->config->item('language'));
    }


    function upload($idwf, $idcase) {
        $debug = false;
        $out = $_FILES;
        $idwf = $this->input->post('idwf');
        $idcase = $this->input->post('idcase');
        $resourceId = $this->input->post('resourceId');
        $mywf = $this->bpm->load($idwf, true);
        if (!$mywf) {
            show_error("Model referenced:$idwf does not exists");
        }
        $wf = $this->bpm->bindArrayToObject($mywf ['data']);
        $shape = $this->bpm->get_shape($resourceId, $wf);
        $out['resourceId']=$resourceId;
        $out['path'] = 'images/user_files/' . $idwf . '/' . $idcase . '/' . str_replace("\n", '_', $shape->properties->name);
        $out = array_merge((array) $shape->properties, $out);
//        $out['dname']=$this->input->post('dname');
//        $out['resourceId']=$this->input->post('resourceId');
        @mkdir($out['path'], 0777, true);
//        $config['upload_path'] = $out['path'];
////        $config['allowed_types'] = 'gif|jpg|png';
////        $config['max_size'] = '100';
////        $config['max_width'] = '1024';
////        $config['max_height'] = '768';
//
//        $this->load->library('upload', $config);
//        $this->upload->do_upload();
        $uploads_dir = $out['path'];

        $tmp_name = $_FILES["userfile"]["tmp_name"];
        $name = urldecode($_FILES["userfile"]["name"]);
        move_uploaded_file($tmp_name, "$uploads_dir/$name");

        if (!$debug) {
            $this->output->set_content_type('json','utf-8');
            echo json_encode($out);
        } else {
            var_dump($out);
        }
    }

}