<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/**
 * sgr
 *
 */
class Sgr extends MX_Controller {

    function __construct() {
        parent::__construct();
//----habilita acceso a todo los metodos de este controlador
        $this->user->authorize('modules/sgr/controllers/sgr');
        $this->load->config('config');
        $this->load->library('parser');
        $this->load->library('ui');
        $this->load->model('app');
        $this->load->model('user/user');
        $this->load->model('bpm/bpm');
        $this->load->model('user/rbac');
        $this->load->model('sgr/sgr_model');
        $this->load->model('sgr/model_organos_sociales');
        $this->load->helper('sgr/tools');
        $this->load->library('session');




        /* update db */
        $this->load->Model("mysql_model_periods");
        $this->mysql_model_periods->active_periods_dna2();


//---base variables
        $this->base_url = base_url();
        $this->module_url = base_url() . 'sgr/';

//----LOAD LANGUAGE
        $this->lang->load('library', $this->config->item('language'));

// IDU : Chequeo de sesion        



        $this->idu = (float) switch_users($this->session->userdata('iduser'));

        /* bypass session */
        session_start();

        $_SESSION['idu'] = $this->idu;



        if (!$this->idu) {
            header("$this->module_url/user/logout");
            exit();
        }

        /* DATOS SGR */
        $sgrArr = $this->sgr_model->get_sgr();
        foreach ($sgrArr as $sgr) {
            $this->sgr_id = (float) $sgr['id'];
            $this->sgr_nombre = $sgr['1693'];
            $this->sgr_cuit = $sgr['1695'];
        }


        $this->anexo = (isset($this->session->userdata['anexo_code'])) ? $this->session->userdata['anexo_code'] : "06";

        if (isset($this->session->userdata['period']))
            $this->period = $this->session->userdata['period'];

        /* TIME LIMIT */
        set_time_limit(28800);
        ini_set("error_reporting", "E_ALL");
    }

// ==== Dashboard ====
    function Dashboard() {
        $customData = array();
        $customData['sgr_nombre'] = $this->sgr_nombre;
        $customData['sgr_id'] = $this->sgr_id;
        $customData['sgr_id_encode'] = base64_encode($this->sgr_id);
        $customData['base_url'] = base_url();
        $customData['module_url'] = base_url() . 'sgr/';

        $customData['base_url_dna2'] = 'http://' . $_SERVER['HTTP_HOST'] . '/dna2/';

        $customData['titulo'] = "Dashboard";
        $customData['js'] = array($this->module_url . "assets/jscript/dashboard.js" => 'Dashboard JS', $this->module_url . "assets/jscript/jquery-validate/jquery.validate.min_1.js" => 'Validate');
        $customData['css'] = array($this->module_url . "assets/css/dashboard.css" => 'Dashboard CSS');
//$customData['layout']="layout.php"; 

        $sections = array();
        $sections['Anexos'] = array();
        $customData['anexo_list'] = $this->AnexosDB('_blank');

        /* FRE SESSION */
        if (isset($this->session->userdata['fre_session']))
            $customData['fre_session'] = $this->session->userdata['fre_session'];

        $customData['fre_list'] = $this->freDB();
        $customData['is_sgr_sociedades'] = $this->user->has('root/modules/sgr/controllers/sgr/anexo');

        /* ORGANOS SOCIALES */
        $social_structure = $this->model_organos_sociales->get_ident();
        $print_file = anchor('sgr/dna2_social_structure_asset/RenderEdit/' . $social_structure, ' <i class="fa fa-print" alt="Organos Sociales"> Organos Sociales </i>', array('target' => '_blank', 'class' => 'btn btn-primary'));
        $list_files = "<li>" . $print_file . "</li>";
        $customData['social_structure'] = $list_files;
        $customData['social_structure'] = ($this->idu == -342725103) ? $list_files : '';

        /* RENDER */
        $this->render('main_dashboard', $customData);
    }

// ==== Anexos ====
    function Index() {

        $customData = array();
        $default_dashboard = 'dashboard';

        /* HEADERS */
        $header_merge = array_merge($customData, $this->headers());
        foreach ($header_merge as $key => $each) {
            $customData[$key] = $each;
        }



        /* FRE */
        /* fre_session */
        if (isset($this->session->userdata['fre_session']))
            $customData['fre_session'] = $this->session->userdata['fre_session'];

        /* DD.JJ PRESENTATION */
        if ($this->anexo == "17") {
            $default_dashboard = 'dashboard_17';

            $anexo_merge = array_merge($customData, $this->anexo_17());
            foreach ($anexo_merge as $key => $each) {
                $customData[$key] = $each;
            }
        } else {

            /* DEFAULT ANEXOS */
            $anexo_merge = array_merge($customData, $this->anexos_default());
            foreach ($anexo_merge as $key => $each) {
                $customData[$key] = $each;
            }

            $error_set_period = $this->set_period();
            $translate_error = ($this->translate_error_period($error_set_period)) ? : array();
            $rectify_status = ($this->rectify_status()) ? : array();
            $rectify_merge = array_merge($rectify_status, $translate_error);
            foreach ($rectify_merge as $key => $each) {
                $customData[$key] = $each;
            }

// UPLOAD ANEXO
            $upload = $this->upload_file();
            $translate_upload = ($this->translate_upload($upload)) ? $this->translate_upload($upload) : array();
            $upload_status = ($this->upload_status($upload)) ? $this->upload_status($upload) : array();
            $upload_merge = array_merge($translate_upload, $upload_status);
            foreach ($upload_merge as $key => $each) {
                $customData[$key] = $each;
            }
// FILE BROWSER
            $fileBrowserData = $this->file_browser();

//FILE UPLOAD FORM TEMPLATE
            $customData['upload_form_btn'] = ($this->anexo == '09') ? "Subir Archivo PDF" : "Subir Archivo XLS";

            $customData['upload_form_template'] = $this->parser->parse('file_upload_form', $customData, true);

//FORM TEMPLATE
            $customData['form_template'] = $this->parser->parse('form', $customData, true);
        }

//RENDER
        if (!empty($fileBrowserData)) {
            $resultRender = array_replace_recursive($customData, $fileBrowserData);
            $this->render($default_dashboard, $resultRender);
        } else {
            $this->render($default_dashboard, $customData);
        }
    }

    /* ASSETS HEADERS */

    function headers() {
        $rtn = array();

        $rtn['sgr_nombre'] = $this->sgr_nombre;
        $rtn['sgr_id'] = $this->sgr_id;
        $rtn['sgr_id_encode'] = base64_encode($this->sgr_id);
        $rtn['base_url'] = base_url();
        $rtn['module_url'] = base_url() . 'sgr/';
        $rtn['titulo'] = "";
        $rtn['js'] = array($this->module_url . "assets/jscript/dashboard.js" => 'Dashboard JS', $this->module_url . "assets/jscript/jquery-validate/jquery.validate.min_1.js" => 'Validate');
        $rtn['css'] = array($this->module_url . "assets/css/dashboard.css" => 'Dashboard CSS');

        $rtn['anexo'] = $this->anexo;
        $rtn['anexo_title'] = $this->oneAnexoDB($this->anexo);
        $rtn['anexo_title_cap'] = strtoupper($this->oneAnexoDB($this->anexo));
        $rtn['anexo_list'] = $this->AnexosDB();
        $rtn['anexo_short'] = $this->oneAnexoDB_short($this->anexo);

        return $rtn;
    }

    /* RECTIFY FNs */

    function anexos_default() {

        $rtn = array();

        /* TABS */
        $rtn['processed_tab'] = $this->get_processed_tab($this->anexo);
        $rtn['processed_list'] = $this->get_processed($this->anexo);

// RECTIFY LIST
        $rtn['rectified_tab'] = $this->get_rectified_tab($this->anexo);
        $rtn['rectified_list'] = $this->get_rectified($this->anexo);

//RECTIFY
        $rtn['sgr_period'] = $this->period;

// PENDING LIST        
        $rtn['pending_list'] = $this->get_pending($this->anexo, $this->sgr_id);

        return $rtn;
    }

    function anexo_17() {
        $rtn = array();

        $rtn['processed_tab'] = $this->get_processed_17_tab($this->anexo);
        $rtn['processed_list'] = $this->get_processed_17($this->anexo);

        return $rtn;
    }

    /* RECTIFY FNs */

    function rectify_status() {
        $customData = array();
        $customData['rectify_message_template'] = "";
        $customData['rectified_legend'] = $this->get_rectified_legend($this->anexo);
        $customData['rectify_message'] = $this->period;
        if (isset($this->session->userdata['rectify'])) {
            $customData['rectify_message_template'] = $this->parser->parse('rectify', $customData, true);
        }
        return $customData;
    }

    /* UPLOAD FN */

    function upload_status($upload) {
        $customData = array();
        if (!$upload) {
            if (!isset($this->session->userdata['period'])) {
                $customData['message'] = ' <i class="fa fa-info-circle"></i> Para procesar debe seleccionar el periodo a informar.';
                $customData['select_period'] = true;
            }
        }
        return $customData;
    }

    function translate_upload($upload) {
        $customData = array();

        if ($upload['success']) {
            $customData['message'] = $upload['message'];
            $customData['success'] = "success";
            $customData['rectify_message_template'] = "";

            if (!$this->session->userdata['period']) {
                $customData['message'] = $upload['message'] . ' <i class="fa fa-info-circle"></i> Para procesar debe seleccionar el periodo a informar..';
                $customData['select_period'] = true;
            }
        } else {
            $customData['message'] = $upload['message'];
            $customData['success'] = "danger";
        }

        return $customData;
    }

