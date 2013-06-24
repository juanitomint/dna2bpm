<?php

class Be extends MX_Controller {

    function __construct() {
        parent::__construct();
        $this->load->library('parser');
        $this->load->model('user');
        $this->load->model('app');
        $this->load->model('backend');
        $this->user->authorize('USE,ADM,SUP');
        //----LOAD LANGUAGE
        $this->lang->load('library', $this->config->item('language'));
        $this->idu = (int) $this->session->userdata('iduser');
    }

    function Index() {
        
    }

    function Edit($idapp=null) {

        $level = $this->user->getlevel($this->idu);
        $cpData = $this->lang->language;
        $segments = $this->uri->segment_array();
        $cpData['nolayout'] = (in_array('nolayout', $segments)) ? '1' : '0';
        //var_dump($level);
        $cpData['theme'] = $this->config->item('theme');
        $cpData['level'] = $level;
        $cpData['base_url'] = base_url();
        $cpData['apps'] = $this->user->getapps();
        list($null, $firstApp) = each($cpData['apps']);
        if (!$idapp) {
            $cpData['active_app'] = ($this->session->userdata('active_app')) ? ($this->session->userdata('active_app')) : $firstApp['idapp'];
        } else {
            $cpData['active_app'] = $idapp;
        }
//$this->parser->parse('header',$data);
        $app = $this->backend->get_app($idapp);
        $cpData['app'][] = $app;
        $this->parser->parse('dna2/backend', $cpData);
        //$this->load->view('footer');
    }


    function get_properties($idform, $idobj, $idapp, $cast_type=null) {
        $level = $this->user->getlevel($this->idu);
        $cpData = $this->lang->language;
        $cpData['idform'] = $idform;
        $cpData['idobj'] = $idobj;
        $cpData['idapp'] = $idapp;
        $cpData['level'] = $level;
        $cpData['base_url'] = base_url();
        if ($idform <> 'new') {
            $cpData['obj'] = $this->app->get_object($idobj);
        } else {
            $cpData['type'] = 'V';
        }

        //---Fix help tag
        $cpData['help'] = (isset($cpData['help'])) ? $cpData['help'] : null;
        //---Visibvle attr
        if (isset($cpData['obj']['visible'])) {
            if ($cpData['obj']['visible'] == 1)
                $cpData['obj']['visible'] = 'checked="checked"';
        }

        //---Locked attr
        if (isset($cpData['obj']['locked'])) {
            if ($cpData['obj']['locked'] == 1)
                $cpData['obj']['locked'] = 'checked="checked"';
        }

        //----replace with cast_type
        if ($cast_type)
            $cpData['obj']['type'] = $cast_type;
        //---parse custom properties based on object
        if (isset($cpData['obj']['type'])) {
            switch ($cpData['obj']['type']) {
                case 'L':
                    if (isset($cpData['filterByUser']))
                        $cpData['filterByUser'] = ($cpData['filterByUser']) ? 'checked="checked"' : '';
                    if (isset($cpData['filterByGroup']))
                        $cpData['filterByGroup'] = ($cpData['filterByGroup']) ? 'checked="checked"' : '';

                    break;
            }
        }
        //var_dump($cpData);
        echo $this->parser->parse('dna2/common_obj_properties', $cpData, true);
    }

    function save($idobj) {
        $post_obj = $this->input->post('obj');

        $isnew = false;
        if ($idobj == 'new') {
            $idform = $this->app->gen_inc('forms', 'idform');
            $idobj = $post_obj['type'] . $idform;
            $post_obj['idobj'] = $post_obj['type'] . $idform;
            $post_obj['idform'] = $idform;
            $idapp = $post_obj['idapp'];
            $isnew = true;
            $this->backend->push_object($idobj, $idapp);
        } else {
            $obj = $this->app->get_object($idobj);
        }
        $idform = $post_obj['idform'];
        $idobj = $post_obj['idobj'];

        //---delete values that are checks
        $obj['visible'] = '';
        $obj['locked'] = '';
        //----prepare json fields
        $post_obj['filters'] = (isset($post_obj['filters'])) ? json_decode($post_obj['filters']) : null;
        //---Clear the object
        $obj = array_filter($obj);
        $post_obj = array_filter($post_obj);

        $new_obj = array_merge($obj, $post_obj);
        $result = $this->app->put_object($new_obj);
        //var_dump($post_obj);
        //var_dump($new_obj);
        $result = date('H:i:s');
        $result.= ( $result['ok']) ? ' Saved OK!' : 'Error:' . $result['err'];
        $result.= ( $result['updatedExisting']) ? ' Updated Existing Object' : '';
        $result.='<br/>';
        echo json_encode(array(
            'result' => $result,
            'isnew' => $isnew,
            'idobj' => $idobj,
            'idform' => $idform
        ));
    }

    function get_container($ident) {

        $rs = $this->mongo->db->entities->findOne(array('ident' => (int) $ident));
        echo json_encode(array('container' => utf8_decode($rs['container'])));
    }

    function delete_object_db($idobj, $idapp) {
        $this->backend->delete_object($idobj);
        $this->backend->remove_from_app($idobj, (int) $idapp);
    }

    function removefromapp($idobj, $idapp) {
        $this->backend->remove_from_app($idobj, (int) $idapp);
    }

    function get_json_editor($idobj) {
        $cpData = $this->app->get_object($idobj);
        $cpData['base_url'] = base_url();
        if ($this->input->post('jsonstring'))
            $cpData['filters'] = json_decode($this->input->post('jsonstring'), true);
        $this->parser->parse('dna2/be_json_editor', $cpData);
    }

    function decode_filters() {
        //var_dump($_POST);
        $frame = $this->input->post('frame');
        $cond = $this->input->post('cond');
        $values = $this->input->post('value');
        foreach ($frame as $key => $value) {
            $string = '';
            $string.="\"$value\":";
            if (trim($cond[$key]) <> '') {
                $string.=str_replace('$value', $values[$key], $cond[$key]);
            } else {
                $string.='"' . $values[$key] . '"';
            }
            $string.="";
            //var_dump($string,json_decode($string));
            $filters[] = $string;
            //$filters[]=json_decode($string,true);
        }
        $rtn = '{' . implode(',', $filters) . '}';
        //$rtn=''. implode(',',$filters) .'';
        echo $rtn;
    }

}