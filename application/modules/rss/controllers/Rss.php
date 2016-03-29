<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Rss extends MX_Controller {
    
    public $items = array();
    public $title = 'My feed title';
    public $description = 'My feed description';
    public $link;
    public $pubdate;
    public $lang;
    
    function __construct() {
        parent::__construct();
        $this->load->library('parser');
        $this->load->library('pagination');
        //---base variables
        $this->base_url = base_url();
        $this->module_url = base_url() . $this->router->fetch_module().'/';
        $this->user->authorize();
    }

    /**
     * Add new item to $items array
     *
     * @param string $title
     * @param string $author
     * @param string $link
     * @param string $pubdate
     * @param string $description
     *
     * @return void
     */
    public function add($title, $author, $link, $pubdate, $description) {
        $this->items[] = array(
            'title' => $title,
            'author' => $author,
            'link' => $link,
            'pubdate' => $pubdate,
            'description' => $description
        );
    }
    
    public function Index(){
        $this->render();
    }

    /**
     * Returns aggregated feed with all items from $items array
     * @param string $format (options: 'atom', 'rss')
     * @return view
     */
    public function render($format = 'atom')
    {

        if (empty($this->lang)) $this->lang = $this->config->item('language');
        if (empty($this->link)) $this->link = $this->config->item('base_url');
        if (empty($this->pubdate)) $this->pubdate = date('D, d M Y H:i:s O');

        $data['channel'] = array(
            'title'=>$this->title,
            'description'=>$this->description,
            'link'=>$this->link,
            'lang'=>$this->lang,
            'pubdate'=>$this->pubdate
        );

        $data['items'] = $this->items;
        $this->output->set_content_type('xml');
        $this->load->view('feed/'.$format, $data);
    }

}