<?php

 function sla_time($kpi,$CI) {
        
        $filter = $CI->get_filter($kpi);
        $filter['status'] = 'closed';
        $cases = $CI->bpm->get_cases_byFilter($filter);
        //var_dump($filter, $cases);
        $cpData = $kpi;
        $sla_in = array();
        $sla_out = array();
        $cpData['total'] = count($cases);
        foreach ($cases as $case) {
            $d1 = new DateTime($case['checkdate']);
            $d3 = new DateTime($case['checkdate']);
            $d2 = new DateTime($case['checkoutdate']);
            $interval = $d1->diff($d2);
            $ref = date_interval_create_from_date_string($kpi['time_limit']);
            $d3->add($ref);
            if ($d2 > $d3) {
                $cpData['on_time'][] = $case;
            } else {
                $cpData['out_time'][] = $case;
            }
        }
        $cpData['sla_percent'] =  100*(count($cpData['on_time'])/$cpData['total']);
        $cpData['sla_percent_out'] =  100*(count($cpData['out_time'])/$cpData['total']);
        $cpData['sla']=  number_format($cpData['sla_percent'],2).'% ('. count($cpData['on_time']).')';
        $cpData['sla_out'] =number_format($cpData['sla_percent_out'],2).'% ('. count($cpData['out_time']).')';
        $rtn = $CI->parser->parse('bpm/kpi_sla_time', $cpData, true);
        return $rtn;
    }