<?php
session_start();

class Main extends CI_Controller {

    function Main() {
        parent::__construct();
        $this->load->library('parser');
        $this->load->model('user/user');
        $this->load->model('agenda/agenda');

        $this->load->helper('agenda/get_tree');
        $this->load->helper('cookie');

        //----LOAD LANGUAGE
        $this->lang->load('library', $this->config->item('language'));
        $this->idu = (float) $this->session->userdata('iduser');
              

    }
    

    function index() {
    $this->user->authorize();
    $cpData = $this->lang->language;
    $level=$this->session->userdata('level');      
    $cpData['base_url'] = base_url();
    $cpData['module_url'] = base_url() . 'agenda/';
    $agendas=$this->agenda->get_agenda_colors();
    foreach($agendas as $agenda){
        
       $cpData["agenda_colors"][]=array("color"=>$agenda["color"],"id"=>$agenda["id"]);
    }

    // Username
    $userdata=$this->user->getbyid($this->idu);
    foreach($userdata as $v){
       $user=(array)$v;
       $cpData["username"]="{$user['lastname']}, {$user['name']}";
    }
  
    $this->set_visibles(1);// Carga la session con las agendas permitidas
    
    $cpData["tree_colors"]=$this->get_tree_colors();

    // Settings @todo
    $cpData["rowHeight"]='50';
    
echo "test:".$this->user->has("root/modules");
exit();

    //Session 
    $this->session->set_userdata("AG_idu",$this->idu);                 
    $this->session->set_userdata("AG_isadmin",$this->user->has("ADM",$this->idu) OR $this->user->has("ADMAG",$this->idu));
    //$this->session->set_userdata("AG_isadmin",false);
    
    $cpData["is_admin"]= $this->session->userdata("AG_isadmin");
    $cpData["idu"]= $this->session->userdata("AG_idu");
    
    //Parse
    $this->parser->parse('main', $cpData);
   

    }


    /**
     *    TREE
     */
    
    // Traigo el xml 
    function get_tree(){    
        $folders= $this->agenda->get_folders();
        $agendas= $this->agenda->get_agendas(); 
        return get_tree($this->agenda->get_agendas(),$this->agenda->get_folders());
    }

    // Armo los colores del tree
    function get_tree_colors(){
        $colors = "";
        $rs = $this->agenda->get_agenda_colors();
        foreach ($rs as $agenda)$colors.="tree_agendas2.setItemStyle({$agenda["id"]},'color:#{$agenda["color"]}');\n";     
        return $colors;
    }

    /**
     *     AGENDAS
     */
     
    function set_visibles() {

        // Allowed Scheduller
        $agendas_permitidas=array();
        foreach($this->agenda->get_agendas() as $agenda){
           $agendas_permitidas[]=$agenda["id"];
        }

       // Visible Scheduller
        $agendas_visibles=(array)$this->input->post('items');
        $agendas=array_intersect($agendas_permitidas, $agendas_visibles);

        $this->session->set_userdata("agendas", $agendas);
    }

    function get_visibles(){
        echo $this->session->userdata("agendas");
    }
    
    // devuelve lista de agendas que el usuario logueado puede modificar
    function get_editables(){
        foreach($this->agenda->get_editables() as $agenda){
           $agendas_editables[]=$agenda["id"];
        }
        return implode(",", $agendas_editables);
    }
    
    /**
     *    HELPERS
     */

    // Devuelve un nombre de usuario
    function get_username($idu){
        foreach($this->user->getbyid($idu) as $usr){
                return $usr['lastname'].", ".$usr['name'];
        }
    }

    // xxx Print 

    function set_print_mode($mode,$date){
     $this->session->set_userdata("print_mode",$mode);
     $this->session->set_userdata("print_date",$date);
    }


    function get_hora_actual(){
        echo date("H");
    }


    // Tool para pasar a UTF8 la base @todo Quitar en produccion
    function migrar_tabla_eventos(){
        $this->agenda->migrar_tabla_eventos();
    }

    function migrar_tabla_agendas(){
        $this->agenda->migrar_tabla_agendas();
    }


    
    /**
     *     LIGHTBOX || ventana de detalle
     */
      
