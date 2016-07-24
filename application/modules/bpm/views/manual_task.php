<!-- Modal -->
<form role="form" action="{base_url}bpm/engine/run_post/model/{idwf}/{idcase}/{resourceId}" method="post">
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
                    {ui}
                {/DataObject_Input}
                {DataInputSet}
                    <div class="form-group">
                        <label for="{name}">{name}</label>
                        <input name="{name}" type="text" class="form-control {required}" id="{name}_id" placeholder="">
                    </div>
                {/DataInputSet}
            </div>
            <div class="modal-footer">
                <button id="closeTask" class="btn pull-left btn-danger" data-dismiss="modal" aria-hidden="true">
                    <i class="fa fa-pause fa-white"></i>
                    {lang dismissTask}
                </button>
                <button type="submit" id="finishTask-1" class="btn btn-success">
                    <i class="fa fa-play fa-white"></i>
                    {lang finishTask}
                </button>
            </div>
        </div>
    </div>
</div>
</form>