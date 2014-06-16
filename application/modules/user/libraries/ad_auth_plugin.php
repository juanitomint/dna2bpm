<?php

/*
 * 
 * 
 */

/**
 * This libray load submodules and apply bindings for replaced functions
 * ad_user_plugin
 * ad_group_plugin
 * 
 * @author Juan Ignacio Borda <juanignacioborda@gmail.com>
 */
class ad_auth_plugin {

    function __construct() {
        //parent::__construct();
        $ci = & get_instance();
        if ($ci) {
            $ci->load->config('user/ad');
            $ci->load->library('user/ad_user_plugin');
            $ci->user = $ci->ad_user_plugin;
            if ($ci->config->item('ad_use_groups')) {
                $ci->load->library('user/ad_group_plugin');
                $ci->group = $ci->ad_group_plugin;
            }
        }
    }

    function apply() {
        return true;
    }

}
