<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/**
 * isloggedin
 * 
 * Description of the class isloggedin
 * This controller allow you to check from client apps if the user 
 * is logged or not and if not you can then reload the page or take any 
 * other action
 * 
 * @author Juan Ignacio Borda <juanignacioborda@gmail.com>
 * @date   Mar 29, 2014
 * @example /user/islogeddin
 * retuns:
 * {
 *    "isloggedin": false
 *  }
 *  
 */
class isloggedin extends CI_Controller {

    function __construct() {
        parent::__construct();
        $result = ($this->session->userdata('loggedin')) ? array('isloggedin' => true) : array('isloggedin' => false);
        $this->output->set_content_type('json','utf-8');
        echo json_encode($result);
        exit;
    }

}

/* End of file isloggedin */
