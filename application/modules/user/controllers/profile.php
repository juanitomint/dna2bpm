<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/**
 * profile
 * 
 * Description of the class
 * 
 * @author Juan Ignacio Borda <juanignacioborda@gmail.com>
 * @date    Apr 15, 2013
 */
class Profile extends MX_Controller {

    function __construct() {
        parent::__construct();
        $this->load->library('parser');
        $this->load->model('user');
        $this->user->authorize();
        //---base variables
        $this->base_url = base_url();
        $this->module_url = base_url() . 'user/';
        //----LOAD LANGUAGE
        $this->lang->load('library', $this->config->item('language'));
        $this->idu = (float) $this->session->userdata('iduser');
    }

    /*
     * Edit /view user profile
     */
    function Index() {
        echo "INDEX";
    }

    /*
     * Edit user profile
     */
    function Edit() {
        echo "EDIT";
    }

    /*
     * Save Profile data uses $this->user->save($data);
     */
    function Save() {
        echo "SAVE";
    }

    /*
     * View user Profile
     */
    function View() {
        echo "VIEW";
    }

}

/* End of file welcome.php */
/* Location: ./system/application/controllers/welcome.php */