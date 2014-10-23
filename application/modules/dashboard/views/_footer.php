        <!-- ======== MODAL ======== --> 

        <div  class="modal fade " id="myModal"  role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
            <div class="modal-dialog" >
                <div class="modal-content ">
                    <div class="modal-header">
                    <button type="button" class="close " data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                    <button type="button" class="hidden pull-right btn btn-default btn-sm btn-flat" style="margin-right:10px"><i class="fa fa-print"></i><span class="sr-only">Print</button></a>
                        
                        <h4 class="modal-title" id="myModalLabel">Modal title</h4>
                    </div>
                    <div class="modal-body ">
                        ...
                    </div>

                </div>
            </div>
        </div>
        
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