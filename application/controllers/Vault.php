<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Vault extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->library('parser');
    }

    function GetIdHandler() {
        $this->session->set_userdata('dhxvlt_state', 0);
        echo $this->session->userdata('session_id');
    }

    function GetInfoHandler() {
        $id = $this->input->post('sessionId');
        if ($this->session->userdata('dhxvlt_state') == -1) {
            // -1 is set in UploadHandler.php after a successful upload
            // send 100% back and mark state for invalidation
            echo 100;
            $this->session->set_userdata('dhxvlt_state', -2);
        } else if ($this->session->userdata('dhxvlt_state') == -2) {
            // -2 is set above to invalidate current upload session
            echo -1;
            //session_destroy();
        } else if ($this->session->userdata('dhxvlt_state') == -3) {
            // -3 is set in UploadHandler.php in case of some error (like filename encoding, "post_max_size" oversized).
            $maxPost = ini_get('post_max_size');
            echo "error:-3:$maxPost:";
            //session_destroy();
        } else {

            $info = uploadprogress_get_info($id);
            $bt = $info['bytes_total'];

            if ($bt < 1) {
                $percent = 0;
            } else {
                if (!$this->session->userdata('dhxvlt_max')) {
                    // check the upload_max_filesize config value
                    $this->session->userdata('dhxvlt_max', true);
                    $maxSizeM = ini_get('upload_max_filesize');
                    $maxSize = $this->return_bytes($maxSizeM);
                    if ($maxSize < $bt) {
                        $this->session->set_userdata('dhxvlt_state', -2);
                        echo "error:-2:$bt:$maxSizeM:";
                        exit;
                    }
                }
                $percent = round($info['bytes_uploaded'] / $bt * 100, 0);
            }
            echo $percent;
            $id = $this->session->userdata('session_id');

            if ($this->session->userdata('dhxvlt_state') == -1) {
                // -1 is set in UploadHandler.php after a successful upload
                // send 100% back and mark state for invalidation
                echo 100;
                $this->session->set_userdata('dhxvlt_state', -2);
            } else if ($this->session->userdata('dhxvlt_state') == -2) {
                // -2 is set above to invalidate current upload session
                echo -1;
                //session_destroy();
            } else if ($this->session->userdata('dhxvlt_state') == -3) {
                // -3 is set in UploadHandler.php in case of some error (like filename encoding, "post_max_size" oversized).
                $maxPost = ini_get('post_max_size');
                echo "error:-3:$maxPost:";
                //session_destroy();
            } else {

                $info = uploadprogress_get_info($id);
                $bt = $info['bytes_total'];

                if ($bt < 1) {
                    $percent = 0;
                } else {
                    if (!$this->session->userdata('dhxvlt_max')) {
                        // check the upload_max_filesize config value
                        $this->session->userdata('dhxvlt_max', true);
                        $maxSizeM = ini_get('upload_max_filesize');
                        $maxSize = $this->return_bytes($maxSizeM);
                        if ($maxSize < $bt) {
                            $this->session->set_userdata('dhxvlt_state', -2);
                            echo "error:-2:$bt:$maxSizeM:";
                            exit;
                        }
                    }
                    $percent = round($info['bytes_uploaded'] / $bt * 100, 0);
                }
                echo $percent;
            }
        }
    }

    function return_bytes($val) {
        $val = trim($val);
        $last = strtolower($val[strlen($val) - 1]);
        switch ($last) {
            case 'g':
                $val *= 1024;
            case 'm':
                $val *= 1024;
            case 'k':
                $val *= 1024;
        }

        return $val;
    }

    function UploadHandler() {
        $id = $this->session->userdata('session_id');
        $inputName = $this->input->get('userfile');
        $fileName = $_FILES[$inputName]['name'];
        $tempLoc = $_FILES[$inputName]['tmp_name'];
        echo $_FILES[$inputName]['error'];
        $target_path =$this->input->post('target_path');
        $target_path = $target_path . basename($fileName);
        if (move_uploaded_file($tempLoc, $target_path)) {
            $this->session->set_userdata('dhxvlt_state', -1);
        } else {
            $this->session->set_userdata('dhxvlt_state', -3);
        }
    }

}