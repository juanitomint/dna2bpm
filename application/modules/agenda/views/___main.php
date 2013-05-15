<!doctype html>
<!-- paulirish.com/2008/conditional-stylesheets-vs-css-hacks-answer-neither/ -->
<!--[if lt IE 7]> <html class="no-js lt-ie9 lt-ie8 lt-ie7" lang="en"> <![endif]-->
<!--[if IE 7]>    <html class="no-js lt-ie9 lt-ie8" lang="en"> <![endif]-->
<!--[if IE 8]>    <html class="no-js lt-ie9" lang="en"> <![endif]-->
<!-- Consider adding a manifest.appcache: h5bp.com/d/Offline -->
<!--[if gt IE 8]><!--> <html class="no-js" lang="en"> <!--<![endif]-->
<head>
  <meta charset="utf-8">

  <!-- Use the .htaccess and remove these lines to avoid edge case issues.
       More info: h5bp.com/i/378 -->
  <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">

  <title>Agenda</title>
  <meta name="description" content="">

  <!-- Mobile viewport optimized: h5bp.com/viewport -->
  <meta name="viewport" content="width=device-width">

<!-- Place favicon.ico and apple-touch-icon.png in the root directory: mathiasbynens.be/notes/touch-icons -->

<!-- First CSS-->
<link rel="stylesheet" href="{module_url}assets/css/style_first.css">
  
<!-- Scheduler -->
<link rel="stylesheet" type="text/css" href="{module_url}assets/jscript/dhtmlxSuite/dhtmlxScheduler_v31/codebase/dhtmlxscheduler.css" />
<!-- Calendar -->
<link rel="STYLESHEET" type="text/css" href="{module_url}assets/jscript/dhtmlxSuite/dhtmlxCalendar/codebase/dhtmlxcalendar.css"></link>
<!-- Tree -->
<link rel="STYLESHEET" type="text/css" href="{module_url}assets/jscript/dhtmlxSuite/dhtmlxTree/codebase/dhtmlxtree.css" />
<!-- Layout -->
<link rel="stylesheet" type="text/css" href="{module_url}assets/jscript/dhtmlxSuite/dhtmlxLayout/codebase/dhtmlxlayout.css" />
<link rel="stylesheet" type="text/css" href="{module_url}assets/jscript/dhtmlxSuite/dhtmlxLayout/codebase/skins/dhtmlxlayout_dhx_skyblue.css" />
<!-- WindowX -->
<link rel="stylesheet" type="text/css" href="{module_url}assets/jscript/dhtmlxSuite/dhtmlxWindows/codebase/dhtmlxwindows.css"/>
<link rel="stylesheet" type="text/css" href="{module_url}assets/jscript/dhtmlxSuite/dhtmlxWindows/codebase/skins/dhtmlxwindows_dhx_skyblue.css"/>
<link rel="stylesheet" type="text/css" href="{module_url}assets/jscript/libs/UI/ui-lightness/jquery-ui-1.8.20.custom.css"/>


