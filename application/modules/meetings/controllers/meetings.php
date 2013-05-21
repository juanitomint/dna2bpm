<?php

if (!defined('BASEPATH'))
        exit('No direct script access allowed');

class meetings extends MX_Controller {
        /*
          var $this->business_agenda;
          var $table_agenda;
         */

        function __construct() {
                parent::__construct();
                $this->load->library('parser');
                $this->load->library('ui');
                //--Initialize Mongo
                $this->load->library('cimongo/cimongo');
                $this->db = $this->cimongo;
                $this->load->model('user/user');
                $this->load->model('user/rbac');
                $this->load->model('meeting');
                //$this->user->authorize();
                //---base variables
                $this->base_url = base_url();
                $this->module_url = base_url() . 'meetings/';
                //----LOAD LANGUAGE
                $this->lang->load('library', $this->config->item('language'));
                $this->idu = (float) $this->session->userdata('iduser');
                $this->dups = array();
                $this->wishlist = array();

                //---Basic Params
                $this->business_total = 500;
                $this->tables_count = 10;
                $this->interviews = 5;
                $this->frameEvent = '7403';
                $this->frameBusiness = '7466';
                $this->container_empresas = 'container.ronda1';
                $this->intervals = array(
                    
                    
                    '16:40',
                    '17:00',
                    '17:20',
                    '17:40',
                    '18:00',
                    '18:20',
                    '18:40',
                );
        }

        function Index() {
                $this->load_data();
                //$this->prepare();
                $cpData['title'] = 'Meeting Main Menu';
                $cpData['base_url'] = $this->base_url;
                $cpData['module_url'] = $this->module_url;
                $cpData['theme'] = 'c';
                $cpData['global_js'] = array(
                    'base_url' => $this->base_url,
                    'module_url' => $this->module_url,
                );
                //---some metrics of the event
                $cpData['business_total'] = $this->business_total;
                $cpData['accredited_business'] = count($this->meeting->get_accredited());
                $used_tables = count(array_filter($this->table_agenda));
                $cpData["Used_Tables"] = $used_tables;
                $cpData['available_tables'] = $this->tables_count;
                $cpData["Wishes_Granted"] = count($this->linear($this->wishes));
                $cpData["Total_wishes"] = count($this->linear($this->wishlist));
                foreach ($this->intervals as $interval)
                        $cpData['intervals'][] = array('interval' => $interval);
                $cpData['css'] = array(
                    $this->module_url . "assets/css/menu.css" => 'Jquery mobile docs style',
                );

                $cpData['js'] = array(
                   // $this->base_url . "jscript/jquery/plugins/maskedinput/jquery.maskedinput-1.3.min.js" => 'JQuery MaskedInput',
                    $this->module_url . "assets/jscript/initCUIT.js" => 'CUIT Function',
                    $this->module_url . "assets/jscript/menu.js" => 'Menu Functions',
                );

                //$cpData+=$this->stats();
                $this->ui->compose('menu', 'jquerymobile.ui.php', $cpData);
        }

        function free_at($interval) {
                $this->load_data();
                $cpData['title'] = 'Meeting Main Menu';
                $cpData['base_url'] = $this->base_url;
                $cpData['module_url'] = $this->module_url;
                foreach ($this->business_agenda as $id => $agenda) {
                        $busy = array_keys($agenda);
                        if (!in_array($interval, $busy)) {
                                $cpData['business'][] = $this->meeting->get_data($id);
                        }
                }
                if (isset($cpData['business'])) {
                        @$this->parser->parse('business_registered', $cpData);
                } else {
                        echo "No hay empresas disponibles para las <b>$interval</b>";
                }
        }

        function print_meetings() {
                
        }
        function merge(){
                $result=$this->meeting->merge_data();
                echo "<h1>Imports:".$result['imports'].'<br/>';
                echo "Updates:".$result['updates'].'</h1>';
        }
        function prepare() {
                $this->wishes_not_granted = array();
                $this->wishes_granted = array();
                //---create TABLES
                $this->tables = array();
                for ($i = 1; $i <= $this->tables_count; $i++) {
                        $this->tables[] = 'M' . $i;
                }
                //---build main arrays
                //--- table_agenda
                $this->table_agenda = array();
                foreach ($this->tables as $table) {
                        $this->table_agenda[$table] = null;
                }
                //----business_agenda
                $this->business_agenda = array();
                foreach ($this->business as $b) {
                        $this->business_agenda[$b] = array();
                }
        }

