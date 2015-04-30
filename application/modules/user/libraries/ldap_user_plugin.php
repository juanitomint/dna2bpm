<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class ldap_user_plugin extends User {

    function __construct() {
        $this->config->load('user/ldap');
        parent::__construct();
    }

    function genid() {
//parent::genid();
        $ldapconn = $this->connect();
        $ldapbind = ldap_bind($ldapconn, $this->config->item('ldaprdn'), $this->config->item('ldappass')) or die("Could not bind with password to: " . $this->config->item('ldaprdn'));
        $filter = '(cn=*)';
        $result = ldap_search($ldapconn, $this->config->item('rootDN'), $filter, array('cn', 'uidnumber')) or die("Search error.");
        ldap_sort($ldapconn, $result, 'uidnumber');
        $data = ldap_get_entries($ldapconn, $result);
        $max = array();
        for ($i = 0; $i < $data["count"]; $i++) {

            if (isset($data[$i]['uidnumber']))
                $max[] = (int) $data[$i]['uidnumber'][0];
        }
        return max($max) + 1;
    }

    function rmap($user, $isExternal = null) {
        $map = $this->config->item('user_map');
        $ldap_entry = ($isExternal) ? $this->config->item('userExt_template') : $this->config->item('user_template');
        foreach ($map as $ukey => $ldapkey) {
            if ($ldapkey)
                $ldap_entry[$ldapkey] = (isset($user[$ukey])) ? $user[$ukey] : '';
        }

        return $ldap_entry;
    }

    function add($user_data, $isExternal = null) {
        $ldapconn = $this->connect();
        $ldapbind = ldap_bind($ldapconn, $this->config->item('ldaprdn'), $this->config->item('ldappass')) or die("Could not bind with password to: " . $this->config->item('ldaprdn'));
        //@todo add user to ldap directory
        //----add user to groups
        $user_data['idu'] = (isset($user_data['idu']) && $user_data['idu'] <> '') ? $user_data['idu'] : -1;
        //var_dump($user_data);
        $user_entry = $this->rmap($user_data, $isExternal);
        $user_dn = (isset($user_data['dn'])) ? $user_data['dn'] : 'uid=' . $user_entry['uid'] . ',' . $this->config->item('baseDN');
        //Add user
        if (!$this->get_user_byDN($user_dn)) {
            $user_entry['uidnumber'] = (int) $this->genid();
            //---set default group
            $user_entry['objectClass'] = (isset($user_data['objectClass'])) ? $user_data['objectClass'] : $user_entry['objectClass'];
            $user_entry['gidNumber'] = (isset($user_data['gidNumber'])) ? $user_data['gidNumber'] : (int) $this->config->item('userDefaultGidnumber');
            $user_entry['homeDirectory'] = $this->config->item('home') . $user_entry['uid'];
            $user_entry['givenName'] = $user_entry['cn'] . ' ' . $user_entry['sn'];
            $user_data['idu'] = $user_entry['uidnumber'];
            $user_dn = (isset($user_data['dn'])) ? $user_data['dn'] : 'uid=' . $user_entry['uid'] . ',' . $this->config->item('baseDN');
            $res = ldap_add($ldapconn, $user_dn, $user_entry);
            if (!$res) {
                $errstr = ldap_error($ldapconn);
                echo "Ldap error: $errstr<p>";
                return FALSE;
            }
        } else {
            //users exists
        }
        if ($user_data['group']) {
            $group = explode(',', $user_data['group']);
            if (count($group)) {
                $this->group->remove_user($user_dn);
                //----update user groups
                foreach ($group as $gidNumber) {
                    $group_dn = $this->group->get_DN_byid($gidNumber);
                    //$filter = "(|(member=" . $user_dn . ")(uniqueMember=" . $user_dn . "))";
                    $filter = "(" . $this->config->item('member_attr') . '=' . $user_dn . ")";
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
            }//---end if count
        }
        return $this->get_user_byDN($user_dn);
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
            $ldapbind = ldap_bind($ldapconn, $dn, $password);
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
        $filter = "(" . $this->config->item('member_attr') . '=' . $dn . ")";
        $result = ldap_search($ldapconn, $this->config->item('groupsDN'), $filter, array('dn'), 1) or die("Search error.");
        $info = ldap_get_entries($ldapconn, $result);
        $groups = array();
        for ($j = 0; $j < $info["count"]; $j++) {
            $groups[] = $this->get_group_id_byDN($info[$j]['dn']);
            //$groups[] = $info[$info[$j]][0];
        }
        return $groups;
    }

    function get_users($offset = 0, $limit = 50, $order = null, $query_txt = null, $idgroup = null, $match = 'both') {
        $ldapconn = $this->connect();
        $ldapbind = ldap_bind($ldapconn, $this->config->item('ldaprdn'), $this->config->item('ldappass')) or die("Could not bind with password to: " . $this->config->item('ldaprdn'));
        $filter = array(
                // "(objectclass=*)"
        );

        if ($query_txt) {
            $filter[] = "(uid=$query_txt*)";
            $filter[] = "(cn=*$query_txt*)";
        } else {
            $filter[] = "(uid=*)";
        }
        $filter_str = '(|' . implode('', $filter) . ')';
//        echo $filter_str . '<hr/>';
//        var_dump($query_txt == null);
        if ($idgroup && $query_txt == null) {
            $filter = "(gidnumber=$idgroup)";
            $result = ldap_search($ldapconn, $this->config->item('groupsDN'), $filter, array()) or die("Search error.");
            $data = ldap_get_entries($ldapconn, $result);
            $groups = array();
            $data = $data[0];
            $ldap_users = (isset($data[$this->config->item('member_attr')])) ? $data[$this->config->item('member_attr')] : array();
            unset($ldap_users['count']);
            $ldap_users = array_slice($ldap_users, $offset, $limit);
            $users = array();
            foreach ($ldap_users as $dn) {
                $users[] = $this->get_user_byDN($dn);
            }
        } else {
            $result = ldap_search($ldapconn, $this->config->item('baseDN'), $filter_str, array('dn')) or die("Search error.");
            $data = ldap_get_entries($ldapconn, $result);
            $groups = array();
            $users = array();
            $max = min(ldap_count_entries($ldapconn, $result), $limit);
            for ($i = $offset; $i < ldap_count_entries($ldapconn, $result); $i++) {
                if ($idgroup) {
                    if (in_array($idgroup, $this->get_user_groups($data[$i]['dn'])))
                        $users[] = $this->get_user_byDN($data[$i]['dn']);
                } else {
                    $users[] = $this->get_user_byDN($data[$i]['dn']);
                }
            }
        }
        //$users= array_slice($users, $offset, $limit);
        return array_filter($users);
    }

    function get_users_count($query_txt = null, $idgroup = null, $match = 'both') {
        $ldapconn = $this->connect();
        $ldapbind = ldap_bind($ldapconn, $this->config->item('ldaprdn'), $this->config->item('ldappass')) or die("Could not bind with password to: " . $this->config->item('ldaprdn'));
        $filter = array(
                // "(objectclass=*)"
        );

        if ($query_txt) {
            $filter[] = "(uid=$query_txt*)";
        } else {
            $filter[] = "(uid=*)";
        }
        $filter_str = '(|' . implode('', $filter) . ')';
//        echo $filter_str . '<hr/>';
         if ($idgroup && $query_txt == null) {
            $filter = "(gidnumber=$idgroup)";
            $result = ldap_search($ldapconn, $this->config->item('groupsDN'), $filter, array()) or die("Search error.");
            $data = ldap_get_entries($ldapconn, $result);
            $data = $data[0];
            $ldap_users = (isset($data[$this->config->item('member_attr')])) ? $data[$this->config->item('member_attr')] : array();
            unset($ldap_users['count']);
            $users = array();
            foreach ($ldap_users as $dn) {
                $users[] = $dn;
            }
        } else {
        $result = ldap_search($ldapconn, $this->config->item('baseDN'), $filter_str, array('dn')) or die("Search error.");
        $data = ldap_get_entries($ldapconn, $result);
        $groups = array();
        $users = array();
        for ($i = 0; $i < ldap_count_entries($ldapconn, $result); $i++) {
            $user = $this->get_user_byDN($data[$i]['dn']);
            if ($idgroup) {
                if (in_array($idgroup, $this->get_user_groups($data[$i]['dn'])))
                    $users[] = $data[$i]['dn'];
            } else {
                $users[] = $data[$i]['dn'];
            }
        }
        
            }
        return count($users);
    }

    function get_user($iduser) {
//*        $user = array();
        $ldapconn = $this->connect();
        $ldapbind = ldap_bind($ldapconn, $this->config->item('ldaprdn'), $this->config->item('ldappass')) or die("Could not bind with password to: " . $this->config->item('ldaprdn'));
        $filter = "(uidNumber=$iduser)";
        $result = ldap_search($ldapconn, $this->config->item('rootDN'), $filter) or die("Search error.");
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
        $result = @ldap_read($ldapconn, $dn, $filter, array());
        if ($result) {
            $info = ldap_get_entries($ldapconn, $result);
            if ($info['count']) {
                return $this->prepare($info);
            }
        } else {
            return false;
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
