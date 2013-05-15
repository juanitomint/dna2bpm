<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

    <head>
        <title>{PageTitle}</title>
        <meta http-equiv="Content-type" content="text/html; charset=utf-8" />
        <link  href="{base_url}css/reset.css" rel="stylesheet" type="text/css" />
        <link  href="{base_url}css/login.css" rel="stylesheet" type="text/css" />
        <!-- START  Jscript Block -->
        <script type="text/javascript" src="{base_url}jscript/ui/js/jquery-1.4.2.min.js"></script>
        <script type="text/javascript" src="{base_url}jscript/ui/js/jquery-ui-1.8.6.custom.min.js"></script>
        <link type="text/css" href="{base_url}jscript/ui/css/{theme}/jquery-ui-1.8.5.custom.css" rel="stylesheet" />
        {ignore_pre}
        <!-- JQUERY ON-LOAD -->
        <script type="text/javascript">
            
            $(document).ready(function(){

                // Password
                $('#password').bind('focusin', function() {
                    $("#label-password").hide();
                });

                $('#label-password').bind('click', function() {
                    $("#label-password").hide();
                    $("#password").focus();

                });

                $('#password').bind('focusout', function() {
                    if(!$(this).val().length)$("#label-password").show();
                });

                // Username
                $('#label-username').bind('click', function() {
                    $("#label-username").hide();
                    $("#username").focus();

                });

                $('#username').bind('focusin', function() {
                    $("#label-username").hide();
                });

                $('#username').bind('focusout', function() {
                    if(!$(this).val().length)$("#label-username").show();
                });

                // Hide tips if there is some text in the text fields
                if($('#password').val().length)$("#label-password").hide();
                if($('#username').val().length)$("#label-username").hide();

                // Jquery Buttons
                $("button").button();

            });

        </script>
        {/ignore_pre}
    </head>
    <body>

        <div id="login" class="ui-helper-reset ui-widget-content ui-corner-all">
            <div id="login-top"></div>
            <div id="login-center">
                <form name="formAuth" id="formAuth" action="{authUrl}" method="post">
                    <div id="box-username">
                        <div id="label-username" no-edit>{username}</div>
                        <input name="username" id="username" type="text" size="32" maxlength="64" value="" />
                    </div>
                    <div id="box-password">
                        <div id="label-password">{password}</div>
                        <input name="password" id="password" type="password" size="32" maxlength="64" value="" />
                    </div>
                    {if {show_warn}}                
                    <div class="ui-widget">
                        <div id="msgbox"  class=" ui-corner-all">
                            <p class="ui-state-error">
                                <span style="float: left; margin-right: 0em; margin-left:.7em " class="ui-icon ui-icon-alert"></span>
                                {msgcode}
                            </p>
                        </div>
                    </div>
                    {/if}
                    <div id="login-center-submitArea">
                        <button type="submit" id="submit" name="submit" class="ui-button">
                            <span style="float: left; margin-right: 0.3em;" class="ui-icon ui-icon-locked"></span>
                            {loginButton}
                        </button>
                        <span id="forgot-password" >
                            <a href="#" >
                                {forgotPassword}
                            </a>
                        </span>
                    </div>
                </form>

            </div>
        </div>
    </body>
</html>