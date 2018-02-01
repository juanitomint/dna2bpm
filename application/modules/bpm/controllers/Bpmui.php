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
        $this->user->isloggedin();
        $this->load->config('config');
        $this->load->model('bpm/bpm');
        $this->load->library('parser');
        $this->base_url = base_url();
        $this->module_url = base_url() . $this->router->fetch_module() . '/';
        $this->idu = $this->user->idu;
        $this->activeUser = $this->user->get_user($this->idu);
        //----LOAD LANGUAGE
        $this->lang->load('library', $this->config->item('language'));
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

    function widget_ministatus($addIgnore = false) {
        $this->lang->load('bpm/bpm');
        $mymodels = $this->bpm->get_models();
        
        foreach($mymodels as $model)$models[]=$model;
        //----convert to arrays
        
        $models =($models)? array_map(function($model) {
            // $model = (array) $model;

            $model['properties'] = $model['data']['properties'];
            return (array) $model;
        }, $models):array();
        //----get folders
        $folders =($models) ? array_unique(array_map(function($model) {
                    if (isset($model['folder']))
                        return $model['folder'];
                }, $models)):array();
        sort($folders);
        //-----make 2 level tree
        foreach ($folders as $folder) {
            $data['folders'][] = array(
                'folder' => $folder,
                'models' => array_filter($models, function($model) use($folder) {
                            if (isset($model['folder']))
                                return $model['folder'] == $folder;
                        })
            );
        }
        $data['base_url'] = $this->base_url;
        $data['qtty'] = count($models);
        $data['widget_url'] = base_url() . $this->router->fetch_module() . '/' . $this->router->class . '/' . __FUNCTION__;
//        $models_flat = array_map(function ($model) {
//            $m['idwf'] = $model->idwf;
//            $m['properties'] = $model->data['properties'];
//            return $m;
//        }, $models);
//        $data['models'] = $models_flat;
        //----prepare script
        if ($addIgnore) {
            $add = "";
        } else {
            $add = "
<script>
    if (window.$) {
    $('.treeview').tree();
    }
</script>
    ";
        }
        echo $this->parser->parse('bpm/widgets/ministatus_widget', $data, true, true) . $add;
    }

    function ministatus($idwf, $showArr = array()) {

        $showArr = (count($showArr)) ? (array)$showArr : array(
            'StartNoneEvent',
            'StartMessageEvent',
            'Task',
            'Exclusive_Databased_Gateway',
            'EndMessageEvent',
        );
        $this->user->authorize();
        $this->lang->load('bpm/bpm');
        $data['widget_url'] = base_url() . implode('/', array_filter(array($this->router->fetch_module(), $this->router->class, __FUNCTION__, $idwf)));
        $filter=array('idwf'=>$idwf,'type'=>array('$in'=>$showArr));
        $state = $this->bpm->get_cases_stats($filter);
        $data['lang'] = $this->lang->language;
        //---las aplano un poco
;
        
        foreach ($state as $task) {
            $task['user'] = (isset($task['status']['user'])) ? $task['status']['user'] : 0;
            $task['finished'] = (isset($task['status']['finished'])) ? $task['status']['finished'] : 0;
            $task['icon'] = $this->bpm->get_icon($task['type']);
            $data['mini'][] = $task;
        }
        $data['base_url'] = base_url();
        $data['idwf'] = $idwf;
        
        $wf = $this->bpm->load($idwf);
        $data+=$wf['data']['properties'];
        $data['name'] = 'Mini Status: ' . $data['name'];
        echo $this->parser->parse('bpm/widgets/ministatus', $data, true, true);
    }

    function widget_data($idwf, $idcase) {
        $case = $this->bpm->get_case($idcase,$idwf);
        ob_start();
        var_dump($this->bpm->load_case_data($case));
        $content = ob_get_contents();
        ob_end_clean();
        $data['content'] = $content;
        $data['title'] = $idcase . ' Data';
        $data['widget_url'] = base_url() . implode('/', array_filter(array($this->router->fetch_module(), $this->router->class, __FUNCTION__, $idwf)));
        echo $this->parser->parse('dashboard/widgets/box_warning_solid', $data, true, true);
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

    function tile_tasks2me() {
        $data['lang'] = $this->lang->language;
        $data['id'] = __FUNCTION__;
        $data['title'] = $this->lang->line('MyTasks');
//        $query = array(
//            'assign' => $this->idu,
//            'status' => 'user',
//        );
        $query = array(
            'assign' => $this->idu,
            'status' => 'user'
        );

        $tasks = $this->bpm->get_tasks_byFilter($query);
        $data['number'] = count($tasks);
        $data['icon'] = 'ion-android-checkmark';
        $data['more_info_link'] = $this->base_url . 'dashboard/show/tasks';
        $data['widget_url'] = base_url() . $this->router->fetch_module() . '/' . $this->router->class . '/' . __FUNCTION__;
        return $this->parser->parse('dashboard/tiles/tile-yellow', $data, true, true);
    }

    function tile_tasks() {
        $data['lang'] = $this->lang->language;
        $data['id'] = __FUNCTION__;
        $data['title'] = $this->lang->line('MyTasks');
//        $query = array(
//            'assign' => $this->idu,
//            'status' => 'user',
//        );
        $query = array(
            '$or' => array(
                array('assign' => $this->idu),
                array('idgroup' => array('$in' => $this->activeUser->group), 'assign' => array('$exists' => false))
            ),
            'status' => 'user'
        );
        //@todo get just task count instead tasks
        $tasks = $this->bpm->get_tasks_byFilter($query);
        $data['number'] = count($tasks);
        $data['icon'] = 'ion-android-checkmark';
        $data['more_info_link'] = $this->base_url . 'dashboard/show/tasks';
        $data['widget_url'] = base_url() . $this->router->fetch_module() . '/' . $this->router->class . '/' . __FUNCTION__;
        return $this->parser->parse('dashboard/tiles/tile-yellow', $data, true, true);
    }

    function tile_tasks_done() {
        $data['lang'] = $this->lang->language;
        $data['id'] = __FUNCTION__;
        $data['title'] = $this->lang->line('TasksDone');
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
        $data['widget_url'] = base_url() . $this->router->fetch_module() . '/' . $this->router->class . '/' . __FUNCTION__;
        return $this->parser->parse('dashboard/tiles/tile-green', $data, true, true);
    }

    function tile_cases($idwf = null) {
        $data['lang'] = $this->lang->language;
        $data['id'] = __FUNCTION__;
        $data['title'] = $this->lang->line('Cases');
        $cases = $this->bpm->get_cases_byFilter(
                array(
                    'iduser' => $this->idu,
                    'status' => 'open',
                )
        );
        $data['number'] = count($cases);
        $data['icon'] = 'ion-play';
        $data['more_info_link'] = $this->base_url . 'dashboard/show/tasks';
        $data['widget_url'] = base_url() . implode('/', array_filter(array($this->router->fetch_module(), $this->router->class, __FUNCTION__, $idwf)));
        return $this->parser->parse('dashboard/tiles/tile-blue', $data, true, true);
    }

    function tile_cases_closed($idwf = null) {
        $data['lang'] = $this->lang->language;
        $data['id'] = __FUNCTION__;
        $data['title'] = $this->lang->line('CasesClosed');
        ;
        $cases = $this->bpm->get_cases_byFilter(
                array(
                    'iduser' => $this->idu,
                    'status' => 'closed',
                )
        );
        $data['number'] = count($cases);
        $data['icon'] = 'ion-play';
        $data['more_info_link'] = $this->base_url . 'dashboard/show/tasks';
        $data['widget_url'] = base_url() . implode('/', array_filter(array($this->router->fetch_module(), $this->router->class, __FUNCTION__, $idwf)));
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
        $data['lang'] = $this->lang->language;
        $data['title'] = $this->lang->line('Tasks') . ' ' . $this->lang->line('Finished');

        $data['more_info_link'] = $this->base_url . 'bpm/';
        $data['widget_url'] = base_url() . $this->router->fetch_module() . '/' . $this->router->class . '/' . __FUNCTION__;
        echo $this->parser->parse('bpm/widgets/tasks_done', $data, true, true);
    }


    function widget_2doMe($chunk = 1, $pagesize = 5, $filter = null) {
        //$data['lang']=$this->lang->language; ==}
        if ($filter){
        $query = array(
            'assign' => $this->idu,
            'status' => 'user',
            'idwf' => $filter
        );
        }else{
        $query = array(
            'assign' => $this->idu,
            'status' => 'user'
        );
        }
        //var_dump(json_encode($query));exit;
        $tasks = $this->bpm->get_tasks_byFilter($query, array(), array('checkdate' => 'desc'));
        $data = $this->prepare_tasks($tasks, $chunk, $pagesize);
        //$data['lang'] = $this->lang->language;
        $data['title'] = $this->lang->line('Tasks') . ' ' . $this->lang->line('Pending');

        $data['more_info_link'] = $this->base_url . 'bpm/';
        $data['widget_url'] = base_url() . $this->router->fetch_module() . '/' . $this->router->class . '/' . __FUNCTION__;
        echo $this->parser->parse('bpm/widgets/2do', $data, true, true);
    }

    function widget_2doMeCards($chunk = 1, $pagesize = 8) {
        //$data['lang']=$this->lang->language; ==
        $query = array(
            'assign' => $this->idu,
            'status' => 'user'
        );
        //var_dump(json_encode($query));exit;
        $tasks = $this->bpm->get_tasks_byFilter($query, array(), array('checkdate' => 'desc'));
        $data = $this->prepare_tasks($tasks, $chunk, $pagesize);
        //$data['lang'] = $this->lang->language;
        $data['title'] = $this->lang->line('Tasks') . ' ' . $this->lang->line('Pending');

        $data['more_info_link'] = $this->base_url . 'bpm/';
        $data['widget_url'] = base_url() . $this->router->fetch_module() . '/' . $this->router->class . '/' . __FUNCTION__;
        echo $this->parser->parse('bpm/widgets/2do_cards', $data, true, true);
    }

    function widget_2do($chunk = 1, $pagesize = 5) {
        //$data['lang']=$this->lang->language;
        $query = array(
            '$or' => array(
                array('assign' => $this->idu),
                array('idgroup' => array('$in' => $this->activeUser->group), 'assign' => array('$exists' => false))
            ),
            'status' => 'user'
        );
//        $query=array(
//        		'assign' => $this->idu,
//            	'status' => 'user'
//
//        );
        //var_dump(json_encode($query));exit;
        $tasks = $this->bpm->get_tasks_byFilter($query, array(), array('checkdate' => 'desc'));
        $data = $this->prepare_tasks($tasks, $chunk, $pagesize);
        //$data['lang'] = $this->lang->language;
        $data['title'] = $this->lang->line('Tasks') . ' ' . $this->lang->line('Pending');

        $data['more_info_link'] = $this->base_url . 'bpm/';
        $data['widget_url'] = base_url() . $this->router->fetch_module() . '/' . $this->router->class . '/' . __FUNCTION__;
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
        //$data['lang'] = $this->lang->language;
        $data['title'] = $this->lang->line('closedCases');

        $data['more_info_link'] = $this->base_url . 'bpm/';
        $data['widget_url'] = base_url() . $this->router->fetch_module() . '/' . $this->router->class . '/' . __FUNCTION__;
        echo $this->parser->parse('bpm/widgets/cases_closed', $data, true, true);
    }

    function widget_cases($chunk = 1, $pagesize = 5, $filter = null) {
        if ($filter){
            $cases = $this->bpm->get_cases_byFilter(
                array(
            'iduser' => $this->idu,
            'idwf' => $filter,
            'status' => 'open',
                ), array(), array('checkdate' => 'desc')
        );
            
            
        }else{
        $cases = $this->bpm->get_cases_byFilter(
                array(
            'iduser' => $this->idu,
            'status' => 'open',
                ), array(), array('checkdate' => 'desc')
        );
        }
        $data = $this->prepare_cases($cases, $chunk, $pagesize);
        //$data['lang'] = $this->lang->language;
        $data['title'] = $this->lang->line('openCases');

        $data['more_info_link'] = $this->base_url . 'bpm/';
        $data['widget_url'] = base_url() . $this->router->fetch_module() . '/' . $this->router->class . '/' . __FUNCTION__;
        echo $this->parser->parse('bpm/widgets/cases_open', $data, true, true);
    }

    private function prepare_cases($cases, $chunk, $pagesize) {
        $data = array();
        $data['module_url'] = $this->module_url;
        $data['base_url'] = $this->base_url;
        $data['showPager'] = false;
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
                if (isset($task['idwf'])) {
                    $model = $this->bpm->get_model($task['idwf'], array('data.properties'));
                    if ($model) {
                        $task['title'] = $model->data['properties']['name'];
                    } else {
                        $task['title'] = $task['idwf'] . ':: Missing model';
                    }
                    $task['label'] = (isset($task['checkdate'])) ? $this->time_elapsed_string($task['checkdate']) : '';
                    $task['label-class'] = 'label-warning';
                    $task['body'] = date($this->lang->line('dateTimeFmt'), strtotime($task['checkdate']));
                    $task['body'].='<br/>' . strtoupper($task['status']);
                    $task['showBody'] = true;
                    $data['mytasks'][] = $task;
                    //var_dump($task);exit;
                }
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

    public function prepare_tasks($tasks, $chunk, $pagesize) {
        $data = array();
        $data['module_url'] = $this->module_url;
        $data['base_url'] = $this->base_url;
        $data['showPager'] = false;
        $data['isAdmin'] = $this->user->isAdmin();
        $trace = debug_backtrace();
        $caller = $trace[1]['function'];

        $total = count($tasks);
        $data['qtty'] = $total;
        $parts = array_chunk($tasks, $pagesize, true);
        $pages = count($parts);
        $data['mytasks']=array();//--prevent parser problems
        if ($pages) {
            $tasks = $parts[$chunk - 1];
            foreach ($tasks as $task) {
                $model = $this->bpm->get_model($task['idwf'], array('data.properties'));
                if ($model) {
                    $task['model']=$model->data['properties']['name'];
                    $task['name']=$task['title'];
                    $title = $model->data['properties']['name'] . ' :: ' . $task['title'];
                } else {
                    $title = '???' . ' :: ' . $task['title']; //---missing model
                }
                $task['title'] = $title;
                $task['label'] = (isset($task['checkdate'])) ? $this->time_elapsed_string($task['checkdate']) : '';
                //----calculate task color
                $task['class'] = 'success';
                $now = new DateTime;
                $ago = new DateTime($task['checkdate']);
                $diff = $now->diff($ago);
                //---ok=success
                if($diff->days>=$this->config->item('task_ok')){
                    $task['class']='success';
                }
                //----warning
                if($diff->days>=$this->config->item('task_warn')){
                    $task['class']='warning';
                }
                //----danger
                if($diff->days>=$this->config->item('task_danger')){
                    $task['class']='danger';
                }



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