<div class="box box-info">
    <div class="box-header">
        <h3 class="box-title">{name}</h3>
        <div class="box-tools pull-right">
            <button class="btn btn-default btn-sm" data-widget="collapse"><i class="fa fa-minus"></i></button>
            <button class="btn btn-default btn-sm" data-widget="remove"><i class="fa fa-times"></i></button>
        </div>
    </div>
    <div class="box-body">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Nombre</th>
                    <th>En curso</th>
                    <th>Terminadas</th>
                    <th>Total</th>
                </tr>
            </thead>
            {mini}
            <tr>
                <td class="center">
                    {title}
                </td>
                <td>            
                    <a href="{base_url}crefis/mini_status_resultado/{idwf}/{resourceId}/user" class="load_tiles_after">
                        {user}
                    </a>
                </td>
                <td>
                    <a href="{base_url}crefis/mini_status_resultado/{idwf}/{resourceId}/finished" class="load_tiles_after">
                        {finished}
                    </a>
                </td>
                <td>

                    {run}
                </td>
            </tr>
            {/mini}
        </table>
    </div><!-- /.box-body -->
    <div class="box-footer">
        {footer}
    </div><!-- /.box-footer-->
</div>