        function mark_empresas() {
                //---get random bussines from container.empresas
                $this->business = array();
                $this->db->select('id', '1693');
                $this->db->order_by(array('1693' => 'ASC'));
                $this->db->where(array('status' => 'activa'));

                $min = 0;
                $max = 10;
                $j = 0;
                //----para resultado acotado este $result el de mas abajo son todas
                //$result = $this->db->get($this->container_empresas, $this->business_total, 0)->result_array();
                $result = $this->db->get($this->container_empresas)->result_array();
                //---mark empresa
                $object = array($this->frameEvent => 1);
                echo "Intentando marcar:";
                foreach ($result as $emp) {
                        $nombre = (isset($emp['1693'])) ? $emp['1693'] : '';
                        $id = $emp['id'];
                        $this->db->where(array('id' => $id));
                        echo $j++ . " :: Actualizando $id :: '$nombre'<br/>";
                        $this->business[] = $id;
                        $this->db->update($this->container_empresas, $object, array("safe" => true));
                }

                echo '<hr/>';
                $j = 0;
                //---emulate list pick-up
                foreach ($result as $emp) {
                        $nombre = (isset($emp['1693'])) ? $emp['1693'] : '';
                        $id = $emp['id'];

                        $list = rand($min, $max);
                        echo $j++ . " :: $list elecciones para: $nombre<br/>";
                        $object = array();
                        for ($i = 0; $i <= $list; $i++) {
                                $addid = $this->business[rand(0, $this->business_total - 1)];
                                if ($addid <> $id)
                                        $object[] = (float) $addid;
                        }
                        $this->db->where(array('id' => $id));
                        $this->db->update($this->container_empresas, array($this->frameBusiness => $object));
                }
        }

        function run() {
                $this->output->enable_profiler(TRUE);
                //----load marked business
                $this->wishlist = $this->meeting->load_business();
                $this->business = array_keys($this->wishlist);
                //----make fixed tables;
                $this->fixed_tables = array();
                /*
                  $this->fixed_tables = array(
                  '1869582913' => 'T1', // Sushi Club SRL
                  '1989358889' => 'T2', // La Fusión L.A S. A
                  );
                 */
//-----start simulation

                /*
                  //---Create Bussiness
                  $this->business = array();
                  for ($x = 1; $x <= $this->business_total; $x++) {
                  $this->business[] = 'B' . $x;
                  }
                  //---build fake whishlist
                  for ($x = 1; $x <= $this->business_total; $x++) {
                  //---simulate n interviews
                  $this->wishlist['B' . $x] = array_slice(array_diff($this->business, array('B' . $x)), rand(0, $x), $this->interviews);
                  }
                 * 
                 * 
                 */
                $this->prepare();
                //---remove duplicate meetings from wishlist a=>b & b=>a
                //---store plain wishlist
                if($this->wishlist){
                $this->meeting->store_wishlist($this->wishlist);
                $this->wishlist['_id'] = null;
                $this->wishlist = array_filter($this->wishlist);


                $this->remove_dups($this->wishlist);
                //---process duplicates first
                $this->agendas($this->dups);
                //---precess all other whishes
                $this->agendas($this->wishlist);
                //---optimization
                //$this->optimize();
                //---show stats
                //$this->stats();
                /*
                  $this->print_tables();
                 */
                //var_dump($this->table_agenda);
                echo "OK!";
                } else {
                        echo "No se pueden Generar agendas con las Empresas presentes";
                }
        }

        function get_max_business() {
                $max_meets = max(array_map("count", $this->business_agenda));
                foreach ($this->business_agenda as $b1 => $meets) {
                        $c = count($meets);
                        if ($c == $max_meets) {
                                $arr[] = $b1;
                        }
                }
                return $arr;
        }

