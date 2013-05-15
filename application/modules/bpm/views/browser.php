<?php
$this->load->helper('html');
$this->load->helper('url');
$this->load->helper('file');

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <meta http-equiv="Content-type" content="text/html; charset=utf-8" />
    <head>
        <title>{htmltitle}</title>
        <link  href="{base_url}css/layout.css" rel="stylesheet" type="text/css" />
        <!-- START  Jscript Block -->
        <script type="text/javascript">
            var idapp='{idapp}';
            var base_url='{base_url}';
            var idapp='{active_app}';
            var dateFmt='{dateFmt_JS}';
        </script>
        <script type="text/javascript" src="{base_url}jscript/ui/js/jquery-1.4.2.min.js"></script>
        <script type="text/javascript" src="{base_url}jscript/ui/js/jquery-ui-1.8.6.custom.min.js"></script>
        <script type="text/javascript" src="{base_url}jscript/ui/js/jquery-ui-1.8.6.custom.min.js"></script>
        <link type="text/css" href="{base_url}jscript/ui/css/{theme}/jquery-ui-1.8.5.custom.css" rel="stylesheet" />
        <script type="text/javascript" src="{base_url}jscript/bpm/browser.js"></script>
        <script type="text/javascript" src="{base_url}jscript/bpm/buttons.js"></script>
        <!-- END    Jscript Block -->
        <!-- CSS General -->
        <style type="text/css">
            /*demo page css*/
            /*
              ul #icons {margin: 0; padding: 0;}
              .nav li {list-style: none;}
            */
            .wf{
                font-size: 12px;

                border-bottom: 1px solid  #000;
                margin-bottom: 1em;
                padding-top: .5em;
                padding-bottom: .5em;
                url({base_url}css/tab_icons/formularios.png);
                no-repeat;
                scroll 0% 50%;
                padding-left: 2em;
            }

            .wf:hover{
                background-color: #CCEAFE;
            }
            .wf_image_container{
                background: white;
                border: 2px solid #9da2a3;
                margin: 1em;
                width: 215px;
                height: 215px;


            }
            .labelwf{
                font-size:  large;
            }
        </style>
    </head>
    <body>
        <p>WORKFLOW</p>
        <!-- TOP  -->
        <div id="top">
            <div id="top-menu">
                <label for="idwf_file">idwf:
                    <select name="idwf_file" id="idwf_file" title="please select a file">
                        <option value=''>{SelectOne}</option>
                        <?php
                        foreach ($files as $file)
                            echo "<option value='$file'>$file</option>\n";
                        ?>
                    </select>
                </label>
                <a href="#" id="import">{Import}</a>
                <span id="msg"></span>
                <label for="idwf">idwf:
                    <input name="idwf" id="idwf" type="text" title="please write a short name to be used as: idwf"/>
                </label>
                <a href="#" id="newDiagram">{New_Diagram}</a>
                <a href="{base_url}user/logout" title="{user_logout}">
                    {user_logout}
                </a>
            </div>
        </div>
        <div id="center">
        </div>
        
    </body>
</html>