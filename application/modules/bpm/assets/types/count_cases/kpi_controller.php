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
    $cpData['count'] = count($tokens);
    if (!$list) {
        $rtn = $CI->parser->parse('bpm/kpi_count', $cpData, true);
        return $rtn;
    } else { //----return cases matched
        //---map tokens to get case
        $cases = array_map(function ($token) {
            return $token['case'];
        }, $tokens);
        return $cases;
    }
}
