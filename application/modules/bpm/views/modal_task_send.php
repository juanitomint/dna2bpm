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
                <ul class="nav nav-pills nav-stacked">
                    {shapes} 
                    <li class="">
                        <a href="{base_url}bpm/test/send/{idwf}/{idcase}/{resourceId}">
                            <i class="icon-play icon-chevron-right"></i>
                            {properties name}
                        </a>
                    </li>
                    {/shapes}
                </ul>
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