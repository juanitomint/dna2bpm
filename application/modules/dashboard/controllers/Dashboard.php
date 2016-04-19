<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/**
 * dna2
 * 
 * Description of the class dna2
 * 
 * @author Juan Ignacio Borda <juanignacioborda@gmail.com>
 * @date   Mar 23, 2013
 */
class Dashboard extends MX_Controller {

    function __construct() {
        parent::__construct();
        $this->load->config('dashboard/config');
        $this->load->library('parser');
        $this->load->library('ui');
        $this->load->model('app');
        $this->load->model('bpm/bpm');
        $this->load->model('msg');

        //---base variables
        $this->base_url = base_url();
        $this->module_url = base_url() . $this->router->fetch_module() . '/';
        $this->user->authorize();
        //---update session ttl
        $this->session->sess_update();
        //----LOAD LANGUAGE
        $this->lang->load('library', $this->config->item('language'));
        $this->idu = $this->user->idu;
    }

    function Application($idapp) {
        $this->load->model('bpm/kpi_model');
        $customData = array();
        $user = $this->user->get_user($this->idu);
        $app = $this->app->get_app($idapp);
        $models = $this->Getmodels($app['objs']);
        $models_array = array();
        foreach ($models as &$thisModel) {
            $models_array[] = $thisModel['idwf'];
        }
        //---search in cases involved and sum
        $cases_data = $this->bpm->get_cases($this->idu);
        array_unique($models_array);

//---search in cases involved and sum
        foreach ($cases_data['cases'] as $thisCase) {
            @$arr_sum[$thisCase['idwf']]+=1;
        }
        foreach ($models as &$thisModel) {
            $db_wf = $this->bpm->load($thisModel['idwf']);
            $wf = $this->bpm->bindArrayToObject($db_wf['data']);
            $thisModel['sum'] = (isset($arr_sum[$thisModel['idwf']])) ? $arr_sum[$thisModel['idwf']] : 0;
            $kpis = $this->kpi_model->get_model($thisModel['idwf']);
            $kpi_show = array();
            //----PROCESS KPIS
            foreach ($kpis as $kpi) {
                $kpi_show[] = Modules::run('bpm/kpi/render', $kpi);
            }
            $customData['kpi'] = implode($kpi_show);
            /* token statistics 
              foreach ($cases_data['cases']as $thisCase) {
              if ($thisCase['idwf'] == $thisModel['idwf']) {

              $tokens = $this->bpm->get_tokens($thisCase['idwf'], $thisCase['id'], null);
              foreach ($tokens as $thisToken) {
              //form token array
              @$TTOKENS[$thisToken['resourceId']]['qtty']+=1;
              $shape = $this->bpm->get_shape($thisToken['resourceId'], $wf);
              @$TTOKENS[$thisToken['resourceId']]['name'] = $shape->properties->name;
              @$TTOKENS[$thisToken['resourceId']]['documentation'] = $shape->properties->documentation;
              }
              }
              }
              var_dump($TTOKENS);
              exit;
             */
        }
        //var_dump($models);
        $customData['app_models'] = $models;
        $customData['app'] = $app;
        $customData['app_title'] = '<i class="icon ' . $app['icon'] . '"></i>' . $app['title'];
        $this->render('application', $customData);
    }

    function Getmodels($arr) {

        $rtnArr = array();
        foreach ($arr as $item) {
            if ($item['idobj'][0] == 'M') {
                $idbpm = substr($item['idobj'], 1);
                $bpm = (array) $this->bpm->get_model($idbpm);
                $rtnArr[] = $bpm + $bpm['data']['properties']; //---Flatten information a little
            }
        }
        return $rtnArr;
    }

    function Index() {
        $dashboard = ($this->session->userdata('json')) ? $this->session->userdata('json'):null;
         $this->hooks_group();
        if ($this->user->isAdmin()) {
            $dashboard = 'dashboard/json/admin.json';
        }
        if($dashboard<>''){
        $this->Dashboard($dashboard);
        } else {
        $this->Dashboard($this->config->item('default_dashboard'));
        }
    }

    function Show($file, $debug = false) {
        //---only admins can debug
        $debug = ($this->user->isAdmin()) ? $debug : false;
        if (!is_file(APPPATH . "modules/dashboard/views/json/$file.json")) {
            // Whoops, we don't have a page for that!
            return null;
        } else {
            $myconfig = json_decode($this->load->view("dashboard/json/$file.json", '', true), true);
            if (isset($myconfig['private']) && $myconfig['private'] == true) {
                return;
            }
            $this->Dashboard("dashboard/json/$file.json", $debug);
        }
    }