    /* ANEXOS FNs */

    function Anexo_code($parameter) {
        /* BORRO SESSION RECTIFY */

        $this->session->unset_userdata('rectify');
        $this->session->unset_userdata('others');


        $newdata = array('anexo_code' => $parameter);
        $this->session->set_userdata($newdata);
        redirect('/sgr');
    }

    /* FRE */

    function Fre_code($parameter) {

        /* BORRO SESSION RECTIFY */
        $this->session->unset_userdata('rectify');
        $this->session->unset_userdata('others');


        $parameter = str_replace("rEpLaCe", "=", $parameter);

        $newdata = array('fre_session' => $this->idu, 'iduser' => base64_decode($parameter));
        $this->session->set_userdata($newdata);
        redirect('/sgr/dashboard');
    }

    function Exit_fre() {

        $newdata = array('iduser' => $this->session->userdata('fre_session'));
        $this->session->set_userdata($newdata);

        /* CLEAR SESSION */
        $this->session->unset_userdata('fre_session');

        redirect('/sgr/dashboard');
    }

    function AnexosDB($target = '_self') {
        $module_url = base_url() . 'sgr/';
        $anexosArr = $this->sgr_model->get_anexos();
        $result = "";
        foreach ($anexosArr as $anexo) {
            /*
             * FILTER 4 FRE
             * FONDOS DE AFECTACIÓN ESPECÍFICOS, no deben tener la opcion de subir los Anexo 6, ni 6.1 ni 6.2.
             */
            $chunk_id = (int) $anexo['id'];
            $limit_chunk_id = (isset($this->session->userdata['fre_session'])) ? 3 : 0;

            if ($chunk_id > $limit_chunk_id)
                $result .= '<li><a target="' . $target . '" href=  "' . $module_url . 'anexo_code/' . $anexo['number'] . '"> ' . $anexo['title'] . ' <strong>[' . $anexo['short'] . ']</strong></a></li>';
        }
        return $result;
    }

    function freDB($target = '_self') {

        $module_url = base_url() . 'sgr/';
        $anexosArr = $this->sgr_model->get_fre($this->idu);

        $result = null;
        foreach ($anexosArr as $anexo) {

            $crypt = str_replace("=", "rEpLaCe", base64_encode($anexo['id']));

            $result .= '<li>' . $anexo['title'] . ' <a target="' . $target . '" href=  "' . $module_url . 'fre_code/' . $crypt . '"> [SELECCIONAR]</a></li>';
        }

        return $result;
    }

    function oneAnexoDB() {
        $anexoValues = $this->sgr_model->get_anexo($this->anexo);
        return $anexoValues['title'];
    }

    function oneAnexoDB_short() {
        $anexoValues = $this->sgr_model->get_anexo($this->anexo);
        return $anexoValues['short'];
    }

    /*
     * ANEXO PROCESS
     * 
     * Example Usage:

      $data = new Spreadsheet_Excel_Reader("test.xls");

      Retrieve formatted value of cell (first or only sheet):

      $data->val($row,$col)

      Or using column names:

      $data->val(10,'AZ')

      From a sheet other than the first:

      $data->val($row,$col,$sheet_index)

      Retrieve cell info:

      $data->type($row,$col);
      $data->raw($row,$col);
      $data->format($row,$col);
      $data->formatIndex($row,$col);

      Get sheet size:
      $data->rowcount();
      $data->colcount();

      $data->sheets[0]['cells'][$i][$j] - data from $i-row $j-column

      $data->sheets[0]['numRows'] - count rows
      $data->sheets[0]['numCols'] - count columns

      $data->sheets[0]['cellsInfo'][$i][$j] - extended info about cell
      $data->sheets[0]['cellsInfo'][$i][$j]['type'] = "date" | "number" | "unknown"
      $data->sheets[0]['cellsInfo'][$i][$j]['raw'] = value if cell without format
      $data->sheets[0]['cellsInfo'][$i][$j]['format'] = Excel-style Format string of cell
      $data->sheets[0]['cellsInfo'][$i][$j]['formatIndex'] = The internal Excel index of format

      $data->sheets[0]['cellsInfo'][$i][$j]['colspan']
      $data->sheets[0]['cellsInfo'][$i][$j]['rowspan']
     */

    function Anexo($filename = null) {

        $customData = array();
        $customData['base_url'] = base_url();
        $customData['module_url'] = base_url() . 'sgr/';
        $customData['sgr_nombre'] = $this->sgr_nombre;
        $customData['sgr_id'] = $this->sgr_id;

        $customData['js'] = array($this->module_url . "assets/jscript/dashboard.js" => 'Dashboard JS', $this->module_url . "assets/jscript/jquery-validate/jquery.validate.min_1.js" => 'Validate');
        $customData['css'] = array($this->module_url . "assets/css/dashboard.css" => 'Dashboard CSS');

        if (!$filename) {
            exit();
        }
        $process_filename = $filename;


        $filename_ext = ($this->anexo == '09') ? ".pdf" : ".xls";

        $filename = $process_filename . $filename_ext;
        list($sgr, $anexo, $date) = explode("_", $filename);

        if ($sgr != $this->sgr_id) {
            var_dump($sgr, $this->sgr_id);
            exit();
        }
        /* XLS */
        if ($this->anexo != '09') {

            /* PRELIMINAR VALIDATION */
            $VG = $this->pre_general_validation($anexo);

            if ($VG) {
                $customData['anexo_title_cap'] = strtoupper($this->oneAnexoDB($this->anexo));
                $customData['sgr_period'] = $this->period;
                $customData['anexo_list'] = $this->AnexosDB();
                $uploadpath = getcwd() . '/anexos_sgr/' . $filename;
                $customData['message'] = $VG;
                $this->render('errors', $customData);
                unlink($uploadpath);
            } else {
                $this->process($process_filename);
            }
        } else {
            /* PDF */
            $this->pdf($process_filename);
        }
    }

    function Pdf($filename) {
        $customData = array();
        $customData['base_url'] = base_url();
        $customData['module_url'] = base_url() . 'sgr/';
        $customData['sgr_nombre'] = $this->sgr_nombre;
        $customData['sgr_id'] = $this->sgr_id;
        $customData['js'] = array($this->module_url . "assets/jscript/dashboard.js" => 'Dashboard JS', $this->module_url . "assets/jscript/jquery-validate/jquery.validate.min_1.js" => 'Validate');
        $customData['css'] = array($this->module_url . "assets/css/dashboard.css" => 'Dashboard CSS');


        $filename_ext = ($this->anexo == '09') ? ".pdf" : ".xls";
        $filename = $filename . $filename_ext;
        list($sgr, $anexo, $date) = explode("_", $filename);

        if ($sgr != $this->sgr_id) {
            var_dump($sgr, $this->sgr_id);
            exit();
        }



        $original = array($this->sgr_id . '_', $this->anexo . '_', '_');
        $replaced = array("Anexo " . $this->oneAnexoDB_short($this->anexo) . ' - ', strtoupper($this->sgr_nombre) . ' - ', ' ');
        $new_filename = str_replace($original, $replaced, $filename);


        $uploadpath = getcwd() . '/anexos_sgr/' . $filename;
        $movepath = getcwd() . '/anexos_sgr/' . $anexo . '/' . $new_filename;





        if (!$error) {
            $model = "model_" . $anexo;
            $this->load->Model($model);

            /* INSERT UPDATE */
            $result = array();
            $result['filename'] = $new_filename;
            $result['sgr_id'] = $this->sgr_id;
            $save = (array) $this->$model->save($result);


            /* SET PERIOD */
            if ($save) {
                $result = array();
                $result['filename'] = $new_filename;
                $result['sgr_id'] = $this->sgr_id;
                $result['anexo'] = $this->anexo;
                $save_period = (array) $this->$model->save_period($result);


                if ($save_period['status'] == "ok") {
                    /* RENDER */
                    $customData['anexo_title_cap'] = strtoupper($this->oneAnexoDB($this->anexo));
                    $customData['sgr_period'] = $this->period;
                    $customData['anexo_list'] = $this->AnexosDB();
                    $customData['print_file'] = anchor('/sgr/pdf_asset/09/' . $new_filename, ' <i class="fa fa-print" alt="Imprimir"> Imprimir PDF </i>', array('target' => '_blank', 'class' => 'btn btn-primary')) . '</li>';
                    $customData['message'] = '<li>El Archivo (' . $new_filename . ') fue importado con exito</li>';
                    $this->render('success', $customData);
//$this->parser->parse('success2', $customData);
                    copy($uploadpath, $movepath) or die("Unable to copy $uploadpath to $movepath.");
                    unlink($uploadpath);
                } else {
                    $error = 4;
                }
            }
        }
    }

