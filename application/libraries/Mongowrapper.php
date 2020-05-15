<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Mongowrapper extends MongoClient {

    var $db;

    function __construct(){
        // Fetch CodeIgniter instance
        $ci = get_instance();
        // Load Mongo configuration file
        $ci->load->config('cimongo');

        // Fetch Mongo server and database configuration
        $server = $ci->config->item('host');
        $port = $ci->config->item('port');
        $dbname = $ci->config->item('db');
        $user = $ci->config->item('user');
        $password = $ci->config->item('pass');
        $mongodb_uri="mongodb://$server:$port";
        $options=array();
        if ($user <> '' and $password <> '') {
            $mongodb_uri="mongodb://$user:$password@$server:$port";
        }
        // Initialize Mongo
        // var_dump($mongodb_uri);
        try{
            parent::__construct($mongodb_uri);
        }    
        catch (MongoConnectionException $e){
            show_error($e->getMessage().'<hr><p>Check your config file ('.ENVIRONMENT.'/cimongo.php) or run the setup wizard again</p>');
        }
        $this->db = $this->$dbname;
    }

}