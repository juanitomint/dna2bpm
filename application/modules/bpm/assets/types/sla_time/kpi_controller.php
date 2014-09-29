<?php

function sla_time($kpi, $CI, $list = null) {

    $filter = $CI->get_filter($kpi);
    //$filter['status'] = 'closed';
    $cases = $CI->bpm->get_cases_byFilter($filter);
    //var_dump($filter, $cases);
    //----sanitize some values
    //----set on time to seven days if nothing is set
    $kpi['time_limit'] = ($kpi['time_limit'] <> '') ? $kpi['time_limit'] : '7 day';
    $kpi['list_type'] = ($kpi['list_type'] <> '') ? $kpi['list_type'] : 'out_time';
    $cpData = $kpi;
    $sla_in = array();
    $sla_out = array();
    $cpData['total'] = count($cases);
    $cpData['on_time'] = array();
    $cpData['out_time'] = array();
    //var_dump($cases);exit;
    if ($cases) {
        foreach ($cases as $case) {
            $d1 = new DateTime($case['checkdate']);
            $d3 = new DateTime($case['checkdate']);
            $d2 = (isset($case['checkoutdate'])) ? new DateTime($case['checkoutdate']) : new DateTime();
            $interval = $d1->diff($d2);
            //var_dump($d1->format('Y-m-d H:i:s'),$d2->format('Y-m-d H:i:s'),$interval);
            $ref = date_interval_create_from_date_string($kpi['time_limit']);
            $d3->add($ref);
            if ($d2 > $d3) {
                $cpData['out_time'][] = $case;
            } else {
                $cpData['on_time'][] = $case;
            }
        }
        $cpData['sla_percent'] = 100 * (count($cpData['on_time']) / $cpData['total']);
        $cpData['sla_percent_out'] = 100 * (count($cpData['out_time']) / $cpData['total']);
        if (!$list) {
            $cpData['sla'] = number_format($cpData['sla_percent'], 2) . '% (' . count($cpData['on_time']) . ')';
            $cpData['sla_out'] = number_format($cpData['sla_percent_out'], 2) . '% (' . count($cpData['out_time']) . ')';
            $cpData['content'] = $CI->parser->parse('bpm/kpi_sla_time', $cpData, true);
            $rtn = $CI->parser->parse('dashboard/'.$kpi['widget_type'].'/'.$kpi['widget'], $cpData, true);
            return $rtn;
        } else {
            //----return cases on time
            //---map cases to get caseid
            $cases = array_map(function ($token) {
                return $token['id'];
            }, $cpData[$kpi['list_type']]);
            return $cases;
        }
    }
}
