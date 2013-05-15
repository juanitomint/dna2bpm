<?php

class Printer extends CI_Controller {

    function Printer() {
        parent::__construct();
        $this->load->library('parser');
        $this->load->model('user');
        $this->load->model('agenda');

        //----LOAD LANGUAGE
        $this->lang->load('main', $this->config->item('language'));
        $this->idu = (double) $this->session->userdata('iduser');
        
        $this->test=$cpData['base_url'] = base_url();
        $this->base_url = base_url();
        $this->module_url = base_url() . 'agenda/';
        
    }

    function index() {
    $this->user->authorize();
    $print_mode=$this->session->userdata('print_mode');
    if(empty($print_mode)){
        $this->set_print_mode('month',date("Ymd"));
    }

        switch($print_mode){
            case "week":
               $this->print_week();
            break;

            case "workweek":
               $this->print_week(5);
            break;

            case "day":
                $this->print_week(1);
            break;

            case "month":
                $this->print_month();
            break;

            default:
               $this->print_month();

        }

    }
    
    function set_print_mode($mode,$date){
        $this->user->authorize();
        $this->session->set_userdata("print_mode",$mode);
        $this->session->set_userdata("print_date",$date);   
    }
    

    /*
     *  PrintMonth 
     * 
     */
    
    private function print_month(){
        $this->user->authorize();
       $cpData['base_url'] = $this->base_url;
       $cpData['module_url'] = $this->module_url;
       $cpData["agenda_colors"]=$this->agenda->get_agendas();
       
       // fechas
       $meses=array(1=>"Enero",2=>"Febrero",3=>"Marzo",4=>"Abril",5=>"Mayo",6=>"Junio",7=>"Julio",8=>"Agosto",9=>"Septiembre",10=>"Octubre",11=>"Noviembre",12=>"Diciembre");

       $fecha=$this->session->userdata("print_date");
       $fecha_year=substr($fecha,0,4);
       $fecha_month=substr($fecha,4,2);
       $cpData['fecha']=$fecha_year.' , '.$meses[(int)$fecha_month];
             
       $dias_de_mes = cal_days_in_month(CAL_GREGORIAN,$fecha_month, $fecha_year); //< cantidad de dias este mes
    
       $dia1TS=mktime(0, 0, 0,$fecha_month, 1, $fecha_year);
       $dia1=getDate($dia1TS);
       $dia1_orden=$dia1["wday"]; //< que dia de la semana cae el 1
       $orden=array(6,0,1,2,3,4,5);
       
       $dias=array();
       
    //cargo mes anterior
    for($i=$orden[$dia1_orden];$i>0;$i--){
       $dias[]=date("Y-m-d",mktime(0, 0, 0,$fecha_month, 1-$i, $fecha_year));
       $css[]="mes-anterior";
    }

    //cargo mes actual
    for($i=1;$i<=$dias_de_mes;$i++){
       $dias[]=date("Y-m-d",mktime(0, 0, 0,$fecha_month, $i, $fecha_year));
       $css[]="mes-actual";
    }
    
    $Qdias=count($dias);
    while($Qdias%7){
       $Qdias++;
    }
    $faltan=$Qdias-count($dias);
    //cargo mes proximo
    for($i=1;$i<=$faltan;$i++){
       $dias[]=date("Y-m-d",mktime(0, 0, 0,$fecha_month, $dias_de_mes+$i, $fecha_year));
       $css[]="mes-proximo";
    }
    // Agendas visibles
    $cpData['agendas']=$this->session->userdata("agendas");

    
    // Armo el calendario

    $calendario="";
    $style="";

    
    for($i=0;$i<count($dias);$i++){

    $eventos=json_decode($this->agenda->get_events($dias[$i]));
    
    if($i==0 or $i==7 or $i==14 or $i==21 or $i==28 or $i==35 or $i==42 )$calendario.="<tr>";
    $calendario.="<td  class='$css[$i]' $style  valign='top'>";
    $dia=getdate(strtotime($dias[$i]));
    $calendario.='<span class="mes-date">'.$dia["mday"].'</span>';

    foreach($eventos as $v){
        $row=(array)$v;
        $ed=strtotime($row['end_date']);
        $sd=strtotime($row['start_date']);
        $horas=($ed-$sd)/3600;
        $clase=($horas>23)?("listado-item-recurrente"):("listado-item");
        $estrella=($row['autorID']==$this->idu)?("<img src='{$this->module_url}assets/images/star.png' alt='Autor' title='Autor'/>"):("");
        $agendaID=(array)$row['agendaID'];
        $calendario.="<span class='$clase agenda{$agendaID[0]}'>".$estrella.$row['text'].'</span>';
    }
     $calendario.="</td>\n";
      if($i==6 or $i==13 or $i==20 or $i==27 or $i==34 or $i==41 or $i==48){
      $calendario.="</tr>";
    }
    }
    $cpData['calendario']=$calendario;
       $this->parser->parse('print_month', $cpData);
    
    
    }
    
