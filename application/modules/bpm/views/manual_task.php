<!-- Modal -->
<div id="myModal" class="modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h3 id="myModalLabel">{task_name}</h3>
            </div>
            <div class="modal-body">
                <p>{task_documentation}</p>
                {DataObject_Input}
                <div class="file-input row" resourceId="{resourceId}">
                    
                    {ui}
                </div>
                {/DataObject_Input}
            </div>
            <div class="modal-footer">
                <button id="closeTask" class="btn pull-left btn-danger" data-dismiss="modal" aria-hidden="true">
                    <i class="fa fa-chevron-left fa-white"></i>
                    {lang closeTask}
                </button>
                <button id="finishTask" class="btn btn-success">
                    <i class="fa fa-play fa-white"></i>
                    {lang finishTask}
                </button>
            </div>
        </div>
    </div>
</div>