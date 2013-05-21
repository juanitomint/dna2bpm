<?php

class ui {

        var $CI;
        var $no_loader;
        public function __construct($params = array()) {
                log_message('debug', "Extui Class Initialized");

// Set the super object to a local variable for use throughout the class
                $this->CI = & get_instance();
        }

        /*
         * Compose an ui with $content as main content combining $content into $file as {content} tag
         */

        function compose($content, $file, $data) {
                $this->CI->parser->options['convert_delimiters'] = array(false, '&#123;', '&#125;');
                $data['content'] = $this->CI->parser->parse($content, $data, true, true);
                $this->makeui($file, $data);
        }

        function makeui($file, $data) {


//---Make css string
                $strcss = '';
                if (isset($data['css'])) {
                        foreach ($data['css']as $cssfile => $desc) {
                                $strcss.="<!-- CSS:$desc -->\n";
                                $strcss.="<link rel='stylesheet' type='text/css' href='$cssfile' />\n";
                        }
                }
                $data['css'] = $strcss;

//---Make js string
                $strjs = '';
                if (isset($data['js'])) {
                        foreach ($data['js']as $jsfile => $desc) {
                                $strjs.="<!-- JS:$desc -->\n";
                                //$strjs.="<script type='text/javascript'>try{document.getElementById('loading-msg').innerHTML +=\"<span class='ok'>OK.</span><br/>Loading $desc...\";}catch(e){}</script>\n";
                                $strjs.="<script type='text/javascript' src='$jsfile'></script>\n\n";
                        }
                }
                $data['js'] = $strjs;

//---Make global string
                $globaljs = "''";
                if (isset($data['global_js'])) {
                        $globaljs = json_encode($data['global_js']);

                        /* foreach ($data['global_js']as $var => $value) {
                          $globaljs.="var $var=";
                          switch (gettype($value)){
                          case 'boolean':
                          break;
                          default:
                          $globaljs.="'".addslashes($value)."';\n";
                          }
                          }
                         * 
                         */
                }
                $data['inline_js'] = $globaljs;
                $this->CI->parser->options['convert_delimiters'] = array(false, '&#123;', '&#125;');
                $this->CI->parser->parse($file, $data);
        }

}

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
?>