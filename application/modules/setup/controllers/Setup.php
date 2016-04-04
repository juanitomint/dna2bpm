<?php

class Setup extends MX_Controller {

    function Setup() {
        parent::__construct();
        $this->load->library('parser');
        // $this->load->model('user');
        // $this->load->model('group');
        // $this->load->model('rbac');
        // $this->config->load('user/config');
        $this->load->library('index/ui');
        $this->load->helper('file');
        //---base variables
        $this->base_url = base_url();
        $this->module_url = base_url() . $this->router->fetch_module() . '/';
        $this->writable_folders = array(
            'images/avatar',
            'images/model',
            'images/png',
            'images/svg',
            'images/zip',
        );
    }
    
    
    function Index(){
        $cpData['title'] = 'DNA2BPM Setup()';
        $cpData['base_url'] = $this->base_url;
        $cpData['module_url'] = $this->module_url;
        $cpData['siteurl']=$this->url_origin($_SERVER).'/';
        $cpData['environment']=ENVIRONMENT;
        
        $this->ui->compose('setup/setup.bootstrap.php', 'setup/bootstrap3.ui.php', $cpData);
    }
    
    /**
     * Save settings to config files
     */ 
    
    function step1(){
        $cpData['title'] = 'DNA2BPM Setup->step1()';
        $cpData['base_url'] = $this->base_url;
        $cpData['module_url'] = $this->module_url;
        $cpData['siteurl']=$this->url_origin($_SERVER).'/';
        $cpData['environment']=ENVIRONMENT;
        $post_data=$this->input->post();
        unset($post_data['submit']);
        $folder=APPPATH.'config/'.ENVIRONMENT;
        ///----exit if already configured
        if (file_exists($folder.'/config.php')) {
            show_error("Setup already done, delete your $folder dir if you want to start over");
        }
        
        ///--check if dir exists already (should be check index.php for enviroment)
        if (!file_exists($folder)) {
        mkdir($folder);
        }
        
        if (is_writable($folder)) {
                    $badge = '<span class="label label-success pull-right">OK</span>';
                } else {
                    $badge = '<span class="label label-warning pull-right">Not Writable</span>';
                }
                $cpData['msgcode'][] = array('msg' => $folder . '   ' . $badge);
        
        foreach($post_data as $file=>$arr){
            $str_config='';
            //---check if default config exists
            if(file_exists(APPPATH.'config/'.$file.'.php')){
                $str=read_file(APPPATH.'config/'.$file.'.php');
            } else {
                $str="<?php\n"; 
            }
            $str.="\n\n//   Generated by setup on ".date('D d M Y H:i')."\n"; 
            foreach($arr as $key=>$value)
            $str.="\$config['$key']='".$value."';\n";
            //$str.="\$config.=".var_export($arr,true).";\n";
             
            $config_path=$folder.'/'.$file.'.php';
            write_file($config_path, $str);
            $badge = '<span class="label label-success pull-right">OK</span>';
            $cpData['msgcode'][] = array('msg' => $config_path . '   ' . $badge);
        }
        
        //----Test Mongo connection
        
        $this->load->library('mongowrapper');
        //$config=var_export($cpData,true);
        $this->ui->compose('setup/step1.bootstrap.php', 'setup/bootstrap3.ui.php', $cpData);    
    }
    function step2(){
        $cpData['title'] = 'DNA2BPM Setup->step2()';
        $cpData['base_url'] = $this->base_url;
        $cpData['module_url'] = $this->module_url;
        $cpData['siteurl']=$this->url_origin($_SERVER).'/';
        $cpData['environment']=ENVIRONMENT;
        $folder=APPPATH.'config/'.ENVIRONMENT;
        //----copy autoload
        if (copy(APPPATH.'config/autoload.php.pre',$folder.'/autoload.php')) {
                    $badge = '<span class="label label-success pull-right">OK</span>';
                } else {
                    $badge = '<span class="label label-warning pull-right">Not Writable</span>';
                }
                $cpData['msgcode'][] = array('msg' => 'New autoload... ' . $badge);
        redirect($this->module_url.'users');
    }
    /**
     * Try to get your requested url
     */ 
    function url_origin( $s, $use_forwarded_host = false ){
        $ssl      = ( ! empty( $s['HTTPS'] ) && $s['HTTPS'] == 'on' );
        $sp       = strtolower( $s['SERVER_PROTOCOL'] );
        $protocol = substr( $sp, 0, strpos( $sp, '/' ) ) . ( ( $ssl ) ? 's' : '' );
        $port     = $s['SERVER_PORT'];
        $port     = ( ( ! $ssl && $port=='80' ) || ( $ssl && $port=='443' ) ) ? '' : ':'.$port;
        $host     = ( $use_forwarded_host && isset( $s['HTTP_X_FORWARDED_HOST'] ) ) ? $s['HTTP_X_FORWARDED_HOST'] : ( isset( $s['HTTP_HOST'] ) ? $s['HTTP_HOST'] : null );
        $host     = isset( $host ) ? $host : $s['SERVER_NAME'] . $port;
        return $protocol . '://' . $host;
    }

