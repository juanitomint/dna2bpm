<?php

class ui {

    var $CI;
    var $scripts;
    var $styles;

    public function __construct($params = array()) {
        log_message('debug', "Extui Class Initialized");

// Set the super object to a local variable for use throughout the class
        $this->CI = & get_instance();
        // Register Scripts
    }

    /*
     * Compose an ui with $content as main content combining $content into $file as {content} tag
     */

    function compose($file, $data) {
        $this->CI->parser->options['convert_delimiters'] = array(false, '&#123;', '&#125;');

        // Register Scripts
        $this->register_script('jquery', $data['base_url'] . 'jscript/jquery/jquery.min.js');
        $this->register_script('jqueryUI', $data['base_url'] . 'jscript/jquery/ui/jquery-ui-1.10.2.custom/jquery-ui-1.10.2.custom.min.js', array('jquery'));
        $this->register_script('bootstrap', $data['module_url'] . 'assets/bootstrap-wysihtml5/js/bootstrap.min.js');
        $this->register_script('raphael', '//cdnjs.cloudflare.com/ajax/libs/raphael/2.1.0/raphael-min.js');
        $this->register_script('morris', $data['module_url'] . 'assets/bootstrap-wysihtml5/js/plugins/morris/morris.min.js');
        $this->register_script('sparkline', $data['module_url'] . 'assets/bootstrap-wysihtml5/js/plugins/sparkline/jquery.sparkline.min.js', array('jquery'));
        $this->register_script('jvectormap', $data['module_url'] . 'assets/bootstrap-wysihtml5/js/plugins/jvectormap/jquery-jvectormap-1.2.2.min.js', array('jquery'));
        $this->register_script('jvectormap.world.mill', $data['module_url'] . 'assets/bootstrap-wysihtml5/js/plugins/jvectormap/jquery-jvectormap-world-mill-en.js', array('jquery', 'jvectormap'));
        $this->register_script('fullcalendar-moment', $data['module_url'] . 'assets/bootstrap-wysihtml5/js/plugins/fullcalendar-2.3.1/lib/moment.min.js');         
        $this->register_script('fullcalendar', $data['module_url'] . 'assets/bootstrap-wysihtml5/js/plugins/fullcalendar-2.3.1/fullcalendar.min.js');
        $this->register_script('fullcalendar-lang', $data['module_url'] . 'assets/bootstrap-wysihtml5/js/plugins/fullcalendar-2.3.1/lang-all.js',array('fullcalendar'));
        
        
        
        $this->register_script('knob', $data['module_url'] . 'assets/bootstrap-wysihtml5/js/plugins/jqueryKnob/jquery.knob.js', array('jquery'));
        $this->register_script('daterangerpicker', $data['module_url'] . 'assets/bootstrap-wysihtml5/js/plugins/daterangepicker/daterangepicker.js');
        $this->register_script('WYSIHTML5', $data['module_url'] . 'assets/bootstrap-wysihtml5/js/plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.all.min.js');
        $this->register_script('adminLTE', $data['module_url'] . 'assets/bootstrap-wysihtml5/js/AdminLTE/app.js', array('bootstrap', 'WYSIHTML5'));
        $this->register_script('dashboardJS', $data['module_url'] . 'assets/jscript/app.js', array('jquery', 'WYSIHTML5'));
        $this->register_script('PLUpload', $data['base_url'] . 'jscript/plupload-2.1.2/plupload.full.min.js', array('jquery'));
        $this->register_script('jquery.form', $data['base_url'] . 'jscript/jquery/plugins/Form/jquery.form.min.js', array('jquery'));
        $this->register_script('icheck', $data['module_url'] . 'assets/bootstrap-wysihtml5/js/plugins/iCheck/icheck.min.js', array('WYSIHTML5'));
        $this->register_script('selectJS', $data['base_url'] . 'jscript/select2-3.4.5/select2.min.js', array());
        $this->register_script('inboxJS', $data['base_url'] . 'inbox/assets/jscript/inbox.js', array('jquery'));
        $this->register_script('highCharts', $data['base_url'] . 'jscript/Highcharts-4.0.4/js/highcharts.js', array('jquery'));
        $this->register_script('inboxJS', $data['base_url'] . 'inbox/assets/jscript/inbox.js', array('jquery'));
        $this->register_script('ckeditor', $data['base_url'] . 'jscript/ckeditor/ckeditor.js', array('jquery'));
        $this->register_script('jquery.smooth-scroll', $data['base_url'] . 'jscript/jquery/plugins/jquery.smooth-scroll.min.js', array('jquery'));
        $this->register_script('jquery.matchHeight', $data['base_url'] . 'jscript/jquery/plugins/jquery.matchHeight-min.js', array('jquery'));
        
        
        //===== CSS loaded only when same JS  handle is loaded
        $this->styles['morris'][] = $data['base_url'] . "dashboard/assets/bootstrap-wysihtml5/css/morris/morris.css";
        $this->styles['jvectormap'][] = $data['base_url'] . "dashboard/assets/bootstrap-wysihtml5/css/jvectormap/jquery-jvectormap-1.2.2.css";
        $this->styles['fullcalendar'][] = $data['base_url'] . "dashboard/assets/bootstrap-wysihtml5/js/plugins/fullcalendar-2.3.1/fullcalendar.css";
        $this->styles['selectJS'][] = $data['base_url'] . "jscript/select2-3.4.5/select2.css";
        $this->styles['selectJS'][] = $data['base_url'] . "jscript/select2-3.4.5/select2-bootstrap.css";
        $this->styles['inboxJS'][] = $data['base_url'] . "inbox/assets/css/inbox.css";
        $this->styles['daterangerpicker'][] = $data['base_url'] . "dashboard/assets/bootstrap-wysihtml5/css/daterangepicker/daterangepicker.css";
        
        // Load default JS 
        $default = array('jquery', 'jqueryUI','jquery.smooth-scroll','jquery.matchHeight','bootstrap','WYSIHTML5','adminLTE', 'inboxJS','dashboardJS', 'jquery.form');
 
        //Custom JS Check
        if (isset($data['js'])) {
            foreach ($data['js'] as $k => $js) {
                if (is_numeric($k)) {
                    // Is a handle let's addit to default
                    if (!in_array($js, $default))
                        $default[] = $js;
                }else {
                    $data['custom_js'][$k] = $js;
                }
            }
        }

        // CSS for scripts enqueued
        $data['widgets_css'] = "";
        foreach ($default as $myjs) {
            if (isset($this->styles[$myjs])) {        	
                //$data['widgets_css'] = "";
                foreach ($this->styles[$myjs] as $k => $mycss) {              	
                    $data['widgets_css'].=$this->custom_styles(array($mycss => $myjs));
                   // var_dump( $data['widgets_css'],"-----");
                }
            }
        }
        
        // Custom JS from user
        if (isset($data['custom_js']))
            $data['js'] = $this->custom_scripts($data['custom_js']);

        //Custom CSS from user
        if (isset($data['css']))
            $data['custom_css'] = $this->custom_styles($data['css']);

        // Globals JS
        if (isset($data['global_js']))
            $data['global_js'] = $this->global_scripts($data['global_js']);

        $data['footer'] = $this->enqueue_scripts($default);
        // Flush!!

        $this->CI->parser->parse($file, $data, false, true);
    }

