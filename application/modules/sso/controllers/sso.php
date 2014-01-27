<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/**
 * sso
 * 
 * Description of the class
 * 
 * @author Juan Ignacio Borda <juanignacioborda@gmail.com>
 * @date    Jan 14, 2013
 */
class sso extends CI_Controller {

    function __construct() {
        parent::__construct();
        //$this->load->library('user/ldap_user_plugin');
    }

    function test_ldap() {
        echo "<h1>TEST LDAP</h1>";
        $idu = $this->user->authenticate('juanb', '123123');
        if ($idu) {
            var_dump($this->user->get_user($idu));
        }
    }

    function parseLdapDn($dn) {
        $parsr = ldap_explode_dn($dn, 0);
        //$parsr[] = 'EE=Sôme Krazï string';
        //$parsr[] = 'AndBogusOne';
        $out = array();
        foreach ($parsr as $key => $value) {
            if (FALSE !== strstr($value, '=')) {
                list($prefix, $data) = explode("=", $value);
//                //$data = preg_replace("/\\\\\\([0-9A-Fa-f]{2})/e", "''.chr(hexdec('\\\\1')).''", $data);
//                if (isset($current_prefix) && $prefix == $current_prefix) {
//                    $out[$prefix][] = $data;
//                } else {
//                    $current_prefix = $prefix;
//                    $out[$prefix][] = $data;
//                }
                $out[$prefix] = $data;
            }
        }
        return $out;
    }

    function zentyal() {
        echo "<h1>SSO</h1>";
        echo "<h3>Consulta de prueba LDAP</h3>";
        echo "Conectando ...";
        $ldap_server = '192.168.1.11';
        $ldap_port = '390';
        $ldaprdn = 'cn=zentyal,dc=s1,dc=local';
        $ldappass = 'yzNRgV8fP4gL@eKCKfkq';
        $baseDN = "ou=Users,dc=s1,dc=local";
        $groupsDN= "ou=Groups,dc=s1,dc=local";
        // Debe ser un servidor LDAP válido!
        $ldapconn = ldap_connect($ldap_server, $ldap_port) or die("Can\'t connect to LDAP Server:$ldap_server on port $ldap_port");

        //-----SETINGS
        ldap_set_option($ldapconn, LDAP_OPT_PROTOCOL_VERSION, 3);
        ldap_set_option($ldapconn, LDAP_OPT_REFERRALS, 0);
        //-----SETINGS
        //
        // realizando la autenticación as ROOT
        //$ldapbind = ldap_bind($ldapconn, $ldaprdn, $ldappass);

        $mail = "jborda@mp.gba.gov.ar";

        $passw = 'test123';
        //---1ro busco el uid del usuario con el 
        // Bind anonymously to the LDAP server to search.
        $ldapbind = ldap_bind($ldapconn) or die("Could not bind anonymously.");
        $ldapbind = ldap_bind($ldapconn, $ldaprdn, $ldappass) or die("Could not bind with password");
        $filter = "(mail=$mail)";
        $result = ldap_search($ldapconn, $baseDN, $filter, array('dn'), 1) or die("Search error.");
        $info = ldap_get_entries($ldapconn, $result);

        if ($info['count']) {
            var_dump($info);
            $dn = $info[0]['dn'];
            // realizando la autenticación as test
            $ldapbind = ldap_bind($ldapconn, $dn, 'jborda1234');
            if ($ldapbind) {
                echo "<h3>AUTH OK!</h3>";
                $dnData = $this->parseLdapDn($dn);
                //search groups where user is member
                $filter = "(member=".$dn.")";
                $result = ldap_search($ldapconn, $groupsDN, $filter, array('dn'), 1) or die("Search error.");
                $info = ldap_get_entries($ldapconn, $result);
                var_dump($info);
//@todo pass data to next step
            } else {
                echo "<h3>AUTH #FAIL</h3>";
            }
        } else {
            echo 'user not found';
        }
    }

    function ldap() {
        echo "<h1>SSO</h1>";
        echo "<h3>Consulta de prueba LDAP</h3>";
        echo "Conectando ...";
        $ldap_server = 'ldap.mp.gba.gov.ar';
        $ldap_port = '390';
        $ldaprdn = 'cn=config,dc=mp,dc=gba,dc=gov,dc=ar';
        $ldappass = 'root';
        $baseDN = "ou=People,dc=mp,dc=gba,dc=gov,dc=ar";
        // Debe ser un servidor LDAP válido!
        $ldapconn = ldap_connect($ldap_server, $ldap_port) or die("Can\'t connect to LDAP Server:$ldap_server on port $ldap_port");
        //-----SETINGS
        ldap_set_option($ldapconn, LDAP_OPT_PROTOCOL_VERSION, 3);
        ldap_set_option($ldapconn, LDAP_OPT_REFERRALS, 0);
        //-----SETINGS
        //
        // realizando la autenticación as ROOT
        //$ldapbind = ldap_bind($ldapconn, $ldaprdn, $ldappass);

        $mail = "juanb";
        $passw = '123123';
        //---1ro busco el uid del usuario con el 
        // Bind anonymously to the LDAP server to search.
        //$ldapbind = ldap_bind($ldapconn) or die("Could not bind anonymously.");
        $ldapbind = ldap_bind($ldapconn, $ldaprdn, $ldappass) or die("Could not bind with password");
        $filter = "(mail=$mail)";
        $result = ldap_search($ldapconn, $baseDN, $filter, array('dn'), 1) or die("Search error.");
        $info = ldap_get_entries($ldapconn, $result);

        if ($info['count']) {
            var_dump($info);
            $dn = $info[0]['dn'];
            // realizando la autenticación as test
            $ldapbind = ldap_bind($ldapconn, $dn, 'test123');
            if ($ldapbind) {
                echo "<h3>AUTH OK!</h3>";
                //@todo pass data to next step
            } else {
                echo "<h3>AUTH #FAIL</h3>";
            }
        } else {
            echo 'user not found';
        }
    }

}

/* End of file welcome.php */
/* Location: ./system/application/controllers/welcome.php */