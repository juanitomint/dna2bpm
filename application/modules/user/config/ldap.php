<?php
$config['groupAdmin']	= 1000;
$config['ldap_server']= '192.168.1.11';
$config['ldap_port']= '390';
$config['ldaprdn']= 'cn=zentyal,dc=s1,dc=local';
$config['ldappass']= 'yzNRgV8fP4gL@eKCKfkq';
$config['baseDN']= "ou=Users,dc=s1,dc=local";
$config['groupsDN']= "ou=Groups,dc=s1,dc=local";
$config['ldap_use_groups']=true;
//-----OpenDS local
$config['ldap_server']= '127.0.0.1';
$config['ldap_port']= '1389';
$config['ldaprdn']= 'cn=root';
$config['ldappass']= 'root';
$config['baseDN']= "ou=User,dc=mp,dc=gba,dc=gov,dc=ar";
$config['groupsDN']= "ou=Groups,dc=mp,dc=gba,dc=gov,dc=ar";
$config['ldap_use_groups']=true;

//$config['ldap_server'] = 'ldap.mp.gba.gov.ar';
//$config['ldap_port']= '389';
//$config['ldaprdn']= 'cn=admin,dc=mp,dc=gba,dc=gov,dc=ar';
//$config['ldappass'] = 'root';
//$config['baseDN']= "ou=People,dc=mp,dc=gba,dc=gov,dc=ar";
//$config['groupsDN']= "ou=Groups,dc=mp,dc=gba,dc=gov,dc=ar";

