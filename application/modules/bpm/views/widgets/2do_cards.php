<div class="box box-default">
    <span class="hidden widget_url">{widget_url}</span>
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
            <div class="col-md-3">
			<div class="box  box-{class}">
			  <div class="box-header">
			      <h5 class="">
			          &nbsp;{model}
		          </h5>

	          </div>
				  <div class="profile-usertitle" style=" padding: 0px 0 3px;">
				  	<i style="float: left; display: inline-block; margin: 0; padding: 6px 0 10px 16px;" class="fa fa-plus-square-o"></i>
				    <p style="float: left; display: inline-block; margin: 0; padding: 2px 0 10px 5px; color: #5b9bd1; font-weight: 600">
				        {name}
			        </p>
			        <br>
					<span class="label label-{class}">{label}</span>
				  </div>

				<div class="box-footer clearfix no-border">

						<a href='{base_url}bpm/engine/run/model/{idwf}/{case}/{resourceId}'>
                            <i class="fa fa-play play_task"></i>
                        </a>

				</div>
			</div>
	</div>
            {/mytasks}
        </ul>
    </div><!-- /.box-body -->
    <div class="box-footer clearfix no-border">
<button style="margin-right: 5px;" title="" data-toggle="tooltip" data-widget="refresh" class="btn btn-sm pull-right" data-original-title="Refresh"><i class="fa fa-refresh"></i></button>
    </div>
</div>