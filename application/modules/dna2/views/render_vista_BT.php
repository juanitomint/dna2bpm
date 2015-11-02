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
        <!--<script src="{base_url}jscript/jquery/plugins/jquery-validate/jquery.validate.js" type="text/javascript"></script>-->
        <!--<script src="{base_url}jscript/jquery/plugins/jquery-validate/additional-methods.js" type="text/javascript"></script>-->
        <!--<script src="{base_url}jscript/dna2/validator.custom.js" type="text/javascript"></script>-->
        <!--<script src="{base_url}jscript/jquery/plugins/jquery-validate/localization/messages_es.js" type="text/javascript"></script>-->
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


     <script type="text/javascript">
           $(document).ready(function(){
                $('#datetimepicker1').datetimepicker();
                console.log('---- date');
            });
        </script>
    </head>
    <body>
        APP{idapp}/{idobject}/{thisLang}
        {adminbar}
        {header}

        <div class="container">
                    <div class='input-group date' id='datetimepicker1'>
                    <input type='text' class="form-control" />
                    <span class="input-group-addon">
                        <span class="glyphicon glyphicon-calendar"></span>
                    </span>
                </div>
                
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
                         <label class='titulopreg'>{cname} {title} </label>
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
        

    </body>
</html>


