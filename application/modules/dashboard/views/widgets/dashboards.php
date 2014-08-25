<div class="box box-info">
    <div class="box-header" style="cursor: move;">
        <i class="ion ion-ios7-albums"></i>
        <h3 class="box-title">{title} ({qtty})</h3>
        <div class="box-tools pull-right">
        <button style="margin-right: 5px;" title="" data-toggle="tooltip" data-widget="collapse" class="btn btn-primary btn-sm pull-right" data-original-title="Collapse"><i class="fa fa-minus"></i></button>
        </div>
    </div><!-- /.box-header -->
    <div class="box-body">
        <ul class="todo-list ui-sortable">
            {dashboards}
            <li>
                <!-- drag handle -->
                <span class="handle">
                    <i class="fa fa-2x {icon}"></i>
                </span>  
                <!-- todo text -->
                <span class="text">{title}</span>
                <!-- Emphasis label -->
                
                <!-- General tools such as edit or delete-->
                <div class="tools">
                    <a href='{base_url}dashboard/show/{dash_name}'>
                        <i class="fa fa-2x fa-play"></i>
                    </a> 
                    <a href='{base_url}dashboard/show/{dash_name}/debug'>
                        <i class="fa fa-2x fa-bug"></i>
                    </a>
                </div>
                <p>
                    <small class="text">
                        {view}
                        <br/>
                        {createdBy}
                    </small>
                </p>
            </li>
            {/dashboards}
        </ul>
    </div><!-- /.box-body -->
    <div class="box-footer clearfix no-border">
<!--        <button class="btn btn-default pull-right"><i class="fa fa-plus"></i> Add item</button>-->
    </div>
</div>