<div class="box box-info">
    <div class="box-header">
        <h3 class="box-title">{name}</h3>
        <div class="box-tools pull-right">
            <button class="btn btn-default btn-sm" data-widget="collapse"><i class="fa fa-minus"></i></button>
            <button class="btn btn-default btn-sm" data-widget="remove"><i class="fa fa-times"></i></button>
        </div>
    </div>





    <div class="box-body">
        {mini}        


        <div class="accordion" id="accordion{toggle_id}">        
            <div class="accordion-group">
                <div class="accordion-heading">
                    <a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion{toggle_id}" href="#{toggle_id}">
                        <i class="ion-arrow-right-b fa-adjust"></i> {evaluator} ({how_many})    
                    </a>
                </div>
                <div id="{toggle_id}" class="accordion-body collapse">
                    <div class="accordion-inner">
                        <table class="table table-striped">
                            <thead>
                                <tr><td colspan="6" align="right"></td></tr>
                                <tr>
                                    <th>Nro Proyecto</th>
                                    <th>Fecha</th>
                                    <th>Nombre</th>
                                    <th>CUIT</th>
                                    <th>Estado</th>
                                   <!-- <th>Caso</th>-->
                                </tr>
                            </thead>
                            <tbody>

                                {project}            
                            </tbody>
                        </table> 
                    </div>
                </div>
            </div>
        </div>




        {/mini}
    </div>
    <!-- /.box-body -->
    <div class="box-footer">
        {footer}
    </div><!-- /.box-footer-->
</div>



