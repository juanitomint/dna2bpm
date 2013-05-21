<!DOCTYPE html>
<html>
    <meta charset='utf-8'>
    <head>
        <title>{title}</title>
        <meta name="viewport" content="width=device-width, initial-scale=1"> 

        <link rel="stylesheet" href="{module_url}assets/jscript/jquery/mobile/jquery.mobile-1.1.1.css" />
        {css}
        <script type="text/javascript">
            //-----declare global vars
            var globals={inline_js};
        </script>
        <!-- JQuery Mobile-->

        <!-- JQuery -->
        <script type="text/javascript" src="{module_url}assets/jscript/jquery/jquery.min.js"></script>

        <!-- JQuery Mobile-->
        <script src="{module_url}assets/jscript/jquery/mobile/jquery.mobile-1.1.1.min.js"></script>
        {js}

    </head>
    <body>
        <div data-role="dialog">
            <div data-role="header">
                <h1>{title}</h1>                                
            </div><!-- /header -->


            <div class="ui-content" data-role="content" role="main" data-theme="c">	
                {content}
            </div><!-- /content -->
            <div id="loading-msg"></div>
        </div><!-- /page -->


    </body>        
</html>
