<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

    <head>
        <meta http-equiv="Content-type" content="text/html; charset=utf-8" />
        <title>{AppEditor}{active_app}</title>
        <!--        <link  href="{base_url}css/layout.css" rel="stylesheet" type="text/css" />
                <link  href="{base_url}css/inbox.css" rel="stylesheet" type="text/css" />-->
        <!-- START  Jscript Block -->
        <script type="text/javascript" src="{base_url}jscript/ui/js/jquery-1.4.2.min.js"></script>
        <script type="text/javascript" src="{base_url}jscript/ui/js/jquery-ui-1.8.4.custom.min.js"></script>
        <script type="text/javascript" src="{base_url}jscript/ui/layout/jquery.layout.min-1.2.0.js"></script>
        <link type="text/css" href="{base_url}jscript/ui/css/{theme}/jquery-ui-1.8.5.custom.css" rel="stylesheet" />
        <!-- Page Specific -->
<!--        <script type="text/javascript" src="{base_url}jscript/jquery/plugins/dform-0.1/js/jquery.dform.js"></script>-->
        <script type="text/javascript" src="{base_url}jscript/jquery/plugins/SelectBoxes/jquery.selectboxes.min.js"></script>
        <script type="text/javascript" src="{base_url}jscript/jquery/plugins/Form/jquery.form.js"></script>
        <script type="text/javascript" src="{base_url}jscript/edit_area/edit_area_full.js"></script>

        <link type="text/css" href="{base_url}css/backend.css" rel="stylesheet" />
        <script type="text/javascript">
            //--- START $(document).ready
            
            var idapp='{idapp}';
            var nolayout='{nolayout}';
            var base_url='{base_url}';
            var idapp='{active_app}';
        </script>
        <script type="text/javascript" src="{base_url}jscript/dna2/backend.js"></script>
        <!-- END    Jscript Block -->
        <!-- CSS General -->

    </head>
    <body >
        <br />
        <br/>
        <!-- TOP  -->
        <!--        <div id="top" class="ui-layout-northt">

                </div>-->
        <!-- MENU LATERAL  -->
        <div id="west" class="ui-layout-west">
            <div id="menu-top" class="ui-widget ui-widget-content">
                IDAPP:{active_app}
                <br/>
                {app}
                {title}
                {/app}
            </div>
            <div id="menu-side">

                <ul id="menu-list" >

                    <li>
                        <a class="menuitem ui-widget ui-widget-content" id="menu_def">
                            {definitions}
                        </a>
                    </li>

                    <li>
                        <a class="menuitem ui-widget ui-widget-content" id="menu_views">
                            {views}
                        </a>
                    </li>

                    <li>
                        <a class="menuitem ui-widget ui-widget-content" id="menu_options">
                            {options}
                        </a>
                    </li>

                    <li>
                        <a class="menuitem ui-widget ui-widget-content" id="menu_users">
                            {users}
                        </a>
                    </li>
                    <li>
                        <a class="menuitem ui-widget ui-widget-content" id="menu_groups">
                            {groups}
                        </a>
                    </li>

                </ul>

            </div>
            <div id="menu-bot"></div>

        </div>
        <div id="center" class="ui-layout-center">


        </div>
        <!--START EAST -->
        <div id="east" class="ui-layout-east">
            <div id="pForm">


            </div>
            <div id="eMSG" />
        </div>

        <!--END EAST -->
        <div id="dialog-message" >
            <img src="{base_url}css/ajax/loadingAnimation.gif" />
        </div>
        <!-- DEFAULT SELECTOR -->
        <div id="defaultOps" style="display:none;">
            <div class="left">
                <select name="itemsToChoose" id="default_left" size="8" multiple="multiple">

                </select>
            </div>

            <div class="left">
                <button name="left2right" value="add" type="button">>></button>
                <br/>
                <button name="right2left" value="remove" type="button"><<</button>
            </div>

            <div class="left">
                <select name="itemsToAdd" id="default_right" size="8" multiple="multiple">
                </select>
            </div>
        </div>
        <!-- SCRIPT EDITOR -->
        <div id="codeEditor" class="dot7" style="display:none; width: 800px;height: 500px;">
            <form action=""  name="ce" method="post">
                <input type="hidden" name="context" value="" />
                <input type="hidden" name="language" value="" />
                <input type="hidden" name="object" value="" />
                <textarea id="code" name="code" style="height: 95%; width: 100%;">
                </textarea>
                <textarea id="codeMsg" style="height: 40px; width: 100%;" class="ui-corner-bottom"></textarea>
            </form>
        </div>
        <!-- Json Editor -->
        <div id="jsonEditor" title="Filter Editor ver:1.3" style="display: none">
            
            <button type="button" id="json_add_row">[+] Add Condition</button>
        </div>
    </body>
</html>