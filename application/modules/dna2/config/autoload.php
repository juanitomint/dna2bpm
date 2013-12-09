<?php

$autoload['models'] = array(
    'user',
    'group',
    'rbac'
);
$autoload['libraries'] = array(
    'mongo',
    'session',
    'user/ldap_plugin',
);
?>
