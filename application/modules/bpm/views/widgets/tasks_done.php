<div class="box box-success">
    <div class="box-header" style="cursor: move;">
        <i class="ion ion-clipboard"></i>
        <h3 class="box-title">{title} ({qtty})</h3>
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
            <li class="done">
                <!-- drag handle -->
                <span class="handle">
                    <i class="fa fa-ellipsis-v"></i>
                    <i class="fa fa-ellipsis-v"></i>
                </span>  
                <!-- todo text -->
                <span class="text"><img src="{base_url}{icon}"/></span>
                <span class="text">{title}</span>
                <!-- Emphasis label -->
                <small class="label {label-class}"><i class="fa fa-clock-o"></i>&nbsp;&nbsp;{label}</small>
                <!--                 General tools such as edit or delete -->
                <div class="tools">
                   
                </div>

            </li>
            {/mytasks}
        </ul>
    </div><!-- /.box-body -->
    <div class="box-footer clearfix no-border">
<!--        <button class="btn btn-default pull-right"><i class="fa fa-plus"></i> Add item</button>-->
    </div>
</div>