<?php 
/* 
 *  Header : CSS Load & some body
 * 
 */
include('_header.php')

?>
       
        <div class="wrapper row-offcanvas row-offcanvas-left hidden-print"><!-- Wrapper -->

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
            <!--   -->

            <!-- ======== CONTENT AREA ======== --> 
            <aside class="right-side">
                <!-- Content Header (Page header) -->
                <section class="content-header">
                    <h1>
                        {title}
                    </h1>
                    <ol class="breadcrumb">
                        <li><a href="#"><i class="fa {icon}"></i> Home</a></li>
                        <li class="active">{title}</li>
                    </ol>
                </section>

                <section class="content">
                		{alerts}
                
                        {widgets}                    
                </section>         


            </aside>
            <!-- ++++++++ CENTRO  -->
        </div><!-- /Wrapper -->

		
<?php 
/* 
 *  FOOTER 
 * 
 */
include('_footer.php')

?>
	