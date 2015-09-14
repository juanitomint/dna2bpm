<div class="box box-warning">
    <span class="hidden widget_url">{widget_url}</span>
    <div class="box-header" style="cursor: move;">
        <i class="ion ion-clipboard"></i>
        <h3 class="box-title">{title} ({qtty}) </h3>
        <div class="box-tools pull-right">
            {if {showPager}}
            <ul class="pagination pagination-sm inline">
                <li><a href="#">«</a></li>
                {pages}
                <li><a href="{url}" class="reload_widget {class}">{title}</a></li>
                {/pages}
                <li><a href="#">»</a></li>
            </ul>
            {/if}
        </div>
    </div><!-- /.box-header -->
    <div class="box-body">
        <ul class="todo-list ui-sortable">
            {mytasks}
            <li>
                <!-- drag handle -->
                <span class="handle">
                    <i class="fa fa-ellipsis-v"></i>
                    <i class="fa fa-ellipsis-v"></i>
                </span>  
                <!-- checkbox -->
                <!-- todo icon -->
                <span class="text"><img src="{base_url}{icon}"/></span>
                <!-- todo text -->
                <span class="text">{title} </span>
                                <!-- Extra data -->

                <button type="button" class="btn btn-default" data-toggle="tooltip" data-placement="left" title="{extra_data empresa}">{extra_data ip}</button>

                <!-- Emphasis label -->
                <small class="label {label-class}"><i class="fa fa-clock-o"></i>&nbsp;&nbsp;{label}</small>

                <!--                 General tools such as edit or delete -->
                <div class="tools">
                    <a href='{base_url}bpm/engine/run/model/{idwf}/{case}/{resourceId}'>
                        <i class="fa fa-play play_task"></i>
                    </a>
                </div>

            </li>
            {/mytasks}
        </ul>
    </div><!-- /.box-body -->
    <div class="box-footer clearfix no-border">
<button style="margin-right: 5px;" title="" data-toggle="tooltip" data-widget="refresh" class="btn btn-sm pull-right" data-original-title="Refresh"><i class="fa fa-refresh"></i></button>
    </div>
</div>