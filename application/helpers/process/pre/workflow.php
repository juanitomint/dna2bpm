<?php
$debug = (isset($debug)) ? $debug : false;
//$debug = true;
if($debug) echo '<h3>pre workflow</h3>';
//---process if has tokenID
if ($this->input->post('token')) {
    $this->load->model('bpm');
    $this->load->helper('workflow');
//---take workflow from here    
    $idcase = $this->input->post('case');
    $token_id = new MongoId($this->input->post('token'));
    $token = $this->bpm->get_token_byid($token_id);
    $idwf = $token['idwf'];
    $idcase = $token['case'];
    //---just check if the task is still available
    //---if not then just pass the control to wf-Engine
    //---to avoid duplicate form load
    if($token['status']=='finished'){
    //---Redir to run wf and process next step
        $url = base_url() . "/bpm/engine/run/model/$idwf/$idcase";
        if ($debug)
            echo "<a href='$url'> >>>click<<< </a>";
        if (!$debug)
            header('Location:' . $url);
        exit ();
    }
}//--end if ($this->input->post('token'))
?>