<!DOCTYPE html
    PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"
      xmlns:svg="http://www.w3.org/2000/svg"
      xmlns:xlink="http://www.w3.org/1999/xlink">
    <meta http-equiv="Content-type" content="text/html; charset=utf-8" />
    <head>
        <title>{htmltitle}</title>
        <script type="text/javascript">
            var i = 0;
            var thiscase;
            var base_url = '{base_url}';
            var idwf = '{idwf}';
            var idcase = '{idcase}';
            var svgfile = '{base_url}{svgfile}';
            var intervalID;
        </script>
        <link  href="{base_url}css/layout.css" rel="stylesheet" type="text/css" />
        <!-- START  Jscript Block -->
        <script type="text/javascript" src="{base_url}jscript/jquery/jquery-1.4.2.js"></script>
        <script type="text/javascript" src="{base_url}jscript/jquery/plugins/svg/jquery.svg.js"></script>
        <script type="text/javascript" src="{base_url}jscript/jquery/plugins/svg/jquery.svgdom.js"></script>
        <script type="text/javascript" src="{base_url}jscript/jquery/plugins/svg/jquery.svganim.js"></script>
        <script type="text/javascript" src="{base_url}jscript/jquery/plugins/popup-bubble/jquery.bubblepopup.v2.3.1.min.js"></script>
        <link  href="{base_url}jscript/jquery/plugins/popup-bubble/jquery.bubblepopup.v2.3.1.css" rel="stylesheet" type="text/css" />

        <script type="text/javascript" src="{module_url}assets/jscript/model-utils.js"></script>
        <script type="text/javascript" src="{module_url}assets/jscript/view-model.js"></script>
        <style type="text/css">
            .overlay{
                /*                border:2px dashed gray;*/
                padding: 0px;
            }
            .overlay span{
                font-size: small;
                background-color: black;
                color: white;
                padding: 2px;
                font-family: monospace;
            }
            #jsoneditor {
                width: 500px;
                height: 500px;
            }
        </style>
    </head>
    <body>
        <div id="svg-box" style="border: 1px solid black">
            <?php
            echo html_entity_decode($SVG);
            ?>
        </div>
        <table class="ui-widget" id="tokens_table">
            <thead class="ui-widget-header">
                <th>icon</th>
                <th>Name</th>
                <th>Shape</th>
                <th>Rule</th>
                <th>Status</th>
                <th>nice/warn/alert</th>
                <th>ResourceId</th>
            </thead>
            {rules}
            <tr>
                <td>
                    <img src="{base_url}{icon}" alt="{type}"/>
                </td>
                <td>
                    {name}
                </td>
                <td>
                    {type}
                </td>
                <td>
                    {subtype}
                </td>
                <td>
                    {status}<span class="ui-widget ui-corner-all ui-state-default ui-icon {icon-status}" style="float: left;"/>
                </td>
                <td>&nbsp;</td>
                <td>
                    {resourceId}
                </td>

            </tr>
            {/rules}
        </table>
    </body>
</html>