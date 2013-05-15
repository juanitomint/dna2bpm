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
<link rel="stylesheet" type="text/css" href="{module_url}assets/jscript/dhtmlxSuite/dhtmlxCalendar/codebase/skins/dhtmlxcalendar_dhx_skyblue.css"></link>

<link rel="STYLESHEET" type="text/css" href="{module_url}assets/css/detalles2.css" media="screen" />
<link rel="stylesheet" href="{module_url}assets/css/style_last.css">
<link rel="STYLESHEET" type="text/css" href="{module_url}assets/jscript/libs/UI/ui-lightness/jquery-ui-1.8.20.custom.css" media="screen" />



  <!-- More ideas for your <head> here: h5bp.com/d/head-Tips -->

  <!-- All JavaScript at the bottom, except this Modernizr build.
       Modernizr enables HTML5 elements & feature detects for optimal performance.
       Create your own custom Modernizr build: www.modernizr.com/download/ -->
  <script src="{module_url}assets/jscript/libs/modernizr-2.5.3.min.js"></script>

</head>
<body>
        
<div id="dialog" ></div>   
<div id="lightbox" class="lightbox" >
<div id="calendario1" style="position:relative;display:none"></div>
<div id="calendario2" style="position:relative;display:none"></div>


<input type="hidden" name="id"  id="id" value="{id}"/>
<input type="hidden" name="id_dhtmlx"  id="id_dhtmlx" value="{id_dhtmlx}"/>
<select  id="agenda" class="input" multiple="multiple">{agendas_editables}</select>
<label>{title}</label><input type="text" class="input" id="titulo"  value="{titulo}" class="required" />
<label>{topic}</label><input type="text" class="input" id="tema"  value="{tema}"  />
<label>{detail}</label><textarea class="input" id="detalle" >{detalle}</textarea>

<!-- GMaps -->
<label>{location}</label>
<div class="input"><input type="text" id="lugar"  value="{lugar}"  /><a class="bt_gmap"  href="#"></a></div>
<div id="map"></div>
<input type="hidden" id="latLng"  value="{latLng}"  />
<input name="map_loaded" id="map_loaded" type="hidden" value="0" />

<!-- Estado -->
<label>{task}</label>
<select  id="estado">
<option value="1" <?php if(isset($estado) && $estado==1) echo 'selected="selected"'?>>{lightbox_taskcombo_1}</option>
<option value="2" <?php if(isset($estado) &&  $estado==2) echo 'selected="selected"'?>>{lightbox_taskcombo_2}</option>
<option value="3" <?php if(isset($estado) &&  $estado==3) echo 'selected="selected"'?>>{lightbox_taskcombo_3}</option>
<option value="4" <?php if(isset($estado) &&  $estado==4) echo 'selected="selected"'?>>{lightbox_taskcombo_4}</option>
<option value="5" <?php if(isset($estado) &&  $estado==5) echo 'selected="selected"'?>>{lightbox_taskcombo_5}</option>
<option value="6" <?php if(isset($estado) &&  $estado==6) echo 'selected="selected"'?>>{lightbox_taskcombo_6}</option>
</select>

<div id="desde">
<input id="start_hour" name="start_hour" type="text" value="{start_hour}"/>
<input id="start_minute" name="start_minute" type="text" value="{start_minute}"/>
<input id="start_date" name="start_date" type="text" readonly value="{start_date}" style="position:relative" />
</div>
<div id="hasta">
<input id="end_hour" name="end_hour" type="text" value="{end_hour}"/>
<input id="end_minute" name="end_minute" type="text" value="{end_minute}" />
<input id="end_date" name="end_date" type="text" readonly value="{end_date}" style="position:relative"/>
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
<a href="#" id="borrar"><span></span>{delete}</a>
<a href="#" id="guardar"><span></span>{save}</a>
<a href="#" id="cancelar"><span></span>{cancel}</a>
</div>
</div>     

<!-- Google Map  -->
<script type="text/javascript" src="http://maps.google.com/maps/api/js?sensor=false"></script>


<!-- scripts concatenated and minified via build script -->
<script src="{module_url}assets/jscript/libs/jquery-1.7.2.min.js"></script>
<script src="{module_url}assets/jscript/libs/jquery-ui-1.9.2.custom.min.js"></script>
<script src="{module_url}assets/jscript/dhtmlxSuite/dhtmlxCalendar/codebase/dhtmlxcommon.js"></script>
<script src="{module_url}assets/jscript/dhtmlxSuite/dhtmlxCalendar/codebase/dhtmlxcalendar.js"></script>
<script src="{module_url}assets/jscript/dhtmlxSuite/dhtmlxCalendar/codebase/dhtmlxcalendar_es.js"></script>
<script src="{module_url}assets/jscript/plugins.js"></script>

<script type="text/javascript">
    /* Author: Gabriel Fojo 

*/

/*
* 
*    JQUERY ONLOAD
* 
*/ 



