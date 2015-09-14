<div class="col-md-12" >
	<div class="box box-info">
		<div class="box-header">
			<i class="fa fa-bullhorn"></i>
			<h3 class="box-title">{lang Alerts}</h3>
		</div>
		<!-- /.box-header -->
		<div class="box-body">
			{my_alerts}
			<div class="callout callout-{class} alert alert-dismissable widget_alert"  data-id="{_id}">
			<button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
				<h4>{subject}</h4>
				<p>{body}</p>
			</div>
			{/my_alerts}
		</div>
		<!-- /.box-body -->
	</div>
	<!-- /.box xxxxxxx-->
</div>