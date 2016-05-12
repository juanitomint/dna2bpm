<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Csv_connector extends CI_Model {

    function csv_connector() {
        parent::__construct();
        $this->load->library('CSVReader');
    }

    function get_data($resource) {
        //---connect to database and retrive data as specified
        if (isset($resource['source'])) {
            if(is_file($resrource['source'])){
            $rtn_arr=$this->CSVReader->parse_file($source, true);
            } else {
                echo "File:".$resrource['source']." can't be found";
            }
            return $rtn_arr;
        }
    }

}

?>