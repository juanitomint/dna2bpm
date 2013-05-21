<?php
/**
 * @class genia
 */
if (!defined('BASEPATH'))
    exit('No direct script access allowed');


class Genias_model extends CI_Model {

    function __construct()
    {
        // Call the Model constructor
        parent::__construct();
    }
    
    function tasks(){
        return "ggg";
    }
    
    function goals_new($goal){
        $options = array('upsert' => true, 'safe' => true);
        $container='container.genias';
        return $this->mongo->db->$container->save($goal, $options);
    }
    
    function goals_get($idu){
        $query = array('idu' =>(double) $idu);
        $container='container.genias';
        $result = $this->mongo->db->$container->find($query)->sort(array('desde'=>-1));        
        //var_dump($result, json_encode($result), $result->count());

        return $result;
    }
    
    // -- Config -- //
    
    function config_get($name){
        $container='container.genias_config';
        $query=array('name'=>$name);
        $result = $this->mongo->db->$container->findOne($query); 
        return $result;
    }
    
        function config_set($data){
        $container='container.genias_config';
        $options = array('upsert' => true, 'safe' => true);
        $query=array('name'=>'projects2');
        return $this->mongo->db->$container->update($query, $data, $options);
    }
    

}
