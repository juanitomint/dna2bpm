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
        $this->load->library('menu/ui');
    }

    /*
     * Main function if no other invoked
     */

    function Index() {
        //ini_set('xdebug.var_display_max_depth', 16);
        $cpData['menu1'] = modules::run('menu/menu/get_menu', '0', 'nav btn-group');
        $cpData['menu2'] = modules::run('menu/menu/get_menu_bs', '0', 'role="navigation" class="nav"');
        $this->parser->parse('bootstrap.ui.php',$cpData,false,true);
        //echo $cpData['menu1'];
        //echo $cpData['menu2'];
    }

}