    function Process($filename) {
        $customData = array();
        $customData['base_url'] = base_url();
        $customData['module_url'] = base_url() . 'sgr/';
        $customData['sgr_nombre'] = $this->sgr_nombre;
        $customData['sgr_id'] = $this->sgr_id;

        $customData['js'] = array($this->module_url . "assets/jscript/dashboard.js" => 'Dashboard JS', $this->module_url . "assets/jscript/jquery-validate/jquery.validate.min_1.js" => 'Validate');
        $customData['css'] = array($this->module_url . "assets/css/dashboard.css" => 'Dashboard CSS');


        $filename_ext = ($this->anexo == '09') ? ".pdf" : ".xls";
        $filename = $filename . $filename_ext;
        list($sgr, $anexo, $date) = explode("_", $filename);

        if ($sgr != $this->sgr_id) {
            var_dump($sgr, $this->sgr_id);
            exit();
        }


//echo dirname(__FILE__); //$this->module_url;

        $original = array($this->sgr_id . '_', $this->anexo . '_', '_');
        $replaced = array("Anexo " . $this->oneAnexoDB_short($this->anexo) . ' - ', strtoupper($this->sgr_nombre) . ' - ', ' ');
        $new_filename = str_replace($original, $replaced, $filename);


        $uploadpath = getcwd() . '/anexos_sgr/' . $filename;
        $movepath = getcwd() . '/anexos_sgr/' . $anexo . '/' . $new_filename;

        $this->load->library('excel_reader2');
        $data = new Excel_reader2($uploadpath);

        $stack = array();
        $fields = "";
        $result = "";
        $result_header = "";
        $error = false;
        $headerArr = array();
        $valuesArr = array();
        for ($index = 1; $index <= $data->sheets[0]['numCols']; $index++) {
            $headerArr[] = $data->sheets[0]['cells'][1][$index];
        }

        $header = "lib_" . $anexo . "_header";
        $result_head = (array) $this->load->library("validators/" . $header, $headerArr);

        /* COLUMN HEADER ERROR */

        if (!$result_head['result']) {
            for ($i = 2; $i <= $data->sheets[0]['numRows']; $i++) {
                $data_sheets_cells = $data->sheets[0]['cells'][$i];

                /* CHECK FOR EMPTY ROWS */
                if (isset($data_sheets_cells))
                    $row_count = implode($data_sheets_cells);

                $row_lenght = strlen($row_count);
                for ($j = 1; $j <= $data->sheets[0]['numCols']; $j++) {
                    if ($row_lenght > 1) {
                        $count = $data->rowcount();

                        $fields = (isset($data->sheets[0]['cells'][$i][$j])) ? trim($data->sheets[0]['cells'][$i][$j]) : NULL;

                        $stack = array('fieldValue' => $fields, "row" => $i, "col" => $j, "count" => $count);
                        array_push($valuesArr, $stack);
                    }
                }
            }

            /* VALIDATIONS */
            if (!$count) {
                $result_header = $this->empty_xls_advice($this->anexo);
                $error = 1;
            }



            /* XLS CELL DATA ERROR */
            $data_values = "lib_" . $anexo . "_data";
            $lib_error = "lib_" . $anexo . "_error_legend";
            $this->load->library("validators/" . $lib_error);
            $get_data = (array) $this->load->library("validators/" . $data_values, $valuesArr);




            foreach ($get_data['data'] as $result_data) {

                if (!empty($result_data['error_code'])) {
                    $error_input_value = ($result_data['error_input_value'] != "") ? " <br>Valor Ingresado:<strong>“" . $result_data['error_input_value'] . "”</strong>" : "";

                    if ($result_data['error_input_value'] == "empty") {
                        list($column_value) = explode(".", $result_data['error_code']);
                        $result .= '<li><strong>Columna ' . $column_value . ' - Fila Nro.' . $result_data['error_row'] . ' - Código Validación ' . $result_data['error_code'] . '</strong><br/>El campo no puede estar vacío.</li>';
                    } else {
                        $result .= "<li>" . $this->$lib_error->return_legend($result_data['error_code'], $result_data['error_row'], $result_data['error_input_value']) . $error_input_value . "</li>";
                    }

                    $error = 2;
                }
            }
        } else {
//ERROR    
            $result_header_desc = "<h3>El modelo de Anexo es incorrecto, restan las siguientes columnas</h3>";
            foreach ($result_head['result'] as $error_head) {
                $result_header .= "<li>" . $error_head . "</li>";
                $error = true;
            }
            $result_header = $result_header_desc . $result_header;
            $error = 3;
        }



        if (!$error) {
            $model = "model_" . $anexo;
            $this->load->Model($model);

            for ($i = 2; $i <= $data->rowcount(); $i++)
                $sanitize_data = $this->$model->sanitize($data->sheets[0]['cells'][$i]);


            /* INSERT UPDATE */
            for ($i = 2; $i <= $data->rowcount(); $i++) {
                if (!empty($data->sheets[0]['cells'][$i][1])) {
                    $result = (array) $this->$model->check($data->sheets[0]['cells'][$i]);
                    $result['filename'] = $new_filename;
                    $result['sgr_id'] = $this->sgr_id;
                    $save = (array) $this->$model->save($result);
                }
            }

            /* SET PERIOD */
            if ($save) {
                $result = array();
                $result['filename'] = $new_filename;
                $result['sgr_id'] = $this->sgr_id;
                $result['anexo'] = $this->anexo;
                $save_period = (array) $this->$model->save_period($result);


                if (isset($save_period['status']) == "ok") {
                    /* RENDER */
                    $customData['anexo_title_cap'] = strtoupper($this->oneAnexoDB($this->anexo));
                    $customData['sgr_period'] = $this->period;
                    $customData['anexo_list'] = $this->AnexosDB();
                    $custo_Data['process_filename'] = $new_filename;
                    $customData['print_file'] = anchor('/sgr/print_anexo/' . $new_filename, ' <i class="fa fa-print" alt="Imprimir"> Imprimir Anexo </i>', array('target' => '_blank', 'class' => 'btn btn-primary')) . '</li>';
                    $customData['message'] = '<li>El Archivo (' . $new_filename . ') fue importado con exito</li>';
                    $this->render('success', $customData);
//$this->parser->parse('success2', $customData);
                    copy($uploadpath, $movepath) or die("Unable to copy $uploadpath to $movepath.");
                    unlink($uploadpath);
                } else {
                    $error = 4;
                }
            }
        }


        /* ERROR CASE */
        if ($error) {
            $customData['anexo_title_cap'] = strtoupper($this->oneAnexoDB($this->anexo));
            $customData['sgr_period'] = $this->period;
            $customData['anexo_list'] = $this->AnexosDB();
            $customData['message_header'] = $result_header;


            if (strlen($result) > 100000)
                $result = substr($result, 0, 100000) . "...";

            $customData['message'] = $result;

            $this->render('errors', $customData);


            /*
             * 4 FOR TEST PURPOSES ONLY
             * 
              if($_SESSION['idu']==-338563259)
              exit(); */


            unlink($uploadpath);
        }



        /*
          /* consulta
          $config['hostname'] = "localhost";
          $config['username'] = "root";
          $config['password'] = "root";
          $config['database'] = "forms2";
          $config['dbdriver'] = "mysql";
          $config['dbprefix'] = "";
          $config['pconnect'] = FALSE;
          $config['db_debug'] = TRUE;
          $config['cache_on'] = FALSE;
          $config['cachedir'] = "";
          $config['char_set'] = "utf8";
          $config['dbcollat'] = "utf8_general_ci";
          $db = $this->load->database($config, true, false);
         */
        /*
         * Mysql Query & Insert
         * 
         * $SQL = "SELECT * FROM `" . $this->oneAnexoDB($this->anexo) . "` WHERE `cuit_sgr` = '30-70937729-5'";
          $DB_forms2 = $db;
          $query = $DB_forms2->query($SQL);

          if ($query->num_rows() > 0) {
          foreach ($query->result() as $row) {
          //  var_dump($row);
          }
          }


          $sql = "INSERT INTO $table (";
          for ($index = 1; $index <= $data->sheets[0]['numCols']; $index++) {
          $sql.= strtolower($data->sheets[0]['cells'][1][$index]) . ", ";
          }

          $sql = rtrim($sql, ", ") . " ) VALUES ( ";
          for ($i = 2; $i <= $data->sheets[0]['numRows']; $i++) {
          $valuesSQL = '';
          for ($j = 1; $j <= $data->sheets[0]['numCols']; $j++) {
          $valuesSql .= "\"" . $data->sheets[0]['cells'][$i][$j] . "\", ";
          }
          echo $sql . rtrim($valuesSql, ", ") . " ) <br>";
          } */
    }

    function empty_xls_advice($anexo) {
        switch ($anexo) {
            case 06:
                $msg = "El campo no puede estar vacío, y debe contener alguno de los siguientes parámetros : INCORPORACION, INCREMENTO TENENCIA ACCIONARIA o DISMINUCION DE CAPITAL SOCIAL";
                break;
            case 061:
                $msg = "El campo no puede estar vacío y debe tener 11 caracteres sin guiones.";
                break;
            case 062:
                $msg = "Debe tener 11 caracteres numéricos sin guiones.";
                break;
        }

        $legend = '<li><i class="fa fa-info-circle"></i> Error archivo no tiene la informacion necesaria</li><li>' . $msg . '</li>';
        return $legend;
    }

    function translate_error_period($error_set_period) {
        if ($error_set_period) {

            switch ($error_set_period) {
                case "1":
                    $error_legend = ($this->anexo == "06") ? " El Periodo seleccionado es Invalido o tiene un Anexo pendiente." : "El Periodo seleccionado es Invalido.";
                    $error_msg = '<i class="fa fa-info-circle"></i> ' . $error_legend;
                    break;

                case "2":
                    $error_msg = '<i class="fa fa-info-circle"></i> El Periodo a informar no puede ser anterior a 01/2014';
                    break;

                default:
                    $new_period = anchor('sgr', 'Volver <i class="fa fa-external-link" alt="Volver"></i>');
                    $get_period = $this->sgr_model->get_current_period_info($this->anexo, $error_set_period);
                    $error_msg = '<i class="fa fa-info-circle"></i> El periodo del ' . str_replace('-', '/', $error_set_period) . ' ya fue informado [ ' . $get_period['filename'] . ' ] | ' . $new_period;
                    $customData['post_period'] = $error_set_period;
                    $customData['rectifica'] = true;
                    $customData['js'] = $link_arr = array($this->module_url . "assets/jscript/rectify.js" => 'Rectify JS');

                    if (!in_array($link_arr, $customData)) {
                        array_push($customData['js'], $link_arr);
                    }
                    break;
            }
            $customData['period_message'] = $error_msg;
            $customData['success'] = "danger";

            return $customData;
        } else {
            return false;
        }
    }