    function get_lightbox($id,$sd,$ed,$id_dhtmlx){

        $cpData = $this->lang->language;
        $cpData['base_url'] = base_url();
        $cpData['module_url'] = base_url() . 'agenda/';
        $cpData['idu']= $this->session->userdata("AG_idu");

        // Select de agendas editables
        $agendas_editables="";
        $editables=array();
        
        
        if(!$id){// xxxxx Nuevo
            // Todos los editables sin seleccion
            foreach($this->agenda->get_editables() as $agenda){
            $agendas_editables.="<option value='{$agenda["id"]}' >{$agenda["nombre"]}({$agenda["id"]})</<option>";
            }

            $sd2= explode("_",$sd);
            $ed2= explode("_",$ed);
            $cpData["start_minute"]=$sd2[4];
            $cpData["start_hour"]=$sd2[3];
            $cpData["start_date"]="{$sd2[0]}/{$sd2[1]}/{$sd2[2]}";
            $cpData["end_minute"]=$ed2[4];
            $cpData["end_hour"]=$ed2[3];
            $cpData["end_date"]="{$ed2[0]}/{$ed2[1]}/{$ed2[2]}";
            
            $cpData["detalle"]="";          
            $cpData["titulo"]=""; ;
            $cpData["lugar"]=""; 
            $cpData["tema"]=""; 
            $cpData["tarea"]="";
            $cpData["latLng"]="";
            $cpData["id"]=null; 
            $cpData["id_dhtmlx"]=$id_dhtmlx;
            
        }else{
            // Carga
            $eventos=$this->agenda->get_event_by_id($id);
               if(!is_array($eventos["agendaID"])){
                 $eventos["agendaID"]=(array)$eventos["agendaID"];
               }
                      
            // todos los editables
             $Qsel=0;
            foreach($this->agenda->get_editables() as $agenda){              
            $selected=(in_array($agenda["id"],$eventos["agendaID"]))?('selected=selected'):('');
            if(strlen($selected)>5)$Qsel++;
            $agendas_editables.="<option value='{$agenda["id"]}' $selected >{$agenda["nombre"]}({$agenda["id"]})</<option>";
            $editables[$agenda["nombre"]]=$agenda["id"];
            }
                    
            // Is not in editable list -> show readonly
            if($Qsel==0){ 
               foreach($eventos["agendaID"] as $k=>$v){
                       $my_agenda=$this->agenda->get_agenda($v);
                     $agendas_editables="<option value='$v' disabled='disabled' >".$my_agenda["nombre"]."($v)</<option>";
                }
            }
            
       
            $cpData["detalle"]=(isset($eventos["detalle"]))?($eventos["detalle"]):("");
            $cpData["titulo"]=(isset($eventos["event_name"]))?($eventos["event_name"]):("");

            $cpData["lugar"]=(isset($eventos["lugar"]))?($eventos["lugar"]):("");
            $cpData["tema"]=(isset($eventos["tema"]))?($eventos["tema"]):("");
            $cpData["estado"]=(isset($eventos["estado"]))?($eventos["estado"]):("");
            $cpData["autorID"]=(isset($eventos["autorID"]))?($eventos["autorID"]):("");
            $cpData["autor_nombre"]= $this->get_username($cpData["autorID"]);
            $cpData["latLng"]=(isset($eventos["latLng"]))?($eventos["latLng"]):("");
            
            $sd=strtotime(($eventos["start_date"]));
            $ed=strtotime(($eventos["end_date"]));
            $d=strtotime(($eventos["date"]));

            $cpData["start_minute"]=date("i",$sd);
            $cpData["start_hour"]=date("H",$sd);
            $cpData["start_date"]=date("d/m/Y",$sd);
            $cpData["end_minute"]=date("i",$ed);
            $cpData["end_hour"]=date("H",$ed);
            $cpData["end_date"]=date("d/m/Y",$ed);
            $cpData["date"]=date("d/m/Y",$d);
            
            $cpData["id"]=$id; 
            $cpData["id_dhtmlx"]=$id_dhtmlx;
           
        }
        

        $cpData["agendas_editables"]=$agendas_editables;
        $this->parser->parse('agenda/lightbox2', $cpData);
}
    
    /**
     *     EVENTOS
     */

    
    function lightbox_save_event(){
         echo $this->agenda->lightbox_save_event();
    }
    
     
    function delete_event(){
    echo $this->agenda->delete_event();

    //$this->agenda->is_writeable($this->session->userdata("AG_idu"), $this->input->post('eventID'));
    }   
    
    
    function get_events($min_date,$max_date){
        echo $this->agenda->get_events($min_date,$max_date);
    }
    


}
?>