    function users() {
        $this->load->model('user/user');
        $this->load->model('user/group');
        $this->load->model('user/rbac');
        $this->config->load('user/config');
        $cpData['title'] = 'Setup Users Page';
        $cpData['authUrl'] = base_url() . 'user/authenticate';
        $cpData['base_url'] = $this->base_url;
        $cpData['module_url'] = $this->module_url;
        $cpData['theme'] = $this->config->item('theme');
        $cpData['plugins'] = (class_exists('Userlayer')) ? implode(',', $this->config->item('user_plugin')) : array();
        if (!$this->group->get($this->config->item('groupAdmin'))) {
            $cpData['msgcode'][] = array('msg' => "Creating Admin Group.");
            $grp = array(
                "idgroup" => 1,
                "name" => "DNA² Admin",
                "desc" => "System Administrators Group, everybody on this group will have sytem admin rights",
                "perm" => array('ADM'),
                "idsup" => "1"
            );
            $this->group->save($grp);
        } else {
            $cpData['msgcode'][] = array('msg' => "Admin Groups already exists");
        }
        $admins = count($this->user->getByGroup($this->config->item('groupAdmin')));
        //var_dump($adm->count());
        //---create administrator user if not exists
        if (!$admins) {
            $adm = array();
            $adm['idu'] = 1;
            $adm['nick'] = 'admin';
            $adm['passw'] = md5('admin');
            $adm['name'] = 'System';
            $adm['lastname'] = 'Administrator';
            $adm['perm'] = array('ADM');
            $adm['idgroup'] = $this->config->item('groupAdmin'); //---primary group
            $adm['group'] = array($this->config->item('groupAdmin')); //---group that user belong

            $adm['checkdate'] = date('Y-m-d h:i:s');
            $this->user->save($adm);
            $cpData['msgcode'][] = array('msg' => 'Created Admin user:');
            $cpData['msgcode'][] = array('msg' => "Nick: " . $adm['nick'] . '<br/>password: admin<br/>Name: ' . $adm['name'] . '<br/>Last Name: ' . $adm['lastname']);
        } else {
            $cpData['msgcode'][] = array('msg' => "Admin users already exists");
        }
        $cpData['msgcode'][] = array('msg' => "Checking Permissions: the folders below has to have write permission to the user runing the site, usually www-data");
        foreach ($this->writable_folders as $folder) {
            if (is_writable($folder)) {
                $badge = '<span class="label label-success pull-right">OK</span>';
            } else {
                $badge = '<span class="label label-warning pull-right">Not Writable</span>';
            }
            $cpData['msgcode'][] = array('msg' => $folder . '   ' . $badge);
        }
        $groupUser=$this->group->get($this->config->item('groupUser'));
        //---creates a group
        if(!$groupUser){
             $cpData['msgcode'][] = array('msg' => "Creating Users Group.");
             $grp = array(
                "idgroup" => 1000,
                "name" => "DNA² Users",
                "desc" => "Group for Regular users, everybody on this group will have sytem access rights",
                "perm" => array('USE'),
                "idsup" => "1"
            );
            $this->group->save($grp);
        } else {
             $cpData['msgcode'][] = array('msg' => "User's Group already exists:".$groupUser['name']);
        }
        
        $groupUser=$this->group->get($this->config->item('groupUser'));
        //---ensures at least 1 user in the users group;
        $users = count($this->user->getByGroup($this->config->item('groupUser')));
        //var_dump($adm->count());
        //---create administrator user if not exists
        if (!$users) {
            $user = array();
            $user['nick'] = 'user';
            $user['passw'] = '123456';
            $user['name'] = 'John';
            $user['lastname'] = 'Doe';
            $user['perm'] = array('USE');
            $user['idgroup'] = $this->config->item('groupUser'); //---primary group
            $user['group'] = array($this->config->item('groupUser')); //---group that user belong
            $user['checkdate'] = date('Y-m-d h:i:s');
            $this->user->add($user);
            $cpData['msgcode'][] = array('msg' => 'Created Regular user:');
            $cpData['msgcode'][] = array('msg' => "Nick: " . $user['nick'] . '<br/>password: 123456<br/>Name: ' . $user['name'] . '<br/>Last Name: ' . $user['lastname']);
        }  
        //------ensures basic premisions
        $file_path=APPPATH . 'modules/user/assets/json/perm.user.json';
        if(is_file($file_path)){
          $cpData['msgcode'][] = array('msg' => "Importing basic User Group permissions");
          $data =json_decode(read_file($file_path));
          foreach($data as $path){
              $this->rbac->put_path($path, array(
                    'source' => 'Setup',
                    'checkdate' => date('Y-m-d H:i:s'),
                ));
                $this->rbac->put_path_to_group($path,$this->config->item('groupUser'));
          }
          $cpData['msgcode'][] = array('msg' => "Imported ".count($data).' paths.');
        } else {
            $cpData['msgcode'][] = array('msg' => "file not exists");
        }
        $cpData['msgcode'][] = array('msg' => "Click <a href='".base_url()."user/login'>>>here<<</a> to log-in");
        $this->ui->compose('setup/step2.bootstrap.php', 'setup/bootstrap3.ui.php', $cpData);
    }
    
    function make_group_file($group=null){
    $this->user->authorize();
    $idgroup=isset($group) ? $group : $this->config->item('groupUser');
    $repo=$this->rbac->get_group_paths((int)$idgroup);
    $path=APPPATH . 'modules/user/assets/json/perm.user.json';
    $data=json_encode($repo);
        if (!write_file($path, $data))
        {
            echo "Unable to write the file $path from group $idgroup";
        } else {
            echo "Created $path from group $idgroup";
        }
    }
}