    function print_motionless($parameter = null) {
        if (!$parameter)
            exit();

        $anexo = ($this->session->userdata['anexo_code']) ? : '06';
        $customData = array();
        $customData['sgr_nombre'] = $this->sgr_nombre;
        $customData['sgr_cuit'] = $this->sgr_cuit;
        $customData['sgr_id'] = $this->sgr_id;
        $customData['sgr_id_encode'] = base64_encode($this->sgr_id);
        $customData['base_url'] = base_url();


        $customData['module_url'] = base_url() . 'sgr/';
        $customData['logo'] = "http://" . $_SERVER['HTTP_HOST'] . "/dna2bpm/sgr/assets/images/orgullo.jpg"; //$this->module_url."/assets/images/orgullo.jpg";
        $customData['parameter'] = "SIN MOVIMIENTO";

        $customData['anexo_short'] = $this->oneAnexoDB_short($this->anexo);
        $customData['anexo'] = $this->anexo;
        $customData['anexo_title'] = $this->oneAnexoDB($this->anexo);
        $customData['anexo_title_cap'] = strtoupper($this->oneAnexoDB($this->anexo));

        $customData['print_period'] = $parameter;
        /* PRINT ANEXO */
        $tepmplate_print = "motionless_print";


        $this->$tepmplate_print($anexo, $customData, $parameter);
    }

    function print_anexo($parameter = null) {
        if (!$parameter)
            exit();

        $parameter = urldecode($parameter);

        $anexo = ($this->session->userdata['anexo_code']) ? : '06';
        $model = "model_" . $anexo;
        $this->load->model($model);


        $customData = array();
        $customData['sgr_nombre'] = $this->sgr_nombre;
        $customData['sgr_cuit'] = $this->sgr_cuit;
        $customData['sgr_id'] = $this->sgr_id;
        $customData['sgr_id_encode'] = base64_encode($this->sgr_id);
        $customData['base_url'] = base_url();


        $customData['module_url'] = base_url() . 'sgr/';
        $customData['logo'] = "http://" . $_SERVER['HTTP_HOST'] . "/dna2bpm/sgr/assets/images/orgullo.jpg"; //$this->module_url."/assets/images/orgullo.jpg";
        $customData['parameter'] = urldecode($parameter);
        $customData['anexo_short'] = $this->oneAnexoDB_short($this->anexo);

        $customData['anexo'] = $this->anexo;
        $customData['anexo_title'] = $this->oneAnexoDB($this->anexo);
        $customData['anexo_title_cap'] = strtoupper($this->oneAnexoDB($this->anexo));

        /* PERIOD INFO */
        $get_period_info = $this->sgr_model->get_period_filename($parameter);

        $user = $this->user->get_user($get_period_info['idu']);

        $customData['user_print'] = strtoupper($user->lastname . ", " . $user->name);
        $customData['print_period'] = str_replace("-", "/", $get_period_info['period']);
        $customData['show_table'] = html_entity_decode($this->$model->get_anexo_info($this->anexo, $parameter));
        if ($anexo == '06') {
            $customData['show_footer'] = $this->$model->get_anexo_footer($this->anexo, $parameter);
        }


        /* PRINT ANEXO */
        $tepmplate_print = "stream_print";
        $this->$tepmplate_print($anexo, $customData, $parameter);
    }

    function stream_print($anexo, $customData, $parameter) {

        /* Print on HTML */
        $no_pdf = array('12', '123');

        if (in_array($anexo, $no_pdf)) {
            echo $this->parser->parse('print', $customData, true);
        } else {
            /* LOAD LIBRARY */
            $this->load->library('pdf/pdf');

            $this->pdf->set_paper('a4', 'landscape');
            $this->pdf->parse('print', $customData);
            $this->pdf->render();
            $this->pdf->stream("$parameter.pdf");
        }
    }

    /* SIN MOVIMIENTO PRINT */

    function motionless_print($anexo, $customData, $parameter) {

        $filename = "Anexo" . $anexo . "_periodo_" . $customData['print_period'] . "_SIN_MOVIMIENTO";
        /* LOAD LIBRARY */
        $this->load->library('pdf/pdf');

        $this->pdf->set_paper('a4', 'landscape');
        $this->pdf->parse('print_motionless', $customData);
        $this->pdf->render();
        $this->pdf->stream("$filename.pdf");
    }

    function print_xls($parameter = null) {

        if (!isset($parameter)) {
            exit();
        }
        $parameter = urldecode($parameter);

        if ($parameter == 'SIN MOVIMIENTOS')
            redirect('/sgr');

        $anexo = ($this->session->userdata['anexo_code']) ? : '06';
        $model = "model_" . $anexo;
        $this->load->model($model);


        $customData = array();
        $customData['sgr_nombre'] = $this->sgr_nombre;
        $customData['sgr_cuit'] = $this->sgr_cuit;
        $customData['sgr_id'] = $this->sgr_id;
        $customData['sgr_id_encode'] = base64_encode($this->sgr_id);
        $customData['base_url'] = base_url();
        $customData['module_url'] = base_url() . 'sgr/';
        $customData['parameter'] = urldecode($parameter);
        $customData['anexo_short'] = $this->oneAnexoDB_short($this->anexo);

        $customData['anexo'] = $this->anexo;
        $customData['anexo_title'] = $this->oneAnexoDB($this->anexo);
        $customData['anexo_title_cap'] = strtoupper($this->oneAnexoDB($this->anexo));

        /* PERIOD INFO */
        $get_period_info = $this->sgr_model->get_period_filename($parameter);

        $user = $this->user->get_user($get_period_info['idu']);

        $customData['user_print'] = strtoupper($user->lastname . ", " . $user->name);
        $customData['print_period'] = str_replace("-", "/", $get_period_info['period']);
        $get_anexo = $this->$model->get_anexo_info($this->anexo, $parameter, true);
        $customData['show_table'] = utf8_decode($get_anexo);

        echo $this->parser->parse('print_to_xls', $customData, true);
    }

    function print_ddjj($parameter = null) {


        if (!isset($parameter)) {
            exit();
        }


        $parameter = urldecode($parameter);
        $anexo = ($this->session->userdata['anexo_code']) ? : '06';
        $model = "model_" . $anexo;
        $this->load->model($model);
        //----Load pdf lib
        $this->load->library('pdf/pdf');
        $customData = array();
        $customData['sgr_nombre'] = $this->sgr_nombre;
        $customData['sgr_cuit'] = $this->sgr_cuit;
        $customData['sgr_id'] = $this->sgr_id;
        $customData['sgr_id_encode'] = base64_encode($this->sgr_id);
        $customData['base_url'] = base_url();
        $customData['module_url'] = base_url() . 'sgr/';
        $customData['parameter'] = urldecode($parameter);
        $customData['anexo_short'] = $this->oneAnexoDB_short($this->anexo);

        $customData['anexo'] = $this->anexo;
        $customData['anexo_title'] = $this->oneAnexoDB($this->anexo);
        $customData['anexo_title_cap'] = strtoupper($this->oneAnexoDB($this->anexo));

        /* PERIOD INFO */
        $get_period_info = $this->sgr_model->get_period_filename($parameter);

        $user = $this->user->get_user($get_period_info['idu']);

        $customData['user_print'] = strtoupper($user->lastname . ", " . $user->name);
        $customData['print_period'] = $parameter;

        /* POST */
        $customData['comisions'] = $this->input->post("comisions");
        $customData['observations'] = $this->input->post("observations");
        $period_req = $this->input->post("period");

        /* FILENAMES */
        $anexos_arr = array("12", "06", "13", "16", "15", "14");
        $filenames_arr = array("12", "121", "122", "123", "124", "125", "13", "14", "141", "15", "16");
        foreach ($filenames_arr as $each) {
            $get_anexo = $this->sgr_model->get_period_data($each, $parameter, true);
            $customData['f_' . $each] = $get_anexo[0]['filename'];
        }

        /* DD.JJ DATA */
        foreach ($anexos_arr as $anexo_req) {
            $get_ddjj_data = $this->ddjj_data($anexo_req, $period_req);

            foreach ($get_ddjj_data as $key => $each) {

                if ($each == "")
                    $each = "-";

                if ($each == "$")
                    $each = "$0.00";

                $customData[$key] = $each;
            }
        }
        $this->pdf->set_paper('A4', 'landscape');
        if ($period_req) {

            /* SAVE DD.JJ */
            $model = "model_" . $anexo;
            $this->load->model($model);

            $save = (array) $this->$model->save($customData);


            $result = array();
            $result['period'] = $parameter;
            $result['filename'] = $save['status'];
            $result['sgr_id'] = $this->sgr_id;
            $result['anexo'] = $anexo;


            $save_period = (array) $this->$model->save_period($result);


            /* PRINT DD.JJ PDF MODE */
            $this->pdf->parse('print_ddjj', $customData);
            $this->pdf->render();
            $this->pdf->stream("$parameter.pdf");
        } else {

            /* PRINT DD.JJ FORM */
            echo $this->parser->parse('print_ddjj_form', $customData, true);
        }
    }

