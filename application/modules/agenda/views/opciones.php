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
<!-- First CSS-->
<link rel="stylesheet" href="{module_url}assets/css/style_first.css">
<link rel="STYLESHEET" type="text/css" href="{module_url}assets/css/main.css" media="screen" />  
<link rel="STYLESHEET" type="text/css" href="{module_url}assets/css/opciones.css" media="screen" /> 
<link rel="stylesheet" type="text/css" href="{module_url}assets/jscript/dhtmlxSuite/dhtmlxColorPicker/codebase/dhtmlxcolorpicker.css">
<!-- Last CSS -->
<link rel="stylesheet" href="{module_url}assets/css/style_last.css">
  <!-- Mobile viewport optimized: h5bp.com/viewport -->
  <meta name="viewport" content="width=device-width">
  


</head>
<body >
        
    <div style="overflow:auto;height:100%;width:100%;position:relative">
        <ul id="wrapper_colorPicker">
        {agendas}   
        </ul>
        
    </div>
     <div id="colorPicker" style="position:absolute;top:0px;right:15px;"></div>
 <script type="text/javascript">              
                   
 // JQUERY ONLOAD //
$(document).ready(function(){
    
  $('.swatch').click(function(e){
      //var actual=$(this).parent().attr('id');
      $('body').data('actual',$(this).parent().attr('id'));
      myCP.show();
  });
  
    var myCP = new dhtmlXColorPicker('colorPicker', false, false, true, false);
     myCP.setImagePath("{module_url}assets/jscript/dhtmlxSuite/dhtmlxColorPicker/codebase/imgs/");
     myCP.init();
     
     myCP.setOnSelectHandler(function (color) {
            var actual=$('body').data('actual');
            $('#'+actual+' .swatch').css('background-color',color);
            // Guardar
            
            var temp=actual.split('_');
            
            json = {};
            json["agenda"]=temp[1];
            json["color"]=color;
            json=JSON.stringify(json);

                $.post('{module_url}main/options_save_colors/',{data:json},function(feedback){
                    console.log(feedback);
//                   if(feedback.show==true){  
//                      parent.msg(feedback.msg);   
//                   }  
                },'json');
     })
     
});


     
     
  </script>
</body>
</html>