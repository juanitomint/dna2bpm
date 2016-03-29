<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Logout extends MX_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->library('parser');
    }

    function Index() {
        $this->session->unset_userdata('loggedin');
        $this->session->sess_destroy();
        redirect(base_url() . 'user/login');
    }

}

?>
