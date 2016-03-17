<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/**
 * crefis
 *
 * Description of the class crefis
 *
 * @author Juan Ignacio Borda <juanignacioborda@gmail.com>
 *         @date Jul 18, 2014
 */
class crefis extends MX_Controller {
    //--define el token que guarda la data consolidada para buscadores etc
    public $consolida_resrourceId='oryx_6772A7D9-3D05-4064-8E9F-B23B4F84F164';
    
    function __construct() {
        parent::__construct();
        $this->load->model('menu/menu_model');
        $this->load->model('bpm/bpm');
        $this->user->isloggedin();
        // ---base variables
        $this->base_url = base_url();
        $this->module_url = base_url() . $this->router->fetch_module() . '/';
        $this->load->config('crefis/config');
        // ----LOAD LANGUAGE
        $this->lang->load('library', $this->config->item('language'));
        $this->idu = (int) $this->session->userdata('iduser');
        $this->load->library('pagination');

        /* GROUP */
        $user = $this->user->get_user($this->idu);

        $this->id_group = ($user->{'group'});
    }

    function Index() {
        $this->Add_group();
        $this->proyecto();
    }

    function Proyecto($debug=false) {
        $this->user->authorize();
        Modules::run('dashboard/dashboard', 'crefis/json/crefis_proyectos.json',$debug);
    }

    function Evaluador($debug=false) {
        $this->user->authorize();
        Modules::run('dashboard/dashboard', 'crefis/json/crefis_evaluador.json',$debug);
    }

    function Admin($debug=false) {
        $this->user->authorize();
        Modules::run('dashboard/dashboard', 'crefis/json/crefis_admin.json',$debug);
    }

    function Mesa_de_entradas($debug=false) {
        $this->user->authorize();
        Modules::run('dashboard/dashboard', 'crefis/json/crefis_mesaentrada.json',$debug);
    }

    function tile_proyectos() {
        // ----portable indicators are stored as json files
        $kpi = json_decode($this->load->view("crefis/kpi/empresa_proyectos_presentados.json", '', true), true);
        echo Modules::run('bpm/kpi/tile_kpi', $kpi);
    }

    function tile_solicitud() {
        $data ['number'] = 'Solicitud';
        $data ['title'] = 'Crea una nueva solicitud';
        $data ['icon'] = 'ion-document-text';
        $data ['more_info_text'] = 'Comenzar';
        $data ['more_info_link'] = $this->base_url . 'bpm/engine/newcase/model/crefisGral';
        echo Modules::run('dashboard/tile', 'dashboard/tiles/tile-green', $data);
    }

    function tile_aprobados_condicional() {
        $this->user->authorize();
        $this->load->model('crefis/crefis_model');
        $data ['number'] = count($this->crefis_model->get_cases_byFilter_container('crefisGral', 195, array('8334' => '87')));
        $data ['title'] = 'Aprobados Condicional';
        $data ['icon'] = 'ion-document-text';
        $data ['more_info_text'] = 'Listar';
        $data ['more_info_class'] = 'load_tiles_after';
        $data ['more_info_link'] = $this->base_url . 'crefis/listar_aprobados_condicional';

        echo Modules::run('dashboard/tile', 'dashboard/tiles/tile-green', $data);
    }

    function tile_comite() {
        $this->user->authorize();
        $this->load->model('bpm/bpm');
        $this->load->model('dna2/dna2old');
        $dna2url = $this->dna2old->get('url');
        // http://www.accionpyme.mecon.gob.ar/dna2/frontcustom/286/sol_ministro_2014.R.php
        $url = $dna2url . "frontcustom/286/sol_ministro_2014.R.php";
        $url = $this->bpm->gateway($url);
        $data ['number'] = 'Comité';
        $data ['title'] = 'Enviar a Comité';
        $data ['icon'] = 'ion-archive';
        $data ['more_info_text'] = 'Descargar';
        $data ['more_info_link'] = $url;
        echo Modules::run('dashboard/tile', 'dashboard/tiles/tile-green', $data);
    }

    function tile_buscar() {
        $this->user->authorize();
        $data = array();
        return $this->parser->parse('crefis/buscar_proyecto', $data, true);
    }

    function get_token_history($resourceId, $history) {
        $rtnArr = array();
        foreach ($history as $token) {
            if ($token['resourceId'] == $resourceId)
                $rtnArr[] = $token;
        }
        return $rtnArr;
    }

    function listar_pp($action = null) {
        $this->user->authorize();
        $this->load->library('parser');
        $this->load->model('app');
        $option = $this->app->get_ops(772);
        $template = 'crefis/listar_proyectos_fechas_pp';
        $filter = array(
            'idwf' => 'crefisGral',
//            'id' => 'VNSU'
        );
        $cases = $this->bpm->get_cases_byFilter($filter, array(), array('checkdate'));
//        var_dump($cases[0]);exit;
//        var_dump(json_encode($filter),count($tokens));
        $i = 0;
        foreach ($cases as $case) {

            $case_data = $this->bpm->load_case_data($case);
            if (!isset($case_data ['Proyectos_crefis'] ['8339']))
                continue;
            /* STATUS */
            $status = "N/A";
            if (isset($case_data ['Proyectos_crefis'] ['8334'])) {
                $status = $option[$case_data ['Proyectos_crefis'] ['8334'][0]];
            }
            $i++;
            $arr = array(
                'case' => $case['id'],
                'nombre' => (isset($case_data['Empresas']['1693'])) ? $case_data['Empresas']['1693'] : '',
                'cuit' => (isset($case_data['Empresas']['1695'])) ? $case_data['Empresas']['1695'] : '',
                'Nro' => (isset($case_data ['Proyectos_crefis'] ['8339'])) ? $case_data ['Proyectos_crefis'] ['8339'] : 'N/A',
            );


//            $arr['fechapresentacion']=isset($case_data['Proyectos_crefis']['8340'])?date('d/m/Y', strtotime($case_data['Proyectos_crefis']['8340'])):'???';
            //---saco fecha presentación
            $date = explode('/', $case_data['Proyectos_crefis']['8340']);
            $arr['fechapresentacion'] = (count($date)) ? $date[2] . '/' . $date[1] . '/' . $date[0] : '???';
            //---fecha aprobacion / rechazo
            //---busco solicitud rechazada
            $rechazada = $this->get_token_history('oryx_FE3863C1-F7F4-40E1-95E7-FF407112C648', $case['history']);
            if (count($rechazada)) {
                $arr['fechafinal'] = date('d/m/Y', strtotime($rechazada[0]['checkdate']));
                $arr['estado'] = 'Solicitud No Admisible';
            }
            //---busco proyecto rechazado
            $proy_rechazado = $this->get_token_history('oryx_CE7D350E-FEA2-4BFF-B96D-77B29D249C7D', $case['history']);
            if (count($proy_rechazado)) {
                $arr['fechafinal'] = date('d/m/Y', strtotime($proy_rechazado[0]['checkdate']));
                $arr['estado'] = 'Proyecto Rechazado';
            }
            //---busco proyecto pre-aprobado
            $proy_pre = $this->get_token_history('oryx_2882861D-9261-4874-8FA9-12BA72AC43C0', $case['history']);
            if (count($proy_pre)) {
                $arr['fechafinal'] = date('d/m/Y', strtotime($proy_pre[0]['checkdate']));
                $arr['estado'] = 'Proyecto Preaprobado';
            }
            //---busco proyecto baja
            $proy_baja = $this->get_token_history('oryx_16600243-7CAC-4EAB-8AF8-0A135CE14FEE', $case['history']);
            if (count($proy_baja)) {
                $arr['fechafinal'] = date('d/m/Y', strtotime($proy_baja[0]['checkdate']));
                $arr['estado'] = 'Baja Solicitud Coordinación';
            }
            $data['proyectos'][] = $arr;
        }

        $data ['count'] = $i;
        $data['base_url'] = $this->base_url;
//        var_dump($data);
//        exit;
        if ($action == 'xls') {
            header("Content-Description: File Transfer");
            header("Content-type: application/x-msexcel");
            header("Content-Type: application/force-download");
            header("Content-Disposition: attachment; filename=listado_pp.xls");
            header("Content-Description: PHP Generated XLS Data");
        }

        $this->parser->parse($template, $data, false, true);
    }

