<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Gridfs extends Mongo {

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
            $mongodb_uri="mongodb://$user:$password@$server:$port/$dbname";
        }
        // Initialize Mongo
        try{
            parent::__construct($server);
        }    
        catch (MongoConnectionException $e){
            show_error($e->getMessage().'<hr><p>Check your config file ('.ENVIRONMENT.'/cimongo.php) or run the setup wizard again</p>');
        }
        $this->db = $this->$dbname;
        $this->grid = $this->db->getGridFS();
    }
     /**
      * Store a file from filesystem
      * $path: end with slash
      */ 
     function storeFile($file,$data=array()){
         $metadata=array('metadata'=>$data);
         return  $this->grid->storeFile($file, $metadata);
     }
     
     function put($file,$data=array()){
         $metadata=array('metadata'=>$data);
         return  $this->grid->put($file, $metadata);
     }
     
     function storeUpload($file,$data=array()){
         $metadata=array('metadata'=>$data);
         return  $this->grid->storeUpload($file, $metadata);
     }
     
     function find($query=array(),$fields=array()){
        return  $this->grid->find($query, $fields); 
     }
    
     function get($id){
        return  $this->grid->get($id); 
     }
     
     function findFilename($filename='',$fields=array()){
        $query=array('filename'=>array('$regex'=>new MongoRegex("/$filename/")));
        return  $this->grid->find($query, $fields); 
     }
     
     function findOne($query=array(),$fields=array()){
         var_dump($query);
        return  $this->grid->findOne($query, $fields); 
     }
    
     function remove($criteria=array(),$options=array('w'=>true)){
        return  $this->grid->remove($criteria, $options); 
     }
     
     
}