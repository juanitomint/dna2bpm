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
        $this->load->model('user/user');
        $this->load->config('config');
        $this->load->library('parser');
        $this->load->library('ui');
        $this->load->model('app');
        $this->load->model('bpm/bpm');
        $this->load->model('msg');

        //---base variables
        $this->base_url = base_url();
        $this->module_url = base_url() . $this->router->fetch_module() . '/';
        $this->user->authorize();
        //----LOAD LANGUAGE
        $this->lang->load('library', $this->config->item('language'));
        $this->idu = (int) $this->session->userdata('iduser');
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
        if ($this->user->isAdmin()) {

            $this->Dashboard('admin');
        } else {

            $this->Dashboard();
        }
    }

    function Show($json,$debug=false) {
        //---only admins can debug
        $debug=($this->user->isAdmin()) ? $debug:false;
        $this->Dashboard($json,$debug);
    }

    // =========== New Way ===========


    function menu() {
        $customData['base_url'] = $this->base_url;
        $customData['module_url'] = $this->module_url;
        $customData['lang'] = $this->lang->language;

        return $this->parser->parse('menu', $customData, true);
    }

    // ==== Dashboard

    function Dashboard($json = 'dashboard',$debug=false) {

        $myconfig = $this->parse_config($json,$debug);

        $layout = ($myconfig['view'] <> '') ? $myconfig['view'] : 'layout';
        $customData = $myconfig;
        $customData['menu'] = $this->menu();
        $customData['avatar'] = Modules::run('user/profile/get_avatar'); //Avatar URL
        $customData['base_url'] = $this->base_url;
        $customData['module_url'] = $this->module_url;
        $user = $this->user->get_user((int) $this->idu);

        $customData['name'] = $user->name . ' ' . $user->lastname;

        // Global JS
        $customData['global_js'] = array(
            'base_url' => $this->base_url,
            'module_url' => $this->module_url,
            'myidu' => $this->idu
        );

        /*
          Custom JS Example
          $customData['js']=array('knob','jquery'); // Just handles must be registered in UI
          $customData['js']=array('app'=>$this->module_url."assets/jscript/knob.js"); // Complete
          Custom CSS
          $customData['css']=array('style'=>$this->module_url."assets/css/style.css");
         */

        // Flush!
        //var_dump(array_keys($customData));exit; 
        // var_dump($customData);    

        $this->ui->compose($layout, $customData);
    }

    // ==== Tiles fixed
    function tile_admin_users() {
        $data['base_url'] = $this->base_url;
        $data['module_url'] = $this->module_url;
        return $this->parser->parse('tiles/admin_users', $data, true, true);
    }

    function tile_admin_bpm() {
        $data['base_url'] = $this->base_url;
        $data['module_url'] = $this->module_url;
        return $this->parser->parse('tiles/admin_bpm', $data, true, true);
    }

    function tile($template, $data) {
        $this->parser->parse($template, $data);
    }

    // ==== Tasks

    function twocols() {
        $this->dashboard('twocols', 'layout_2cols');
    }

    // ============ Parse JSON config
    function parse_config($file,$debug=false) {
        if (!is_file(FCPATH . APPPATH . "modules/dashboard/views/json/$file.json")) {
            // Whoops, we don't have a page for that!
            return null;
        } else {
            $myconfig = json_decode($this->load->view("json/$file.json", '', true), true);

//             $return['js'] = array();
            //Root config
            foreach ($myconfig as $key => $value) {
                if ($key != 'zones')
                    $return[$key] = $value;
            }

            //Zones
            foreach ($myconfig['zones'] as $zones) {
                $content = "";
                $widgets = array();
                $myzone = current($zones);

                $myzone_key = key($zones);


                foreach ($myzone as $item) {
                    $widgets[] = $item;
                }


                // ==== Reparto de columnas
                $cant = count($myzone);
                if ($cant > 6 || $cant < 1)
                    continue;;
                $suma = 0;
                $row = array();
                $auto = 0;
                $resto = 0;
                // Reparto inicial
                foreach ($widgets as $k => $myWidget) {

                    if (!empty($myWidget['span'])) {
                        $row[$k] = $myWidget['span'];
                        $suma+=$myWidget['span'];
                    } else {
                        $row[$k] = 0;
                        $auto++;
                    }
                }
                $resto = 12 - $suma;
                if ($auto != 0) {
                    // Hay sobrantes
                    $span = ($resto / $auto);
                }
                //Segunda pasada
                foreach ($row as $k => $v) {
                    if ($v == 0)
                        $row[$k] = $span;
                }
                // ____ Reparto de columnas


                $content.='<div class="row">';

                foreach ($widgets as $k => $myWidget) {
//                     if (isset($myWidget['js']))
//                         $return["js"] += $myWidget['js'];
                    $span = $row[$k];
                    if ($span == 0)
                        continue;
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
                    if (is_array($markup)) {
                        $content.="<div class='col-lg-$span connectedSortable'>{$markup['content']}</div>";
                    } else {
                        $content.="<div class='col-lg-$span connectedSortable'>$markup</div>";
                    }
                }
                $content.='</div>';

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
    }

    // ============ Profile
    function inbox($data = array()) {
        $this->dashboard('inbox');
    }

    // ============ Profile
    function profile($data = array()) {
        $this->dashboard('profile');
    }

    // ============ Widgets

    function box_primary($data = array()) {
        return $this->parser->parse('widgets/box_primary', $data, true, true);
    }

    function knob($data = array()) {
        return $this->parser->parse('widgets/knob', $data, true, true);
    }

    function widget_dashboards() {
        $this->load->helper('file');
        $data['title'] = 'Dashboards';
        $data['base_url'] = $this->base_url;
        $data['module_url'] = $this->module_url;
        $files = get_filenames(FCPATH . APPPATH . 'modules/dashboard/views/json/');
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