    // =========== New Way ===========


    function menu() {
        $customData['base_url'] = $this->base_url;
        $customData['module_url'] = $this->module_url;
        $customData['lang'] = $this->lang->language;
        $customData['is_admin']=$this->user->isAdmin();
        //---load custom menu
        $menu_custom = Modules::run('menu/get_menu', '0', 'sidebar-menu', !$this->user->isAdmin());
        $customData['menu_custom'] = $this->parser->parse_string($menu_custom, $customData, TRUE, TRUE);
        //----check if extra library exists and load it 
        if (is_file( APPPATH . "modules/dashboard/libraries/menu_extra.php")) {
            $this->load->library('dashboard/menu_extra');
            
            $customData['menu_custom'].=$this->menu_extra->get();
        }
        return $this->parser->parse('dashboard/menu', $customData, true, true);
    }

    function hooks_group($user = null) {
        $user = ($user) ? $user : $this->user->get_user((int) $this->idu);
        if (is_file(APPPATH . "modules/dashboard/views/hooks/groups.json")) {
            $config = json_decode($this->load->view('hooks/groups.json', '', true));
            foreach ($config->hooks as $hook) {
                if (array_intersect($user->group, $hook->group))
                    redirect($this->base_url . $hook->redir);
            }
        }
    }

    // ==== Dashboard

    function Dashboard($json = 'dashboard/json/dashboard.json',$debug = false,$extraData=null) {


        /* eval Group hooks first */
        $this->session->set_userdata('json', $json);
        $user = $this->user->get_user((int) $this->idu);
        $myconfig = $this->parse_config($json, $debug);

        $layout = ($myconfig['view'] <> '') ? $myconfig['view'] : 'layout';
        $customData = $myconfig;
        $customData['lang'] = $this->lang->language;
        $customData['alerts']=Modules::run('dashboard/alerts/get_my_alerts');
        $customData['brand'] = $this->config->item('brand');
        $customData['menu'] = $this->menu();
        $customData['avatar'] = Modules::run('user/profile/get_avatar'); //Avatar URL
        $customData['base_url'] = $this->base_url;
        $customData['module_url'] = $this->module_url;
        $customData['inbox_count'] = $this->msg->count_msgs($this->idu, 'inbox');
        $customData['config_panel'] =$this->parser->parse('_config_panel',  $customData['lang'], true, true);
        
        $customData['name'] = $user->name . ' ' . $user->lastname;

        // Global JS
        $customData['global_js'] = array(
            'base_url' => $this->base_url,
            'module_url' => $this->module_url,
            'myidu' => $this->idu,
            'lang'=>$this->config->item('language')
        );


        // Toolbar
        $customData['toolbar_inbox'] = Modules::run('inbox/inbox/toolbar');

        //$customData['js']=array('daterangerpicker'); // Just handles must be registered in UI
        /*
          Custom JS Example
          $customData['js']=array('knob','jquery'); // Just handles must be registered in UI
          $customData['js']=array('app'=>$this->module_url."assets/jscript/knob.js"); // Complete
          Custom CSS
          $customData['css']=array('style'=>$this->module_url."assets/css/style.css");
         */

        // Flush!
        //var_dump(array_keys($customData));exit; 
//          var_dump($customData);  
//          exit(); 
        /*
         * Adds extra data if passed
         */

        if($extraData){
         $customData=$extraData+$customData;  
        }
        
        $this->ui->compose($layout, $customData);
    }

    
    // ==== Tiles fixed
    function tile_admin_users() {
        $data['lang'] = $this->lang->language;
        $data['base_url'] = $this->base_url;
        $data['module_url'] = $this->module_url;
        return $this->parser->parse('tiles/admin_users', $data, true, true);
    }

    function tile_admin_bpm() {
        $data['lang'] = $this->lang->language;
        $data['base_url'] = $this->base_url;
        $data['module_url'] = $this->module_url;
        return $this->parser->parse('tiles/admin_bpm', $data, true, true);
    }

    function tile_admin_menu() {
        $data['lang'] = $this->lang->language;
        $data['base_url'] = $this->base_url;
        $data['module_url'] = $this->module_url;
        return $this->parser->parse('tiles/admin_menu', $data, true, true);
    }

