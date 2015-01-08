<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

    /**
     * This class is for manipulate all related to bpm objects: models, cases and tokens
     * @author Juan Ignacio Borda <juanignacioborda@gmail.com>
     * @date Feb 10, 2013
     *
     */
class Bpm extends CI_Model {

    /**
     * Container for storing models
     * @var bpm_container
     */
    public $bpm_container = 'workflow';
    public $debug = array();
    public $digInto = array('Pool', 'Subprocess', 'CollapsedSubprocess', 'Lane');

    function __construct() {
        parent::__construct();
        $this->idu = (int) $this->session->userdata('iduser');
        $this->load->library('cimongo/cimongo');
        $this->db = $this->cimongo;
        $this->load->config('bpm/config');
    }

    function load($idwf, $replace = false) {

        $query = array('idwf' => $idwf);
//        var_dump2($query);
        $result = $this->db->get_where($this->bpm_container, $query)->result_array();
        if ($result) {
            $wf = $result[0];
        } else {
            return false;
            //show_error("Model: '$idwf' not found<br>Contact your system Administrator");
        }

        if ($wf) {
            //var_dump2($wf);
            if ($replace)
                $wf = array_map(array($this->bpm, 'replace_subproc'), (array) $wf);
            //echo '<hr/>';
            //var_dump2($wf);
        } else {//----return deleted msg
        }
        return $wf;
    }

    function load_case_data($case, $idwf = null) {
        $data = array();
        $debug = (isset($this->debug[__FUNCTION__])) ? $this->debug[__FUNCTION__] : false;
//$debug = true;
        if ($debug)
            echo '<h2>' . __FUNCTION__ . '</h2>' .
            "Called @ " . xdebug_call_file() . "<br/>Line:" . xdebug_call_line() . "<br/>from: <b>" . xdebug_call_function() . '</b><hr/>';

////////////////////////////////////////////////////////////////////////
//---load mongo_connector by default
        $this->load->model('bpm/connectors/mongo_connector');
        if (isset($case['data'])) {
            foreach ($case['data'] as $key => $value) {
                if (is_array($value)) {
                    if (isset($value['connector'])) {
                        $conn = $value['connector'] . '_connector';
                        if ($debug)
                            echo "Calling Connector: $conn<br/>";
                        $data[$key] = $this->$conn->get_data($value);
                    } else {
                        $data[$key] = $value;
                    }
                } else { //add regular data
                    $data[$key] = $value;
                }
            }
        }
        if ($debug)
            var_dump('$data', $data);
        return $data;
    }

    function replace_subproc($item) {

        //var_dump2($item);
        if (is_array($item)) {
            if (isset($item['stencil']['id'])) {
                //var_dump2($item['stencil']['id']);
                if ($item['stencil']['id'] == 'CollapsedSubprocess') {

                    switch ($item['properties']['subprocesstype']) {
                        case "Independent":
                            break;

                        case "Reference":
                            break;

                        case "Embedded":
                            break;
                    }
                    //----4 now we do the same for all: load the model into the shape
                    if ($item['properties']['entry']) {
                        $wf = $this->bpm->load($item['properties']['entry'], true);
                        //var_dump2('linked',$wf['data']['childShapes']);
                        $item['childShapes'] = $wf['data']['childShapes'];
                    }
                }
            }//---isset
            if (isset($item['childShapes'])) {
                $item['childShapes'] = array_map(array($this->bpm, 'replace_subproc'), (array) $item['childShapes']);
            }
        } //---is array
        return $item;
    }

    function get_properties($idwf) {
        $query = array('idwf' => $idwf);
//        var_dump2($query);
        $wf = $this->load($idwf);

        return $wf['data']['properties'];
    }

    function svg($idwf) {
        $query = array('idwf' => $idwf);
        $fields = array('svg' => true);
        $wf = $this->load($idwf);
        return $wf['svg'];
    }

    function save($idwf, $data, $svg) {
        $query = array('idwf' => $idwf);
        $mywf = $this->load($idwf);
        //*
        //@todo make a backup before overwrite
        //---update modification date
        unset($mywf['_id']);
        $wf_back = $mywf;
        //----if set make a zip backup of actual model
        if ($this->config->item('make_model_backup') && is_file("images/zip/$idwf.zip")) {
            copy("images/zip/$idwf.zip", "images/zip/$idwf-BACKUP-" . date('Y-m-d-H-i-s') . ".zip");
        }
        $data->properties->modificationdate = date('Y-m-d') . 'T00:00:00';
        $mywf['idwf'] = $idwf;
        $mywf['data'] = (isset($data)) ? $data : $mywf['data'];
        $mywf['svg'] = (isset($svg)) ? $svg : $mywf['svg'];
        $mywf['version'] = (isset($mywf['version'])) ? $mywf['version'] ++ : 1;
        array_filter($mywf);
        //--only save if 
        //var_dump2($mywf);
        $wf = $this->db->where($query)->update('workflow', $mywf, array('upsert' => true));
        $this->save_image_file($idwf, $svg);
        $this->save_model_file($idwf, $data);
        $this->zip_model($idwf, $data);
        return json_encode($wf);
    }

    function model_exists($idwf) {
        $query = array('idwf' => $idwf);
        $result = $this->db->get_where($this->bpm_container, $query)->result_array();
        if (count($result)) {
            return $this->load($idwf);
        } else {
            return false;
        }
    }

    function save_raw($mywf) {
        $options = array('w' => true, 'upsert' => true);
        $wf = $this->mongo->db->workflow->save($mywf, $options);
    }

    function update_folder($idwf, $folder) {
        $query = array('idwf' => $idwf);
        $action = array('$set' => array('folder' => $folder));
        $options = array('upsert' => false, 'w' => true);
        $rs = $this->mongo->db->workflow->update($query, $action, $options);
        return $rs;
    }

    function get_started_cases($iduser) {
        $query = array('iduser' => $iduser);
        $result = $this->mongo->db->find($query);
        //var_dump2($query,json_encode($query),$result->count());
        return $result;
    }

    /*
     * Get Cases of current user
     * 
     */

    function get_cases_byId($id) {
        return $this->get_cases_byFilter(array('id' => $id));
    }

    function get_cases_byFilter($filter, $fields = array(), $sort = array()) {
        //$this->db->debug=true;
        $this->db->where($filter);
        $this->db->select($fields);
        $this->db->order_by($sort);
        $rs = $this->db->get('case');
        return $rs->result_array();
    }

    function get_cases_stats($filter) {

        $all_tokens = array();
        $allcases = $this->get_cases_byFilter($filter, array('id', 'idwf', 'token_status'));
        foreach ($allcases as $case) {
            $tokens = (isset($case['token_status'])) ? $case['token_status'] : null;
            //var_dump($case,$tokens);exit;
            if ($tokens) {
                foreach ($tokens as $resourceId => $state) {
                    if (isset($all_tokens[$resourceId])) {
                        $all_tokens[$resourceId]['run'] ++;
                        $all_tokens[$resourceId]['status'][$state] = (!isset($all_tokens[$resourceId]['status'][$state])) ? 0 : $all_tokens[$resourceId]['status'][$state];
                        $all_tokens[$resourceId]['status'][$state] ++;
                    } else {

                        $token = $this->bpm->get_token($case['idwf'], $case['id'], $resourceId);
                        //var_dump($token);exit;
                        //$data = $this->bpm->get_shape($resourceId, $wf);

                        $all_tokens[$resourceId] = array(
                            'idwf' => $case['idwf'],
                            'resourceId' => $resourceId,
                            'title' => (isset($token['title'])) ? $token['title'] : '',
                            'type' => $token['type'],
                            'run' => 1,
                            'status' => array($state => 1),
                            'icon' => $this->get_icon($token['type'])
                        );
                    }
                }
            }
        }//---end foreach cases
        return $all_tokens;
    }

    function get_cases($user = null, $offset = 0, $limit = null, $filter_status = array()) {
        $data = array(
            'cases' => array(),
            'totalCases' => 0
        );

        //---allow asking 4 other users only if admin
        if (($this->user->has("root/ADM") OR $this->user->has("root/ADMWF"))) {

            $iduser = (isset($user)) ? (int) $user : $this->idu;
        } else {
            $iduser = $this->idu;
        }
        $tasks = $this->bpm->get_tasks($iduser);
        $tarr = array();
        foreach ($tasks as $task) {
            //---count tasks in this case.
            @$tarr[$task['case']]+= 1;
        }
        $data['totalCases'] = count($tarr);
        if (count($tarr)) {
            foreach ($tarr as $idcase => $qtty) {
                //@todo set idwf
                $case = $this->bpm->get_case($idcase);
                if ($case) {
                    $mybpm = $this->bpm->load($case['idwf'], true);
                    //unset($case['_id']);
                    $case['name'] = $mybpm['data']['properties']['name'];
                    $case['documentation'] = $mybpm['data']['properties']['documentation'];
                    //$case['mytasks'] = $tasks;
                    $case['task_count'] = $qtty;
                    $case['date'] = date($this->lang->line('dateFmt'), strtotime($case['checkdate']));
                    if (count($filter_status)) {
                        if (in_array($case['status'], $filter_status)) {
                            $data['cases'][] = $case;
                            $sort_date[] = $case['checkdate'];
                        }
                    } else {
                        $data['cases'][] = $case;
                        $sort_date[] = $case['checkdate'];
                    }

                    //----order Cases
                }
            }

            //---sort by ddate desc
            if (isset($data['cases']) and isset($sort_date))
                array_multisort($sort_date, SORT_DESC, $data['cases']);

            $data['cases'] = array_slice($data['cases'], $offset, $limit);
        }
        return $data;
    }