    function listar_aprobados_condicional($action = null) {
        $this->user->authorize();
        $this->load->library('parser');
        $this->load->model('app');
        $this->load->model('crefis/crefis_model');
        $option = $this->app->get_ops(772);
        $template = 'crefis/listar_proyectos_fechas_pde';
        $template = 'crefis/listar_proyectos';
        $filter = array(
            'idwf' => 'crefisGral',
        );
        $cases = $this->crefis_model->get_cases_byFilter_container('crefisGral', 195, array('8334' => '87'));
//        var_dump($cases[0]);exit;

        $i = 0;
        foreach ($cases as $case) {

            $case_data = $this->bpm->load_case_data($case);
            // var_dump($case_data);exit;
            if (!isset($case_data ['Proyectos_crefis'] ['8339']))
                continue;
            /* STATUS */
            $status = "N/A";
            if (isset($case_data ['Proyectos_crefis'] ['8334'])) {
                $status = $option[$case_data ['Proyectos_crefis'] ['8334'][0]];
            }
            $i++;
            $arr = array(
                'case' => $case['id'],
                'nombre' => (isset($case_data['Empresas']['1693'])) ? $case_data['Empresas']['1693'] : '',
                'cuit' => (isset($case_data['Empresas']['1695'])) ? $case_data['Empresas']['1695'] : '',
                'Nro' => (isset($case_data ['Proyectos_crefis'] ['8339'])) ? $case_data ['Proyectos_crefis'] ['8339'] : 'N/A',
                'estado' => $status,
                'url_reevaluar_pde'=>0
            );

            $url = (isset($case_data ['Proyectos_crefis']['id'])) ? $this->bpm->gateway('../dna2/frontcustom/284/list_docs_crefis_eval.php?id=' . $case_data ['Proyectos_crefis'] ['id']) : '#';
            $arr['link_msg'] = '';
            $arr['link_open'] = $url;

            $arr['url_bpm'] = $this->module_url . 'crefis/escalar_condicional/crefisGral/' . $case['id'];
            $arr['url_clone'] = '';
            $arr['url_cancelar_pp'] = '';
            $arr['url_cancelar_pde'] = '';
            $data['empresas'][] = $arr;
        }

        $this->parser->parse($template, $data, false, true);
    }

    function listar_pde($action = null) {
        $this->user->authorize();
        $this->load->library('parser');
        $this->load->model('app');
        $option = $this->app->get_ops(772);
        $template = 'crefis/listar_proyectos_fechas_pde';
        $filter = array(
            'idwf' => 'crefisGral',
        );
        $cases = $this->bpm->get_cases_byFilter($filter, array(), array('checkdate'));
//        var_dump($cases[0]);exit;
//        var_dump(json_encode($filter),count($tokens));

        $i = 0;
        foreach ($cases as $case) {

            $case_data = $this->bpm->load_case_data($case);
            if (!isset($case_data ['Proyectos_crefis'] ['8339']))
                continue;
            /* STATUS */
            $status = "N/A";
            if (isset($case_data ['Proyectos_crefis'] ['8334'])) {
                $status = $option[$case_data ['Proyectos_crefis'] ['8334'][0]];
            }
            $i++;
            $arr = array(
                'case' => $case['id'],
                'nombre' => (isset($case_data['Empresas']['1693'])) ? $case_data['Empresas']['1693'] : '',
                'cuit' => (isset($case_data['Empresas']['1695'])) ? $case_data['Empresas']['1695'] : '',
                'Nro' => (isset($case_data ['Proyectos_crefis'] ['8339'])) ? $case_data ['Proyectos_crefis'] ['8339'] : 'N/A',
                'estado' => $status,
            );


//            $arr['fechapresentacion']=isset($case_data['Proyectos_crefis']['8340'])?date('d/m/Y', strtotime($case_data['Proyectos_crefis']['8340'])):'???';
            //---saco fecha presentación PP
            $f0 = $this->bpm->get_token('crefisGral', $case['id'], 'oryx_4EFED47A-7AAD-4728-9577-49F8901AC5B9');
            $arr['fechapresentacionPP'] = (count($f0)) ? date('d/m/Y', strtotime($f0['checkdate'])) : '';
            //---saco fecha presentación
            $f1 = $this->bpm->get_token('crefisGral', $case['id'], 'oryx_A58D5ECD-6899-4F60-856C-CFE89B36FB91');
            $arr['fechapresentacion'] = (count($f1)) ? date('d/m/Y', strtotime($f1['checkdate'])) : '';
            //-----tomo el evaluador
            $iduser = $case_data ['Proyectos_crefis'] ['8668'][0];
            $evaluador = $this->user->get_user_safe($iduser);
            $arr['evaluador'] = $evaluador->name . ' ' . $evaluador->lastname;
            //---Tomo primera y ultima fecha eval
            $f1 = $this->get_token_history('oryx_9246751E-B435-4359-988B-8E1B84932A50', $case['history']);
            if (count($f1)) {
                $arr['fechaprimereval'] = (count($f1)) ? date('d/m/Y', strtotime($f1[0]['checkdate'])) : '';
                $arr['fechaultimoeval'] = (count($f1)) ? date('d/m/Y', strtotime($f1[count($f1) - 1]['checkdate'])) : '';
            } else {
                //tomo del token
                $f1 = $this->bpm->get_token('crefisGral', $case['id'], 'oryx_A58D5ECD-6899-4F60-856C-CFE89B36FB91');
                $arr['fechaprimereval'] = (count($f1)) ? date('d/m/Y', strtotime($f1['checkdate'])) : '';
                $arr['fechaultimoeval'] = (count($f1)) ? date('d/m/Y', strtotime($case['checkdate'] . ' +' . $f1['interval']['days'] . ' days')) : '';
                $arr['case'].='*';
            }

            $data['proyectos'][] = $arr;
        }

        $data ['count'] = $i;
        $data['base_url'] = $this->base_url;
//        var_dump($data);
//        exit;
        if ($action == 'xls') {
            header("Content-Description: File Transfer");
            header("Content-type: application/x-msexcel");
            header("Content-Type: application/force-download");
            header("Content-Disposition: attachment; filename=listado_pde.xls");
            header("Content-Description: PHP Generated XLS Data");
        }

        $this->parser->parse($template, $data, false, true);
    }