    /*
     * Place the JS in the right dependency order and avoids duplicity
     */

    private function enqueue_scripts($stack) {
        $ready = array();

        while (!empty($stack)) {

            $current = array_shift($stack);
            if (in_array($current, $ready))
                continue; // avoids duplicity
            $mydeps = $this->scripts[$current]['dep'];

            if (isset($this->scripts[$current]) && empty($mydeps)) {
                // If hs no dependencies is added to $ready
                $ready[] = $current;
            } else {
                // has dependencies 
                $check = array_intersect($mydeps, $ready);

                if (count($mydeps) == count($check)) {
                    //all dependecies are inside ready now can insert 
                    $ready[] = $current;
                } else {
                    // some dependency is missing
                    $stack[] = $current;
                }
            }
        }

        // Now $ready has the proper order , make the magic
        $strjs = '';

        foreach ($ready as $handle) {
            $strjs.="<script  src='{$this->scripts[$handle]['source']}'></script>\n\n";
        }
        return $strjs;
    }

    //==== Get custom Scripts
    private function custom_scripts($js = array()) {
        //---Make js string
        $strjs = '';
        foreach ($js as $jsfile=>$desc) {
            $jsfile=str_replace('{base_url}',  base_url(),$jsfile);
            $strjs.="<!-- JS:$desc -->\n";
            //if(!stristr($desc,'http://'))$jsfile.=$data['base_url'].$jsfile; // Si viene sin base_url
            //$strjs.="<script type='text/javascript'>try{document.getElementById('loading-msg').innerHTML +=\"<span class='ok'>OK.</span><br/>Loading $desc...\";}catch(e){}</script>\n";
            $strjs.="<script  src='$jsfile'></script>\n\n";
        }
        return $strjs;
    }

