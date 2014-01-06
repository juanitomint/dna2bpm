<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class ldap_user_plugin extends User {

    function __construct() {
        $this->config->load('user/ldap');
        parent::__construct();
    }

////-----update last access
    function add($user_data) {
        $ldapconn = $this->connect();
        $ldapbind = ldap_bind($ldapconn, $this->config->item('ldaprdn'), $this->config->item('ldappass')) or die("Could not bind with password to: " . $this->config->item('ldaprdn'));
        //@todo add user to ldap directory
        //----add user to groups
        if ($user_data['group']) {
            $group = explode(',', $user_data['group']);
            $user_dn = $this->get_DN_byid((int) $user_data['idu']);
            //var_dump($group, $user_dn);
            $this->group->remove_user($user_dn);
            foreach ($group as $gidNumber) {
                $group_dn = $this->group->get_DN_byid($gidNumber);

                //$filter = "(|(member=" . $user_dn . ")(uniqueMember=" . $user_dn . "))";
                $filter = "(".$this->config->item('member_attr').'=' . $user_dn . ")";
                $result = ldap_search($ldapconn, $group_dn, $filter, array('dn')) or die("Search error.");
                $info = ldap_get_entries($ldapconn, $result);
                if (!$info['count']) {
                    //echo "add user $user_dn to $group_dn<br/>";
                    $ldap_obj = array(
                        $this->config->item('member_attr') => $user_dn
                    );
                    //---Add the user to the group
                    ldap_mod_add($ldapconn, $group_dn, $ldap_obj) or die('error in ldap MOdule<br/>File:".__FILE__."<br/>Line:' . __LINE__);
                }
            }
            
        }
    }

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
        return (int) $info[0]['gidnumber'][0];
    }

    function get_user_groups($dn) {
        $ldapconn = $this->connect();
        $ldapbind = ldap_bind($ldapconn, $this->config->item('ldaprdn'), $this->config->item('ldappass')) or die("Could not bind with password to: " . $this->config->item('ldaprdn'));
        //$filter = "(|(member=" . $dn . ")(uniqueMember=" . $dn . "))";
        $filter = "(".$this->config->item('member_attr').'=' . $dn . ")";
        $result = ldap_search($ldapconn, $this->config->item('groupsDN'), $filter, array('dn'), 1) or die("Search error.");
        $info = ldap_get_entries($ldapconn, $result);
        $groups = array();
        for ($j = 0; $j < $info["count"]; $j++) {
            $groups[] = $this->get_group_id_byDN($info[$j]['dn']);
            //$groups[] = $info[$info[$j]][0];
        }
        return $groups;
    }

    function get_users($start, $limit, $sort, $query, $idgroup) {
        $ldapconn = $this->connect();
        $ldapbind = ldap_bind($ldapconn, $this->config->item('ldaprdn'), $this->config->item('ldappass')) or die("Could not bind with password to: " . $this->config->item('ldaprdn'));
        $filter = "(gidnumber=$idgroup)";
        //@todo implements query;
        $result = ldap_search($ldapconn, $this->config->item('groupsDN'), $filter, array()) or die("Search error.");
        $data = ldap_get_entries($ldapconn, $result);
        $groups = array();
        $data = $data[0];
        $ldap_users =(isset($data[$this->config->item('member_attr')])) ? $data[$this->config->item('member_attr')]:array();
        unset($ldap_users['count']);

        $ldap_users = array_slice($ldap_users, $start, $limit);
        $users = array();
        foreach ($ldap_users as $dn) {
            $users[] = $this->get_user_byDN($dn);
        }
        return array_filter($users);
    }

    function get_user($iduser) {
//*        $user = array();
        $ldapconn = $this->connect();
        $ldapbind = ldap_bind($ldapconn, $this->config->item('ldaprdn'), $this->config->item('ldappass')) or die("Could not bind with password to: " . $this->config->item('ldaprdn'));
        $filter = "(uidNumber=$iduser)";
        $result = ldap_search($ldapconn, $this->config->item('baseDN'), $filter) or die("Search error.");
        $info = ldap_get_entries($ldapconn, $result);
        if ($info['count']) {
            return $this->prepare($info);
        }
    }

    function prepare($info) {
        if ($info) {
            $dn = $info[0]['dn'];
            $info = $info[0];
            $map = $this->config->item('user_map');
            $map = array_flip(array_filter($map));
            for ($j = 0; $j < $info["count"]; $j++) {
                if (isset($map[$info[$j]])) {
                    $user[$map[$info[$j]]] = $info[$info[$j]][0];
                }
            }
            $user['group'] = $this->get_user_groups($dn);
        }
        //---map user attrs
        //----only return users with id
        if (isset($user['idu'])) {
            $user['idu'] = (int) $user['idu'];
            $user['dn'] = $info['dn'];
            return (object) $user;
        }
    }

    function get_DN_byid($iduser) {
        $ldapconn = $this->connect();
        $ldapbind = ldap_bind($ldapconn, $this->config->item('ldaprdn'), $this->config->item('ldappass')) or die("Could not bind with password to: " . $this->config->item('ldaprdn'));
        $filter = "(uidNumber=$iduser)";
        $result = ldap_search($ldapconn, $this->config->item('baseDN'), $filter, array('dn')) or die("Search error.");
        $info = ldap_get_entries($ldapconn, $result);
        if ($info['count']) {
            return $info[0]['dn'];
        }
    }

    function get_user_byDN($dn) {
        $ldapconn = $this->connect();
        $ldapbind = ldap_bind($ldapconn, $this->config->item('ldaprdn'), $this->config->item('ldappass')) or die("Could not bind with password to: " . $this->config->item('ldaprdn'));
        $filter = "(objectclass=*)";
        $result = ldap_read($ldapconn, $dn, $filter, array()) or die("Search error.");
        $info = ldap_get_entries($ldapconn, $result);
        if ($info['count']) {
            return $this->prepare($info);
        }
    }

    function get_user_id_byDN($dn) {
        $ldapconn = $this->connect();
        $ldapbind = ldap_bind($ldapconn, $this->config->item('ldaprdn'), $this->config->item('ldappass')) or die("Could not bind with password to: " . $this->config->item('ldaprdn'));
        $filter = "(objectclass=*)";
        $result = ldap_read($ldapconn, $dn, $filter, array('uidNumber')) or die("Search error.");
        $info = ldap_get_entries($ldapconn, $result);
        if ($info['count']) {
            return $info[0]['uidnumber'][0];
        }
    }

}
