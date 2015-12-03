<?php
$this->load->helper('html');
$this->load->helper('url');
?>
<!DOCTYPE html>
<html>
    <meta http-equiv="Content-type" content="text/html; charset=utf-8" />
    <head>
        <title>View Form DNA&sup2;</title>
        
      <!-- START  Jscript Block -->
        <script type="text/javascript" src="{base_url}jscript/jquery/jquery-1.9.1.min.js"></script>
        
        <!-- Bootstrap -->
        <link  href="{base_url}dashboard/assets/bootstrap-wysihtml5/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link  href="{base_url}dashboard/assets/bootstrap-wysihtml5/css/font-awesome.min.css" rel="stylesheet" type="text/css" />
        <script src="{base_url}dashboard/assets/bootstrap-wysihtml5/js/bootstrap.min.js" type="text/javascript"></script>
        <script src="{base_url}jscript/bootstrap-datetimepicker/js/moment.min.js" type="text/javascript"></script>
        <script src="{base_url}jscript/bootstrap-datetimepicker/js/es.js" type="text/javascript"></script> 
        <script src="{base_url}jscript/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js" type="text/javascript"></script>     
        <link  href="{base_url}jscript/bootstrap-datetimepicker/css/bootstrap-datetimepicker.min.css" type="text/css" rel="stylesheet" />
        
        
<!-- JQuery UI -->
        <!--<script src="{base_url}jscript/ui/js/jquery-ui-1.8.6.custom.min.js" type="text/javascript" ></script>-->
        <!--<link  href="{base_url}jscript/ui/css/{theme}/jquery-ui-1.8.5.custom.css" type="text/css" rel="stylesheet" />-->
<!-- Button FrameWork -->
        <!--<script src="{base_url}jscript/ui/buttonFramework/load.js" type="text/javascript" ></script>-->
        <!--<link  href="{base_url}jscript/ui/buttonFramework/load.css" type="text/css" rel="stylesheet" />-->

<!-- Select FrameWork -->
        <!--<script src="{base_url}jscript/ui/selectmenu/ui.selectmenu.js" type="text/javascript" ></script>-->
        <!--<link  href="{base_url}jscript/ui/selectmenu/ui.selectmenu.css" type="text/css" rel="stylesheet" />-->


<!-- JQuery Validate -->
        <script src="{base_url}jscript/jquery/plugins/jquery-validate/jquery.validate.js" type="text/javascript"></script>
        <script src="{base_url}jscript/jquery/plugins/jquery-validate/additional-methods.js" type="text/javascript"></script>
        <script src="{base_url}jscript/dna2/validator.custom.js" type="text/javascript"></script>
        <script src="{base_url}jscript/jquery/plugins/jquery-validate/localization/messages_es.js" type="text/javascript"></script>
<!-- JQuery Metadata 2.0 -->
        <!--<script src="{base_url}jscript/jquery/plugins/metadata/jquery.metadata.min.js" type="text/javascript"></script>-->
<!-- JQuery Table Sorter -->
        <!--<script src="{base_url}jscript/jquery/plugins/tablesorter/jquery.tablesorter.min.js" type="text/javascript"></script>-->
        <!--<link  href="{base_url}jscript/jquery/plugins/tablesorter/themes/blue/style.css" rel="stylesheet"  type="text/css" media="print, projection, screen" />-->
<!-- Jquery ColorBox -->
        <!--<link  href="{base_url}jscript/jquery/plugins/colorbox/colorbox.css"  type="text/css" media="screen" rel="stylesheet" />-->
        <!--<script type="text/javascript" src="{base_url}jscript/jquery/plugins/colorbox/jquery.colorbox-min.js"></script>-->

