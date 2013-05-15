<?php
$this->load->helper('html');
$this->load->helper('url');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <meta http-equiv="Content-type" content="text/html; charset=utf-8" />
    <head>
        <title>Edit Form DNA&sup2;</title>
        <!-- START  Jscript Block -->
        <script type="text/javascript" src="{base_url}jscript/ui/js/jquery-1.4.2.min.js"></script>
        <!-- JQuery UI -->
        <script src="{base_url}jscript/ui/js/jquery-ui-1.8.6.custom.min.js" type="text/javascript" ></script>
        <link  href="{base_url}jscript/ui/css/{theme}/jquery-ui-1.8.5.custom.css" type="text/css" rel="stylesheet" />

        <!-- Layout -->
        <link  href="{base_url}css/layout.css" rel="stylesheet" type="text/css" />

        <!-- Select FrameWork -->
        <script src="{base_url}jscript/ui/selectmenu/ui.selectmenu.js" type="text/javascript" ></script>
        <link  href="{base_url}jscript/ui/selectmenu/ui.selectmenu.css" type="text/css" rel="stylesheet" />


        <!-- JQuery Validate -->
        <script src="{base_url}jscript/jquery/plugins/jquery-validate/jquery.validate.js" type="text/javascript"></script>
        <script src="{base_url}jscript/jquery/plugins/jquery-validate/additional-methods.js" type="text/javascript"></script>
        <script src="{base_url}jscript/dna2/validator.custom.js" type="text/javascript"></script>
        <script src="{base_url}jscript/jquery/plugins/jquery-validate/localization/messages_es.js" type="text/javascript"></script>
        <!-- JQuery Metadata 2.0 -->
        <script src="{base_url}jscript/jquery/plugins/metadata/jquery.metadata.min.js" type="text/javascript"></script>
        <!-- JQuery Table Sorter -->
        <script src="{base_url}jscript/jquery/plugins/tablesorter/jquery.tablesorter.min.js" type="text/javascript"></script>
        <!-- script src="{base_url}jscript/jquery/plugins/tablesorter/jquery.metadata.js" type="text/javascript"></script> -->
        <link  href="{base_url}jscript/jquery/plugins/tablesorter/themes/blue/style.css" rel="stylesheet"  type="text/css" media="print, projection, screen" />
        <!-- Jquery ColorBox -->
        <link  href="{base_url}jscript/jquery/plugins/colorbox/colorbox.css"  type="text/css" media="screen" rel="stylesheet" />
        <script type="text/javascript" src="{base_url}jscript/jquery/plugins/colorbox/jquery.colorbox-min.js"></script>

        <!-- funciones nativas DNA2 -->
        <script src="{base_url}jscript/dna2/subform.js" type="text/javascript"></script>
        <script src="{base_url}jscript/dna2/utils.js" type="text/javascript"></script>
        <script src="{base_url}jscript/dna2/prototype.js" type="text/javascript"></script>
        <script src="{base_url}jscript/dna2/buttons.js" type="text/javascript"></script>

        <script language="JavaScript" type="text/JavaScript">
            var imin='{idobject}';     //-----Context 4 other scripts
            var id='{id}';          //-----Context 4 other scripts
            var idap='{idapp}';    //-----Context 4 other scripts

            $.metadata.setType("attr", "validate");
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

            //---------------------- START:  D O C U M E N T       R E A D Y    ----------------------------------
            $(document).ready(function(){
                //hover states on the static widgets
                $('.ui-icon').hover(
                function() { $(this).addClass('ui-state-hover'); },
                function() { $(this).removeClass('ui-state-hover'); }
            );
                // Para las tablas ordenables
//                $(".tablesorter").tablesorter({debug:false,
//                    // pass the headers argument and assing a object
//                    headers: {
//                        // assign the secound column (we start counting zero)
//                        0: {sorter: false }
//                    }
//                });
            

                //----Add colorbox to propper classes
//                $('.subformAddNew').colorbox({'width':"80%", 'height':"80%", 'iframe':true,'transition':"none"});
//
//                $('.subformPreview').colorbox({'width':"80%", 'height':"80%", 'iframe':true,'transition':"none"});

                //----Initialize Selects
                //$('select').selectmenu();
//                $('select.combodb').selectmenu({
//                    style:'dropdown',format: addressFormatting
//                });

                //----initialize buttons
//                initButtons();
            });

            //---------------------- END:  D O C U M E N T       R E A D Y    ----------------------------------
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

    </head>
    <body>
        APP{idapp}/{idobject}/{thisLang}
        {adminbar}
        {header}
        <div class="titulo">
            <h1>{form_title}</h1>
            <h2>{desc}</h2>
        </div>
        <form id="form1" name="form1" action="{base_url}dna2/process/go/{idobject}/{id}" method="post">
            <ul id="frames">
                {frames}
                <li class="frame">
                    <div class="renderEditMain table" id="BLOCK_{cname}">
                        <div class="renderEditRow row">
                            <div class="renderEditCol column pregunta">
                                {if {showframe}}
                                <!-- START Show Frame info 4 debug -->
                                <div class="ui-widget">
                                    <div style="padding: 0pt 0.7em; margin-top: 20px;" class="ui-state-highlight ui-corner-all">

                                        cname:{cname}<br/>type:{type}<br/>container:{container}<br/>value:{value}
                                    </div>
                                </div>
                                <!-- END   Show Frame info 4 debug -->
                                {/if}
                                <span class='titulopreg'>

                                    {if {show_hist}}
                                    <button class="btn_hist dot7" type="button" value="{cname}">hist</button>
                                    {/if}
                                    {cname}
                                    {title}
                                </span>
                                <div id="div_{idframe}">
                                    {render}
                                </div>
                            </div>
                        </div>
                    </div>
                </li>
                {/frames}
            </ul>
            <button type="submit" class="btn_save" value="{save}" >{save}</button>
            {form_extra}
            <input type="hidden" name="{name}" value="{value}"/>
            {/form_extra}
        </form>
        {footer}
    </body>
</html>


