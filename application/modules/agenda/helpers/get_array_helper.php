<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');?>
<?php

            
            
if ( ! function_exists('get_array'))
{
	function get_array($tree)
	{

            return new_folder(0, $tree); // Inicio el recursivo
            
            
//            foreach($mytree as $k=>$v){      
//                echo "$k $v<br>";
//                foreach($v as $k1=>$v1){
//                    echo "-- $k1 $v1<br>";
//                        foreach($v1 as $k2=>$v2){
//                            echo "---- $k2<br>";
//                        }
//                }
//            }
	}
}



if ( ! function_exists('new_folder'))
{
	function new_folder($f,$tree)
	{
        reset($tree);
        $myitems=array();
        
            foreach($tree as $k=>$v){          
                if($v['parent']==$f){
                        $myitems[$k]=new_folder($k,$tree);
                }
            }
            
           return $myitems;
	}
}


?>
