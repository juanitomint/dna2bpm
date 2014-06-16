<?php

require_once('ActiveResource.php');

class Issue extends ActiveResource {

    function __construct($data = array()) {
        $this->_data = $data;
        $this->request_format = 'xml'; // REQUIRED!; 
        $this->element_name = 'issue';
        $this->element_name_plural = 'issues';
        $this->user = '';
        $this->password = '//---random-password----//';
    }

}

?>
