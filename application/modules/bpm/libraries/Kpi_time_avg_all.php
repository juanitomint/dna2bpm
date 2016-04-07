<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of kpi_time_avg
 *
 * @author Juan Ignacio Borda <juanignacioborda@gmail.com>
 */
class kpi_time_avg_all {

    //put your code here
    var $CI;

    public function __construct($params = array()) {

// Set the super object to a local variable for use throughout the class
        $this->CI = & get_instance();
    }

    function tile($kpi) {
        $cpData = $this->core($kpi);
        $cpData['id']=$cpData['idkpi'];
        $cpData['number'] =(int) $cpData['avg'];
        $rtn = $this->CI->parser->parse('dashboard/tiles/' . $kpi['widget'], $cpData, true);
        return $rtn;
    }

    function list_cases($kpi) {
        $filter = $this->CI->kpi_model->get_filter($kpi); 
        $tokens = $this->CI->bpm->get_tokens_byResourceId($kpi['resourceId'], $filter,array('checkdate'=>true));
        $cpData = $kpi;
        $cases = array_map(function ($token) {
            return $token['case'];
        }, $tokens);
        return $cases;
    }

    function core($kpi) {
        $filter = $this->CI->kpi_model->get_filter($kpi); 
        $cases = $this->CI->bpm->get_cases_byFilter($filter);
        $cpData = $kpi;
        $max = 0;
        $min = 36000;
        $timesum = 0;
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
                $d2 = (isset($case['checkoutdate'])) ? new DateTime($case['checkoutdate']) : new DateTime();
                $interval =$d1->diff($d2);
                
                $max = ($max < $interval->days) ? $interval->days: $max;
                $min = ($min > $interval->days) ? $interval->days : $min;
                $timesum+=$interval->days;
            }
            if ($timesum) {
                $cpData['avg'] = (int) ($timesum / count($cases));

                $cpData['avg_formated'] = number_format($timesum / count($cases), 2);
            } else {
                $cpData['avg_formated'] = 0;
            }
            $cpData['max'] = $max;
            $cpData['min'] = $min;
            return $cpData;
        }
    }

    function widget($kpi) {
        $cpData = $this->core($kpi);
         $cpData['label']=(int)$cpData['avg'];
        
        $cpData['content'] = $this->CI->parser->parse('bpm/kpi_time_avg', $cpData, true);
        $rtn = $this->CI->parser->parse('dashboard/' . $kpi['widget_type'] . '/' . $kpi['widget'], $cpData, true);
        return $rtn;
    }

}
