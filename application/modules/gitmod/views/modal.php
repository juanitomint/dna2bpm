<!-- Modal -->
    <div class="modal fade" id="gitModal" tabindex="-1" role="dialog" aria-labelledby="gitModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span>
                    </button>
                    <h4 class="modal-title" id="gitModalLabel">Commit files</h4>
                </div>
                <div class="modal-body">
                    <form role="form">
                        <div class="form-group">
                            <label for="message-text" class="control-label">Descripction</label>
                            <textarea class="form-control" id="message-text"></textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button"  class="btn btn-default" data-dismiss="modal">Close</button>
                    <button id="commitBtn" type="button" class="btn btn-success">Commit</button>
                </div>
            </div>
        </div>
    </div>