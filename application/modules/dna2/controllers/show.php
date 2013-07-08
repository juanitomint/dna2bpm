<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Show extends MX_Controller {

    function Show() {
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
    }

    //---------- EDIT -------------
    function Records($idobject, $page=1) {
        $page--;
        $maxPages = 10;
        //---get user and register data 4 filters
        $idu = (int) $this->session->userdata('iduser');
        $user = $this->user->get_user($idu);
        //$idg=$user['group'];
        //var_dump('user',$user);
        //----get url as array
        $segments = $this->uri->segment_array();
        //----get Active application for context
        $idapp = $this->session->userdata('active_app');
        $pagesize = $this->config->item('pageSize');
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
        $renderData['filters'] = (isset($form['filters'])) ? json_encode($form['filters']) : null;
        $renderData['sort'] = (isset($form['sortBy'])) ? json_encode($form['sortBy']) : null;
        //---prepare Filters
        if (isset($form['filters'])) {

            $filter = json_encode($form['filters']);
            $filter = str_replace('$idu', $idu, $filter);
            //$filter = str_replace('$idu', '469517683', $filter);
            $form['filters']=json_decode($filter);

            //var_dump(json_decode($filter));
        }

        //--------------SELECT IDS ---------------------------------------------
        //

        $query = (isset($form['filters'])) ? (array) $form['filters'] : array();
        $sort = (isset($form['sortBy'])) ? (array) $form['sortBy'] : array();
        $fields = array_map('toString', $form['frames']);
        $fields[] = 'id';
        //var_dump('container',$form['container'],'$query',json_encode( $query), '$fields', $fields, '$sort', $sort);
        $subforms = $this->mongo->db->selectCollection($form['container'])->find($query, $fields);
        $totalRecords = $subforms->count();
        $subforms->sort($sort);
        $subforms->limit($pagesize);
        if ($page)
            $subforms->skip($pagesize * $page);
        //                                                                    //
        //--------------SELECT IDS ---------------------------------------------
        //var_dump($form['container'],json_encode($query),json_encode($fields),json_encode($sort));
        $renderData['totalRecords'] = $totalRecords;
        $renderData['render'] = $this->get_table($idobject, $pagesize, $page, $subforms);

        //----PAGES
        $totalPages = intval($totalRecords / $pagesize);
        $showPages = min($maxPages, $totalPages);
        //var_dump($totalRecords, $totalPages,$showPages,$page);
        //----GoTo START
        if ($page > 0)
            $renderData['pages'][] = array('link' => "<a href='../$idobject/1'><span class='ui-icon ui-icon-seek-first'></span></a>");
        //----PREV SET
        if ($page + 1 >= $showPages)
            $renderData['pages'][] = array('link' => "<a href='../$idobject/" . ($page + 1 - $showPages) . "' ><span class='ui-icon ui-icon-seek-prev'></span></a>");
        for ($i = 1; $i <= $showPages; $i++) {
            $pageNumber = $page + $i;
            $renderData['pages'][] = array('link' => "<a href='../$idobject/$pageNumber'>" . str_pad($pageNumber, 2, '0', STR_PAD_LEFT) . "</a>");
        }
        //----NEXT SET
        if ($page + $showPages < $totalPages)
            $renderData['pages'][] = array('link' => "<a href='../$idobject/" . (1 + $pageNumber) . "'><span class='ui-icon ui-icon-seek-next'></span></a>");

        $renderData['header'] = (!in_array('notop', $segments)) ? $this->parser->parse('dna2/header', $renderData, true) : null;
        //----Show/Hide footer
        $renderData['footer'] = (!in_array('nofoot', $segments)) ? $this->parser->parse('dna2/footer', $renderData, true) : null;
        //----Get Frames
        $renderData['frames'] = $this->app->get_form_frames($form);



        //var_dump($renderData['pages']);

        $this->parser->parse('dna2/records', $renderData);
    }

    function Results($idobject, $page=1) {
        $page--;
        $maxPages = 10;
        //----get url as array
        $segments = $this->uri->segment_array();
        //----get Active application for context
        $idapp = $this->session->userdata('active_app');
        $pagesize = $this->config->item('pageSize');
        $renderData = array();
        $frames = array();
        $filters = array();

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
        $renderData['sort'] = (isset($form['sortBy'])) ? json_encode($form['sortBy']) : null;

        $renderData['filters'] = array();
        $renderData['frames'] = $this->app->get_form_frames($form);
        //var_dump($renderData['frames']);
        foreach ($renderData['frames'] as $thisFrame) {
            $filterval = '';
            $input = $this->input->post($thisFrame['cname']);
            if ($input) {
                switch ($thisFrame['type']) {
                    case 'checklist':
                        $filterval = (array) $input;
                        break;
                    case 'combo':
                        $filterval = (array) $input;
                        break;
                    case 'combodb':
                        $filterval = (array) $input;
                        break;
                    case 'radio':
                        $filterval = (array) $input;
                        break;
                    case 'subform':

                        break;
                    case 'date':
                        $fromArr = $input;
                        $toArr = $this->input->post('to_' . $thisFrame['cname']);
                        if ($fromArr['Y'] <> '' and $fromArr['m'] <> '' and $fromArr['d'] <> '')
                            $filterval = array('$gt' => $fromArr['Y'] . '-' . $fromArr['m'] . '-' . $fromArr['d']);
                        break;
                    case 'datetime':
                        $fromArr = $input;
                        $toArr = $this->input->post('to_' . $thisFrame['cname']);
                        break;
                    default:
                        $filterval = new MongoRegex('/' . $input . '/i');
                        break;
                }

                if ($filterval)
                    $filters[$thisFrame['idframe']] = $filterval;
            }
        }
        $form['filters'] = $filters;
        var_dump($form['container'], json_encode($filters));
        //--------------SELECT IDS ---------------------------------------------
        //                                                                    //
        $query = (isset($form['filters'])) ? (array) $form['filters'] : array();
        $sort = (isset($form['sortBy'])) ? (array) $form['sortBy'] : array();
        $fields = $form['frames'];
        $fields[] = 'id';

        $subforms = $this->mongo->db->selectCollection($form['container'])->find($query, $fields);
        $totalRecords = $subforms->count();
        $subforms->sort($sort);
        $subforms->limit($pagesize);
        if ($page)
            $subforms->skip($pagesize * $page);
        //                                                                    //
        //--------------SELECT IDS ---------------------------------------------
        $renderData['totalRecords'] = $totalRecords;
        //----Initialize options cache
        $this->options = array();
        $renderData['render'] = $this->get_table($idobject, $pagesize, $page, $subforms);

        //----PAGES
        $totalPages = intval($totalRecords / $pagesize);
        $showPages = min($maxPages, $totalPages);
        //var_dump($totalRecords, $totalPages,$showPages,$page);
        //----GoTo START
        if ($page > 0)
            $renderData['pages'][] = array('link' => "<a href='../$idobject/1'><span class='ui-icon ui-icon-seek-first'></span></a>");
        //----PREV SET
        if ($page + 1 >= $showPages)
            $renderData['pages'][] = array('link' => "<a href='../$idobject/" . ($page + 1 - $showPages) . "' ><span class='ui-icon ui-icon-seek-prev'></span></a>");
        for ($i = 1; $i <= $showPages; $i++) {
            $pageNumber = $page + $i;
            $renderData['pages'][] = array('link' => "<a href='../$idobject/$pageNumber'>" . str_pad($pageNumber, 2, '0', STR_PAD_LEFT) . "</a>");
        }
        //----NEXT SET
        if ($page + $showPages < $totalPages)
            $renderData['pages'][] = array('link' => "<a href='../$idobject/" . (1 + $pageNumber) . "'><span class='ui-icon ui-icon-seek-next'></span></a>");

        $renderData['header'] = (!in_array('notop', $segments)) ? $this->parser->parse('dna2/header', $renderData, true) : null;
        //----Show/Hide footer
        $renderData['footer'] = (!in_array('nofoot', $segments)) ? $this->parser->parse('dna2/footer', $renderData, true) : null;
        //----Get Frames
        $renderData['frames'] = $this->app->get_form_frames($form);



        //var_dump($renderData['pages']);

        $this->parser->parse('dna2/records', $renderData);
    }

    //--------------SHOW TABLE ---------------------------------------------
    //                                                                    //
    function get_table($idobject, $pagesize=20, $page=0, $subforms) {
        $CI = &$this;
        $retstr = '';
        $nodelete = true;
        $nobrowse = false;
        $frame = array();
        $form = $this->app->get_object($idobject);

        $idparent = null;

        //----get descriptions 4 columns
        $query = array('idframe' => array('$in' => $form['frames']), 'type' => array('$ne' => 'label'));
        $fields = array('idframe', 'desc', 'type', 'idop');
        $fields = array();
        $selframes = $this->mongo->db->frames->find($query, $fields);
        $desc = array();
        //var_dump('frames',json_encode($query),$fields);

        while ($arr = $selframes->getNext()) {
            $frames [$arr ['idframe']] = $arr;
            $desc [$arr ['idframe']] = "<th idframe='" . $arr ['idframe'] . "'>" . $arr ['idframe'] . ': ' . $arr['title'] . "</th>\n";
            $type[$arr['idframe']] = $arr['type'];
        }
        //-----START like subform
        $retstr.="<table id='table_" . $idobject . "' class='tablesorter'><thead><tr><th class=\"{sorter: false}\" >&nbsp;</th>";
        $rows = count($form['frames']);
        $desc = array_slice($desc, 0, $rows, true);

        $retstr.=implode('', $desc);
//---4 delete icon
        if (!$nodelete)
            $retstr.="<th>&nbsp;</th>";
//---Table Header -------------------------------------------------------
        $retstr.="</tr></thead><tbody>";
//------empieza el bucle de los id's
        $i = 0;
        $j = 0;
        while ($arr = $subforms->getNext()) {
            //var_dump('arr',$arr);
            //----Make id available to other scripts trhu $this (CI instance)
            $this->dna_id = $arr['id'];
            $i++;
            $retstr.="<tr id='child_" . $form['idobj'] . $arr['id'] . "' class='row-$i'>";
//----------------iconos-----------------
            $retstr.="<td>";
            // tarjetita ver subform
            if (!$nobrowse) {
                $url = base_url() . 'dna2/render/go/' . $form['redir'] . '/' . $arr['id'];

                $retstr.="<a title=\"" . ucfirst($CI->lang->line('viewData')) . ":$url\" href=\"$url\"  alt=\"" . ucfirst($CI->lang->line('viewData')) . "\" class=\"fg-button ui-state-default fg-button-icon-left ui-corner-all\">";
                $retstr.=$page * $pagesize + $i . '&nbsp;';
                $retstr.=" <span class='ui-icon ui-icon-folder-collapsed subformbrowse'/>";
//$retstr.="<img src=\"$basedir/Icons/24x24/Document 2 Search.gif\" border=\"0\" align=\"absmiddle\" alt=\"Ver Datos\">";
                $retstr.="</a>\n";
            }
            $retstr.="</td>";
            //---------------------------------------
            //----GET all needed frames 4 this ID
            $val = $this->app->getall($arr['id'], $form['container'], $form['frames']);
            //----------------------------------------
            foreach ($desc as $idframe => $title) {
                $callfunc = 'view_' . $type[$idframe];
                //echo "** TRY: $callfunc<hr>";
                $alt = '';
                if (function_exists($callfunc)) {
                    //echo "** CALLING: $callfunc<hr>";
                    //var_dump($val);

                    $alt = $callfunc(
                                    $frames[$idframe],
                                    (isset($val[$idframe])) ? $val[$idframe] : null
                    );
                }
                $retstr.="<td class='col-$j'>" . $alt . "</td>";
                $j++;
            }
            if (!$nodelete) {
                // tarjetita delete
                $retstr.='<td>';
                $retstr.="<a href=\"javascript:delsel(" . $arr['id'] . ",'" . $frame['cname'] . "')\" title=\"" . ucfirst($CI->lang->line('delete')) . "\" class=\"subformDelete\">";
                $retstr.=" <span class=\"ui-corner-all ui-state-default ui-icon ui-icon-closethick subformdelete\"></span>";
                //$retstr.="<img src=\"$base_url/Icons/24x24/Document 2 Delete.gif\" border=\"0\" align=\"absmiddle\" alt=\"Eliminar Relación\">";
                $retstr.="</a>";
                $retstr.='</td>';
            }
            $retstr.="</tr>";
        }
        //----fin bucle id's
        $retstr.="</tbody></table>";
        //-----END like subform

        return $retstr;
    }

    //                                                                    //
    //--------------SHOW TABLE ---------------------------------------------
}

//function toString($val){return (string)$val;}
?>