    function save_model_file($idwf, $data) {
        $this->load->helper('file');
        $path = 'images/model/';
        $filename = $path . $idwf . '.json';
        write_file($filename, json_encode($data));
    }

    function save_image_file($idwf, $svg) {
        $debug = false;
        //---save actual SVG file
        $header = '<svg xmlns="http://www.w3.org/2000/svg" xmlns:oryx="http://oryx-editor.org" id="oryx_0A451D6E-C8F6-4F08-B845-64C7F2FC08AC" xmlns:xlink="http://www.w3.org/1999/xlink" xmlns:svg="http://www.w3.org/2000/svg">';
        $svg = str_replace('<svg >', $header, $svg);
        $svg = str_replace('blank"href', 'blank" href', $svg);
        $this->load->helper('file');
        $phantom_path = APPPATH . 'modules/bpm/assets/jscript/phantomjs-1.9.7-linux-x86_64';
        $resize = '-resize 30%';
        $crop = '-crop 720x720+0+0';
        $path = 'images/svg/';
        $path_thumb = 'images/png/';
        $filename = $path . $idwf . '.svg';
        $filename_thumb = $path_thumb . $idwf . '.png';
        $filename_crop = $path_thumb . $idwf . '-cropped.png';
        $filename_thumb_small = $path_thumb . $idwf . '-small.png';

        $result = write_file($filename, $svg);

        $rtn = '';
        if ($this->config->item('make_thumbnails')) {
            $command = "$phantom_path/bin/phantomjs $phantom_path/rasterize.js $filename $filename_thumb";
            exec($command, $cmd, $rtn);
            if ($debug) {
                echo "$command\n rt:$rtn\n";
            }
            $command = "$phantom_path/bin/phantomjs $phantom_path/crop.js $filename $filename_crop";
            exec($command, $cmd, $rtn);

            if ($debug) {
                echo "$command\n rt:$rtn\n";
            }
            $command = "$phantom_path/bin/phantomjs $phantom_path/zoom.js $filename_crop $filename_thumb_small .5";
            exec($command, $cmd, $rtn);
            if ($debug) {
                echo getcwd() . "\n";
                echo "$command\n rt:$rtn\n";
            }
        }
        return $result;
    }

    function zip_model($idwf, $data) {
        $zip = new ZipArchive();
        $filename = $idwf . ".zip";
        $path = 'images/zip/';
        $svg = 'images/svg/' . $idwf . '.svg';
        $filename_thumb_small = 'images/png/' . $idwf . '-small.png';

        if ($zip->open($path . $filename, ZIPARCHIVE::CREATE) !== TRUE) {
            exit("cannot open <$filename>\n");
        }
        //---add the model file
        $zip->addFromString('images/model/' . $idwf . ".json", json_encode($data));
        //---add SVG diagram
        $zip->addFile($svg);
        //---Add thumbnail
        if (is_file($filename_thumb_small))
            $zip->addFile($filename_thumb_small);
        $zip->close();
    }

    function delete($idwf) {
        $options = array('w' => true, 'justOne' => true);
        $criteria = array('idwf' => $idwf);
        //var_dump2($options,$criteria);
        $result = $this->mongo->db->workflow->remove($criteria, $options);
        if ($result['ok'] == 1) { //is OK
            return true;
        } else {
            return false;
        }
    }

    function set_token($idwf, $case, $resourceId, $type, $status = 'pending', $data = array()) {
        //---check 4 incomplete data
        //---set execution user
        if (!isset($data['iduser']))
            $data['iduser'] = (int) $this->session->userdata('iduser');

        if (!isset($idwf) or !isset($case) or !isset($resourceId)) {
            show_error("Can't update whith: idwf:$idwf case:$case  resourceId:$resourceId<br/>Incomplete Data.");
        }
        //$title=(isset($shape->properties->title))?$shape->properties->title;$shape->stencil->id;
        //echo "<br>setting: $idwf:$case $resourceId to $status<hr/>";
        //----sanitize $data----------------------------------------------------
        $data['status'] = $status;
        //----------------------------------------------------------------------
        $data+=array(
            //'checkdate' => date('Y-m-d H:i:s'),
            'idwf' => $idwf,
            'case' => $case,
            'resourceId' => $resourceId,
            'type' => $type,
            'status' => $status,
        );

        $criteria = array('idwf' => $idwf, 'case' => $case, 'resourceId' => $resourceId);
        //var_dump2($query, $criteria, $options);

        if ($this->get_token($idwf, $case, $resourceId)) {
            $this->db->where($criteria)->update('tokens', $data);
        } else {
            $this->db->insert('tokens', $data);
        }
        //---TODO set token on cache
    }

    function clear_tokens($idwf, $case, $criteria = null) {
        $criteria = ($criteria) ? $criteria : array(
            'idwf' => $idwf,
            'case' => $case
        );
        return $this->db->where($criteria)->delete('tokens');
    }

    function clear_case($idwf, $idcase) {
        $case = $this->get_case($idcase);
        $_id = $case['_id'];
        return $this->save_case(
                        array(
                            '_id' => $case['_id'],
                            'id' => $case['id'],
                            'idwf' => $case['idwf'],
                            //---resets the date
                            'checkdate' => date('Y-m-d H:i:s'),
                            //---reset history 
                            'history' => array(),
                            'run_manual' => (isset($case['run_manual'])) ? $case['run_manual'] : false,
                            'data' => (isset($case['data'])) ? $case['data'] : array()
                        )
        );
    }

    function delete_case($idwf, $case) {
        $this->clear_tokens($idwf, $case);
        $criteria = array(
            'idwf' => $idwf,
            'id' => $case
        );
        return $this->mongo->db->case->remove($criteria);
    }

    function get_assigned($idwf, $case, $resourceId) {
        $token = $this->get_token($idwf, $case, $resourceId);
        $return = (isset($token['assign'])) ? $token['assign'] : null;
        return $return;
    }

    function get_token($idwf, $idcase, $resourceId) {
        //---TODO get token from cache
        $query = array(
            'idwf' => $idwf,
            'case' => $idcase,
            'resourceId' => $resourceId
        );
        return $this->mongo->db->tokens->findOne($query);
    }

    /*
     * This function loads data from case and pastes it on a token 
     * for custom uses use it carrefou since data results can be big
     */

    function consolidate_data($idwf, $idcase, $resourceId) {
        $case = $this->get_case($idcase);
        $data = $this->load_case_data($case, $idwf);
        $token = $this->get_token($idwf, $idcase, $resourceId);
        if (!$token) {
            $mywf = $this->load($idwf);
            $wf = $this->bindArrayToObject($mywf ['data']);
            //---tomo el template de la tarea
            $wf->idwf = $idwf;
            $wf->case = $idcase;
            $shape = $this->bpm->get_shape($resourceId, $wf);
            $token = $this->token_checkin(array('status' => 'finished'), $wf, $shape);
        }
        $token['data'] = (isset($token['data'])) ? $token['data'] : array();
        foreach ($data as $entity => $values) {
            unset($values['_id']);
            unset($values['id']);
            unset($values['owner']);
            unset($values['parent']);
            try {
                $token['data'] = (array) $values + $token['data'];
            } catch (Exception $e) {
                echo $e->getMessage();
            }
        }
//            var_dump($token);
//            echo json_encode($token);
//            exit;
        $this->save_token($token);
        return $token;
    }

    function get_tokens($idwf, $idcase, $status = 'pending', $type = null) {
        $query = array_filter(
                array(
                    'idwf' => $idwf,
                    'case' => $idcase,
                    'status' => $status,
                )
        );
        if ($type) {
            $query['type'] = $type;
        }

        //var_dump2(json_encode($query));
        $result = $this->db
                ->where($query)
                ->order_by(array('_id' => true))
                ->get('tokens')
                ->result_array();
        return $result;
    }