        function get_min_business() {
                $min_meets = min(array_map("count", $this->business_agenda));
                foreach ($this->business_agenda as $b1 => $meets) {
                        $c = count($meets);
                        if ($c == $min_meets) {
                                $arr[] = $b1;
                        }
                }
                return $arr;
        }

        function load_data() {

//---load business with their original wishlist;
                $this->wishlist = $this->meeting->load_business();
                $this->business_total = $this->meeting->get_total_business();
                $this->business = array_keys($this->wishlist);

                //---table agenda
                $this->table_agenda = $this->meeting->get_tables();
                $this->table_agenda['_id'] = null;
                $this->table_agenda = array_filter($this->table_agenda);
                $this->tables = array_keys($this->table_agenda);
                //---wishes
                $this->wishes = $this->meeting->get_wishlist();
                $this->wishes['_id'] = null;
                $this->wishes = array_filter($this->wishes);
                //---bussines agenda
                $this->business_agenda = $this->meeting->get_agenda_business();
                $this->business_agenda['_id'] = null;
                $this->business_agenda = array_filter($this->business_agenda);
        }

        function stats() {
                $this->load_data();
                $this->load->library('table');
                $cpData = array();
                $this->tables_count = count($this->tables);
                $possible_meets = count($this->intervals) * $this->tables_count;
                $wishes_count = $this->count_wishes($this->wishlist);
                //---remove dups
                $this->remove_dups($this->wishlist);
                $count_wishes = $this->count_wishes($this->wishlist) + $this->count_wishes($this->dups);
                $cpData['business_total'] = $this->business_total;
                $cpData['available_tables'] = count($this->tables);
                $cpData['available_periods'] = count($this->intervals);
                $cpData['meetings_per_business'] = $this->interviews;
                $cpData['Total_Posible_meetings'] = (count($this->intervals) * count($this->tables));
                $cpData['Total_wishes'] = $count_wishes;
                $cpData['Total_wishes_without_dups'] = $wishes_count;
                //--calculate % of marginal gap
                $grant_percent = number_format(100 - ($wishes_count / $possible_meets * 100), 2);
                $not_granted = -$this->count_wishes($this->table_agenda) + $count_wishes;
                $not_granted_percent = number_format(($not_granted / $wishes_count * 100), 2);
                $used_tables = count(array_filter($this->table_agenda));
                $cpData["Grant_marginal_gap"] = $grant_percent;
                $cpData["Wishes_not_Granted"] = $not_granted;
                $cpData["Wishes_Granted"] = $this->count_wishes($this->table_agenda);
                $cpData["not_granted_percent"] = $not_granted_percent;
                $cpData["Used_Tables"] = $used_tables;
                $cpData["Free_Tables"] = json_encode(array_diff(array_keys($this->table_agenda), $this->tables));

                $cpData["Min_Business"] = min(array_map("count", $this->business_agenda));
                $cpData["Max_Business"] = max(array_map("count", $this->business_agenda));
                $min = $this->get_min_business();
                $min_arr = array(array('id', 'CUIT', 'Nombre'));

                foreach ($min as $b1) {
                        $rs = $this->meeting->get_data($b1);
                        $min_arr[] = array(
                            $rs['id'],
                            $rs['1695'],
                            $rs['1693'],
                        );
                }

                $max_arr = array(array('id', 'CUIT', 'Nombre'));
                $max = $this->get_max_business();
                foreach ($max as $b1) {
                        $rs = $this->meeting->get_data($b1);
                        $max_arr[] = array(
                            $rs['id'],
                            $rs['1695'],
                            $rs['1693'],
                        );
                }
                $cpData["Free_Business"] = $this->table->generate($min_arr);
                $cpData["Busiest_Business"] = $this->table->generate($max_arr);

                //$this->print_tables();

                $this->parser->parse('process-es', $cpData);
        }

