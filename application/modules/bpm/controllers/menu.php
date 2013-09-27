<?php

/**
 * bpm
 * 
 * Description of the class bpm
 * 
 * @author Juan Ignacio Borda <juanignacioborda@gmail.com>
 * @date   Feb 10, 2013
 */
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Menu extends MX_Controller {

    public $tree = array(
        'title' => 'BPM', //----Section/module
        'target' => '/bpm',
        'text' => 'BPM Index',
        'cls' => '',
        'iconCls' => 'icon-bpm',
        'order' => 0,
        'info' => '15/32', //---Icon
        'items' => array(
            array(
                'title' => 'BPM Models admin',
                'target' => '/bpm/browser',
                'text' => 'BPM Browser',
                'cls' => '',
                'iconCls' => 'icon-bpm',
                'order' => 0,
            ),
        )
    );

    function Index() {
        $this->test();
    }

    function Get() {
        return $this->tree;
    }

    function Get_json() {
        header('Content-type: application/json;charset=UTF-8');
        echo json_encode($this->tree);
    }

    function dump() {
        var_dump($this->tree);
    }

}
