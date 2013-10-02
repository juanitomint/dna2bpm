<?php
$debug = (isset($debug)) ? $debug : false;
//$debug = false;
if($debug) echo '<h3>post workflow</h3>';
//---process if has tokenID
if ($this->input->post('token')) {
    $id = (double) $id;
    $this->load->model('bpm');
    $this->load->helper('workflow');
//---take workflow from here
    $form['redir'] = '';
    $idcase = $this->input->post('case');
    $token_id = new MongoId($this->input->post('token'));
    $token = $this->bpm->get_token_byid($token_id);
    //var_dump('token',$token);
    $idwf = $token['idwf'];
    $idcase = $token['case'];
    $resourceId = $token['resourceId'];

//---1st get wf
    $mywf = $this->bpm->load($idwf);
    $mywf['data']['idwf'] = $idwf;
    $mywf['data']['case'] = $idcase;
    $mywf['data']['id'] = $id;
    $wf = bindArrayToObject($mywf['data']);
//--get the shape
    $shape = $this->bpm->get_shape($resourceId, $wf);

//---update DATA token for case USING OperationRef
    $case = $this->bpm->get_case($idcase);

    if (property_exists($shape->properties, 'operationref')) {
        if ($shape->properties->operationref) {
            $opRef = $shape->properties->operationref;
            //----set data sosurce
            $case['data'][$opRef]['connector'] = 'mongo';
            $case['data'][$opRef]['server'] = $this->config->item('mongo_server');
            $case['data'][$opRef]['dbname'] = $this->config->item('mongo_dbname');
            //----set data reference (may vary upon db connectors)
            $case['data'][$opRef]['query'] =array('id'=>$id);
            $case['data'][$opRef]['container'] = $form['container'];
            //$case['data'][$opRef]['checkdate'] = date('Y-m-d H:i:s');

            //---add formdata
            //$case['data'][$opRef] +=$frames;
            if ($debug)
                var_dump('case', $case);
            $this->bpm->save_case($case);
        }
    }
//--------------------------------
    $data = array();
    $data['finishedBy'] = (double) $this->session->userdata('iduser');
    $data['finishedDate'] = date('Y-m-d H:i:s');
    $data['id'] = (double) $id;
    $data['container'] = $form['container'];
    //---add user data
    //-------------------
    if ($debug)
        var_dump('Token Data', $data);
    //---store named object in case
    //
    //-------------------------------
    $this->bpm->movenext($shape, $wf, $data);
//var_dump($case,$token_id->__toString(),$token);
//---Redir to run wf and process next step
    $url = base_url() . "/bpm/engine/run/model/$idwf/$idcase";
    if ($debug)
        echo "<a href='$url'> >>>click<<< </a>";
    if (!$debug)
        header('Location:' . $url);
    exit ();
}//--end if ($this->input->post('token'))
?>