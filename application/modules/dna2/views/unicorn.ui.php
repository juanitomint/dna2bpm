<!DOCTYPE html>
<html lang="en">
    <head>
        <title>DNA&sup2; Admin</title>
        <meta charset="UTF-8" />

        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <link rel="stylesheet" href="{base_url}jscript/bootstrap/css/bootstrap.min.css" />
        <link rel="stylesheet" href="{base_url}jscript/bootstrap/css/bootstrap-responsive.min.css" />
<!--        <link rel="stylesheet" href="{base_url}jscript/font-awesome-4.0.3/css/font-awesome.min.css" />-->
        <link rel="stylesheet" href="{base_url}jscript/fontawesome/css/font-awesome.min.css" />
        <link rel="stylesheet" href="{module_url}assets/css/extra-icons.css" />	
        <link rel="stylesheet" href="{module_url}assets/css/fullcalendar.css" />	
        <link rel="stylesheet" href="{module_url}assets/css/unicorn.main.css" />
        <link rel="stylesheet" href="{module_url}assets/css/unicorn.grey.css" class="skin-color" />
        {css}
    </head>
    <body>
        <div id="header">
            <h1>
                <a href="./dashboard.html">DNA&sup2; Admin</a>
            </h1>		
        </div>

        <div id="user-nav" class="navbar navbar-inverse">
            <ul class="nav btn-group">
                <li class="btn btn-inverse" >
                    <a title="" href="#">
                        <i class="icon icon-user">
                        </i> <span class="text">{user nick}</span>
                    </a>
                </li>
                <li class="btn btn-inverse dropdown" id="menu-messages">
                    <a href="#" data-toggle="dropdown" data-target="#menu-messages" class="dropdown-toggle">
                        <i class="icon icon-coment">
                        </i> <span class="text">Messages</span> <span class="label label-important">{inbox_count}</span> <b class="caret">
                        </b>
                    </a>
                    <ul class="dropdown-menu">
                        <li>
                            <a class="sAdd" title="" href="{module_url}inbox/new_msg">new message</a>
                        </li>
                        <li>
                            <a class="sInbox" title="" href="{module_url}inbox/">inbox</a>
                        </li>
                        <li>
                            <a class="sOutbox" title="" href="#">outbox</a>
                        </li>
                        <li>
                            <a class="sTrash" title="" href="#">trash</a>
                        </li>
                    </ul>
                </li>
                <li class="btn btn-inverse">
                    <a title="" href="#">
                        <i class="icon icon-cog">
                        </i> <span class="text">Settings</span>
                    </a>
                </li>
                <li class="btn btn-inverse">
                    <a title="" href="{base_url}user/logout">
                        <i class="icon icon-signout">
                        </i> 
                        <span class="text">Logout</span>
                    </a>
                </li>
            </ul>
        </div>
        <!-- SIDEBAR -->
        <div id="sidebar">
            <a href="dashboard" class="visible-phone">
                <i class="icon icon-envelope    ">
                </i> Dashboard </a>
            <ul>
                <li class="{dashboard_class}">
                    <a href="{module_url}dashboard">
                        <i class="icon icon-home">
                        </i> 
                        <span>Dashboard</span>
                    </a>
                </li>
                <li class="{mytasks_class}">
                    <a href="{module_url}mytasks">
                        <i class="icon icon-bpm">
                        </i> <span>{MyTasks}</span> <span class="label label-info">{openCases}/{totalCases}</span>
                    </a>
                    <!--                    <ul>
                                            <li>
                                                <a href="form-common.html">
                                                    {Pending}
                                                    <span class="label label-warning">{brief user}</span>
                                                </a>
                                            </li>
                                            <li>
                                                <a href="form-validation.html">
                                                    {Finished}
                                                    <span class="label label-success">{brief finished}</span>
                                                </a>
                                            </li>
                                        </ul>-->
                </li>
                <li class="{inbox_class}">
                    <a id="inbox" href="{module_url}inbox">
                        <i class="icon icon-envelope">
                        </i> 
                        <span>InBox</span>
                        <span class="label label-info">{inbox_count}</span>
                    </a>
                </li>
                <!--Start Admin -->
               {if {isAdmin}}
                <li >
                    <a id="rbac" href="{base_url}user/admin" target="_blank">
                        <i class="icon icon-group">
                        </i> 
                        <span>User Manager</span>
                    </a>
                </li>
                <li >
                    <a id="appmanager" href="{base_url}application/browser" target="_blank">
                        <i class="icon-list-alt">
                        </i> 
                        <span>Application Browser</span>
                    </a>
                </li>
                <li >
                    <a id="appmanager" href="{base_url}bpm/admin" target="_blank">
                        <i class="icon-bpm">
                        </i> 
                        <span>BPM Browser</span>
                    </a>
                </li>
{/if}
                <!--Start Applications -->
                <li class="submenu {apps_class}">
                    <a href="#">
                        <i class="icon icon-tasks">
                        </i> <span>{applications}</span> <span class="label label-info">{apps SumApps}</span>
                    </a>
                    <ul>
                        {apps}
                        <li>
                            <a href="{link}" target="{target}">
                                {if {icon}<>''}
                                <i class="icon {icon}"></i>
                                {/if}
                                {name}
                            </a>                       

                        </li>
                        {/apps}
                    </ul>
                </li>
                <!--End Applications -->
            </ul>

        </div>


        <div id="content">
            {content}
        </div>


        <script src="{module_url}assets/jscript/excanvas.min.js">
        </script>

        <script src="{module_url}assets/jscript/jquery.min.js">
        </script>
        <script src="{module_url}assets/jscript/jquery.ui.custom.js">
        </script>

        <script src="{base_url}/jscript/bootstrap/js/bootstrap.min.js">
        </script>
        <script src="{module_url}assets/jscript/jquery.flot.min.js">
        </script>
        <script src="{module_url}assets/jscript/jquery.flot.resize.min.js">
        </script>
        <script src="{module_url}assets/jscript/jquery.peity.min.js">
        </script>
        <script src="{module_url}assets/jscript/fullcalendar.min.js">
        </script>
        <script src="{module_url}assets/jscript/unicorn.js">
        </script>
        <script type="text/javascript">
            //-----declare global vars
            var globals={inline_js};
        </script>
<!--        <script src="{module_url}assets/jscript/unicorn.dashboard.js">
        </script>-->
        {js}

        <!--        TODAVIA NO!
        <script src="assets/jscript/dashboard.js">
                </script>
        -->
    </body>
</html>
