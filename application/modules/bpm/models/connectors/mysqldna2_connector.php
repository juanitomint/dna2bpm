<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class mysqldna2_connector extends CI_Model {

    function Mysqldna2_connector() {
        parent::__construct();
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
        $this->db = $this->load->database($config, true, false);
    }

    function get_data($resource) {
//---connect to database and retrive data as specified
        if (isset($resource['query'])) {
            $fields = (isset($resource['fields'])) ? $resource['fields'] : '*';
            $query = $resource['query'];
            $rs = $this->db->query($query);

            $rtn_arr = array();
            foreach ($rs->result_array() as $arr) {
                $rtn_arr[] = array_filter($arr);
            }
            return $rtn_arr;
        }
    }

}

?>