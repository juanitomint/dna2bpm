<?php

//---This class is for use on the form editor
class Fe extends CI_Model {

    function Fe() {
        parent::__construct();
    }

    function Get_options($query=array()) {
        $fields = array('idop' => true, 'title' => true);
        $sort = array('title' => 1);
        $rs = $this->mongowrapper->db->options->find($query, $fields);
        $rs->sort($sort);
        return($rs);
    }

   

}

?>