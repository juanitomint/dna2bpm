<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/**
 * profile
 * 
 * Description of the class
 * 
 * @author Juan Ignacio Borda <juanignacioborda@gmail.com>
 * @date    Apr 15, 2013
 */
class Profile extends MX_Controller {

    function __construct() {
        parent::__construct();
        $this->load->library('parser');
        $this->load->model('user');
        $this->load->library('ui');
        $this->load->model('app');
        $this->user->authorize();
        //---base variables
        $this->base_url = base_url();
        $this->module_url = base_url() . 'user/';
        //----LOAD LANGUAGE
        $this->lang->load('library', $this->config->item('language'));
        $this->idu = (int) $this->session->userdata('iduser');
        
    }

    /*
     * Edit /view user profile
     */
    function Index() {
        
        $customData['base_url'] = base_url();
        $customData['module_url'] = base_url() . 'user/';


        
        $customData['css'] = array($this->module_url . "assets/css/profile.css" => 'profile CSS');
        $this->ui->compose('profileIndex', 'bootstrap.ui.php', $customData);
        
    }

    /*
     * Edit user profile
     */
    function Edit() {
   
        $customData['base_url'] = base_url();
        $customData['module_url'] = base_url() . 'user/';
        
        $customData['js'] = array($this->base_url . "jscript/jquery/ui/jquery-ui-1.10.2.custom/jquery-ui-1.10.2.custom.min.js" => 'profile JS',$this->module_url . "assets/jscript/bootstrap-fileupload.js" => 'profile JS',$this->module_url . "assets/jscript/profile.js" => 'profile JS');
        $customData['css'] = array($this->base_url . "jscript/jquery/ui/jquery-ui-1.10.2.custom/css/smoothness/jquery-ui-1.10.2.custom.css" => 'profile CSS',$this->module_url . "assets/css/fix_bootstrap_checkbox.css" => 'profile CSS', $this->module_url . "assets/css/bootstrap-fileupload.css" => 'profile CSS');
      
        
        //tomamos los datos del usuario
        $idu = (int) $this->session->userdata('iduser');
        
        
        $customData+=(array)$this->user->get_user((int) $idu);
        
        $genero=isset($customData['gender'])? ($customData['gender']):("male") ;
        if($genero=="female") $customData['checkedF']= 'checked';
        else  $customData['checkedM']= 'checked';
        
        $this->ui->compose('profileEdit', 'bootstrap.ui.php', $customData);
    }

    /*
     * Save Profile data uses $this->userimage/jpg->save($data);
     */
    function Save() {
        
        $customData['base_url'] = base_url();
        $customData['module_url'] = base_url() . 'user/';
        $iduser = (int) $this->session->userdata('iduser');
        $post_obj['gender']  = $this->input->post('gender');
        
       
        
        //var_dump($this->input->post());
       // var_dump($_FILES);
        $img_avatar = $_FILES["avatar"];
        //si hay archivo
        if($img_avatar["name"]){
            $allowedExts = array("gif", "jpeg", "jpg", "png");
            $allowedTypes = array("image/gif", "image/jpeg","image/jpg", "image/pjpeg", "image/png","image/x-png");
            $extension = end(explode(".", $img_avatar["name"]));
            $type = $img_avatar["type"];
            if (in_array($type, $allowedTypes) && ($_FILES["file"]["size"] < 20000) && in_array($extension, $allowedExts)){
             
              move_uploaded_file($img_avatar["tmp_name"],"images/avatar/".$iduser.".".$extension);
              //echo "Stored in: " . "images/avatar/".$iduser.".".$extension;
              $post_obj['avatar']  = "images/avatar/".$iduser.".".$extension;
  
            }else echo "Invalid file";  
            
        }else{
            if( $post_obj['gender']=="male" )
                $post_obj['avatar']  = "images/avatar/male.jpg";
            else  $post_obj['avatar']  = "images/avatar/female.jpg";
            
            
        }
        
        
        
        $iduser = (int) $this->session->userdata('iduser');
        $post_obj['nick']  = $this->input->post('nick');
        //la foto 
        $post_obj['name']  = $this->input->post('nombre');
        $post_obj['gender']  = $this->input->post('gender');
        $post_obj['lastname']  = $this->input->post('apellido');
        $post_obj['idnumber']  = $this->input->post('dni');
        $post_obj['birthdate']  = $this->input->post('fechanac');
        $post_obj['email']  = $this->input->post('inputEmail');
        $post_obj['phone']  = $this->input->post('telefono');
        $post_obj['celular']  = $this->input->post('celular');
        $post_obj['address']  = $this->input->post('domicilio');
        $post_obj['cp']  = $this->input->post('cp');
        $post_obj['city']  = $this->input->post('ciudad');
        $post_obj['idu'] = (int) $iduser;

        
        //lo que esta en la base
        $dbobj = (array)$this->user->get_user((int)$iduser);
        
        //process password
        //vemos si es la misma 
        if($dbobj['passw']==$this->input->post('passw'))
            $post_obj['passw'] = $this->input->post('passw');
        else 
            $post_obj['passw'] = ($this->input->post('passw')) ? md5($this->input->post('passw')) : md5('nopass');
        
       
       //juntamos
        $new_obj = $post_obj+(array)$dbobj;
      //var_dump($new_obj);
      //---Clear the object
        $obj = array_filter($new_obj);
        $new_obj = $obj;
        //---now SAVE it
        $result = $this->user->save($new_obj);

        echo "Actualizacion realizada con exito <a href='".$customData['module_url']."profile/index'>Volver</a>";
        //header('Location:');
        
        
        
        
    }

