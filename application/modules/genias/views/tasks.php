<!-- / Breadcrumbs -->
<ul class="breadcrumb navbar-static-top">
  <li><a href="{module_url}">Dashboard</a> <span class="divider">/</span></li>
  <li><a href="#">Tareas</a> <span class="divider">/</span></li>
</ul>

<div class="container">  
<!-- xxxxxxxxxx Contenido xxxxxxxxxx-->   
<div class="accordion" id="proyectos">
<!-- Item1 -->
{projects}
<div class="accordion-group">
<div class="accordion-heading">
<a class="accordion-toggle btn" data-toggle="collapse" data-parent="#proyectos" href="#collapse{id}">
{name}
</a>
</div>
<div id="collapse{id}" class="accordion-body collapse">
    <ul class="accordion-inner unstyled task_list">
        <li><input type="checkbox" value=""><a href="#">Tarea1</a><i class="icon-calendar"></i>2 de Octubre 2013<i class="icon-time"></i>08:00</li>
        <li><input type="checkbox" value=""><a href="#">Tarea1</a><i class="icon-calendar"></i>2 de Octubre 2013<i class="icon-time"></i>08:00</li>
    </ul>
</div>
</div>
{/projects}
<!-- --------- Detalle ----------->
</div>
<!-- --------- Contenido ----------->


</div>


