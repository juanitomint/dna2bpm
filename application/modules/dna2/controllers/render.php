<?php

class Render extends MX_Controller {

    function Render() {
        parent::__construct();
        $this->load->library('parser');
        $this->load->model('user');
        $this->load->model('app');
        $this->user->authorize();
        //----LOAD LANGUAGE
        $this->lang->load('library', $this->config->item('language'));
        //---LOAD CORE Functions
        $this->load->helper('types/text/render');
        $this->load->helper('types/textarea/render');
        $this->load->helper('types/radio/render');
        $this->load->helper('types/combo/render');
        $this->load->helper('types/combodb/render');
        $this->load->helper('types/checklist/render');
        $this->load->helper('types/subform/render');
        $this->load->helper('types/subformparent/render');
        $this->load->helper('types/date/render');
        $this->load->helper('types/datetime/render');
        $this->load->helper('dna');
        //----prepare some globals
        $this->uri_assoc = $this->uri->uri_to_assoc();
        $this->uri_segments = $this->uri->segment_array();
    }

    function Go($idobject='', $id=null) {
        $type = $idobject[0];
        switch ($type) {

            case 'V':
                $this->Vista($idobject, $id);
                break;
            case 'D':
//                $this->Vista($idobject, $id);
                $this->Edit($idobject, $id);
                break;
            case 'Q':
                $this->Search($idobject);
                break;
            case 'F':
                $idobject[0] = 'V';
                $this->Edit($idobject, $id);
            case 'E':
                $idobject[0] = 'V';
                $this->Edit($idobject, $id);
                break;
            case 'P':
                $idobject[0] = 'V';
                $this->Printable($idobject, $id);
                break;
            //---the object in fact is a link to a workflow process
            //---this WF creates a case but doesn't have to appear into InBox (cause is a system WF)
            case 'W':
                $idobject[0] = '';
                $idobject=trim($idobject);
                $this->startSysteWF($idobject, $id);
                break;
        }
    }

    //---------- EDIT -------------
    function Edit($idform, $id='new') {
        //----4 id
        if ($id <> 'new')
            $id = (int) $id;
        //----get url as array
        $segments = $this->uri_segments;

        
        //----get Active application for context
        $idapp = $this->session->userdata('active_app');
        $renderData = array();
        $frames = array();
        //---add language data
        $renderData = $this->lang->language;
        //----get object from DB
        $form = $this->app->get_object($idform);
        if(!$form) show_error ("Couldn't get $idform");

        $renderData +=$form;
        $renderData['form_title']=$form['title'];
        //----set other needed data
        $renderData['theme'] = $this->config->item('theme');
        $renderData['base_url'] = base_url();
        $renderData['idapp'] = $idapp;
        $renderData['idobject'] = $idform;
        $renderData['has_id'] = ($id) ? true : false;
        $renderData['id'] = $id;
        //----Make id available to other scripts trhu $this (CI instance)
        $this->dna_id = $id;
        //----Init options cacher
        $this->options = array();

        //---START Pre edit Hooks
        $path = 'system/application/helpers/edit/pre/';
        $hooks = glob($path . '*.php');
        //var_dump($hooks);
        foreach($hooks as $file) include($file);
        //---END Pre process Hooks

        //----Show/Hide AdminBar
        $renderData['adminbar'] = ($this->user->has('ADM')) ? $this->parser->parse('dna2/adminbar', $renderData, true) : null;
        //----Show/Hide hist
        $renderData['show_hist'] = false and ($this->user->has('ADM') or $this->user->has('SUP')) ? true : false;
        //----Show/Hide Header
        //---- URL RELATED
        //----Show/Hide frameData
        $renderData['showframe'] = (in_array('showframes', $segments) and $this->user->has('ADM')) ? true : false;

        $renderData['header'] = (!in_array('notop', $segments)) ? $this->parser->parse('dna2/header', $renderData, true) : null;
        //----Show/Hide footer
        $renderData['footer'] = (!in_array('nofoot', $segments)) ? $this->parser->parse('dna2/footer', $renderData, true) : null;
        //----Get Frames
        $renderData['frames'] = $this->app->get_form_frames($form);
        //var_dump($renderData['frames']);
        //----prepare frame output 
        $values = $this->app->getall($id, $form['container']);
        //var_dump($id,$form['container'],$values,$values['1790']);
        foreach ($renderData['frames'] as $key => $thisframe) {
            //var_dump($thisframe);
            $value = (isset($values[(int) $thisframe['idframe']])) ? $values[(int) $thisframe['idframe']] : null;
            $renderData['frames'][$key]['value'] = json_encode($value);
            $callfunc = 'edit_' . $thisframe['type'];
            //echo "** CALLING: $callfunc **<hr>";
            //$renderData['frames'][$key]['render']= (function_exists($callfunc)) ? $callfunc($thisframe, $value):null;
            $frames[$key] = (function_exists($callfunc)) ? $callfunc($thisframe, $value) : null;
            //---- Apply parser to the html string
            //
            //$frames[$key]=$this->parser->parse_string($frames[$key],$renderData);
        }
        foreach ($frames as $key => $html)
            $renderData['frames'][$key]['render'] = $html;

        //var_dump($renderData);
        //---set Extras
        foreach ($this->uri_assoc as $key => $value)
            $renderData['form_extra'][] = array(
                'name' => $key,
                'value' => $value,
            );
        //var_dump($renderData);
        $this->parser->parse('dna2/render_edit', $renderData);
    }

