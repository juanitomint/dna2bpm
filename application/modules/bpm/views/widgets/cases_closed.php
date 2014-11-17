
<div class="box box-info">
    <div class="box-header" style="cursor: move;">
        <i class="ion ion-folder"></i>
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
            <li>
                <!-- drag handle -->
                <span class="handle">
                    <i class="fa fa-ellipsis-v"></i>
                    <i class="fa fa-ellipsis-v"></i>
                </span>  
                <!-- todo text -->
                <span class="text">{title}</span>
                <!-- Emphasis label -->
                <span class="pull-right label bg-blue">{id}</span>
                <!-- General tools such as edit or delete-->
<!--                                <div class="tools">
                                    <i class="fa fa-play play_case"></i>
                                </div>-->
                <p>
                    <small class="text">{body}</small>
                </p>
            </li>
            {/mytasks}
        </ul>
    </div><!-- /.box-body -->
    <div class="box-footer clearfix no-border">
<!--        <button class="btn btn-default pull-right"><i class="fa fa-plus"></i> Add item</button>-->
    </div>
</div>