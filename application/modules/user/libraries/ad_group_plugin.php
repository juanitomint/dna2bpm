<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class ad_group_plugin extends Group {

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

    function get_DN_byid($group) {
        $ldapconn = $this->connect();
        $ldapbind = ldap_bind($ldapconn, $this->config->item('ldaprdn'), $this->config->item('ldappass')) or die("Could not bind with password to: " . $this->config->item('ldaprdn'));
        $filter = "(gidnumber=$group)";
        $result = ldap_search($ldapconn, $this->config->item('groupsDN'), $filter, array('dn')) or die("Search error.");
        $info = ldap_get_entries($ldapconn, $result);
        if ($info['count']) {
            return $info[0]['dn'];
        }
    }

    function get($gidnumber) {
        $ldapconn = $this->connect();
        $ldapbind = ldap_bind($ldapconn, $this->config->item('ldaprdn'), $this->config->item('ldappass')) or die("Could not bind with password to: " . $this->config->item('ldaprdn'));
        $filter = "(gidnumber=$gidnumber)";
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

            if (isset($data[$i][$this->config->item('member_attr')])) {
                for ($j = 0; $j < $data[$i][$this->config->item('member_attr')]["count"]; $j++) {

                    $thisgroup['users'][] = $this->user->get_user_id_byDN($data[$i][$this->config->item('member_attr')][$j]);
                }
            }

            return $thisgroup;
        }
    }

    function remove_user($user_dn) {
        $ldapconn = $this->connect();
        $ldapbind = ldap_bind($ldapconn, $this->config->item('ldaprdn'), $this->config->item('ldappass')) or die("Could not bind with password to: " . $this->config->item('ldaprdn'));
        $groups = $this->get_groups();
        $ldap_obj = array(
            'uniqueMember' => $user_dn
        );
        foreach ($groups as $group) {
            @ldap_mod_del($ldapconn, $group['dn'], $ldap_obj);
        }
        $ldapbind = ldap_bind($ldapconn, $this->config->item('ldaprdn'), $this->config->item('ldappass')) or die("Could not bind with password to: " . $this->config->item('ldaprdn'));
    }

    function get_groups($order = null, $query_txt = null) {
        parent::get_groups();
        $ldapconn = $this->connect();
        $ldapbind = ldap_bind($ldapconn, $this->config->item('ldaprdn'), $this->config->item('ldappass')) or die("Could not bind with password to: " . $this->config->item('ldaprdn'));
        $filter = ($query_txt) ? "(cn=$query_txt)" : '(cn=*)';
        $result = ldap_search($ldapconn, $this->config->item('groupsDN'), $filter, array()) or die("Search error.");
        $data = ldap_get_entries($ldapconn, $result);
        $groups = array();
//var_dump($data);echo "<hr/>";
        for ($i = 0; $i < $data["count"]; $i++) {
//----map to unified group object
            $thisgroup = array(
                'dn' => $data[$i]["dn"],
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

    function save($group) {
//parent::save($group);
        $ldapconn = $this->connect();
        $ldapbind = ldap_bind($ldapconn, $this->config->item('ldaprdn'), $this->config->item('ldappass')) or die("Could not bind with password to: " . $this->config->item('ldaprdn'));
        //create parent ou
        $path=  explode('/', $group['name']);
        $groupname=  array_pop($path);
        $dn= $this->config->item('groupsDN');
        if(count($path)){
            foreach($path as $part){
                $dn='ou=' . $part. ',' . $dn;
                $entry['objectClass'][0]='organizationalUnit';
                $entry['objectClass'][1]='top';
                $entry['ou']=$part;
                @ldap_add($ldapconn,$dn, $entry); 
            }
            
        }
        $group_dn = 'cn=' . $groupname . ',' . $dn;
//---Add the user to the group
        $group_obj = $this->config->item('group_template');
        $group_obj['gidnumber'] = $group['idgroup'] . '';
        $group_obj['cn'] = $groupname;
        $res = ldap_add($ldapconn, $group_dn, $group_obj);
        if ($res) {
            return TRUE;
        } else {
            $errstr = ldap_error($ldapconn);
            echo "Ldap error: $errstr<p>";
            return FALSE;
        }
    }

    function delete($gidnumber) {
        $ldapconn = $this->connect();
        $ldapbind = ldap_bind($ldapconn, $this->config->item('ldaprdn'), $this->config->item('ldappass')) or die("Could not bind with password to: " . $this->config->item('ldaprdn'));
        $dn = $this->get_DN_byid($gidnumber);
        $res=ldap_delete($ldapconn, $dn);
        if ($res) {
            return TRUE;
        } else {
            $errstr = ldap_error($ldapconn);
            echo "Ldap error: $errstr<p>";
            return FALSE;
        }
    }

    function genid() {
//parent::genid();
        $ldapconn = $this->connect();
        $ldapbind = ldap_bind($ldapconn, $this->config->item('ldaprdn'), $this->config->item('ldappass')) or die("Could not bind with password to: " . $this->config->item('ldaprdn'));
        $filter = '(cn=*)';
        $result = ldap_search($ldapconn, $this->config->item('groupsDN'), $filter, array('cn', 'gidnumber')) or die("Search error.");
        ldap_sort($ldapconn, $result, 'gidnumber');
        $data = ldap_get_entries($ldapconn, $result);
        $max = array();
        for ($i = 0; $i < $data["count"]; $i++) {
            $max[] = (int) $data[$i]['gidnumber'][0];
        }
        return max($max) + 1;
    }

    function get_byname($groupname) {
//parent::get_byname($groupname);
        $path=  explode('/', $groupname);
        $groupname=  array_pop($path);
        $ldapconn = $this->connect();
        $ldapbind = ldap_bind($ldapconn, $this->config->item('ldaprdn'), $this->config->item('ldappass')) or die("Could not bind with password to: " . $this->config->item('ldaprdn'));
        $filter = "(cn=$groupname)";
        $result = ldap_search($ldapconn, $this->config->item('groupsDN'), $filter, array()) or die("Search error.");
        $data = ldap_get_entries($ldapconn, $result);
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
            return $thisgroup;
        }
    }
    
    function get_ou_byname($ouname) {
//parent::get_byname($groupname);
        $ldapconn = $this->connect();
        $ldapbind = ldap_bind($ldapconn, $this->config->item('ldaprdn'), $this->config->item('ldappass')) or die("Could not bind with password to: " . $this->config->item('ldaprdn'));
        $filter = "(ou=$ouname)";
        $result = ldap_search($ldapconn, $this->config->item('groupsDN'), $filter, array()) or die("Search error.");
        $data = ldap_get_entries($ldapconn, $result);
        for ($i = 0; $i < $data["count"]; $i++) {
            return $data[$i];
        }
    }

}
