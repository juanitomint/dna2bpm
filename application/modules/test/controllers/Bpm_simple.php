<?php

require_once(APPPATH . 'modules/test/controllers/Toast.php');

class Bpm_simple extends Toast {

    function Bpm_simple() {
        parent::Toast(__FILE__);
        $this->load->model('bpm/bpm');
        $this->zips = APPPATH . 'modules/test/assets/zip/';
        $this->zips = FCPATH . $config["bpm_file_path"]."zip/";
        // Load any models, libraries etc. you need here
    }

    /**
     * OPTIONAL; Anything in this function will be run before each test
     * Good for doing cleanup: resetting sessions, renewing objects, etc.
     */
    function _pre() {
        
    }

    /**
     * OPTIONAL; Anything in this function will be run after each test
     * I use it for setting $this->message = $this->My_model->getError();
     */
    function _post() {
        
    }

    /**
     * Fetch a number of URLs as a string
     * 
     * @return string containing the (concatenated) HTML documents
     * @param array $urls array of fully qualified URLs
     */
    function _curl_get($urls) {
        $html_str = '';
        foreach ($urls as $url) {
            $curl_handle = curl_init();
            curl_setopt($curl_handle, CURLOPT_URL, $url);
            curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, 1);
            $html_str .= curl_exec($curl_handle);
            curl_close($curl_handle);
        }
        return $html_str;
    }

    /* TESTS BELOW */

    function test_import_trivial() {
        $result = $this->bpm->import($this->zips . 'test_trivial.zip', true, 'Tests');
        $this->message = $result['msg'];
        $this->_assert_equals_strict($result['success'], true);
        $this->idwf = 'test_trivial';
        $this->idcase = 'TEST';
    }
    
    function test_remove_case(){
        $result=$this->bpm->delete_case($this->idwf, $this->idcase);
        $this->message = "Remove Case ID:".$this->idcase;
        $this->_assert_true($result);
    }
    function test_gen_case(){
        $this->idcase=$this->bpm->gen_case($this->idwf, $this->idcase);
        $this->message = "Case ID (is string):".$this->idcase;
        $this->_assert_true(is_string($this->idcase));
    }
    
    function test_get_case(){
        $case=$this->bpm->get_case($this->idcase, $this->idwf);
        $this->message = "Case:".json_encode($case);
        $this->_assert_equals_strict($case['status'], 'open');
    }
    function test_clear_tokens() {
        $result = $this->bpm->clear_tokens($this->idwf, $this->idcase);
    }

    function test_clear_case() {
        $result = $this->bpm->clear_case($this->idwf, $this->idcase);
    }

    function test_start_case() {
        $url[]=site_url('bpm/engine/startcase/model/'.$this->idwf.'/'.$this->idcase);
        $result=$this->_curl_get($url);
        $this->message =$result;
    }

    function test_run() {
        $url[]=site_url('bpm/engine/run/model/'.$this->idwf.'/'.$this->idcase);
        $result=$this->_curl_get($url);

        $this->message =$result;
	   // $this->_assert_equals_strict($result['success'], true);
    }

 
}

// End of file example_test.php */
// Location: ./system/application/controllers/test/example_test.php */