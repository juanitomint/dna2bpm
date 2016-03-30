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
 

 
class Calendar extends MX_Controller {

    function __construct() {
        parent::__construct();


        date_default_timezone_set('UTC');
        //---base variables
        $this->base_url = base_url();
        $this->module_url = base_url() . $this->router->fetch_module() . '/';
        $this->load->library('parser');
        $this->idu = (int) $this->session->userdata('iduser');
        $this->user_can_create=$this->user->has("root/modules/calendar/controllers/calendar/create_event");
        $this->user_can_update=$this->user->has("root/modules/calendar/controllers/calendar/update_event");
        $this->user_can_delete=$this->user->has("root/modules/calendar/controllers/calendar/delete_event_by_id");
        $this->user_can_create_group_events=$this->user->has("root/modules/calendar/controllers/calendar/create_group_events");
        $this->user_can_delete_other_events=$this->user->has("root/modules/calendar/controllers/calendar/delete_other_events");
        $this->user_can_update_other_events=$this->user->has("root/modules/calendar/controllers/calendar/update_other_events");
        
        

        $this->load->config('calendar/config');
        $this->load->model('calendar/calendar_model');
        //Lang
        $this->lang->load('calendar/calendar', $this->config->item('language'));
        
        $this->user->authorize();
        //---Output Profiler
        //$this->output->enable_profiler(TRUE);
        //error_reporting(E_ALL);

    }
    
   
    function Index(){
      if($this->user_can_create){
          Modules::run('dashboard/dashboard', 'calendar/json/dashboard_edit.json');
      }else{
          Modules::run('dashboard/dashboard', 'calendar/json/dashboard_view.json');
      }
    }
    
    //====== Print calendar 
    
    function widget_calendar(){
         $data=array();
         echo  $this->parser->parse('calendar/calendar',$data ,true);
    }
    

    //====== Print Groups Panel
    
    function widget_groups(){
        $data=array();
        $groups=$this->get_my_groups();
        $visible="<a  href='#' data-visible='true'><i class='fa fa-eye'></i></a>";
        $hidden="<a  href='#' data-visible='false'><i class='fa fa-eye-slash'></i></a>";
        $config=$this->get_user_config();
        $haystack=(isset($config['exclude']) && ($config['exclude']))?($config['exclude']):(array());
        
         foreach($groups as $id=>$group){
            $data['groups'][$id]=$group;
            $data['groups'][$id]['eye']=(in_array($id,$haystack))?($hidden):($visible);  // Recover state for show/hide buttons
             
            if(!empty($group['color']))
                $data['groups'][$id]+=$this->get_colors_ul($group['color']);
                    else
                $data['groups'][$id]+=$this->get_colors_ul();
        }

         echo $this->parser->parse('groups',$data,true);
    } 
    

    //====== Create events
    
    // Print Create event Panel
    
    function widget_create(){
        $start=date('d/m/Y H:00');
        $end=date('d/m/Y H:30');
        $data=array('intervalo'=>"$start - $end");
        //colores
        $colors=$this->get_colors_ul();
        $data+=$colors;
        $data['lang']= $this->lang->language;

        $data['user_can_create_group_events']=$this->current_user_can('create_group_events',$this->idu);
        
        $groups=$this->get_my_groups();
        $data['groups']="";
        foreach($groups as $k=>$mygroup){
          $data['groups'].="<option value='{$mygroup['idgroup']}'>{$mygroup['name']}</option>"; 
        }

        echo $this->parser->parse('create',$data,true);

    } 
    
    //=== Create  buttons groups on ajax call
    
    function get_option_button(){
     $sel=$this->input->post('sel');
     $myuser=$this->user->get_user($this->idu);
     $groups=$this->get_my_groups();
     $ret="";
              
     if($sel=='all'){
         foreach($groups as $g){
              $ret.= "<button type='button' data-groupid='{$g['idgroup']}' class='btn btn-default btn-xs'><i class='fa fa-times-circle'></i> {$g['name']}</button>";
         }
     }else{
         // just one
          foreach($groups as $g){
              if($g['idgroup']==$sel){
              $ret.= "<button type='button' data-groupid='{$g['idgroup']}' class='btn btn-default btn-xs'><i class='fa fa-times-circle'></i> {$g['name']}</button>";
              break;
              }
          }
     }
     echo $ret;
    }
    
    //=== get the events for the calendar
    
    function get_events(){
        $q=parse_url($_SERVER['REQUEST_URI']);
        $query=$q['query'];
        parse_str($query,$myquery);
        $events=$this->calendar_model->get_events($myquery);
        echo json_encode($events);
    }
    
    // used by modal window to populate form
    
