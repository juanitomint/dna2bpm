<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');
/**
 * test
 * 
 * Description of the class
 * 
 * @author Juan Ignacio Borda <juanignacioborda@gmail.com>
 * @date    May 28, 2014
 */
class test extends MX_Controller {

    function __construct() {
        parent::__construct();
        $this->load->model('user/user');
    }

    function Index() {
     $module="bpm";
     $controller='manager';
     $function='show_tasks';
     
     //$this->load->module($module);
    //$widget=$this->$module->run($controller.'/'.$function);
    $widget=Modules::run($module.'/'.$controller.'/'.$function);
     var_dump($widget);
        
    }
    


}

/* End of file welcome.php */
/* Location: ./system/application/controllers/welcome.php */