        <!-- ======== MODAL ======== --> 

        <div  class="modal fade hidden-print" id="myModal"  role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
            <div class="modal-dialog" >
                <div class="modal-content ">
                    <div class="modal-header">
                    <button type="button" class="close " data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                    <button type="button" class="pull-right btn btn-default btn-sm btn-flat bt-print" style="margin-right:10px"><i class="fa fa-print"></i><span class=""> {lang print}</button></a>
                        
                        <h4 class="modal-title" id="myModalLabel">Modal title</h4>
                    </div>
                    <div class="modal-body ">
                        ...
                    </div>

                </div>
            </div>
        </div>
         <!-- ======== FOR PRINTING MSGS ======== -->         
        <div class="visible-print-block" id="printboard"></div>
        <!-- ________ FOR PRINTING MSGS ________ -->
        
        
        <!-- JS Global -->
        <script>
            //-----declare global vars
            var globals = {global_js};
        </script>

        {footer}

        <!-- JS custom -->     
        {js} 
        
        <!-- JS inline -->     
        <script>
        {ignore}
        $(document).ready(function(){
        	{inlineJS}
        });
        {/ignore}
        </script>

    </body>
</html>