    function get_tokens_byFilter($filter, $fields = array(), $sort = array()) {
        //$this->db->debug=true;
        $this->db->where($filter);
        $this->db->select($fields);
        $this->db->order_by($sort);
        $rs = $this->db->get('tokens');
        return $rs->result_array();
    }

    function get_last_token($idwf, $idcase) {
        $query = array_filter(
                array(
                    'idwf' => $idwf,
                    'case' => $idcase,
                )
        );

        //var_dump2(json_encode($query));
        $result = $this->db
                ->where($query)
                ->order_by(array('_id' => -1))
                ->get('tokens', 1)
                ->result_array();
        if (count($result)) {
            return end($result);
        }
        return false;
    }

    function get_token_status($idwf, $idcase) {
        //----filter all statuses
        $query = array_filter(
                array(
                    'idwf' => $idwf,
                    'case' => $idcase,
                //'status' => array('$nin' => array('finished', 'canceled'))
                )
        );
//        $this->db->debug=true;
        $tokens = $this->db
                ->select(array('resourceId', 'status'))
                ->where($query)
                ->get('tokens')
                ->result_array();
//        $this->db->debug=false;
        //     var_dump2($idwf,$idcase,json_encode($query),$tokens);
        if (count($tokens)) {
            $reduced = array_reduce(
                    $tokens, function (&$result, $token) {
                $result[$token['resourceId']] = (isset($token['status'])) ? $token['status'] : '???';
                return $result;
            }, array()
            );
            return $reduced;
        }
        return false;
    }

    function assign_task($token, $users) {
        //---this function is for manually assign a certain task
        //--- can be invoked from script tasks.
        //---check if property exists
        if (!isset($token['assign']))
            $token['assign'] = array();
        //---merge user arrays
        $token['assign']+=$users;
        //---ensure uniqness
        array_unique($token['assign']);
        $this->save_token($token);
    }

    function get_token_byid($id) {
        $query = array(
            '_id' => $id,
        );
        //var_dump2(json_encode($query));
        return $this->mongo->db->tokens->findOne($query);
    }

    function get_tokens_byResourceId($resourceId, $filter = array(), $sort = array()) {
        $query = array(
            'resourceId' => $resourceId,
                ) + $filter;
//        var_dump(json_encode($query));
//        exit;
        $result = $this->mongo->db->tokens->find($query);
        $rs = array();
        foreach ($result as $record) {
            //----don't add missing tokens
            if ($this->get_case($record['case'], $record['idwf']))
                $rs[] = $record;
        }

        return $rs;
    }

    function get_pending($idwf, $case, $status = 'user', $filter) {
        $query = array(
            'idwf' => $idwf,
            'case' => $case,
            'status' => array('$in' => (array) $status),
        );
        $query+=$filter;
        toRegex($query);
        //var_dump2(json_encode($query));
        return $this->mongo->db->tokens->find($query)->sort(array('_id' => true));
    }

    function get_triggers() {
        //---defines wich types will be returned
        $type = array(
            'IntermediateTimerEvent',
            'StartTimerEvent',
        );
        $query = array(
            'status' => 'waiting',
            'type' => array('$in' => $type),
        );
        //var_dump2(json_encode($query));
        return $this->mongo->db->tokens->find($query);
    }

    function get_signal_thrower($name) {
        $query = array(
            'status' => 'finished',
            'type' => 'IntermediateSignalEventThrowing',
            'name' => $name,
        );
        //var_dump2(json_encode($query));
        return $this->mongo->db->tokens->findOne($query);
    }

    function get_signal_catchers($name) {
        $query = array(
            'status' => 'waiting',
            'type' => 'IntermediateSignalEventCatching',
            'name' => $name,
        );
        //var_dump2(json_encode($query));
        return $this->db->where($query)->get('tokens')->result_array();
    }

    function get_models($filter = null, $fields = null) {
        //----return ids by default
        $query = array();
        $sort = array('idwf' => 1);
        $query = array() + (array) $filter;
        $fields_default = array('idwf', 'folder', 'data.properties.name', 'data.properties.documentation');
        $fields = ($fields) ? array_merge($fields_default, (array) $fields) : $fields_default;
        $this->toRegex($query);
        //var_dump(json_encode($query), $sort, $fields);
        //$exit;
        $rs = $this->db
                ->select($fields)
                ->where($query)
                ->order_by($sort)
                ->get('workflow')
                ->result();

        return $rs;
    }

    function get_model($idwf, $fields = null) {
        //----return ids by default
        $query = array('idwf' => $idwf);
        $sort = array('idwf' => 1);
        $fields_default = array('idwf', 'folder', 'data.properties.name', 'data.properties.documentation');
        $fields = ($fields) ? array_merge($fields_default, (array) $fields) : $fields_default;
        //var_dump(json_encode($query), $sort, $fields);
        //$exit;
        $rs = $this->db
                ->select($fields)
                ->where($query)
                ->order_by($sort)
                ->limit(1)
                ->get('workflow')
                ->result();

        if (count($rs)) {
            return $rs[0];
        }
        return false;
    }

    function get_case($idcase, $idwf = null) {
        //---TODO get token from cache
        $query = array(
            'id' => $idcase,
        );
        //----if isset add to filter (faster)
        if ($idwf)
            $query['idwf'] = $idwf;

        //var_dump(json_encode($query));
        //var_dump(xdebug_get_function_stack());
        $result = $this->db->get_where('case', $query)->result_array();
        if ($result) {
            return $result[0];
        } else {
            return false;
            //show_error("Case: '$idcase' not found<br>Contact your system Administrator");
        }
    }

    function get_all_cases_count($idwf = null, $model) {
        if ($model) {
            $this->db->where(array('idwf' => $model));
        }
        if ($idwf) {
            $this->db->like('id', $idwf);
        }
        return $this->db->count_all_results('case');
    }

    function get_all_cases($offset = 0, $limit = 50, $order = null, $query_txt = null, $model, $fields = array()) {
        if ($fields) {
            $this->db->select($fields);
        }
        if ($model) {
            $this->db->where(array('idwf' => $model));
        }
        if ($query_txt) {
            $this->db->like('id', $query_txt);
        }
        if ($order) {
            #@todo //--check order like
            $this->db->order_by($order);
        }
        $rs = $this->db->get('case', $limit, $offset);

        return $rs->result_array();
    }

    function save_case($case) {
        unset($case['_id']);
        $query = array(
            'id' => $case['id'],
            'idwf' => $case['idwf']
        );
        //----get the status tokens
        //$case['token_status'] = $this->get_token_status($case['idwf'], $case['id']);
        return $this->db->where($query)->update('case', $case);
    }

    function archive_case($case) {
        unset($case['_id']);
        $query = array(
            'id' => $case['id']
        );
        //----get the status tokens
        //$case['token_status'] = $this->get_token_status($case['idwf'], $case['id']);
        return $this->db->where($query)->update('case_archive', $case);
    }

    function gen_case($idwf) {
        $insert = array();
        $trys = 10;
        $i = 0;
        $id = chr(64 + rand(1, 26)) . chr(64 + rand(1, 26)) . chr(64 + rand(1, 26)) . chr(64 + rand(1, 26));
        //---if passed specific id
        if (func_num_args() > 1) {
            $id = func_get_arg(1);
            $passed = true;
            //echo "passed: $id<br>";
        }
        $hasone = false;

        while (!$hasone and $i <= $trys) {//---search until found or $trys iterations
            $query = array('id' => $id, 'idwf' => $idwf);
            $result = $this->db->get_where('case', $query)->result();
            $i++;
            if ($result) {
                if ($passed) {
                    show_error("id:$id already Exists in db.case");
                    $hasone = true;
                    break;
                } else {//---continue search for free id
                    $id = chr(64 + rand(1, 26)) . chr(64 + rand(1, 26)) . chr(64 + rand(1, 26)) . chr(64 + rand(1, 26));
                }
            } else {//---result is null
                $hasone = true;
            }
        }
        if (!$hasone) {//-----cant allocate free id
            show_error("Can't allocate an id in 'case' after $trys attempts");
        }
        //-----make basic object
        $insert['id'] = $id;
        $insert['idwf'] = $idwf;
        $insert['iduser'] = $this->idu;
        $insert['status'] = 'open';
        $insert['checkdate'] = date('Y-m-d H:i:s');
        //----Allocate id in the collection (may result in empty docs)
        $options = array('w' => true);
        $this->db->insert('case', $insert);
        return $id;
    }

    function update_model($idmodel, $data) {
        if (!is_array($data)) {
            show_error(__FUNCTION__ . ': $data must be an array');
        }
        $query = array('idwf' => $idmodel);
        $this->db
                ->where($query)
                ->update($this->bpm_container, $data);
    }

