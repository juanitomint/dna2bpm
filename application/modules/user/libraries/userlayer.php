<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of userloader
 *
 * @author Juan Ignacio Borda <juanignacioborda@gmail.com>
 */
class Userlayer {
     function __construct() {
        //parent::__construct();
        $ci = & get_instance();
        $ci->load->config('user/config');
        $ci->load->model('user/user');
        $ci->load->model('user/group');
        $ci->load->model('user/rbac');
        if($ci->config->item('user_plugin')){
            foreach($ci->config->item('user_plugin') as $plugin)
            $ci->load->library('user/'.$plugin.'_plugin');
        }
    }

    function apply() {
        return true;
    }
}
