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
//
        $this->load->model('app');
        $this->load->config('config');


        $this->user->authorize();
        //---base variables
        $this->base_url = base_url();
        $this->module_url = base_url() . $this->router->fetch_module() . '/';
        //----LOAD LANGUAGE
        $this->lang->load('profile', $this->config->item('language'));
        $this->idu = (double) $this->session->userdata('iduser');
    }

    /*
     * Edit /view user profile
     */

    function Index() {

		$this->edit();
    }



    function Edit($disabled=false) {
        //$this->lang->load('profile', $this->config->item('language'));
        $data['lang']= $this->lang->language;
    	$data['base_url'] = base_url();
        $data['module_url'] = base_url() . 'user/';
        $data['disabled']=($disabled)?('disabled'):('');

         $customData['js'] = array(
         		'jqueryUI', 'PLUpload',
         		$this->module_url . "assets/jscript/profile.js" => 'profile JS',
         		$this->module_url . "assets/jscript/profile.js" => 'profile JS',
                $this->base_url."jscript/jquery/plugins/jquery-validate/jquery.validate.js"=>"Validate",
                $this->base_url."jscript/jquery/plugins/jquery-validate/additional-methods.js"=>"Aditional Methods",
         );
         
         $customData['css'] = array(
         		$this->module_url . "assets/css/profile.css" => 'profile CSS',
         );
         $customData['global_js'] = array(
         		'myidu'=>$this->idu
         );

        //tomamos los datos del usuario
        $data+=(array) $this->user->get_user((int) $this->idu);

        //genero
        $genero = isset($data['gender']) ? ($data['gender']) : ("male");
        if ($genero == "female")
            $data['checkedF'] = 'checked';
        else
            $data['checkedM'] = 'checked';

        //Notifications
        $noti = isset($data['notification_by_email']) ? ($data['notification_by_email']) : ("no");

        if ($noti == "yes")
        	$data['check_notiY'] = 'checked';
        else
        	$data['check_notiN'] = 'checked';

        // Chequeo avatar
        $data['avatar']=$this->get_avatar();

		$customData['content']=$this->parser->parse('user/profile',$data,true);
        //$this->ui->compose('profile', 'bootstrap3.ui.php', $customData);
		return $customData;

    }

    /*
     * Save Profile data uses $this->userimage/jpg->save($data);
     */

    function Save() {

        $customData['base_url'] = base_url();
        $customData['module_url'] = base_url() . 'user/';
        $iduser = (double) $this->session->userdata('iduser');

        $allowed=array('name','gender','lastname','idnumber','birthdate','email','phone','celular','address','cp','city','signature','notification_by_email');

        //lo que esta en la base
        $dbobj = (array) $this->user->get_user((int) $iduser);


		foreach($this->input->post('data') as $item){
 			if(in_array($item['name'],$allowed))
 			$dbobj[$item['name']]=$item['value'];
 			//---sanitize fields
 			    $dbobj[$item['name']]=strip_tags($item['value'],'<p><a><br><hr><b><strong>');
 			//---set password if posted
 			if($item['name']=='passw' && !empty($item['value']))
 	        	// Cambio de pass
        	    $dbobj['passw']=$this->user->hash($item['value']);
		}

        $result = $this->user->update($dbobj);

		echo json_encode($result);

    }

    /*
     * View user Profile
     */

    function View() {
		$this->edit(true);
    }

    /*
     * View user Profile
     */

    function addnew($numgrupo) {



        $iduser = (double) $this->session->userdata('iduser');
        $post_obj['nick'] = $this->input->post('nick');
        //la foto
        $post_obj['name'] = $this->input->post('nombre');
        $post_obj['lastname'] = $this->input->post('apellido');
        $post_obj['idnumber'] = $this->input->post('dni');
        $post_obj['birthdate'] = $this->input->post('fechanac');
        $post_obj['email'] = $this->input->post('inputEmail');
        $post_obj['phone'] = $this->input->post('telefono');
        $post_obj['celular'] = $this->input->post('celular');
        $post_obj['address'] = $this->input->post('domicilio');
        $post_obj['cp'] = $this->input->post('cp');
        $post_obj['city'] = $this->input->post('ciudad');
        $post_obj['idu'] = (int) $iduser;


        //lo que esta en la base
        $dbobj = (array) $this->user->get_user((int) $iduser);

        //process password
        //vemos si es la misma
        if ($dbobj['passw'] == $this->input->post('passw'))
            $post_obj['passw'] = $this->input->post('passw');
        else
            $post_obj['passw'] = ($this->input->post('passw')) ? md5($this->input->post('passw')) : md5('nopass');


        //juntamos
        $new_obj = $post_obj + (array) $dbobj;
        //var_dump($new_obj);
        //---Clear the object
        $obj = array_filter($new_obj);
        $new_obj = $obj;
        //---now SAVE it
        //$result = $this->user->save($new_obj);
        //header('Location:');
    }

    function get_avatar($userID=null){

        $current_user=(empty($userID))?((int)$this->idu):((int)$userID);
        $genero = isset($current_user['gender']) ? ($current_user['gender']) : ("male");
        $userdata=(array) $this->user->get_user($current_user);

        // Chequeo avatar
        if ( is_file(FCPATH."images/avatar/".$current_user.".jpg")){
        	return base_url()."images/avatar/".$current_user.".jpg";
        }elseif(is_file(FCPATH."images/avatar/".$current_user.".png")){
        	return base_url()."images/avatar/".$current_user.".png";
        }else{
            //=== gravatar test
            if($this->config->item('gravatar')){
                $hash=md5( strtolower( trim( $userdata['email'] ) ) );
                $gravatar="http://www.gravatar.com/avatar/$hash";
                return $gravatar;
            }
            //

        	return ($genero == "male")?(base_url()."images/avatar/male.jpg"):(base_url()."images/avatar/female.jpg");
        }
    }



    function upload(){

    	// Make sure file is not cached (as it happens for example on iOS devices)
     	header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
    	header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
    	header("Cache-Control: no-store, no-cache, must-revalidate");
    	header("Cache-Control: post-check=0, pre-check=0", false);
    	header("Pragma: no-cache");


    	/*
    	 // Support CORS
    	header("Access-Control-Allow-Origin: *");
    	// other CORS headers if any...
    	if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    	exit; // finish preflight CORS requests here
    	}
    	*/

    	// 5 minutes execution time
    	@set_time_limit(5 * 60);

    	// Uncomment this one to fake upload time
    	// usleep(5000);

    	// Settings
    	$targetDir = 'images/avatar';
    	$cleanupTargetDir = true; // Remove old files
    	$maxFileAge = 5 * 3600; // Temp file age in seconds


    	// Create target dir
    	if (!file_exists($targetDir)) {
    		@mkdir($targetDir);
    	}

    	// Get a file name
    	$path=pathinfo($_REQUEST["name"]);
//     	if (isset($_REQUEST["name"])) {
//     		$fileName = pathinfo($_REQUEST["name"]);
//     	} elseif (!empty($_FILES)) {
//     		$fileName = "_2".$_FILES["file"]["name"];
//     	} else {
//     		 $this->idu  = uniqid("file_");
//     	}
		$fileName=$this->idu.".".$path['extension'];

		$ext2=($path['extension']=='jpg')?('png'):('jpg');
		$file2delete = $targetDir . DIRECTORY_SEPARATOR . $this->idu.".$ext2";
    	$filePath = $targetDir . DIRECTORY_SEPARATOR . $fileName;

    	// Chunking might be enabled
    	$chunk = isset($_REQUEST["chunk"]) ? intval($_REQUEST["chunk"]) : 0;
    	$chunks = isset($_REQUEST["chunks"]) ? intval($_REQUEST["chunks"]) : 0;


    	// Remove old temp files
    	if ($cleanupTargetDir) {
    		if (!is_dir($targetDir) || !$dir = opendir($targetDir)) {
    			die('{"jsonrpc" : "2.0", "error" : {"code": 100, "message": "Failed to open temp directory."}, "id" : "id"}');
    		}

    		while (($file = readdir($dir)) !== false) {
    			$tmpfilePath = $targetDir . DIRECTORY_SEPARATOR . $file;

    			// If temp file is current file proceed to the next
    			if ($tmpfilePath == "{$filePath}.part") {
    				continue;
    			}

    			// Remove temp file if it is older than the max age and is not the current file
    			if (preg_match('/\.part$/', $file) && (filemtime($tmpfilePath) < time() - $maxFileAge)) {
    				@unlink($tmpfilePath);
    			}
    		}
    		closedir($dir);
    	}


    	// Open temp file
    	if (!$out = @fopen("{$filePath}.part", $chunks ? "ab" : "wb")) {
    		die('{"jsonrpc" : "2.0", "error" : {"code": 102, "message": "Failed to open output stream."}, "id" : "id"}');
    	}

    	if (!empty($_FILES)) {
    		if ($_FILES["file"]["error"] || !is_uploaded_file($_FILES["file"]["tmp_name"])) {
    			die('{"jsonrpc" : "2.0", "error" : {"code": 103, "message": "Failed to move uploaded file."}, "id" : "id"}');
    		}

    		// Read binary input stream and append it to temp file
    		if (!$in = @fopen($_FILES["file"]["tmp_name"], "rb")) {
    			die('{"jsonrpc" : "2.0", "error" : {"code": 101, "message": "Failed to open input stream."}, "id" : "id"}');
    		}
    	} else {
    		if (!$in = @fopen("php://input", "rb")) {
    			die('{"jsonrpc" : "2.0", "error" : {"code": 101, "message": "Failed to open input stream."}, "id" : "id"}');
    		}
    	}

    	while ($buff = fread($in, 4096)) {
    		fwrite($out, $buff);
    	}

    	@fclose($out);
    	@fclose($in);

    	// Check if file has been uploaded
    	if (!$chunks || $chunk == $chunks - 1) {
    		// Strip the temp .part suffix off

    		rename("{$filePath}.part", $filePath);
   			@unlink($file2delete);// file with same idu and other extension exists
    	}

    	// Return Success JSON-RPC response
    	die('{"jsonrpc" : "2.0", "result" : null, "id" : "id"}');

    }


}

/* End of file welcome.php */
/* Location: ./system/application/controllers/welcome.php */
