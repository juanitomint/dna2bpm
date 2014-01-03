<?php
$config['show_warn']	= true;
//---run mode development /  production
$config['run_mode']	= 'development';
$config['default_controller']	= 'dna2/dashboard';
$config['groupAdmin']	= 1;

//----set if yopu want to use a plugin
$config['user_plugin']=array(
    'ldap_auth',
    //'mysql',
    );