    function buscar($type = null) {
        $this->user->authorize();
        $this->load->library('parser');
        $template = 'crefis/listar_proyectos';
        $filter = array(
            'idwf' => 'crefisGral',
            'resourceId' =>$this->consolida_resrourceId
        );
        $data ['querystring'] = $this->input->post('query');
        // -----busco en el cuit
        $filter ['$or'] [] = array(
            'data.1695' => array(
                '$regex' => new MongoRegex('/' . $this->input->post('query') . '/i')
            )
        );
        // -----busco en el nombre empresa
        $filter ['$or'] [] = array(
            'data.1693' => array(
                '$regex' => new MongoRegex('/' . $this->input->post('query') . '/i')
            )
        );
        // -----busco en el nro proyecto
        $filter ['$or'] [] = array(
            'data.4837' => array(
                '$regex' => new MongoRegex('/' . $this->input->post('query') . '/i')
            )
        );
        $filter ['$or'] [] = array(
            'case' => array(
                '$regex' => new MongoRegex('/' . $this->input->post('query') . '/i')
            )
        );
        $tokens = $this->bpm->get_tokens_byFilter($filter, array(
            'case',
            'data',
            'checkdate'
                ), array(
            'checkdate' => false
        ));
//        var_dump(json_encode($filter),count($tokens));
        $data ['empresas'] = array_map(function ($token) {
            // var_dump($token['_id']);
            $case = $this->bpm->get_case($token ['case'], 'crefisGral');
            $crefisGral = $this->bpm->get_case($token ['case'], 'crefisGral');
            $data = $this->bpm->load_case_data($case);


            $url = (isset($data ['Proyectos_crefis']['id'])) ? '../dna2/frontcustom/284/list_docs_crefis_eval.php?id=' . $data ['Proyectos_crefis'] ['id'] : '#';
            $url_msg = (isset($token ['case'])) ? $this->base_url . 'crefis/show_msgs/' . $token ['case'] : null;
            /* crefis/COORDINADOR (134) */
            $hist=$this->bpm->get_token_history('crefisGral',$token['case']);
            foreach($hist as $t) $keys[$t['resourceId']]=$t['status'];
            $keys = array_keys($case['token_status']);
            $url_clone = (
                    (in_array(134, $this->id_group) or in_array(138, $this->id_group) or $this->user->isAdmin()) and $case['status'] == 'closed' and in_array('oryx_3346C091-4A4D-4DCD-8DEC-B23C5FE7F80C', $keys) //---está finalizado pero por esta figura
                    and in_array($data ['Proyectos_crefis'] ['8334'][0], array(60, 65, 68, 87)) //---checkeo que esté en alguno de esos estados
                    ) ? $this->base_url . 'crefis/clone_case/crefisGral/crefisGral/' . $token ['case'] : null;
            //---link para cancelar solo para coordinador
            $url_cancelar_pp = ((in_array(134, $this->id_group) or $this->user->isAdmin()) and $case['status'] == 'open') ? $this->base_url . 'crefis/cancelar_pp/' . $token ['case'] : null;
            $url_cancelar_pde = (
                    (in_array(134, $this->id_group) or $this->user->isAdmin()) and in_array('oryx_3346C091-4A4D-4DCD-8DEC-B23C5FE7F80C', $keys) //---está finalizado pero por esta figura
                    and $case['status'] == 'closed'
                    ) ? $this->base_url . 'crefis/cancelar_pde/' . $token ['case'] : null;
            //---link para reevaluar solo para coordinador
            $url_reevaluar_pp = ((in_array(134, $this->id_group) or $this->user->isAdmin()) and $case['status'] == 'open') ? $this->base_url . 'crefis/reevaluar_pp/' . $token ['case'] : null;
            $url_reevaluar_pde = (
                    (in_array(134, $this->id_group) or $this->user->isAdmin()) and in_array('oryx_3346C091-4A4D-4DCD-8DEC-B23C5FE7F80C', $keys) //---está finalizado pero por esta figura
                    and $case['status'] == 'closed'
                    ) ? $this->base_url . 'crefis/reevaluar_pde/' . $token ['case'] : null;
            //---url para checkear

            $url_bpm = '';
            if (in_array(134, $this->id_group) or in_array(135, $this->id_group) or $this->user->isAdmin()) {
                $model = ($crefisGral) ? 'crefisGral' : 'crefisGral';
                $url_bpm = $this->base_url . 'bpm/engine/run/model/' . $model . '/' . $token ['case'];
            }

            /* STATUS */
            $status = "N/A";
            if (isset($data ['Proyectos_crefis'] ['8334'])) {
                $this->load->model('app');
                $option = $this->app->get_ops(772);
                $status = $option[$data ['Proyectos_crefis'] ['8334'][0]];
            }


            return array(
                '_d' => $token ['_id'],
                'case' => $token ['case'],
                'nombre' => (isset($data['Empresas']['1693'])) ? $data['Empresas']['1693'] : '',
                'cuit' => (isset($data['Empresas']['1695'])) ? $data['Empresas']['1695'] : '',
                'Nro' => (isset($data ['Proyectos_crefis'] ['8339'])) ? $data ['Proyectos_crefis'] ['8339'] : 'N/A',
                'estado' => $status,
                'fechaent' => date('d/m/Y', strtotime($token ['checkdate'])),
                'link_open' => $this->bpm->gateway($url),
                'link_msg' => $url_msg,
                'url_clone' => $url_clone,
                'url_bpm' => $url_bpm,
                'url_cancelar_pp' => $url_cancelar_pp,
                'url_cancelar_pde' => $url_cancelar_pde,
                'url_reevaluar_pp' => $url_reevaluar_pp,
                'url_reevaluar_pde' => $url_reevaluar_pde,

            );
        }, $tokens);
        $data ['count'] = count($tokens);
        $data['base_url'] = $this->base_url;
        // var_dump($keys,$data);exit;


        $this->parser->parse($template, $data, false, true);
    }

