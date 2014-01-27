<?php

class Controlpanel extends MX_Controller {

    function Controlpanel() {
        parent::__construct();
        $this->load->library('parser');
        $this->load->model('user/user');
        $this->load->model('app');
        $this->load->model('bpm/bpm');
        $this->load->model('msg');
        $this->user->authorize('USE,ADM,SUP');
        //----LOAD LANGUAGE
        $this->lang->load('library', $this->config->item('language'));
        $this->lang->load('inbox', $this->config->item('language'));
        $this->idu = (int) $this->session->userdata('iduser');
    }

    function Index() {
        $cpData = $this->lang->language;
        //var_dump($level);
        $cpData['theme'] = $this->config->item('theme');
        $cpData['base_url'] = base_url();
        $cpData['apps'] = $this->user->getapps();
        list($null, $firstApp) = each($cpData['apps']);
        $cpData['active_app'] = ($this->session->userdata('active_app')) ? ($this->session->userdata('active_app')) : $firstApp['idapp'];
        $cpData['idapp'] = $cpData['active_app'];
        $cpData['user'] = $this->user->get_user($this->idu);
        //var_dump($cpData['user']);
        //$this->parser->parse('header',$data);
        $this->parser->parse('dna2/controlpanel', $cpData, false, false);
        //$this->load->view('footer');
    }

    function cp_apps($idapp=0) {
//        $cpData = array();
//        $cpData = $this->lang->language;
//        $cpData['base_url'] = base_url();
//        $cpData['apps'] = $this->user->getapps();
        if ($idapp)
            $this->session->set_userdata('active_app', $idapp);
//        list($null, $firstApp) = each($cpData['apps']);
//        $cpData['active_app'] = ($this->session->userdata('active_app')) ? ($this->session->userdata('active_app')) : $firstApp['idapp'];
        //$this->parser->parse('dna2/cp_apps', $cpData);
    }




    function get_forms($idapp=0) {
        $this->load->helper('html');
        $show_objs = array();
        $level = $this->user->getlevel($this->idu);
        $objs = $this->get_objects($idapp, 'forms');
        //echo json_encode($level);
        foreach ($objs as $idobject) {
            $object = $this->app->get_object($idobject);
            //---check if has View permissions

            if (@$object['visible']) {
                if ($this->user->has('ADM')) {
                    if ($idobject[0] == 'V')
                        $idobject[0] = 'E';
                    $show_objs[$idobject] = "<a href='" . base_url() . "dna2/render/go/$idobject/new' id='$idobject'>" . $idobject . ':' . $object['title'] . "</a>";
                } else {
                    if ($this->user->has($idobject)) {
                        //---if isn't a workflow then go edit no matter what
                        if ($idobject[0] <> 'W'

                            )$idobject[0] = 'E';
                        $show_objs[$idobject] = "<a href='" . base_url() . "dna2/render/go/$idobject' id='$idobject'>" . $idobject . ':' . $object['title'] . "</a>";
                    }
                }
            }
        }
        echo "<div id='tab'>".ul($show_objs). "</div>";
    }

    function get_lists($idapp=0) {
        $this->load->helper('html');
        $show_objs = array();
        $level = $this->user->getlevel($this->idu);
        $objs = $this->get_objects($idapp, 'lists');
        //echo json_encode($level);
        foreach ($objs as $idobject) {
            $object = $this->app->get_object($idobject);
            //---check if has View permissions
            if (@$object['visible']) {
                if ($this->user->has('ADM')) {
                    $show_objs[$idobject] = "<a href='" . base_url() . "dna2/show/records/$idobject/1' id='$idobject'>" . $idobject . ':' . $object['title'] . "</a>";
                } else {
                    if (in_array('L' . $idobject, $level)) {
                        $show_objs[$idobject] = "<a href='" . base_url() . "dna2/show/list/$idobject/1' id='$idobject'>" . $idobject . ':' . $object['title'] . "</a>";
                    }
                }
            }
        }
        echo "<div id='tab'>".ul($show_objs). "</div>";
    }

    function get_queries($idapp=0) {
        $this->load->helper('html');
        $show_objs = array();
        $level = $this->user->getlevel($this->idu);
        $objs = $this->get_objects($idapp, 'queries');
        //echo json_encode($level);
        foreach ($objs as $idobject) {
            $object = $this->app->get_object($idobject);
            //---check if has View permissions

            if ($object['visible']) {
                if ($this->user->has('ADM')) {
                    $show_objs[$idobject] = "<a href='" . base_url() . "dna2/render/search/$idobject' id='$idobject'>" . $idobject . ':' . $object['title'] . "</a>";
                } else {
                    if (in_array('V' . $idobject, $level)) {
                        $show_objs[$idobject] = "<a href='" . base_url() . "dna2/render/search/$idobject' id='$idobject'>" . $idobject . ':' . $object['title'] . "</a>";
                    }
                }
            }
        }
        echo "<div id='tab'>".ul($show_objs). "</div>";
    }

//---Helper fucntions
    function get_objects($idapp=0, $section='forms') {
        $myobjects = array();
        //--user->getapps() already contain filtered perm apps
        $apps = $this->user->getapps();
        //var_dump($apps[$idapp]);
        if (isset($apps[$idapp])) {
            $thisApp = $apps[$idapp];
            if (isset($thisApp['objs'])) {
                foreach ($thisApp['objs'] as $thisObj) {
                    //---add only required section
                    if (isset($thisObj['section'])) {
                        if ($thisObj['section'] == $section) {
                            $myobjects[] = $thisObj['idobj'];
                        }
                    }
                }
            }
            $myobjects = array_filter($myobjects, 'trim');
        }
        //var_dump($myobjects);
        return $myobjects;
    }

    function objfilter($chunk, $type) {
        return (strstr($chunk, $type)) ? $chunk : false;
    }

}

?>
