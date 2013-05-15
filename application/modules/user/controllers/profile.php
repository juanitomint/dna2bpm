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
        $this->idu = (float) $this->session->userdata('iduser');
        
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
        
        $customData['js'] = array($this->module_url . "assets/jscript/bootstrap-fileupload.js" => 'profile JS');
        $customData['css'] = array($this->module_url . "assets/css/fix_bootstrap_checkbox.css" => 'profile CSS', $this->module_url . "assets/css/bootstrap-fileupload.css" => 'profile CSS');
       
        
        //tomamos los datos del usuario
        $idu = (float) $this->session->userdata('iduser');
        echo $idu;
        //echo "user from db:<br/>";
        //var_dump($this->user->get_user((float) $idu));
        //echo '<hr/>';
        $customData+=(array)$this->user->get_user((float) $idu);
  
        
        $this->ui->compose('profileEdit', 'bootstrap.ui.php', $customData);
    }

    /*
     * Save Profile data uses $this->user->save($data);
     */
    function Save() {
        //var_dump($this->input->post());
        
        
        $iduser = (float) $this->session->userdata('iduser');
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
        $post_obj['idu'] = (float) $iduser;

        
        //lo que esta en la base
        $dbobj = (array)$this->user->get_user((float)$iduser);
        
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

        
        //header('Location:');
        
        
        
        
    }

    /*
     * View user Profile
     */
    function View() {
        $idu = (float) $this->session->userdata('iduser');
        
        $customData['base_url'] = base_url();
        $customData['module_url'] = base_url() . 'user/';
        
        $customData['js'] = array($this->module_url . "assets/jscript/profile.js" => 'profile JS');
        $customData['css'] = array($this->module_url . "assets/css/fix_bootstrap_checkbox.css" => 'profile CSS');
        
        
        $idu = (float) $this->session->userdata('iduser');
        //echo $idu;
        //echo "user from db:<br/>";
        //var_dump($this->user->get_user((float) $idu));
        //echo '<hr/>';
        
       $customData+=(array)$this->user->get_user((float) $idu);
       
       $this->ui->compose('profile', 'bootstrap.ui.php', $customData);

       
        
    }
    
     /*
     * View user Profile
     */
    function addnew($numgrupo) {
        var_dump($this->input->post());
        
        
        $iduser = (float) $this->session->userdata('iduser');
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
        $post_obj['idu'] = (float) $iduser;

        
        //lo que esta en la base
        $dbobj = (array)$this->user->get_user((float)$iduser);
        
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