<?php

$autoload['models'] = array(
    'user',
    'group',
);
$autoload['libraries'] = array(
    'mongo',
    'session',
    'ui',
    'user/ldap_plugin',
    //----uncomment this line to auth from ldap
    //'user/ldap_user_plugin'
    
);
?>
