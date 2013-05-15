<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <title>{title}</title>
        <link rel="stylesheet" type="text/css" href="{base_url}jscript/ext/resources/css/ext-all-gray.css" />
        <link rel="stylesheet" type="text/css" href="{base_url}jscript/ext/src/ux/css/CheckHeader.css" />
        <link rel="stylesheet" type="text/css" href="{base_url}css/load_mask.css" />
        <link rel="stylesheet" type="text/css" href="{module_url}assets/css/groups.css" />

    </head>
    <body>
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
            var base_url='{base_url}';
            var module_url='{module_url}';
            var idapp='{idapp}';
        </script>
        <script type="text/javascript">document.getElementById('loading-msg').innerHTML += '<br/>Loading Core API...';</script>
        <script type="text/javascript" src="{base_url}jscript/ext/ext-all-debug.js"></script>
        <!-- Custom Settings -->
        <script type="text/javascript">document.getElementById('loading-msg').innerHTML += '<span class="ok">OK.</span><br/>Loading Settings...';</script>
        <script type="text/javascript" src="{module_url}assets/jscript/settings.js"></script>
        <!-- Models & Stores -->
        <script type="text/javascript">document.getElementById('loading-msg').innerHTML += '<span class="ok">OK.</span><br/>Loading Group Data Objects...';</script>
        <script type="text/javascript" src="{module_url}assets/jscript/data.js"></script>
        <!-- Data View -->
        <script type="text/javascript">document.getElementById('loading-msg').innerHTML += '<span class="ok">OK.</span><br/>Loading Data View...';</script>
        <script type="text/javascript" src="{module_url}assets/jscript/dataview.js"></script>
        <!-- Search Field -->
        <script type="text/javascript">document.getElementById('loading-msg').innerHTML += '<span class="ok">OK.</span><br/>Loading Search Field...';</script>
        <script type="text/javascript" src="{base_url}jscript/ext/src/ux/form/SearchField.js"></script>
        <!-- Data View -->
        <script type="text/javascript">document.getElementById('loading-msg').innerHTML += '<span class="ok">OK.</span><br/>Loading Data View...';</script>
        <script type="text/javascript" src="{module_url}assets/jscript/tree.js"></script>
        <!-- Users Grid -->
        <script type="text/javascript">document.getElementById('loading-msg').innerHTML += '<span class="ok">OK.</span><br/>Loading Users Grid...';</script>
        <script type="text/javascript" src="{module_url}assets/jscript/grid.js"></script>
        <!-- User Form -->
        <script type="text/javascript">document.getElementById('loading-msg').innerHTML += '<span class="ok">OK.</span><br/>Loading User Form...';</script>
        <script type="text/javascript" src="{module_url}assets/jscript/userform.js"></script>
        <!-- Drag & Drop -->
        <script type="text/javascript">document.getElementById('loading-msg').innerHTML += '<span class="ok">OK.</span><br/>Loading Drag & Drop...';</script>
        <script type="text/javascript" src="{module_url}assets/jscript/dd.js"></script>
        <!-- Application Locale  -->
        {if "{ext-locale}"}
        <script type="text/javascript">document.getElementById('loading-msg').innerHTML += '<span class="ok">OK.</span><br/>Loading locale...';</script>
        <script type="text/javascript" src="{base_url}jscript/ext/locale/{ext-locale}"></script>
        {/if}
        <!-- Application Viewport  -->
        <script type="text/javascript">document.getElementById('loading-msg').innerHTML += '<span class="ok">OK.</span><br/>Loading Viewport...';</script>
        <script type="text/javascript" src="{module_url}assets/jscript/app.js"></script>
        <script type="text/javascript">document.getElementById('loading-msg').innerHTML += '<span class="ok">OK.</span>';</script>
    </body>
</html>