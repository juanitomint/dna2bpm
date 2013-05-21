<?php
/**
 * Description of agenda
 *
 * @author Gabriel 
 */
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Agenda extends CI_Model {

    function Agenda() {
         parent::__construct();
         $this->lang->load('main', $this->config->item('language'));
    }

    
/*
 *  Devuelve listado de agendas visibles al usuario
 */
    
    function get_agendas(){
        // Agenda Types normal (0), personal (1), public (2)
        
        $is_admin=$this->session->userdata("AG_isadmin");
        $idu=$this->idu;

         if($is_admin){
            $query = array(
                '$or' =>
                array(
                    array('tipo' => array('$in'=>array(0,2))), // Publics & common agendas
                    array('tipo' =>1 , 'users_w'=>$idu), // Personal Write
                    array('tipo' =>1 , 'users_r'=>$idu ) // Personal Read
                )
            );           
         }else{
             // Not admins
             $query = array(
                '$or' =>
                array(
                    array('users_r'=>$idu ),
                    array('users_w'=>$idu ),
                    array('tipo' => array('$in'=>array(2))) // Publics 
                )
            ); 
 
         }  

         
         // color Profile
        $criteria=array('uid' => $idu);
        $rs2 = $this->mongo->agenda->profiles->findOne($criteria);
              
        $agendas=array();
        $rs = $this->mongo->agenda->agendas->find($query);
        $rs->sort(array('nombre'=>1));


        foreach($rs as $agenda){
            $color=(isset($rs2['colors'][$agenda["id"]]))?($rs2['colors'][$agenda['id']]):("#444");
            $agendas[$agenda["id"]]=array('id'=>$agenda["id"],'parent'=>$agenda["parent"],'nombre'=>$agenda["nombre"],'color'=>$color);
        }
        return $agendas;
        

    }

    // Retuns editables agendas
    function get_editables(){

        $is_admin=$this->session->userdata("AG_isadmin");
        $idu=$this->session->userdata("AG_idu");

        $folders=$this->get_folders();

        if($is_admin){
            $query = array('$or'=>array(
                array('tipo' => array('$ne'=>1),'id' => array('$nin'=>$folders)), // Not personals , not folders
                array('tipo' =>1 , 'users_w'=>$idu,'id' => array('$nin'=>$folders)) // Admin Personal , not folders   
            ));          
         }else{         
             $query =array('users_w'=>$idu,'id' => array('$nin'=>$folders)); //  User write agendas
         }
                               
        $editables=array();
        $rs = $this->mongo->agenda->agendas->find($query)->sort(array('nombre'=>-1));
        foreach($rs as $editable){
            $editables[]=array('id'=>$editable["id"],'parent'=>$editable["parent"],'nombre'=>$editable["nombre"]);
        }
        return $editables;      

    }
    
    // returns agenda by ID
    function get_agenda($id){    
        $query = array('id' => (float)$id);
        $agendas=array();
        $rs = $this->mongo->agenda->agendas->find($query)->limit(1) ;
        foreach($rs as $agenda){
            $agendas=array('id'=>$agenda["id"],'parent'=>$agenda["parent"],'nombre'=>$agenda["nombre"]);
        }
        return $agenda;                            
    }
    
    // Devuelve agendas que son folders
    function get_folders(){
        $query=array('distinct'=>'agendas','key'=>'parent');
        $rs = $this->mongo->agenda->command($query);
        //return $rs['values'];   
        //
        
        $query = array('id'=>array('$in'=>$rs['values']));
        $rs = $this->mongo->agenda->agendas->find($query) ;
        foreach($rs as $agenda){
            $folders[$agenda["id"]]=array('id'=>$agenda["id"],'parent'=>$agenda["parent"],'nombre'=>$agenda["nombre"]);
        }
        return $folders;
    }
  
/**
*     MANEJO DE EVENTOS 
*/
     
    function get_events($min_date,$max_date=null){
           
    $agendas=$this->session->userdata("agendas"); 

    if($max_date===null){
        // Trae eventos del dia o cuyo rango incluya el dia 
        $query = array(
        'start_date' => array('$lte'=>$min_date.' 23:59'),
        'end_date' => array('$gte'=>$min_date.' 00:00'),
        'agendaID' => array('$in'=>$agendas)
        );  
    }else{
        // Trae rango de fechas
        $query = array(
        'start_date' => array('$gte'=>$min_date.' 00:00'),
        'end_date' => array('$lte'=>$max_date.' 23:59') ,
        'agendaID' => array('$in'=>$agendas)
        ); 
    }
                    
      
        $eventos=array();
        $rs = $this->mongo->agenda->eventos->find($query);

        foreach($rs as $ev){
            $eventos[]=array('event_id'=>$ev["event_id"],
                'start_date'=>$ev["start_date"],
                'end_date'=>$ev["end_date"],
                'text'=>$ev["event_name"],
                'detalle'=>$ev["detalle"],
                'lugar'=>$ev["lugar"],
                'tema'=>$ev["tema"],
                'agendaID'=>$ev["agendaID"],
                'autorID'=>$ev["autorID"]);
        }
       return json_encode($eventos);     
        
    }
    
//    function get_events2($my_date){
//           
//    $agendas=$this->session->userdata("agendas"); 
//    $query = array(
//    'start_date' => array('$lt'=>$my_date.' 23:59'),
//    'end_date' => array('$gt'=>$my_date.' 00:00'),
//    'agendaID' => array('$in'=>$agendas)
//    );  
//
//    $eventos=array();
//    $rs = $this->mongo->agenda->eventos->find($query);
//    foreach($rs as $ev){
//        $eventos[]=array('event_id'=>$ev["event_id"],'start_date'=>$ev["start_date"],'end_date'=>$ev["end_date"],'text'=>$ev["event_name"],'detalle'=>$ev["detalle"],'agendaID'=>$ev["agendaID"],'autorID'=>$ev["autorID"]);
//    }
//    return json_encode($eventos);           
//    }
    
    
    function get_event_by_id($id=0){

        $agendas=$this->session->userdata("agendas");  

        $query = array(
            'event_id' => (float)$id,'agendaID'=>array('$in'=>$agendas)
            );
        $eventos=array();
        $rs = $this->mongo->agenda->eventos->find($query);

        foreach($rs as $ev){
        return $ev;
        }


    }
    
function delete_event(){
    $eventID=$this->input->post('eventID',true);

//    print_r($misagendas);
//    echo "-----------<br>";
//    print_r($editables);
//    echo "-----------<br>";
    
    if($this->is_writeable($eventID)){
        $query=array('event_id'=>(double)$eventID);
        $rs = $this->mongo->agenda->eventos->remove($query);
        return str_replace(" ", " $eventID ", $this->lang->line('events_deleted'));
    }else{
        return $this->lang->line('error_permission_delete'); 
    }


}

// Determina si puedo o no modificar o eliminar el evento
function is_writeable($eventID){
    $query = array('event_id' => (float)$eventID);
    $rs = $this->mongo->agenda->eventos->findOne($query);

    $misagendas=array();
    foreach($rs['agendaID'] as $k=>$v){
    $misagendas[$k]=$v;
    }
    
    $editables=array();
    foreach($this->agenda->get_editables() as $k=>$v){
        $editables[$k]=$v['id'];
    }
   
    $permitidos=array_intersect($misagendas,$editables);
    return count($permitidos);
}


/**
 *     LIGHTBOX || ventana de detalle
 */
      
// Save
function lightbox_save_event(){
$cpData = $this->lang->language;
$json=$this->input->post('evento',true); 
$evento=json_decode($json, true); 
       

 // Start_date
$sd=explode("_", $evento["start_date"]);
$evento['start_date']="{$sd[2]}-{$sd[1]}-{$sd[0]} {$sd[3]}:{$sd[4]}:00";

// End_Date
$ed=explode("_", $evento["end_date"]);
$evento['end_date']="{$ed[2]}-{$ed[1]}-{$ed[0]} {$ed[3]}:{$ed[4]}:00";

// Just future events allowed
$now= strtotime('now');
$sd=strtotime($evento['start_date']);
$ed=strtotime($evento['end_date']);

if($sd<$now){  
    $msg= array("msg"=>$this->lang->line('error_past_events'),"show"=>true);
    return json_encode($msg);
}     

if($sd>=$ed){  
    $msg= array("msg"=>$this->lang->line('error_wrong_date'),"show"=>true);
    return json_encode($msg);
}      



////Mod 0 (week)
$obj=array();
$obj["event_name"]=$evento["event_name"];
$obj["start_date"]=$evento['start_date'];
$obj["end_date"]=$evento['end_date'];

//// Only mod 1
if((bool)$evento['mod']){
$obj["detalle"]=$evento['detalle'];
$obj["tema"]=$evento['tema'];
$obj["lugar"]=$evento['lugar'];

foreach($evento['agendaID'] as $k=>$v){
   $obj["agendaID"][$k]=(integer)$v;
}

$obj["date"]=date("Y-m-d H:i:s");
$obj["estado"]=(int)$evento['estado'];
$obj["latLng"]=$evento['latLng'];
}


if(empty($evento['event_id'])){
// Insert            
$obj["event_id"]=$this->gen_inc("eventos","event_id");
$obj["autorID"]=(double)$this->session->userdata("AG_idu");
$criteria=array('event_id' => $obj["event_id"]);
$query=array('$set' =>$obj);
$this->mongo->agenda->eventos->update($criteria, $query);
$msg= array("msg"=>$this->lang->line('events_new').": {$obj["event_id"]}","show"=>true);
}else{

// Update 
 if($this->is_writeable($evento["event_id"])){
    $obj["event_id"]=(double)$evento['event_id'];
    $criteria=array('event_id' => $obj["event_id"]);
    $query=array('$set' =>$obj);
    $this->mongo->agenda->eventos->update($criteria, $query);
    $msg= array("msg"=>$this->lang->line('events_updated').": {$evento["event_id"]}","show"=>true); 
 }else{
     $msg= array("msg"=>$this->lang->line('error_permission_edit'),"show"=>true); 
 }
     
        
}


return json_encode($msg);    

}

/**
*     OPTIONS
*/   

function options_save_colors(){
    
    $data=  json_decode($this->input->post('data'));
    $idu=$this->idu;
    
    // retrieve the array
    $criteria=array('uid' => $idu);
    $search=$this->mongo->agenda->profiles->findOne($criteria);
    // add new color
    $search['colors'][$data->agenda]=$data->color;
    // save
    $obj=array("colors"=>$search['colors']);
    $query=array('$set' =>$obj);    
    $this->mongo->agenda->profiles->update($criteria, $query,true);
}


/**
*     HELPERS
*/       

    
// Get next 
function gen_inc($container, $fieldname) {
    $options = array('upsert' => true, 'safe' => true);
    $query = array();
    $fields = array($fieldname);
    $sort = array($fieldname => -1);
    //var_dump($query);
    $result = $this->mongo->agenda->selectCollection($container)->find($query, $fields)->sort($sort)->getNext();
    //var_dump($result);
    $inc_id = 1 * $result[$fieldname] + 1;
    $this->mongo->agenda->selectCollection($container)->save(array($fieldname => $inc_id), $options);
    return $inc_id;
}
    

// xxxxxxxxxxxxx Migrar agendas
                    
    function migrar_tabla_agendas() {
        
        $SQL = "select * from agendas ";        
        $DB_agenda = $this->load->database('agenda', TRUE);
        $query = $DB_agenda->query($SQL);
        function cast(&$item){
            $item=(double)$item;
        }
        
        if ($query->num_rows() > 0) {
            foreach ($query->result() as $row) {
               
                    $obj=array();
                    $obj["nombre"]=utf8_decode($row->nombre);
                    $obj["id"]=(int)$row->id;

                    $users_r=explode("*",trim($row->users_r,"*"));
                    array_walk($users_r, 'cast');               
                    $obj["users_r"]=$users_r;
                    
                    $users_w=explode("*",trim($row->users_w,"*"));
                    array_walk($users_w, 'cast');               
                    $obj["users_w"]=$users_w;
                    
                    $obj["parent"]=(int)$row->parent;
                    $obj["tipo"]=(int)$row->tipo;
                    echo $obj["nombre"]."<br>";
                $options = array('upsert' => true, 'safe' => true);
                $this->mongo->agenda->agendas->save($obj, $options);

            }
            
        }

   }
    
    // Migrar Eventos
    
    function migrar_tabla_eventos() {
        $DB_agenda = $this->load->database('agenda', TRUE);
        $SQL = "select * from eventos where event_id > 6010 ";
        $query = $DB_agenda->query($SQL);
        $counter=0; //counter
        function cast(&$item){
            $item=(double)$item;
        }

        if ($query->num_rows() > 0) {
            foreach ($query->result() as $row) {
                    $counter++;
                    $obj=array();
                    $obj["event_name"]=$this->migrar_tabla_check($row->event_name);
                    $obj["event_id"]=(int)$row->event_id;
                    $obj["start_date"]=$row->start_date;
                    $obj["end_date"]=$row->end_date;
                    $obj["detalle"]=$this->migrar_tabla_check($row->detalle);
                    $obj["tema"]=$this->migrar_tabla_check($row->tema);
                    $obj["lugar"]=$this->migrar_tabla_check($row->lugar);
                    $obj["agendaID"]=array((int)$row->agendaID);
                    $obj["autorID"]=(int)$row->autorID;
                    $obj["date"]=$row->date;
                    $obj["estado"]=(int)$row->estado;
                    
                    $options = array('upsert' => true, 'safe' => true);
                    echo $obj["event_id"]."-".$obj["event_name"]."<br>";                   

                $this->mongo->agenda->eventos->save($obj, $options);
            
        }
        echo "Importados: $counter eventos";
   }}
   
   // Cleaning bad events
   private function migrar_tabla_check($texto){
        if(mb_check_encoding(utf8_decode($texto))){
            return htmlspecialchars(utf8_decode($texto),ENT_QUOTES,'UTF-8');
        }else{
            return htmlspecialchars(utf8_encode($texto),ENT_QUOTES,'UTF-8');    
        }
   }

    
//---------------------------
}
?>
