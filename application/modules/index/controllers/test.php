<?php

/**
 * Description of menu
 *
 * @author juanb
 * @date   Jan 7, 2014
 * 
 */
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Test extends MX_Controller {

    function __construct() {
        parent::__construct();
        $this->load->model('index/menu');
        $this->load->helper('ext');
    }

    /*
     * Main function if no other invoked
     */

    function Index() {
        $m = $this->menu->get_repository();
        //var_dump($m);
        $tree = $this->menu->explodeExtTree($m);
        var_dump($tree[0]->children);
        $menu = $this->get_ul($tree[0]->children);
        echo $menu;
    }

    function get_ul($menu, $ulClass = '') {

        $returnStr = '';
        $returnStr.='<ul class="' . $ulClass . '">';
        foreach ($menu as $path => $node) {

            if (property_exists($node, 'data')) {
                $item = $node->data;
            } else {
                $item = array(
                    'target' => '#',
                    'title' => '',
                    'text' => $node->text,
                );
            }
            $returnStr.='<li>';

            $returnStr.='<a href="' . $item['target']. '" title="' . $item['title'] . '">' . $item['text'];
            if (isset($item['iconCls'])) {
                if ($item['iconCls'] <> '')
                    $returnStr.='<i class="icon ' . $item['iconCls'] . '"></i>';
            }

            $returnStr.='</a>';
            if (!$node->leaf) {
                $returnStr.=$this->get_ul($node->children, $ulClass);
            }
            $returnStr.='</li>';
        }
        $returnStr.='</ul>';
        return $returnStr;
    }

}