        function agendas($wishlist) {

                //---build time array
                $this->tables_count = count($this->tables);
                $possible_meets = count($this->intervals) * $this->tables_count;
                $wishes_count = $this->count_wishes($wishlist);
                if ($possible_meets < $wishes_count) {
                        // show_error("Total wishes($wishes_count) surpases possible meets ($possible_meets)");
                        $needed_tables = ceil($wishes_count / count($this->intervals));
                        $needed_periods = ceil($wishes_count / count($this->tables)) - count($this->intervals);
                        $this->msg("<span style='color:red'>Total wishes($wishes_count) surpases possible meets ($possible_meets)<br/>You will need at least: " . $needed_tables . " tables<br/>Or add $needed_periods periods to your scheme</span>");
                }



                //---make linear array from wishes
                //---Get linear wishes
                $wishes = $this->linear($wishlist);
                //----try to satisfy all meetings
                //----try to grant all wishes
                foreach ($wishes as $wish) {
                        $b1 = (float) $wish[0];
                        $b2 = (float) $wish[1];
                        $free = false;
                        $table = false;
                        //---get an available p for both business
                        $free = $this->get_free_interval($b1, $b2);
                        //---get available table
                        foreach ($free as $p) {
                                $table = $this->get_table($b1, $b2, $p);
                                if ($table) {
                                        break;
                                }
                        }
                        if ($table) {
                                //---add interview to b1
                                $this->business_agenda[$b1][$p] = array('business' => $b2, 'table' => $table);
                                //---add interview to b2
                                $this->business_agenda[$b2][$p] = array('business' => $b1, 'table' => $table);
                                //---add interview to Table
                                $this->table_agenda[$table][$p] = array('business1' => $b1, 'business2' => $b2);
                                //$this->msg("wish:$i: $b1 -> $b2 $p $table");
                                $this->wishes_granted[] = $wish;
                        } else {
                                //---wish can't be granted
                                $this->wishes_not_granted[] = $wish;
                        }
                        //echo '<hr/>';
                }
                //---save table agenda
                //ksort($this->table_agenda);
                $this->meeting->store_tables($this->table_agenda);
                $this->table_agenda['_id'] = null;
                $this->table_agenda = array_filter($this->table_agenda);
                //---save Business agenda
                $this->meeting->store_agenda_business($this->business_agenda);
                $this->business_agenda['_id'] = null;
                $this->business_agenda = array_filter($this->business_agenda);
        }

        function optimize() {
                /* Get the business that has full agenda and asign them a fixed table */
                $opt_business = $this->get_max_business();
                echo "optimizing:" . count($opt_business) . '<br>';
                $reprocess = false;
                $i = 0;
                foreach ($opt_business as $b1) {
                        if (!in_array($b1, $this->fixed_tables)) {
                                $this->fixed_tables[$b1] = $this->tables[$i];
                                $i++;
                                $reprocess = true;
                        }
                }
                $this->optimized_business = count($opt_business);
                //var_dump($this->fixed_tables);
                if ($reprocess) {
                        $this->prepare();
                        $this->agendas($this->dups);
                        $this->agendas($this->wishlist);
                }
        }

        function print_tables() {
                $this->load->helper('html');
                $cpData = array();
                $cpData['title'] = 'Ver Mesa';
                $cpData['base_url'] = $this->base_url;
                $cpData['module_url'] = $this->module_url;
                //---load tables from db
                $this->table_agenda = $this->meeting->get_tables();
                //---remove mongoid
                $this->table_agenda['_id'] = null;
                $this->table_agenda = array_filter($this->table_agenda);
                $this->tables = array_keys($this->table_agenda);
                $tables = $this->tables;
                if ($this->input->post('table') <> '') {
                        $tables = array($this->tables = array($this->input->post('table')));
                }
                //var_dump($tables);exit;
                $attributes = array(
                    'data-role' => 'listview',
                    'id' => 'mymeetings'
                );
                //ksort($this->tables,6);
                foreach ($this->tables as $table) {
                        echo "<h2 style='color:#018ad1'>Mesa:" . $table . '</h2>';
                        if (isset($this->table_agenda[$table])) {
                                foreach ($this->table_agenda[$table] as $key => $meet) {
                                        $arr[$key] = "<h3 class='ui-li-heading'>$key</h3>";
                                        $arr[$key] .= $this->meeting->get_name($meet['business1']) . '<br/>' . $this->meeting->get_name($meet['business2']);
                                }

                                echo ul($arr, $attributes);
                        } else {
                                echo "No hay citas para mesa $table";
                        }
                }
                //$this->ui->makeui('jquerymobile.ui.php', $cpData);
        }

