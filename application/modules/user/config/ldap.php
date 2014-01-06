<?php

//$config['groupAdmin'] = 1000;
//$config['ldap_server'] = '192.168.1.11';
//$config['ldap_port'] = '390';
//$config['ldaprdn'] = 'cn=zentyal,dc=s1,dc=local';
//$config['ldappass'] = 'yzNRgV8fP4gL@eKCKfkq';
//$config['baseDN'] = "ou=Users,dc=s1,dc=local";
//$config['groupsDN'] = "ou=Groups,dc=s1,dc=local";
//$config['ldap_use_groups'] = true;
//-----OpenDS local
$config['ldap_server'] = '127.0.0.1';
$config['ldap_port'] = '1389';
$config['ldaprdn'] = 'cn=root';
$config['ldappass'] = 'root';
$config['baseDN'] = "ou=User,dc=mp,dc=gba,dc=gov,dc=ar";
$config['groupsDN'] = "ou=Groups,dc=mp,dc=gba,dc=gov,dc=ar";
$config['ldap_use_groups'] = true;

//-----Override GroupAdmin
$config['groupAdmin'] = 1000;
//-----set member Attributo to search or save members
//$config['member_attr']='member';  //<------Zentyal
$config['member_attr'] = 'uniquemember'; //<----OpenDs
$config['group_type'] = 'uniquemember'; //<----OpenDs

$config['user_map'] = array(
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
    "phone" => "telephonenumber",
);
$config['group_map'] = array(
    'idgroup' => 'gidnumber',
    "name" => "cn",
);


$config['user_template'] = array(
    'objectClass' => array(
        0 => 'person',
        1 => 'inetorgperson',
        2 => 'organizationalperson',
        3 => 'top'
    )
);
$config['group_template'] = array(
    'objectClass' => array(
        0 => 'groupOfUniqueNames',
        1 => 'posixGroup',
        2 => 'top'
    )
);
//$config['ldap_server'] = 'ldap.mp.gba.gov.ar';
//$config['ldap_port']= '389';
//$config['ldaprdn']= 'cn=admin,dc=mp,dc=gba,dc=gov,dc=ar';
//$config['ldappass'] = 'root';
//$config['baseDN']= "ou=People,dc=mp,dc=gba,dc=gov,dc=ar";
//$config['groupsDN']= "ou=Groups,dc=mp,dc=gba,dc=gov,dc=ar";

