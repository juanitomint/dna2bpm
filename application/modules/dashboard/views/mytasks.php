<!-- TABS -->
<section class="content">
<div class="row">
<ul class="nav nav-tabs">
  <li {if {tabs}=="all"}class="active"{/if}><a href="{module_url}/tasks">All</a></li>
  <li {if {tabs}=="open"}class="active"{/if}><a href="{module_url}tasks/open">Open</a></li>
  <li {if {tabs}=="closed"}class="active"{/if}><a href="{module_url}tasks/closed">Closed</a></li>
</ul>

</div>

</section>
<!-- TASKS -->
{selection}
<section class="content">

	<div class="row">
		<!-- ======== COL 1 ======== -->
		<section class="col-lg-6 connectedSortable">
		{even}

			<div class="box box-success">		
				<div class="box-header" data-toggle="tooltip" title=""	data-original-title="Header tooltip">
					<h5 class="box-title">
					{if "{status}"=="open"}
					<span class="label label-success">{date}</span> 
					{else}
					<span class="label label-default">{date}</span> 
					{/if}
					<small>{name} [{status}]</small></h5>
					<div class="box-tools pull-right">
						<button class="btn btn-primary btn-xs" data-widget="collapse">
							<i class="fa fa-minus"></i>
						</button>
						<button class="btn btn-primary btn-xs" data-widget="remove">
							<i class="fa fa-times"></i>
						</button>
					</div>
				</div>
				<div class="box-body" style="display: block;">

				<!--  State -->
					<a class="btn btn-success btn-xs" href="{base_url}bpm/engine/run/model/{idwf}/{id}">
						<i class="fa fa-play"></i>
						Continue	
					</a>	
{if {is_admin}}
<!--  ======== ADMIN AREA ======== -->
				<!--  State -->
					<a class="btn btn-success btn-xs" href="{base_url}bpm/engine/startcase/model/{idwf}/{id}">
						<i class="fa fa-retweet"></i>
						Re-Start
					</a>
				<!--  Tokens -->
					<a class="btn btn-warning btn-xs" href="{base_url}bpm/tokens/view/{id}" >
						<i class="fa fa-code-fork"></i>
						Tokens
					</a>
				<!--  Tokens -->
					<button class="btn btn-danger btn-xs" >
						<i class="fa fa-unlock-alt"></i>
						Close
					</button>

<!--  ________ ADMIN AREA ________ -->
{/if}
					

				</div>
				<!-- /.box-body -->
				<div class="box-footer" style="display: block;">

				</div>
				<!-- /.box-footer-->
			</div>
			
			{/even}

		</section>
		<!-- -------- col 1 -------- -->

		<!-- ======== COL 2 ======== -->
		<!-- right col (We are only adding the ID to make the widgets sortable)-->
		<section class="col-lg-6 connectedSortable">
			{odd}
			<div class="box box-success">		
				<div class="box-header" data-toggle="tooltip" title=""	data-original-title="Header tooltip">
					<h4 class="box-title">
					{if "{status}"=="open"}
					<span class="label label-success">{date}</span> 
					{else}
					<span class="label label-default">{date}</span> 
					{/if}
					<small>{name} [{status}]</small></h4>
					<div class="box-tools pull-right">
						<button class="btn btn-primary btn-xs" data-widget="collapse">
							<i class="fa fa-minus"></i>
						</button>
						<button class="btn btn-primary btn-xs" data-widget="remove">
							<i class="fa fa-times"></i>
						</button>
					</div>
				</div>
				<div class="box-body" style="display: block;">

				<!--  State -->
					<a class="btn btn-success btn-xs" href="{base_url}bpm/engine/run/model/{idwf}/{id}">
						<i class="fa fa-play"></i>
						Continue	
					</a>	
{if {is_admin}}
<!--  ======== ADMIN AREA ======== -->
				<!--  State -->
					<a class="btn btn-success btn-xs" href="{base_url}bpm/engine/startcase/model/{idwf}/{id}">
						<i class="fa fa-retweet"></i>
						Re-Start
					</a>
				<!--  Tokens -->
					<a class="btn btn-warning btn-xs" href="{base_url}bpm/tokens/view/{id}" >
						<i class="fa fa-code-fork"></i>
						Tokens
					</a>
				<!--  Tokens -->
					<button class="btn btn-danger btn-xs" >
						<i class="fa fa-unlock-alt"></i>
						Close
					</button>

<!--  ________ ADMIN AREA ________ -->
{/if}
				</div>
				<!-- /.box-body -->
				<div class="box-footer" style="display: block;">

				</div>
				<!-- /.box-footer-->
			</div>
			
			{/odd}
		</section>
		<!-- -------- col 2 -------- -->
	</div>
	<!-- /.row (main row) -->

</section>
<!-- /.tasks -->
