<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>{title}</title>
        <meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>
        <!-- bootstrap 3.0.2 -->
        <link href="{module_url}assets/bootstrap-wysihtml5/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
        <!-- font Awesome -->
        <link href="{module_url}assets/bootstrap-wysihtml5/css/font-awesome.min.css" rel="stylesheet" type="text/css" />
        <!-- Ionicons -->
        <link href="{module_url}assets/bootstrap-wysihtml5/css/ionicons.min.css" rel="stylesheet" type="text/css" />
        <!-- Morris chart -->
        <link href="{module_url}assets/bootstrap-wysihtml5/css/morris/morris.css" rel="stylesheet" type="text/css" />
        <!-- jvectormap -->
        <link href="{module_url}assets/bootstrap-wysihtml5/css/jvectormap/jquery-jvectormap-1.2.2.css" rel="stylesheet" type="text/css" />
        <!-- fullCalendar -->
        <link href="{module_url}assets/bootstrap-wysihtml5/css/fullcalendar/fullcalendar.css" rel="stylesheet" type="text/css" />
        <!-- Daterange picker -->
        <link href="{module_url}assets/bootstrap-wysihtml5/css/daterangepicker/daterangepicker-bs3.css" rel="stylesheet" type="text/css" />
        <!-- bootstrap wysihtml5 - text editor -->
        <link href="{module_url}assets/bootstrap-wysihtml5/css/bootstrap-wysihtml5/bootstrap3-wysihtml5.min.css" rel="stylesheet" type="text/css" />
        <!-- Theme style -->
        <link href="{module_url}assets/bootstrap-wysihtml5/css/AdminLTE.css" rel="stylesheet" type="text/css" />
        <!--  Juery UI css -->
        <link href="{base_url}jscript/jquery/ui/jquery-ui-1.10.2.custom/css/smoothness/jquery-ui-1.10.2.custom.min.css" rel="stylesheet" type="text/css" />

        <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
        <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
        <!--[if lt IE 9]>
          <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
          <script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>
        <![endif]-->
        {css}
    </head>
    <body class="skin-blue">

        <!-- ======== HEADER ======== -->   

        <header class="header">
            <a href="index.html" class="logo">
                <!-- Add the class icon to your logo image or logo icon to add the margining -->
                DNA3
            </a>
            <!-- Header Navbar: style can be found in header.less -->
            <nav class="navbar navbar-static-top" role="navigation">
                <!-- Sidebar toggle button-->
                <a href="#" class="navbar-btn sidebar-toggle" data-toggle="offcanvas" role="button">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </a>
                <div class="navbar-right">
                    <ul class="nav navbar-nav">
                      <!-- Messages: style can be found in dropdown.less-->
                          {toolbar_inbox}
                        
                       
                        <!-- ========== USER PROFILE  ==========-->
                        <li class="dropdown user user-menu">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                                <i class="glyphicon glyphicon-user"></i>
                                <span>{name} <i class="caret"></i></span>
                            </a>
                            <ul class="dropdown-menu">
                                <!-- User image -->
                                <li class="user-header bg-light-blue">
                                    <img src="{avatar}" class="" alt="User Image" />
                                    <p>
                                        {name}
                                    </p>
                                </li>
                                <!-- Menu Footer-->
                                <li class="user-footer">
                                    <div class="pull-left">
                                        <a href="{module_url}profile" class="btn btn-default btn-flat">Profile</a>
                                    </div>
                                    <div class="pull-right">
                                        <a href="#" class="btn btn-default btn-flat">Sign out</a>
                                    </div>
                                </li>
                            </ul>
                        </li>
                        <!-- ++++ USER PROFILE -->
                    </ul>
                </div>
            </nav>
        </header>

        <!-- ++++++++ HEADER  -->      


        <div class="wrapper row-offcanvas row-offcanvas-left"><!-- Wrapper -->

            <!-- ======== MENU LEFT ======== --> 
            <aside class="left-side sidebar-offcanvas">
                <!-- sidebar: style can be found in sidebar.less -->
                <section class="sidebar">
                    <!-- /.search form -->
                    <!-- sidebar menu: : style can be found in sidebar.less -->
                    {menu}
                </section>
                <!-- /.sidebar -->
            </aside>
            <!-- ++++++++ MENU LEFT  -->

            <!-- ======== CENTRO ======== --> 
            <aside class="right-side">
                <!-- Content Header (Page header) -->
                <section class="content-header">
                    <h1>
                        {title}
                    </h1>
                    <ol class="breadcrumb">
                        <li><a href="#"><i class="fa {icon}"></i> Home</a></li>
                        <li class="active">{title}</li>
                    </ol>
                </section>

                <section class="content">
                    {tiles}
                    <section class="col-lg-12 connectedSortable ui-sortable">
                        <div class="row" id="tiles_after">
                            {tiles_after}
                        </div>
                    </section>
                    <div class="row">
                        <section class="col-lg-6 connectedSortable ui-sortable" id="col1"> 
                            {col1} 
                        </section>
                        <section class="col-lg-6 connectedSortable ui-sortable" id="col2"> 
                            {col2}   
                        </section>

                </section>         
        </div>


    </aside>
    <!-- ++++++++ CENTRO  -->
</div><!-- /Wrapper -->
<!-- ======== MODAL ======== --> 

<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                <h4 class="modal-title" id="myModalLabel">Modal title</h4>
            </div>
            <div class="modal-body">
                ...
            </div>
        </div>
    </div>
</div>
<!-- ======== FOOTER ======== --> 


<!-- JS Global -->
<script>
    //-----declare global vars
    var globals = {global_js};
</script>

{footer}

<!-- JS custom -->     
{js}


</body>
</html>

