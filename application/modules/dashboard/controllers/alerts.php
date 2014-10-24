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
class Alerts extends MX_Controller {

    function __construct() {
        parent::__construct();
        $this->load->model('user/user');
        $this->load->config('dashboard/config');
        $this->load->library('parser');
        $this->load->model('alerts_model');

        //---base variables
        $this->base_url = base_url();
        $this->module_url = base_url() . $this->router->fetch_module() . '/';
        $this->user->authorize();
        //----LOAD LANGUAGE
        $this->lang->load('library', $this->config->item('language'));
        $this->idu = (int) $this->session->userdata('iduser');
    }

    function get_my_alerts() { 	
    	$dashboard=$this->session->userdata('json');
    	$user = $this->user->get_user($this->idu);
    	$target=$user->group;
    	$target[]=$dashboard;
    	$customData['lang'] = $this->lang->language;

     	$query=array('target'=>array('$in'=>$target),'read'=>array('$ne'=>$this->idu));
      	$alerts=$this->alerts_model->get_alerts_by_filter($query);
      	$customdata['my_alerts']=iterator_to_array($alerts);
      	$customdata['Nick']="test";
//       	var_dump(iterator_to_array($alerts));
//       	exit();
      	$q=$alerts->count();
       	return ($q>0)?( $this->parser->parse('dashboard/widgets/alerts', $customdata, true, false)):('');

    }
    
    function dismiss() {
    	$id=$this->input->post('id');
    	$this->alerts_model->dismiss($id);
    }
    
    function Index(){
    	
    }

}

/* End of file welcome.php */
/* Location: ./system/application/controllers/welcome.php */