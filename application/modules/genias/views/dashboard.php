<!-- / Breadcrumbs -->
<ul class="breadcrumb navbar-static-top">
  <li><a href="{module_url}">Dashboard</a> <span class="divider">/</span></li>
</ul>
<!-- / Contenido -->
<div class="container">  

    
 <!-- xxxxxxxxxxxxxxxx CREAR META  xxxxxxxxxxxxxxxx -->
<div class="accordion" id="goals">
<!-- Item1 -->
<div class="accordion-group">
<div class="accordion-heading">
<a class="accordion-toggle btn" data-toggle="collapse" data-parent="#proyectos" href="#collapse1">
Crear nueva meta
</a>
</div>
    

    
<div id="collapse1" class="accordion-body collapse">
<form id="form_goals">
    <div  class="row-fluid">
        <div class="span6">
            <label>Proyecto</label>
                <select name="proyecto" class="input-block-level">
                {projects}
                <option>{name}</option>
                {/projects}
                </select>
        <div class="">
            <label>Cantidad</label>
            <input type="text" name="cantidad" placeholder="Cantidad"  class="input-block-level"/>
        </div>
        </div>
        <div class="span6">
        <div class="">
            <label>Desde</label>
            <input type="text" name="desde" placeholder="Desde"  class="input-block-level datepicker"/>
        </div>
        <div class="">
            <label>Hasta</label>
            <input type="text" name="hasta" placeholder="Hasta"  class="input-block-level datepicker"/>
        </div>

        </div>
    </div>
<div  class="row-fluid">
    <div class="span6">
            <label>Observaciones</label>
            <textarea name="observaciones" placeholder="Observaciones"  class="input-block-level" ></textarea>
    </div> 
    <div class="span6">
        <a  href="#" class="btn btn-primary input-block-level">Agregar</a>
            
    </div> 
            
</div> 

</form>  
</div>
</div>
</div>
 <!-- xxxxxxxxxxxxxxxx METAS  xxxxxxxxxxxxxxxx -->
    

 {goals}
 <div  class="row-fluid" >
     <div class="span12 {class}">
    <div style="float:right"><h2>{cumplidas}/{cantidad}</h2></div>      
    <h3 style="display: inline-block;margin-right:6px">{proyecto}</h3>
    <span title="Inicio" ><i class="icon-calendar" ></i> {desde}</span>
    <span title="Fin" style="padding-left:15px"><i class="icon-calendar"></i> {hasta}</span> 
    <a class="text-right btn btn-mini detalle"  href="#" ><i class="icon-chevron-down" ></i> Observaciones</a> 
         <div class="observaciones" style="display:none">
         <div class="line"></div>
             {observaciones}
         </div>
     </div>
 </div>
 {/goals}
 
<footer>
  <p>&copy; Genias 2013</p>
</footer>

</div> 