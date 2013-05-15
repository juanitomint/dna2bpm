<!DOCTYPE html>
<html manifest="{base_url}genias/assets/manifest/offline.appcache">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <title>{title}</title>
        <link rel="stylesheet" type="text/css" href="{base_url}jscript/bootstrap/css/bootstrap.min.css" />
        <link rel="stylesheet" type="text/css" href="{base_url}jscript/fontawesome/css/font-awesome.min.css" />
        <link rel="stylesheet" type="text/css" href="{base_url}jscript/ext/resources/css/ext-all-neptune-debug.css" />
        <!--
        no funcionan los buttons
        <link rel="stylesheet" type="text/css" href="{module_url}assets/css/fix_bootstrap_checkbox.css" />-->
        <link rel="stylesheet" type="text/css" href="{base_url}css/load_mask.css" />
        <link rel="stylesheet" type="text/css" href="{base_url}jscript/ext/src/ux/statusbar/css/statusbar.css" />
        <link rel="stylesheet" type="text/css" href="{base_url}jscript/ext/src/ux/css/CheckHeader.css" />
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
                    {title}<br/>
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
        <script type="text/javascript" src="{base_url}jscript/ext/bootstrap.js"></script>
        <script type="text/javascript" src="{base_url}jscript/ext/packages/ext-theme-neptune/build/ext-theme-neptune.js"></script>
        <script type="text/javascript">
            //----prevent ajax to attach dc_4584589 to the end of urls
            //--- this is 4 CodeIgniter smart urls
            //----and make all reads as posts
            Ext.apply(Ext.data.AjaxProxy.prototype,
            {
                noCache:false,
                actionMethods:{
                    read:'POST'
                }
            }
        );
        </script>
        <script type="text/javascript">document.getElementById('loading-msg').innerHTML += '<span class="ok">OK.</span>';</script>        
        {js}
    </body>
</html>