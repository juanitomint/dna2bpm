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
            $cpData['id']=$cpData['idkpi'];
            $rtn = $this->CI->parser->parse('dashboard/tiles/' . $kpi['widget'], $cpData, true);
        } else {
            $rtn = '<strong>Warning!</strong>Function:' . $kpi['type'] . '<br/>' . $kpi['title'] . '<br/>resourceId not defined. ';
        }
        return $rtn;
    }

    function list_cases($kpi) {
        
    }

    function core($kpi) {
         $filter = $this->CI->kpi_model->get_filter($kpi); 
        
        //---prepare aggregation query (faster than foreach)
        $aquery=array(
            array(
                '$match' =>$filter
            ),
            array (
                '$group' =>array (
                          '_id' => '$resourceId',
                          'min' => array ('$min' => '$interval.days'),
                          'max' => array ('$max' => '$interval.days'),
                          'avg' => array ('$avg' => '$interval.days'),
                          'count' => array ('$sum' => 1),
                ),
            ),
            array (
                '$project' => array (
                          'resourceId' => '$_id',
                          'min_real' => '$min',
                          'max_real' => '$max',
                          'avg' => '$avg',
                          'count' => '$count',
                          '_id' => 0,
                ),
            ),
            );
        $rs=$this->CI->mongowrapper->db->tokens->aggregate($aquery);
        $cpData = $kpi;
            if($rs['ok']){
            $cpData=$rs['result'][0]+$kpi;
            }
        $cpData['avg_formated'] = number_format($cpData['avg'], 2);
        $cpData['number'] = $cpData['avg_formated'];
        return $cpData;
    }

    function widget($kpi) {
        if ($kpi['resourceId'] <> '') {
            $cpData = $this->core($kpi);
            $w=(isset($kpi['widget']))?'dashboard/widgets/' .$kpi['widget']:'bpm/widgets/kpi_time_avg';
            $w='bpm/widgets/kpi_time_avg';
            $rtn = $this->CI->parser->parse( $w, $cpData, true);
            
        } else {
            $rtn = $this->CI->ShowMsg('<strong>Warning!</strong>Function:' . $kpi['type'] . '<br/>' . $kpi['title'] . '<br/>resourceId not defined. ', 'alert');
        }
        return $rtn;
    }

}
