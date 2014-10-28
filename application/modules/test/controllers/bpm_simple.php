<?php
require_once(APPPATH. 'modules/test/controllers/Toast.php');

class Bpm_simple extends Toast
{
	function Bpm_simple()
	{
		parent::Toast(__FILE__);
        $this->load->model('bpm/bpm');
		// Load any models, libraries etc. you need here
	}

	/**
	 * OPTIONAL; Anything in this function will be run before each test
	 * Good for doing cleanup: resetting sessions, renewing objects, etc.
	 */
	function _pre() {}

	/**
	 * OPTIONAL; Anything in this function will be run after each test
	 * I use it for setting $this->message = $this->My_model->getError();
	 */
	function _post() {}


	/* TESTS BELOW */

	function test_import()
	{
		$result=$this->bpm->import('images/zip/fondyfpp.zip');
        $this->message =$result['msg'];
		$this->_assert_equals_strict($result['success'], true);
	}
    function test_run(){
        $result=$this->bpm->import('images/zip/test_trivial.zip',true,'Tests');
        Modules::run('');
    }

	function test_that_fails()
	{
		$a = true;
		$b = $a;

		// You can test multiple assertions / variables in one function:

		$this->_assert_true($a); // true
		$this->_assert_false($b); // false
		$this->_assert_equals($a, $b); // true

		// Since one of the assertions failed, this test case will fail
	}


	function test_or_operator()
	{
		$a = true;
		$b = false;
		$var = $a || $b;

		$this->_assert_true($var);

		// If you need to, you can pass a message /
		// description to the unit test results page:

		$this->message = '$a || $b';
	}

}

// End of file example_test.php */
// Location: ./system/application/controllers/test/example_test.php */