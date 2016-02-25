<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class cimongo_user_plugin extends User {
    
    function __construct() {
        parent::__construct();
    }
    function where_id($_id) {
     return   array('_id' => new MongoId($_id));
    }
}