    function update_case_token_status($idwf, $idcase) {
        $case = $this->get_case($idcase, $idwf);
        if (isset($case['status'])) {
            if ($case['status'] <> 'closed') {
                $data['token_status'] = $this->get_token_status($idwf, $idcase);
                $query = array('$set' => (array) $data);
                $criteria = array('idwf' => $idwf, 'id' => $idcase);
                $options = array('upsert' => false, 'w' => true);
                //var_dump2($query,$criteria,$options);
                $this->mongo->db->case->update($criteria, $query, $options);
            }
        }
    }

    function update_case($idwf, $id, $data) {

        $data['id'] = $id;
        $case = $this->get_case($id);
        //---calculate interval since case started
        $dateIn = (isset($case['checkdate'])) ? new DateTime($case['checkdate']) : new DateTime();
        //---now
        $dateOut = new DateTime();
        $data['interval'] = date_diff($dateOut, $dateIn, true);
        //---assign user
        if (!isset($case['iduser']))
            $data['iduser'] = (int) $this->session->userdata('iduser');
        //----update case with latest token status
//      $data['token_status'] = (isset($data['token_status'])) ? $data['token_status'] : $this->get_token_status($case['idwf'], $case['id']);
        $data['token_status'] = $this->get_token_status($case['idwf'], $case['id']);
        $query = array('$set' => (array) $data);
        $criteria = array('id' => $id);
        $options = array('upsert' => true, 'w' => true);
        //var_dump2($query,$criteria,$options);
        $this->mongo->db->case->update($criteria, $query, $options);
    }

    function save_token($token) {
        if (isset($token['_id'])) {
            $_id = $token['_id'];
            unset($token['_id']);
            $criteria = array('_id' => $_id);
            $this->db->where($criteria)->update('tokens', $token);
        } else {
            $this->db->insert('tokens', $token);
        }
        return true;
    }

    function get_icon($type) {
        $icon_map = array(
            'SequenceFlow' => 'jscript/bpm-dna2/stencilsets/bpmn2.0/icons/connector/sequenceflow.png',
            'EndNoneEvent' => 'jscript/bpm-dna2/stencilsets/bpmn2.0/icons/endevent/none.png',
            'EndMessageEvent' => 'jscript/bpm-dna2/stencilsets/bpmn2.0/icons/endevent/message.png',
            'StartMessageEvent ' => 'jscript/bpm-dna2/stencilsets/bpmn2.0/icons/startevent/message.png',
            'Task' => 'jscript/bpm-dna2/stencilsets/bpmn2.0/icons/activity/task.png',
            'CollapsedSubprocess' => 'jscript/bpm-dna2/stencilsets/bpmn2.0/icons/activity/event.subprocess.collapsed.png',
            1 => 'jscript/bpm-dna2/stencilsets/bpmn2.0/icons/activity/event.subprocess.png',
            2 => 'jscript/bpm-dna2/stencilsets/bpmn2.0/icons/activity/expanded.subprocess.png',
            'Subprocess' => 'jscript/bpm-dna2/stencilsets/bpmn2.0/icons/activity/subprocess.png',
            4 => 'jscript/bpm-dna2/stencilsets/bpmn2.0/icons/activity/task.png',
            5 => 'jscript/bpm-dna2/stencilsets/bpmn2.0/icons/artifact/group.png',
            'TextAnnotation' => 'jscript/bpm-dna2/stencilsets/bpmn2.0/icons/artifact/text.annotation.png',
            7 => 'jscript/bpm-dna2/stencilsets/bpmn2.0/icons/catching/cancel.png',
            8 => 'jscript/bpm-dna2/stencilsets/bpmn2.0/icons/catching/compensation.png',
            9 => 'jscript/bpm-dna2/stencilsets/bpmn2.0/icons/catching/conditional.png',
            10 => 'jscript/bpm-dna2/stencilsets/bpmn2.0/icons/catching/error.png',
            11 => 'jscript/bpm-dna2/stencilsets/bpmn2.0/icons/catching/escalation.png',
            12 => 'jscript/bpm-dna2/stencilsets/bpmn2.0/icons/catching/link.png',
            'IntermediateMessageEventCatching' => 'jscript/bpm-dna2/stencilsets/bpmn2.0/icons/catching/message.png',
            'IntermediateParallelMultipleEventCatching' => 'jscript/bpm-dna2/stencilsets/bpmn2.0/icons/catching/multiple.parallel.png',
            15 => 'jscript/bpm-dna2/stencilsets/bpmn2.0/icons/catching/multiple.png',
            'IntermediateSignalEventCatching' => 'jscript/bpm-dna2/stencilsets/bpmn2.0/icons/catching/signal.png',
            'IntermediateTimerEvent' => 'jscript/bpm-dna2/stencilsets/bpmn2.0/icons/catching/timer.png',
            18 => 'jscript/bpm-dna2/stencilsets/bpmn2.0/icons/connector/association.bidirectional.png',
            'Association_Undirected' => 'jscript/bpm-dna2/stencilsets/bpmn2.0/icons/connector/association.undirected.png',
            20 => 'jscript/bpm-dna2/stencilsets/bpmn2.0/icons/connector/association.unidirectional.png',
            'MessageFlow' => 'jscript/bpm-dna2/stencilsets/bpmn2.0/icons/connector/messageflow.png',
            22 => 'jscript/bpm-dna2/stencilsets/bpmn2.0/icons/connector/sequenceflow.png',
            23 => 'jscript/bpm-dna2/stencilsets/bpmn2.0/icons/conversations/communication.png',
            24 => 'jscript/bpm-dna2/stencilsets/bpmn2.0/icons/conversations/participant.png',
            25 => 'jscript/bpm-dna2/stencilsets/bpmn2.0/icons/conversations/subconversation.png',
            26 => 'jscript/bpm-dna2/stencilsets/bpmn2.0/icons/dataobject/data.object.png',
            'DataStore' => 'jscript/bpm-dna2/stencilsets/bpmn2.0/icons/dataobject/data.store.png',
            28 => 'jscript/bpm-dna2/stencilsets/bpmn2.0/icons/dataobject/it.system.png',
            29 => 'jscript/bpm-dna2/stencilsets/bpmn2.0/icons/dataobject/message.png',
            30 => 'jscript/bpm-dna2/stencilsets/bpmn2.0/icons/endevent/cancel.png',
            31 => 'jscript/bpm-dna2/stencilsets/bpmn2.0/icons/endevent/compensation.png',
            32 => 'jscript/bpm-dna2/stencilsets/bpmn2.0/icons/endevent/error.png',
            33 => 'jscript/bpm-dna2/stencilsets/bpmn2.0/icons/endevent/escalation.png',
            34 => 'jscript/bpm-dna2/stencilsets/bpmn2.0/icons/endevent/message.png',
            35 => 'jscript/bpm-dna2/stencilsets/bpmn2.0/icons/endevent/multiple.png',
            36 => 'jscript/bpm-dna2/stencilsets/bpmn2.0/icons/endevent/none.png',
            37 => 'jscript/bpm-dna2/stencilsets/bpmn2.0/icons/endevent/signal.png',
            38 => 'jscript/bpm-dna2/stencilsets/bpmn2.0/icons/endevent/terminate.png',
            39 => 'jscript/bpm-dna2/stencilsets/bpmn2.0/icons/gateway/complex.png',
            'EventbasedGateway' => 'jscript/bpm-dna2/stencilsets/bpmn2.0/icons/gateway/eventbased.png',
            'Exclusive_Databased_Gateway' => 'jscript/bpm-dna2/stencilsets/bpmn2.0/icons/gateway/exclusive.databased.png',
            42 => 'jscript/bpm-dna2/stencilsets/bpmn2.0/icons/gateway/inclusive.png',
            'ParallelGateway' => 'jscript/bpm-dna2/stencilsets/bpmn2.0/icons/gateway/parallel.png',
            44 => 'jscript/bpm-dna2/stencilsets/bpmn2.0/icons/startevent/compensation.png',
            45 => 'jscript/bpm-dna2/stencilsets/bpmn2.0/icons/startevent/conditional.png',
            46 => 'jscript/bpm-dna2/stencilsets/bpmn2.0/icons/startevent/error.png',
            47 => 'jscript/bpm-dna2/stencilsets/bpmn2.0/icons/startevent/escalation.png',
            'StartMessageEvent' => 'jscript/bpm-dna2/stencilsets/bpmn2.0/icons/startevent/message.png',
            49 => 'jscript/bpm-dna2/stencilsets/bpmn2.0/icons/startevent/multiple.parallel.png',
            50 => 'jscript/bpm-dna2/stencilsets/bpmn2.0/icons/startevent/multiple.png',
            'StartNoneEvent' => 'jscript/bpm-dna2/stencilsets/bpmn2.0/icons/startevent/none.png',
            52 => 'jscript/bpm-dna2/stencilsets/bpmn2.0/icons/startevent/signal.png',
            53 => 'jscript/bpm-dna2/stencilsets/bpmn2.0/icons/startevent/timer.png',
            'Lane' => 'jscript/bpm-dna2/stencilsets/bpmn2.0/icons/swimlane/lane.png',
            'Pool' => 'jscript/bpm-dna2/stencilsets/bpmn2.0/icons/swimlane/pool.png',
            56 => 'jscript/bpm-dna2/stencilsets/bpmn2.0/icons/swimlane/process.participant.png',
            57 => 'jscript/bpm-dna2/stencilsets/bpmn2.0/icons/throwing/compensation.png',
            58 => 'jscript/bpm-dna2/stencilsets/bpmn2.0/icons/throwing/escalation.png',
            59 => 'jscript/bpm-dna2/stencilsets/bpmn2.0/icons/throwing/link.png',
            'IntermediateMessageEventThrowing' => 'jscript/bpm-dna2/stencilsets/bpmn2.0/icons/throwing/message.png',
            61 => 'jscript/bpm-dna2/stencilsets/bpmn2.0/icons/throwing/multiple.png',
            62 => 'jscript/bpm-dna2/stencilsets/bpmn2.0/icons/throwing/none.png',
            'IntermediateSignalEventThrowing' => 'jscript/bpm-dna2/stencilsets/bpmn2.0/icons/throwing/signal.png',
        );
        if (isset($icon_map[$type])) {
            return $icon_map[$type];
        } else {
            return "";
        }
    }

