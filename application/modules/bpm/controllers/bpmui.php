<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/**
 * ui
 * 
 * This class renders the graphical elements to dashboards
 * 
 * @author Juan Ignacio Borda <juanignacioborda@gmail.com>
 * @date    Jun 16, 2014
 */
class Bpmui extends MX_Controller {

    function __construct() {
        parent::__construct();
        $this->load->config('config');
        $this->load->model('bpm/bpm');
        $this->load->library('parser');
        $this->base_url = base_url();
        $this->module_url = base_url() . $this->router->fetch_module() . '/';
        $this->idu = (int) $this->session->userdata('iduser');
        //----LOAD LANGUAGE
        $this->lang->load('library', $this->config->item('language'));
        $this->user->isloggedin();
    }

    function time_elapsed_string($datetime, $full = false) {

        $now = new DateTime;
        $ago = new DateTime($datetime);
        $diff = $now->diff($ago);

        $string = array(
            'y' => 'year',
            'm' => 'month',
            'd' => 'day',
            'h' => 'hour',
            'i' => 'minute',
            's' => 'second',
        );
        foreach ($string as $k => &$v) {
            if ($diff->$k) {
                $v = $diff->$k . ' ' . $v . ($diff->$k > 1 ? 's' : '');
            } else {
                unset($string[$k]);
            }
        }

        if (!$full)
            $string = array_slice($string, 0, 1);
        return $string ? implode(', ', $string) . ' ago' : 'just now';
    }

    function Index() {
        echo $this->tile('tasks');
        echo $this->tile('tasks_done');
        echo $this->widget('2do');
        echo $this->widget('cases');
        echo $this->widget('cases_closed');
    }

    function widget($widget, $data = array()) {
        $args = array_slice($this->uri->segments, 4);
        if (method_exists($this, 'widget_' . $widget)) {
            echo call_user_func_array(array($this, 'widget_' . $widget), $args);
        } else {
            echo "There is no widget named: $widget";
        }
    }

    function tile($tile, $data = array()) {
        if ($tile) {
            $args = array_slice($this->uri->segments, 4);
            if (method_exists($this, 'tile_' . $tile)) {
                echo call_user_func_array(array($this, 'tile_' . $tile), $args);
            } else {
                echo "There is no tile named: $tile";
            }
        }
    }

    function tile_tasks() {
        $data['title'] = 'My Tasks';
        $tasks = $this->bpm->get_tasks_byFilter(
                array(
                    'assign' => $this->idu,
                    'status' => 'user',
                )
        );
        $data['number'] = count($tasks);
        $data['icon'] = 'ion-android-checkmark';
        $data['more_info_link'] = $this->base_url . 'dashboard/show/tasks';
        return $this->parser->parse('dashboard/tiles/tile-yellow', $data, true, true);
    }

    function tile_tasks_done() {
        $data['title'] = 'My Tasks Done';
        $tasks = $this->bpm->get_tasks_byFilter(
                array(
                    'assign' => $this->idu,
                    'status' => 'finished',
                    'tasktype' => 'User'
                )
        );
        $data['number'] = count($tasks);
        $data['icon'] = 'ion-android-checkmark';
        $data['more_info_link'] = $this->base_url . 'dashboard/show/tasks';
        return $this->parser->parse('dashboard/tiles/tile-green', $data, true, true);
    }

    function tile_cases($idwf = null) {
        $data['title'] = 'My Cases';
        $cases = $this->bpm->get_cases_byFilter(
                array(
                    'iduser' => $this->idu,
                    'status' => 'open',
                )
        );
        $data['number'] = count($cases);
        $data['icon'] = 'ion-play';
        $data['more_info_link'] = $this->base_url . 'dashboard/show/tasks';
        return $this->parser->parse('dashboard/tiles/tile-blue', $data, true, true);
    }

    function tile_cases_closed($idwf = null) {
        $data['title'] = 'Closed Cases';
        $cases = $this->bpm->get_cases_byFilter(
                array(
                    'iduser' => $this->idu,
                    'status' => 'closed',
                )
        );
        $data['number'] = count($cases);
        $data['icon'] = 'ion-play';
        $data['more_info_link'] = $this->base_url . 'dashboard/show/tasks';
        return $this->parser->parse('dashboard/tiles/tile-teal', $data, true, true);
    }

    function showcase($chunk = 1, $pagesize = 5) {
        echo $this->widget_cases($chunk, $pagesize);
    }

    function show2do($chunk = 1, $pagesize = 5) {
        echo $this->widget_2do($chunk, $pagesize);
    }

    function widget_tasks_done($chunk = 1, $pagesize = 5) {
        //$data['lang']=$this->lang->language;
        $tasks = $this->bpm->get_tasks_byFilter(
                array(
            'assign' => $this->idu,
            'tasktype' => 'User',
            'status' => 'finished',
                ), array(), array('checkdate' => 'desc')
        );
        $data = $this->prepare_tasks($tasks, $chunk, $pagesize);
        $data['title'] = $this->lang->line('Tasks') . ' ' . $this->lang->line('Finished');

        $data['more_info_link'] = $this->base_url . 'bpm/';
        echo $this->parser->parse('bpm/widgets/tasks_done', $data, true, true);
    }

