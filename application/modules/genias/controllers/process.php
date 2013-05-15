<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Process extends MX_Controller {

    function __construct() {
        parent::__construct();
        $this->load->library('parser');
        $this->load->model('user');
        $this->load->model('app');
        $this->user->authorize();
        //----LOAD LANGUAGE

        $this->lang->load('library', $this->config->item('language'));
        $this->idu = (float) $this->session->userdata('iduser');
        $this->container = 'container.genias'; //'container.empresas';
    }

    public function Insert() {
        $container = $this->container;
        $input = json_decode(file_get_contents('php://input'));

        $newArr = array();

        foreach ($input as $key => $value) {


            $newArr['7406'] = strval($this->idu);
            if ($key == 7407) {
                list($yearVal, $monthVal, $dayVal) = explode("-", $value);
                $dataArr = array("Y" => $yearVal, 'm' => $monthVal, 'd' => str_replace("T00:00:00", "", $dayVal));
                $newArr[$key] = $dataArr; //date_parse($value);
            } else if ($key == 'id') {
                /* GENERO ID */
                $id = ($value == null || strlen($value) < 6) ? $this->app->genid($container) : $value;
            } else {
                $newArr[$key] = $value;
            }

            /* BUSCO CUIT */
            if ($key == 7411) {
                $queryCuit = array('7411' => $value);
                $resultCuit = $this->mongo->db->$container->findOne($queryCuit);

                if ($resultCuit['id'] != null) {
                    $id = $resultCuit['id'];
                }
            }
        }




        /* Lo paso como Objeto */
        $array = (array) $newArr;


        $result = $this->app->put_array($id, $container, $array);

        if ($result) {
            $out = array('status' => 'ok');
        } else {
            $out = array('status' => 'error');
        }
    }

    /*
     * VIEW
     */

    public function View() {

        $container = $this->container;
        $query = array('7406' => strval($this->idu));
        $resultData = $this->mongo->db->$container->find($query);

        foreach ($resultData as $returnData) {
            $fileArrMongo[] = $returnData;
        }
        //return $fileArrMongo;             

        if (!empty($fileArrMongo)) {
            echo json_encode(array(
                'success' => true,
                'message' => "Loaded data",
                'data' => $fileArrMongo
            ));
        }
    }

}