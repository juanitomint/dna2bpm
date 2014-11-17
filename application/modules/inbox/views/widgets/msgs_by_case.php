<div class="box box-info">
    <div class="box-header" style="cursor: move;">
        <i class="ion ion-clipboard"></i>
        <h3 class="box-title">{title} ({qtty})</h3>

    </div><!-- /.box-header -->
    <div class="box-body">
 			<ul class="list-unstyled">
			{mymsgs}
			<!-- ==== Loop Toolbar MSGs -->
				<li>
				<small><i class="fa fa-clock-o push-left"></i> {msg_time}</small>
				<small class="pull-right label bg-blue">{case}</small>
					 <a href="#" data-msgid="{msgid}" class="msg">
						 {subject}
					</a>
				</li>
			{/mymsgs}
			<!-- ---- Loop Toolbar MSGs -->

			</ul>
    </div><!-- /.box-body -->
    <div class="box-footer clearfix no-border">
<!--        <button class="btn btn-default pull-right"><i class="fa fa-plus"></i> Add item</button>-->
    </div>
</div>




