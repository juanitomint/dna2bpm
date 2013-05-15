<?php
//session_start();

class Listado extends CI_Controller {

    function Listado() {
        parent::__construct();
        $this->load->library('parser');
        $this->load->model('user/user');
        $this->load->model('agenda/agenda');
        $this->load->library('agenda/vcalendar');

        //----LOAD LANGUAGE
        $this->lang->load('main', $this->config->item('language'));
        $this->idu = (double) $this->session->userdata('iduser');
              

    }
    

    function index() {
        
    $this->user->authorize();
    $cpData = $this->lang->language;    
    $cpData['base_url'] = base_url();
    $cpData['module_url'] = base_url() . 'agenda/';
    
    //Parse
    $this->parser->parse('listado', $cpData);
    }
    
    /*
    *  XML Listing
    * 
    */
    
      
       function get_listado_xml(){ 
        $this->user->authorize();
        $cpData['base_url'] = base_url();
        $cpData['module_url'] = base_url() . 'agenda/';

        $sd = $this->uri->segment(4);
        $ed = $this->uri->segment(5);;
        
        // Date search
        if(!$sd){
        $sd=date("Y-m-d");
        $ed=date("Y-m-d");
        }else{
        $sd=substr($sd,0,4)."-".substr($sd,4,2)."-".substr($sd,6,2);
        $ed=substr($ed,0,4)."-".substr($ed,4,2)."-".substr($ed,6,2);
        }
        
        
        header("Content-type:text/xml"); print("<?xml version=\"1.0\" encoding=\"UTF-8\"?>");   

        $eventos=$this->agenda->get_events($sd,$ed);
        
        echo "<rows>";
        // Loop
        foreach(json_decode($eventos) as $ev){
        $autor=$this->user->get_user($ev->autorID);
        echo "<row>";
        echo "<cell>".$ev->start_date."</cell>";
        echo "<cell>".$ev->end_date."</cell>";
        echo "<cell>".$ev->text."</cell>";
        echo "<cell>".$ev->detalle."</cell>";
        echo "<cell>".$ev->lugar."</cell>";
        echo "<cell>{$autor->lastname}, {$autor->name}</cell>";
        echo "</row>";
        }
        echo "</rows>";

    }
    
   /*
    *  Excel Export
    * 
    */
    
     function get_listado_csv(){ 
        $this->user->authorize();
        $cpData['base_url'] = base_url();
        $cpData['module_url'] = base_url() . 'agenda/';

        $sd = $this->uri->segment(4);
        $ed = $this->uri->segment(5);;
        
        // Date search
        if(!$sd){
        $sd=date("Y-m-d");
        $ed=date("Y-m-d");
        }else{
        $sd=substr($sd,0,4)."-".substr($sd,4,2)."-".substr($sd,6,2);
        $ed=substr($ed,0,4)."-".substr($ed,4,2)."-".substr($ed,6,2);
        }
        
        
        header('Content-type: application/vnd.ms-excel; charset=ISO-8859-1');
        header("Content-Disposition: attachment; filename=listado.xls");
        header("Pragma: no-cache");
        header("Expires: 0");
        
        $flush="<table cellpadding=5 cellspacing=5>\n";
        $flush.= "<tr bgcolor=d2e0e5 color=044d66 >\n";
        $flush.=  "<th>{$this->lang->line('start_date')}</th>\n";
        $flush.=  "<th>{$this->lang->line('end_date')}</th>\n";
        $flush.=  "<th>{$this->lang->line('title')}</th>\n";
        $flush.=  "<th>{$this->lang->line('topic')}</th>\n";
        $flush.=  "<th>{$this->lang->line('detail')}</th>\n";
        $flush.=  "<th>{$this->lang->line('location')}</th>\n";
        $flush.=  "<th>{$this->lang->line('author')}</th>\n";
        $flush.=  "</tr>\n";


        $eventos=$this->agenda->get_events($sd,$ed);

        // Loop
        foreach(json_decode($eventos) as $ev){
        $autor=$this->user->get_user($ev->autorID);
        $flush.=  "<tr>";
        $flush.=  "<td>".$ev->start_date."</td>";
        $flush.=  "<td>".$ev->end_date."</td>";
        $flush.=  "<td>".$ev->text."</td>";
        $flush.=  "<td>".$ev->tema."</td>";
        $flush.=  "<td>".$ev->detalle."</td>";
        $flush.=  "<td>".$ev->lugar."</td>";
        $flush.=  "<td>{$autor->lastname}, {$autor->name}</td>";
        $flush.=  "</tr>";
        }
        $flush.=  "</table>";
        
         echo utf8_decode($flush);
     }
    
    /*
    *  ICAL Export
    * 
    */
    
    function get_listado_ical(){
        $this->user->authorize();
        $v = new vcalendar(); // create a new calendar instance
        $v->setConfig( '11111', 'minprod.gov.ar' ); // set Your unique id
        $v->setProperty( 'method', 'PUBLISH' ); // required of some calendar software
        
        $cpData['base_url'] = base_url();
        $cpData['module_url'] = base_url() . 'agenda/';

        $sd = $this->uri->segment(4);
        $ed = $this->uri->segment(5);;
        
        // Date search
        if(!$sd){
        $sd=date("Y-m-d");
        $ed=date("Y-m-d");
        }else{
        $sd=substr($sd,0,4)."-".substr($sd,4,2)."-".substr($sd,6,2);
        $ed=substr($ed,0,4)."-".substr($ed,4,2)."-".substr($ed,6,2);
        }
        
        $eventos=$this->agenda->get_events($sd,$ed);

        // Loop
        foreach(json_decode($eventos) as $ev){
            $sd=getDate(strtotime($ev->start_date));
            $ed=getDate(strtotime($ev->end_date));
            $eventname=$ev->text;
            $detalle=$ev->detalle;
            $lugar=$ev->lugar;

            $vevent = new vevent(); // create an event calendar component
            //$vevent->setProperty( 'rrule', array( 'FREQ' => 'DAILY', 'count' => 2));
            $vevent->setProperty( 'dtstart', array( 'year'=>$sd["year"], 'month'=>$sd["mon"], 'day'=>$sd["mday"], 'hour'=>$sd["hours"], 'min'=>$sd["minutes"],  'sec'=>$sd["seconds"] ));
            $vevent->setProperty( 'dtend',  array( 'year'=>$ed["year"], 'month'=>$ed["mon"], 'day'=>$ed["mday"], 'hour'=>$ed["hours"], 'min'=>$ed["minutes"],  'sec'=>$ed["seconds"] ));
            if(strlen($detalle))$vevent->setProperty( 'description',$detalle );
            if(strlen($eventname))$vevent->setProperty( 'summary',$eventname );
            if(strlen($lugar))$vevent->setProperty( 'LOCATION',$lugar );
            $v->setComponent ( $vevent ); // add event to calendar
        }
        $v->returnCalendar(); // redirect calendar file to browser
        
    }

    // Devuelve un nombre de usuario
    function get_username($idu){
        foreach($this->user->getbyid($idu) as $usr){
                return $usr['lastname'].", ".$usr['name'];
        }
    }


}
?>
