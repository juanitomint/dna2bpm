<?php

/**
 * Description of menu
 *
 * @author juanb
 * @date   Jan 7, 2014
 * 
 */
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Test extends MX_Controller {

    function __construct() {
        parent::__construct();
        $this->load->model('menu/menu_model');
        $this->load->helper('ext');
        
    }

    /*
     * Main function if no other invoked
     */

    function Index() {
        //ini_set('xdebug.var_display_max_depth', 16);
        $menu=modules::run('menu/menu/get_menu');
        echo $menu;
    }
}