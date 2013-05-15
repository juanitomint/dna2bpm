<?php

class Setup extends CI_Controller {

    function Setup() {
        parent::__construct();
        $this->load->library('parser');
        $this->load->model('user');
        $this->load->model('group');
    }

    function Index() {
        echo "<pre>";
        if (!$this->group->get(1)) {
            echo "Creating Admin Group<hr/>";
            $grp = array(
                "idgroup" => 1,
                "name" => "DNA² Admin",
                "desc" => "System Administrators Group, everybody on this group will have sytem admin rights",
                "perm" => array('ADM'),
                "idsup" => "1"
            );
            $this->group->save($grp);
        }
        $adm = $this->user->getbyid(1);
        //var_dump($adm->count());
        //---create administrator user if not exists
        if (!$adm->count()) {
            $adm = array();
            echo "Creating Admin user";
            $adm['idu'] = 1;
            $adm['nick'] = 'admin';
            $adm['passw'] = md5('admin');
            $adm['name'] = 'System';
            $adm['lastname'] = 'Administrator';
            $adm['perm'] = array('ADM');
            $adm['idgroup'] = 1;//---primary group
            $adm['group'] = array(1);//---group that user belong

            $adm['checkdate']=date('Y-m-d h:i:s');
            $this->user->save($adm);
            echo "<br/>Nick: " . $adm['nick'] . '<br/>password: admin<br/>Name: ' . $adm['name'] . '<br/>Last Name: ' . $adm['lastname'];
        } else {
            $adm = $adm->getNext();
            echo "Admin user already exists:<br/>Nick: " . $adm['nick'] . '<br/>Name: ' . $adm['name'] . '<br/>Last Name: ' . $adm['lastname'];
        }
        echo "<hr/>Click <a href='./user/login'>>>here<<</a> to log-in";
        echo "</pre>";
    }

}
