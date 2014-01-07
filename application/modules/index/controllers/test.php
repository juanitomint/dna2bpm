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
        $this->load->model('index/menu');
    }

    /*
     * Main function if no other invoked
     */

    function Index() {
        $m = $this->menu->get_repository();
        var_dump($m);
        var_dump($this->menu->explodeExtTree($m));
    }

}