    function widget_2do($chunk = 1, $pagesize = 5) {
        //$data['lang']=$this->lang->language;
        $tasks = $this->bpm->get_tasks_byFilter(
                array(
            'assign' => $this->idu,
            'status' => 'user',
                ), array(), array('checkdate' => 'desc')
        );
        $data = $this->prepare_tasks($tasks, $chunk, $pagesize);
        $data['title'] = $this->lang->line('Tasks') . ' ' . $this->lang->line('Pending');

        $data['more_info_link'] = $this->base_url . 'bpm/';
        echo $this->parser->parse('bpm/widgets/2do', $data, true, true);
    }

    function widget_cases_closed($chunk = 1, $pagesize = 5) {

        $cases = $this->bpm->get_cases_byFilter(
                array(
            'iduser' => $this->idu,
            'status' => 'closed',
                ), array(), array('checkdate' => 'desc')
        );
        $data = $this->prepare_cases($cases, $chunk, $pagesize);
        $data['title'] = $this->lang->line('closedCases');

        $data['more_info_link'] = $this->base_url . 'bpm/';
        echo $this->parser->parse('bpm/widgets/cases_closed', $data, true, true);
    }

    function widget_cases($chunk = 1, $pagesize = 5) {
        $cases = $this->bpm->get_cases_byFilter(
                array(
            'iduser' => $this->idu,
            'status' => 'open',
                ), array(), array('checkdate' => 'desc')
        );
        $data = $this->prepare_cases($cases, $chunk, $pagesize);
        $data['title'] = $this->lang->line('openCases');

        $data['more_info_link'] = $this->base_url . 'bpm/';
        echo $this->parser->parse('bpm/widgets/cases_open', $data, true, true);
    }

    private function prepare_cases($cases, $chunk, $pagesize) {
        $data = array();
        $data['module_url'] = $this->module_url;
        $data['base_url'] = $this->base_url;
        $data['showPager']=false;
        $data['isAdmin'] = $this->user->isAdmin();
        //---get caller 4 urls
        $trace = debug_backtrace();
        $caller = $trace[1]['function'];
        $total = count($cases);
        $data['qtty'] = $total;
        $parts = array_chunk($cases, $pagesize, true);
        $pages = count($parts);
        if ($pages) {
            $tasks = $parts[$chunk - 1];
            foreach ($tasks as $task) {
                $model = $this->bpm->get_model($task['idwf'], array('data.properties'));
                $task['title'] = $model->data['properties']['name'];
                $task['label'] = (isset($task['checkdate'])) ? $this->time_elapsed_string($task['checkdate']) : '';
                $task['label-class'] = 'label-warning';
                $task['body'] = date($this->lang->line('dateTimeFmt'), strtotime($task['checkdate']));
                $task['body'].='<br/>' . strtoupper($task['status']);
                $task['showBody'] = true;
                $data['mytasks'][] = $task;
                //var_dump($task);exit;
            }
            //---prepare pages
            $data['showPager'] = ($pages > 1) ? true : false;
            for ($i = 1; $i <= $pages; $i++) {
                $data['pages'][] = array(
                    'title' => $i,
                    'url' => $this->base_url . 'bpm/bpmui/' . $caller . '/' . $i . '/' . $pagesize,
                    'class' => ($i == $chunk) ? 'bg-blue' : '',
                );
            }
            $data['number'] = count($tasks);
        }
        return $data;
    }

    private function prepare_tasks($tasks, $chunk, $pagesize) {
        $data = array();
        $data['module_url'] = $this->module_url;
        $data['base_url'] = $this->base_url;
        $data['showPager']=false;
        $data['isAdmin'] = $this->user->isAdmin();
        $trace = debug_backtrace();
        $caller = $trace[1]['function'];

        $total = count($tasks);
        $data['qtty'] = $total;
        $parts = array_chunk($tasks, $pagesize, true);
        $pages = count($parts);
        if ($pages) {
            $tasks = $parts[$chunk - 1];
            foreach ($tasks as $task) {
                $model = $this->bpm->get_model($task['idwf'], array('data.properties'));
                $title = $model->data['properties']['name'] . ' :: ' . $task['title'];
                $task['label'] = (isset($task['checkdate'])) ? $this->time_elapsed_string($task['checkdate']) : '';
                $task['label-class'] = 'label-warning';
                $task['icon'] = $this->bpm->get_icon($task['type']);
                $data['mytasks'][] = $task;
            }
            //---prepare pages
            $data['showPager'] = ($pages > 1) ? true : false;
            for ($i = 1; $i <= $pages; $i++) {
                $data['pages'][] = array(
                    'title' => $i,
                    'url' => $this->base_url . 'bpm/bpmui/' . $caller . '/' . $i . '/' . $pagesize,
                    'class' => ($i == $chunk) ? 'bg-blue' : '',
                );
            }
            $data['number'] = count($tasks);
        }
        return $data;
    }

}

/* End of file welcome.php */
/* Location: ./system/application/controllers/welcome.php */