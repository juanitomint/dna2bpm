<?php

if (!defined('BASEPATH'))
        exit('No direct script access allowed');

class Business extends MX_Controller {

        function __construct() {
                parent::__construct();
                $this->load->library('parser');
                $this->load->model('meeting');
                //---base variables
                $this->base_url = base_url();
                $this->module_url = base_url() . 'meetings/';
        }

        function Index() {

                $this->load->view('Business Controller');
        }
        function find($query){
                $query = ($query) ? $query : $this->input->post('query');
                $this->meeting->find_business($query);
                
        }
        function registered() {
                $cpData = array();
                $cpData['base_url'] = $this->base_url;
                $cpData['module_url'] = $this->module_url;
                $rs = $this->meeting->get_registered();
                $cpData['business'] = $rs;
                @$this->parser->parse('business_registered', $cpData);
                //var_dump($rs);
        }

        function accredited() {
                $cpData = array();
                $cpData['base_url'] = $this->base_url;
                $cpData['module_url'] = $this->module_url;
                $rs = $this->meeting->get_accredited();
                $cpData['business'] = $rs;
                @$this->parser->parse('business_registered', $cpData);
                //var_dump($rs);
        }

        function get_data_cuit($cuit = null) {

                $cpData = array();
                $cuit = ($cuit) ? $cuit : $this->input->post('cuit');
                $cpData['base_url'] = $this->base_url;
                $cpData['module_url'] = $this->module_url;
                $b1 = $this->meeting->get_empresa_cuit($cuit);
                if (count($b1)) {
//                        $cpData['1693'] = $b1['1693'];
//                        $cpData['1695'] = $b1['1695'];
//                        $cpData['7466'] = $b1['7466'];
//                        $cpData['id'] = $b1['id'];
                        $cpData+=$b1;
                        
                        if (isset($b1['accredited'])) {
                                $cpData['accredited'] = ($b1['accredited']) ? true : false;
                        } else {
                                $cpData['accredited'] = false;
                        }
                        @$this->parser->parse('business_data', $cpData);
                } else {
                        //---the business isn't registered
                        $this->parser->parse('business_not_registered', $cpData);
                }
        }

        function register() {
                $this->parser->parse('business_register', $cpData);
        }

}