<!-- funciones nativas DNA2 -->
        <!--<script src="{base_url}jscript/dna2/subform.js" type="text/javascript"></script>-->
        <!--<script src="{base_url}jscript/dna2/utils.js" type="text/javascript"></script>-->
        <!--<script src="{base_url}jscript/dna2/prototype.js" type="text/javascript"></script>-->



    </head>
    <body>
        APP{idapp}/{idobject}/{thisLang}
        {adminbar}
        {header}

        <div class="container">

                
                <!-- Title -->
                <div class="row title">
                    <div class="col-md-12">
                        <h1>{title}</h1>
                        <h2>{desc}</h2>
                    </div>
                </div>
                <!-- Frames -->
                <form id="frames">
                    {frames}

                    {if {showframe}}
                     <!-- START Show Frame info 4 debug -->
                     <div class="alert alert-warning" role="alert">
                         <ul class="list list-unstyled">
                            <li><strong>cname:</strong> {cname}</li>
                            <li><strong>type:</strong> {type}</li>
                            <li><strong>container:</strong></li> {container}</li>
                            <li>value:{value}</li>
                         </ul>
                     </div>
                     <!-- END   Show Frame info 4 debug -->
                    {/if}
            
                      
                      <!-- field -->
                      <div class="form-group" id="BLOCK_{cname}">
                         <h4 class='titulopreg'>{cname} <small>{title}</small></h4>
                         
                          {if {has_id}}<a href="#" class="btn btn-info btn-xs showHist" data-idframe="{idframe}" title="{showHistory}"><i class="fa fa-clock-o"></i></a>{/if}
                         <div id="div_{idframe}">
                         {render}
                         </div>
                      </div>
                   
                      
                    {/frames}
                </form>
                
                <!-- === footer === -->
                {footer}
                
                
        </div>
        
<!-- === JS onReady === -->        
     <script type="text/javascript">
           $(document).ready(function(){
               
                //====== Context 4 other scripts   
                var imin='{idobject}';     
                var id='{id}';         
                var idap='{idapp}';   
            
                //====== Validator   
                $.validator.messages.required = "";
                $.validator.setDefaults({
                    ignore: ".ignore",
                    submitHandler: function() {
    
                        $("[disabled]").addClass('tmpDisabled');
                        $("#form1 *").removeAttr("disabled");
                        if($("#form1").valid()){
                            dosubmit("submitted!");
                        } else {
                            $(".tmpDisabled").attr('disabled',true);
                            $(".tmpDisabled").removeClass('tmpDisabled');
                        }
                    },
                    invalidHandler: function(e, validator) {
                        var errors = validator.numberOfInvalids();
                        if (errors) {
                            var message = errors == 1
                                ? 'Falta completar 1 campo. Ha sido resaltado más abajo'
                            : 'Faltan completar ' + errors + ' campos.  Han sido resaltados más abajo';
    
                            $("div.error span").html(message);
                            $("div.error").show();
                        } else {
                            $("div.error").hide();
                        }
                    }
                });


            
               //====== Datepicker
                var ops={locale:'es',
                format:'DD-MM-YYYY'};
                $('.datepicker').datetimepicker(ops);
                console.log('---- date');

               //====== Datetimepicker
                var ops2={locale:'es',
                format:'DD-MM-YYYY HH:mm'};
                $('.datetimepicker').datetimepicker(ops2);
                console.log('---- datetime');
                
                
            });
            
            //====== Functions
            
                var addressFormatting = function(text){
                var newText = text;
                //array of find replaces
                var findreps = [
                    {find:/^([^\-]+) \- /g, rep: '<span class="ui-selectmenu-item-header">$1</span>'},
                    {find:/([^\|><]+) \| /g, rep: '<span class="ui-selectmenu-item-content">$1</span>'},
                    {find:/([^\|><\(\)]+) (\()/g, rep: '<span class="ui-selectmenu-item-content">$1</span>$2'},
                    {find:/([^\|><\(\)]+)$/g, rep: '<span class="ui-selectmenu-item-content">$1</span>'},
                    {find:/(\([^\|><]+\))$/g, rep: '<span class="ui-selectmenu-item-footer">$1</span>'}
                ];

                for(var i in findreps){
                    newText = newText.replace(findreps[i].find, findreps[i].rep);
                }
                return newText;
            }
            
            function dosubmit(obj){

                disableSubmit();
                layer=document.getElementById("Layer1");
                layer.style.top=document.body.scrollTop+50;
                $('#Layer1').show();
                document.form1.Submit2.value="Guardar";
                document.form1.submit();
            }

            function dosubmit_child(obj){
                layer=document.getElementById("Layer1");
                layer.style.top=document.body.scrollTop+50;
                MM_showHideLayers('Layer1','','show');
                document.form1.Submit2.value="Guardar";
                obj.disabled=true;
                document.form1.submit();
            }
            function enableSubmit(){
                document.getElementById('mySubmit').disabled = false;
            }

            function disableSubmit(){
                document.getElementById('mySubmit').disabled = true;
            }
            
        </script>
    </body>
</html>