    function escalar_condicional($idwf, $idcase) {
        $resourceId = 'oryx_10CF34E7-0331-40C0-AE7C-0ABCCE9D015E';
        //---Cargo wf
        $mywf = $this->bpm->load($idwf);
        $wf = $this->bpm->bindArrayToObject($mywf ['data']);
        $shape = $this->bpm->get_shape($resourceId, $wf);
        $wf->idwf = $idwf;
        $wf->case = $idcase;
        /**
         * Cancelo todos los tokens primero
         */
        $active_tokens = $this->bpm->get_pending($wf->idwf, $wf->case, array('user', 'waiting', 'pending'), array());
        foreach ($active_tokens as $token) {
            $token['status'] = 'canceled';
            $this->bpm->save_token($token);
        }

        $token = $this->bpm->get_token($idwf, $idcase, $resourceId);
        //---creo un token falso
        $token = $this->bpm->token_checkin($token, $wf, $shape);
        $token['status'] = 'pending';
        $this->bpm->save_token($token);
        //---run_post($model, $idwf, $case, $resourceId)
        $url = $this->base_url . "bpm/engine/run_post/model/$idwf/$idcase/$resourceId";
//        echo "<a href='$url'>click aquí</a>";
        redirect($url);
    }

    function cancelar_pp($idcase) {
        $idwf = 'crefisGral';
        $resourceId = 'oryx_11C3ABEB-C93F-4536-BCD1-B0D006DA5D12';
        //---Cargo wf
        $mywf = $this->bpm->load($idwf);
        $wf = $this->bpm->bindArrayToObject($mywf ['data']);
        $shape = $this->bpm->get_shape($resourceId, $wf);
        $wf->idwf = $idwf;
        $wf->case = $idcase;
        /**
         * Cancelo todos los tokens primero
         */
        $active_tokens = $this->bpm->get_pending($wf->idwf, $wf->case, array('user', 'waiting', 'pending'), array());
        foreach ($active_tokens as $token) {
            $token['status'] = 'canceled';
            $this->bpm->save_token($token);
        }

        $token = $this->bpm->get_token($idwf, $idcase, $resourceId);
        //---creo un token falso
        $token = $this->bpm->token_checkin($token, $wf, $shape);
        $token['status'] = 'pending';
        $this->bpm->save_token($token);
        //---run_post($model, $idwf, $case, $resourceId)
        $url = $this->base_url . "bpm/engine/run_post/model/$idwf/$idcase/$resourceId";
//        echo "<a href='$url'>click aquí</a>";
        redirect($url);
    }

    function cancelar_pde($idcase) {
        $idwf = 'crefisGral';
        $resourceId = 'oryx_928C03EE-D8FE-4693-A3AD-3F082FE84485';
        //---Cargo wf
        $mywf = $this->bpm->load($idwf);
        $wf = $this->bpm->bindArrayToObject($mywf ['data']);
        $shape = $this->bpm->get_shape($resourceId, $wf);
        $wf->idwf = $idwf;
        $wf->case = $idcase;
        /**
         * Cancelo todos los tokens primero
         */
        $active_tokens = $this->bpm->get_pending($wf->idwf, $wf->case, array('user', 'waiting', 'pending'), array());
        foreach ($active_tokens as $token) {
            $token['status'] = 'canceled';
            $this->bpm->save_token($token);
        }

        $token = $this->bpm->get_token($idwf, $idcase, $resourceId);
        //---creo un token falso
        $token = $this->bpm->token_checkin($token, $wf, $shape);
        $token['status'] = 'pending';
        $this->bpm->save_token($token);
        //---run_post($model, $idwf, $case, $resourceId)
        $url = $this->base_url . "bpm/engine/run_post/model/$idwf/$idcase/$resourceId";
//        echo "<a href='$url'>click aquí</a>";
        redirect($url);
    }

    function reevaluar_pp($idcase) {
        $idwf = 'crefisGral';
        $resourceId = 'oryx_D4FB583E-D562-4036-9E94-BA8D69F3D54F';
        //---Cargo wf
        $mywf = $this->bpm->load($idwf);
        $wf = $this->bpm->bindArrayToObject($mywf ['data']);
        $shape = $this->bpm->get_shape($resourceId, $wf);
        $wf->idwf = $idwf;
        $wf->case = $idcase;
        /**
         * Cancelo todos los tokens primero
         */
        $active_tokens = $this->bpm->get_pending($wf->idwf, $wf->case, array('user', 'waiting', 'pending'), array());
        foreach($active_tokens as $token){
            $token['status']='canceled';
            $this->bpm->save_token($token);

            }

        $token = $this->bpm->get_token($idwf, $idcase, $resourceId);
        //---creo un token falso
        $token = $this->bpm->token_checkin($token, $wf, $shape);
        $token['status'] = 'pending';
        $this->bpm->save_token($token);
        //---run_post($model, $idwf, $case, $resourceId)
        $url = $this->base_url . "bpm/engine/run_post/model/$idwf/$idcase/$resourceId";
//        echo "<a href='$url'>click aquí</a>";
        redirect($url);
    }

    function reevaluar_pde($idcase) {
        $idwf = 'crefisGral';
        $resourceId = 'oryx_1BE4C8D8-E8A5-4D48-B59E-407E7B8E3F7A';
        //---Cargo wf
        $mywf = $this->bpm->load($idwf);
        $wf = $this->bpm->bindArrayToObject($mywf ['data']);
        $shape = $this->bpm->get_shape($resourceId, $wf);
        $wf->idwf = $idwf;
        $wf->case = $idcase;
        /**
         * Cancelo todos los tokens primero
         */
        $active_tokens = $this->bpm->get_pending($wf->idwf, $wf->case, array('user', 'waiting', 'pending'), array());
        foreach($active_tokens as $token){
            $token['status']='canceled';
            $this->bpm->save_token($token);

            }

        $token = $this->bpm->get_token($idwf, $idcase, $resourceId);
        //---creo un token falso
        $token = $this->bpm->token_checkin($token, $wf, $shape);
        $token['status'] = 'pending';
        $this->bpm->save_token($token);
        //---run_post($model, $idwf, $case, $resourceId)
        $url = $this->base_url . "bpm/engine/run_post/model/$idwf/$idcase/$resourceId";
//        echo "<a href='$url'>click aquí</a>";
        redirect($url);
    }