        function print_wishes($cuit = null) {
                $cuit = ($cuit) ? $cuit : $this->input->post('cuit');
                $b1 = $this->meeting->get_empresa_cuit($cuit);
                $cpData = $this->meeting->get_data($b1['id']);
                if (isset($cpData['7466'])) {
                        foreach ($cpData['7466'] as $b2) {
                                $arr = $this->meeting->get_data($b2);
                                if (isset($arr['accredited'])) {
                                        $arr['accredited'] = ($arr['accredited']) ? true : false;
                                } else {
                                        $arr['accredited'] = false;
                                }
                                $cpData['wishes'][] = $arr;
                        }
                        //var_dump($cpData);exit;
                        @$this->parser->parse('business_wishes', $cpData);
                } else {
                        echo "La empresa no ha seleccionado a nadie para reunirse";
                }
        }

        function print_business($cuit = null) {
                $cuit = ($cuit) ? $cuit : $this->input->post('cuit');
                $b1 = $this->meeting->get_empresa_cuit($cuit);
                $this->load->helper('html');
                $attributes = array(
                    'data-role' => 'listview',
                    'id' => 'mymeetings'
                );
                $this->business_agenda = $this->meeting->get_agenda_business();

                if (isset($this->business_agenda[$b1['id']])) {

                        foreach ($this->business_agenda[$b1['id']] as $key => $b2) {
                                $cpData['meetings'][] = array(
                                    'time' => $key
                                        ) + $this->meeting->get_data($b2['business']) + $b2;
                                $arr[$key] = "<h3 class='ui-li-heading'>$key</h3>";
                                $arr[$key] .= $this->meeting->get_name($b2['business']);
                                $arr[$key] .= "<span class='ui-li-count'>mesa:" . $b2['table'] . "</span>";
                        }
                        //echo ul($arr, $attributes);
                        @$this->parser->parse('business_agenda', $cpData);
                } else {
                        echo "La empresa no tiene Agenda generada";
                }
        }

        function msg($msg) {
                echo "<h3>$msg</h3></hr>";
        }

        function get_table($b1, $b2, $p) {
                $table = false;
                $tables = array_diff($this->tables, $this->fixed_tables);
                ///---check fixed table 4 b1
                if (isset($this->fixed_tables[$b1])) {
                        $ptable = $this->fixed_tables[$b1];
                        if (!isset($this->table_agenda[$ptable][$p])) {
                                $table = $this->fixed_tables[$b1];
                        }
                }

                ///---check fixed table 4 b2
                if (isset($this->fixed_tables[$b2])) {
                        $ptable = $this->fixed_tables[$b2];
                        if (!isset($this->table_agenda[$ptable][$p])) {
                                $table = $this->fixed_tables[$b2];
                        }
                }
                //---find available table if no one has fixed seats
                if (!$table) {
                        //----get the most nearest table (in time or distance)
                        foreach ($tables as $ptable) {
                                if (!isset($this->table_agenda[$ptable][$p])) {
                                        $table = $ptable;
                                        break;
                                }
                        }
                }
                return $table;
        }

        function get_free_interval($b1, $b2) {
                $free1 = array();
                $free2 = array();
                foreach ($this->intervals as $p) {
                        if (!isset($this->business_agenda[$b1][$p]))
                                $free1[] = $p;
                        if (!isset($this->business_agenda[$b2][$p]))
                                $free2[] = $p;
                }
                $free = array_intersect($free2, $free1);
                if (!count($free)) {
                        //cant find free times for these 2
                }
                return $free;
        }

        function remove_dups(&$wishlist) {
                //@todo priorize dups
                //var_dump($wishlist);
                foreach ($wishlist as $b1 => $wish) {
                        foreach ($wish as $b2) {
                                if (isset($wishlist[$b2])) {
                                        if (in_array($b1, $wishlist[$b2])) {
                                                //----remove b1 from b2 wishlist
                                                $wishlist[$b2] = array_diff($wishlist[$b2], array($b1));
                                                //----remove b2 from b1 wishlist
                                                $wishlist[$b1] = array_diff($wishlist[$b1], array($b2));
                                                //---send dups to dup array to process them first
                                                $this->dups[$b1][] = $b2;
                                        }
                                }
                        }
                }
        }

