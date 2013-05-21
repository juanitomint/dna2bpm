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
        <link rel="stylesheet" type="text/css" href="{module_url}assets/css/print-month.css">
         <!-- AJAX Components JS -->
        <script type="text/javascript"   src="{module_url}assets/jscript/dhtmlxSuite/dhtmlxTree/codebase/dhtmlxcommon.js" charset="utf-8"></script>
        <script  src="{module_url}assets/jscript/dhtmlxSuite/dhtmlxSlider/codebase/dhtmlxslider.js"></script>
        <script  src="{module_url}assets/jscript/dhtmlxSuite/dhtmlxSlider/codebase/ext/dhtmlxslider_start.js"></script>
        
        <!-- AJAX Components CSS    -->
        <link rel="STYLESHEET" type="text/css" href="{module_url}assets/jscript/dhtmlxSuite/dhtmlxSlider/codebase/dhtmlxslider.css">

<script type="text/javascript">
    // JQUERY ONLOAD //
    $(document).ready(function(){

        $('[name=recurrentes]').change(function(){
            if(this.checked){
            $(".listado-item-recurrente").css("display","block");
            }else{
            $(".listado-item-recurrente").css("display", "none");
            }
        });

//            $('[name=altura]').change(function(){
//                var altura=$("[name=altura]").val();
//                $("#calendario").css("height", altura);
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
            $(".calendario_mensual").css("font-size",fsize+"px");
            });

            $("#sfont").click(function(){
            var fsize=parseInt($("[name=fsize]").val())-1;
            $("[name=fsize]").val(fsize);
            $(".calendario_mensual").css("font-size",fsize+"px");
            });

var altura=$("[name=altura]").val();
    });
</script>
<style type="text/css">
    @media print{
        .screen{display:none}
    }
    
        {agenda_colors}
            .agenda{id}{color:{color}}
        {/agenda_colors}
        
</style>
     </head>
     <body>
<input type="hidden" name="fsize" value="12" />
<div class="screen dummy"></div>
<div id="options" class="screen hidden">
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

                $("#calendario").css("height", newValue);

          });
    </script>
    <div class="line" ></div>
    <label>Fuente <a href="#" id="bfont">A+</a> <a href="#" id="sfont">A-</a></label>
    
    
 </div>

<div id="holder">
    <!-- Barra de info -->
<div id='info'>
<h2 id='info-left'>Calendario Mensual {fecha}</h2>
</div>

        <!-- xxx TABLA xxx-->
<table border="0" width="100%" cellspacing="0" style="height:500px" class="calendario_mensual" id="calendario" >
        <thead>
            <tr>
                <th width=""><span>Lunes</span></th>
                <th width="14%"><span>Martes</span></th>
                <th width="14%"><span>Miercoles</span></th>
                <th width="14%"><span>Jueves</span></th>
                <th width="14%"><span>Viernes</span></th>
                <th width="14%"><span>Sabado</span></th>
                <th width="14%"><span>Domingo</span></th>

            </tr>
        </thead>
        <tbody>
        {calendario}
        </tbody>
    </table>
<!-- TABLA -->
</div>
</body>
</html>