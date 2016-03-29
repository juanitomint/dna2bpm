<?

class Redmine extends MX_Controller {

    function __construct() {
        parent::__construct();
        $this->load->library('Issue');
        $this->load->config('redmine/config');
    }

    function index() {
        
    }

    /*
     * Create function is public
     */

    function create($data) {
        // create a new issue
        /*
          $data=array(
          'project_id' => '355',
          'subject' => 'Test:' . date('Y-m-d H:i:s'),
          'description' => 'bla balblabl ablabalb 23423424',
          );
         */
        $issue = new Issue(
                $data
        );
        $issue->site = $this->config->item('site');
        $issue->user = $this->config->item('api_key');
        $issue->save();
        return $issue;
    }

    function read($id = null) {
        $this->user->authorize();
        $issue = new Issue();
        $issue->site = $this->config->item('site');
        $issue->user = $this->config->item('api_key');
        $query = ($id) ? $id : 'all';
        $issues = $issue->find($query);
//        
//        if (!$id) {
//            for ($i = 0; $i < count($issues); $i++) {
//                echo $issues[$i]->id . '::' . $issues[$i]->subject . '<br/>';
//            }
//        } else {
//
//        if($issues->_data){
//            echo $issues->id . '::' . $issues->subject . '<br/>';
//        } else {
//            show_error("Issue #$id Not Found.");
//        }
//        }
        return $issues;
    }

    function update($id, $data) {
        $this->user->authorize();
        if ($id and is_array($data)) {
            $issue = new Issue();
            $issue->site = $this->config->item('site');
            $issue->user = $this->config->item('api_key');
            // find and update an issue
            $issue->find($id);
            foreach ($data as $key => $val) {
                $issue->set($key, $val);
            }
            $issue->save();
            return $issue;
        }
    }

    function delete($id) {
        $this->user->authorize();
        // delete an issue
        if ($id) {
            $issue = new Issue();
            $issue->site = $this->config->item('site');
            $issue->user = $this->config->item('api_key');
            $issue->find($id);
            $issue->destroy();
            return true;
        } else {
            return false;
        }
    }

}

?>
