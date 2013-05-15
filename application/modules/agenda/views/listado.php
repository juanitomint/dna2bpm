<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
?>
<!doctype html>
<!-- paulirish.com/2008/conditional-stylesheets-vs-css-hacks-answer-neither/ -->
<!--[if lt IE 7 ]> <html class="no-js ie6" lang="en"> <![endif]-->
<!--[if IE 7 ]>    <html class="no-js ie7" lang="en"> <![endif]-->
<!--[if IE 8 ]>    <html class="no-js ie8" lang="en"> <![endif]-->
<!--[if (gte IE 9)|!(IE)]><!--> <html class="no-js" lang="en"> <!--<![endif]-->
<head>
  <meta charset="utf-8">

  <!-- Always force latest IE rendering engine (even in intranet) & Chrome Frame
       Remove this if you use the .htaccess -->
  <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">

  <title></title>
  <meta name="description" content="">
  <meta name="author" content="">

  <!-- Mobile viewport optimized: j.mp/bplateviewport -->
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  
<!-- AJAX Components CSS    -->
<link rel="STYLESHEET" type="text/css" href="{module_url}assets/jscript/dhtmlxSuite/dhtmlxGrid/codebase/dhtmlxgrid.css" media="screen">
<link rel="STYLESHEET" type="text/css" href="{module_url}assets/jscript/dhtmlxSuite/dhtmlxCalendar/codebase/dhtmlxcalendar.css">
<link rel="stylesheet" type="text/css" href="{module_url}assets/css/listado.css">


</head>
<body>
<div id="menu">
<div id="date_box">
    {start_date}<input type="text" name="desde" id="desde" value="<?php echo date("d/m/Y");?>">
    {end_date}<input type="text" name="hasta" id="hasta"  value="<?php echo date("d/m/Y");?>">
</div>
<div id="search_box">
<input type="text" id="search_text" value="" placeholder="{title_filter}"/>
</div>
<a href="#;return false" title="Recargar"  id="refresh"></a>
<a href="#;return false" title="Imprimir"  id="print" ></a>
<a href="#;return false" title="CSV" id="csv"></a>
<a href="#;return false" title="ICal"  id="ical"></a>
</div>

<div id="grid1" style="width:100%;height:400px;"></div>



<script type="text/javascript" src="{module_url}assets/jscript/libs/jquery-1.7.2.min.js"></script>
 <!-- AJAX Components JS -->
        
<script type="text/javascript"   src="{module_url}assets/jscript/dhtmlxSuite/dhtmlxTree/codebase/dhtmlxcommon.js" charset="utf-8"></script>
<script  src="{module_url}assets/jscript/dhtmlxSuite/dhtmlxGrid/codebase/dhtmlxgrid.js" type="text/javascript"></script>
<script  src="{module_url}assets/jscript/dhtmlxSuite/dhtmlxGrid/codebase/dhtmlxgridcell.js" type="text/javascript"></script>
<script  src="{module_url}assets/jscript/dhtmlxSuite/dhtmlxGrid/codebase/ext/dhtmlxgrid_filter.js" type="text/javascript"></script>
<script  src="{module_url}assets/jscript/dhtmlxSuite/dhtmlxCalendar/codebase/dhtmlxcalendar.js" type="text/javascript"></script>
<script  src="{module_url}assets/jscript/dhtmlxSuite/dhtmlxCalendar/codebase/dhtmlxcalendar_es.js" type="text/javascript"></script>

<script type="text/javascript">
            // JQUERY ONLOAD //
            $(document).ready(function(){
                
            mygrid = new dhtmlXGridObject('grid1');
            mygrid.setImagePath("{module_url}assets/jscript/dhtmlxSuite/dhtmlxGrid/codebase/imgs/");
            mygrid.setHeader("{start_date},{end_date},{title},{detail},{location},{author}");
            mygrid.setInitWidths("*,*,*,*,*,*");
            mygrid.setSkin("light");
            mygrid.init();
            mygrid.load('{module_url}listado/get_listado_xml');


            //mygrid.setDateFormat("%m/%d/%Y");
            mygrid.setColSorting("date,date,str,str,str,str");
            //

          // Calendario 1
              cal1 = new dhtmlxCalendarObject('desde',true);            
              cal1.loadUserLanguage("es");
              cal1.setDateFormat('%d/%m/%Y');
              cal1.setPosition(110,210);
              cal1.hide();
//
//            // Calendario 2
              cal2 = new dhtmlxCalendarObject('hasta',true);
              cal2.loadUserLanguage("es");
              cal2.setDateFormat('%d/%m/%Y');
              cal2.setPosition(170,210);
              cal2.hide();

              $("#refresh").click(function(){

                var desde=$("#desde").val();
                var sd=desde.substr(6,4)+desde.substr(3,2)+desde.substr(0,2);
                var hasta=$("#hasta").val();
                var ed=hasta.substr(6,4)+hasta.substr(3,2)+hasta.substr(0,2);
                var uri=sd+"/"+ed;
                mygrid = new dhtmlXGridObject('grid1');
                mygrid.setImagePath("{module_url}assets/jscript/dhtmlxSuite/dhtmlxGrid/codebase/imgs/");
                mygrid.setHeader("Inicio,Fin,Titulo,Detalle,Lugar,Autor");
                mygrid.setInitWidths("*,*,*,*,*,*");
                mygrid.setSkin("light");
                mygrid.makeFilter("search_text",2); 
                mygrid.init();
                mygrid.load('{module_url}listado/get_listado_xml/'+uri);

              });
//
              $("#print").click(function(){
                  self.print();
                  //alert("Proximamente");
              });

              $("#csv").click(function(){
                var desde=$("#desde").val();
                var sd=desde.substr(6,4)+desde.substr(3,2)+desde.substr(0,2);
                var hasta=$("#hasta").val();
                var ed=hasta.substr(6,4)+hasta.substr(3,2)+hasta.substr(0,2);
                var uri=sd+"/"+ed;
                document.location.href='{module_url}listado/get_listado_csv/'+uri;
              });

              $("#ical").click(function(){
                var desde=$("#desde").val();
                var sd=desde.substr(6,4)+desde.substr(3,2)+desde.substr(0,2);
                var hasta=$("#hasta").val();
                var ed=hasta.substr(6,4)+hasta.substr(3,2)+hasta.substr(0,2);
                var uri=sd+"/"+ed;
                document.location.href='{module_url}listado/get_listado_ical/'+uri;
              });

            })// Fin JQuery Onload


</script>
</body>
</html>