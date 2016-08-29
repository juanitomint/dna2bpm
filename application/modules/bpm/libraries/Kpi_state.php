<?php

/**
 * Description of kpi_count_cases
 *
 * @author Juan Ignacio Borda <juanignacioborda@gmail.com>
 */
class kpi_state {

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
            $cpData['more_info_class'] = "load_tiles_after";
            $cpData['more_info_link'] = base_url() . "bpm/kpi/list_cases/" . $kpi['idkpi'];
            $rtn = $this->CI->parser->parse('dashboard/tiles/' . $kpi['widget'], $cpData, true);
        } else {
            $rtn = '<strong>Warning!</strong>Function:' . $kpi['type'] . '<br/>' . $kpi['title'] . '<br/>resourceId not defined. ';
        }
        return $rtn;
    }

    function list_cases($kpi) {
        $core=$this->core($kpi);
        foreach($core['result'] as $cc) $cases[]=$cc['case'];
        return $cases;

    }

    function core($kpi) {
        $filter = $this->CI->kpi_model->get_filter($kpi); 
        $status = (isset($kpi['status'])) ? $kpi['status'] : 'user';
        $filter['status'] = $kpi['status'];
        $aquery=array(
            array('$match'=>$filter),
            array('$lookup'=>
                array(
                  "from" => "case",
                  "localField" => "case",
                  "foreignField" => "id",
                  "as" => "cases"
                ),
            ),
        );
        if($kpi['filter']=='owner'){
            $aquery[]=array('$match'=>array('cases.iduser'=>$this->CI->user->idu));
        }
            
        $aquery[]=array('$project'=>array('_id'=>0,'case'=>'$case'));
        
        $rs=$this->CI->mongowrapper->db->tokens->aggregate($aquery);
        
        // $tokens = $this->CI->bpm->get_tokens_byResourceId($kpi['resourceId'], $filter);
        $cpData = $kpi;
        var_dump(json_encode($aquery),$rs);exit;
        if($rs['ok']){
            $cpData['result']=$rs['result'];
            $cpData['number'] = count($rs['result']);
        }
        return $cpData;
    }

    function widget($kpi) {
        if ($kpi['resourceId'] <> '') {


            $cpData = $this->core($kpi);
            $cpData['label'] = $cpData['number'];
            $cpData['more_info_class'] = "load_tiles_after";
            $cpData['more_info_link'] = base_url() . "bpm/kpi/list_cases/" . $kpi['idkpi'];
            $rtn = $this->CI->parser->parse('dashboard/' . $kpi['widget_type'] . '/' . $kpi['widget'], $cpData, true);
        } else {
            $rtn = $this->CI->ShowMsg('<strong>Warning!</strong>Function:' . $kpi['type'] . '<br/>' . $kpi['title'] . '<br/>resourceId not defined. ', 'alert');
        }
        return $rtn;
    }

}
