<?php

/**
 * This libray load cimongo and set db as mongo and apply bindings for replaced functions
 *
 * @author Juan Ignacio Borda <juanignacioborda@gmail.com>
 */
class Cimongo_plugin {

    function __construct() {
        //parent::__construct();
        $ci = & get_instance();
        if ($ci) {
            $ci->load->library('cimongo/cimongo');
            $ci->db = $ci->cimongo;
            //---Load custom user class
            $ci->load->library('user/cimongo_user_plugin');
            $ci->user = $ci->cimongo_user_plugin;
        }
    }

    function apply() {
        return true;
    }

    function where_id($_id) {
     return   array('_id' => new MongoId($_id));
    }

}