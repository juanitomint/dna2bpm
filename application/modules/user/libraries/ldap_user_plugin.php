<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class ldap_user_plugin extends User {

    function __construct() {
        parent::__construct();
    }

////-----update last access


    public function connect() {
        $ldapconn = ldap_connect($this->config->item('ldap_server'), $this->config->item('ldap_port'));
        if ($ldapconn) {
//-----SETINGS
            ldap_set_option($ldapconn, LDAP_OPT_PROTOCOL_VERSION, 3);
            ldap_set_option($ldapconn, LDAP_OPT_REFERRALS, 0);
            return $ldapconn;
        } else {
            show_error("Can\'t connect to LDAP Server:" . $this->config->item('ldap_server') . " on port " . $this->config->item('ldap_port'));
        }
    }

    function authenticate($username = '', $password = '') {
        $ldapconn = $this->connect();
        $ldapbind = ldap_bind($ldapconn, $this->config->item('ldaprdn'), $this->config->item('ldappass')) or die("Could not bind with password to: " . $this->config->item('ldaprdn'));
        $filter = "(uid=$username)";
        $result = ldap_search($ldapconn, $this->config->item('baseDN'), $filter, array('uidnumber')) or die("Search error.");
        $info = ldap_get_entries($ldapconn, $result);
        $auth_success = false;
        if ($info['count']) {
            $dn = $info[0]['dn'];
// realizando la autenticación as test
            $ldapbind = ldap_bind($ldapconn, $dn, $password) or die("Could not bind with password to: " . $dn);
            if ($ldapbind) {
//---get user data
                return $info[0][$info[0][0]][0];
            }
        } else {
            return false;
        }
    }

//---getuser alias.
    function getuser($iduser) {
        return $this->get_user($iduser);
    }

    function get_user($iduser) {
//*
        $user = array();
        $ldapconn = $this->connect();
        $ldapbind = ldap_bind($ldapconn, $this->config->item('ldaprdn'), $this->config->item('ldappass')) or die("Could not bind with password to: " . $this->config->item('ldaprdn'));
        $filter = "(uidNumber=$iduser)";
        $result = ldap_search($ldapconn, $this->config->item('baseDN'), $filter) or die("Search error.");
        $info = ldap_get_entries($ldapconn, $result);
        if ($info) {
            for ($j = 0; $j < $info[0]["count"]; $j++) {
                $user[$info[0][$j]] = $info[0][$info[0][$j]][0];
            }
        }
        return $user;
    }

    function get_id_byDN($dn) {
        $ldapconn = $this->connect();
        $ldapbind = ldap_bind($ldapconn, $this->config->item('ldaprdn'), $this->config->item('ldappass')) or die("Could not bind with password to: " . $this->config->item('ldaprdn'));
        $filter = "(objectclass=*)";
        $result = ldap_read($ldapconn, $dn, $filter, array('uidNumber')) or die("Search error.");
        $info = ldap_get_entries($ldapconn, $result);
        return $info[0]['uidnumber'][0];
    }

}
