<?php

/**
 * Description of acreditation
 * 
 * This controller manages the process of accreditation
 *
 * @author juanb
 * @date Aug 1, 2012
 */
if (!defined('BASEPATH'))
        exit('No direct script access allowed');

class Accreditation extends MX_Controller {

        function __construct() {
                parent::__construct();
                $this->load->library('parser');
                //---base variables
                $this->base_url = base_url();
                $this->module_url = base_url() . 'meetings/';
        }

        function Index() {
                $cpData = array();
                $cpData['base_url'] = $this->base_url;
                $cpData['module_url'] = $this->module_url;
                $cpData['title'] = 'Acreditar Empresa';
                $this->parser->parse('accreditation', $cpData);
        }

        function save($id) {
                $this->load->model('meeting');
                $cpData = array();
                $cuit = $this->input->post('CUIT');
                
                $cpData['base_url'] = $this->base_url;
                $cpData['module_url'] = $this->module_url;
                //var_dump($this->meeting->get_empresa($id));
                $cpData['b1'] = $this->meeting->get_empresa($id);
                        $cpData['title'] = 'Acreditacion';
                if (count($cpData['b1'])) {
                        $this->meeting->accredit($id);
                        $file = 'accreditation_ok';
                        $cpData['msg'] = 'Acreditada Ok!';
                } else {
                        $cpData['msg'] = 'ERROR!';
                        $file = 'business_not_registered_full';
                }
                $this->parser->parse($file, $cpData);
        }

}

/* End of file acreditation.php */