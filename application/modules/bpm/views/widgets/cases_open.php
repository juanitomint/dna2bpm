<div class="box box-info">
    <div class="box-header" style="cursor: move;">
        <i class="ion ion-android-folder"></i>
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
                <div class="tools">
                    <a href='{base_url}bpm/engine/run/model/{idwf}/{id}'>
                        <i class="fa fa-2x fa-play play_case"></i>
                    </a>
                    {if {isAdmin}}
                    <a href='{base_url}bpm/engine/startcase/model/{idwf}/{id}'>
                        <i class="fa fa-2x fa-refresh restart_case"></i>
                    </a>
                    <a href='{base_url}bpm/tokens/view/{id}'>
                        <i class="fa fa-2x fa-rotate-270 fa-sitemap  restart_case"></i>
                    </a>
                    <a href='{base_url}bpm/case_manager/archive/model/{idwf}/{id}' title="Archive" class="load_modal">
                        <i class="fa fa-2x fa-archive archive_case"></i>
                    </a>
                    --
                    <a href='{base_url}bpm/bpmui/widget_data/{idwf}/{id}' title="Data" class="load_modal">
                        <i class="ion ion-android-developer fa-2x"></i>
                    </a>
                    {/if}
                </div>
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