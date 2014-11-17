<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');
/**
 * ui
 * 
 * This class renders the graphical elements to dashboards
 * 
 * @author Juan Ignacio Borda <juanignacioborda@gmail.com>
 * @date    Jun 16, 2014
 */
class Inboxui extends MX_Controller {

    function __construct() {
        parent::__construct();
        $this->load->library('parser');
    }

    function Index() {
     echo $this->tile('tile_inbox');  
        
    }
    
    function widget($widget){
        
    }
    
    function tile($widget){
        $args=array_slice($this->uri->segments,4);
        if(method_exists($this, $widget)){
            return call_user_func_array(array($this,$widget), $args);
        }
    }
    
    function tile_inbox(){
        $data['title']='New Messages';
        $data['number']=  rand(0, 200);
        $data['more_info_']=  rand(0, 200);
        return $this->parser->parse('bpm/tiles/tile-yellow', $data, true, true);
        
    }
}

/* End of file welcome.php */
/* Location: ./system/application/controllers/welcome.php */