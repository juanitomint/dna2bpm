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

function time_avg_all($kpi, $CI, $list = null) {

    $filter['status']='closed';
    $filter['idwf']=$kpi['idwf'];
    $cases=$CI->bpm->get_cases_byFilter($filter,array('id'));
    $cpData = $kpi;
    
    $cpData['base_url'] = $CI->base_url;
    $cpData['module_url'] = $CI->module_url;
    //var_dump($tokens);
    //$cpData['tokens']=$tokens;
    $cpData['count'] = count($cases);
    if (!$list) {
        $rtn = $CI->parser->parse('bpm/kpi_count', $cpData, true);
        return $rtn;
    } else { //----return cases matched
        //---map tokens to get case
        $cases = array_map(function ($case) {
            return $case['id'];
        }, $tokens);
        return $cases;
    }
}
