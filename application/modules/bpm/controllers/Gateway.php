<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/**
 * gateway
 *
 * Description of the class
 *
 * @author Juan Ignacio Borda <juanignacioborda@gmail.com>
 * @date    Jul 23, 2014
 */
class Gateway extends MX_Controller {

    function __construct() {
        parent::__construct();

    }
    function index(){
        session_start();
        if ($this->input->get('url')) {
            $url =  base64_decode(urldecode($this->input->get('url')));
            $_SESSION['idu'] = $this->session->userdata('iduser');
            //echo $url;exit;
            redirect($url);
        } else {
            show_error('No url passed');
        }
            exit;
    }

}