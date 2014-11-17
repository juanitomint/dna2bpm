<div class="box box-info update5">
    <span class="hidden widget_url">{widget_url}</span>
    <div class="box-header">
        <h3 class="box-title">{name}</h3>
        <div class="box-tools pull-right">
            <button class="btn btn-default btn-sm" data-toggle="tooltip" data-widget="refresh" data-original-title="Refresh"><i class="fa fa-refresh"></i></button>
            <button class="btn btn-default btn-sm" data-widget="collapse"><i class="fa fa-minus"></i></button>
            <button class="btn btn-default btn-sm" data-widget="remove"><i class="fa fa-times"></i></button>
        </div>
    </div>
    <div class="box-body">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th></th>
                    <th>{lang name}</th>
                    <th>{lang inProgress}</th>
                    <th>{lang Finished}</th>
                    <th>{lang total}</th>
                </tr>
            </thead>
            {mini}
            <tr>
                <td>            
                    <img src="{base_url}{icon}"/>
                </td>
                <td >
                    {title}
                </td>
                <td class="center">            
                    <a href="{base_url}bpm/kpi/list_status/{idwf}/{resourceId}/user" class="reload_widget">
                        {user}
                    </a>
                </td>
                <td class="center">
                    <a href="{base_url}bpm/kpi/list_status/{idwf}/{resourceId}/finished" class="reload_widget">
                        {finished}
                    </a>
                </td>
                <td class="center">
                    {run}
                </td>
            </tr>
            {/mini}
        </table>
    </div><!-- /.box-body -->
    <div class="box-footer">
        <a href='{base_url}bpm/bpmui/widget_ministatus' class="reload_widget">
            <i class="fa fa-arrow-circle-o-left"></i>
            Go back
        </a>
    </div><!-- /.box-footer-->
</div>



