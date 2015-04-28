<div class="box {update_class}">
<span class="hidden widget_url">{widget_url}</span>
    <div class="box-header">
        <h3 class="box-title">{title}</h3>
        <div class="box-tools pull-right">
            <a id="gitCommit" class="btn btn-sm btn-success" data-toggle="tooltip" data-target="#gitModal" title="Commit your changes"><i class="fa fa-chevron-right"></i> Commit</a>
            <button data-original-title="Collapse" class="btn btn-default btn-sm" data-widget="collapse" data-toggle="tooltip" title=""><i class="fa fa-minus"></i></button>
            <button data-original-title="Remove" class="btn btn-default btn-sm" data-widget="remove" data-toggle="tooltip" title=""><i class="fa fa-times"></i></button>
        </div>
    </div>
    <div style="display: block;" class="box-body">
        <ul id="staged" class="todo-list ui-sortable connectedSortable droptrue"> 
            <li>
                <!-- drag handle -->
                    <span class="text-success drag-below"><i class="fa fa-arrow-circle-down"></i> Drag files below this <i class="fa fa-arrow-circle-down"></i></span>
            </li>
            {staged}
            <li>
                <!-- drag handle -->
                    <span class="handle">
                        <i class="fa fa-ellipsis-v"></i>
                        <i class="fa fa-ellipsis-v"></i>
                    </span>
                     <span class="text-{class}">[{status}]</span>
                    <span class="text-{class} filename">{filename}</span>
            </li>
            {/staged}
        </ul>
    </div>
<!-- /.box-body -->
    <div style="display: block;" class="box-footer">
        {footer}
    </div>
</div>