<?php

class Setup extends CI_Controller {

    function Setup() {
        parent::__construct();
        $this->load->library('parser');
        $this->load->model('user');
        $this->load->model('group');
        $this->load->library('index/ui');
        $this->config->load('user/config');
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

    function Index() {
        $cpData['title'] = 'Setup Page';
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
            $adm['idgroup'] = 1; //---primary group
            $adm['group'] = array(1); //---group that user belong

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
                $badge = '<span class="label label-success">OK</span>';
            } else {
                $badge = '<span class="label label-warning">Not Writable</span>';
            }
            $cpData['msgcode'][] = array('msg' => $folder . '   ' . $badge);
        }
        $cpData['msgcode'][] = array('msg' => "Click <a href='" . $this->module_url . "login'>>>here<<</a> to log-in");
        $this->ui->compose('user/setup.bootstrap.php', 'user/bootstrap.ui.php', $cpData);
    }

}