    //==== Get custom Styles
    private function custom_styles($css = array()) {
    	global $data;
        $strcss = '';
        foreach ($css as $cssfile => $desc) {
        	
            $cssfile=str_replace('{base_url}',  base_url(),$cssfile);
            
            
/*             if(!stristr($cssfile,'http://'))$cssfile.=$data['base_url'].$cssfile; // Si viene sin base_url */

            $strcss.="<!-- CSS:$desc -->\n";
            $strcss.="<link rel='stylesheet' type='text/css' href='$cssfile' />\n";
            
        }

        return $strcss;
    }

    //==== Get Globals
    private function global_scripts($js) {
        $globaljs = "''";
        return json_encode($js);
    }

    /* ==== Register Scripts
     * Handle: Unique id for the link
     * Source: resource URL
     * Dep: array containing dependencies handle 
     * footer: @todo 
     * 
     */

    private function register_script($handle, $source, $dep = array(), $footer = true) {
        $this->scripts[$handle] = array('source' => $source, 'dep' => $dep, 'footer' => $footer);
    }

    function show_scripts() {
        var_dump($this->scripts);
    }


/* ============= UI Elements  =============  */


//=== Callouts
function callout($config=array()){
    // class: info,warning,danger
    $default=array('body'=>'','title'=>'untitled','class'=>'info');
    $default['_id']=md5(time()); // selector
    $data=array_merge( $default,$config);
    return $this->CI->parser->parse('dashboard/ui/callout', $data, true,true);
}


//=== Alerts
function alert($config=array()){
    // class: info,success,warning,danger
    $default=array('body'=>'','class'=>'info','dismissable'=>true,'icon'=>true,'icon_class'=>'fa-info');
    $default['_id']=md5(time()); // selector
    $data=array_merge( $default,$config);

    return $this->CI->parser->parse('dashboard/ui/alert', $data, true,true);
}

// wrappers
function alert_info($body){
    // class: info,success,warning,danger
    $config=array('body'=>$body,'class'=>'info','dismissable'=>true,'icon'=>true,'icon_class'=>'fa-info');
    return $this->alert($config);
}

function alert_danger($body){
    // class: info,success,warning,danger
    $config=array('body'=>$body,'class'=>'danger','dismissable'=>true,'icon'=>true,'icon_class'=>'fa-ban');
    return $this->alert($config);
}

function alert_warning($body){
    // class: info,success,warning,danger
    $config=array('body'=>$body,'class'=>'warning','dismissable'=>true,'icon'=>true,'icon_class'=>'fa-warning');
    return $this->alert($config);
}

function alert_success($body){
    // class: info,success,warning,danger
    $config=array('body'=>$body,'class'=>'success','dismissable'=>true,'icon'=>true,'icon_class'=>'fa-check');
    return $this->alert($config);
}

//=== progress
function progress($config=array()){
    // class: info,success,warning,danger
    // size: sm,xs
    $default=array('width'=>'100','class'=>'info','active'=>true,'stripped'=>true,'size'=>'');
    $default['_id']=md5(time()); // selector
    $data=array_merge( $default,$config);

    return $this->CI->parser->parse('dashboard/ui/progress', $data, true,true);
}

// wrappers
function progress_info($width){
    $config=array('width'=>$width);
    return $this->progress($config);
}

function progress_danger($width){
    $config=array('width'=>$width,'class'=>'danger');
    return $this->progress($config);
}

function progress_warning($width){
    $config=array('width'=>$width,'class'=>'warning');
    return $this->progress($config);
}

function progress_success($width){
    $config=array('width'=>$width,'class'=>'success');
    return $this->progress($config);
}



//=== ULs

function ul($data,$unstyled=false){
    $class=($unstyled)?('list-unstyled'):('');
    return "<ul class='$class'>".$this->arr2ul($data,'ul',$unstyled)."</ul>";
}

function ol($data,$unstyled=false){
    $class=($unstyled)?('list-unstyled'):('');
    return "<ol class='$class'>".$this->arr2ul($data,'ol',$unstyled)."</ol>";
}

private function arr2ul($data,$tag='ol',$unstyled=false){
    $block="";
    foreach($data as $ul){
        if(is_array($ul)){
            $block.="<$tag>\n";
            $block.=$this->ol($ul,$unstyled);
            $block.="</$tag>\n";
        }else{
           $block.="<li>$ul</li>\n";
        }
    }
     return $block;
}

//=== Pagination 