    function ddjj_data($anexo_req, $period_req) {

        $model = "model_" . $anexo_req;
        $this->load->Model($model);

        $model124 = "model_124";
        $this->load->Model($model124);

        $model141 = "model_141";
        $this->load->Model($model141);


        $comisions = $this->input->post("comisions");
        switch ($anexo_req) {

            case '14':
                $t4_1 = $this->$model141->partners_debtors_to_top($period_req);
                $t4_2 = $this->$model141->get_anexo_ddjj($period_req);


                $t4_3 = $this->$model->nums_guarantees_faced($period_req, "CAIDA");
                $t4_4 = $this->$model->amount_guarantees_faced($period_req, "CAIDA");
                $t4_5 = $this->$model->nums_guarantees_faced($period_req, "RECUPERO");
                $t4_6 = $this->$model->amount_guarantees_faced($period_req, "RECUPERO");
                $t4_7 = $this->$model->nums_guarantees_faced($period_req, "INCOBRABLES_PERIODO");
                $t4_8 = $this->$model->amount_guarantees_faced($period_req, "INCOBRABLES_PERIODO");

                $t4_9 = $this->$model->nums_guarantees_faced($period_req, "GASTOS_EFECTUADOS_PERIODO");
                $t4_10 = $this->$model->amount_guarantees_faced($period_req, "GASTOS_EFECTUADOS_PERIODO");
                $t4_11 = $this->$model->nums_guarantees_faced($period_req, "RECUPERO_GASTOS_PERIODO");
                $t4_12 = $this->$model->amount_guarantees_faced($period_req, "RECUPERO_GASTOS_PERIODO");
                $t4_13 = $this->$model->nums_guarantees_faced($period_req, "GASTOS_INCOBRABLES_PERIODO");
                $t4_14 = $this->$model->amount_guarantees_faced($period_req, "GASTOS_INCOBRABLES_PERIODO");

                $t4_16 = $this->$model141->partners_debtors_to_end($period_req);

                $rtn['t4_1'] = $t4_1;
                $rtn['t4_2'] = money_format_custom(0);

                $rtn['t4_3'] = $t4_3;
                $rtn['t4_4'] = money_format_custom($t4_4);
                $rtn['t4_5'] = $t4_5;
                $rtn['t4_6'] = money_format_custom($t4_6);
                $rtn['t4_7'] = $t4_7;
                $rtn['t4_8'] = money_format_custom($t4_8);
                $rtn['t4_9'] = $t4_9;
                $rtn['t4_10'] = money_format_custom($t4_10);

                $rtn['t4_11'] = $t4_11;
                $rtn['t4_12'] = money_format_custom($t4_12);
                $rtn['t4_13'] = $t4_13;
                $rtn['t4_14'] = money_format_custom($t4_14);

                $rtn['t4_15'] = money_format_custom(0);
                $rtn['t4_16'] = $t4_16;


                return $rtn;

                break;

            case '15':
                $result_15 = $this->$model->get_anexo_ddjj($period_req, "A");

                foreach ($result_15 as $r15) {
                    $t5_1 = $r15['col1'];
                    $t5_2 = $r15['col2'];
                    $t5_3 = $r15['col3'];
                    $t5_4 = $r15['col4'];
                }

                $result_15 = $this->$model->get_anexo_ddjj($period_req, "B");
                foreach ($result_15 as $r15) {

                    $t5_5 = $r15['col1'];
                    $t5_6 = $r15['col2'];
                    $t5_7 = $r15['col3'];
                    $t5_8 = $r15['col4'];
                }

                $result_15 = $this->$model->get_anexo_ddjj($period_req, "C");
                foreach ($result_15 as $r15) {

                    $t5_9 = $r15['col1'];
                    $t5_10 = $r15['col2'];
                    $t5_11 = $r15['col3'];
                    $t5_12 = $r15['col4'];
                }

                $result_15 = $this->$model->get_anexo_ddjj($period_req, "D");
                foreach ($result_15 as $r15) {

                    $t5_13 = $r15['col1'];
                    $t5_14 = $r15['col2'];
                    $t5_15 = $r15['col3'];
                    $t5_16 = $r15['col4'];
                }

                $result_15 = $this->$model->get_anexo_ddjj($period_req, "E");
                foreach ($result_15 as $r15) {
                    $t5_17 = $r15['col1'];
                    $t5_18 = $r15['col2'];
                    $t5_19 = $r15['col3'];
                    $t5_20 = $r15['col4'];
                }

                $result_15 = $this->$model->get_anexo_ddjj($period_req, "F");
                foreach ($result_15 as $r15) {



                    $t5_21 = $r15['col1'];
                    $t5_22 = $r15['col2'];
                    $t5_23 = $r15['col3'];
                    $t5_24 = $r15['col4'];
                }

                $result_15 = $this->$model->get_anexo_ddjj($period_req, "G");
                foreach ($result_15 as $r15) {

                    $t5_25 = $r15['col1'];
                    $t5_26 = $r15['col2'];
                    $t5_27 = $r15['col3'];
                    $t5_28 = $r15['col4'];
                }

                $result_15 = $this->$model->get_anexo_ddjj($period_req, "H");
                foreach ($result_15 as $r15) {


                    $t5_29 = $r15['col1'];
                    $t5_30 = $r15['col2'];
                    $t5_31 = $r15['col3'];
                    $t5_32 = $r15['col4'];
                }

                $result_15 = $this->$model->get_anexo_ddjj($period_req, "I");
                foreach ($result_15 as $r15) {

                    $t5_33 = $r15['col1'];
                    $t5_34 = $r15['col2'];
                    $t5_35 = $r15['col3'];
                    $t5_36 = $r15['col4'];
                }

                $result_15 = $this->$model->get_anexo_ddjj($period_req, "J");
                foreach ($result_15 as $r15) {

                    $t5_37 = $r15['col1'];
                    $t5_38 = $r15['col2'];
                    $t5_39 = $r15['col3'];
                    $t5_40 = $r15['col4'];
                }
                $result_15 = $this->$model->get_anexo_ddjj($period_req, "K");
                foreach ($result_15 as $r15) {

                    $t5_41 = $r15['col1'];
                    $t5_42 = $r15['col2'];
                    $t5_43 = $r15['col3'];
                    $t5_44 = $r15['col4'];
                }



                $rtn['t5_1'] = money_format_custom($t5_1);
                $rtn['t5_2'] = money_format_custom($t5_2);
                $rtn['t5_3'] = money_format_custom($t5_3);
                $rtn['t5_4'] = percent_format_custom($t5_4);

                $rtn['t5_5'] = money_format_custom($t5_5);
                $rtn['t5_6'] = money_format_custom($t5_6);
                $rtn['t5_7'] = money_format_custom($t5_7);
                $rtn['t5_8'] = percent_format_custom($t5_8);

                $rtn['t5_9'] = money_format_custom($t5_9);
                $rtn['t5_10'] = money_format_custom($t5_10);
                $rtn['t5_11'] = money_format_custom($t5_11);
                $rtn['t5_12'] = percent_format_custom($t5_12);

                $rtn['t5_13'] = money_format_custom($t5_13);
                $rtn['t5_14'] = money_format_custom($t5_14);
                $rtn['t5_15'] = money_format_custom($t5_15);
                $rtn['t5_16'] = percent_format_custom($t5_16);

                $rtn['t5_17'] = money_format_custom($t5_17);
                $rtn['t5_18'] = money_format_custom($t5_18);
                $rtn['t5_19'] = money_format_custom($t5_19);
                $rtn['t5_20'] = percent_format_custom($t5_20);

                $rtn['t5_21'] = money_format_custom($t5_21);
                $rtn['t5_22'] = money_format_custom($t5_22);
                $rtn['t5_23'] = money_format_custom($t5_23);
                $rtn['t5_24'] = percent_format_custom($t5_24);

                $rtn['t5_25'] = money_format_custom($t5_25);
                $rtn['t5_26'] = money_format_custom($t5_26);
                $rtn['t5_27'] = money_format_custom($t5_27);
                $rtn['t5_28'] = percent_format_custom($t5_28);

                $rtn['t5_29'] = money_format_custom($t5_29);
                $rtn['t5_30'] = money_format_custom($t5_30);
                $rtn['t5_31'] = money_format_custom($t5_31);
                $rtn['t5_32'] = percent_format_custom($t5_32);

                $rtn['t5_33'] = money_format_custom($t5_33);
                $rtn['t5_34'] = money_format_custom($t5_34);
                $rtn['t5_35'] = money_format_custom($t5_35);
                $rtn['t5_36'] = percent_format_custom($t5_36);

                $rtn['t5_37'] = money_format_custom($t5_37);
                $rtn['t5_38'] = money_format_custom($t5_38);
                $rtn['t5_39'] = money_format_custom($t5_39);
                $rtn['t5_40'] = percent_format_custom($t5_40);

                $rtn['t5_41'] = money_format_custom($t5_41);
                $rtn['t5_42'] = money_format_custom($t5_42);
                $rtn['t5_43'] = money_format_custom($t5_43);
                $rtn['t5_44'] = percent_format_custom($t5_44);
                $total_pesos = array_sum(array($t5_1, $t5_5, $t5_9, $t5_13, $t5_17, $t5_21, $t5_25, $t5_29, $t5_33, $t5_37, $t5_41));
                $total_dolar = array_sum(array($t5_2, $t5_6, $t5_10, $t5_14, $t5_18, $t5_22, $t5_26, $t5_30, $t5_34, $t5_38, $t5_42));
                $total = array_sum(array($t5_3, $t5_7, $t5_11, $t5_15, $t5_19, $t5_23, $t5_27, $t5_31, $t5_35, $t5_39, $t5_43));

                $rtn['t5_45'] = money_format_custom($total_pesos);
                $rtn['t5_46'] = money_format_custom($total_dolar);
                $rtn['t5_47'] = money_format_custom($total);
                $rtn['t5_48'] = percent_format_custom(100);

                return $rtn;
                break;

            case '16':
                $result_16 = $this->$model->get_anexo_ddjj($period_req);

                $t6_1 = $result_16[0]['col2'];
                $t6_2 = $result_16[0]['col9'];
                $t6_3 = $result_16[0]['col10'];
                $t6_4 = $result_16[0]['col11'];
                $t6_5 = $result_16[0]['col12'];
                $t6_6 = $result_16[0]['col13'];
                $t6_7 = $result_16[0]['col14'];
                $t6_8 = $result_16[0]['col15'];
                $t6_9 = $result_16[0]['col16'];

                $rtn['t6_1'] = $t6_1;
                $rtn['t6_2'] = $t6_2;
                $rtn['t6_3'] = $t6_3;
                $rtn['t6_4'] = $t6_4;
                $rtn['t6_5'] = $t6_5;
                $rtn['t6_6'] = $t6_6;
                $rtn['t6_7'] = $t6_7;
                $rtn['t6_8'] = $t6_8;
                $rtn['t6_9'] = $t6_9;

                return $rtn;

                break;

            case '13':

                $t3_1 = $this->$model->get_amount_total($period_req, "MENOR_90_DIAS");
                $t3_2 = $this->$model->get_amount_total($period_req, "MENOR_180_DIAS");
                $t3_3 = $this->$model->get_amount_total($period_req, "MENOR_365_DIAS");
                $t3_4 = $this->$model->get_amount_total($period_req, "MAYOR_365_DIAS");

                $sum_totales = array_sum(array($t3_1, $t3_2, $t3_3, $t3_4));

                $t3_5 = $sum_totales;
                $t3_6 = $this->$model->get_amount_total($period_req, "VALOR_CONTRAGARANTIAS");

                $rtn['t3_1'] = money_format_custom($t3_1);
                $rtn['t3_2'] = money_format_custom($t3_2);
                $rtn['t3_3'] = money_format_custom($t3_3);
                $rtn['t3_4'] = money_format_custom($t3_4);
                $rtn['t3_5'] = money_format_custom($t3_5);
                $rtn['t3_6'] = money_format_custom($t3_6);

                return $rtn;
                break;

            case '12':
                $t2_1 = $this->$model->get_assisted_pymes($period_req);
                $t2_2 = $this->$model->get_amount_granted_qty($period_req);
                $t2_3 = $this->$model->get_amount_granted($period_req);


                $t2_8 = $this->$model124->get_warranty_qty($period_req);
                $t2_9 = $this->$model124->get_warranty_amount($period_req);


                $rtn['t2_1'] = $t2_1;
                $rtn['t2_2'] = $t2_2;
                $rtn['t2_3'] = money_format_custom($t2_3);
                $rtn['t2_4'] = $comisions;
                $rtn['t2_5'] = 0;
                $rtn['t2_6'] = 0;
                $rtn['t2_7'] = 0;
                $rtn['t2_8'] = $t2_8;
                $rtn['t2_9'] = money_format_custom($t2_9);


                return $rtn;

                break;

            case '06':

                /* CANTIDAD SOCIOS */
                $t1_1 = $this->$model->balance_count_before($period_req, "A");
                $t1_13 = $this->$model->balance_count_before($period_req, "B");
                $t1_25 = $t1_1 + $t1_13;

                $t1_2 = $this->$model->incorporated_count($period_req, "A");
                $t1_3 = $this->$model->detached_count($period_req, "A");

                $t1_14 = $this->$model->incorporated_count($period_req, "B");
                $t1_15 = $this->$model->detached_count($period_req, "B");

                $t1_4 = ($t1_1 + $t1_2) - $t1_3;
                $t1_16 = ($t1_13 + $t1_14) - $t1_15;

                $t1_26 = $t1_2 + $t1_14;
                $t1_27 = $t1_3 + $t1_15;

                $t1_28 = $t1_25 + $t1_26 + $t1_27;

                $rtn = array();
                $rtn['t1_1'] = $t1_1;
                $rtn['t1_2'] = $t1_2;
                $rtn['t1_3'] = $t1_3;
                $rtn['t1_4'] = $t1_4;

                $rtn['t1_13'] = $t1_13;
                $rtn['t1_14'] = $t1_14;
                $rtn['t1_15'] = $t1_15;
                $rtn['t1_16'] = $t1_16;

                $rtn['t1_25'] = $t1_25;
                $rtn['t1_26'] = $t1_26;
                $rtn['t1_27'] = $t1_27;
                $rtn['t1_28'] = $t1_28;

                /* CANTIDAD ACCIONES */
                $t1_5 = $this->$model->balance_amount_count_before($period_req, "A");
                $t1_17 = $this->$model->balance_amount_count_before($period_req, "B");

                $t1_29 = $t1_5 + $t1_17;

                $t1_6 = $this->$model->buys_shares($period_req, "A");
                $t1_18 = $this->$model->buys_shares($period_req, "B");

                $t1_7 = $this->$model->sells_shares($period_req, "A");
                $t1_19 = $this->$model->sells_shares($period_req, "B");

                $t1_8 = ($t1_5 + $t1_6) - $t1_7;
                $t1_20 = ($t1_17 + $t1_18) - $t1_19;


                $t1_30 = $t1_6 + $t1_18;
                $t1_31 = $t1_7 + $t1_19;

                $t1_32 = $t1_29 + $t1_30 + $t1_31;

                $rtn['t1_5'] = $t1_5;
                $rtn['t1_6'] = $t1_6;
                $rtn['t1_7'] = $t1_7;
                $rtn['t1_8'] = $t1_8;
                $rtn['t1_17'] = $t1_17;
                $rtn['t1_18'] = $t1_18;
                $rtn['t1_19'] = $t1_19;
                $rtn['t1_20'] = $t1_20;
                $rtn['t1_29'] = $t1_29;
                $rtn['t1_30'] = $t1_30;
                $rtn['t1_31'] = $t1_31;
                $rtn['t1_32'] = $t1_32;

                /* MONTO ACCIONES */
                $t1_9 = $comisions * $t1_5;
                $t1_21 = $comisions * $t1_17;

                $t1_33 = $t1_9 + $t1_21;

                $t1_10 = $comisions * $t1_6;
                $t1_22 = $comisions * $t1_18;

                $t1_11 = $comisions * $t1_7;
                $t1_23 = $comisions * $t1_19;

                $t1_12 = ($t1_9 + $t1_10) - $t1_11;
                $t1_24 = ($t1_21 + $t1_22) - $t1_23;


                $t1_34 = $t1_10 + $t1_22;
                $t1_35 = $t1_11 + $t1_23;

                $t1_36 = $t1_33 + $t1_34 + $t1_35;

                $rtn['t1_9'] = $t1_9;
                $rtn['t1_10'] = $t1_10;
                $rtn['t1_11'] = $t1_11;
                $rtn['t1_12'] = $t1_12;
                $rtn['t1_21'] = $t1_21;
                $rtn['t1_22'] = $t1_22;
                $rtn['t1_23'] = $t1_23;
                $rtn['t1_24'] = $t1_24;
                $rtn['t1_33'] = $t1_33;
                $rtn['t1_34'] = $t1_34;
                $rtn['t1_35'] = $t1_35;
                $rtn['t1_36'] = $t1_36;

                return $rtn;

                break;
        }
    }

