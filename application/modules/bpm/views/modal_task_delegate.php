<!-- Modal -->
<div id="myModal" class="modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h3 id="myModalLabel">{label}</h3>
            </div>
            <div class="modal-body">
                {text}
                <div class="row">
                    
                        {users} 
                            <div class="btn btn-default">
                            <a href="{base_url}bpm/task/delegate/{idwf}/{idcase}/{resourceId}/{idu}">
                            <img src="{avatar}" class="avatar" alt="User Image"/>
                                <p>
                                    {name} {lastname}
                                </p>
                            </a>
                            </div>
                        {/users}
                </div>    
            </div>
            <div class="modal-footer">
                <button id="closeTask" class="btn pull-left btn-danger" data-dismiss="modal" aria-hidden="true">
                    <i class="fa fa-chevron-left fa-white"></i>
                    {closeTask}
                </button>
            </div>
        </div>
    </div>
</div>