<style type="text/css" >
    /* Maneja los colores de las agendas */
        {agenda_colors}
            .agenda{id}{color:#{color}}
        {/agenda_colors}
        
    /* Importante*/
        .dhx_cal_cover{
            z-index:1;
            display:none;
        }
</style>
<link rel="STYLESHEET" type="text/css" href="{module_url}assets/css/main.css" media="screen" />  

<!-- Last CSS -->
<link rel="stylesheet" href="{module_url}assets/css/style_last.css">

  <!-- More ideas for your <head> here: h5bp.com/d/head-Tips -->

  <!-- All JavaScript at the bottom, except this Modernizr build.
       Modernizr enables HTML5 elements & feature detects for optimal performance.
       Create your own custom Modernizr build: www.modernizr.com/download/ -->
  <script src="{module_url}assets/jscript/libs/modernizr-2.5.3.min.js"></script>
</head>
<body>
  <!-- Prompt IE 6 users to install Chrome Frame. Remove this if you support IE 6.
       chromium.org/developers/how-tos/chrome-frame-getting-started -->
  <!--[if lt IE 7]><p class=chromeframe>Your browser is <em>ancient!</em> <a href="http://browsehappy.com/">Upgrade to a different browser</a> or <a href="http://www.google.com/chromeframe/?redirect=true">install Google Chrome Frame</a> to experience this site.</p><![endif]-->
 
<!-- MENU LATERAL  -->

<div id="menu" style="width:100%;height:100%;overflow:auto">
    <div class="item-menu"><p id="agendas_visibles">Agendas visibles</p></div>
    <div id="tree_agendas"></div>
    <div class="item-menu"><a href="#"  id="bt_listado"  title="Listado" onclick="openListado();return false">Listado</a></div>
    <div class="item-menu"><a href="{base_url}user/logout" title="Cerrar sesión" id="bt_close">Cerrar sesi&oacute;n</a></div>
    <div class="item-menu"><a href=""  title="Imprimir" id="bt_print" onclick="print_page();return false">Imprimir</a></div>
    <div class="item-menu"><a href=""  title="Opciones" id="bt_opciones" onclick="openOpciones();return false">Opciones</a></div>
    <div class="item-menu"><a href="help.doc"  title="Ayuda" target="_blank" id="bt_help" >Ayuda</a></div>
</div>

<!-- xxxxxxxxxxx  Scheduler  xxxxxxxxxxx -->

<div id="scheduler_here" class="dhx_cal_container" style='width:100%;height:100%'>
            <div class="dhx_cal_navline">
                <div class="dhx_cal_prev_button">&nbsp;</div>
                <div class="dhx_cal_next_button">&nbsp;</div>
                <div class="dhx_cal_today_button"></div>
                <div class="dhx_cal_date"></div>
                <div class="dhx_cal_tab" name="day_tab" style="right:270px;"></div>
                <div class="dhx_cal_tab" name="workweek_tab" style="right:204px"></div>
                <div class="dhx_cal_tab" name="week_tab" style="right:140px"></div>
                <div class="dhx_cal_tab" name="month_tab" style="right:76px;"></div>
            </div>
            <div class="dhx_cal_header">
            </div>
            <div class="dhx_cal_data">
            </div
    </div>

<!-- xxxxxxxxxxx  Scheduler  xxxxxxxxxxx -->
<div id="msg2" style="display:none"></div>
<div id="pop1" title=""></div>    

<!-- JavaScript at the bottom for fast page loading -->
  
<!-- AJAX Components JS -->
<script  type="text/javascript" src="{module_url}assets/jscript/dhtmlxSuite/dhtmlxWindows/codebase/dhtmlxcommon.js" ></script> 

<!-- Scheduler -->
<script  type="text/javascript" src="{module_url}assets/jscript/dhtmlxSuite/dhtmlxScheduler_v31/codebase/dhtmlxscheduler.js"  ></script>
<!-- Scheduler -->
<script  type="text/javascript" src="{module_url}assets/jscript/dhtmlxSuite/dhtmlxScheduler_v31/codebase/ext/dhtmlxscheduler_recurring.js"  ></script>

<script  type="text/javascript" src="{module_url}assets/jscript/dhtmlxSuite/dhtmlxScheduler_v31/codebase/locale_es.js"></script>

<!-- Calendar -->
<script type="text/javascript" src="{module_url}assets/jscript/dhtmlxSuite/dhtmlxCalendar/codebase/dhtmlxcalendar.js"></script>
<script type="text/javascript" src="{module_url}assets/jscript/dhtmlxSuite/dhtmlxCalendar/codebase/dhtmlxcalendar_es.js"></script>

<!-- Tree -->
<script  type="text/javascript" src="{module_url}assets/jscript/dhtmlxSuite/dhtmlxTree/codebase/dhtmlxtree.js" ></script>
<script  type="text/javascript" src="{module_url}assets/jscript/dhtmlxSuite/dhtmlxTree/codebase/ext/dhtmlxtree_start.js" ></script>
<script  type="text/javascript" src="{module_url}assets/jscript/dhtmlxSuite/dhtmlxTree/codebase/ext/dhtmlxtree_ed.js" ></script>

<!-- Layout -->
<script type="text/javascript"  src="{module_url}assets/jscript/dhtmlxSuite/dhtmlxLayout/codebase/dhtmlxlayout.js"  ></script>
<script type="text/javascript"  src="{module_url}assets/jscript/dhtmlxSuite/dhtmlxLayout/codebase/dhtmlxcontainer.js"  ></script>

<!-- WindowX -->
<script  type="text/javascript" src="{module_url}assets/jscript/dhtmlxSuite/dhtmlxWindows/codebase/dhtmlxwindows.js"></script>

<!-- Jquery -->
<script src="{module_url}assets/jscript/libs/jquery-1.7.2.min.js"></script>
<script src="{module_url}assets/jscript/libs/jquery-ui-1.8.20.custom.min.js"></script>



<!-- scripts concatenated and minified via build script -->
<script src="{module_url}assets/jscript/plugins.js"></script>
<script src="{module_url}assets/jscript/script.js"></script>
<!-- end scripts -->

  

  
 <script type="text/javascript">              
                   
 // JQUERY ONLOAD //
$(document).ready(function(){

// xxxxxxxxxxx LAYOUT

var dhxLayout = new dhtmlXLayoutObject(document.body, "2U");
dhxLayout.cells("a").attachObject("menu");
dhxLayout.cells("a").setWidth("200");
dhxLayout.cells("a").setText("Menu");
dhxLayout.cells("b").attachObject("scheduler_here");
dhxLayout.cells("b").setText("{username}");
       
     
// xxxxxxxxxxx AGENDA    
scheduler.config.multi_day = true;
scheduler.config.details_on_create = true;
scheduler.config.first_hour = 8;
scheduler.config.last_hour = 22;
scheduler.config.show_loading=true;
scheduler.config.details_on_dblclick=true;
scheduler.config.full_day  = true;
scheduler.config.time_step = 15;
scheduler.config.event_duration = 30;
scheduler.config.auto_end_date = true;
scheduler.config.xml_date="%Y-%m-%d %H:%i";
scheduler.init('scheduler_here',null,"week");
scheduler.load("{module_url}assets/events2010.xml");
scheduler.config.hour_size_px='50'; //@todo:  Cookie
  

scheduler.update_view();
dhxLayout.attachEvent("onCollapse", function(){
 scheduler.update_view();
});
 dhxLayout.attachEvent("onPanelResizeFinish", function(){
 scheduler.update_view();
});
 dhxLayout.attachEvent("onExpand", function(){
 scheduler.update_view();//
});



});// JQuery Onload

/* xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx
 *              FUNCIONES
 * xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx */        


 
        
</script>


           

</body>
</html>