<!DOCTYPE html>
<html>
        <meta charset='utf-8'>
        <head>
                <title>{title}</title>
                <meta name="viewport" content="width=device-width, initial-scale=1"> 

                <!-- JQuery Mobile-->
                <link rel="stylesheet" href="{base_url}jscript/jquery/mobile/jquery.mobile-1.1.1.min.css" />

                <!-- JQuery -->
                <script type="text/javascript" src="{base_url}jscript/jquery/jquery.min.js"></script>

                <!-- JQuery Mobile-->
                <script src="{base_url}jscript/jquery/mobile/jquery.mobile-1.1.1.min.js"></script>
                <!-- JQuery iMask -->
                <script type="text/javascript" src="{base_url}jscript/jquery/plugins/iMask/jquery-imask.js"></script>
        </head>
        <body>
                <!-- page -->
                <div data-role="page" data-theme="b">

                        <!-- header -->
                        <div data-role="header" >
                                <h1>{title}</h1>
                        </div>
                        <!-- /header -->
                        <!-- content -->
                        <div data-role="content">	
                               <?php
                               $this->load->view('business_not_registered');
                               ?>
                        </div>
                        <!-- /content -->

                </div>
                <!-- /page -->

        </body>        
</html>
