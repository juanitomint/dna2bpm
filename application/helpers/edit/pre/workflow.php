<?php

$debug = (isset($debug)) ? $debug : false;
//$debug = true;
if ($debug)
    echo '<h3>pre workflow</h3>';
$uri_params = $this->uri->uri_to_assoc();
//var_dump($uri_params);
//---process if has tokenID
if (isset($uri_params['token'])) {
    $this->load->model('bpm');
    //$this->load->helper('workflow');
//---take workflow from here    
    $token_id = new MongoId($uri_params['token']);
    $token = $this->bpm->get_token_byid($token_id);
    $idcase = $token['case'];
    $idwf = $token['idwf'];
    $idcase = $token['case'];
    $resourceId=$token['resourceId'];
    //---just check if the task is still available
    //---if not then just pass the control to wf-Engine
    //---to avoid duplicate form load
    if ($token['status'] == 'finished') {
        //---Redir to run wf and process next step
        $url = base_url() . "/bpm/engine/run/model/$idwf/$idcase";
        if ($debug)
            echo "<a href='$url'>" . $token['status'] . " >>>click<<< </a>";
        if (!$debug)
            header('Location:' . $url);
        exit ();
    }
    //---get case
    $case = $this->bpm->get_case($idcase);
    //---1st get wf
    $mywf = $this->bpm->load($idwf);
    $mywf['data']['idwf'] = $idwf;
    $mywf['data']['case'] = $idcase;
    $mywf['data']['id'] = $id;
    $wf = $this->bpm->bindArrayToObject($mywf['data']);
    //--get the shape
    $shape = $this->bpm->get_shape($resourceId, $wf);
    //---set cosmetic
    //var_dump( $shape->properties);
    $parse_data=array();
    $parse_data['wf']=$mywf['data']['properties'];
    $parse_data['token']=$token;
    $parse_data['case']=$case;
    //var_dump($parse_data);
    if (isset($shape->properties->name))
        $renderData['form_title'] =$this->parser->parse_string($shape->properties->name,$parse_data);
    if (isset($shape->properties->documentation))
        $renderData['desc'] = $this->parser->parse_string($shape->properties->documentation,$parse_data);
}//--end if
?>