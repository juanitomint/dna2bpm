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
class kpi_time_avg {

    //put your code here
    var $CI;

    public function __construct($params = array()) {

// Set the super object to a local variable for use throughout the class
        $this->CI = & get_instance();
    }

    function tile($kpi) {
        if ($kpi['resourceId'] <> '') {
            $cpData = $this->core($kpi);
            $rtn = $this->CI->parser->parse('dashboard/tiles/' . $kpi['widget'], $cpData, true);
        } else {
            $rtn = '<strong>Warning!</strong>Function:' . $kpi['type'] . '<br/>' . $kpi['title'] . '<br/>resourceId not defined. ';
        }
        return $rtn;
    }

    function list_cases($kpi) {
        
    }

    function core($kpi) {
        $filter = Modules::run('kpi/get_filter',$kpi);
        $tokens = $this->CI->bpm->get_tokens_byResourceId($kpi['resourceId'], $filter);
        $cpData = $kpi;
        $max = 0;
        $min = 36000;
        $timesum=0;
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
        $cpData['number'] = $cpData['avg_formated'];
        return $cpData;
    }

    function widget($kpi) {
        if ($kpi['resourceId'] <> '') {
            $cpData = $this->core($kpi);
            $rtn = $this->CI->parser->parse('dashboard/widgets/' . $kpi['widget'], $cpData, true);
        } else {
            $rtn = $this->CI->ShowMsg('<strong>Warning!</strong>Function:' . $kpi['type'] . '<br/>' . $kpi['title'] . '<br/>resourceId not defined. ', 'alert');
        }
        return $rtn;
    }

}
