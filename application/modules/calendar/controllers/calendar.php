<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');
/**
 * Actualiza los archivos segun la rama configurada
 * 
 * @autor Fojo Gabriel 
 * 
 * @version 	1.0 
 * 
 * 
 */
 
class calendar extends MX_Controller {

    function __construct() {
        parent::__construct();

        $this->user->authorize();
        //---base variables
        $this->base_url = base_url();
        $this->module_url = base_url() . $this->router->fetch_module() . '/';
        $this->load->library('parser');
        $this->idu = (int) $this->session->userdata('iduser');
        $this->user_can_create=$this->user->has("root/modules/calendar/controllers/calendar/create_event");
        $this->user_can_update=$this->user->has("root/modules/calendar/controllers/calendar/update_event");
        $this->user_can_delete=$this->user->has("root/modules/calendar/controllers/calendar/delete_event_by_id");
        $this->user_can_create_group_events=$this->user->has("root/modules/calendar/controllers/calendar/create_group_events");

        $this->load->config('calendar/config');
        $this->load->model('calendar/calendar_model');
        //Lang
        $this->lang->load('calendar', $this->config->item('language'));
        //---Output Profiler
        //$this->output->enable_profiler(TRUE);
    }
    
   
    function Index(){
      if($this->user_can_create){
          Modules::run('dashboard/dashboard', 'calendar/json/dashboard_edit.json');
      }else{
          Modules::run('dashboard/dashboard', 'calendar/json/dashboard_view.json');
      }

    }
    
    function panel_calendar(){
         $data=array();
         echo  $this->parser->parse('calendar',$data ,true);

    }
    
   function panel_create(){
        $start=date('d/m/Y H:00');
        $end=date('d/m/Y H:30');
        $data=array('intervalo'=>"$start - $end");
        //colores
        $colors=$this->get_colors_ul();
        $data+=$colors;
        $data['lang']= $this->lang->language;

         $data['user_can_create_group_events']=$this->current_user_can('create_group_events',$this->idu);
         echo $this->parser->parse('create',$data,true);

    } 
    

//== Get events
    
    function get_events(){
        $q=parse_url($_SERVER['REQUEST_URI']);
        $query=$q['query'];
        parse_str($query,$myquery);
        $events=$this->calendar_model->get_events($myquery);
        echo json_encode($events);
    }
    
    function get_event_by_id(){
        $myid=$this->input->post('id');
        $data=$this->calendar_model->get_event_by_id($myid);
        $event['title']=$data['title'];
        $data['user_can_delete']=$this->current_user_can('delete',$data['idu']);
        $data['user_can_update']=$this->current_user_can('update',$data['idu']);
        $start=date('d/m/Y H:i',strtotime($data['start']));
        $end=date('d/m/Y H:i',strtotime($data['end']));
        $data['intervalo']="$start - $end";
        $data['allDay']=(isset($data['allDay']) && $data['allDay'])?('checked'):('');
        $data['user_can_create_group_events']=$this->current_user_can('create_group_events',$this->idu);
        //Color Picker
        $colors=$this->get_colors_ul();
        $data+=$colors;
        $data['lang']= $this->lang->language;
        $event['body']=$this->parser->parse('calendar/modal',$data,true,true);

        echo json_encode($event);
    }

//=== Delete   

    function delete_event(){
        $myid=$this->input->post('id');
        echo $this->calendar_model->delete_event($myid);
    }
    
//=== Update     

    function update_event(){
        $event=$this->input->post('event');
        $event['allDay']=($event['allDay']=='true')?(true):(false);
        $event['group']=(isset($event['group']))?($this->parse_groups($event['group'])):(array());
        echo $this->calendar_model->update_event($event);

    }   
    
    
//=== Create 
   
    function create_event($myevent){
       echo $this->calendar_model->create_event($myevent);
    }

    function create_event_wrapper(){
        $event = $this->input->post('event');
        $allDay=($event['allDay']=='true')?(true):(false);
        $myevent=array(
            'title'=>$event['title'],
            'body'=>$event['body'],
            'intervalraw'=>$event['interval'],
            'allDay'=>$allDay,
            'color'=>$event['color']
        );
       $myevent['group']=(isset($event['group']))?($this->parse_groups($event['group'])):(array());

       $this->create_event($myevent);
    }
    

//=========== PRIVATE FUNCTIONS

//=== Can user edit/create/delete this event? 
private function current_user_can($action,$idu=null){
    if($action=='delete'){
        return ($idu==$this->idu && $this->user_can_delete)?(true):(false);
    }elseif($action=='update'){
        return ($idu==$this->idu && $this->user_can_update)?(true):(false);
    }elseif($action=='create'){
        return ($idu==$this->idu && $this->user_can_create)?(true):(false);
    }elseif($action=='create_group_events'){
        return ($idu==$this->idu && $this->user_can_create_group_events)?(true):(false);
    }else{
        return false;
    }
}

private function parse_groups($mygroups){

    $pattern = '/(\d+,)+\d$/';
    $match=preg_match_all($pattern, $mygroups);
    
    if($match=='1'){
        $groups=explode(",",$mygroups);
        $func = function($value) {
        return (int)$value;
        };
        
        return array_map($func, $groups);

    }else{
        return array();
    }
}

private function get_colors_ul(){
        //colores
        $colors=$this->config->item('colors');
        $selected=$this->config->item('color_events');
        $data['first_color']=$colors[$selected];
        $data['ul']='';
        foreach($colors as $name=>$rgb){
           $data['ul'].='<li><a style="color:'.$rgb.'" href="#" data-color="'.$rgb.'"><i class="fa fa-square"></i> '.$name.'</a></li>'; 
        }
        return $data;
}
    
    
    

    
    
    
}