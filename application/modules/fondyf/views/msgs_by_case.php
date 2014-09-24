<div class="box box-success">
    <div class="box-header" style="cursor: move;">
        <i class="ion ion-clipboard"></i>
        <h3 class="box-title">{title} ({qtty})</h3>
    </div><!-- /.box-header -->
    <div class="box-body">
	<ul class="dropdown-menu">
		<li class="header">You have {inbox_count} messages</li>   
		<li>
			
			<ul class="menu">
			{mymsgs}
			<!-- ==== Loop Toolbar MSGs -->
				<li>
					 <a href="#" data-msgid="{msgid}" class="msg">
						<h4 style="margin-left:5px">{subject}<small><i class="fa fa-clock-o"></i> {msg_time}</small></h4>
						<p style="margin-left:5px">{excerpt} ...</p>
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



