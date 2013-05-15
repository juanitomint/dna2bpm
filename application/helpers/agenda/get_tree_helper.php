<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');?>
<?php

if ( ! function_exists('get_tree'))
{
	function get_tree($tree,$folders)
	{
            
            header("Content-type:text/xml"); print("<?xml version=\"1.0\"?>");

            $folders_list=array();
            $rs_nombre=array();
            $rs_parent=array();

             foreach($folders as $folder){
                 $folders_list[]=$folder["parent"];
             }

             foreach($tree as $item){
               $rs_nombre[$item["id"]]=$item["nombre"];
               $rs_parent[$item["id"]]=$item["parent"];
             }

            new_folder(0, $rs_nombre,$rs_parent,$folders_list); // Inicio el recursivo

	}
}

if ( ! function_exists('new_node'))
{
	function new_node($n,$nombre)
	{
        echo "<item id=\"$n\" text=\"$nombre\" checked=\"1\"  tooltip=\"$nombre\" />\n";
	}
}

if ( ! function_exists('new_folder'))
{
	function new_folder($f,$rs_nombre,$rs_parent,$folders_list)
	{
       reset($rs_parent);

       if($f){
        echo "<item id=\"$f\" text=\"".$rs_nombre[$f]."\" checked=\"1\"  >\n";
       }else{
        echo "<tree id=\"$f\" text=\"0\" checked=\"1\"  >\n";
       }
        while (list($key, $val) = each($rs_parent)) {
            if($rs_parent[$key]==$f){ // Busca los children
                if(in_array($key,$folders_list)){
                    new_folder($key,$rs_nombre,$rs_parent,$folders_list);
                }else{
                    new_node($key,$rs_nombre[$key]);
                }
            }
        }
       if($f){
        echo "</item>\n";
       }else{
        echo "</tree>\n";
       }
	}
}

?>
