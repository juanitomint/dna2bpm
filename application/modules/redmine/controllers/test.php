<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/**
 * test
 * 
 * Description of the class
 * 
 * @author Juan Ignacio Borda <juanignacioborda@gmail.com>
 * @date    May 26, 2014
 */
class test extends MX_Controller {

    function __construct() {
        parent::__construct();
    }

    function Index() {
        $this->load->module('redmine');
        $idproject = 121;

        echo "<h1>Welcome to Redmine Test</h1>";

        echo "<h2>Create Issue</h2>";
        $issue = $this->redmine->create(
                array(
                    'project_id' => $idproject,
                    'subject' => 'Test:' . date('Y-m-d H:i:s'),
                    'description' => 'Test Issue',
                )
        );
        $id = $issue->id;
        echo "Created #$id<br/>";
        var_dump($issue);
        echo "<hr/>";

        echo "<h2>Read Issue #$id</h2>";
        $issue = $this->redmine->read($id);
        var_dump($issue);
        echo "<hr/>";

        echo "<h2>Update Issue #$id</h2>";
        $issue = $this->redmine->update($id, array(
            'subject' => 'Test-updated:' . date('Y-m-d H:i:s'),
                )
        );
        var_dump($issue);
        echo "<hr/>";
        
        echo "<h2>Delete Issue #$id</h2>";
        $result = $this->redmine->delete($id);
        var_dump($result);
        
    }

}

/* End of file welcome.php */
/* Location: ./system/application/controllers/welcome.php */