    function mini_status_resultado($idwf, $resourceId, $status) {

        $this->user->authorize();
        $this->load->model('bpm/bpm');
        $this->load->library('parser');
        $template = 'crefis/listar_proyectos';
        $filter = array(
            'idwf' => $idwf,
            'resourceId' => $resourceId,
            'status' => $status,
        );

        $tokens = $this->bpm->get_tokens_byFilter($filter, array(
            'case',
            'data',
            'checkdate'
                ), array(
            'checkdate' => false
        ));
//        var_dump(json_encode($filter),count($tokens));


        $data ['empresas'] = array_map(function ($token) {
            // var_dump($token['_id']);
            $case = $this->bpm->get_case($token ['case']);
            $data = $this->bpm->load_case_data($case);

            $url = (isset($data ['Proyectos_crefis']['id'])) ? '../dna2/RenderView/printvista.php?idvista=3597&idap=286&id=' . $data ['Proyectos_crefis'] ['id'] : '#';
            $url_msg = (isset($token ['case'])) ? 'show_msgs/' . $token ['case'] : null;

            /* crefis/COORDINADOR (134) */
            $url_bpm_check = (in_array(134, $this->id_group)) ? '/bpm/engine/run/model/crefisGral/' . $token ['case'] : null;

            $url_bpm = 0;
            if (isset($url_bpm_check))
                $url_bpm = $this->bpm->gateway($url_bpm_check);

            /* STATUS */
            $status = "N/A";
            if (isset($data ['Proyectos_crefis'] ['8334'])) {
                $this->load->model('app');
                $option = $this->app->get_ops(772);
                $status = $option[$data ['Proyectos_crefis'] ['8334'][0]];
            }

            return array(
                '_d' => $token ['_id'],
                'case' => $token ['case'],
                'nombre' => (isset($data['Empresas']['1693'])) ? $data['Empresas']['1693'] : '',
                'cuit' => (isset($data['Empresas']['1695'])) ? $data['Empresas']['1695'] : '',
                'Nro' => (isset($data ['Proyectos_crefis'] ['8339'])) ? $data ['Proyectos_crefis'] ['8339'] : 'N/A',
                'estado' => $status,
                'url_clone' => null,
                'fechaent' => ($token ['checkdate']) ? date('d/m/Y', strtotime($token ['checkdate'])) : '???',
                'link_open' => $this->bpm->gateway($url),
                'link_msg' => $url_msg,
                'url_bpm' => $url_bpm //---url de procesar tarea
            );
        }, $tokens);


        $data ['count'] = count($tokens);
        //---saco título para el resultado
        $mywf = $this->bpm->load($idwf);
        $wf = $this->bpm->bindArrayToObject($mywf ['data']);
        //---tomo el template de la tarea
        $shape = $this->bpm->get_shape($resourceId, $wf);
        $add = ($status == 'user') ? 'En curso' : 'Finalizado';
        $data['querystring'] = $shape->properties->name . ' / ' . $add;



        $this->parser->parse($template, $data, false, true);
    }

    function setup() {
        $this->user->authorize();
        echo Modules::run('bpm/kpi/import_kpi', 'crefis');
    }

    function eliminar_en_preparacion($process = false) {
        $this->user->authorize();
        $filter = array(
            'resourceId' => 'oryx_B5BD09EE-57CF-41BC-A5D5-FAA1410804A5',
            'status' => 'user',
            'idwf' => 'crefisGral',
        );

        $tokens = $this->bpm->get_tokens_byFilter($filter, array('case'));
        foreach ($tokens as $token) {
            echo "Processing:" . $token['case'] . '<hr/>';
            if ($process)
                $this->bpm->delete_case('crefisGral', $token['case']);
        }
    }

    function ministatus_pp() {
        $this->user->authorize();
        $state = Modules::run('bpm/manager/mini_status', 'crefisGral', 'array');

        $state = array_filter($state, function ($task) {
            return $task ['type'] == 'Task';
        });
        // ---las aplano un poco
        foreach ($state as $task) {
            $task ['user'] = (isset($task ['status'] ['user'])) ? $task ['status'] ['user'] : 0;
            $task ['finished'] = (isset($task ['status'] ['finished'])) ? $task ['status'] ['finished'] : 0;
            $wfData ['mini'] [] = $task;
        }

        //var_dump($wfData);
        $wfData ['base_url'] = base_url();
        $wf = $this->bpm->load('crefisGral');
        $wfData += $wf ['data'] ['properties'];
        $wfData ['name'] = 'Mini Status: ' . $wfData ['name'];
        return $this->parser->parse('crefis/ministatus_pp', $wfData, true, true);
    }

    /**
     * PROYECTS AMOUNT
     *
     * Description Calculate the amount  of money  in projects grouped by status
     * name proyects_amount
     * @author Diego Otero
     */
    function proyects_amount($filtroproy = null) {

        $this->user->authorize();
        $this->load->model('crefis/crefis_model');

        /* OPTIONS */
        $this->load->model('app');
        $option = $this->app->get_ops(772);

        $llamado = (isset($filtroproy)) ? $filtroproy['llamado'] : array('$exists' => true);
        $query = array('8335' => $llamado);

        $cases = $this->crefis_model->get_cases_byFilter_container('crefisGral', 195, $query);
        $cases_arr = array();
        foreach ($cases as $case) {
            $id = $case['data']['Proyectos_crefis']['query']['id'];
            if (isset($id)) {
                $query = array('id' => $id);
                $values = $this->crefis_model->get_amount_stats_by_id($query);

                $ctrl_value = (isset($values[0][8334][0])) ? $values[0][8334][0] : $values[0][8334];
                $value8326 = (isset($values[0][8326])) ? str_replace(",", ".", str_replace(".", "", $values[0][8326])) : 0;
                $value8573 = (isset($values[0][8573])) ? str_replace(",", ".", str_replace(".", "", $values[0][8573])) : 0;

                $amount = ($ctrl_value >= 30) ? $value8573 : $value8326;
                $cases_arr[$option[$ctrl_value]][]  = (float) $amount;
            }
        }


        foreach ($cases_arr as $key => $task) {
            $new_task = array();
            $new_task['status'] = $key;
            $new_task['how_many'] = count($task);
            $new_task['amount'] = "$" . @number_format(array_sum($task), 2, ",", ".");
            $wfData['mini'][] = $new_task;
        }


        $wfData ['base_url'] = base_url();
        $wf = $this->bpm->load('crefisGral');
        $wfData += $wf ['data'] ['properties'];
        $wfData ['name'] = 'Montos por Estados';

        return $this->parser->parse('crefis/montos_estados', $wfData, true, true);
    }

