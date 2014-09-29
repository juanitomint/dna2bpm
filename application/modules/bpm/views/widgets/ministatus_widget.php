<div class="box box-solid box-info">
    <span class="hidden widget_url">{widget_url}</span>
    <div class="box-header" style="cursor: move;">
        <i class="ion ion-clipboard"></i>
        <h3 class="box-title">{title} ({qtty})</h3>
        <div class="box-tools pull-right">
            <button style="margin-right: 5px;" title="" data-toggle="tooltip" data-widget="refresh" class="btn btn-primary btn-sm pull-right" data-original-title="Refresh"><i class="fa fa-refresh"></i></button>
            <button style="margin-right: 5px;" title="" data-toggle="tooltip" data-widget="collapse" class="btn btn-primary btn-sm pull-right" data-original-title="Collapse"><i class="fa fa-minus"></i></button>
        </div>
    </div><!-- /.box-header -->
    <div class="box-body">
        <section class="sidebar">
            <ul class="sidebar-menu">
                {folders}
                <li class="treeview">
                    <a href="#">
                        <i class="fa fa-laptop"></i>
                        <span>{folder}</span>
                        <i class="fa fa-angle-left pull-right"></i>
                    </a>
                    <ul class="treeview-menu">
                        {models}
                        <li>
                            <!-- text -->
                            <a href='{base_url}bpm/bpmui/ministatus/{idwf}' class="reload_widget">
                                <span class="text">{idwf}<br/>{properties name}</span>
                            </a>
                        </li>
                        {/models}
                    </ul>
                </li>
                {/folders}
            </ul>
        </section>
    </div><!-- /.box-body -->
    <div class="box-footer clearfix no-border">
<!--        <button class="btn btn-default pull-right"><i class="fa fa-plus"></i> Add item</button>-->
    </div>
</div>