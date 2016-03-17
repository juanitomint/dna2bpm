<div class="box box-info">
    <div class="box-header">
        <h3 class="box-title">Resultados de: "{querystring}" <span>({count})</span></h3>
        <div class="box-tools pull-right">
            <button class="btn btn-default btn-sm" data-widget="collapse"><i class="fa fa-minus"></i></button>
            <button class="btn btn-default btn-sm" data-widget="remove"><i class="fa fa-times"></i></button>
        </div>
    </div>
    <div class="box-body">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th>Nro Proyecto</th>
                    <th>Fecha</th>
                    <th>Nombre</th>
                    <th>CUIT</th>
                    <th>Estado</th>
                    <th>Caso</th>
                </tr>
            </thead>
            <tbody>
                {empresas}
                <tr>
                    <td>
                        <a href="{link_open}" target="_blank" title="Ver Proyecto">
                            <i class="ion ion-folder fa-2x fa-adjust"></i>
                        </a>

                    </td>
                    <td>{if {link_msg}>0}<a href="{link_msg}" class="load_tiles_after"  title="Ver Notificaciones">
                            <i class="ion ion-email fa-2x fa-adjust"></i>                            
                        </a>   {/if}                 
                    </td>
                    <td>  
                    {if {url_bpm}>0}<a href="{url_bpm}" title="Procesar Tareas">
                            <i class="ion-arrow-right-b fa-2x fa-adjust"></i>                            
                        </a>{/if}
                    </td>
                    <td>  
                    {if {url_clone}>0}<a href="{url_clone}" title="Documentación Recibida: Comenzar PDE!">
                            <i class="ion ion-android-inbox fa-2x fa-adjust"></i>                            
                        </a>{/if}
                    </td>
                    <td>
                        <!--icon reevaluar -->
                    {if {url_reevaluar_pde}>0}
                    <a href="{url_reevaluar_pde}" title="Re-Evaluar PDE" class="btn btn-xs  btn-success btn-default">
                            <i class="fa fa-undo"></i> PDE                            
                        </a>
                        {else}
                        <a href="{url_reevaluar_pp}" title="Re-Evaluar PP" class="btn btn-xs  btn-success btn-default">
                            <i class="fa fa-undo"></i> PP                            
                        {/if}
                    </td>
                     <td>  
                    {if {url_cancelar_pp}>0}<a href="{url_cancelar_pp}" title="Cancelar PP" class="btn btn-xs  btn-danger btn-default">
                            <i class="fa fa-times"></i> PP                            
                        </a>{/if}
                    {if {url_cancelar_pde}>0}<a href="{url_cancelar_pde}" title="Cancelar PDE" class="btn btn-xs  btn-danger btn-default">
                            <i class="fa fa-times"></i> PDE                            
                        </a>{/if}
                        </td>
                    <td>{Nro}</td>
                    <td>{fechaent}</td>
                    <td>{nombre}</td>
                    <td>{cuit}</td>
                    <td>{estado}</td>
                    <td>{case}</td>
                </tr>
                {/empresas}

            </tbody>
        </table>
    </div>
</div>