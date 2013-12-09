<?php

/*
 * 
 * 
 */

/**
 * This libray load submodules and apply bindings for replaced functions
 * ldap_user_plugin
 * ldap_group_plugin
 * 
 * @author Juan Ignacio Borda <juanignacioborda@gmail.com>
 */
class ldap_plugin {
  function __construct() {
  //parent::__construct();
  $ci =& get_instance();
  $ci->load->config('ldap');
  $ci->load->library('ldap_user_plugin');
  $ci->user=$ci->ldap_user_plugin;
  $ci->load->library('ldap_group_plugin');
  $ci->group=$ci->ldap_group_plugin;
  }
}