    function set_period() {
        $rectify = $this->input->post("rectify");
        $period = $this->input->post("input_period");
        $others = $this->input->post("others");
        $anexo = $this->input->post("anexo");

        if ($period) {
            $this->session->unset_userdata('period');
            $this->session->unset_userdata('rectify');
            $this->session->unset_userdata('others');

            $date_string = date('Y-m', strtotime('-1 month', strtotime(date('Y-m-01'))));

            list($month, $year) = explode("-", $period);
            $set_month = strtotime(date($year . '-' . $month . '-01'));

            $limit_month = strtotime('-1 month', strtotime(date('Y-m-01')));
            $set_start_month = strtotime(date('2013-12-30'));

            if ($this->idu == -342725103)
                $set_start_month = strtotime(date('2010-12-30'));

            if ($rectify) {
                $newdata = array('period' => $period, 'rectify' => $rectify, 'others' => $others);
                /* PERIOD SESSION */
                $this->session->set_userdata($newdata);
                redirect('/sgr');
            } else {
                if ($limit_month < $set_month) {
                    return "1"; // Posterior al mes actual
                } else if ($set_start_month > $set_month) {
                    return "2"; // Anterior al mes Inicial
                } else {
                    $get_period = $this->sgr_model->get_current_period_info($this->anexo, $period);
                    if ($get_period) {
                        return $this->input->post("input_period"); //Ya fue informado                    
                    } else {
                        $newdata = array('period' => $period);
                        $this->session->set_userdata($newdata);
                        redirect('/sgr');
                    }
                }
            }
        }
    }

    function unset_period() {

        $this->session->unset_userdata('rectify');
        $this->session->unset_userdata('others');
        $this->session->unset_userdata('period');
        redirect('/sgr');
    }

    function unset_period_active() {
        $this->session->unset_userdata('rectify');
        $this->session->unset_userdata('others');
        $this->session->unset_userdata('period');
    }

    function check_session_period() {
        if ($this->session->userdata['period']) {
            echo $this->session->userdata['period'];
        }
    }