    function get_status_icon($status) {
        $status_map = array(
            'pending' => 'ui-icon-play',
            'manual' => 'ui-icon-person',
            'user' => 'ui-icon-person',
            'waiting' => 'ui-icon-clock',
            'stoped' => 'ui-icon-closethick',
            'finished' => 'ui-icon-check',
            'canceled' => 'ui-icon-closethick',
        );

        return $status_map[$status];
    }

    function get_tasks_byFilter($filter = array(), $fields = array(), $sort = array()) {
        //$this->db->debug=true;
        $this->db->where($filter);
        $this->db->select($fields);
        $this->db->order_by($sort);
        $rs = $this->db->get('tokens');
        return $rs->result_array();
    }

    function get_tasks($iduser, $idcase = null) {
        $user = $this->user->get_user((int) $iduser);
        $user_groups = $user->group;
        $query = array(
            //'type' =>array('$in'=>array('Task','Exclusive_Databased_Gateway')),
            //'tasktype' => array('$in' => array('User', 'Manual')),
            'type' => array('$in' => array('Task', 'Exclusive_Databased_Gateway', 'CollapsedSubprocess')),
            'title' => array('$exists' => true),
            //'status' => array('$nin' => array('finished','canceled')),
            'status' => 'user',
            '$or' => array(
                array('iduser' => $iduser), //---task i've done or i've started
                array('assign' => $iduser), //----assigned to me
                array('idgroup' => array('$in' => $user_groups)) //---tasks that are to my group
            )
        );
        if ($idcase)
            $query['idcase'] = $idcase;
        //var_dump(json_encode($query));
        return $this->mongo->db->tokens->find($query);
    }
    /** 
    *  get next shape in diagram skiping flows
    */ 
    function get_outgoing_shapes($shape, $wf) {
        $out = array();
        foreach ($shape->outgoing as $out_shape) {
            $out[] = $this->get_shape($out_shape->resourceId, $wf);
        }
        return $out;
    }

    function get_next_shapes($shape, $wf) {
        //---get next shape in diagram skiping flows
        $next = array();
        foreach ($shape->outgoing as $out) {
            $this_shape = $this->get_shape($out->resourceId, $wf);
            if ($this_shape->stencil->id == 'SequenceFlow')
                $next[] = $this->get_shape($this_shape->outogoing{0}->resourceId, $wf);
        }
        return $next;
    }

    function get_shape($resourceId, &$wf) {
        $debug = (isset($this->debug[__FUNCTION__])) ? $this->debug[__FUNCTION__] : false;
        if ($debug)
            echo "<h2>get_shape</h2>" . $resourceId . '<hr/>';
        foreach ($wf->childShapes as $key => $obj) {
            if ($debug)
                echo "Analizing:" . $obj->stencil->id . '<hr>';
            if ($obj->resourceId == $resourceId) {
                return $wf->childShapes->$key;
            }
            if (in_array($obj->stencil->id, $this->digInto)) {
                $thisobj = $this->get_shape($resourceId, $wf->childShapes->$key);
                if ($thisobj)
                    return $thisobj;
            }
        }
    }

    function get_shape_byname($name, $wf) {
        $debug = (isset($this->debug[__FUNCTION__])) ? $this->debug[__FUNCTION__] : false;
        //$debug = true;
        if ($debug)
            echo '<h2>' . __FUNCTION__ . '</h2>' .
            "Called @ " . xdebug_call_file() . "<br/>Line:" . xdebug_call_line() . "<br/>from: <b>" . xdebug_call_function() . '</b><hr/>';
        $rtnarr = array();
        //--con vert $wf to object;
        $wf = (object) $wf;
        if (!strstr($name, '/'))
            $name = '/' . $name . '/';
        foreach ($wf->childShapes as $obj) {
            if ($debug)
                echo "Analizing:" . $obj->stencil->id . '<hr>';
            if (preg_match($name, $obj->stencil->id)) {
                $rtnarr[] = $obj;
            }
            //---Search inside this objects
            if (in_array($obj->stencil->id, $this->digInto)) {
                if ($debug)
                    echo "&nbsp;&nbsp;&nbsp;Recalling:" . $obj->stencil->id . '<hr>';
                $shapes = $this->get_shape_byname($name, $obj);
                if ($shapes)
                    $rtnarr = array_merge($shapes, $rtnarr);
            }
        }
        return $rtnarr;
    }

    function get_shape_byprop($parray, $wf, $exclude = array()) {
        $debug = (isset($this->debug[__FUNCTION__])) ? $this->debug[__FUNCTION__] : false;
//        $debug = true;
        if ($debug) {
            echo '<h2>' . __FUNCTION__ . '</h2>' .
            "Called @ " . xdebug_call_file() . "<br/>Line:" . xdebug_call_line() . "<br/>from: <b>" . xdebug_call_function() . '</b><hr/>';
            var_dump($parray);
        }
        $rtnarr = array();
        //--con vert $wf to object;
        $wf = (object) $wf;
        foreach ($wf->childShapes as $obj) {
            if ($debug)
                echo "Analyzing:" . $obj->stencil->id . ' ' . $obj->resourceId . '<hr>';
            if (!in_array($obj->resourceId, $exclude)) {
                $has_all = array();
                //--start Analysis
                foreach ($parray as $pname => $pvalue) {
                    //---set prop match to zero
                    $hass_all[$pname] = 0;
                    //---Check 4 matching prop
                    if (isset($obj->properties->$pname)) {
                        //---allow regexps to be passed textual
                        if (!strstr($pvalue, '/'))
                            $pvalue = '/^' . preg_quote($pvalue) . '$/';
                        //---check for equal
                        if (preg_match($pvalue, $obj->properties->$pname))
                            $has_all[$pname] = 1;
                    }
                }
                //---only return obj if matching props equals count of $parr
                //var_dump($parray, $has_all, count($parray) == array_sum($has_all));
                if (count($parray) == array_sum($has_all)) {
                    $rtnarr[] = $obj;
                }
            }
            //---End analisys
            if (in_array($obj->stencil->id, $this->digInto)) {
                if ($debug)
                    echo "   Recalling:" . $obj->stencil->id . '<hr>';
                $shape = $this->get_shape_byprop($parray, $obj, $exclude);
                if ($shape)
                    $rtnarr = array_merge($shape, $rtnarr);
            }
        }
        return $rtnarr;
    }

    function get_all_shapes($wf) {
        $debug = (isset($this->debug[__FUNCTION__])) ? $this->debug[__FUNCTION__] : false;
        //$debug=true;
        if ($debug)
            echo '<h2>' . __FUNCTION__ . '</h2>' .
            "Called @ " . xdebug_call_file() . "<br/>Line:" . xdebug_call_line() . "<br/>from: <b>" . xdebug_call_function() . '</b><hr/>';
        $rtnarr = array();
        //--con vert $wf to object;
        $wf = (object) $wf;
        foreach ($wf->childShapes as $obj) {
            if ($debug)
                echo "Analyzing:" . $obj->stencil->id . '<hr>';
            $has_all = array();
            //--start Analysis

            $rtnarr[] = $obj;

            //---End analisys
            if (in_array($obj->stencil->id, $this->digInto)) {
                if ($debug)
                    echo "   Recalling:" . $obj->stencil->id . '<hr>';
                $shape = $this->get_all_shapes($obj);
                if ($shape)
                    $rtnarr[] = $shape[0];
            }
        }
        return $rtnarr;
    }