    /* REFACTOR */

    function proyects_amount_ori() {
        $this->user->authorize();
        $state = $this->status_amounts();

        foreach ($state as $key => $task) {

            $new_task = array();
            $new_task['status'] = $key;
            $new_task['how_many'] = count($task);
            $new_task['amount'] = "$" . @number_format(array_sum($task), 2, ",", ".");
            $wfData['mini'][] = $new_task;
        }

        $wfData ['base_url'] = base_url();
        $wf = $this->bpm->load('crefisGral');
        $wfData += $wf ['data'] ['properties'];
        $wfData ['name'] = 'Montos por Estados';

        return $this->parser->parse('crefis/montos_estados', $wfData, true, true);
    }

    /**
     * STATUS_AMOUNTS
     *
     * Description Calculate the amount  of money  in projects grouped by status
     * name status_amounts
     * @author Diego Otero
     */
    function status_amounts() {
        $filter['idwf'] = 'crefisGral';
        $querys = $this->get_amount_stats($filter);

        /* OPTIONS */
        $this->load->model('app');
        $option = $this->app->get_ops(772);

        foreach ($querys as $values) {

            $ctrl_value = (isset($values[0][8334][0])) ? $values[0][8334][0] : $values[0][8334];
            $value8326 = (isset($values[0][8326])) ? str_replace(",", ".", str_replace(".", "", $values[0][8326])) : 0;
            $value8573 = (isset($values[0][8573])) ? str_replace(",", ".", str_replace(".", "", $values[0][8573])) : 0;


            $amount = ($ctrl_value >= 30) ? $value8573 : $value8326;

            foreach ($option as $opt => $desc) {
                if ($opt == $ctrl_value)
                    $cases_arr[$desc][] = (float) $amount;
            }
        }

        return $cases_arr;
    }

    function get_amount_stats($filter) {
        $this->load->model('crefis_model');
        /* get ids */
        $all_ids = array();
        $arr_status = array();


        $allcases = $this->bpm->get_cases_byFilter($filter, array('id', 'idwf', 'data'));

        foreach ($allcases as $case) {
            if (isset($case['data']['Proyectos_crefis']['query']))
                $all_ids[] = $case['data']['Proyectos_crefis']['query'];
        }


        $get_value = array_map(function ($all_ids) {
            return $this->crefis_model->get_amount_stats_by_id($all_ids);
        }, $all_ids);



        return $get_value;
    }

    /* END REFACTOR */

    /**
     * PROYECTS EVALUATOR
     *
     * Description
     * name projects_evaluator
     * @author Diego Otero
     */
    function projects_evaluator() {
        $this->user->authorize();
        $state = $this->evaluator_projects();


        foreach ($state as $key => $task) {
            $new_task = array();
            $project = null;
            foreach ($task as $each) {

                $user = (array) $this->user->get_user_safe($key);
                //$evaluator_info = strtoupper($user['nick']) . " | " . $user['name'] . " " . $user['lastname'];
                $evaluator_info = $user['name'] . " " . $user['lastname'];
                $how_many = count($task);

                $url = '../dna2/RenderView/printvista.php?idvista=3597&idap=286&id=' . $each['project_id'];

                $projData['url'] = $this->bpm->gateway($url);
                $projData['project_value'] = $each['project_ip'];
                $projData['status'] = $each['status'];
                $projData['filing_date'] = $each['filing_date'];
                $projData['cuit'] = $each['cuit'];
                $projData['business_name'] = $each['business_name'];


                $project .= $this->parser->parse('crefis/proyectos_evaluador_anchor', $projData, true, true);
            }


            $new_task['evaluator'] = $evaluator_info;
            $new_task['toggle_id'] = md5($evaluator_info);
            $new_task['how_many'] = $how_many;
            $new_task['project'] = $project;
            $wfData['mini'][] = $new_task;
        }

        $wfData ['base_url'] = base_url();
        $wf = $this->bpm->load('crefisGral');
        $wfData += $wf ['data'] ['properties'];
        $wfData ['name'] = 'Evaluadores por proyecto';

        return $this->parser->parse('crefis/proyectos_evaluador', $wfData, true, true);
    }

    /**
     * EVALUATOR PROJECTS
     *
     * Description
     * name evaluator_projects
     * @author Diego Otero
     */
    function evaluator_projects() {
        $this->load->model('crefis_model');

        $output = 'array';
        $filter = array();

        $filter['idwf'] = 'crefisGral';
        $querys = $this->crefis_model->get_evaluator_by_project($filter);
        //var_dump($querys);exit;

        /* OPTIONS */
        $this->load->model('app');
        $option = $this->app->get_ops(772);


        foreach ($querys[0] as $values) {

            $ctrl_value = (isset($values[8334][0])) ? $values[8334][0] : $values[8334];


            $evaluator_id = $values[8668][0];

            list($filing_year, $filing_month, $filing_day) = explode("/", $values[8340]);
            $filing_date = $filing_day . "/" . $filing_month . "/" . $filing_year;

            $company_id = floatval($values[8325][0]);
            $company = $this->crefis_model->get_company_by_project_by_id($company_id);

            $proyect_array = array(
                "project_ip" => $values[8339]
                , "project_id" => $values['id']
                , "status" => $option[$ctrl_value]
                , "filing_date" => $filing_date
                , "cuit" => $company[0][1695], "business_name" => $company[0][1693]
            );
            $cases_arr[$evaluator_id][] = $proyect_array;
        }

        return $cases_arr;
    }

    function ver_ficha($idwf, $idcase, $token, $id = null) {

        $this->user->authorize();
        $this->load->model('bpm/bpm');
        $this->load->model('dna2/dna2old');
        $dna2url = $this->dna2old->get('url');
        if ($id) {
            $url = $dna2url . "RenderEdit/editnew.php?idvista=3560&origen=V&idap=286&id=$id&idwf=$idwf&case=$idcase&token=$token";
        } else {
            $url = $dna2url . "RenderEdit/editnew.php?idvista=3560&origen=V&idap=286&idwf=$idwf&case=$idcase&token=$token";
        }

        $url = $this->bpm->gateway($url);
        redirect($url);
    }

