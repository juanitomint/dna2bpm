<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Mongo_gridfs extends MX_Controller {
	
    function __construct() {
        parent::__construct();
        $this->user->authorize();
        $this->base_url = base_url();
        $this->module_url = base_url() . $this->router->fetch_module() . '/';
    }
    
	public function index()
	{
		$this->load->helper('url');
		echo '<h1>'.anchor('test/mongo_gridfs/upload' ).'</h1>';
		$this->load->library('gridfs');
		$file='index.php';
		// $this->gridfs->grid->drop();
		$rs=$this->gridfs->storeFile($file,array('pico'=>'pato'));
		echo '<h1>storeFile</h1>';
		var_dump($rs);
		$rs=$this->gridfs->put($file,array('path'=>'/juanb/','pico'=>'pato'));
		echo '<h1>put</h1>';
		var_dump($rs);
		$rs=$this->gridfs->find(array(
			'filename'=>new MongoRegex("/index/")
			)
			);
		echo '<h1>find</h1>';
		var_dump($rs->count());
		
		$rs=$this->gridfs->findOne(
			array(
				'metadata.path'=>'/juanb/'
			)
			);
		echo '<h1>findOne (metadata)</h1>';
		var_dump($rs);
		
		$rs=$this->gridfs->findOne(array(
			'filename'=>new MongoRegex("/index/")
			)
			);
		echo '<h1>findOne</h1>';
		var_dump($rs);
		
		$ff=$this->gridfs->get($rs->file['_id']);
		echo '<h1>getBytes</h1>';
		var_dump($ff->getBytes());
		
	}
	
	function upload(){
		$this->load->helper('form');
		$this->load->view('test/mongo_upload', array('error' => ' ' ));	
	}
	
	function do_upload(){
		var_dump($_FILES);
		$this->load->library('gridfs');
		$name = $_FILES['Filedata']['name'];
		$id=$this->gridfs->storeUpload('Filedata');
		var_dump($id);
		$ff=$this->gridfs->get($id);
		var_dump($ff->getBytes());
	}
	
}
