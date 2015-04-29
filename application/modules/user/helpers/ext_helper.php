<?php

/**
 * Ext JS helper functions
 */

function convert_to_ext($array) {
    $rtn_arr = array();
    foreach ($array as $key => $value) {
        if (is_array($value)) {
            asort($value);
            $rtn_arr[] = array(
                'text' => $key,
                'leaf' => false,
                'checked' => false,
                //'expanded' => true,
                'children' => array_filter(convert_to_ext($value))
            );
            //$id++;
        } else {
            $rtn_arr[] = array(
                'text' => "$key",
                'leaf' => true,
                'checked' => false,
                'data' => $value,
                'cls' => 'task'
            );
        }
    }
    return array_filter($rtn_arr);
}

function explodeTree($array, $delimiter = '/') {
    if (!is_array($array))
        return false;
    $splitRE = '/' . preg_quote($delimiter, '/') . '/';
    $returnArr = array();
    foreach ($array as $key => $val) {
        $parts = preg_split($splitRE, $key, -1, PREG_SPLIT_NO_EMPTY);
        $parentArr = &$returnArr;
        foreach ($parts as $part) {
            if (!isset($parentArr[$part])) {
                $parentArr[$part] = array();
            }
            $parentArr = &$parentArr[$part];
        }

        // Add the final part to the structure
        if (empty($parentArr[$part])) {
            //$parentArr[$part] = $val;
        }
    }
    //---order by name
    asort($returnArr);
    return array_filter($returnArr);
}

function explodeExtTree($array, $delimiter = '/') {
    if (!is_array($array))
        return false;
        
    $debug=false;
    //---Setings
    $expanded=false;
    $leafCls='dot-green';
    $splitRE = '/' . preg_quote($delimiter, '/') . '/';
    $pointer_index=array();
    
     $root= (object) array(
            "id" => 'root',
            "text" => "Object Repository",
            "cls" => "folder",
            "expanded" => true,
            "checked" => false,
            );
    //---save index to object            
    $pointer_index['root']=&$root;
    $rtnArr=array(&$root);
    foreach ($array as $key => $val) {
        // Get parent parts and the current leaf
        $parts = explode('/', $key);
        // Build parent structure
        $localpath = array('root');
        $cachepath = array();
        foreach ($parts as $part) {
            $parentArr = &$returnArr;
            $thisparentpath = implode($delimiter, $localpath);
            $localpath[] = $part;
            $thispath = implode($delimiter, $localpath);
            $isleaf = ($thispath =='root/'. $key) ? true : false;
            //prepare object to add
            $obj = (object) array(
                        'id' => $thispath,
                        'text' => $part,
                        'leaf' => $isleaf,
                        'checked' => false,
                        
            );
            if ($isleaf) {
                $obj->iconCls=$leafCls;
                $obj->data = $val;   
            }
            //---set the internal pointer to the parent
            $pointer =&$pointer_index[$thisparentpath];
            //----if parent exists (we start with 1 root so has to exists but just in case...)
            if ($pointer) {
                if($debug)
                    echo "Pointer Found: $thisparentpath<br/>";
                
                $pointerChild = &$pointer_index[$thispath];
                //---check if child exists
                if (!$pointerChild){
                //---adds object to pointer index
                $pointer_index[$thispath]=$obj;
                if($debug)
                     echo "No PointerChild: $thispath<br/>";
                    $pointer->leaf=false;
                    $pointer->expanded = $expanded;
                    //---check if object has childrens
                    if(property_exists($pointer,'children')){
                        if($debug)
                            echo "Childrens<br/>";
                        $pointer->children[] = &$pointer_index[$thispath];
                    } else {
                        if($debug)
                            echo "no Childrens<br/>";
                        $pointer->children= array(&$pointer_index[$thispath]);
                    }
                    
                } else {
                    if($debug)
                        echo "PointerChild: $thispath<br/>";
                    
                }
            }
            if($debug){
                echo "<hr/>";
                echo "Parent: $thisparentpath<br/>";
                echo "Child: $thispath<br/>";
                var_dump($pointer_index);
            }
        }
    }
    return $rtnArr;
}