<?php

class util extends MX_Controller {

    function __construct() {
        parent::__construct();
        $this->load->library('parser');
        $this->load->model('user');
        $this->load->model('group');
        $this->idu = (double) $this->session->userdata('iduser');
    }

    function Index() {

    }

    //---return a json representation of a user.
    function get_user() {
        $segments = $this->uri->segment_array();
        $debug = (in_array('debug', $segments)) ? true : false;
        $iduser = ($this->input->post('idu')) ? $this->input->post('idu') : 1;
        $user = $this->user->get_user($iduser);
        //---Available
        /* {"_id":{"$id":"4e82d4263ad5e0956f00004f"},
         * "idu"
         * "idgroup"
         * "nick"
         * "passw"
         * "name"
         * "lastname"
         * "idnumber"
         * "birthDate"
         * "perm"
         * "checkdate"
         * "lastacc"
         * "id"
         * "group"
         *
         */
        $rtnU['idu']=$user->idu;
        $rtnU['nick']=$user->nick;
        $rtnU['name']=$user->name;
        $rtnU['lastname']=$user->lastname;
        $rtn=array('rows'=>$rtnU);
        if (!$debug) {
            $this->output->set_content_type('json','utf-8');
            $this->output->set_output(json_encode($rtn));
        } else {
            var_dump($rtn);
        }


    }
    function checknick(){
        $rtn=true;
        if($this->input->post('nick')){
            $rs=$this->user->getbynick($this->input->post('nick'));
            $rtn=(!empty($rs))?false:true;
        }
        $this->output->set_content_type('json','utf-8');
        $this->output->set_output(json_encode($rtn));
    }

    function get_active_user($debug=false) {
        //---check login



        if(!$this->session->userdata('loggedin')){
            show_error('Session Expired<br>', 500);
            exit;
        }
        $this->session->set_userdata('lastsee',date('H:i:s'));
        $rtnU['sess_updated']=$this->sess_update();

        $iduser = $this->user->idu;
        $user = $this->user->get_user($iduser);
        //---Available
        /* {"_id":{"$id":"4e82d4263ad5e0956f00004f"},
         * "idu"
         * "idgroup"
         * "nick"
         * "passw"
         * "name"
         * "lastname"
         * "idnumber"
         * "birthDate"
         * "perm"
         * "checkdate"
         * "lastacc"
         * "id"
         * "group"
         *
         */
        // $rtnU+=$this->session->userdata;
        $rtnU['idu']=$user->idu;
        $rtnU['nick']=$user->nick;
        $rtnU['name']=$user->name;
        $rtnU['lastsee']=$this->session->userdata('lastsee');
        $rtnU['lastname']=$user->lastname;
        $rtnU['ajax']=$this->input->is_ajax_request();
        $rtnU['sess_time_to_update']=$this->session->now-($this->session->userdata['last_activity'] + $this->session->sess_time_to_update);
        $rtnU['sess_time_to']=($this->session->userdata['last_activity'] + $this->session->sess_time_to_update >= $this->session->now);

        if (!$debug) {
            $this->output->set_header('Last-Modified: '.gmdate('D, d M Y H:i:s', $last_update).' GMT');
            $this->output->set_header("Cache-Control: no-store, no-cache, must-revalidate");
            $this->output->set_header("Cache-Control: post-check=0, pre-check=0");
            $this->output->set_header("Pragma: no-cache");
            $this->output->set_content_type('json','utf-8');
            $this->output->set_output(json_encode($rtnU));
        } else {
            var_dump($rtnU);
        }


    }

    function sess_update()	{
		// We only update the session every five minutes by default
    // 		if ($this->session->userdata['last_activity'] + $this->session->sess_time_to_update >= $this->session->now)
    // 		{
    // 			return false;
    // 		}

		// Save the old session id so we know which record to
		// update in the database if we need it
		$old_sessid = $this->session->userdata['session_id'];
		$new_sessid = '';
		while (strlen($new_sessid) < 32)
		{
			$new_sessid .= mt_rand(0, mt_getrandmax());
		}

		// To make the session ID even more secure we'll combine it with the user's IP
		$new_sessid .= $this->input->ip_address();

		// Turn it into a hash
		$new_sessid = md5(uniqid($new_sessid, TRUE));

		// Update the session data in the session data array
// 		$this->session->userdata['session_id'] =$old_sessid;
		$this->session->userdata['last_activity'] = $this->now;

		// _set_cookie() will handle this for us if we aren't using database sessions
		// by pushing all userdata to the cookie.
		$cookie_data = NULL;

		// Update the session ID and last_activity field in the DB if needed
		if ($this->session->sess_use_database === TRUE)
		{
			// set cookie explicitly to only have our session data
			$cookie_data = array();
			foreach (array('session_id','ip_address','user_agent','last_activity') as $val)
			{
				$cookie_data[$val] = $this->userdata[$val];
			}

			$this->db->query($this->db->update_string($this->sess_table_name, array('last_activity' => $this->now, 'session_id' => $new_sessid), array('session_id' => $old_sessid)));
		}

		// Write the cookie
    //$this->session->_set_cookie($cookie_data);
    return true;
	}
}

?>