$(document).ready(function(){

     
// Date Picker 1
  var cal1;
  cal1 = new dhtmlxCalendarObject('calendario1');
  cal1.attachEvent("onClick",function(date){
     var d= cal1.getFormatedDate('%d/%m/%Y', date);
     $("#start_date").val(d);
     cal1.hide();
  })
 $("#start_date").click(function(){
    cal1.loadUserLanguage("es");
    cal1.setPosition(200,100);
    cal1.show();
 })

 
// Date Picker 2
  var cal2;
  cal2 = new dhtmlxCalendarObject('calendario2');
  cal2.attachEvent("onClick",function(date){
     var d= cal2.getFormatedDate('%d/%m/%Y', date);
     $("#end_date").val(d);
     cal2.hide();
  })
 $("#end_date").click(function(){
    cal2.loadUserLanguage("es");
    cal2.setPosition(200,270);
    cal2.show();
 })

 // Cancelar
 $("#cancelar").click(function(){
     parent.lightbox_close($('#id_dhtmlx').val());
 });

 // Borrar
$("#borrar").click(function(){
          $( "#dialog2" ).dialog({ buttons: { 
        "{yes}": function() { 
            $(this).dialog("close"); 
                 var id=$('#id').val();
                 $.post('{module_url}main/delete_event/',{eventID:id},function(data){

                   if(data.length>1)parent.msg(data);
                   console.log(data);
                   parent.lightbox_close($('#id_dhtmlx').val());
                });
              parent.refresh(); 

        },
        "{no}": function() { $(this).dialog("close");} 
 }});
});


     

/**
*       
*   Validaciones {confirmation_delete}
*
* */


// Validate titulo
$("#titulo").click(function(){
if($(this).hasClass('error')){
  $(this).removeClass("error");
  $(this).val("");  
}
});

//$("#estado").val("{estado}");

// xxxx Guardar
$("#guardar").click(function(){

    var s_date=$('#start_date').val();
    var s_date2=s_date.split("/");
    var sd=s_date2[0];
    var sm=s_date2[1];
    var sy=s_date2[2];
    var sh=$('#start_hour').val();
    var smi=$('#start_minute').val();             
    var start_date=sd+"_"+sm+"_"+sy+"_"+sh+"_"+smi;

    var e_date=$('#end_date').val();
    var e_date2=e_date.split("/");
    var ed=e_date2[0];
    var em=e_date2[1];
    var ey=e_date2[2];
    var eh=$('#end_hour').val();
    var emi=$('#end_minute').val();             
    var end_date=ed+"_"+em+"_"+ey+"_"+eh+"_"+emi;
 
    json = {};
    json["event_id"]=$('#id').val();
    json["id_dhtmlx"]=$('#id_dhtmlx').val();
    json["agendaID"]= $("#agenda").val() || [];
    var Qagendas=json["agendaID"].length;
    json["event_name"]=$("#titulo").val();
    json["latLng"]=$("#latLng").val();
    json["tema"]=$("#tema").val();
    json["detalle"]=$("#detalle").val();
    json["lugar"]=$("#lugar").val();
    json["estado"]=$('#estado option:selected').val();
    json["start_date"]=start_date;
    json["end_date"]=end_date;
    json["mod"]=1;
    json=JSON.stringify(json);

    // Validaciones 
    var trap=0;
    if($("#titulo").val()=="" || $("#titulo").val()=="{error_missing_info}" ){    
        // title check
        $("#titulo").val("{error_missing_info}");
        $("#titulo").addClass("error");
        trap=1;
    }

    if(!Qagendas){
        trap=1;
        parent.pop('Error','{error_missing_agenda}');
    }  

    if(!trap){   
        $.post('{module_url}main/lightbox_save_event/',{evento:json},function(data){
           if(data.show==true){  
              parent.msg(data.msg);   
           } 
           parent.lightbox_close($('#id_dhtmlx').val());
        },'json');
        parent.refresh();
    }

});

// xxxxxxxxxxx Dates control
// Start Hour validation

$("#start_hour").change(function(){
    sh=parseInt($("#start_hour").val());
    if(sh<8)sh=8;
    if(sh>22)sh=22;
    eh=sh+1;
    sh="00"+sh;
    sh=sh.match(/[0-9]{2}$/);
    $("#start_hour").val(sh);
    eh="00"+eh;
    eh=eh.match(/[0-9]{2}$/);
    $("#end_hour").val(eh);
});

// Start Minute validation and end minute adjust
$("#start_minute").bind('change',function(){
    
    sm=parseInt($("#start_minute").val());
    sm=checkMin(sm);
    $("#start_minute").val(sm);
    $("#end_minute").val(sm);
    alert(sm);
});

// End hour validation
$("#end_hour").change(function(){
    eh=parseInt($("#end_hour").val());
    eh= checkHour(eh);
    $("#end_hour").val(eh);

});

// End Minutes Validation
$("#end_minute").change(function(){
    sm=parseInt($("#end_minute").val());
    sm=checkMin(sm);
    $("#end_minute").val(sm);
});

// Map Button
$(".bt_gmap").click(function(e){
    var map_loaded=$('#map_loaded').val();
    // JS loaded on demand
    if(map_loaded==0){
    $('#map_loaded').val('1');
    Modernizr.load({
      load: '{module_url}assets/jscript/gmap.js',
      callback: function (url, result, key) {
      },
      complete:function(){
          init_map();
      }
    });

 };

$('#map').slideToggle();
    e.preventDefault();
});

     
  
}); // Jquery onload
 
/*
* 
*    FUNCIONES
* 
*/ 


function sqlDate(date){
Y=date.substr(6, 4);
M=date.substr(3, 2);
D=date.substr(0, 2);
return Y+"/"+M+"/"+D;
}

function checkHour(h){
    if(h<8)h=8;
    if(h>22)h=22;
    h="00"+h;
    h=h.match(/[0-9]{2}$/);
    return h;
}

function checkMin(m){
    if(m<0)m=0;
    if(m>59)m=59;
    m="00"+m;
    m=m.match(/[0-9]{2}$/);
    return m;
}


</script>
  <!-- Dialog -->
<div id="dialog2" title="{delete_event}" class="dialog">{confirmation_delete}</div>

</body>
</html>