<div class="box {update_class}">
    <span class="hidden widget_url">{widget_url}</span>
    <div class="box-header">
        <h3 class="box-title">{title}</h3>
        <div class="box-tools pull-right">
             <a id="gitStatusReload" class="btn btn-primary btn-sm refresh-btn" href="{widget_url}" data-toggle="tooltip" title="Reload repo status"><i class="fa fa-refresh"></i> Reload</a>
            <button data-original-title="Collapse" class="btn btn-default btn-sm" data-widget="collapse" data-toggle="tooltip" title=""><i class="fa fa-minus"></i></button>
            <button data-original-title="Remove" class="btn btn-default btn-sm" data-widget="remove" data-toggle="tooltip" title=""><i class="fa fa-times"></i></button>
        </div>
    </div>
    <div style="display: block;" class="box-body">
<ul id="status" class="todo-list ui-sortable connectedSortable">
           
            {status}
            <li>
                <!-- drag handle -->
                <span class="handle">
                    <i class="fa fa-ellipsis-v"></i>
                    <i class="fa fa-ellipsis-v"></i>
                </span>
                <span class="text-{class}">[{status}]</span>
                <span class="text-{class} filename">{filename}</span>
                <div class="tools">
                    <a href="{filename}" class="gitRevert">
                        <i class="fa fa-reply checkout"></i>
                        Discard
                    </a>
                </div>
            </li>
            {/status}
                    <li>
            <!-- drag handle -->
                <span class="text-success"><i class="fa fa-arrow-circle-up"></i> Drag files above this</span>
        </li>
        </ul>
    </div>
    <!-- /.box-body -->
    <div style="display: block;" class="box-footer">
        {footer}
    </div>
</div>