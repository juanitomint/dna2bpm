<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Task extends MX_Controller {

    function Task() {
        parent::__construct();
        $this->debug = false;
        $this->debug_manual = true;
        $this->load->library('parser');
        $this->load->model('user');
        $this->load->model('bpm');
        $this->load->model('app');
        $this->load->model('msg');
        $this->load->helper('workflow');
        $this->user->authorize();
        //----LOAD LANGUAGE
        $this->lang->load('library', $this->config->item('language'));
    }

    function claim($idwf, $case, $resourceId) {
        $iduser = (int) $this->session->userdata('iduser');
        //---get the user
        $user = $this->user->get_user($iduser);
        $user_groups = $user->group;
        $token = $this->bpm->get_token($idwf, $case, $resourceId);
        //---check if the user is in the assigned groups

        $is_allowed = false;
        //---check if user belong to the group the task is assigned to
        if (isset($token['idgroup'])) {
            foreach ($user_groups as $thisgroup) {
                if (in_array($thisgroup,$token['idgroup']))
                    $is_allowed = true;
            }
        }
        if (!isset($token['assign'])) {
            if ($is_allowed) {
                $token['assign'] = array((int) $this->session->userdata('iduser'));
                $this->bpm->save_token($token);
            }
        }
    }

    function refuse($idwf, $case, $resourceId) {
        $iduser = (int) $this->session->userdata('iduser');
        //---get the user
        $user = $this->user->get_user($iduser);
        $user_groups = $user->group;
        $token = $this->bpm->get_token($idwf, $case, $resourceId);
        
        //---check if the user is in the assigned array
        if (in_array($iduser, $token['assign'])) {
            $token['assign'] = array_diff($token['assign'], array($iduser));
            $token=array_filter($token);
            $this->bpm->save_token($token);
        }
    }

}

?>