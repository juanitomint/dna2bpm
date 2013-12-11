<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class admin extends MX_Controller {

    function __construct() {
        parent::__construct();
        $this->load->library('parser');
        $this->load->model('rbac');
        $this->user->authorize();
        //---base variables
        $this->base_url = base_url();
        $this->module_url = base_url() . $this->router->fetch_module() . '/';
        //----LOAD LANGUAGE
        $this->lang->load('library', $this->config->item('language'));
        $this->idu = (int) $this->session->userdata('iduser');
    }

    function Index() {
        //    var_dump(base_url()); exit;
        //---only allow admins and Groups/Users enabled
        $this->user->authorize();
        $cpData = $this->lang->language;
        $segments = $this->uri->segment_array();
        $cpData['nolayout'] = (in_array('nolayout', $segments)) ? '1' : '0';
        //var_dump($level);
        $cpData['theme'] = $this->config->item('theme');
        $cpData['base_url'] = $this->base_url;
        $cpData['module_url'] = $this->module_url;
        $cpData['title'] = 'RBAC Admin';
        $cpData['ext-locale'] = 'ext-lang-es';
        //---define files to viewport
        $cpData['css'] = array(
            $this->base_url . "jscript/ext/src/ux/css/CheckHeader.css" => 'checkHeader',
            $this->module_url . "assets/css/admin.css" => 'Admin css',
            $this->module_url . "assets/css/load_mask.css" => 'loadingmask',
        );

        $cpData['js'] = array(
            $this->module_url . "assets/jscript/ext.settings.js" => 'Settings',
            $this->module_url . "assets/jscript/data.js" => 'Group Data Objects',
            $this->module_url . "assets/jscript/dataview.js" => 'Data View',
            $this->base_url . "jscript/ext/src/ux/form/SearchField.js" => 'Search Field',
            $this->module_url . "assets/jscript/tree.js" => 'Perm Tree',
            $this->module_url . "assets/jscript/grid.js" => 'Users Grid',
            $this->module_url . "assets/jscript/userform.js" => 'Users Edit Form',
            $this->module_url . "assets/jscript/app.js" => 'Viewport',
        );

        $cpData['global_js'] = array(
            'base_url' => $this->base_url,
            'module_url' => $this->module_url,
        );

        $this->ui->makeui('user/ext.ui.php', $cpData);
    }

    function group($action) {
        $this->user->authorize();
        $segments = $this->uri->segments;
        $debug = (in_array('debug', $segments)) ? true : false;
        $cpData = $this->lang->language;
        $groups = array();
        $i = 0;
        switch ($action) {
            case 'create':
                $post_groups = json_decode(file_get_contents('php://input'));
                $groups = array();
                //---Gen id 4 group
                foreach ($post_groups as $group) {
                    $idgroup = $this->group->genid();
                    $group->idgroup = $idgroup;
                    $this->group->save($group);
                    $groups[] = $group;
                }
                break;
            case 'read':
                $db_groups = $this->group->get_groups();
                $groups['totalCount'] = count($db_groups);
                $groups['rows'] = $db_groups;

                break;
            case 'update':
                $post_groups = json_decode(file_get_contents('php://input'));

                foreach ($post_groups as $group) {
                    $idgroup = $group->idgroup;
                    $db_group = $this->group->get($idgroup);
                    $obj = (array) $group + $db_group;
                    $groups[] = $obj;
                    $this->group->save($obj);
                }
                break;
            case 'destroy':
                $post_groups = json_decode(file_get_contents('php://input'));
                foreach ($post_groups as $group) {
                    $this->group->delete($group->idgroup);
                }
                break;
        }
        //var_dump($cpData);
        if (!$debug) {
            header('Content-type: application/json;charset=UTF-8');
            echo json_encode($groups);
        } else {
            var_dump($groups);
        }
        //$this->load->view('footer');
    }

    function user($action) {
        $this->user->authorize();
        $segments = $this->uri->segments;
        $debug = (in_array('debug', $segments)) ? true : false;
        $cpData = $this->lang->language;
        $rtnArr = array();
        $i = 0;
        switch ($action) {
            case 'read':
                $start = ($this->input->post('start')) ? $this->input->post('start') : 0;
                $limit = ($this->input->post('limit')) ? $this->input->post('limit') : 50;
                $query = $this->input->post('query');
                $idgroup = (int) $this->input->post('idgroup');
                $sortObj = json_decode($this->input->post('sort'));
                // build sort array
                $sort = array();
                foreach ($sortObj as $value) {

                    $sort[$value->property] = $value->direction;
                };
                $rs = $this->user->get_users($start, $limit, $sort, $query, $idgroup);
                $rtnArr['totalCount'] = count($rs);

                foreach ($rs as $thisUser) {
                    
                    $thisUser->_id = (property_exists($thisUser,'_id')) ? $thisUser->_id->{'$id'} :$thisUser->idu;
                    $rtnArr['rows'][] = $thisUser;
                    //break;
                }
                break;
            case 'update':
                $user_data = $_POST;
                $user = $this->user->add($user_data);
                $rtnArr['success'] = true;
                $rtnArr['msg'] = 'User updated: ok!';
                $rtnArr['data'] = $user;
                break;
            case 'destroy':
                $post_users = json_decode(file_get_contents('php://input'));
                foreach ($post_users as $user) {
                    $this->user->delete_by_id($user->_id);
                }
                break;
        }
        //var_dump($cpData);
        if (!$debug) {
            header('Content-type: application/json;charset=UTF-8');
            echo json_encode($rtnArr);
        } else {
            var_dump($rtnArr);
        }
        //$this->load->view('footer');
    }

    function get_group_properties($idgroup) {
        $cpData = array();
        $cpData = $this->lang->language;
        $cpData['theme'] = $this->config->item('theme');
        $cpData['base_url'] = base_url();
        $cpData['new'] = false;

        //----load group properties
        if ($idgroup == 'new' or $idgroup == 'undefined') {
            $cpData['new'] = true;
            $cpData['idsup'] = $this->session->userdata('iduser');
            $cpData['idgroup'] = 'new';
        } else {
            $group = $this->group->get($idgroup);

            //----set visible
            if (isset($group['visible']))
                $cpData['visible'] = ($group['visible']) ? 'checked="checked"' : '';
            //----set locked
            if (isset($group['locked']))
                $cpData['locked'] = ($group['locked']) ? 'checked="checked"' : '';
            //---make perm String
            //----prepare perm string
            $group['perm'] = (isset($group['perm'])) ? implode(',', (array) $group['perm']) : '';

            if ($group)
                $cpData+=$group;
        }
        //var_dump($group);
        //---set name for supervisor
        if (isset($cpData['idsup'])) {
            $supervisor = $this->user->get_user($cpData['idsup']);
            $cpData['supervisor'] = $supervisor['name'] . ' ' . $supervisor['lastname'];
        } else {
            $cpData['supervisor'] = 'UNSUPERVISED GROUP';
        }
        $this->parser->parse('user/group_properties', $cpData, false, true);
    }

    function delete_group_db($idgroup) {
        $this->user->delete_group($idgroup);
        echo '{"result":"ok"}';
    }

    function delete_user_db($iduser) {
        $this->user->delete($iduser);
        echo '{"result":"ok"}';
    }

    function save_group($idgroup) {
        $post_obj = $this->input->post('obj');

        $isnew = false;
        if ($idgroup == 'new') {
            $idgroup = $this->app->gen_inc('groups', 'idgroup');
            $post_obj['idgroup'] = $idgroup;
            $post_obj['idsup'] = $this->session->userdata('iduser');
            $isnew = true;
        }
        $obj = $this->group->get($idgroup);
        $post_obj['_id'] = $obj['_id'];
        $post_obj['idgroup'] = (int) $idgroup;

        //---conform perm array
        $post_obj['perm'] = explode(',', $post_obj['perm']);

        //---Clear the object
        $obj = array_filter($post_obj);
        $new_obj = $post_obj;

        //---now SAVE it
        $result = $this->group->save($new_obj);
        //var_dump($post_obj);
        //var_dump($new_obj);
        $result = date('H:i:s');
        $result.= ( $result['ok']) ? ' Saved OK!' : 'Error:' . $result['err'];
        $result.= ( $result['updatedExisting']) ? ' Updated Existing Object' : '';
        $result.='<br/>';
        echo json_encode(array(
            'result' => $result,
            'isnew' => $isnew,
            'idgroup' => $idgroup,
        ));
    }

    function save_user($iduser) {
        $post_obj = $this->input->post('obj');

        $isnew = false;
        if ($iduser == 'new') {
            $iduser = $this->app->genid_general('users', 'idu');
            $post_obj['iduser'] = $iduser;
            $post_obj['owner'] = $this->session->userdata('iduser');
            $post_obj['checkdate'] = date('Y-m-d');
            //---make hash 4 password
            $post_obj['passw'] = ($post_obj['passw']) ? md5($post_obj['passw']) : md5('nopass');
            $isnew = true;
        }
        $obj = $this->user->get_user($iduser);
        $post_obj['_id'] = $obj['_id'];
        $post_obj['idu'] = (int) $iduser;
        $post_obj['idgroup'] = (int) $post_obj['idgroup'];
        $post_obj['passw'] = ($post_obj['passw']) ? md5($post_obj['passw']) : md5('nopass');
        //---conform group
        $post_obj['group'] = explode(',', $post_obj['group']);
        $post_obj['group'] = array_map(
                create_function('$value', 'return (int)$value;'), $post_obj['group']
        );
        //---conform perm array
        $post_obj['perm'] = explode(',', $post_obj['perm']);

        //---Clear the object
        $obj = array_filter($post_obj);
        $new_obj = $post_obj;

        //---now SAVE it
        $result = $this->user->save($new_obj);
        //var_dump($post_obj);
        //var_dump($new_obj);
        $result = date('H:i:s');
        $result.= ( $result['ok']) ? ' Saved OK!' : 'Error:' . $result['err'];
        $result.= ( $result['updatedExisting']) ? ' Updated Existing Object' : '';
        $result.='<br/>';
        echo json_encode(array(
            'result' => $result,
            'isnew' => $isnew,
            'iduser' => $iduser,
        ));
        //header('Location:');
    }

    function showall($idu) {

        echo "user from db:<br/>";
        var_dump($this->user->get_user((int) $idu));
        echo '<hr/>';

        echo "level from db:<br/>";
        var_dump($this->user->getlevel((int) $idu));
        echo '<hr/>';

        echo "Session data:<br/>";
        var_dump('iduser', $this->session->userdata('iduser'), 'level', $this->session->userdata('level'));
        echo '<hr/>';
    }

    function test_user($idu) {
        $this->user->authorize();
        //---only allow users to impersonate
        if ($this->user->isAdmin($this->user->get_user($this->idu))) {
            $this->load->config('config');
            //---register if it has logged is
            $this->session->set_userdata('loggedin', true);
            //---register the user id
            $this->session->set_userdata('iduser', $idu);
            //---register level string
            $redir = base_url() . $this->config->item('default_controller');
            //---redirect
            header('Location: ' . $redir);
            exit;
        }
    }

}
