<?php

class calendar_model extends CI_Model {

    function __construct() {
        parent::__construct();
        $this->idu = (int) $this->session->userdata('iduser');
        $this->load->library('cimongo/cimongo');
        $this->db = $this->cimongo;
        $this->container="container.calendar";
        $this->load->config('calendar/config');
    }
    
    //=== Entrega eventos entre dos fechas
    
    function get_events($myquery){
        
        $myuser=$this->user->get_user($this->idu);       
        $groups=(is_array($myuser->group))?($myuser->group):(array());     
        if (!isset($myquery['start']) || !isset($myquery['end'])) 
        	die("Please provide a date range.");
        
        $query=array(
            'start'=>array('$gte'=>$myquery['start']),
            'end'=>array('$lte'=>$myquery['end']),
            '$or'=>array(array('idu'=>$this->idu),array('group'=>array('$in'=>$groups)))
        );
        
        //=== hide groups 
        $groups=array();
        if(isset($myquery['exclude'])){
            // New setting - save
            while($mygroup=array_pop($myquery['exclude']['hide_groups']))
            if(is_numeric($mygroup)){
                $groups[]=(int)$mygroup;
            }
            $query['group']=array('$nin'=>$groups);
        }
        
        // Save exclude config
        $query_groups=array('class'=>'config','uid'=>$this->idu);
        $this->db->where($query_groups);
        $data['exclude']=$groups;
        $this->db->update($this->container,$data);
            


        $fields=array('title','start','end','tags','allDay','idu','color','group');
        //$query=array('start'=>array('$gte'=>'2015-06-08','$lte'=>'2015-09-20'));

        //$this->db->debug=true;
        $this->db->where($query,true);
        $this->db->select($fields);
        // $this->db->order_by($sort);
        $rs = $this->db->get($this->container)->result_array();

        // colors list
        
        $myconfig=$this->get_user_config();
        $colors=(isset($myconfig['colors']))?($myconfig['colors']):(array());

        //--- events loop
        $rs2=array();
        //$this->config->item('color_events'); //default
        
        foreach($rs as $k=>$evento ){
             $evento['_id']=(string)$evento['_id'];
             $gcolor=null;

             $default_group_color=$this->config->item('color_group_events');
                // group events 
                if( count($evento['group']) ){
                    //  var_dump($evento['group']);
                     $gcolor=$default_group_color;
                    // has group  - use group color
                    foreach($evento['group'] as $groupid){
                        if(isset($colors[$groupid])){
                            $gcolor=$colors[$groupid];
                            break;
                        }
                    }
                }

                if(isset($gcolor)){
                    // group event - 
                    $evento['color']=$gcolor;
                }

             $rs2[]=$evento;
        }
    
        return $rs2;

    }
    
    //=== get
    function get_event_by_id($id){
        $mongoID=new MongoID($id);
        $query=array('_id'=>$mongoID);
        $fields=array('body','title','start','end','idu','allDay','group','color');
        $this->db->select($fields);
        $this->db->where($query,true);
        $rs=$this->db->get($this->container)->result_array();
        $author=$this->user->getuser($rs[0]['idu']);
       // if(isset($rs[0]['group']))$rs[0]['group']=implode(',',$rs[0]['group']);
        $rs[0]['author']=$author->lastname.', '.$author->name;
        return $rs[0];
    }
    
    //=== delete event
    function delete_event($myid,$admin=false){
        $mongoID=$this->create_mongoID($myid);
        if($mongoID==false)return;
        $query=($admin)?(array('_id'=>$mongoID)):(array('_id'=>$mongoID,'idu'=>$this->idu));
        
        $this->db->where($query);
        echo $this->db->delete($this->container);
    }
    
    //=== update event
    
    function update_event($event,$admin=false){
        $mongoID=new MongoID($event['_id']);
        $query=($admin)?(array('_id'=>$mongoID)):(array('_id'=>$mongoID,'idu'=>$this->idu));
        unset($event['_id']);
       
         if(isset($event['title']))// came from modal
             if(empty($event['group']))$event['group']=array();
        
  
         // Fechas
        if(isset($event['intervalraw'])){
            $fechas=explode('-',$event['intervalraw']);
            $start=date_create_from_format("d/m/Y H:i", trim($fechas[0]));
            $end=date_create_from_format( "d/m/Y H:i", trim($fechas[1]));
            $event['start']=date_format($start,'Y-m-d').'T'.date_format($start,'H:i').':00.000Z'; 
            $event['end']=date_format($end,'Y-m-d').'T'.date_format($end,'H:i').':00.000Z';  
        }



         $this->db->where($query);
         echo $this->db->update($this->container,$event);
    }      

   //=== Creacion evento
   
    function create_event($event){

        if(empty($event))return false;
        if(empty($event['intervalraw']))return false;
        // Fechas
        $fechas=explode('-',$event['intervalraw']);
        $start=date_create_from_format("d/m/Y H:i", trim($fechas[0]));
        $end=date_create_from_format( "d/m/Y H:i", trim($fechas[1]));
        $event['start']=date_format($start,'Y-m-d').'T'.date_format($start,'H:i').':00.000Z'; 
        $event['end']=date_format($end,'Y-m-d').'T'.date_format($end,'H:i').':00.000Z';  

        $event['idu']=$this->idu;

        return $this->db->insert($this->container, $event); 
        
    }
    
    
    //=== Get Settings
    
    // function get_settings($uid){
    //     $query=array('class'=>'config','uid'=>$uid);
    //     $this->db->where($query,true);
    //     $rs=$this->db->get($this->container)->result_array();
    //     if(empty($rs))
    //         return array();
    //     else
    //         return $rs[0];
    
    // }
    
    //=== Create config if does not exist
    
    function get_user_config(){
        $data=array(
            'uid'=>$this->idu,
            'class'=>'config'
        );
        $query=array('class'=>'config','uid'=>$this->idu);
        $this->db->where($query);
        $rs=$this->db->get($this->container)->result_array();  
       // return $rs;
        if(empty($rs)){
            // create
            $this->db->insert($this->container, $data);  
            return $data;
        }else{
            return $rs[0];
        }

    }

    //=== Groups 

    function groups_set_color($colors){
      
        $myconfig=$this->get_user_config();
        $data['colors']=$colors;
        if(isset($myconfig['colors']))
            $data['colors']+=$myconfig['colors'];

        $query=array('class'=>'config','uid'=>$this->idu);
        $this->db->where($query);

        echo $this->db->update($this->container,$data);

    }
    
    //=== check ID
    
    function create_mongoID($myid){
        try {
            $mongoID=new MongoID($myid);
        } catch (Exception $e) {
            return false;
        }
        return $mongoID;
    }

    
    

}
