<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Mongowrapper extends Mongo {

    var $db;

    function __construct(){
        // Fetch CodeIgniter instance
        $ci = get_instance();
        // Load Mongo configuration file
        $ci->load->config('cimongo');

        // Fetch Mongo server and database configuration
        $server = $ci->config->item('host');
        $dbname = $ci->config->item('db');
        $user = $ci->config->item('username');
        $password = $ci->config->item('pass');
        
        // Initialise Mongo
        if ($server) {
            parent::__construct($server);
        } else {
            parent::__construct();
        }
        $this->db = $this->$dbname;
        if ($user <> '' and $password <> '') {
            $rs = $this->db->authenticate($user, $password);
            //var_dump('authenticate',$user, $password,$rs);
            if(!$rs['ok'])
                show_error ($rs['errmsg'].'<br/>Mongo Authentication has Failed for user: '.$user.
                        '<br/>Check username & password on: /system/application/config/mongo.php'
                        );
        }
    }

}