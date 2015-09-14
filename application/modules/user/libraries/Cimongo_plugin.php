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
        }
    }

    function apply() {
        return true;
    }

}