    function upload_file() {
        try {
            if ($this->input->post("submit")) {
                $this->load->library("app/uploader");
                $result = (array) $this->uploader->do_upload();

                return $result;
            }
//to render ->
        } catch (Exception $err) {
            log_message("error", $err->getMessage());
            return show_error($err->getMessage());
        }
    }

    /* PERIODOS SIN MOVIMIENTO */

    function set_no_movement() {
        $data = $this->input->post('data');
        $period = $data['no_movement'];
        $anexo = ($this->session->userdata['anexo_code']) ? : '06';
        $model = "model_" . $anexo;
        $this->load->model($model);

        if (!$this->session->userdata['rectify']) {
            $get_period = $this->sgr_model->get_current_period_info($anexo, $period);
        }


        /* CHECK 141 "SIN MOVIMIENTO" VALIDATION */
        $check_141 = $this->sgr_model->get_just_active("125", $this->period);

        if (empty($check_141)) {
            echo "error141";
        } else {

            if (!$get_period) {
                $result = array();
                $result['period'] = $period;
                $result['filename'] = "SIN MOVIMIENTOS";
                $result['sgr_id'] = $this->sgr_id;
                $result['anexo'] = $anexo;
                $save_period = (array) $this->$model->save_period($result);
                echo "ok";
            }
        }
    }

// OFFLINE FALLBACK
    function offline() {
// testeo reemplazo appcache
        $customData = array();
        $customData['base_url'] = base_url();
        $customData['module_url'] = base_url() . 'sgr/';
        $this->render('offline', $customData);
    }

    /* DD.JJ. */

    function get_processed_17_tab() {
        $list_files = "<li class=processed><b>Presentación Regimen Informativo DD.JJ.</b></li>";
        for ($i = date("Y"); $i > 2009; $i--) {
            $processed = $this->sgr_model->get_ready($this->sgr_id, $i);
            $processed = array($processed);
            foreach ($processed as $file) {

                if ($file)
                    $list_files .= '<li><a href="#tab_processed' . $i . '" data-toggle="tab">' . $i . '</a></li>';
            }
        }
        return $list_files;
    }

    function get_processed_17() {

        $list_files = '';
        for ($i = date("Y"); $i > 2009; $i--) {
            $list_files .= '<div id="tab_processed' . $i . '" class="tab-pane">             
            <div class="" id="' . $i . '"><ul>';


            for ($j = 12; $j > 0; $j--) {

                $j = sprintf('%02s', $j);
                $new_query = $j . "-" . $i;
                $disabled_link = ($i < 2014) ? ' disabled_link' : "";


                $processed = $this->sgr_model->get_ready_anexo($this->sgr_id, $new_query);
                $print_file = anchor('/sgr/print_ddjj/' . $new_query, ' <i class="fa fa-print" alt="Imprimir"></i> Generar DD.JJ. Para ' . $new_query, array('target' => '_blank', 'class' => 'btn btn-primary' . $disabled_link));

                if ($processed)
                    $list_files .= "<li>" . $print_file . "</li>";
            }
            $list_files .= '</ul></div>
        </div>';
        }

        return $list_files;
    }

    function get_processed_tab($anexo) {
        $list_files = "<li class=processed><b>ANEXOS PROCESADOS</b></li>";
        for ($i = date("Y"); $i > 2009; $i--) {
            $processed = $this->sgr_model->get_processed($anexo, $this->sgr_id, $i);
            $processed = array($processed);
            foreach ($processed as $file) {

                $show_period = ($i != 2010) ? $i : "ADMINISTRADOR";

                if ($file)
                    $list_files .= '<li><a href="#tab_processed' . $i . '" data-toggle="tab">' . $show_period . '</a></li>';
            }
        }
        return $list_files;
    }

    function get_rectified_tab($anexo) {
        $list_files = "<li class=rectified><b>ANEXOS RECTIFICADOS</b></li>";
        for ($i = date("Y"); $i > 2009; $i--) {
            $processed = $this->sgr_model->get_rectified($anexo, $this->sgr_id, $i);
            $processed = array($processed);
            foreach ($processed as $file) {
                if ($file)
                    $list_files .= '<li><a href="#tab_rectified' . $i . '" data-toggle="tab">' . $i . '</a></li>';
            }
        }
        return $list_files;
    }

    /*
     * PROCESSED FILES BROWSER
     * 
     */

    function get_processed($anexo) {
        $list_files = '';
        for ($i = date("Y"); $i > 2009; $i--) {
            $list_files .= '<div id="tab_processed' . $i . '" class="tab-pane">             
            <div class="" id="' . $i . '"><ul>';
            $processed = $this->sgr_model->get_processed($anexo, $this->sgr_id, $i);



            foreach ($processed as $file) {

                $print_xls_array = array('12', '125', '141', '202');

                $asset = ($anexo == "09") ? "pdf_asset" : "xls_asset";
                $file_origen = $file['origen'];
                $file_filename = $file['filename'];
                $link_filename = $file_filename;

                $print_filename = substr($file_filename, 0, -25);
                $disabled_link = '';
                $print_fn_lnk = 'print_anexo';

                /* SIN MOVIMIENTO */
                if ($file_filename == "SIN MOVIMIENTOS") {
                    $print_filename = $file_filename;
                    $print_fn_lnk = 'print_motionless';
                    $link_filename = $file['period'];
                }

                if ($file_origen == "forms2") {
                    $disabled_link = ' disabled_link';
                    $print_filename = $file_filename;

                    $show_period = ($i != 2010) ? $file['period'] : "ADMINISTRADOR";

                    $download = anchor('sgr/' . $asset . '/' . $anexo . '/' . $file_filename, ' <i class="fa fa-download" alt="Descargar"></i>', array('class' => 'btn btn-primary' . $disabled_link));
                    $print_file = anchor('sgr/dna2_asset/XML-Import/' . translate_anexos_dna2_urls($anexo) . '/' . $file_filename, ' <i class="fa fa-print" alt="Imprimir"></i>', array('target' => '_blank', 'class' => 'btn btn-primary'));
                    $print_xls_link = anchor('/sgr/print_xls/' . $file_filename, ' <i class="fa fa-table" alt="XLS"></i>', array('target' => '_blank', 'class' => 'btn btn-primary' . $disabled_link));
                    $print_xls = (in_array($anexo, $print_xls_array)) ? $print_xls_link : "";


                    $rectifica_link_class = "";
                    $rectify = anchor($file['period'] . "/" . $anexo, '<i class="fa fa-undo" alt="Rectificar"></i> RECTIFICAR', array('class' => $rectifica_link_class . ' btn btn-danger' . $disabled_link));
                    $list_files .= "<li>" . $download . " " . $print_file . " " . $print_xls . " " . $rectify . " " . $print_filename . "  [" . $show_period . "]  </li>";
                } else {

                    /* RECTIFY COUNT */
                    $count = $this->sgr_model->get_period_count($anexo, $file['period']);

                    $rectify_count_each = ($count > 0) ? "- " . $count . "º RECTIFICATIVA" : "";
                    $new_disabled_link = ($anexo == "09") ? ' disabled_link' : $disabled_link;
                    $download = anchor('sgr/' . $asset . '/' . $anexo . '/' . $file_filename, ' <i class="fa fa-download" alt="Descargar"></i>', array('target' => '_blank', 'class' => 'btn btn-primary' . $disabled_link));

                    $print_file = anchor('/sgr/' . $print_fn_lnk . '/' . $link_filename, ' <i class="fa fa-print" alt="Imprimir"></i>', array('target' => '_blank', 'class' => 'btn btn-primary' . $new_disabled_link));

                    $print_xls_link = anchor('/sgr/print_xls/' . $file_filename, ' <i class="fa fa-table" alt="XLS"></i>', array('target' => '_blank', 'class' => 'btn btn-primary' . $disabled_link));
                    $print_xls = (in_array($anexo, $print_xls_array)) ? $print_xls_link : "";



                    $rectifica_link_class = ($this->period) ? 'rectifica-warning_' . $file['period'] : 'rectifica-link_' . $file['period'];
                    $rectify = anchor($file['period'] . "/" . $anexo, '<i class="fa fa-undo" alt="Rectificar"></i> RECTIFICAR', array('class' => $rectifica_link_class . ' btn btn-danger'));
                    $list_files .= "<li>" . $download . " " . $print_file . " " . $print_xls . " " . $rectify . " " . $print_filename . "  [" . $file['period'] . "] " . $rectify_count_each . " </li>";
                }
            }
            $list_files .= '</ul></div>
        </div>';
        }
        if (isset($file))
            return $list_files;
    }

    /*
     * RECTIFIED FILES BROWSER
     * 
     */

    function get_rectified($anexo) {

        $list_files = '';
        $translate = '';


        for ($i = date("Y"); $i > 2009; $i--) {
            $list_files .= '<div id="tab_rectified' . $i . '" class="tab-pane">             
            <div id="' . $i . '"><ul>';
            $rectified = $this->sgr_model->get_rectified($anexo, $this->sgr_id, $i);
            foreach ($rectified as $file) {

                $file_filename = $file['filename'];

                $print_filename = substr($file_filename, 0, -25);
                $disabled_link = '';

                if ($file_filename == "SIN MOVIMIENTOS") {
                    $disabled_link = ' disabled_link';
                    $print_filename = $file_filename;
                }

                $rectified_on = $file['rectified_on'];
                switch ($file['reason']) {
                    case 1:
                        $translate = 'Errores en el sistema y/o procesamiento del archivo';
                        break;

                    case 2:
                        $translate = 'Error en la informacion sumistrada';
                        break;

                    case 3:
                        $translate = $file['others'];
                        break;
                }

                $list_files .= '<li>[' . $file['period'] . '] ' . $print_filename . ' (' . $rectified_on . ') <small><em>' . $translate . '</em></small> </li>';
            }
            $list_files .= '</ul></div>
        </div>';
        }
        if (isset($file))
            return $list_files;
    }

