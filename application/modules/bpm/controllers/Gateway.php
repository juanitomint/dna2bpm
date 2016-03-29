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
class Gateway extends CI_Controller {

    function __construct() {
        parent::__construct();
        session_start();
        if (isset($_REQUEST['url'])) {
            $url =  base64_decode(urldecode($_REQUEST['url']));
            $_SESSION['idu'] = $this->session->userdata('iduser');
            //echo $url;exit;
            redirect($url);
        } else {
            show_error('No url passed');
        }
            exit;
    }

}

/* End of file welcome.php */
/* Location: ./system/application/controllers/welcome.php */