    function bindArrayToObject($array) {
        //$return = json_decode(json_encode($array));
        $return = new stdClass();
        foreach ($array as $k => $v) {
            if (is_array($v)) {
                $return->$k = $this->bindArrayToObject($v);
            } else {
                $return->$k = $v;
            }
        }
        return $return;
    }

    function get_shape_parent($resourceId, $wf) {
        $debug = (isset($this->debug[__FUNCTION__])) ? $this->debug[__FUNCTION__] : false;
        //$debug=true;
        if ($debug)
            echo '<h2>' . __FUNCTION__ . '</h2>' . $resourceId . '<hr/>';
        foreach ($wf->childShapes as $obj) {
            if ($debug)
                echo 'Analizing:' . $obj->stencil->id . '<hr>';
            if ($obj->resourceId == $resourceId) {
                if ($debug)
                    echo "FOUND!(direct)";
                return $wf;
            }
            if (in_array($obj->stencil->id, $this->digInto)) {
                if ($debug)
                    echo "&nbsp;&nbsp;&nbsp;Recalling:" . $obj->stencil->id . '<hr>';
                $thisobj = $this->get_shape_parent($resourceId, $obj);
                if ($debug)
                    var_dump2('$thisobj', $thisobj);
                if ($thisobj) {
                    if ($debug)
                        echo "FOUND!(recall)";
                    return $thisobj;
                }
            }
        }
    }

    function get_previous($resourceId, $wf) {
        $shapes = array();
        $flows = $this->bpm->get_inbound_shapes($resourceId, $wf);
        foreach ($flows as $flow) {
            $tmp = $this->bpm->get_inbound_shapes($flow->resourceId, $wf);
            $shapes[] = $tmp[0];
        }
        return $shapes;
    }

    function get_inbound_shapes($resourceId, $wf) {
        $debug = (isset($this->debug[__FUNCTION__])) ? $this->debug[__FUNCTION__] : false;
        $rtnarr = array();
        //$debug=true;
        if ($debug)
            echo "<h2>get_inbound_shapes</h2>" . $resourceId . '<hr/>';

        foreach ($wf->childShapes as $obj) {
            if ($debug)
                echo "Searching in:" . $obj->properties->name . ':' . $obj->stencil->id . ':' . $obj->resourceId . '<br/>';
            //---if it's a pool or a lane search inside
            //----why did i do this?
            if (in_array($obj->stencil->id, $this->digInto)) {
                if ($debug)
                    echo "&nbsp;&nbsp;&nbsp;Recalling:" . $obj->stencil->id . '<hr>';
                $shape = $this->get_inbound_shapes($resourceId, $obj);
                if ($shape)
                    $rtnarr[] = $shape[0];
            }
//---go thru outgoing
            foreach ($obj->outgoing as $out) {
                if ($out->resourceId == $resourceId) {
                    $rtnarr[] = $obj;
                }
            }//---end foreach
        }
        if ($debug) {
            echo 'Return:<br/>';
            var_dump2($rtnarr);
            echo '<hr/>';
        }
        return $rtnarr;
    }

    function get_start_shapes($wf) {
        $debug = (isset($this->debug[__FUNCTION__])) ? $this->debug[__FUNCTION__] : false;
        $start_shapes = array();
        //---Get start shape
        foreach ($wf->childShapes as $obj) {
            // find childs
            //if (preg_match('/^Start/', $obj->stencil->id)) {
            if ($obj->stencil->id == 'StartNoneEvent') {
                $start_shapes[] = $obj;
                if ($debug) {
                    echo '<h2>$start_shapes</h2>';
                    var_dump2($obj);
                    echo '<hr>';
                }
            }
            //----don't look in subprocess
            if (in_array($obj->stencil->id, array('Pool', 'Lane'))) {
                if ($debug)
                    echo "&nbsp;&nbsp;&nbsp;Recalling:" . $obj->stencil->id . '<hr>';
                $thisobj = $this->get_start_shapes($obj);
                if ($thisobj)
                    $start_shapes[] = $thisobj[0];
            }
        }
        return $start_shapes;
    }

    function update_history($idwf, $idcase, $data) {
        $query = array('id' => $idcase, 'idwf' => $idwf);
        $options = array('w' => true, 'justOne' => true);
        $action = array(
            '$push' => array(
                'history' => $data
            )
        );
        $this->mongo->db->case->update($query, $action);
    }

    function movenext($shape_src, $wf, $token = array(), $process_out = true) {
        $debug = (isset($this->debug[__FUNCTION__])) ? $this->debug[__FUNCTION__] : false;
        // $debug=true;

        if ($debug)
            echo '<h2>' . __FUNCTION__ . '</h2>';
        //----ignore certainshapes
        $ignore_shapes = array('TextAnnotation', 'Association_Undirected');
        if (in_array($shape_src->stencil->id, $ignore_shapes)) {
            return;
        }
        //---set default status
        $status = 'pending';
        //---mark this shape as FINISHED
        $token = $this->get_token($wf->idwf, $wf->case, $shape_src->resourceId);
        //---if shape haven't been assigned then assign to performer / runner
        if ($shape_src->stencil->id == 'Task') {
            if (!isset($token['assign'])) {
                $token['assign'] = array($this->idu);
            } else {
                if ($token['assign'] == '')
                    $token['assign'] = array($this->idu);
            }
        }
        //---set status
        $token['status'] = 'finished';
        //---ensures has needed values
        //---set Runtimes
        $token['run'] = (isset($token['run'])) ? $token['run'] + 1 : 1;
        $token = $this->token_checkin($token, $wf, $shape_src);
        //---calculate interval since case started
        $case = $this->bpm->get_case($wf->case, $wf->idwf);
        $dateIn = new DateTime($case['checkdate']);
        //---now
        $dateOut = new DateTime();
        //---calculate time diff
        $token['interval'] = date_diff($dateOut, $dateIn, true);

        //////////////////////////////////////////////////////////////////////// 
        //////////////     UPDATE PARENT LANE                     ////////////// 
        //////////////////////////////////////////////////////////////////////// 
        $shape = $shape_src;
        if($debug)
            echo "<h2>".$shape->resourceId.' '.$shape->stencil->id.'</h2>';
        $lane = $this->find_parent($shape, 'Lane', $wf);
        //---try to get resources from lane
        if ($lane) {
            $tokenLane['interval'] = date_diff($dateOut, $dateIn, true);
            $this->set_token($wf->idwf, $wf->case, $lane->resourceId, 'Lane', $status = 'finished', $tokenLane);
        }
        //////////////////////////////////////////////////////////////////////// 
        //////////////     SAVE HISTORY IN CASE          /////////////////////// 
        //////////////////////////////////////////////////////////////////////// 
        $history = array(
            'checkdate' => date('Y-m-d H:i:s'),
            'microtime' => microtime(),
            'resourceId' => $shape_src->resourceId,
            'iduser' => $this->idu,
            'type' => $shape_src->stencil->id,
            'run' => $token['run'],
            'status' => $token['status'],
            'name' => (isset($shape_src->properties->name)) ? $shape_src->properties->name : ''
        );
        $this->update_history($wf->idwf, $wf->case, $history);
        //---remove lock
        $token['lockedBy'] = null;
        $token['lockedDate'] = null;
        //---sanitize Token
        $token = array_filter($token);
        //---SAVE Token as finished
        $this->save_token($token);
        //---Update case status
        $this->update_case_token_status($wf->idwf, $wf->case);
        //---process outgoing
        if ($process_out) {
            if ($shape_src->outgoing) {
                foreach ($shape_src->outgoing as $pointer) {
                    //---Get Token 4 pointer
                    $token = $this->get_token($wf->idwf, $wf->case, $pointer);

                    //---If token already has status leave it alone!
                    if (!isset($token['status']) or true) {
                        $status = 'pending';
                        $shape = $this->get_shape($pointer->resourceId, $wf);
                        if($debug)
                        echo "Setting 'pending' to ".$shape->resourceId.' '.$shape->stencil->id.'<br/>';
                        $token = $this->token_checkin($token, $wf, $shape);
                        //var_dump2('pointer', $pointer->resourceId);
                        //----skip ignored
                        if (in_array($shape->stencil->id, $ignore_shapes)) {
                            continue;
                        }
                        //var_dump2($shape);
                        if ($debug)
                            echo $shape->stencil->id . ' ' . $shape->resourceId . '<br/>';
                        if ($shape) {
                            if (isset($shape->properties->name))
                                $token['title'] = $shape->properties->name;
                            //---//////////////////////////////////////////////////////////////////////////////////////////
                            //---//////////////////////////////////////////////////////////////////////////////////////////
                            //----------------------------------------------------------
                            //----------SAVE token--------------------------------------
                            $this->set_token($wf->idwf, $wf->case, $shape->resourceId, $shape->stencil->id, $status, $token);

                            //---end if($sahpe)
                        } else {
                            show_error("The shape $pointer->resourceId doesn't exists anymore");
                        }
                    }
                }
            }
        }//---don't process outgoing flow
        return true;
    }

