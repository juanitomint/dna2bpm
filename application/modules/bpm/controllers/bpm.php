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
class bpm extends CI_Controller {

    function __construct() {
        parent::__construct();
        $this->load->config('config');
        
    }

    function Index() {
        if($this->config->item('run_mode')=='development'){
            $links=array(
                'Get tasks manager/get_tasks/$user/$filter' => 'manager/get_tasks',
                'BPM Browser' => 'browser',
                'Fix stencil.url' => 'repository/repair_stencil_path',
            );
            foreach($links as $text=>$url){
                echo "<a href='./bpm/".$url."'>$text</a><hr/>";
            }
        }
    }

}

/* End of file bpm */
/* Location: ./system/application/controllers/welcome.php */