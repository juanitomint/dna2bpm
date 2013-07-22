<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Authenticate extends MX_Controller {
    /**
     * Athenticate User
     * 
     * This class is used to authenticate and load user attrs
     * Expects parameters username & password by POST
     */
    public function __construct() {
        parent::__construct();
        $this->load->config('config');
    }

    function Index() {
        /**
         * Default method
         *
         */
        
        $segments = $this->uri->segments;
        $debug = (in_array('debug', $segments)) ? true : false;
        $this->load->model('user');
        $idu = $this->user->authenticate($this->input->post('username'), $this->input->post('password'));
        if ((bool) $idu) {
            //---register if it has logged is
            $this->session->set_userdata('loggedin', true);
            //---register the user id
            $this->session->set_userdata('iduser', $idu);
            //---register level string
            $redir=$this->session->userdata('redir');
            $redir = ($this->session->userdata('redir')) ? $this->session->userdata('redir') : base_url() .$this->config->item('default_controller');
            log_message('debug', 'Redirecting user:' . $this->session->userdata('iduser') . ' to:' . $redir);
            //---clear redir from session
            $this->session->unset_userdata('redir');
            //---clear msg from session
            $this->session->unset_userdata('msg');
            //---clear msgcode from session
            $this->session->unset_userdata('msgcode');
            $rtnArr['success'] = true;
            $rtnArr['msg'] = 'User authentication: OK!';
            $rtnArr['redir'] = $redir;
            header('Location: '.$redir);
            exit;
        } else {
            
            $rtnArr['success'] = false;
            $this->session->set_userdata('msg','nouser');
            header('Location: '.base_url().'user/login');
            exit;
        }
    }

    private function Byhash($username, $hash) {
        /**
         * Authenticate user by a given username and hash
         *
         * Only admins can impersonate their selves
         */
        $this->user->authorize('ADM');
        $this->load->model('user', '', false);
        //var_dump($_POST);
        $idu = $this->user->authenticateByHash($username, $hash);
        if ((bool) $idu) {
            //---register if it has logged is
            $this->session->set_userdata('loggedin', true);
            //---register the user id
            $this->session->set_userdata('iduser', $idu);
            //---register level string
            $this->session->set_userdata('level', $this->user->getlevel($idu));

            $redir = ($this->session->userdata('redir')) ? $this->session->userdata('redir') : base_url() .$this->config->item('default_controller');
            log_message('debug', 'Redirecting user:' . $this->session->userdata('iduser') . ' to:' . $redir);
            header("Location: $redir");
            //echo "si";
        } else {
            show_error('Error user don\'t exist');
            header('Location: ' . base_url() . 'user/login/nouser');
        }
    }

}

?>
