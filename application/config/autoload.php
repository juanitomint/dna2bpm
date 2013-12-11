<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*
| -------------------------------------------------------------------
| AUTO-LOADER
| -------------------------------------------------------------------
| This file specifies which systems should be loaded by default.
|
| In order to keep the framework as light-weight as possible only the
| absolute minimal resources are loaded by default. For example,
| the database is not connected to automatically since no assumption
| is made regarding whether you intend to use it.  This file lets
| you globally define which systems you would like loaded with every
| request.
|
| -------------------------------------------------------------------
| Instructions
| -------------------------------------------------------------------
|
| These are the things you can load automatically:
|
| 1. Libraries
| 2. Helper files
| 3. Plugins
| 4. Custom config files
| 5. Language files
| 6. Models
|
*/

/*
| -------------------------------------------------------------------
|  Auto-load Libraries
| -------------------------------------------------------------------
| These are the classes located in the system/libraries folder
| or in your system/application/libraries folder.
|
| Prototype:
|
|	$autoload['libraries'] = array('database', 'session', 'xmlrpc');
*/

$autoload['libraries'] = array(
    'mongo',
    'session',      
    //-----if you need full layer support with plugin loader load the user/userlayer library
    //'user/userlayer'
    );

/*
| -------------------------------------------------------------------
|  Auto-load Helper Files
| -------------------------------------------------------------------
| Prototype:
|
|	$autoload['helper'] = array('url', 'file');
*/

$autoload['helper'] = array('url');


/*
| -------------------------------------------------------------------
|  Auto-load Plugins
| -------------------------------------------------------------------
| Prototype:
|
|	$autoload['plugin'] = array('captcha', 'js_calendar');
*/

$autoload['plugin'] = array();


/*
| -------------------------------------------------------------------
|  Auto-load Config files
| -------------------------------------------------------------------
| Prototype:
|
|	$autoload['config'] = array('config1', 'config2');
|
| NOTE: This item is intended for use ONLY if you have created custom
| config files.  Otherwise, leave it blank.
|
*/

$autoload['config'] = array();


/*
| -------------------------------------------------------------------
|  Auto-load Language files
| -------------------------------------------------------------------
| Prototype:
|
|	$autoload['language'] = array('lang1', 'lang2');
|
| NOTE: Do not include the "_lang" part of your file.  For example 
| "codeigniter_lang.php" would be referenced as array('codeigniter');
|
*/

$autoload['language'] = array();


/*
| -------------------------------------------------------------------
|  Auto-load Models
| -------------------------------------------------------------------
| Prototype:
|
|	$autoload['model'] = array('model1', 'model2');
|
*/
//----load the user model from module: 'user'
/* 
 * Actual library provides:
     authenticate($username='', $password='') 
     authenticateByHash($username='', $hash='') 
     getlevel($idu) 
     authorize($reqlevel='') 
     isloggedin() 
     has($piece) 
     getapps($level=null) 
     get_user_apps($level=null) 
     appfilter($chunk) 
     getbyid($iduser) 
     getbypassw($hash) 
     getbynick($nick) 
     getbygroup($idgroup) 
     getbygroupname($groupname) 
     get_user($iduser) 
     get_groups($order=null, $query_txt=null) 
     get_users($idgroup, $order=null, $query_txt=null) 
     put_user($object) 
     save($object) 
     delete_group($idgroup) 
     delete_user($iduser) 
 
 */
$autoload['model'] = array(
    'user/user',
    'user/group',
    'user/rbac',
    );



/* End of file autoload.php */
/* Location: ./system/application/config/autoload.php */