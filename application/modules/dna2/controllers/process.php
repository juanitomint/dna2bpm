<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Process extends MX_Controller {

    function Process() {
        parent::__construct();
        $this->load->library('parser');
        $this->load->model('user');
        $this->load->model('app');
        $this->user->authorize();
        //----LOAD LANGUAGE
        $this->lang->load('library', $this->config->item('language'));
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

    function Go($idobject, $id='new') {
        if($id<>'new') $id=(int)$id;
        //---START Pre process Hooks
        $path = 'system/application/helpers/process/pre/';
        $hooks = glob($path . '*.php');
        //var_dump($hooks);
        foreach($hooks as $file) include($file);
        //---END Pre process Hooks

        //----get object from DB
        $form = $this->app->get_object($idobject);
        //var_dump($form);
        //----4 Testing
        /*
        $testid = 666;
        if (!$this->app->check_id($testid, $form['container'])) {
            $id = $this->app->genid($form['container'], $testid);
        } else {
            $id = $testid;
        }
         * 
         */
        //----get Active application for context
        $idapp = $this->session->userdata('active_app');
        $renderData = array();
        $frames = array();
        //---add language data
        $renderData = $this->lang->language;
        //----walk frames to make array
        $form_frames=$this->app->get_form_frames($form);
        foreach ($form_frames as $thisFrame) {
            //$thisFrame = $this->app->get_frame($idframe);
            $idframe=$thisFrame['idframe'];
            //----HOOK PRE input

            $frames[$idframe] = $this->input->post($thisFrame['cname']);
            //----HOOK POST input
        }

        //----CHECK 4 PROPPER ID and CHECK Existance
       
        if (is_numeric($id)) {
            if (!$this->app->check_id($id, $form['container']))
                show_error("Can't locate id:$id in $container, may be it doesn't exists.");
        }

        //----GEN NEW ID if new
        if ($id=='new') {
            $id = $this->app->genid($form['container']);
        }
        //---STORE VALUES ->DB
        $rtnarr = $this->app->put_array($id, $form['container'], $frames);
        $id = $rtnarr['id'];
        //var_dump($rtnarr);
        //
        //---START Post process Hooks
        $path = 'system/application/helpers/process/post/';
        $hooks = glob($path . '*.php');
        //var_dump($hooks);
        foreach($hooks as $file) include($file);
        //---END Post process Hooks

        //---HANDLE Redirection / Work Flow evals
        if(isset($form['redir'])) {
            $redir = ($form['redir']<>'') ? 'dna2/render/go/' . $form['redir'] .'/'.$id: 'dna2/controlpanel';
        } else {
            $redir = 'dna2/controlpanel';
        }
        header("Location:".base_url().$redir);
    }

}

/* * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
?>
