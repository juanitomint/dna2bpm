<?php

class util extends MX_Controller {

    function __construct() {
        parent::__construct();
        $this->load->library('parser');
        $this->load->model('user');
        $this->load->model('group');
        $this->idu = (int) $this->session->userdata('iduser');
    }

    function Index() {
        
    }

    //---return a json representation of a user.
    function get_user() {
        $segments = $this->uri->segment_array();
        $debug = (in_array('debug', $segments)) ? true : false;
        $iduser = ($this->input->post('idu')) ? $this->input->post('idu') : 1;
        $user = $this->user->get_user($iduser);
        //---Available
        /* {"_id":{"$id":"4e82d4263ad5e0956f00004f"},
         * "idu"
         * "idgroup"
         * "nick"
         * "passw"
         * "name"
         * "lastname"
         * "idnumber"
         * "birthDate"
         * "perm"
         * "checkdate"
         * "lastacc"
         * "id"
         * "group"
         * 
         */
        $rtnU['idu']=$user->idu;
        $rtnU['nick']=$user->nick;
        $rtnU['name']=$user->name;
        $rtnU['lastname']=$user->lastname;
        $rtn=array('rows'=>$rtnU);
        if (!$debug) {
            header('Content-type: application/json;charset=UTF-8');
            echo json_encode($rtn);
        } else {
            var_dump($rtn);
        }
    
        
    }

}

?>
