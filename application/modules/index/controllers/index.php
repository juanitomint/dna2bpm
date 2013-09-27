<?php

/**
 * Index
 * 
 * This class glue all the modules together thru their menu controllers
 * 
 * @author Juan Ignacio Borda <juanignacioborda@gmail.com>
 * @date   Sep 27, 2013
 * 
 */
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Index extends MX_Controller {

    function __construct() {
        parent::__construct();
    }

    function Index() {
        //-- get all modules
        $modules = $this->get_dirs(APPPATH . 'modules/');
        $menuArr= $this->getModulesWithMenu(APPPATH . 'modules/');
        var_dump(APPPATH . 'modules', $modules,$menuArr);
        foreach ($menuArr as $module){
            $menuPath=$module.'/menu/get';
            $menuItem=modules::run($menuPath);
            var_dump($menuItem);
                    
        }
        
    }

    private function get_dirs($path) {
        $dirArr = array();
        if ($handle = opendir($path)) {
            while (false !== ($entry = readdir($handle))) {
                if ($entry != "." && $entry != "..") {
                    $dirArr[] = $entry;
                }
            }
            closedir($handle);
        }
        return $dirArr;
    }

    private function getModulesWithMenu($path) {
        $dirArr = array();
        if ($handle = opendir($path)) {
            while (false !== ($entry = readdir($handle))) {
                if ($entry != "." && $entry != "..") {
                    //echo 'Testing:'.$path .$entry.'/controllers/menu.php<br/>';
                    if (is_file($path .$entry.'/controllers/menu.php')) {
                        $dirArr[] = $entry;
                    }
                }
            }
            closedir($handle);
        }
        return $dirArr;
    }

}