    //---------- SEARCH -------------
    function Search($idform) {

        //----get url as array
        $segments = $this->uri->segment_array();
        //----RESET search parameters
        $this->session->set_userdata('searchparams', array($idform => ''));
        //----get Active application for context
        $idapp = $this->session->userdata('active_app');
        $renderData = array();
        $frames = array();
        //---add language data
        $renderData = $this->lang->language;
        //----get object from DB
        $form = $this->app->get_object($idform);
        //var_dump($form);
        $renderData = array_merge($form, $renderData);
        //----set other needed data
        $renderData['theme'] = $this->config->item('theme');
        $renderData['base_url'] = base_url();
        $renderData['idapp'] = $idapp;
        $renderData['idobject'] = $idform;

        //----Init options cacher
        $this->options = array();

        //----Show/Hide AdminBar
        $renderData['adminbar'] = ($this->user->has('ADM')) ? $this->parser->parse('dna2/adminbar', $renderData, true) : null;
        //----Show/Hide Header
        //---- URL RELATED
        //----Show/Hide frameData
        $renderData['showframe'] = (in_array('showframes', $segments) and $this->user->has('ADM')) ? true : false;

        $renderData['header'] = (!in_array('notop', $segments)) ? $this->parser->parse('dna2/header', $renderData, true) : null;
        //----Show/Hide footer
        $renderData['footer'] = (!in_array('nofoot', $segments)) ? $this->parser->parse('dna2/footer', $renderData, true) : null;
        //----Get Frames
        $renderData['frames'] = $this->app->get_form_frames($form);
        //----Clear un-searchable frames (subforms)
        foreach ($renderData['frames'] as $key => $thisframe) {
            //echo $key.':'.$thisframe['type'].'<br>';
            if ($thisframe['type'] == 'subform' or $thisframe['type'] == 'subformparent') {
                //echo "unsetting:$key<br/>";
                unset($renderData['frames'][$key]);
            }
        }
        //----prepare frame output

        foreach ($renderData['frames'] as $key => $thisframe) {
            $value = '';
            $value = (isset($values[(int) $thisframe['idframe']])) ? $values[(int) $thisframe['idframe']] : null;
            $renderData['frames'][$key]['value'] = json_encode($value);
            $callfunc = 'search_' . $thisframe['type'];
            //echo "** CALLING: $callfunc **<hr>";
            //$renderData['frames'][$key]['render']= (function_exists($callfunc)) ? $callfunc($thisframe, $value):null;
            $frames[$key] = (function_exists($callfunc)) ? $callfunc($thisframe, $value) : null;
            //---- Apply parser to the html string
            //
            //$frames[$key]=$this->parser->parse_string($frames[$key],$renderData);
        }
        foreach ($frames as $key => $html)
            $renderData['frames'][$key]['render'] = $html;

        //var_dump($renderData);

        $this->parser->parse('dna2/render_search', $renderData);
    }