        function count_wishes($wishlist) {
                $count = 0;
                foreach ($wishlist as $wish) {
                        $count+=count($wish);
                }
                return $count;
        }

        function linear($wishlist) {
                //---linearize wishes so we can process them sequentialy
                $linear = array();
                $i = 0;
                foreach ($wishlist as $b1 => $wish) {
                        foreach ($wish as $b2) {
                                //$linear[] = array('b1'=>$b1,'b2'=>$b2,'i'=>$i);
                                $linear[] = array($b1, $b2, $i++);
                        }
                }
                $order = array();
                //---democratize wishes: try to grant first wish first for every business
                foreach ($linear as $key => $row) {
                        $order[$key] = $row[2];
                }
                array_multisort($order, SORT_ASC, $linear);
                return $linear;
        }

        function pdf_business($debug = false) {
                $this->load->library('cezpdf');
                $this->load->helper('pdf');

                //---load data from db
                $this->business_agenda = $this->meeting->get_agenda_business();
                //---remove mongoid
                $this->business_agenda['_id'] = null;
                $this->business_agenda = array_filter($this->business_agenda);
                $this->business = array_keys($this->business_agenda);
                //----Start ordering
                foreach($this->business as $b1_id){
                        $b1=$this->meeting->get_data($b1_id);
                        $business_order[$b1_id]=$b1['1693'];
                }
                asort($business_order);
                prep_pdf();

                $col_names = array(
                    'time' => 'Hora',
                    'table' => 'Mesa',
                    'business' => 'Empresa'
                );
                $i = 0;
                //---begin process

                foreach ($business_order as $b1=>$cuit) {
                        $data = $this->meeting->get_data($b1);
                        $db_data = array();
                        foreach ($this->business_agenda[$b1] as $interval => $meetingData) {
                                $b2 = $this->meeting->get_name($meetingData['business']);
                                $db_data[$interval] = array('time' => $interval, 'business' => $b2, 'table' => $meetingData['table']);
                                ksort($db_data);
                        }
                        if ($debug) {
                                var_dump($db_data);
                        } else {
                                $this->cezpdf->ezTable($db_data, $col_names, 'Lista de Entrevistas:' . $data['1693'], array('width' => 550));
                                $this->cezpdf->ezSetY(0);
                        }
                        /*
                          if ($i > 3)
                          break;
                         */
                        $i++;
                        //$this->cezpdf->newPage();
                        //break;
                }


                if (!$debug) {
                        $this->cezpdf->ezStream();
                } else {
                        //var_dump('');
                }
        }

        function pdf_tables($debug = false) {
                $this->load->library('cezpdf');
                $this->load->helper('pdf');

                //---load data from db
                $this->table_agenda = $this->meeting->get_tables();
                //---remove mongoid
                $this->table_agenda['_id'] = null;
                $this->table_agenda = array_filter($this->table_agenda);
                $this->tables = array_keys($this->table_agenda);
                prep_pdf();

                $col_names = array(
                    'time' => 'Hora',
                    'business1' => 'Empresa1',
                    'business2' => 'Empresa2'
                );
                $i = 0;
                //---begin process

                foreach ($this->tables as $b) {
                        $db_data = array();
                        foreach ($this->table_agenda[$b] as $interval => $meetingData) {
                                $b1 = $this->meeting->get_name($meetingData['business1']);
                                $b2 = $this->meeting->get_name($meetingData['business2']);
                                $db_data[] = array('time' => $interval, 'business1' => $b1, 'business2' => $b2);
                        }
                        if ($debug) {
                                var_dump($db_data);
                        } else {
                                $this->cezpdf->ezTable($db_data, $col_names, 'Lista de Entrevistas:' . $b, array('width' => 550));
                                $this->cezpdf->ezSetY(0);
                        }
                        /*
                          if ($i > 3)
                          break;
                         */
                        $i++;
                        //$this->cezpdf->newPage();
                        //break;
                }


                if (!$debug) {
                        $this->cezpdf->ezStream();
                } else {
                        //var_dump('');
                }
        }

}