    function token_checkin($token, $wf, $shape) {
        $token['checkdate'] = (!isset($token['checkdate'])) ? date('Y-m-d H:i:s') : $token['checkdate'];
        $token['resourceId'] = $shape->resourceId;
        $token['type'] = $shape->stencil->id;
        $token['idwf'] = $wf->idwf;
        $token['case'] = $wf->case;
        $token['idu'] = $this->idu;
        $token['microtime'] = microtime();
        $token['checkdate'] = (!isset($token['checkdate'])) ? date('Y-m-d H:i:s') : $token['checkdate'];
        return $token;
    }

    function bindObjectToArray($object) {
        if (!is_object($object) && !is_array($object)) {
            return $object;
        }
        if (is_object($object)) {
            $object = get_object_vars($object);
        }
        return array_map(array($this, 'bindObjectToArray'), $object);
    }

    function toRegex(&$item) {
//---usage array_walk_recursive($query,'toRegex');
        if (is_array($item)) {
            $arr = each($item);
            $key = $arr['key'];
            $itemc = $arr['value'];
            //var_dump2("$key holds $itemc<br/>");
            if ($key == '$regex') {
                $item = array($key => new MongoRegex($itemc));
            }
        }
    }

    function assign($shape, $wf) {
        $debug = (isset($this->debug[__FUNCTION__])) ? $this->debug[__FUNCTION__] : false;
        if ($debug)
            echo '<H1>Assign:' . $shape->properties->name . '</H1>';
        $token = $this->get_token($wf->idwf, $wf->case, $shape->resourceId);
        //---set special status "user"
        //---Get Case
        $case = $this->get_case($wf->case);


        //---Set Initiator same as case creator
        $this->user->Initiator = (int) $case['iduser'];
        //---set data as token data
        $data = $token;
        //--remove unnecesary data
        $status = $token['status'];
        $data['_id'] = null;
        $data['status'] = null;
        $data = array_filter($data);
        if ($debug) {
            
        }
        //---set Title
        if ($shape->stencil->id == 'Task') {
            if ($shape->properties->tasktype == 'User')
                $status = 'user';
            $data['tasktype'] = $shape->properties->tasktype;
        }

        $data['title'] = $shape->properties->name;

        ////---check if key exists and if not make it an array.
        $data['assign'] = (isset($data['assign'])) ? $data['assign'] : array();
        ////---check if key exists and if not make it an array.
        $data['idgroup'] = (isset($data['idgroup'])) ? $data['idgroup'] : array();

        ////////////////////////////////////////////////////////////////////////////
        //-------------------- RESOURCE ASSIGNMENTS---------------------------------
        ////////////////////////////////////////////////////////////////////////////
        //----This will determine the execution policy------------------------------
        //---get parent user group 4 Lanes override assignment? no!
        //----set assign to group if is in a Pool/Lane
        $user = $this->user->get_user($this->idu);
        /*
         * PARENT RESOURCES
         */
        $parent = $this->bpm->find_parent($shape, 'Lane', $wf);
        if ($parent) {
            $data['parent'] = $parent->resourceId;
            //----try to get group by name
//            $group_name = $wf->idwf . '/' . $parent->properties->name;
            $group_name = $wf->folder . '/' . $parent->properties->name;
            $group = $this->group->get_byname($group_name);
            $parent_token = $this->get_token($wf->idwf, $wf->case, $parent->resourceId);
            //---check if parent is preassigned
            $data['idgroup'] = (isset($parent_token['idgroup'])) ? array_merge($parent_token['idgroup'], $data['idgroup']) : $data['idgroup'];
            $data['assign'] = (isset($parent_token['assign'])) ? array_merge($parent_token['assign'], $data['assign']) : $data['assign'];

            //---ASSIGN to the group the lane represents
            if ($group) {
                $idgroup = (int) $group['idgroup'];
                $data['idgroup'][] = $group['idgroup'];
                //---if group exists add it to the array
            } else {
                //---if group doesn't exists add a -1
                //---autocreate group here
                if ($this->config->item('auto_create_groups')) {
                    $idgroup = $this->group->genid();
                    $group = array();
                    $group['idgroup'] = $idgroup;
                    $group['name'] = $group_name;
                    $this->group->save($group);
                } else {
                    $idgroup = -1;
                }
                $data['idgroup'][] = $idgroup;
            }
            /*
             * TRY GET Lane Resources
             */
            $resources = $this->get_resources($parent, $wf);
            if ($debug) {
                echo "Get Resources result:<br/>";
                var_dump($resources);
            }
            if (count($resources)) {
                $data['assign'] = (isset($resources['assign'])) ? array_merge($resources['assign'], $data['assign']) : array();
                $data['idgroup'] = (isset($resources['idgroup'])) ? array_merge($resources['idgroup'], $data['idgroup']) : array();
            } else {
                //---check if owner/initiator is in the group
                if ($debug)
                    echo "Check if owner/initiator is in the group<br/>";
                $initiator = $this->user->get_user($this->user->Initiator);
                if (in_array($idgroup, $initiator->group)) {
                    $data['assign'][] = $this->user->Initiator;
                    if ($debug)
                        echo '<H3>Assign Initiator as him belongs to lane group</H3>';
                }

                // If lane has no resources then try some other approach
                //----Assign the the shape to the runner if belongs to group and assignment hasn't been set
                if (!count($data['assign'])) {
                    if (in_array($idgroup, $user->group)) {
                        $data['assign'][] = $this->idu;
                        if ($debug)
                            echo '<H3>Auto-Assign Runner have parent "LANE" but no resources found</H3>';
                    }
                }
            }
        }
        /*
          //----SHAPE HAS NO PARENT LANE
          else {
          if ($debug)
          echo '<H3>Auto-Assign Runner have no parent "LANE"</H3>';
          //----Assign the the shape to the runner
          $data['assign'][] = $this->user->Initiator;
          }
         */
        /*
         * EVAL SHAPE RESOURCES
         */
        //---now get specific task assignements and added (if no parent lanes runner has to be in assign group
        if (isset($shape->properties->resources->items)) {
            //---merge assignment with specific data.
            $resources = $this->get_resources($shape, $wf);
            if (count($resources)) {
                $data['assign'] = (isset($resources['assign'])) ? array_merge($resources['assign'], $data['assign']) : array();
                $data['idgroup'] = (isset($resources['idgroup'])) ? array_merge($resources['idgroup'], $data['idgroup']) : array();
            } else {
                if ($debug)
                    echo '<H3>Auto-Assign Runner no resources found, $shape->properties->resources->items is not set </H3>';
                //----Assign the the shape to the runner
                $data['assign'][] = $this->idu;
            }
        }

        //---if the user who is running the process is an admin assign him
        if ($this->config->item('auto_add_admin')) {
            if ($this->user->isAdmin($user)) {
                $data['assign'][] = $this->idu;
                if ($debug)
                    echo '<H3>Auto Add Admin</H3>';
            }
        }

        if ($this->config->item('auto_assign_admin')) {
            if ($debug)
                echo '<H3>Auto-Assign Admin</H3>';
            if ($this->user->isAdmin($user)) {
                $data['assign'] = array($this->idu);
            }
        }
        //---clear assign
        $data['assign'] = array_unique(array_filter($data['assign']));
        //---clear idgroup
        $data['idgroup'] = array_unique(array_filter($data['idgroup']));

        ///-clear data
        $data = array_filter($data);

        //---if assignment not set either by group or explicit assignment then assign task to "Initiator"
        if (!isset($data['assign']) or !count($data['assign'])) {
            if (isset($data['idgroup'])) {
                if (count($data['idgroup'])) {
                    $initiator = $this->user->get_user($this->user->Initiator);
                    if (array_intersect($data['idgroup'], $initiator->group)) {
                        $data['assign'][] = $this->user->Initiator;
                        if ($debug)
                            echo '<H3>Assign Initiator as him belongs to lane group</H3>';
                    }
                }
            } else {
                $data['assign'][] = $this->user->Initiator;
            }
        }


        if ($debug)
            var_dump2($data);
        //----SAVE TOKEN
        $this->set_token($wf->idwf, $wf->case, $shape->resourceId, $shape->stencil->id, $status, $data);
        //----SAVE PARENT TOKEN IF ANY
        if ($parent) {
            $token = $this->get_token($wf->idwf, $wf->case, $parent->resourceId);
            $status = $token['status'];
            if (isset($token['assign'])) {
                $data_parent['assign'] = (isset($token['assign'])) ? array_merge($token['assign'], $data['assign']) : $data['assign'];
                $data_parent['assign'] = array_unique($data_parent['assign']);
                $data_parent = array_filter($data_parent);
                $this->set_token($wf->idwf, $wf->case, $parent->resourceId, $parent->stencil->id, $status, $data_parent);
            }
        }
        return $data;
    }

