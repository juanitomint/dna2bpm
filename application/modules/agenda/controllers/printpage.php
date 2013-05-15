<?php

class Printpage extends CI_Controller {

    function Printpage() {
        parent::__construct();
        $this->load->library('parser');
        $this->load->model('user');
        $this->load->model('agenda');
        $this->user->authorize('ADM,ADMAG,UAG');
        $this->load->helper('cookie');
        //----LOAD LANGUAGE
        $this->lang->load('library', $this->config->item('language'));
        $this->idu = (float) $this->session->userdata('iduser');
    }

    function index() {

//        $cpData["print_mode"]=$this->session->userdata("print_mode");
//        $cpData["print_date"]=$this->session->userdata("print_date");
        $cpData["agendas"]=$this->session->userdata("agendas");

//        if(isset($this->session->userdata("print_date"))){
////        $fechaTS=strtotime($_SESSION["printDate"]);
////        $fecha=getDate($fechaTS);
//            echo 1;
//        }else{
//        $fecha=getDate();
//        }


        switch($this->session->userdata("print_mode")){
            case "week":
                $this->parser->parse('agenda/printweek', $cpData);
            break;

            case "workweek":
                $this->parser->parse('agenda/printworkweek', $cpData);
            break;

            case "day":
                echo $this->session->userdata("print_date");
                $this->parser->parse('agenda/printday', $cpData);
            break;

            case "month":
                $this->parser->parse('agenda/printmonth', $cpData);
            break;

            default:
                $this->parser->parse('agenda/printmonth', $cpData);

        }

    }
}
?>