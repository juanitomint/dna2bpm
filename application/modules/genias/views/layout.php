<!DOCTYPE html>
<html lang="en">
    <head>
        <title>DNA&sup2; Admin</title>
        <meta charset="UTF-8" />

        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <link rel="stylesheet" href="{base_url}jscript/bootstrap/css/bootstrap.min.css" />
        <link rel="stylesheet" href="{base_url}jscript/bootstrap/css/bootstrap-responsive.min.css" />
        
        
        <link rel="stylesheet" href="{base_url}jscript/fontawesome/css/font-awesome.min.css" />
        <link rel="stylesheet" href="{module_url}assets/jscript/jquery-ui-1.10.2.custom/css/smoothness/jquery-ui-1.10.2.custom.min.css" />	
        <link rel="stylesheet" href="{module_url}assets/css/extra-icons.css" />	
        <link rel="stylesheet" href="{module_url}assets/jscript/fullcalendar/fullcalendar.css" />
        
        <link rel="stylesheet" href="{module_url}assets/css/genias.css" />
	<!--/ Custom CSS -->
        {css}

    </head>
    <body>
<!--/ NAVIGATION -->
<div class="navbar navbar-inverse navbar-static-top">
      <div class="navbar-inner">
        <div class="container">
          <button type="button" class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
          <a class="brand" href="#">GenIA</a>
          
          <div class="nav-collapse collapse">
            <ul class="nav">
              <li><a href="{module_url}">Inicio</a></li>
              <li><a href="{module_url}tasks">Tareas</a></li>
              <li><a href="{module_url}map">Mapa</a></li>      
              <li><a href="{module_url}contacts">Contactos</a></li>
              <li><a href="{module_url}scheduler">Agenda</a></li>
              <li><a href="{module_url}programs">Programas</a></li>
            </ul>
          </div>
          <div class="nav-collapse collapse">
            <div class="pull-right profile">
                <img src="{profile_img}"  class="pull-left" title="{username}"/>
                <ul class="unstyled pull-left" >
                    <li><a href="#" title="Perfil"><i class="icon-wrench"></i></a></li>
                    <li><a href="#" title="Salir"><i class="icon-off"></i></a></li>
                </ul>
            </div>
          </div>
        </div>
      </div>
    </div>
<!-- CONTAINER -->
{content}
<!-- CONTAINER -->

        <script src="{module_url}assets/jscript/jquery.min.js"></script>
        <script src="{module_url}assets/jscript/jquery-ui-1.10.2.custom/js/jquery-ui-1.10.2.custom.min.js"></script>
        <script src="{base_url}jscript/bootstrap/js/bootstrap.min.js"></script>
        <script src="{module_url}assets/jscript/fullcalendar/fullcalendar.min.js"></script>

        
        <!-- Custom JS -->
        <script type="text/javascript">
            //-----declare global vars
            var globals={inline_js};
        </script>
        {js}


    </body>
</html>
