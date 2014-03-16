<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/**
 * test
 * 
 * Description of the class
 * 
 * @author Juan Ignacio Borda <juanignacioborda@gmail.com>
 * @date    Dec 9, 2013
 */
class Test extends MX_Controller {

    function __construct() {
        parent::__construct();
        $this->load->library('parser');
        //----LOAD LANGUAGE
        $this->lang->load('library', $this->config->item('language'));
        $this->idu = (int) $this->session->userdata('iduser');
    }

    function Index() {
        echo "<h3>get_groups</h3>";
        var_dump($this->group->get_groups());
    }

    function ldap() {

        echo "<h1>LDAP TEST</h1>";
        echo "<h3>Authenticate: jborda -> jborda1234</h3>";
        $userId = $this->user->authenticate('jborda', 'jborda1234');
        var_dump('$userId', $userId);
        echo "<h3>get_user(2002)</h3>";
        var_dump($this->user->get_user($userId));
        echo "<h3>get_groups</h3>";
        var_dump($this->group->get_groups());
        echo "<h3>get_group by gidnumber 1010</h3>";
        var_dump($this->group->get(1010));
        echo "<h3>genid()</h3>";
        $newid = $this->group->genid();
        var_dump($newid);
        echo "<h3>Add Group:$newid</h3>";
        $group = $this->group->save(
                array(
                    'idgroup' => $newid,
                    'name' => 'SQL/Admin/Test_' . $newid
                )
        );
        echo "<h3>Remove Group:$newid</h3>";
        $this->group->delete($newid);
    }

}

/* End of file welcome.php */
/* Location: ./system/application/controllers/welcome.php */