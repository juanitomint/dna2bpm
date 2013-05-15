<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');?>
<?php

            
            
if ( ! function_exists('get_tree'))
{
	function get_tree($tree,$folders_list)
	{

           header("Content-type:text/xml"); print("<?xml version=\"1.0\"?>");

            $todo=array();
            $mis_folders=array();

            foreach($tree as $k=>$v){
                $todo[$k]['folder']=0;
                $todo[$k]['nombre']=$v['nombre'];
                $todo[$k]['parent']=$v['parent'];
                
                $f=$v['parent'];
                // Elimino folders vacios
                while($f!=0){
                    $mis_folders[$f]=$f;
                    $f=$folders_list[$f]['parent'];
                }
            }
            foreach($folders_list as $k=>$v){
                if(in_array($k,$mis_folders)){
                $todo[$k]['folder']=1;
                $todo[$k]['nombre']=$v['nombre'];
                $todo[$k]['parent']=$v['parent'];
                }
            }


            new_folder(0, $todo); // Inicio el recursivo
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
	function new_folder($f,$todo)
	{
       reset($todo);
       //echo "--folder $f \n";
       if($f){
        echo "<item id=\"$f\" text=\"".$todo[$f]['nombre']."\" checked=\"1\"  >\n";
       }else{
        echo "<tree id=\"$f\" text=\"0\" checked=\"1\"  >\n";
       }

        foreach($todo as $k=>$v){
            
            if($v['parent']==$f){
                // Es hijo , lo meto        
                if($v['folder']){
                    // es folder , nos metemos
                    new_folder($k,$todo);
                }else{
                    new_node($k,$v['nombre']);
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
