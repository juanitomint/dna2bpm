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

function state($kpi, $CI, $list = null) {

    $filter = $CI->get_filter($kpi);
    //---add status filter if status has been specified or else return any status
    if (isset($kpi['status'])) {
        if ($kpi['status'] <> '') {
            $filter['status'] = array('$in' => (array) $filter['status']);
        }
    }
    $filter['idwf'] = $kpi['idwf'];
    $allcases = $CI->bpm->get_cases_byFilter($filter, array('id'));
    $cases = array();
    foreach ($allcases as $case) {
        $token = $CI->bpm->get_last_token($kpi['idwf'], $case['id']);
        if ($token) {
            if ($token['resourceId'] == $kpi['resourceId']) { //---the case mathed
                $cases[] = $case;
            }
        }
    }
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
        }, $cases);
        return $cases;
    }
}
