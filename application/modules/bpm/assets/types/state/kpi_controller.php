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
    $filter = array();
    //----return cases if list=true
    $cases = array();
    switch ($kpi['filter']) {
        case 'group':
            break;
        case 'user':
            $filter = array(
                'idwf' => $kpi['idwf'],
                'iduser' => $this->idu
            );
            break;
        default: //---filter by idwf
            $filter = array(
                'idwf' => $kpi['idwf']
            );
            break;
    }
    /*
     *  'pending'
      'manual'
      'user'
      'waiting'
     */
    $status = array(
        'pending',
        'manual',
        'user',
        'waiting'
    );
    //$filter['status'] = array('$in' => (array) $status); //@todo include other statuses
    $tokens = $this->bpm->get_tokens_byResourceId($kpi['resourceId'], $filter);
    $cpData = $kpi;
    $cpData['base_url'] = $this->base_url;
    $cpData['module_url'] = $this->module_url;
    //var_dump($tokens);
    //$cpData['tokens']=$tokens;
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