    /*
     *  xxxxxxxxxxxxxxxxxxxx Semanal // Laboral // Diario
     * 
     */
    
    private function print_week($week_days=7){
        $this->user->authorize();
        
        $cpData['base_url'] = $this->base_url;
        $cpData['module_url'] = $this->module_url;
        $cpData["agenda_colors"]=$this->agenda->get_agendas();
        $fecha=getDate(strtotime($this->session->userdata("print_date")));
        
        // Weekdays
        

        $cpData['dia_orden']=$fecha["wday"]; //< que dia de la semana cae
        $orden=array(6=>5,0=>6,1=>0,2=>1,3=>2,4=>3,5=>4);
        $lunes=$fecha["mday"]-$orden[$cpData['dia_orden']];

        $dias[1]=date("Y-m-d",mktime(0, 0, 0, $fecha["mon"], $lunes, $fecha["year"]));
        $dias[2]=date("Y-m-d",mktime(0, 0, 0, $fecha["mon"], $lunes+1, $fecha["year"]));
        $dias[3]=date("Y-m-d",mktime(0, 0, 0, $fecha["mon"], $lunes+2, $fecha["year"]));
        $dias[4]=date("Y-m-d",mktime(0, 0, 0, $fecha["mon"], $lunes+3, $fecha["year"]));
        $dias[5]=date("Y-m-d",mktime(0, 0, 0, $fecha["mon"], $lunes+4, $fecha["year"]));
        $dias[6]=date("Y-m-d",mktime(0, 0, 0, $fecha["mon"], $lunes+5, $fecha["year"]));
        $dias[7]=date("Y-m-d",mktime(0, 0, 0, $fecha["mon"], $lunes+6, $fecha["year"]));
        $cpData['calendario_dias']="{$dias[1]},{$dias[2]},{$dias[3]},{$dias[4]},{$dias[5]},{$dias[6]},{$dias[7]}";

        $cpData['dias2'][1]=$this->lang->line('weekdays_1').", ".date("d/m",mktime(0, 0, 0, $fecha["mon"], $lunes, $fecha["year"]));
        $cpData['dias2'][2]=$this->lang->line('weekdays_2').", ".date("d/m",mktime(0, 0, 0, $fecha["mon"], $lunes+1, $fecha["year"]));
        $cpData['dias2'][3]=$this->lang->line('weekdays_3').", ".date("d/m",mktime(0, 0, 0, $fecha["mon"], $lunes+2, $fecha["year"]));
        $cpData['dias2'][4]=$this->lang->line('weekdays_4').", ".date("d/m",mktime(0, 0, 0, $fecha["mon"], $lunes+3, $fecha["year"]));
        $cpData['dias2'][5]=$this->lang->line('weekdays_5').", ".date("d/m",mktime(0, 0, 0, $fecha["mon"], $lunes+4, $fecha["year"]));
        $cpData['dias2'][6]=$this->lang->line('weekdays_6').", ".date("d/m",mktime(0, 0, 0, $fecha["mon"], $lunes+5, $fecha["year"]));
        $cpData['dias2'][7]=$this->lang->line('weekdays_0').", ".date("d/m",mktime(0, 0, 0, $fecha["mon"], $lunes+6, $fecha["year"]));

       // $this->lang->line('weekdays_0');
        // Calendarios 
        $cpData['mes_anterior']=date("Y-m-d",mktime(0, 0, 0, $fecha["mon"]-1, $lunes, $fecha["year"]));

        $cpData['mes_proximo']=date("Y-m-d",mktime(0, 0, 0, $fecha["mon"]+1, $lunes, $fecha["year"]));

        
        $dias[1]=date("d-m-Y",mktime(0, 0, 0, $fecha["mon"], $lunes, $fecha["year"]));
        $dias[7]=date("d-m-Y",mktime(0, 0, 0, $fecha["mon"], $lunes+1, $fecha["year"]));
        $cpData['periodo']=$dias[1]." / ".$dias[7];
        $cpData['dias']=$dias;

        
        $estrella="<img src='{$this->module_url}assets/images/star.png' alt='Autor' title='Autor'/>";
        

            for($i=1;$i<=7;$i++){
            $eventos=json_decode($this->agenda->get_events($dias[$i]));
            if(count($eventos)!=0){
                foreach($eventos as $v){
                $row=(array)$v;

                $ed=strtotime($row['end_date']);
                $sd=strtotime($row['start_date']);
                $sd_parsed=date_parse($row['start_date']);
                $horas=($ed-$sd)/3600;
                $clase=($horas>23)?("listado-item-recurrente"):("listado-item");
                $agendaID=(array)$row['agendaID'];
                $clases="$clase agenda{$agendaID[0]}";
                $agendaID=(array)$row['agendaID'];                 
                $mis_eventos[$i][]=array('clases'=>$clases,
                    'titulo'=>$row["text"],
                    'hour'=>str_pad($sd_parsed['hour'], 2, "0", STR_PAD_LEFT),
                    'minute'=>str_pad($sd_parsed['minute'], 2, "0", STR_PAD_LEFT),
                    'day'=>$sd_parsed['day'],
                    'autorID'=>$row['autorID'],
                    'year'=>$sd_parsed['year']);         
                } 
            } 

            }
            
            /*
            *     xxxx Calendario Normal
            */
                       
            $cpData['calendario']="";
            for($j=8;$j<=21;$j++){
                $cpData['calendario'].="<tr>";
                $cpData['calendario'].="<td class='print-numeros'><strong>$j</strong></td>";
                for($i=1;$i<=7;$i++){
                    if($week_days==1 && $fecha['wday']!=$i)continue; // Day
                    if($week_days==5 && $i>5)continue; // Workweek
                $cpData['calendario'].="<td>";
                if(isset($mis_eventos[$i]) && count($mis_eventos[$i])!=0){
                   $trap=1;
                   foreach($mis_eventos[$i] as $k=>$v){
                       if($v['hour']==$j){
                        $cpData['calendario'].="<div class='{$v['clases']}'>";
                        $estrellaOK=($this->idu==$v['autorID'])?($estrella):('');
                        $cpData['calendario'].="<span>{$v['hour']}:{$v['minute']}</span>".$estrellaOK.$v['titulo'];
                        $cpData['calendario'].="</div><img width='1' height='1' />"; 
                           $trap=0;
                       }
                   }
                   if($trap)$cpData['calendario'].="&nbsp;";
                }else{
                    $cpData['calendario'].="&nbsp;";
                }

                $cpData['calendario'].="</td>";
               //echo "------------------------<br/><br/>";
                }
            }
            $cpData['calendario'].="</tr>";      
            
            /*
            *     xxxx Calendario Continuo
            */
            
                $cpData['calendario2']="<tr>";
                for($i=1;$i<=7;$i++){
                    if($week_days==1 && $fecha['wday']!=$i)continue; // Day
                    if($week_days==5 && $i>5)continue; // Workweek
                     $cpData['calendario2'].="<td>";
                 
                if(isset($mis_eventos[$i]) && count($mis_eventos[$i])!=0){          
                   foreach($mis_eventos[$i] as $k=>$v){
                        $cpData['calendario2'].="<div class='{$v['clases']}'>";
                        $estrellaOK=($this->idu==$v['autorID'])?($estrella):('');
                        $cpData['calendario2'].="<span>{$v['hour']}:{$v['minute']}</span>".$estrellaOK.$v['titulo'];
                        $cpData['calendario2'].="</div><img width='1' height='1' />"; 
                   }
                }else{
                    $cpData['calendario2'].="&nbsp;";
                }

                $cpData['calendario2'].="</td>";

                }
                 $cpData['calendario2'].="</tr>";
     
            
            /*
            *     xxxx Parser
            */          

            if($week_days==7){
                $this->parser->parse('print_week', $cpData);
            }elseif($week_days==5){
                $this->parser->parse('print_work_week', $cpData);
            }else{
                $this->parser->parse('print_day', $cpData);
            }

             
           
    }
    
   
    
    


}//class
?>