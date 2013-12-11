<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class ldap_user_plugin extends User {

    function __construct() {
        $this->config->load('user/ldap');
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

    function isAdmin($user) {
        if ($this->isloggedin()) {
            //---this is the ADMIN policy
            if (in_array('1', $user->group)) {
                return true;
            }
        }
        return false;
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
                if (isset($info[0]['uidnumber'])) {
                    return $info[0]['uidnumber'][0];
                } else {
                    show_error("The LDAP entry doesn't contains <br/> property: uidnumber<br/> for user:$username");
                    exit;
                }
            }
        } else {
            return false;
        }
    }

//---getuser alias.
    function getuser($iduser) {
        return $this->get_user($iduser);
    }

    function get_group_id_byDN($dn) {
        $ldapconn = $this->connect();
        $ldapbind = ldap_bind($ldapconn, $this->config->item('ldaprdn'), $this->config->item('ldappass')) or die("Could not bind with password to: " . $this->config->item('ldaprdn'));
        $filter = "(objectclass=*)";
        $result = ldap_read($ldapconn, $dn, $filter, array('gidnumber')) or die("Search error.");
        $info = ldap_get_entries($ldapconn, $result);
        return (int)$info[0]['gidnumber'][0];
    }

    function get_user_groups($dn) {
        $ldapconn = $this->connect();
        $ldapbind = ldap_bind($ldapconn, $this->config->item('ldaprdn'), $this->config->item('ldappass')) or die("Could not bind with password to: " . $this->config->item('ldaprdn'));
        $filter = "(|(member=" . $dn . ")(uniqueMember=" . $dn . "))";
        $result = ldap_search($ldapconn, $this->config->item('groupsDN'), $filter, array('dn'), 1) or die("Search error.");
        $info = ldap_get_entries($ldapconn, $result);
        $groups = array();
        for ($j = 0; $j < $info["count"]; $j++) {
            $groups[] = $this->get_group_id_byDN($info[$j]['dn']);
            //$groups[] = $info[$info[$j]][0];
        }
        return $groups;
    }

    function get_user($iduser) {
//*        $user = array();
        $ldapconn = $this->connect();
        $ldapbind = ldap_bind($ldapconn, $this->config->item('ldaprdn'), $this->config->item('ldappass')) or die("Could not bind with password to: " . $this->config->item('ldaprdn'));
        $filter = "(uidNumber=$iduser)";
        $result = ldap_search($ldapconn, $this->config->item('baseDN'), $filter) or die("Search error.");
        $info = ldap_get_entries($ldapconn, $result);
        if ($info) {
            $dn = $info[0]['dn'];
            $info = $info[0];
            $map = array(
                'idu' => 'uidnumber',
                "name" => "givenname",
                "lastname" => "sn",
                "cn" => "cn",
                "company" => "",
                "email" => "mail",
                "idnumber" => "",
                "phone" => "",
                "nick" => "uid",
                "passw" => "",
            );
            $map = array_flip(array_filter($map));
            for ($j = 0; $j < $info["count"]; $j++) {
                if (isset($map[$info[$j]])) {
                    $user[$map[$info[$j]]] = $info[$info[$j]][0];
                }
            }
            $user['group'] = $this->get_user_groups($dn);
        }
        //---map user attrs
        $user['idu']=(int)$user['idu'];
        return (object)$user;
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
