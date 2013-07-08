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
class Dna2 extends MX_Controller {

    function __construct() {
        parent::__construct();
        $this->load->config('config');
        $this->load->library('parser');
        $this->load->library('ui');
        $this->load->model('app');
        $this->load->model('user/user');
        $this->load->model('user/rbac');
        $this->load->model('bpm/bpm');
        $this->load->model('msg');

        //---base variables
        $this->base_url = base_url();
        $this->module_url = base_url() . 'dna2/';
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

    function Dashboard() {
        $customData = array();
        $customData['base_url'] = $this->base_url;
        $customData['module_url'] = $this->module_url;
        $customData['title'] = 'Dashboard';
        //var_dump($cpData);
        //----get taks info
        $cases_data = $this->bpm->get_cases($this->idu, 0, 5, array('open'));
        $customData['cases'] = $cases_data['cases'];
        $customData['widget_count'] = count($cases_data['cases']);
        $customData['widget_title'] = $this->lang->line('openCases');
        $customData['widget_icon'] = 'icon-folder-open';
        $customData['cases_widget'] = $this->parser->parse('cases', $customData, true);

        //Inbox Msgs
        $mymgs = $this->msg->get_msgs($this->idu);
        $customData['inbox_count'] = $mymgs->count();

        $customData['show_task_detail'] = true;
        $customData['dashboard_class'] = 'active';
        $this->render('dashboard', $customData);
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
        $this->Dashboard();
    }

    function Mytasks() {
        $customData['user'] = (array) $this->user->get_user($this->idu);

        //----get taks info
        $customData['base_url'] = $this->base_url;
        $customData['module_url'] = $this->module_url;
        $cases_data = $this->bpm->get_cases($this->idu, 0, 5, array('open'));
        $customData['totalCases'] = $cases_data['totalCases'];
        $customData['openCases'] = count($cases_data['cases']);
        $customData['cases'] = $cases_data['cases'];
        //---open cases
        $customData['widget_count'] = count($cases_data['cases']);
        $customData['widget_title'] = $this->lang->line('openCases');
        $customData['widget_icon'] = 'icon-folder-open';
        $customData['widget_col1'] = $this->parser->parse('cases', $customData, true);
        //---Closed
        $cases_data = $this->bpm->get_cases($this->idu, 0, 5, array('closed'));
        $customData['cases'] = $cases_data['cases'];
        $customData['widget_count'] = count($cases_data['cases']);
        $customData['widget_title'] = $this->lang->line('closedCases');
        $customData['widget_icon'] = 'icon-folder-close';
        $customData['widget_col2'] = $this->parser->parse('cases', $customData, true);

        $customData['show_task_detail'] = true;
        $customData['mytasks_class'] = 'active';
        $customData['title'] = 'My Tasks';
        $this->render('mytasks', $customData);
    }

    function render($file, $customData) {

        $this->load->model('user/user');
        $this->load->model('app');
        $this->load->model('bpm/bpm');
        $this->user->authorize();
        $cpData = $this->lang->language;
        $segments = $this->uri->segment_array();
        $cpData['nolayout'] = (in_array('nolayout', $segments)) ? '1' : '0';
        //var_dump($level);
        $cpData['theme'] = $this->config->item('theme');
        $cpData['base_url'] = $this->base_url;
        $cpData['module_url'] = $this->module_url;
        //---define files to viewport

        $cpData['global_js'] = array(
            'base_url' => $this->base_url,
            'module_url' => $this->module_url,
        );
        $user = $this->user->get_user($this->idu);
        $cpData['user'] = (array) $user;
        $cpData['isAdmin'] = $this->user->isAdmin($user);

        //----Cases from DB
        $cases_data = $this->bpm->get_cases($this->idu, 0, 5, array('open'));
        $cpData['totalCases'] = $cases_data['totalCases'];
        $cpData['openCases'] = count($cases_data['cases']);


        /*
         * @todo let the user choose if want to see all cases or only open
         */
        $cpData['cases'] = $cases_data['cases'];
        //----get Apps from DB
        $apps = $this->app->get_apps();
        if ($apps) {
            //----check if the user has access to thi app
            foreach ($apps as $thisApp) {
                $authorized = false;
                if (isset($thisApp['groups'])) {
                    foreach ($thisApp['groups'] as $idgroup) {
                        if (in_array($idgroup, $user->group)) {
                            $authorized = true;
                            break;
                        }
                    }
                }
                //if ($this->user->has('root/modules/application/' . $thisApp['idapp']) or $this->user->isAdmin($user)) {
                if ($authorized or $this->user->isAdmin($user)) {
                    $cpData['apps'][] = array(
                        'icon' => isset($thisApp['icon']) ? $thisApp['icon'] : 'icon-list-alt',
                        'name' => isset($thisApp['title']) ? $thisApp['title'] : $thisApp['idapp'] . '(???)',
                        'link' => $this->base_url . 'dna2/application/' . $thisApp['idapp'],
                        'target' => '_self'
                    );
                }
            }
            $cpData['apps']['SumApps'] = count($cpData['apps']);
        }

        /* Inbox Count MSgs */
        $mymgs = $this->msg->get_msgs($this->idu);
        $cpData['inbox_count'] = $mymgs->count();


        $cpData+=$customData;
        $this->ui->compose($file, 'dna2/unicorn.ui.php', $cpData);
    }

}

/* End of file dna2 */
/* Location: ./system/application/controllers/welcome.php */
