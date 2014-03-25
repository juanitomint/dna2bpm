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

function count($kpi, $CI, $list = null) {
    $filter = $this->get_filter($kpi);
    $tokens = $this->bpm->get_tokens_byResourceId($kpi['resourceId'], $filter);
    $cpData = $kpi;
    $cpData['base_url'] = $this->base_url;
    $cpData['module_url'] = $this->module_url;
    //var_dump($tokens);
    $cpData['count'] = count($tokens);
    if (!$list) {
        $rtn = $this->parser->parse('bpm/kpi_count', $cpData, true);
        return $rtn;
    } else { //----return cases matched
        //---map tokens to get case
        $cases = array_map(function ($token) {
            return $token['case'];
        }, $tokens);
        return $cases;
    }
}
