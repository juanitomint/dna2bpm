<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/**
 * bpm
 * 
 * Description of the class bpm
 * 
 * @author Juan Ignacio Borda <juanignacioborda@gmail.com>
 * @date   Feb 10, 2013
 */
class bpm extends MX_Controller {

    function __construct() {
        parent::__construct();
        $this->load->config('config');
        //---base variables
        $this->base_url = base_url();
        $this->module_url = base_url() . $this->router->fetch_module() . '/';
        $this->user->authorize();
    }

//@todo remove this function
    function Index() {
        if ($this->config->item('run_mode') == 'development') {
            $links = array(
                'Show tasks manager/show_tasks/$user/$filter' => 'manager/show_tasks',
                'BPM Browser' => 'browser',
                'Fix stencil.url' => 'repository/repair_stencil_path',
            );
            foreach ($links as $text => $url) {
                echo "<a href='".$this->module_url. $url . "'>$text</a><hr/>";
            }
        }
    }

}

/* End of file bpm */
/* Location: ./system/application/controllers/welcome.php */