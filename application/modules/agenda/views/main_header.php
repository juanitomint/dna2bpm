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

<link rel="stylesheet" type="text/css" href="{module_url}assets/jscript/dhtmlxSuite/dhtmlxTabbar/codebase/dhtmlxtabbar.css">
<style type="text/css" >
    /* Maneja los colores de las agendas */
        {agenda_colors}
            .agenda{id}{color:{color}}
        {/agenda_colors}
        
    /* Importante*/
        .dhx_cal_cover{
            z-index:1;
            display:none;
        }
</style>
<link rel="STYLESHEET" type="text/css" href="{module_url}assets/css/main.css" media="screen" />       
<link rel="STYLESHEET" type="text/css" href="{module_url}assets/css/P_main.css" media="print" />

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
            <div class="item-menu"><p id="agendas_visibles">{my_agenda}</p></div>
            <div id="tree_agendas"></div>
            <div class="item-menu"><a href="#"  id="bt_listado"  title="Listado" onclick="open_listado();return false">{listing}</a></div>
            <div class="item-menu"><a href="{base_url}user/logout" title="Cerrar sesión" id="bt_close">{close_session}</a></div>
            <div class="item-menu"><a href="{module_url}printer"  title="Imprimir" id="bt_print" target="_blank">{print}</a></div>
            <div class="item-menu"><a href=""  title="Opciones" id="bt_opciones" onclick="open_opciones();return false">{options}</a></div>
            <div class="item-menu"><a href="help.doc"  title="Ayuda" target="_blank" id="bt_help" >{help}</a></div>

            <!-- <div class="item-menu"><a href="#"  onclick="scheduler.toPDF('{module_url}main/PdfGenerate')" title="Ayuda" target="_blank" id="bt_help" >PDF</a></div>-->