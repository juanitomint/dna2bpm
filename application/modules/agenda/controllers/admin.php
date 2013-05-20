<?php
//session_start();

class Admin extends CI_Controller {

    function Admin() {
        parent::__construct();
        $this->load->library('parser');
        $this->load->model('user/user');
        $this->load->model('agenda/agenda');
        $this->load->helper('agenda/get_array');
        //----LOAD LANGUAGE
        $this->lang->load('main', $this->config->item('language'));
        $this->idu = (double) $this->session->userdata('iduser');
        
}
    

    function index() {
    // admin de agendas       
    $this->user->authorize();
    $cpData = $this->lang->language; 
    $cpData['base_url'] = base_url();
    $cpData['module_url'] = base_url() . 'agenda/';
    

     $tree=$this->agenda->get_agendas(); 
     $mytree=$this->nest(0,$tree);

     $cpData['agendas']=$this->treeread($mytree,'sortable');
    
    //Parse
    $this->parser->parse('admin_agendas', $cpData);
    

        

    }
    
    // Arma estructura de arbol
    function nest($f,$tree){
        reset($tree);
        $myitems=array();
            foreach($tree as $k=>$v){
                if($v['parent']==$f){
                        $myitems[$k]=new_folder($k,$tree);
                }
            }    

                return $myitems;

           
    }
   
    
        function treeread($item,$class=null){
        $mytext="<ol class='$class'>";
        if(count($item)){    
            foreach($item as $k=>$v){          
                 $mytext.="<li><div>$k</div>";              
                 if(count($v))$mytext.=$this->treeread($v,null);
                 $mytext.="</li>";
            }
        }
        $mytext.="</ol>";
        return $mytext;
    }



}
?>