    /*
     * PENDING FILES BROWSER
     * 
     */

    function get_pending($anexo, $sgr_id) {

        $pending = $this->sgr_model->get_pending($anexo, $sgr_id);
        $list_files = NULL;

        foreach ($pending as $file) {

            if (!$file) {
                return false;
                exit();
            }

            $file_filename = $file['filename'];




            $print_filename = substr($file_filename, 0, -25);
            $disabled_link = '';

            if ($file_filename == "SIN MOVIMIENTOS") {
                $disabled_link = ' disabled_link';
                $print_filename = $file_filename;
            }
            $pending_on = $file['pending_on'];
            $list_files .= '<li><strong>Anexos Pendientes:  ' . $print_filename . ' (' . $pending_on . ') [' . $file['period'] . '] </strong></li>';
        }

        return $list_files;
    }

    function get_rectified_legend($anexo) {

        switch ($anexo) {
            case 06:
                $legend_msg = "6.1, 6.2, 20.1";
                break;

            case 12:
                $legend_msg = "12.1 ,12.2 ,12.3 ,12.4 ,12.5";
                break;
            case 13:
            case 14:
                $legend_msg = "14.1";
                break;
            case 201:
                $legend_msg = "20.2";
                break;
            case 202:
                $legend_msg = "13, 20.2";
                break;
        }
        if (isset($legend_msg))
            return "Los siguentes anexos relacionados pueden ser Rectificados<br>" . $legend_msg . "<br> Desea continuar?";
    }

    /* FILE BROWSER
     * 
     * 
     */

    function file_browser() {
        $segment_array = $this->uri->segment_array();

// first and second segments are the controller and method
        $controller = array_shift($segment_array);
        $method = array_shift($segment_array);

// absolute path using additional segments
        $path_in_url = 'anexos_sgr';
        foreach ($segment_array as $segment)
            $path_in_url.= $segment . '/';
        $absolute_path = getcwd() . '/' . $path_in_url;
        $absolute_path = rtrim($absolute_path, '/');


        if (is_dir($absolute_path)) {

// link generation helper
            $this->load->helper('url');

            $dirs = array();
            $files = array();
// fetching directory
            if ($handle = @opendir($absolute_path)) {
                while (false !== ($file = readdir($handle))) {
                    if (( $file != "." AND $file != "..")) {
                        if (is_dir($absolute_path . '/' . $file)) {
                            $dirs[]['name'] = $file;
                        } else {
                            $files[]['name'] = $file;
                        }
                    }
                }
                closedir($handle);
                sort($dirs);
                sort($files);
            }
// parent folder
// ensure it exists and is the first in array
            if ($path_in_url != '')
                array_unshift($dirs, array('name' => ' '));

// view data
            $fileData = array(
                'controller' => $controller,
                'method' => $method,
                'virtual_root' => getcwd(),
                'path_in_url' => $path_in_url,
                'dirs' => $dirs,
                'files' => $files,
            );
//$this->render('dashboard', $customData);
//           

            /* CALL RENDER */
            $files_list = $this->render_file_browser($fileData);
            $customData['files_list'] = $files_list;
            return $customData;
        }
        else {
// is it a file?
            if (is_file($absolute_path)) {
// open it
                header('Cache-Control: no-store, no-cache, must-revalidate');
                header('Cache-Control: pre-check=0, post-check=0, max-age=0');
                header('Pragma: no-cache');

                $text_types = array(
                    'xls'
                );
                $ext = explode('.', $absolute_path);
// download necessary ?
                if (in_array($ext[count($ext) - 1], $text_types)) {
                    header('Content-Type: text/plain');
                } else {
                    header('Content-Description: File Transfer');
                    header('Content-Length: ' . filesize($absolute_path));
                    header('Content-Disposition: attachment; filename=' . basename($absolute_path));
                }

                @readfile($absolute_path);
            } else {
//@show_404();
                return "";
            }
        }
    }

    /*
     * FILE BROWSER by anexo
     * directory /sgr/anexos_sgr/
     * 
     */

    function render_file_browser($customData) {
        $files_list = "";
        $prefix = $customData['controller'] . '/' . $customData['method'] . '/' . $customData['path_in_url'];
        if (!empty($customData['dirs'])) {
            foreach ($customData['dirs'] as $dir) {
//PRINT DIRECTORIES
//$files_list .= anchor($prefix . $dir['name'], $dir['name']) . '<br>';
            }
        }

        if (!empty($customData['files'])) {
            $countfiles = 0;
            foreach ($customData['files'] as $file) {
                list($sgr, $anexo, $filedate, $filetime) = explode("_", $file['name']);
                if ($anexo == $this->anexo && (float) $sgr == $this->sgr_id) {
                    list($filename, $extension) = explode(".", $file['name']);
                    /* Vars */
                    $disabled_link = (isset($this->period)) ? '' : ' disabled_link';
                    $disabled_link = (isset($this->session->userdata['rectify'])) ? '' : $disabled_link;

                    $process_file = anchor('/sgr/anexo/' . $filename, '<i class="fa fa-external-link" alt="Procesar"></i> PROCESAR', array('id' => 'procesar', 'class' => 'btn btn-success procesar' . $disabled_link));
                    $process_file_disabled = '<i class="fa fa-external-link fa-spin" alt="Procesar">PROCESAR</i>';
                    $download = anchor('sgr/xls_asset/' . $file['name'], '<i class="fa fa-download" alt="Descargar"></i>', array('class' => 'btn btn-success'));

                    $files_list .= '<li> ' . $download . " " . $process_file . ' PENDIENTE ' . $filedate . ' ' . $filetime . ' </li>';
                }
            }
        }


        $file_list_html = '<div class="alert">                        
        <ol>
            ' . $files_list . '
        </ol>
    </div>';

        $no_list_html = '<div class="alert alert-danger" id="{_id}">
        No hay Archivos Pendientes |
        <i class="fa fa-plus"></i> <a data-target="#file_div" data-toggle="collapse"> Seleccionar Archivos a Procesar</a>
    </div>';

        if ($files_list != "") {
            return $file_list_html;
        } else {
            return $no_list_html;
        }
    }

    function pre_general_validation($anexo) {
        switch ($anexo) {
            case '061':
                $info_06 = $this->sgr_model->get_just_active("06", $this->period);
                foreach ($info_06 as $filenames) {
                    if ($filenames['filename'] == 'SIN MOVIMIENTOS') {
                        return "Si el Anexo 6 de un período fue informado “SIN MOVIMIENTOS”, para ese mismo período este anexo debe ser indicado como “SIN MOVIMIENTOS” automáticamente.";
                    }
                }
                break;

            case '141':
                $base_legend = "Debe validar que previamente hayan sido informados los siguientes Anexos correspondientes al mismo período que se está queriendo importar:";
                $add_base_legend = "";
                $error = false;
                $info_14 = $this->sgr_model->get_just_active("14", $this->session->userdata['period']);
                if (!$info_14) {
                    $error = true;
                    $base_legend .= $add_base_legend . "<br>Anexo 14 ";
                }

                $info_124 = $this->sgr_model->get_just_active("124", $this->session->userdata['period']);
                if (!$info_124) {
                    $error = true;
                    $base_legend .= $add_base_legend . "<br>Anexo 12.4 ";
                }

                $info_125 = $this->sgr_model->get_just_active("125", $this->session->userdata['period']);
                if (!$info_125) {
                    $error = true;
                    $base_legend .= $add_base_legend . "<br>Anexo 12.5 ";
                }

                if ($error) {
                    return $base_legend . $add_base_legend;
                }


                break;
        }
    }

    function render($file, $customData) {
        $this->load->model('user/user');
        $this->load->model('msg');
        $this->load->language('inbox');
        $cpData['lang'] = $this->lang->language;
        $segments = $this->uri->segment_array();
        $cpData['nolayout'] = (in_array('nolayout', $segments)) ? '1' : '0';
        $cpData['theme'] = $this->config->item('theme');
        $cpData['base_url'] = $this->base_url;
        $cpData['module_url'] = $this->module_url;
        $cpData['global_js'] = array(
            'base_url' => $this->base_url,
            'module_url' => $this->module_url,
            'idu' => $this->idu
        );
        $user = $this->user->get_user($this->idu);



        $cpData['user'] = (array) $user;
        $cpData['isAdmin'] = $this->user->isAdmin($user);
        $cpData['username'] = strtoupper($user->lastname . ", " . $user->name);
        $cpData['usermail'] = $user->email;
// Profile 
//$cpData['profile_img'] = get_gravatar($user->email);

        $cpData['gravatar'] = (isset($user->avatar)) ? $this->base_url . $user->avatar : get_gravatar($user->email);
        $cpData['rol'] = "Usuarios";
        $cpData['rol_icono'] = ($cpData['rol'] == 'coordinador') ? ('fa fa-users') : ('fa fa-user');

        $cpData = array_replace_recursive($customData, $cpData);

        /* Inbox Count MSgs */
        $mymgs = $this->msg->get_msgs($this->idu);
        $cpData['inbox_count'] = $mymgs->count();

// offline mark
        $cpData['is_offline'] = ($this->uri->segment(3) == 'offline') ? ('offline') : ('');
        $layout = (isset($customData['layout'])) ? : 'layout.php';
        $this->ui->compose($file, 'layout.php', $cpData);
    }

}