    //---------- VIEW -------------
    function Vista($idobject, $id='new') {
        //----4 id
        if ($id == 'new') {//----RAISE ERROR
            show_error("id is needed to render Vista: $idobject");
        }
        if (isset($id))
            $id = (int) $id;
        //----get url as array
        $segments = $this->uri->segment_array();

        //----get Active application for context
        $idapp = $this->session->userdata('active_app');
        $renderData = array();
        $frames = array();
        //---add language data
        $renderData = $this->lang->language;
        //----get object from DB
        $form = $this->app->get_object($idobject);
        $renderData = array_merge($form, $renderData);
        //----set other needed data
        $renderData['theme'] = $this->config->item('theme');
        $renderData['base_url'] = base_url();
        $renderData['idapp'] = $idapp;
        $renderData['idobject'] = $idobject;
        $renderData['has_id'] = ($id) ? true : false;
        $renderData['id'] = $id;
        //----Make id available to other scripts trhu $this (CI instance)
        $this->dna_id = $id;
        //----Init options cacher
        $this->options = array();

        //----Show/Hide AdminBar
        $renderData['adminbar'] = ($this->user->has('ADM')) ? $this->parser->parse('dna2/adminbar', $renderData, true) : null;
        //----Show/Hide Header
        //---- URL RELATED
        //----Show/Hide frameData
        $renderData['showframe'] = (in_array('showframes', $segments) and $this->user->has('ADM')) ? true : false;

        $renderData['header'] = (!in_array('notop', $segments)) ? $this->parser->parse('dna2/header', $renderData, true) : null;
        //----Show/Hide footer
        $renderData['footer'] = (!in_array('nofoot', $segments)) ? $this->parser->parse('dna2/footer', $renderData, true) : null;
        //----Get Frames
        $renderData['frames'] = $this->app->get_form_frames($form);

        //----prepare frame output
        $values = $this->app->getall($id, $form['container']);
        //var_dump($id,$form['container'],$values);
        foreach ($renderData['frames'] as $key => $thisframe) {
            $value = (isset($values[(int) $thisframe['idframe']])) ? $values[(int) $thisframe['idframe']] : null;
            $renderData['frames'][$key]['value'] = json_encode($value);
            //---set callfunc name
            $callfunc = 'edit_' . $thisframe['type'];
            //---change call func 4 certain controls (subforms)
            switch ($thisframe['type']) {
                case 'subform':
                    $callfunc = 'view_' . $thisframe['type'];
                    break;
            }
            //---Disable Edit action
            $thisframe['disabled'] = true;
            //---Locks Controls
            $thisframe['locked'] = true;

            //echo "** CALLING: $callfunc **<hr>";
            //$renderData['frames'][$key]['render']= (function_exists($callfunc)) ? $callfunc($thisframe, $value):null;
            $frames[$key] = (function_exists($callfunc)) ? $callfunc($thisframe, $value) : null;
            //---- Apply parser to the html string
            //
            //$frames[$key]=$this->parser->parse_string($frames[$key],$renderData);
        }
        foreach ($frames as $key => $html)
            $renderData['frames'][$key]['render'] = $html;

        //var_dump($renderData);

        $this->parser->parse('dna2/render_vista', $renderData);
    }
    //---------- PRINT -------------
    function Printable($idobject, $id='new') {
        //----4 id
        if ($id == 'new') {//----RAISE ERROR
            show_error("id is needed to render Vista: $idobject");
        }
        if (isset($id))
            $id = (int) $id;
        //----get url as array
        $segments = $this->uri->segment_array();

        //----get Active application for context
        $idapp = $this->session->userdata('active_app');
        $renderData = array();
        $frames = array();
        //---add language data
        $renderData = $this->lang->language;
        //----get object from DB
        $form = $this->app->get_object($idobject);
        $renderData = array_merge($form, $renderData);
        //----set other needed data
        $renderData['theme'] = $this->config->item('theme');
        $renderData['base_url'] = base_url();
        $renderData['idapp'] = $idapp;
        $renderData['idobject'] = $idobject;
        $renderData['has_id'] = ($id) ? true : false;
        $renderData['id'] = $id;
        //----Make id available to other scripts trhu $this (CI instance)
        $this->dna_id = $id;
        //----Init options cacher
        $this->options = array();

        //----Show/Hide AdminBar
        $renderData['adminbar'] = ($this->user->has('ADM')) ? $this->parser->parse('dna2/adminbar', $renderData, true) : null;
        //----Show/Hide Header
        //---- URL RELATED
        //----Show/Hide frameData
        $renderData['showframe'] = (in_array('showframes', $segments) and $this->user->has('ADM')) ? true : false;

        $renderData['header'] = (!in_array('notop', $segments)) ? $this->parser->parse('dna2/header', $renderData, true) : null;
        //----Show/Hide footer
        $renderData['footer'] = (!in_array('nofoot', $segments)) ? $this->parser->parse('dna2/footer', $renderData, true) : null;
        //----Get Frames
        $renderData['frames'] = $this->app->get_form_frames($form);

        //----prepare frame output
        $values = $this->app->getall($id, $form['container']);
        //var_dump($id,$form['container'],$values);
        foreach ($renderData['frames'] as $key => $thisframe) {
            $value = (isset($values[(int) $thisframe['idframe']])) ? $values[(int) $thisframe['idframe']] : null;
            $renderData['frames'][$key]['value'] = json_encode($value);
            //---set callfunc name
            $callfunc = 'edit_' . $thisframe['type'];
            //---change call func 4 certain controls (subforms)
            switch ($thisframe['type']) {
                case 'subform':
                    $callfunc = 'view_' . $thisframe['type'];
                    break;
            }
            //---Disable Edit action
            $thisframe['disabled'] = true;
            //---Locks Controls
            $thisframe['locked'] = true;

            //echo "** CALLING: $callfunc **<hr>";
            //$renderData['frames'][$key]['render']= (function_exists($callfunc)) ? $callfunc($thisframe, $value):null;
            $frames[$key] = (function_exists($callfunc)) ? $callfunc($thisframe, $value) : null;
            //---- Apply parser to the html string
            //
            //$frames[$key]=$this->parser->parse_string($frames[$key],$renderData);
        }
        foreach ($frames as $key => $html)
            $renderData['frames'][$key]['render'] = $html;

        //var_dump($renderData);

        $this->parser->parse('dna2/render_print', $renderData);
    }

    function startSysteWF($idwf,$id){
    var_dump($idobject);
        
    }



}

?>
