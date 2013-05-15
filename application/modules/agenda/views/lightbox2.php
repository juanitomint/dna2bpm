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

<!-- CSS-->
<link rel="stylesheet" href="{module_url}assets/css/style_first.css">
<link rel="STYLESHEET" type="text/css" href="{module_url}assets/jscript/dhtmlxSuite/dhtmlxCalendar/codebase/dhtmlxcalendar.css"></link>
<link rel="STYLESHEET" type="text/css" href="{module_url}assets/css/detalles2.css" media="screen" />
<link rel="stylesheet" href="{module_url}assets/css/style_last.css">

<link rel="stylesheet" href="{module_url}assets/jscript/libs/UI/ui-lightness/jquery-ui-1.8.20.custom.css">

  <!-- More ideas for your <head> here: h5bp.com/d/head-Tips -->

  <!-- All JavaScript at the bottom, except this Modernizr build.
       Modernizr enables HTML5 elements & feature detects for optimal performance.
       Create your own custom Modernizr build: www.modernizr.com/download/ -->
  <script src="{module_url}assets/jscript/libs/modernizr-2.5.3.min.js"></script>

</head>
<body>
        
        
<div id="lightbox" class="lightbox" >
<div id="calendario1"></div>
<div id="calendario2"></div>


<input type="hidden" name="id"  id="id" value="{id}"/>
<input type="hidden" name="id_dhtmlx"  id="id_dhtmlx" value="{id_dhtmlx}"/>
<select  id="agenda" class="input" multiple="multiple">{agendas_editables}</select>
<label>Titulo</label><input type="text" class="input" id="titulo"  value="{titulo}" class="required" />
<label>Tema</label><input type="text" class="input" id="tema"  value="{tema}"  />
<label>Detalle</label><textarea class="input" id="detalle" >{detalle}</textarea>

<!-- GMaps -->
<label>Lugar</label>
<div class="input"><input type="text" id="lugar"  value="{lugar}"  /><a class="bt_gmap"  href="#"></a></div>
<div id="map"></div>
<input type="hidden" id="latLng"  value="{latLng}"  />
<input name="map_loaded" id="map_loaded" type="hidden" value="0" />

<!-- Estado -->
<label>Tarea</label>
<select  id="estado">
<option value="1">No especificado</option>
<option value="2">Asistir</option>
<option value="3">Informarse</option>
<option value="4">Confirmar por email</option>
<option value="5">Confirmar por teléfono</option>
<option value="6">Necesita preparación</option>
</select>

<div id="desde">
<label>Desde</label>
<input id="start_hour" name="start_hour" type="text" value="{start_hour}"/>
<input id="start_minute" name="start_minute" type="text" value="{start_minute}"/>
<input id="start_date" name="start_date" type="text" readonly value="{start_date}" />
</div>
<div id="hasta">
<label>Hasta</label>
<input id="end_hour" name="end_hour" type="text" value="{end_hour}"/>
<input id="end_minute" name="end_minute" type="text" value="{end_minute}" />
<input id="end_date" name="end_date" type="text" readonly value="{end_date}" />
</div>

<div class="clear sep"></div>


<!-- Botonera -->
<div id="botonera">
<!-- Autor -->
<div  id="autor">
<div id="autor-fecha">{date}</div>
<div id="autor-nombre">{autor_nombre}({autorID})</div>
</div>
<div  clas="clear"></div>
<div id="botonera2">
<a href="#" id="borrar"><span></span>Borrar</a>
<a href="#" id="guardar"><span></span>Guardar</a>
<a href="#" id="cancelar"><span></span>Cancelar</a>
</div>
</div>    
   
<!-- Dialog -->
<div id="dialog" title="Borrar evento">Seguro que desea borrar este evento?</div>
<div id="dialog2" title=""></div>    
    

<!-- Google Map  -->
<script type="text/javascript" src="http://maps.google.com/maps/api/js?sensor=false"></script>


<!-- scripts concatenated and minified via build script -->
<script src="{module_url}assets/jscript/plugins.js"></script>
<!--<script src="{module_url}assets/jscript/lightbox_script.js"></script>-->
<script type="text/javascript">
    
/*
 * 
 *  Carga asincronica de JS 
 * 
 */    

yepnope([{
  // Load jquery from a 3rd party CDN
  load: '//ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.js',
  callback: function (url, result, key) {
    // The boss doesn't trust the jQuery CDN, so you have to have a fallback
    // So here you can check if your file really loaded (since callbacks will still
    // fire after an error or a timeout)
    if (!window.jQuery) {
      // Load jQuery from our local server
      // Inject it into the middle of our order of scripts to execute
      // even if other scripts are listed after this one, and are already
      // done loading.
      yepnope('{module_url}assets/jscript/libs/jquery-1.7.2.min.js');
    }
  }
}, {
  // This file will start downloading as soon as this line is executed,
  // but if the jQuery CDN was down it won't be executed until after the
  // local version was loaded.
  load: ['{module_url}assets/jscript/dhtmlxSuite/dhtmlxAccordion/codebase/dhtmlxcommon.js',
      '{module_url}assets/jscript/dhtmlxSuite/dhtmlxCalendar/codebase/dhtmlxcalendar.js',
      '{module_url}assets/jscript/dhtmlxSuite/dhtmlxCalendar/codebase/dhtmlxcalendar_es.js'
  ],
  callback: function (url, result, key) {
  },
  complete:function(){
      yepnope({load:'{module_url}assets/jscript/lightbox_script.js'});
  }
}]);
</script>

  

</body>
</html>