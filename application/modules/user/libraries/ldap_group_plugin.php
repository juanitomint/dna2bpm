<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class ldap_group_plugin extends User {

    function __construct() {
        parent::__construct();
        $this->config->load('user/ldap');
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

    function get_groups($order = null, $query_txt = null) {
        $ldapconn = $this->connect();
        $ldapbind = ldap_bind($ldapconn, $this->config->item('ldaprdn'), $this->config->item('ldappass')) or die("Could not bind with password to: " . $this->config->item('ldaprdn'));
        $filter = ($query_txt) ? "(cn=$query_txt)" : '(cn=*)';
        $result = ldap_search($ldapconn, $this->config->item('groupsDN'), $filter, array()) or die("Search error.");
        $data = ldap_get_entries($ldapconn, $result);
        $groups = array();
        for ($i = 0; $i < $data["count"]; $i++) {
            //----map to unified group object
            $thisgroup = array(
                'idgroup' => $data[$i]["gidnumber"][0],
                'name' => $data[$i]["cn"][0],
                'desc' => (isset($data[$i]["description"][0])) ? $data[$i]["description"][0] : '',
            );
            //----get members()
            /*
            if (isset($data[$i]['member'])) {
                for ($j = 0; $j < $data[$i]['member']["count"]; $j++) {
                    
                    $thisgroup['users'][]=$this->user->get_id_byDN($data[$i]['member'][$j]);
                }
            }
             */
            $groups[] = $thisgroup;
        }
        return $groups;
    }

}