 function paginate($text=null,$config=array()){
        
        if(is_null($text))return;
        
        $default=array(
            'sep'=>'<!-- pagebreak -->',
            'width'=>3,
            'align'=>'right'
            );
        $config=array_merge($default,$config);

        //=== Content
        
        if(is_array($text)){
            $text=implode($config['sep'],$text);
        }
        $pages=explode($config['sep'],$text);
        $count=count($pages);

        $li="";
        $anchors=array();

        for($i=0;$i<$count;$i++){
            $i2=$i+1;
            if($i>0){
               
                $li.="<li style='display:none' data-page='$i2'>{$pages[$i]}</li>";
            }else{
                 $li.="<li data-page='$i2'>{$pages[$i]}</li>";
            }
        }

        $id=md5(time());
        $ret="<ul data-id='$id' class='list-unstyled'>$li</ul>";
        
        // Buttons
        
        $data_group=1;
        $extra=($count%$config['width']);
        $extra=$count+($config['width']-$extra);

        for($i=0,$j=1;$i<$extra;$i++){
            $disabled=($i>=$count)?('disabled'):('');
             $i2=$i+1;
            if($i<$config['width']){
                $anchors[]="<li class='$disabled '><a href='#' data-link='$i2' data-group='$data_group'>$i2</a></li>";
            }else{
                $anchors[]="<li class='$disabled'><a href='#' data-link='$i2' data-group='$data_group' style='display:none'>$i2</a></li>";
            }
            $j++;
            if($j>$config['width']){
                $j=1;
                $data_group++;
            }
        }
        

        // Render    
        $li2=implode('',$anchors);
        $disabled=($count>$config['width'])?(''):('disabled');
        $ret.=<<<_EOF_
        <nav class='text-{$config['align']}'>
          <ul class="pagination" data-width="{$config['width']}" data-target="$id">
            <li class="disabled" data-link="back">
              <a href="#" aria-label="Previous" data-link="back">
                <span aria-hidden="true">&laquo;</span>
              </a>
            </li>
                $li2
            <li class="$disabled" data-link="next">
              <a href="#" aria-label="Next" data-link="next">
                <span aria-hidden="true">&raquo;</span>
              </a>
            </li>
          </ul>
        </nav>
_EOF_;


        return $ret;
    }



}

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */