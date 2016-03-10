<?php

class ssl_model extends CI_Model {

    function __construct() {
        parent::__construct();
        $this->idu = (int) $this->session->userdata('iduser');
        $this->load->library('cimongo/cimongo');
        $this->db = $this->cimongo;
        $this->container="ssl";

    }
    
    //=== Creacion evento
   
    function add_key($key){
        $query=array('fingerprint'=>$key['fingerprint']);
        $num=$this->db->where($query)->get($this->container)->num_rows();
        if($num>0){
            return array('status'=>false,'error'=>'fingerprint in use');
        }else{
            $rs=$this->db->insert($this->container, $key); 
            return array('status'=>$rs,'error'=>'');
        }
    
    }
    
    //=== Keys by user
    public function get_my_keys(){
        $query=array('idu'=>$this->idu);
        $fields=array('fingerprint','description');
        $this->db->select($fields);

        return $this->db->where($query)->get($this->container)->result_array();

    }
    
     //=== delete user Key
    public function delete_my_key($fingerprint){
        $query=array('idu'=>$this->idu,'fingerprint'=>$fingerprint);
        return $this->db->where($query)->delete($this->container);
    }  
    
    //=== key by fingerprint
     public function get_key($fingerprint){
        $query=array('fingerprint'=>$fingerprint);
        return $this->db->where($query)->get($this->container)->row();

    }   
    

}
