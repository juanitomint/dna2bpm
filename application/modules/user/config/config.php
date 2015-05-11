<?php
$config['show_warn']	= true;
//---run mode development / test /  production
$config['run_mode']	= 'development';
$config['default_controller']	= 'dashboard';
//----The group which holds the system administrators
$config['groupAdmin']	= 1;
//----The group which holds the unpriveleged users
$config['groupUser']=1000;
//----default autodicover policy set to false for production
 $config['autodiscover']	= true;
//----set if you want to use a plugin
$config['user_plugin']=array(
    'cimongo'
    //'ldap_auth',
    //'mysql',
    );