    function get_event_by_id(){
        
        $myid=$this->input->post('id');
        $data=$this->calendar_model->get_event_by_id($myid);
        $event['title']=$data['title'];
        $data['user_can_delete']=$this->current_user_can('delete',$data['idu'])||$this->current_user_can('delete_other_events');
        $data['user_can_update']=$this->current_user_can('update',$data['idu'])||$this->current_user_can('update_other_events');
        $start=date('d/m/Y H:i',strtotime($data['start']));
        $end=date('d/m/Y H:i',strtotime($data['end']));
        $data['intervalo']="$start - $end";
        $data['allDay']=(isset($data['allDay']) && $data['allDay'])?('checked'):('');
        $data['user_can_create_group_events']=$this->current_user_can('create_group_events',$this->idu);
        //Color Picker
        $mycolor=(isset($data['color']))?($data['color']):('void');
        $colors=$this->get_colors_ul($mycolor);
        $data+=$colors;
        $data['lang']= $this->lang->language;
        //groups
        $groups=$this->get_my_groups();
        $data['groups']="";
        foreach($groups as $k=>$mygroup){
          $data['groups'].="<option value='{$mygroup['idgroup']}'>{$mygroup['name']}</option>"; 
        }
        $data['group_box']="";
          foreach($data['group'] as $group){
             $name=$groups[$group]['name'];
             $groupid=$groups[$group]['idgroup'];
              $data['group_box'].= "<button type='button' data-groupid='{$groupid}' class='btn btn-default btn-xs'><i class='fa fa-times-circle'></i> $name ($groupid)</button>";
          }
    
        // $data['group_box']=print_r($groups,true);
        $event['body']=$this->parser->parse('calendar/modal',$data,true,true);

        echo json_encode($event);
    }

    
    //===  Remove event
    // @todo - only owners can delete  - add admin 
    
    function delete_event(){
        $myid=$this->input->post('id');
        if(!isset($myid))return;
        $admin=$this->current_user_can('delete_other_events');
        echo $this->calendar_model->delete_event($myid,$admin);
    }
    
    //===  update event 

    function update_event(){
        $event=$this->input->post('event');
        if(!isset($event))return;
        
        $admin=$this->current_user_can('update_other_events');
        $event['allDay']=($event['allDay']=='true')?(true):(false);
        $toInt = function ($n){
          return (int)$n;
        };
        
        if(isset($event['title']))
            if(isset($event['group']) && is_array($event['group'])){
                $event['group']=array_map($toInt,array_unique($event['group']));
            }else{
                $event['group']=array();
            }
         echo $this->calendar_model->update_event($event,$admin);
    }   
    
    
    //=== Create event
   
    private function create_event($myevent){
      return $this->calendar_model->create_event($myevent);
    }

    // wrapper for ajax
    
    function create_event_wrapper(){
        $event = $this->input->post('event');
        if(!isset($event))return;
                
        $allDay=($event['allDay']=='true')?(true):(false);
        $myevent=array(
            'title'=>$event['title'],
            'body'=>$event['body'],
            'intervalraw'=>$event['interval'],
            'allDay'=>$allDay
        );
        
        if($event['color']!='void')$myevent['color']=$event['color'];
        // groups
        $groups=(isset($event['group'])&&is_array($event['group']))?($event['group']):(array());
        $toInt = function ($n){
          return (int)$n;
        };
        $myevent['group']=array_map($toInt,array_unique($groups));

        echo $this->create_event($myevent);
        
    }
    
    //=== Config options
    
    function groups_set_color(){
        $config = $this->input->post('config');
        if(!isset($config))return;
                
        $colors=array($config['idgroup']=>$config['color']);
        echo $this->calendar_model->groups_set_color($colors);
    }
    
    //=== Get config
    private function get_user_config(){
        $config=$this->calendar_model->get_user_config();
        return $config;
    }
    
    function print_get_user_config(){
        $config=$this->get_user_config();
        echo json_encode($config);
    }
    
//=========== DUMMYS for RBAC
    
    function create_group_events(){
        // if on user can create events shared 
    }   
    
    function delete_other_events(){
        // user can delete events from other guys in his groups 
    }  
    
    function update_other_events(){
        // user can update events from other guys in his groups 
    }  
    
    

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
    }elseif($action=='delete_other_events'){
        return ($this->user_can_delete_other_events)?(true):(false);
    }elseif($action=='update_other_events'){
        return ($this->user_can_update_other_events)?(true):(false);
    }
    else{
        return false;
    }
}




// print color ul dropdown

private function get_colors_ul($color='void'){
        //colores
        $colors=$this->config->item('colors');
        $selected_rgb=($color=='void')?('#ccc'):($color);
        $fa=($color=='void')?('fa-th'):('fa-square');
        $data['first_color_anchor']="<a  href='#' data-color='$color' style='color:$selected_rgb'><i class='fa $fa'></i> </a>";
        $data['first_color']=$color;
        $data['ul']='<li><a  href="#" data-color="void" style="color:#ccc"><i class="fa fa-th"></i> no color</a></li>';
        foreach($colors as $name=>$rgb){
          $data['ul'].='<li><a style="color:'.$rgb.'" href="#" data-color="'.$rgb.'"><i class="fa fa-square"></i> '.$name.'</a></li>'; 
        }
        return $data;
}

// return groups for this user    
 private function get_my_groups(){
    $myuser=$this->user->get_user($this->idu);
    $myconfig=$this->calendar_model->get_user_config();
   
    $groups=is_array($myuser->group)?($myuser->group):(array());
    $ret=array();
    
    foreach($groups as $k=>$idgroup){
        $mygroup=$this->group->getbyid($idgroup);
        if(!empty($myconfig['colors'][$idgroup]))$mygroup['color']=$myconfig['colors'][$idgroup];
        
        $ret[$idgroup]=$mygroup;
    }
    
    return $ret;

}
   
   


    
    
  
    
}//class