    /*
     * View user Profile
     */
    function View() {
        $idu = (int) $this->session->userdata('iduser');
        
        $customData['base_url'] = base_url();
        $customData['module_url'] = base_url() . 'user/';
        
        $customData['css'] = array($this->module_url . "assets/css/fix_bootstrap_checkbox.css" => 'profile CSS');
        
        
        $idu = (int) $this->session->userdata('iduser');
       //var_dump($this->user->get_user((int) $idu));

       $customData+=(array)$this->user->get_user((int) $idu);
       $genero=isset($customData['gender'])? ($customData['gender']):("nada") ;
        if($genero=="female") $customData['checkedG']= 'femenino';
        if($genero=="male") $customData['checkedG']= 'masculino';
       
       $this->ui->compose('profile', 'bootstrap.ui.php', $customData);
      
    }
    
     /*
     * View user Profile
     */
    function addnew($numgrupo) {
        var_dump($this->input->post());
        
        
        $iduser = (int) $this->session->userdata('iduser');
        $post_obj['nick']  = $this->input->post('nick');
        //la foto 
        $post_obj['name']  = $this->input->post('nombre');
        $post_obj['name']  = $this->input->post('nombre');
        $post_obj['lastname']  = $this->input->post('apellido');
        $post_obj['idnumber']  = $this->input->post('dni');
        $post_obj['birthdate']  = $this->input->post('fechanac');
        $post_obj['email']  = $this->input->post('inputEmail');
        $post_obj['phone']  = $this->input->post('telefono');
        $post_obj['celular']  = $this->input->post('celular');
        $post_obj['address']  = $this->input->post('domicilio');
        $post_obj['cp']  = $this->input->post('cp');
        $post_obj['city']  = $this->input->post('ciudad');
        $post_obj['idu'] = (int) $iduser;

        
        //lo que esta en la base
        $dbobj = (array)$this->user->get_user((int)$iduser);
        
        //process password
        //vemos si es la misma 
        if($dbobj['passw']==$this->input->post('passw'))
            $post_obj['passw'] = $this->input->post('passw');
        else 
            $post_obj['passw'] = ($this->input->post('passw')) ? md5($this->input->post('passw')) : md5('nopass');
        
       
       //juntamos
        $new_obj = $post_obj+(array)$dbobj;
      //var_dump($new_obj);
      //---Clear the object
        $obj = array_filter($new_obj);
        $new_obj = $obj;
        //---now SAVE it
        //$result = $this->user->save($new_obj);

        
        //header('Location:');

       
        
    }

}

/* End of file welcome.php */
/* Location: ./system/application/controllers/welcome.php */