<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
?>
<html>
    <head>

<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title>Agenda</title>
<script type="text/javascript" src="{module_url}assets/jscript/libs/jquery-1.7.2.min.js"></script>
<link rel="stylesheet" type="text/css" href="{module_url}assets/css/print-week.css">
 <!-- AJAX Components JS -->
        
<script type="text/javascript"   src="{module_url}assets/jscript/dhtmlxSuite/dhtmlxTree/codebase/dhtmlxcommon.js" charset="utf-8"></script>
<script  type="text/javascript" src="{module_url}assets/jscript/dhtmlxSuite/dhtmlxCalendar/codebase/dhtmlxcalendar.js"></script>
<script  type="text/javascript" src="{module_url}assets/jscript/dhtmlxSuite/dhtmlxCalendar/codebase/dhtmlxcalendar_es.js"></script>
<script  src="{module_url}assets/jscript/dhtmlxSuite/dhtmlxSlider/codebase/dhtmlxslider.js"></script>
<script  src="{module_url}assets/jscript/dhtmlxSuite/dhtmlxSlider/codebase/ext/dhtmlxslider_start.js"></script>
        
<!-- AJAX Components CSS    -->
<link rel="STYLESHEET" type="text/css" href="{module_url}assets/jscript/dhtmlxSuite/dhtmlxCalendar/codebase/dhtmlxcalendar.css">
<link rel="STYLESHEET" type="text/css" href="{module_url}assets/jscript/dhtmlxSuite/dhtmlxSlider/codebase/dhtmlxslider.css">
<script type="text/javascript">
    // JQUERY ONLOAD //
    $(document).ready(function(){

        $('[name=calendarios]').change(function(){
            if(this.checked){
            $("#week-top").css("display","block");
            }else{
            $("#week-top").css("display", "none");
            }
        });

        $('[name=recurrentes]').change(function(){
            if(this.checked){
            $(".listado-item-recurrente").css("display","block");
            }else{
            $(".listado-item-recurrente").css("display", "none");
            }
        });

          $('[name=continuo]').change(function(){
            if(this.checked){
            $("#listado1").css("display", "block");
            $("#listado2").css("display","none");
            }else{
            $("#listado1").css("display", "none");
            $("#listado2").css("display","block");
            }
            });
//            $('[name=altura]').change(function(){
//                var altura=$("[name=altura]").val();
//                $("#listado1 table").css("height", altura);
//                $("#listado2 table").css("height", altura);
//            });


$(".menu").click(function(e){
      if($("#options").hasClass('hidden')){          
          $("#options").removeClass('hidden');
          $("#options").animate({top: "0px"}, 500 );
      }else{
          $("#options").addClass('hidden');
          $("#options").animate({top: "-175px"}, 500 );
      }
});
            $("#bfont").click(function(){
            var fsize=parseInt($("[name=fsize]").val())+1;
            $("[name=fsize]").val(fsize);
            $(".semana_listado > *").css("font-size",fsize+"px");
            });

            $("#sfont").click(function(){
            var fsize=parseInt($("[name=fsize]").val())-1;
            $("[name=fsize]").val(fsize);
            $(".semana_listado > *").css("font-size",fsize+"px");
            });

$("#listado1").css("display", "none");
var altura=$("[name=altura]").val();
$("#listado1 table").css("height", altura);
$("#listado2 table").css("height", altura);
    });
</script>
<style type="text/css">
    @media print{
        .screen{display:none}
    }
</style>
    </head>
 <body>
<input type="hidden" name="fsize" value="12" />
<div class="screen dummy"></div>
 <div id="options" class="screen hidden">
     <label>Calendarios <input type="checkbox" name="calendarios" checked /></label>
     <label>Continuo <input type="checkbox" name="continuo"  /></label>
     <label>Recurrentes <input type="checkbox" name="recurrentes"  checked /></label>
     <div class="line" ></div>
     <div id="block_altura" >
        <label >Altura</label>
        <div id="sliderBox"></div>
     </div>
     <a href="#" class="menu">Opciones</a>
    
    <script>
        //var slider = new dhtmlxSlider("sliderBox", 200);

        var sld = new dhtmlxSlider("sliderBox", 200,"dhx_skyblue",false,500,1500,100);
        sld.setImagePath("{module_url}assets/jscript/dhtmlxSuite/dhtmlxSlider/codebase/imgs/");
        sld.init();
                    
        sld.attachEvent("onChange",function(newValue,sliderObj){

                $("#listado1 table").css("height", newValue);
                $("#listado2 table").css("height", newValue);

          });
    </script>
    <div class="line" ></div>
    <label>Fuente <a href="#" id="bfont">A+</a> <a href="#" id="sfont">A-</a></label>
    
    
 </div>
<div id="holder-week">
<!-- Barra de info -->

<!-- Mini calendarios -->
<div id='week-top'>
<div id='cal1'></div>
<div id='cal2'></div>
<div id='cal3'></div>
</div>
</div>


<script type="text/javascript">
    mCal = new dhtmlxCalendarObject("cal1",true);
    mCal.setDate('{mes_anterior}');
    mCal.setSensitive('<?php echo $calendario_dias;?>');
    mCal.options.weekstart=1;
    mCal.loadUserLanguage("es");
    mCal.draw();

    mCal2 = new dhtmlxCalendarObject("cal2",true);
    mCal2.setDate('<?php echo $dias[$dia_orden];?>');
    mCal2.setSensitive('<?php echo $calendario_dias;?>');
    mCal2.options.weekstart=1;
    mCal2.loadUserLanguage("es");
    mCal2.draw();

    mCal3 = new dhtmlxCalendarObject("cal3",true);
    mCal3.setDate('{mes_proximo}');
    mCal3.setSensitive('<?php echo $calendario_dias;?>');
    mCal3.options.weekstart=1;
    mCal3.loadUserLanguage("es");
    mCal3.draw();
</script>


<!-- xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx -->
<!-- xxxxxxxxxxxx  LISTADO CONTINUOxxxxxxxxxxxx -->

<div class="semana_listado" id="listado1">
<table border="0" width="100%" cellspacing="0">
        <thead>
            <tr>
                <th width="14%"><?php echo $dias2[1]?></th>
                <th width="14%"><?php echo $dias2[2]?></th>
                <th width="14%"><?php echo $dias2[3]?></th>
                <th width="14%"><?php echo $dias2[4]?></th>
                <th width="14%"><?php echo $dias2[5]?></th>
                <th width="14%"><?php echo $dias2[6]?></th>
                <th width="14%"><?php echo $dias2[7]?></th>
            </tr>
        </thead>
        <tbody>
            <tr style="vertical-align:top">
                 {calendario2}
            </tr>
        </tbody>
    </table>
</div>
<!-- xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx -->
<!-- xxxxxxxxxxxx  LISTADO NORMAL xxxxxxxxxxxx -->

<div class="semana_listado" id="listado2">
<table border="0" width="100%" cellspacing="0" >
        <thead>
            <tr>
                <th>&nbsp;</th>
                <th width="14%"><?php echo $dias2[1]?></th>
                <th width="14%"><?php echo $dias2[2]?></th>
                <th width="14%"><?php echo $dias2[3]?></th>
                <th width="14%"><?php echo $dias2[4]?></th>
                <th width="14%"><?php echo $dias2[5]?></th>
                <th width="14%"><?php echo $dias2[6]?></th>
                <th width="14%"><?php echo $dias2[7]?></th>
            </tr>
        </thead>
        <tbody>        
<tr>
    {calendario}
</tr>
        </tbody>
    </table>

</div>
</body>
</html>