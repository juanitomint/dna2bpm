<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Mongowrapper extends mongo {

    var $db;

    function __construct(){
        // Fetch CodeIgniter instance
        $ci = get_instance();
        // Load Mongo configuration file
        $ci->load->config('mongo');

        // Fetch Mongo server and database configuration
        $server = $ci->config->item('mongo_server');
        $dbname = $ci->config->item('mongo_dbname');
        $user = $ci->config->item('mongo_username');
        $password = $ci->config->item('mongo_password');

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