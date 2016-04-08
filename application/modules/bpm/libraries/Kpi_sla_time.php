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
class kpi_sla_time {

    //put your code here
    var $CI;

    public function __construct($params = array()) {

// Set the super object to a local variable for use throughout the class
        $this->CI = & get_instance();
    }

    function tile($kpi) {
        if ($kpi['resourceId'] <> '') {
            $cpData = $this->core($kpi); 
            $cpData['number']=$cpData[$kpi['list_type'].'_percent'].'%';
            $cpData['more_info_class'] = "load_tiles_after";
            $cpData['more_info_link'] = base_url() . "bpm/kpi/list_cases/" . $kpi['idkpi'];
            $rtn = $this->CI->parser->parse('dashboard/tiles/' . $kpi['widget'], $cpData, true);
        } else {
            $rtn = '<strong>Warning!</strong>Function:' . $kpi['type'] . '<br/>' . $kpi['title'] . '<br/>resourceId not defined. ';
        }
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
            $cpData['on_time_percent'] = 100 * (count($cpData['on_time']) / $cpData['total']);
            $cpData['out_time_percent'] = 100 * (count($cpData['out_time']) / $cpData['total']);
            return $cpData;
        }
    }

    function widget($kpi) {
        if ($kpi['resourceId'] <> '') {

            $cpData = $this->core($kpi);
            $cpData['sla_percent']=$cpData[$kpi['list_type'].'_percent'];
            $cpData['sla'] = number_format($cpData['on_time_percent'], 2) . '% (' . count($cpData['on_time']) . ')';
            $cpData['sla_out'] = number_format($cpData['out_time_percent'], 2) . '% (' . count($cpData['out_time']) . ')';
            $cpData['content'] = $this->CI->parser->parse('bpm/kpi_sla_time', $cpData, true);
            $rtn = $this->CI->parser->parse('dashboard/' . $kpi['widget_type'] . '/' . $kpi['widget'], $cpData, true);
        } else {
            $rtn = $this->CI->ShowMsg('<strong>Warning!</strong>Function:' . $kpi['type'] . '<br/>' . $kpi['title'] . '<br/>resourceId not defined. ', 'alert');
        }
        return $rtn;
    }

}
