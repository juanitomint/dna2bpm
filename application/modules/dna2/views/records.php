<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <meta http-equiv="Content-type" content="text/html; charset=utf-8" />
    <head>
        <title>{listrecords}</title>
        <!-- Layout -->
        <link  href="{base_url}css/layout.css" rel="stylesheet" type="text/css" />
        <!-- JQuery  -->
        <script type="text/javascript" src="{base_url}jscript/ui/js/jquery-1.4.2.min.js"></script>
        <!-- JQuery UI -->
        <script src="{base_url}jscript/ui/js/jquery-ui-1.8.6.custom.min.js" type="text/javascript" ></script>
        <link  href="{base_url}jscript/ui/css/{theme}/jquery-ui-1.8.5.custom.css" type="text/css" rel="stylesheet" />
        <!-- Button FrameWork -->
        <script src="{base_url}jscript/ui/buttonFramework/load.js" type="text/javascript" ></script>
        <link  href="{base_url}jscript/ui/buttonFramework/load.css" type="text/css" rel="stylesheet" />
        <!-- JQuery Metadata 2.0 -->
        <script src="{base_url}jscript/jquery/plugins/metadata/jquery.metadata.min.js" type="text/javascript"></script>
        <!-- JQuery Table Sorter -->
        <script src="{base_url}jscript/jquery/plugins/tablesorter/jquery.tablesorter.min.js" type="text/javascript"></script>
        <!-- script src="{base_url}jscript/jquery/plugins/tablesorter/jquery.metadata.js" type="text/javascript"></script> -->
        <link  href="{base_url}jscript/jquery/plugins/tablesorter/themes/blue/style.css" rel="stylesheet"  type="text/css" media="print, projection, screen" />
        <script src="{base_url}jscript/dna2/utils.js" type="text/javascript"></script>

        <script type="text/javascript">
            // Onload
            $(document).ready(function(){

                // Para las tablas ordenables

//               $(".tablesorter").tablesorter({debug:false,
//                    // pass the headers argument and assing a object
//                    headers: {
//                        // assign the secound column (we start counting zero)
//                        0: {sorter: false }
//                    }
//                });
 			 

                var thisurl='./';
                $('#movefirst').click(function(){
                    window.location=thisurl;
                });

                $('.page').click(function(){
                    window.location=thisurl+'&page='+$(this).attr('page');
                });


            });


        </script>
        <style type="text/css">
            /*demo page css*/
/*            #dialog_link {padding: .4em 1em .4em 20px;text-decoration: none;position: relative;}
            #dialog_link span.ui-icon {margin: 0 5px 0 0;position: absolute;left: .2em;top: 50%;margin-top: -8px;}
            ul#icons {margin: 0; padding: 0;}
            ul#icons li {margin: 2px; position: relative; padding: 4px 2px 4px 2px; cursor: pointer; float: left;  list-style: none;font-size: .7em;}
            ul#icons span.ui-icon {float: left; margin: 0 4px;}*/
        </style>

    <body>
        <!-- TOP  -->
        {header}
        <div id="centro">
            <div class="corner_top"><div class="corner_top_left"></div><div class="corner_top_right"></div></div>
            <div class="side_left">
                <div class="content" id="content">
                <div class="titulo">
                <h1>{title}</h1>
                <h2>{desc}</h2>
                <h3>{totalRecords}</h3>
                </div>
                    <!-- START RECORDS  -->
                    <div id="records">
                        {render}
                    </div>
                    <!-- END   RECORDS  -->
                    <div id="barra-inferior">
                        <div id="pages">
                            <ul id="icons" class="ui-widget ">
                                {pages}
                                <li title=".ui-icon-seek-first" class="ui-state-default ui-corner-all">
                                    {link}
                                </li>
                                {/pages}
                            </ul>
                        </div>
                        <!-- xxx END pages  xxx -->
                        <!-- xxxxxxxxxxxxxxxx ORDENAR POR xxxxxxxxxxxxxxxx -->
                        <div id="pages-order">
                            <p>{sortby}:{sort}</p>
                            <form  name="form1" id="form1" action="">
                                {sortSelect}
                                <input type="submit" value="1" name="sortOrder" id="submit-asc">
                                <input type="submit" value="-1" name="sortOrder" id="submit-desc">
                                <input type="hidden" name="idvista" value="{idobj}">
                                <input type="hidden" name="idap" value="{idapp}">

                            </form>
                        </div>
                        <!-- xxx END Ordenar por  xxx -->
                    </div><!-- xxx END Barra Inferior xxx -->

                </div>
            </div>
            <div class="corner_bot">
                <div class="corner_bot_left"/>
                <div class="corner_bot_right"/>
            </div>
        </div>
    </body>
</html>