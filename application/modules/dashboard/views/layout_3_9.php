<?php 
/* 
 *  Header : CSS Load & some body
 * 
 */
include('_header.php')

?>

	<div class="wrapper row-offcanvas row-offcanvas-left ">
		<!-- Wrapper -->

		<!-- ======== MENU LEFT ======== -->
		<aside class="left-side sidebar-offcanvas">
			<!-- sidebar: style can be found in sidebar.less -->
			<section class="sidebar">
				<!-- /.search form -->
				<!-- sidebar menu: : style can be found in sidebar.less -->
				{menu}
			</section>
			<!-- /.sidebar -->
		</aside>
		<!-- ++++++++ MENU LEFT  -->

		<!-- ======== CENTRO ======== -->
		<aside class="right-side">
			<!-- Content Header (Page header) -->
			<section class="content-header hidden-print">
				<h1>{title}</h1>
				<ol class="breadcrumb">
					<li><a href="#"><i class="fa {icon}"></i> Home</a></li>
					<li class="active">{title}</li>
				</ol>
			</section>

			<section class="content">
				<div class="full-height">
				{tiles}
				</div>

				{alerts}


				<div id="tiles_after hidden-print">
					<section class="col-lg-12 connectedSortable ui-sortable">
						{tiles_after}</section>
				</div>


				<section class="col-lg-3 connectedSortable ui-sortable hidden-print" id="col1">
					{col1}</section>
				<section class="col-lg-9 connectedSortable ui-sortable " id="col2">
					{col2}</section>
			</section>
	
	</div>


	</aside>
	<!-- ++++++++ CENTRO  -->
	</div>
	<!-- /Wrapper -->
	

	
<?php 
/* 
 *  FOOTER 
 * 
 */
include('_footer.php')

?>