    function tile($template, $data) {
        $darr=debug_backtrace();
        if(!isset($data['id']))
            $data['id']=isset($darr[3]['function'])? $darr[3]['function']:'';
        $data['lang'] = $this->lang->language;
        $data['more_info_text'] = (isset($data['more_info_text'])) ? $data['more_info_text'] : $this->lang->line('more_info');
        return $this->parser->parse($template, $data, true);
    }
    function widget($template, $data) {
        $data['lang'] = $this->lang->language;
        $data['more_info_text'] = (isset($data['more_info_text'])) ? $data['more_info_text'] : $this->lang->line('more_info');
        return $this->parser->parse($template, $data, true);
    }

    // ==== Tasks

    function twocols() {
        $this->dashboard('twocols', 'layout_2cols');
    }

    // ============ Parse JSON config
    function parse_config($file, $debug = false) {
        $myconfig = json_decode($this->load->view($file, '', true), true);
        $minwidth=2;
//             $return['js'] = array();
        //Root config

        foreach ($myconfig as $key => $value) {
            if ($key != 'zones')
                $return[$key] = $value;
        }
        $return['js']=array();
        $return['css']=array();
        $return['inlineJS']="";
        // CSS 
 		if(isset($myconfig['css'])){
 			foreach($myconfig['css'] as $item){
 				foreach($item as $k=>$v){
 					if(is_numeric($k))
 						$return['css'][]=$v;
 					else{
 						$return['css'][$k]=$v;
 					}
 				}
 			}
 		}
    	// JS 
 		if(isset($myconfig['js'])){
 			foreach($myconfig['js'] as $item){
 				foreach($item as $k=>$v){
 					if(is_numeric($k))
 						$return['js'][]=$v;
 					else{
 						$return['js'][$k]=$v;
 					}
 				}
 			}		
 		}
        //Zones
        foreach ($myconfig['zones'] as $zones) {
            $content = "";
            $widgets = array();
            $spans = array();
            $myzone = current($zones);
            $myzone_key = key($zones);
            $empty_spans = 0;
            $no_span = false;

            
            foreach ($myzone as $item) {
                $widgets[] = $item;
                $spans[] = (empty($item["span"])) ? ($minwidth) : ($item["span"]);
                if (isset($item["span"]))
                    $empty_spans++;
            }
            //$content.="<div class='row zone_$myzone_key  '>";
            $Qspan = 0;
            $markup='';
            foreach ($widgets as $k => $myWidget) {

                // Span handle
                $myspan = $spans[$k];
                $next = (isset($spans[$k + 1])) ? ($spans[$k + 1]) : (null);
                $fit_in = ($myspan + $Qspan < 13) ? (true) : (null);
                // Open div		
                //if($Qspan==0)$content.="<div class='mywidget '>";
                if ($fit_in) {
                    // There is space for this col
                    if ($next) {
                        // we have more cols
                        if ($Qspan + $myspan + $next < 13) {
                            $span = $myspan;
                            $Qspan = ($Qspan + $span == 12) ? (0) : ($Qspan + $span);
                        } else {
                            $span = 12 - $Qspan;
                            $Qspan = 0;
                        }
                    } else {
                        $span = 12 - $Qspan;
                        $Qspan = 0;
                    }
                } else {
                    // col too big
                    $span = $myspan;
                }
                //

                if (isset($myWidget['params'])) {
                    $args = $myWidget['params'];
                    array_unshift($args, $myWidget['module'] . '/' . $myWidget['controller'] . '/' . $myWidget['function']);
                    $markup = $widget = call_user_func_array(array('Modules', 'run'), $args);                   
                } else {
                    $markup = $widget = Modules::run($myWidget['module'] . '/' . $myWidget['controller'] . '/' . $myWidget['function']);
                }
                if ($debug)
                    $markup = $myWidget['module'] . '/' . $myWidget['controller'] . '/' . $myWidget['function'] . $markup;

                // Si es un array uso el zonekey para identificar el markup
                if(isset($markup)){
                    if(is_array($markup)){
                    	$mycontent=$markup['content'];
                    	// inlineJS
                    	if(isset($markup['inlineJS']))
                    	$return['inlineJS'].=$markup['inlineJS'];
                    }else{
                    	$mycontent=$markup;
                    }
                }else{
                    $markup="";
                    $mycontent="";
                }
                
            //==== Box Management  ========================= 
            
                
            if(isset($myWidget['box_class'])){
               // box info present, use this box bitch --
                $customData=array();
                $customData['box_class']=implode(" ",$myWidget['box_class']);
                // Color schema
                $customData['btn_class']='btn-default';
                if(in_array('box-primary',$myWidget['box_class']))$customData['btn_class']='btn-primary';
                if(in_array('box-danger',$myWidget['box_class']))$customData['btn_class']='btn-danger';
                if(in_array('box-info',$myWidget['box_class']))$customData['btn_class']='btn-info';
                if(in_array('box-warning',$myWidget['box_class']))$customData['btn_class']='btn-warning';
                if(in_array('box-success',$myWidget['box_class']))$customData['btn_class']='btn-success';
                $customData['title']=(isset($myWidget['title']))?($myWidget['title']):('');
                $customData['box_icon']=(isset($myWidget['box_icon']))?($myWidget['box_icon']):('');
                $customData['content']=$mycontent;
                //Buttons
                $customData['button_collapse']=(in_array('collapse',$myWidget['box_buttons']))?("1"):("0");
                $customData['button_remove']=(in_array('remove',$myWidget['box_buttons']))?("1"):("0");
                // Collapsed init ?
                if(isset($myWidget['box_collapsed']) && $myWidget['box_collapsed']=='1'){
                    $customData['box_class'].=' collapsed-box';
                    $customData['body_style']='display:none';
                }
                        
                $mycontent=$this->parser->parse('dashboard/widgets/box', $customData, true, true);
            }    
                if (!$empty_spans)
                	$content.=$mycontent;
                else
                	$content.="<div class='col-lg-$span '>$mycontent</div>";
                

				// CSS 
 				if(isset($myWidget['css']))
 					foreach($myWidget['css'] as $item){
	 					foreach($item as $k=>$v){
	 						if(is_numeric($k))
	 							$return['css'][]=$v;
	 						else{
	 							$return['css'][$k]=$v;
	 						}
	 					}
 					}
 					
            	// JS 
 				if(isset($myWidget['js']))
 					foreach($myWidget['js'] as $item){
	 					foreach($item as $k=>$v){
	 						if(is_numeric($k))
	 							$return['js'][]=$v;
	 						else{
	 							$return['js'][$k]=$v;
	 						}
	 					}
 					}		
            }
            
            // $content.='</div>';
            // Por si el widget devuelve un array en lugar del contenido solamente
            if (is_array($markup)) {
                if (isset($markup['content']))
                    unset($markup['content']); // Content ahora es $myzone_key
                $return+=$markup;
            }


            $return[$myzone_key] = $content;
        }

        return $return;
    }

