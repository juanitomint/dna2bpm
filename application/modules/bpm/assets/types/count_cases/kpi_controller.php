<?php

/*
 * KPI STATE Controller function
 * This file contains the function necesary to filter cases by a shape state
 * 
 * if you want to collect the cases matched just pass true for $list parameter
 * @author Borda Juan Ignacio
 * 
 * @version 	1.0 (2014-03-24)
 * 
 */

function count_cases($kpi, $CI, $list = null) {
    $filter = $CI->get_filter($kpi);
    $tokens = $CI->bpm->get_tokens_byResourceId($kpi['resourceId'], $filter);
    $cpData = $kpi;
    $cpData['base_url'] = $CI->base_url;
    $cpData['module_url'] = $CI->module_url;
    //var_dump($tokens);
    $cpData['number'] = count($tokens);
    $cpData['more_info_class'] = "load_tiles_after";
    $cpData['more_info_link'] = $CI->base_url."bpm/kpi/list_cases/".$kpi['idkpi'];
    if (!$list) {
        $rtn = $CI->parser->parse('dashboard/'.$kpi['widget_type'].'/'.$kpi['widget'], $cpData, true);
        return $rtn;
    } else { //----return cases matched
        //---map tokens to get case
        $cases = array_map(function ($token) {
            return $token['case'];
        }, $tokens);
        return $cases;
    }
}
