<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <title>{title}</title>
        <link rel="stylesheet" type="text/css" href="{base_url}jscript/ext/resources/css/ext-neptune.css" />
        <link rel="stylesheet" type="text/css" href="{base_url}css/load_mask.css" />
        {css}
        <script type="text/javascript">
        </script>
    </head>
    <body>
        <div id="content"></div>
        <div id="loading-mask" style=""></div>
        <div id="loading">
            <div class="loading-indicator">
                <img src="{module_url}assets/images/loader18.gif" style="margin-right:8px;float:left;vertical-align:top;"/>
                <div style="float: left;">
                    Users Admin<br/>
                    <span id="loading-msg">
                        Loading Engine Items...
                    </span>
                </div>
            </div>
        </div>
        
        <!-- Boot -->
        <script type="text/javascript">
        //-----declare global vars
        var globals={inline_js};
        </script>
        <script type="text/javascript">document.getElementById('loading-msg').innerHTML += '<br/>Loading Core API...';</script>
        <script type="text/javascript" src="{base_url}jscript/ext/ext-all-debug.js"></script>
        <script type="text/javascript">document.getElementById('loading-msg').innerHTML += '<span class="ok">OK.</span>';</script>
        {js}
    </body>
</html>