    function imprimir_proyecto($idwf, $idcase, $token, $id = null) {

        $this->user->authorize();
        $this->load->model('bpm/bpm');
        $this->load->model('dna2/dna2old');
        $dna2url = $this->dna2old->get('url');
//         if ($id) {
//             $url = $dna2url . "frontcustom/284/proyecto_crefis_preA_new.php?id=$id&idwf=$idwf&case=$idcase&token=$token";
//         } else {
//             show_error('El Caso no tiene id de proyecto');
//         }
//         $url = $this->bpm->gateway($url);
//         redirect($url);
        if ($id) {
            $todo = $id . '&idwf=' . $idwf . '&case=' . $idcase . '&token=' . $token;
            echo <<<BLOCK
                <p align='left'>1. <a href="{$dna2url}frontcustom/284/nota_crefis_preapro.php?id=$id" target="_blank">Nota de Presentac&oacute;n</a></p>
                <p align='left'>2. <a href="{$dna2url}frontcustom/284/print_crefis_preA.php?id=$todo" target="_blank">Imprimible del Proyecto</a></p>
                <p align='left'>3. <a href="{$dna2url}frontcustom/284/ddjj_docu_crefis_preA.php?id=$id" target="_blank">Listado de Documentaci&oacute;n a Presentar</a></p>
BLOCK;
        } else {
            echo 'div class="alert alert-success" role="alert">El Caso no tiene id de proyecto</div>';
        }
    }

    function fix_data($case = null) {
        $debug = false;
        $this->load->model('bpm/bpm');
        $resourceId = $this->consolida_resrourceId;
        $filter = ($case) ? array('idwf' => 'crefisGral', 'id' => $case) : array('idwf' => 'crefisGral');
        $rs = $this->bpm->get_cases_byFilter($filter);
        foreach ($rs as $case) {
            if ($debug)
                var_dump($case['id']);
            $token = $this->bpm->consolidate_data('crefisGral', $case['id'], $resourceId);
        }
    }

    function Landing() {
        $this->Add_group();
        redirect($this->module_url);
    }

   /**
     * Agrega el grupo EMPRESA a los que entran al panel para que puedan ejecutar el BPM
     */
    function Add_group() {
        $user =$this->user->get_user($this->idu);
        if (!$this->user->isAdmin($user)) {
            $user=$user;
            $group_add = $this->group->get_byname('CREFIS/EMPRESA');
            array_push($user->group, (int) $group_add ['idgroup']);
            $user->group = array_unique($user->group);
            $this->user->save($user);
        }
    }


    function asignar_evaluador($idwf, $idcase, $tokenId) {
        $this->load->library('parser');
        $this->load->model('user/group');
        $this->load->model('bpm/bpm');
        $case = $this->bpm->get_case($idcase, $idwf);
        $renderData = $this->bpm->load_case_data($case, $idwf);
        //----tomo evaluador del caso
        $evaluador = $renderData['Proyectos_crefis']['4939'][0];
        //----token que hay que finalizar
        $src_resourceId = 'oryx_9E2BE9E9-5067-440E-AAA2-17602D277147';
        // ---Token de pp asignado
        $lane_resourceId = 'oryx_FB601E1C-E420-49D6-BB3C-D8BD4166D1ED';

        $url = $this->base_url . "bpm/engine/assign/model/$idwf/$idcase/$src_resourceId/$lane_resourceId/$evaluador";

        redirect($url);
    }

    function asignar_evaluador_pde($idwf, $idcase, $tokenId) {
        $this->load->library('parser');
        $this->load->model('user/group');
        $this->load->model('bpm/bpm');
        $case = $this->bpm->get_case($idcase, $idwf);
        $renderData = $this->bpm->load_case_data($case, $idwf);
        //----tomo evaluador del caso
        $evaluador = $renderData['Proyectos_crefis']['8668'][0];
        //----token que hay que finalizar
        $src_resourceId = 'oryx_336D35BD-229C-47FA-9012-3670DDB73937';
        // ---Token de pp asignado
        $lane_resourceId = 'oryx_B59407D5-0805-46F0-871F-7C8634B133E1';

        $url = $this->base_url . "bpm/engine/assign/model/$idwf/$idcase/$src_resourceId/$lane_resourceId/$evaluador";

        redirect($url);
    }

    function info($tipo, $idcase) {
        $idwf = 'crefisGral';
        $this->load->model('bpm/bpm');
        $this->load->library('parser');
        $this->load->library('bpm/ui');
        $renderData = array();
        $renderData ['base_url'] = $this->base_url;
        // ---prepare UI
        $renderData ['js'] = array(
            $this->base_url . 'bpm/assets/jscript/modal_window.js' => 'Modal Window Generic JS'
        );
        // ---prepare globals 4 js
        $renderData ['global_js'] = array(
            'base_url' => $this->base_url,
            'module_url' => $this->base_url . 'bpm'
        );
//        $this->bpm->debug['load_case_data'] = true;
        $user = $this->user->getuser((int) $this->session->userdata('iduser'));
        $case = $this->bpm->get_case($idcase, $idwf);
        $this->user->Initiator = $case['iduser'];
        //---saco título para el resultado
        $mywf = $this->bpm->load($idwf);
        $wf = $this->bpm->bindArrayToObject($mywf ['data']);
        //---tomo el template de la tarea
        //$shape = $this->bpm->get_shape($resourceId, $wf);

        $data = $this->bpm->load_case_data($case, $idwf);
        $data['user'] = (array) $user;

        //$resources = $this->bpm->get_resources($shape, $wf, $case);
        //---if has no messageref and noone is assigned then
        //---fire a message to lane or self
//            if (!count($resources['assign']) and !$shape->properties->messageref) {
//                $lane = $this->bpm->find_parent($shape, 'Lane', $wf);
//                //---try to get resources from lane
//                if ($lane) {
//                    $resources = $this->bpm->get_resources($lane, $wf);
//                }
//                //---if can't get resources from lane then assign it self as destinatary
//                if (!count($resources['assign']))
//                    $resources['assign'][] = $this->user->Initiator;
//            }
        //---process inbox--------------

        $renderData['name'] = 'Ingresar Proyecto';
        $renderData['text'] = '';
        $renderData['text'] .= '<hr/>';
//        $renderData['text'] .=nl2br();
        $this->ui->compose('bpm/modal_msg_little', 'bpm/bootstrap.ui.php', $renderData);
    }

