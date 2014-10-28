<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/**
 * test
 * 
 * Description of the class
 * 
 * @author Juan Ignacio Borda <juanignacioborda@gmail.com>
 * @date    Oct 27, 2014
 */
class test extends MX_Controller {

    function __construct() {
        parent::__construct();
        $this->load->library('parser');
    }

    function Index() {
        
    }

    function tile_toast() {
        $data['lang'] = $this->lang->language;
        $data['base_url'] = $this->base_url;
        $data['module_url'] = $this->module_url;
        $files = Modules::run('test/Toast_all/_get_test_files');
        $data['number']=count($files);
        $data['title']="Run Tests";
        $this->parser->parse('dashboard/tiles/tile-green', $data, false, true);
    }

}

/* End of file welcome.php */
/* Location: ./system/application/controllers/welcome.php */