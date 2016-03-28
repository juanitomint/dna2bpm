<!-- Modal -->
<div id="myModal" class="modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="false" >
    <div class="modal-dialog" style="width:90%">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h3 id="myModalLabel">{name}</h3>
            </div>
            <div class="modal-body">
                {text}
                <div class="row">
                    <div class="col-md-8">
                    
                        {ignore}
                        <pre id="editor" style="height: 550px; width: 100%;"><?php echo "$script";?></pre>
                        {/ignore}
                    </div>
                    <div class="col-md-4">
                        <button id="testTask" class="btn pull-left btn-success">
                        <i class="fa fa-flask fa-white"></i>
                        TEST
                    </button>
                        <button id="saveTask" class="btn pull-right btn-info">
                        <i class="fa fa-save fa-white"></i>
                        SAVE
                    </button>
                    <hr/>
                        <div id="results"/>
                        </div>
                    </div>
                </div>
            </div>
</div>