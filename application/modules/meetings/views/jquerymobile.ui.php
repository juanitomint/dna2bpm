<!DOCTYPE html>
<html>
    <meta charset='utf-8'>
    <head>
        <title>{title}</title>
        <meta name="viewport" content="width=device-width, initial-scale=1"> 
        <link rel="stylesheet" href="{base_url}jscript/jquery/mobile/jquery.mobile-1.3.1.min.css" />
        {css}


    </head>
    <body>
        <div id="page" data-role="page" data-theme="a" role="main">

            <div data-role="header">
                <h1>{title}</h1>
                <a href="{module_url}" data-icon="home" data-iconpos="notext" data-direction="reverse" isMenu="true">Home</a>
                <a href="../nav.html" data-icon="search" data-iconpos="notext" data-rel="dialog" data-transition="fade">Search</a>
            </div><!-- /header -->


            <div class="ui-content" data-role="content" role="main" data-theme="b">	
                {content}
            </div><!-- /content -->
            <div id="loading-msg"></div>
        </div><!-- /page -->

        <script type="text/javascript">
            //-----declare global vars
            var globals={inline_js};
        </script>
        <!-- JQuery Mobile-->

        <!-- JQuery -->
        <script type="text/javascript" src="{base_url}jscript/jquery/jquery-1.9.1.min.js"></script>

        <!-- JQuery Mobile-->
        <script src="{base_url}jscript/jquery/mobile/jquery.mobile-1.3.1.min.js"></script>
        <script type="text/javascript" src="{base_url}jscript/jquery/plugins/maskedinput/jquery.maskedinput-1.3.min.js"></script>
        {js}
    </body>        
</html>
