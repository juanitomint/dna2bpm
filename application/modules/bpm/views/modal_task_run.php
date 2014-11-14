<!-- Modal -->
<div id="myModal" class="modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h3 id="myModalLabel">{name}</h3>
            </div>
            <div class="modal-body">
                {text}
<div id="editor">{source}</div>
            <div class="modal-footer">
                <button id="runTask" class="btn pull-left btn-success" data-dismiss="modal" aria-hidden="true">
                    <i class="fa fa-chevron-right fa-white"></i>
                    {closeTask}
                </button>
                <div id="results"/>
            </div>
        </div>
    </div>
</div>