    // ============ Inbox
    function Inbox($data = array()) {
        $this->dashboard('dashboard/json/inbox.json');
    }

    // ============ Profile
    function Profile($debug=false) {
        $extraData['js'] = array(
         		'jqueryUI', 'PLUpload',
         		$this->base_url."user/assets/jscript/profile.js" => 'profile JS',
                $this->base_url."jscript/jquery-validation/dist/jquery.validate.min.js"=>"Validate",
                $this->base_url."jscript/jquery-validation/dist/additional-methods.min.js"=>"Aditional Methods",
         );
        //----add custom language validator
         switch($this->config->item('language')){
             case "spanish":
                $extraData['js'][$this->base_url."jscript/jquery/plugins/jquery-validate/localization/messages_es.js"]="Validator Locale";
                 break;
         }
         
        $this->dashboard('dashboard/json/profile.json',$debug,$extraData);
    }

    // ============ Tasks
    function Tasks($data = array()) {
        $this->dashboard('dashboard/json/tasks.json');
    }
    
    
    // ============ Widgets

    // function box_primary($data = array()) {
    //     return $this->parser->parse('widgets/box_primary', $data, true, true);
    // }


    function widget_dashboards() {
        $this->load->helper('file');
        $data['title'] = 'Dashboards';
        $data['base_url'] = $this->base_url;
        $data['module_url'] = $this->module_url;
        $files = get_filenames(APPPATH . 'modules/dashboard/views/json/');
        $data['qtty'] = count($files);

        foreach ($files as $file) {
            $config = json_decode($this->load->view("json/$file", '', true), true);
            $config['dash_name'] = str_replace('.json', '', $file);
            $data['dashboards'][] = $config;
        }
//        var_dump($data);
//        exit;

        return $this->parser->parse('widgets/dashboards', $data, true, true);
    }
    


}

/* End of file dna2 */
/* Location: ./system/application/controllers/welcome.php */
