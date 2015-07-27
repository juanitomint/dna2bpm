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
        
                
                
        if (!isset($myquery['start']) || !isset($myquery['end'])) {
        	die("Please provide a date range.");
        }

        $query=array(
            'start'=>array('$gte'=>$myquery['start']),
            'end'=>array('$lte'=>$myquery['end']),
            '$or'=>array(array('idu'=>$this->idu),array('group'=>1))
        );
        //$myuser=$this->user->get_user($this->idu);
                
         
        $fields=array('title','start','end','tags','allDay','idu','color');
        //$query=array('start'=>array('$gte'=>'2015-06-08','$lte'=>'2015-06-20'));

        //$this->db->debug=true;
        $this->db->where($query,true);
        $this->db->select($fields);
        // $this->db->order_by($sort);
        $rs = $this->db->get($this->container)->result_array();

        $rs2=array();
        foreach($rs as $k=>$v ){
             $v['_id']=(string)$v['_id'];
             if(isset($v['idu']) && ($v['idu']==$this->idu)){
                 if(!isset($v['color']))
                    $v['color']=$this->config->item('color_events');
             }else{
                 $v['color']=$this->config->item('color_group_events');
             }
             $rs2[]=$v;
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
        $author=$this->user->getuser($this->idu);
        if(isset($rs[0]['group']))$rs[0]['group']=implode(',',$rs[0]['group']);
        $rs[0]['author']=$author->lastname.', '.$author->name;
        return $rs[0];
    }
    
    //=== delete event
    function delete_event($myid){
        $mongoID=new MongoID($myid);
        $query=array('_id'=>$mongoID,'idu'=>$this->idu);
        $this->db->where($query);
        echo $this->db->delete($this->container);
    }
    
    //=== update event
    
    function update_event($event){
        $mongoID=new MongoID($event['_id']);
        $query=array('_id'=>$mongoID,'idu'=>$this->idu);
          

        if(!empty($event['start']))$data['start']=$event['start'];
        if(!empty($event['end']))$data['end']=$event['end'];
        if(!empty($event['body']))$data['body']=$event['body'];
        if(!empty($event['group']))$data['group']=$event['group'];
        if(!empty($event['color']))$data['color']=$event['color'];
        $data['allDay']=$event['allDay'];
        
        $fechas=explode('-',$event['intervalraw']);
        $start=date_create_from_format("d/m/Y H:i", trim($fechas[0]));
        $end=date_create_from_format( "d/m/Y H:i", trim($fechas[1]));
        $event['start']=date_format($start,'Y-m-d').'T'.date_format($start,'H:i').':00.000Z'; 
        $event['end']=date_format($end,'Y-m-d').'T'.date_format($end,'H:i').':00.000Z';  


         $this->db->where($query);
         echo $this->db->update($this->container,$data);
    }      

   //=== Creacion evento
   
    function create_event($event){
        if(empty($event))return false;
        $fechas=explode('-',$event['intervalraw']);
        $start=date_create_from_format("d/m/Y H:i", trim($fechas[0]));
        $end=date_create_from_format( "d/m/Y H:i", trim($fechas[1]));
        
        $event['start']=date_format($start,'Y-m-d').'T'.date_format($start,'H:i').':00.000Z'; 
        $event['end']=date_format($end,'Y-m-d').'T'.date_format($end,'H:i').':00.000Z';  

        $event['idu']=$this->idu;
        return $this->db->insert($this->container, $event); 
        
    }

}