    function find_parent($shape, $parent_name, $wf) {
        $debug = false;
        $findParent = false;
        $parent = null;
        $i = 0;
        while (!$findParent and $i < 10) {
            $parent = $this->get_shape_parent($shape->resourceId, $wf);
            if ($parent) {
                if ($parent->stencil->id == $parent_name) {
                    $findParent = true;
                    if ($debug)
                        echo "Found:" . $parent->properties->name . "! <br/>";
                } else {
                    if ($debug)
                        echo "Found:" . $parent->stencil->id . '::' . $parent->properties->name . "<br/>";
                    $shape = $parent;
                    $parent = null;
                }
            }
            $i++;
        }
        return $parent;
    }

    function get_resources($shape, $wf, $case = null) {
        $debug = (isset($this->debug[__FUNCTION__])) ? $this->debug[__FUNCTION__] : false;
//        $debug = true;
        $rtn = array();
        if (isset($shape->properties->resources->items)) {
            if ($debug)
                echo 'Resources of:' . $shape->properties->name . '<br/>';
            if ($debug)
                echo 'Resources:' . count($shape->properties->resources->items) . '<br/>';
            /*
             * load case to $this
             */
            if ($case) {
                $this->case = $this->bindArrayToObject($case);
            }
            //---Evaluates each rule for assignements
            foreach ($shape->properties->resources->items as $rule) {
                if ($rule->resourceassignmentexpr) {
                    $resource = $rule->resource;
                    $type = $rule->resource_type;
                    $resourceassignmentexpr = $rule->resourceassignmentexpr;
                    $ruleEval = 'return $this->' . $resource . '->' . $resourceassignmentexpr . ';';
                    //---allow resources to be passed by JSON
                    if (json_decode($resourceassignmentexpr)) {
                        if ($debug)
                            echo '  JSON:' . $resourceassignmentexpr . '<br/>';
//                        $matches = json_decode($resourceassignmentexpr);
                    } else {
                        if ($debug)
                            echo '  Rule:' . $rule->resourceassignmentexpr . '<br/>' . $ruleEval . '<br/>';
                    }

                    switch ($resource) {
                        //---Add matched users to $data array
                        case 'user':
                            $matches = (json_decode($resourceassignmentexpr)) ? json_decode($resourceassignmentexpr) : eval($ruleEval);
                            $matches = (is_array($matches)) ? $matches : (array) $matches;
                            foreach ($matches as $iduser) {

                                $rtn[$type][] = (int) $iduser;
                                if ($debug) {
                                    $user = $this->user->get_user($iduser);
                                    echo "adding user:" . $user->nick . ':' . $user->idu . ':' . $user->name . ' ' . $user->lastname . '<hr/>';
                                }
                            }
                            break;
                        case 'token':
                            $shape = $this->get_shape_byprop(array('name' => str_replace('\n', "\n", $resourceassignmentexpr)), $wf);
                            if ($shape) {
                                $token = $this->get_token($case['idwf'], $case['id'], $shape[0]->resourceId);
                                if ($token) {
                                    if ($debug) {
                                        echo "Get Resources from BPM shape: $resourceassignmentexpr <hr/>";
                                    }
                                    $rtn[$type] = (isset($rtn[$type])) ? $rtn[$type] : array();
                                    $token['assign'] = (isset($token['assign'])) ? $token['assign'] : array();
                                    $rtn[$type] = array_unique(array_merge($token['assign'], $rtn[$type]));
                                }
                            }
                            break;
                        case 'shape':
                            $shape = $this->get_shape_byprop(array('name' => str_replace('\n', "\n", $resourceassignmentexpr)), $wf);
                            if ($shape) {
                                $res_extra = $this->get_resources($shape, $wf, $case);
                                if ($debug) {
                                    echo "Get Resources from BPM shape: $resourceassignmentexpr <hr/>";
                                    var_dump($res_extra, $rtn);
                                }
                                $rtn = array_merge($rtn, $res_extra);
                            }
                            break;
                        case 'case':
                            //---only eval if case passed
                            if ($case) {
                                $matches = (json_decode($resourceassignmentexpr)) ? json_decode($resourceassignmentexpr) : eval($ruleEval);
                                $matches = (is_array($matches)) ? $matches : (array) $matches;
                                foreach ((array) $matches as $iduser) {
                                    $rtn[$type][] = (int) $iduser;
                                    if ($debug) {
                                        $user = $this->user->get_user($iduser);
                                        echo "adding user:" . $user->nick . ':' . $user->idu . ':' . $user->name . ' ' . $user->lastname . '<br/>';
                                    }
                                }
                            }
                            break;
                        case 'group':
                            $matches = (json_decode($resourceassignmentexpr)) ? json_decode($resourceassignmentexpr) : eval($ruleEval);
                            $matches = (is_array($matches)) ? $matches : (array) $matches;
                            foreach ($matches as $group) {
                                $rtn['idgroup'][] = (int) $group['idgroup'];
                                if ($debug)
                                    echo "adding group:" . $group['idgroup'] . ':' . $group['name'] . '<br/>';
                            }
                            break;
                    }//---end switch
                }//--end if rule
            }//---end foreach $rule
        }//---end if has assignments
        //----make assign equals PotentialOwner if exists
        if (isset($rtn['PotentialOwner'])) {
            $rtn['assign'] = $rtn['PotentialOwner'];
        }

        return $rtn;
    }

    function gateway($url) {

        $redir = base_url() . 'bpm/gateway/?url=' . urlencode(base64_encode($url));
        return $redir;
    }

    function is_allowed($token, $user) {
        $is_allowed = false;
        $debug = (isset($this->debug[__FUNCTION__])) ? $this->debug[__FUNCTION__] : false;
        if ($debug)
            echo "Eval is_allowed<br/>";
//---check if the user is assigned to the task
        if (isset($token['assign'])) {
            if (in_array($user->idu, $token['assign'])) {
                $is_allowed = true;
                if ($debug)
                    echo "is_allowed=true user is in token assign<br/>";
            }
        }


//---check if user belong to the group the task is assigned to
//---but only if the task havent been assigned to an specific user
        if (isset($token['idgroup']) and !isset($token['assign'])) {
            foreach ($user->group as $thisgroup) {
                if (in_array((int) $thisgroup, $token['idgroup'])) {
                    $is_allowed = true;
                    if ($debug)
                        echo "is_allowed=true user is in token group<br/>";
                }
            }
        }


        if (!$is_allowed) {
            if ($debug)
                echo "is_allowed=false<br/>";
        }
        return $is_allowed;
    }

    function import($file_import, $overwrite = true, $folder = 'General') {
        $this->load->helper('file');
        $data = pathinfo($file_import);
        /*
         * array (size=4)
          'dirname' => string 'images/zip' (length=10)
          'basename' => string 'fondyfpp.zip' (length=12)
          'extension' => string 'zip' (length=3)
          'filename' => string 'fondyfpp' (length=8)
         */
        $err = false;
        $zip = new ZipArchive;
        if ($zip->open($file_import) === true) {
            $zip->extractTo('./');
            $zip->close();
        } else {
            $err = true;
            $rtnObject['msg'] = "Error can't deflate:$file_import";
            $rtnObject['success'] = false;
        }
        if (!$err) {
            $idwf = $data['filename'];
            $filename = "images/model/$idwf.json";
            $filename_svg = "images/svg/$idwf.svg";
            $model = $this->bpm->model_exists($idwf);

            $svg = read_file($filename_svg);
            if ($raw = read_file($filename)) {
                $data = json_decode($raw, false);
//---if exists set the internal id of the old one
                $thisModel['idwf'] = $idwf;
                $thisModel['data'] = $data;
                $thisModel['folder'] = $folder;
                $thisModel['svg'] = $svg;
                if ($model) {
                    $this->bpm->save($idwf, $data, $svg);
                    $rtnObject['msg'] = "Imported OK! Updated existing model: $idwf";
                    $rtnObject['success'] = true;
                } else {
                    $rtnObject['msg'] = "Imported OK! New Model Created: $idwf";
                    $rtnObject['success'] = true;
                    $rs = $this->bpm->save_raw($thisModel);
                }
            } else {
                $rtnObject['msg'] = "Error reading $file_import";
                $rtnObject['success'] = false;
            }
        }//---not error
        return $rtnObject;
    }

}
