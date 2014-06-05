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
    $CI = &get_instance();
    if (!is_array($array))
        return false;
    //---Setings
    $expanded = false;
    $leafCls = 'dot-green';
    $splitRE = '/' . preg_quote($delimiter, '/') . '/';
    $returnArr = array((object) array(
            "id" => 'root',
            "text" => "Object Repository",
            "cls" => "folder",
            "icon-cls" => "icon-home",
            "expanded" => true,
            "leaf" => false,
            "path" => '/root',
            "children" => array(),
    ));
    foreach ($array as $thispath => $val) {
        // Get parent parts and the current leaf
        //$parts = preg_split($splitRE, $key, -1, PREG_SPLIT_NO_EMPTY);
        // Build parent structure
        //$thispath = '/root/' . $key;
        $path_arr = explode($delimiter, $thispath);
        array_pop($path_arr);
        $thisparentpath = '/' . implode('/', $path_arr);
        //prepare object to add
        $obj = (object) array_merge(array(
                    'id' => $val['id'],
                    'text' => $val['text'],
                    'priority' => (isset($val['priority'])) ? $val['priority'] : 10,
                    'leaf' => false,
                    'path' => $thispath,
                    'expanded' => true,
                    'children' => array()
                        //'checked' => false,
                        ), (array) $val);
        $obj->leaf=false;
        //$obj->data = $val;
        //---set the internal pointer to the parent
        $pointer = search($returnArr, 'path', $thisparentpath);
        //----if parent exists (we start with 1 root so has to exists but just in case...)
        if ($pointer) {
            $pointer['leaf'] = false;
            $pointer['expanded'] = $expanded;
            $pointer['children'][] = $obj;
        }
    }
    return $returnArr;
}

/*
 *  This function returns a pointer to the part of the array matching key=>value
 */

function search(&$arr, $key, $value) {
    $arrIt = new RecursiveIteratorIterator(new RecursiveArrayIterator($arr));
    foreach ($arrIt as $sub) {
        $subArray = $arrIt->getSubIterator();
        $subArray->jj = true;
        if (isset($subArray[$key]) && $subArray[$key] == $value) {
            //return iterator_to_array($subArray);
            return $subArray;
        }
    }
    return null;
}
