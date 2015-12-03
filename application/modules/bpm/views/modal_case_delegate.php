<!-- Modal -->
<div class="modal-dialog modal-lg" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            <div class="modal-header">
                <h3 id="myModalLabel">{label}</h3>
                Delegate all your tasks to other user
            </div>
            <div class="modal-body">
            <form name="form1" id="form1">
            <input type="hidden" name="idcase" value="{idcase}">    
            <input type="hidden" name="idwf" value="{idwf}">    
                {text}
                <div class="row">
                <div class="col-lg-6">
                    <h3>FROM</h3>
                    <!--  To -->
                    <div class="form-group">
                        <!--<input type="hidden" name="from" id="from" class="select2 form-control" multiple="multiple"/>-->
                        <select id="from" name="from" class="select2 form-control">
                            
                        </select>
                    </div>
                </div>
                <div class="col-lg-6">
                  <h3>TO</h3>
                        <select name="to" id="to" class="select2 form-control">
                        </select>

                        {users}
                            <div class="btn btn-default">
                            <a href="{base_url}bpm/case_manager/delegate/{idwf}/{idcase}/{idu}">
                            <img src="{avatar}" class="avatar" alt="User Image"/>
                                <p>
                                    {name} {lastname}
                                </p>
                            </a>
                            </div>
                        {/users}
                </div>
                </div>
            
            </form>
            </div>
            <div class="modal-footer">
                <button id="closeTask" class="btn pull-left btn-danger" data-dismiss="modal" aria-hidden="true">
                    <i class="fa fa-chevron-left fa-white"></i>
                    {closeTask}
                <div class="row pull-right">
                    <button id="delegate-btn" class="btn btn-success">
                        DELEGATE
                         <i class="fa fa-chevron-right fa-white"></i>
                    </button>
                </div>
                </button>
            </div>
        </div>
    </div>
</div>