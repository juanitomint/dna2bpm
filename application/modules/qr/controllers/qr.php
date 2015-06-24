<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/**
 * qr
 * 
 * This class scans and generates qrcodes
 * 
 * @author Juan Ignacio Borda <juanignacioborda@gmail.com>
 * @date    May 18, 2013
 *  Level 
 * Level L (Low) 	7% of codewords can be restored.
 * Level M (Medium) 	15% of codewords can be restored.
 * Level Q (Quartile)[33] 	25% of codewords can be restored.
 * Level H (High) 	30% of codewords can be restored.
 */
class Qr extends MX_Controller {

    function __construct() {
        parent::__construct();
        $this->load->model('user');
        $this->user->authorize('modules/qr');
        $this->load->library('parser');
        $this->load->library('ui');
        //---base variables
        $this->base_url = base_url();
        $this->module_url = base_url() . 'qr/';
        //----LOAD LANGUAGE
        $this->idu = (float) $this->session->userdata('iduser');
        //---config
        $this->load->config('config');
    }

    function Gen_demo() {

        $this->gen('www.dna2.org');
    }

    function Gen_url($url = null, $size = '9', $level = 'H') {
        if ($url) {
            $url_gen = base64_decode(urldecode($url));
        }

        if ($this->input->post('url')) {
            $url_gen = $this->input->post('url');
            $size = ($this->input->post('size')) ? $this->input->post('size') : 9;
            $level = ($this->input->post('level')) ? $this->input->post('level') : 'H';
        }

        $this->gen($url_gen, $size, $level);
        //echo "<img src='".$this->module_url."gen_url/".base64_encode($url_gen)."' width='100' height='100'/>";
        //echo base64_encode($url_gen);
    }

    /*
     * Index
     */

    function Index() {
        $cpData['base_url'] = $this->base_url;
        $cpData['module_url'] = $this->module_url;
        $cpData['module_url_encoded'] =$this->encode($this->module_url);
        $cpData['title'] = 'QR Code Demo Page';
        $this->ui->compose('demoindex', 'bootstrap.ui.php', $cpData);
    }
    
    function test_encode(){
        echo $this->encode($this->input->post('url'));
        
    }
    function encode($str){
        
        return urlencode(base64_encode($str));
        
    }
    
    function Gen_vcard() {

        $this->gen(
                'BEGIN:VCARD
                VERSION:3.0
                N:Borda;Juan Ignacio
                FN:Juan Ignacio Borda
                ORG:DNA² Evolutive Computing.
                TITLE:Developer & Code Artist
                TEL;TYPE=WORK,VOICE:+542215430660
                PHOTO;VALUE=URL;TYPE=PNG:http://www.gravatar.com/avatar/8759d36146d2df25a31c959a8b1ad326.png
                EMAIL;TYPE=PREF,INTERNET:juanb@webexperts.com.ar
                URL:http://www.dna2.org
                REV:20130426T103000Z
                END:VCARD', 6);
    }

    function Get_demo() {
        $cpData['base_url'] = $this->base_url;
        $cpData['module_url'] = $this->module_url;
        $cpData['title'] = 'QR Code';
        $cpData['reader_title'] = $cpData['title'];
        $cpData['reader_subtitle'] = 'Read QR Codes from any HTML5 enabled device';
        $cpData['css'] = array(
            $this->module_url . "assets/css/qr.css" => 'custom css',
        );
        $cpData['js'] = array(
            $this->module_url . "assets/jscript/html5-qrcode.min.js" => 'HTML5 qrcode',
            $this->module_url . "assets/jscript/jquery.animate-colors-min.js" => 'Color Animation',
            $this->module_url . "assets/jscript/qr.js" => 'Main functions',
        );

        $cpData['global_js'] = array(
            'base_url' => $this->base_url,
            'module_url' => $this->module_url,
        );
        $this->ui->compose('readqr', 'bootstrap.ui.php', $cpData);
    }

    function Read_demo() {
        $cpData['base_url'] = $this->base_url;
        $cpData['module_url'] = $this->module_url;
        $cpData['title'] = 'QR Code';
        $cpData['reader_title'] = $cpData['title'];
        $cpData['reader_subtitle'] = 'Read QR Codes from any HTML5 enabled device';
        $cpData['css'] = array(
            $this->module_url . "assets/css/qr.css" => 'custom css',
        );
        $cpData['js'] = array(
            $this->module_url . "assets/jscript/html5-qrcode.min.js" => 'HTML5 qrcode',
            $this->module_url . "assets/jscript/jquery.animate-colors-min.js" => 'Color Animation',
            $this->module_url . "assets/jscript/qr.js" => 'Main functions',
        );

        if (!$this->input->post('redir'))
            show_error('error redir');

        $redir = $this->input->post('redir');


        $cpData['global_js'] = array(
            'base_url' => $this->base_url,
            'module_url' => $this->module_url,
            'redir' => $redir,
        );


        $this->ui->compose('readqr', 'bootstrap.ui.php', $cpData);
    }

    function dummy() {

        echo '<i class="icon-ok"></i>' . $this->input->post('data');
    }

    function Read_demo_form() {
        $cpData['base_url'] = $this->base_url;
        $cpData['module_url'] = $this->module_url;
        $cpData['title'] = 'QR Code Form redir';
        $cpData['reader_title'] = $cpData['title'];
        $cpData['reader_subtitle'] = 'Read QR Codes from any HTML5 enabled device';
        $cpData['css'] = array(
            $this->module_url . "assets/css/qr.css" => 'custom css',
        );
        $cpData['js'] = array(
            $this->module_url . "assets/jscript/html5-qrcode.min.js" => 'HTML5 qrcode',
            $this->module_url . "assets/jscript/jquery.animate-colors-min.js" => 'Color Animation',
            $this->module_url . "assets/jscript/qr.form.js" => 'Main functions',
        );

        if (!$this->input->post('redir'))
            show_error('error redir');

        $redir = $this->input->post('redir');
        $cpData['redir'] = $redir;

        $cpData['global_js'] = array(
            'base_url' => $this->base_url,
            'module_url' => $this->module_url,
            'redir' => $redir,
        );
        $this->ui->compose('readqr', 'bootstrap.ui.php', $cpData);
    }

    function Gen($data, $size = '9', $level = 'H') {
        $config['cachedir'] = 'application/modules/qr/cache/';
        if (!is_writable($config['cachedir'])) {
            show_error($config['cachedir'] . ' is not writable');
        }
        $config['errorlog'] = 'application/modules/qr/log/';
        if (!is_writable($config['errorlog'])) {
            show_error($config['errorlog'] . ' is not writable');
        }
        $this->load->library('ciqrcode', $config);
        $params['data'] = $data;
        $params['level'] = $level;
        $params['size'] = $size;
        header("Content-Type: image/png");
        $this->ciqrcode->generate($params);
    }

}