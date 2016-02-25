<?php

function edit_subformparent($frame, $value) {
    $CI = & get_instance();
    return '------ subformparent';
        
    $retstr = '';
    $id = $CI->dna_id;
    $form = $CI->app->get_object($frame['object']);
    $parents = $CI->app->getall($id, $frame['container'], 'parent');
    $value = (isset($parents['parent'][$form['container']])) ? $parents['parent'][$form['container']] : null;
// TODO Fetch parents but HOW?
    $form = $CI->app->get_object($frame['object']);
    $frame['nosubhead'] = true;
    //$frame['nobrowse'] = true;
    $frame['nodelete'] = true;
    //$retstr.= json_encode($value);
    //var_dump($frame);
    $retstr.=edit_subform($frame, $value);
    return $retstr;
}

function view_subformparent($frame, $value) {
    $CI = & get_instance();
    $retstr = '';
    $id = $CI->dna_id;
    $form = $CI->app->get_object($frame['object']);
    $parents = $CI->app->getall($id, $frame['container'], 'parent');
    $value = (isset($parents['parent'][$form['container']])) ? $parents['parent'][$form['container']] : null;
    $form = $CI->app->get_object($frame['object']);

    $frame['nosubhead'] = true;
    $frame['nobrowse'] = true;
    $frame['nodelete'] = true;
    //$retstr.= json_encode($value);
    $retstr.=edit_subform($frame, $value);
    return $retstr;
}
?>
