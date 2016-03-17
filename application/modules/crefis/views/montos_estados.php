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
                    <th>Estado</th>
                    <th>Cantidad</th>
                    <th>Importe</th>
                </tr>
            </thead>
            {mini}
            <tr>
                <td class="center">
                    {status}
                </td>
                <td align="center">            
                    {how_many}
                </td>   
                <td>            
                    {amount}
                </td>                
            </tr>
            {/mini}
        </table>
    </div><!-- /.box-body -->
    <div class="box-footer">
        {footer}
    </div><!-- /.box-footer-->
</div>



