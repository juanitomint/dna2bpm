<?php

/*
 * KPI time_avg Controller function
 * This file contains the function necesary to calculate avg time
 * 
 * if you want to collect the cases matched just pass true for $list parameter
 * @author Borda Juan Ignacio
 * 
 * @version 	1.0 (2014-07-03)
 * 
 */
function time_avg($kpi, $CI, $list = null) {
        $timesum = 0;
        if ($kpi['resourceId'] <> '') {
            $filter = $CI->get_filter($kpi);
            $tokens = $CI->bpm->get_tokens_byResourceId($kpi['resourceId'], $filter);
            $cpData = $kpi;
            $max = 0;
            $min = 36000;
            foreach ($tokens as $thisToken) {
                $max = ($max < $thisToken['interval']['days']) ? $thisToken['interval']['days'] : $max;
                $min = ($min > $thisToken['interval']['days']) ? $thisToken['interval']['days'] : $min;
                $timesum+=$thisToken['interval']['days'];
            }
            $cpData['avg'] = (int) ($timesum / count($tokens));
            if ($timesum) {

                $cpData['avg_formated'] = number_format($timesum / count($tokens), 2);
            } else {
                $cpData['avg_formated'] = 0;
            }
            $cpData['max'] = ($kpi['max']) ? $kpi['max'] : $max;
            $cpData['min'] = $min;
            $cpData['number']= $cpData['avg_formated'];
            $rtn = $CI->parser->parse('dashboard/tiles/'.$kpi['widget'], $cpData, true);
            
        } else {
            $rtn = $CI->ShowMsg('<strong>Warning!</strong>Function:' . $kpi['type'] . '<br/>' . $kpi['title'] . '<br/>resourceId not defined. ', 'alert');
        }
        return $rtn;
    }