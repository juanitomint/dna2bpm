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
    /*
    //---add status filter if status has been specified or else return any status
    if (isset($kpi['status'])) {
        if ($kpi['status'] <> '') {
            $filter['status'] = array('$in' => (array) $kpi['status']);
        }
    }
     * 
     */
    $filter['idwf'] = $kpi['idwf'];
    $filter['token_status.resourceId']=$kpi['resourceId'];
    //var_dump(json_encode($filter));
    
    ///----way too ineficient
    $cases = $CI->bpm->get_cases_byFilter($filter, array('id'));
    $cpData = $kpi;
    $cpData['base_url'] = $CI->base_url;
    $cpData['module_url'] = $CI->module_url;
    //var_dump($tokens);
    //$cpData['tokens']=$tokens;
    $cpData['number'] = count($cases);
    if (!$list) {
        $rtn = $CI->parser->parse('dashboard/'.$kpi['widget_type'].'/'.$kpi['widget'], $cpData, true);
        return $rtn;
    } else { //----return cases matched
        //---map tokens to get case
        $cases = array_map(function ($case) {
            return $case['id'];
        }, $cases);
        return $cases;
    }
}
