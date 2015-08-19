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
        $this->lang->load('alerts', $this->config->item('language'));
        $this->idu = $this->user->idu;
    }

    function get_my_alerts() { 	

    	$dashboard=$this->session->userdata('json');
    	$user = $this->user->get_user($this->idu);
    	$target=$user->group;
    	$target[]=$dashboard;
    	$customdata['lang'] = $this->lang->language;

      //	$customdata['my_alerts']=$this->alerts_model->get_alerts_by_filter($target);
        $alerts_raw=$this->alerts_model->get_alerts_by_filter($target);
        $alerts=array();
        // Check publish and unpublish
         $now=date('Y-m-d H:i:s');

        if(count($alerts_raw)>0){
          foreach($alerts_raw as $alert){
               if(isset($alert['start_date']) && $alert['start_date']<$now && $alert['end_date']>$now)
                   $alerts[]=$alert;
          }
          $customdata['my_alerts']=$alerts;

          if(count($alerts)>0)
           return $this->parser->parse('dashboard/widgets/alerts', $customdata, true, false);
             else
           return array();
        }else{
           return array();
        }


         //return ($q>0)?( ):('');

       	

    }
    
    function dismiss() {
    	$id=$this->input->post('id');
    	$this->alerts_model->dismiss($id);
    }
    
    function create_alert($alert=array()){
        if(empty($alert)){
            // Ajax call ?
            $alert=$this->input->post('myalert');
            if(empty($alert))$alert=$this->input->get('myalert');
            if(is_null($alert)){
                return false;
            }else{
                if(isset($alert['target']) && !is_array($alert['target'])){
                    $groups=explode(',',$alert['target']);
                    $new_group=array();
                    foreach($groups as $k=>$g){
                        $new_group[]=(is_numeric($g))?((int)$g):($g);
                    }
                    $alert['target']=$new_group;
                    echo $this->alerts_model->create_alert($alert);
                }
            }
        }else{
             // Function call 
            $this->alerts_model->create_alert($alert);
        }    

    }
    
    function create_alert_box(){
        $customdata=array();
        $customdata['lang']= $this->lang->language;

        return $this->parser->parse('dashboard/alert_create_box', $customdata,true, false);
        
    }



 
 
 

}

/* End of file welcome.php */
/* Location: ./system/application/controllers/welcome.php */