    function set_evaluador($idwf, $idcase, $tokenId) {
        $this->load->library('parser');
        $this->load->model('bpm/bpm');
        $this->load->library('bpm/ui');

        $group_name = 'crefis/EVALUADOR TÉCNICO';
        $case = $this->bpm->get_case($idcase, $idwf);
        $renderData = $this->bpm->load_case_data($case, $idwf);
        $mywf = $this->bpm->load($idwf);
        $wf = $this->bpm->bindArrayToObject($mywf ['data']);
        //---tomo el template de la tarea
        $shape = $this->bpm->get_shape('oryx_86F5055B-EF9B-4EB3-A636-F4D8AD782981', $wf);
        //----token que hay que finalizar
        $src_resourceId = 'oryx_86F5055B-EF9B-4EB3-A636-F4D8AD782981';
        // ---Token de pp asignado
        $lane_resourceId = 'oryx_295810F2-8C34-4D03-80F8-7B5C371381B8';
        // ----get evaluadores
        $evaluadores = $this->user->getbygroupname($group_name);
        $url = $this->base_url . "bpm/engine/assign/model/$idwf/$idcase/$src_resourceId/$lane_resourceId";
        $evaluadores = array_map(function ($user) use($url) {
            // var_dump($user);exit;
            $rtn_arr = array(
                'iduser' => $user->idu,
                'name' => $user->name,
                'lastname' => $user->lastname,
                'nick' => $user->nick,
                'url' => $url . '/' . $user->idu
            );
            return $rtn_arr;
        }, $evaluadores);

        $renderData ['title'] = "crefis::Assignar Evaluador";
        $renderData ['name'] = "Assignar Evaluador";
        $renderData ['documentation'] = ($shape->properties->documentation <> '') ? nl2br($this->parser->parse_string($shape->properties->documentation, $renderData, true, true)) : '';
        $renderData ['base_url'] = base_url();
        $renderData ['button'] = $evaluadores;
        // ---prepare UI
        $renderData ['js'] = array(
            $this->base_url . 'bpm/assets/jscript/modal_window.js' => 'Modal Window Generic JS'
        );
        $renderData ['global_js'] = array(
            'base_url' => $this->base_url,
            'module_url' => $this->module_url,
            'idwf' => $idwf,
            'idcase' => $idcase,
            'resourceId' => $src_resourceId
        );

        $this->ui->compose('crefis/get_user', 'bpm/bootstrap.ui.php', $renderData);
    }

    function clone_case($from_idwf, $to_idwf, $idcase) {
        $this->load->model('bpm/bpm');
        $this->load->module('bpm/engine');
        $case = $this->bpm->get_case($idcase, $from_idwf);
        $case_to = $this->bpm->get_case($idcase, $to_idwf);
        if (!$case_to) {

            $this->bpm->gen_case($to_idwf, $idcase);
            $case_to = $this->bpm->get_case($idcase, $to_idwf);
            $case_to['data'] = $case['data'];
            $case_to['iduser'] = $case['iduser'];
            $case_to = $this->bpm->save_case($case_to);
            $this->engine->Startcase('model', $to_idwf, $idcase);
        } else {
            /*
             *   continue case
             */
            $mywf = $this->bpm->load($to_idwf);
            if (!$mywf) {
                show_error("Model referenced:$idwf does not exists");
            }
            $wf = bindArrayToObject($mywf ['data']);
            // ---Get all start points of diagram
            $start_shapes = $this->bpm->get_start_shapes($wf);
            // ----Raise an error if doesn't found any start point
            if (!$start_shapes)
                show_error("The Schema doesn't have an start point");
            // ---Start all StartNoneEvents as possible
            foreach ($start_shapes as $start_shape) {
                $this->bpm->set_token($to_idwf, $idcase, $start_shape->resourceId, $start_shape->stencil->id, 'pending');
            }
            $this->engine->Run('model', $to_idwf, $idcase);
        }

        //----run case
        // Modules::run("bpm/run/model/$to_idwf/$idcase");
    }

    function show_msgs($idcase) {

        $idwfs = array('crefisGral', 'crefisGral');
        foreach ($idwfs as $idwf) {
            $filter = array(
                'idwf' => $idwf,
                'case' => $idcase,
            );

            $title = ($idwf == 'crefisGral') ? "Pre Aprobados" : "Aprobados";

            $cdata = array();
            $cdata['title'] = "Notificaciones (" . $title . "): ";
            echo Modules::run('inbox/show_msgs_by_filter', $filter, $cdata);
        }
    }

    function delegate_case() {

        $this->load->model('crefis_model');
        $idwf = 'crefisGral';
        $idcase = 'XYIK';
        $iduser_dest = -2101255759;

        $update = $this->crefis_model->delegate_case_action($idwf, $idcase, $iduser_dest);
        return $update;
    }

    function widget_2doMe2($chunk = 1, $pagesize = 5) {
        //$data['lang']=$this->lang->language;
        $this->load->model('bpm/bpm');
        $query = array(
            'assign' => $this->idu,
            'status' => 'user'
        );

        //var_dump(json_encode($query));exit;
        $tasks = $this->bpm->get_tasks_byFilter($query, array(), array('checkdate' => 'desc'));
        //$data=$this->prepare_tasks($tasks, $chunk, $pagesize);
        $data = Modules::run('bpm/bpmui/prepare_tasks', $tasks, $chunk, $pagesize);

        if (isset($data['mytasks'])) {
            foreach ($data['mytasks'] as $k => $mytask) {
                $mycase = $this->bpm->get_case($mytask['case']);
                $data['mytasks'][$k]['extra_data']['ip'] = false;
                if (isset($mycase['data']['Empresas']['query']['id'])) {
                    $empresaID = $mycase['data']['Empresas']['query']['id'];
                    $empresa = $this->bpm->get_data('container.empresas', array('id' => $empresaID));
                    $data['mytasks'][$k]['extra_data']['empresa'] = $empresa[0]['1693'];
                }
                if (isset($mycase['data']['Proyectos_crefis']['query']['id'])) {

                    $proyectoID = $mycase['data']['Proyectos_crefis']['query']['id'];
                    $proyecto = $this->bpm->get_data('container.proyectos_crefis', array('id' => $proyectoID));
                    $data['mytasks'][$k]['extra_data']['ip'] = $proyecto[0]['8339'];

                    $url = (isset($mycase['data'] ['Proyectos_crefis']['query']['id'])) ? '../dna2/frontcustom/284/list_docs_crefis_eval.php?id=' . $mycase['data'] ['Proyectos_crefis']['query'] ['id'] : '#';
                    $data['mytasks'][$k]['link_open'] = $this->bpm->gateway($url);

                }
            }
        } else {
            $data['mytasks'] = array();
        }

        $data['title'] = $this->lang->line('Tasks') . ' ' . $this->lang->line('Pending');

        $data['more_info_link'] = $this->base_url . 'bpm/';
        $data['widget_url'] = base_url() . $this->router->fetch_module() . '/' . $this->router->class . '/' . __FUNCTION__;
      echo $this->parser->parse('crefis/widgets/2doMe2', $data, true, true);
    }

}

/* End of file crefis */
    /* Location: ./system/application/controllers/welcome.php */
