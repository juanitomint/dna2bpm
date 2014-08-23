<div class="box box-solid box-info">
    <div class="box-header" style="cursor: move;">
        <i class="ion ion-clipboard"></i>
        <h3 class="box-title">{title} ({qtty})</h3>
        <div class="box-tools pull-right">
            <button style="margin-right: 5px;" title="" data-toggle="tooltip" data-widget="collapse" class="btn btn-primary btn-sm pull-right" data-original-title="Collapse"><i class="fa fa-minus"></i></button>
        </div>
    </div><!-- /.box-header -->
    <div class="box-body">
        <ul class="todo-list ui-sortable">
            {models}
            <li>
                <!-- drag handle -->
                <span class="handle">
                    <i class="fa fa-ellipsis-v"></i>
                    <i class="fa fa-ellipsis-v"></i>
                </span>  
                <!-- todo text -->
                <span class="text">{idwf}<br/>{properties name}</span>
                <!--                 General tools such as edit or delete -->
                <div class="tools">
                    <a href='{base_url}bpm/bpmui/ministatus/{idwf}' class="reload_widget">
                        <i class="fa fa-dashboard fa-2x"></i>
                    </a>
                </div>

            </li>
            {/models}
        </ul>
    </div><!-- /.box-body -->
    <div class="box-footer clearfix no-border">
<!--        <button class="btn btn-default pull-right"><i class="fa fa-plus"></i> Add item</button>-->
    </div>
</div>